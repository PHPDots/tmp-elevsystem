<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class AdminpagesController extends AppController {
    
    public $uses = array('Booking','Page','Tnc','BookingTrack','Track','Category');
    
    public function beforeRender(){
        if(!$this->request->is('ajax')) {
            $this->layout = 'admin';
        }
        if(($this->notifications['count'][0] > 0) && in_array($this->currentUser['User']['role'],array('internal_teacher','external_teacher'))){               
            return $this->redirect(array('controller' => 'tncs','action' => 'userTerms'));
         }
    }
    
    public function display($pageId = NULL) {
        $path = func_get_args();
        // print_r($path);
        $count = count($path);

        if (!$count) {
            return $this->redirect('/admin');
        }
        $page = $subpage = $title_for_layout = null;

        if (!empty($path[0])) {
                $page = $path[0];
        }
        if (!empty($path[1])) {
                $subpage = $path[1];
        }
        if (!empty($path[$count - 1])) {
                $title_for_layout = Inflector::humanize($path[$count - 1]);
        } 
        try {     
            if($pageId != 'home') {
               $this->view($pageId);  
               $this->render('view');
                
            } else {
                //$this->render(implode('/', $path));
                $conditions     = array();
                $conditionsuser     = array();
                $currentUser    = $this->currentUser;

                $conditions['OR'][]['date >'] = date('Y-m-d');
                $conditions['OR'][] = array(
                    'date ='                                            => date('Y-m-d'),
                    //'SUBSTRING_INDEX(BookingTrack.time_slot,"-",1) >='  => date('H:i'),
                    'SUBSTRING_INDEX(BookingTrack.time_slot,"-",1) >='  => date('H:i',  strtotime('-6 hours')),
                );
                 
                if($this->request->is('ajax')) {
                    if(isset($this->request->query['area']) && !empty($this->request->query['area'])) {
                        $conditions['area_slug'] = "{$this->request->query['area']}";
                    }
                    if(isset($this->request->query['booking_date']) && !empty($this->request->query['booking_date'])) {
                        $conditions['date'] = date('Y-m-d', strtotime($this->request->query['booking_date']));
                    }
                    if(isset($this->request->query['booking_name']) && !empty($this->request->query['booking_name'])) {

                        $conditionsuser['CONCAT(REPLACE(firstname," ","")," ",REPLACE(lastname," ","")) LIKE'] = "%{$this->request->query['booking_name']}%";
                        // $conditionsuser['OR']["firstname LIKE"] = "%{$this->request->query['booking_name']}%";
                        // $conditionsuser['OR']['lastname LIKE'] = "%{$this->request->query['booking_name']}%";

                    }
                }
                $areas      = $this->Area->find('list',array('fields' => array('slug','name')));
                if($currentUser['User']['role'] != 'admin') {
                    $conditions1['OR']['user_id'] = $currentUser['User']['id'];
                    $conditions1['OR']['booking_user_id'] = $currentUser['User']['id'];
                    $conditions['AND'] = $conditions1;
                   
                }

                $bookingTracks   = $this->BookingTrack->find('all',array(
                    'fields'        => array(
                        'Booking.id','Booking.date','Booking.user_id','Booking.area_slug','BookingTrack.id','time_slot','track_id','booking_user_id'
                    ),
                    'type'          => 'LEFT',
                    'joins'         => array(
                        array(
                            'table'         => 'bookings',
                            'alias'         => 'Booking',
                            'conditions'    => array(
                                'BookingTrack.booking_id = Booking.id',
                            )
                        )
                    ),
                    'order'         => array('Booking.date','SUBSTRING_INDEX(BookingTrack.time_slot,"-",1)'),
                    'conditions'    => $conditions,
                ));
               
               
                $User   = $this->User->find('all',array(
                    'conditions'    => $conditionsuser,
                ));

                $bookedTracks = array();
                foreach($bookingTracks as $bookingTrack) {
                    $bookedTracks[$bookingTrack['Booking']['id']][$bookingTrack['BookingTrack']['time_slot']][$bookingTrack['BookingTrack']['track_id']] = $bookingTrack;
                }
                
                $users          = Hash::combine($User,'{n}.User.id','{n}.User');
                $tracks         = Hash::combine($this->Track->find('all'),'{n}.Track.id','{n}.Track','{n}.Track.area_id');
                $this->set(array(
                    'areas'         => $areas,
                    'users'         => $users,
                    'bookingTracks' => $bookingTracks,
                    'tracks'        => $tracks,
                    'bookedTracks'  => $bookedTracks,
                ));
                
                $this->render(implode('/', $path));
                if($this->request->is('ajax')) {
                    $this->layout = 'ajax';                    
                    $this->render('Ajax/bookings');
                }
            }
        } catch (MissingViewException $e) {           
            if (Configure::read('debug')) {
                    throw $e;
            }
            throw new NotFoundException();
        } 
        
        $this->set(compact('page', 'subpage', 'title_for_layout'));
          
    }
    
    private function breadcrum($case,$page = array()){
        
        $pageTitle[] = array(
            'name'  => __('Pages'),
            'url'   => Router::url(array('controller'=>'adminpages','action'=>'documents')),
        );
        
        switch ($case){
            
            case 'add':
                
                $pageTitle[] = array(
                    'name'  => __('Add'),
                     'url'   => '#',
                );
                break;
            
            case 'edit':
                
                $pageTitle[] = array(
                    'name'  => $page['Page']['title'],
                     'url'   => '#',
                );
                break;
            
            case 'view':
                
                $pageTitle[] = array(
                    'name'  => $page['Page']['title'],
                     'url'   => '#',
                );
                break;
        }
        
        $this->SiteInfo->write('pageTitle',$pageTitle);
    }
    
    public function documents() {
        
        $this->perPage  = $this->getPerPage('Page');
        
        
        $args = array(
            'limit' => $this->perPage,
            'order' => array('Page.id' => 'DESC'),
        );
        
        $this->Paginator->settings = $args;
        
        $pages = $this->Paginator->paginate('Page');

        $category          = Hash::combine($this->Category->find('all'),'{n}.Category.category_code','{n}.Category');

        $this->set(array(
            'pages'     => $pages,
            'category'     => $category,
            'perPage'   => $this->perPage,
        ));
        
    }
    
    public function add() {
        
        $this->set(array('isEdit' => FALSE));
        $this->breadcrum('add');
        $category  = $this->Category->find('list',array(
            'fields'        => array('category_code','name'),
        ));

        $this->set(array(
            'category'     => $category,
        ));
        $this->render('add');
        if ($this->request->is('post') && $this->request->data['Page']['title'] != '') {
            $this->Page->create();
                $check = $this->request->data['Page'];

                $filename = Inflector::slug(pathinfo($check['filename']['name'], PATHINFO_FILENAME)).rand(0,5000).'.'.pathinfo($check['filename']['name'], PATHINFO_EXTENSION);
                $full_file_name = WWW_ROOT . 'documents' . DS . $filename;

                if (!move_uploaded_file($check['filename']['tmp_name'], $full_file_name)) {
                    return FALSE;
                } else {
                    $this->request->data['Page']['file'] = $filename;
                    // $this->data[$this->alias]['filepath'] = str_replace(DS, "/", str_replace(WWW_ROOT, "", $filename) );
                }
            if ($this->Page->save($this->request->data)) {
                $this->Session->setFlash(__('The Page has been saved'), 'alert/success');
                return $this->redirect(array('controller' => 'adminpages','action' => 'documents'));
            } else {
                $this->Session->setFlash(__('The Page could not be saved. Please, try again.'), 'alert/error');
            }
        }
        return;

    }
    
    public function delete($id = NULL) {
        
        if (empty($id)) {
            $this->redirect(array('action' => 'index'));
        }

        if ($this->Page->delete($id)) {                                 
            $this->Session->setFlash(__('The Page has been deleted'), 'alert/success');
            return $this->redirect(array('controller' => 'adminpages','action' => 'documents'));
        }
    }
    
    public function edit($id = NULL) {
        
        $this->set(array('isEdit' => TRUE));
                
        if (empty($id)) {
            $this->redirect(array('action' => 'home'));
        }

        $page = $this->Page->findById($id);
        $category  = $this->Category->find('list',array(
            'fields'        => array('category_code','name'),
        ));
        $this->set(array(
            'category'     => $category,
        ));
        if (empty($page)) {
            return $this->redirect(array('action' => 'home'));
        }

        $this->breadcrum('edit',$page);
        if (!$this->request->data) {
            $this->request->data = $page;
        }

        $this->render('add');
        if (!$this->request->is('post')) {
             return;
        }

        $this->Page->create();
        $this->Page->id = $id;
        $check = $this->request->data['Page'];

        if($check['filename'] != ''){

            $filename = Inflector::slug(pathinfo($check['filename']['name'], PATHINFO_FILENAME)).rand(0,5000).'.'.pathinfo($check['filename']['name'], PATHINFO_EXTENSION);
            $full_file_name = WWW_ROOT . 'documents' . DS . $filename;

            if (!move_uploaded_file($check['filename']['tmp_name'], $full_file_name)) {
                print_r($this->request->data['Page']);
                die();
                return FALSE;
            } else {
                $this->request->data['Page']['file'] = $filename;
                unset($this->request->data['Page']['filename']);
            }
        }
        if ($this->Page->save($this->request->data)) {

            $this->Session->setFlash(__('The Page has been saved'), 'alert/success');
            return $this->redirect(array('controller' => 'adminpages','action' => 'documents'));
        } else {
            $this->Session->setFlash(__('The Page could not be saved. Please, try again.'), 'alert/error');
        }
    }
    
    public function view($id = NULL) {
        
        if (empty($id)) {
            $this->redirect(array('action' => 'home'));
        }

        $page = $this->Page->findBySlug($id);
        
        $this->breadcrum('view',$page);

        if (empty($page)) {
            return $this->redirect(array('action' => 'documents'));
        }
        
        $this->set(array('page' => $page));
    }
}