<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	Router::connect('/', array('controller' => 'users', 'action' => 'login'));
	Router::connect('/admin', array('controller' => 'adminusers', 'action' => 'login'));
/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */

Router::connect('/admin/menus', array('controller' => 'menus', 'action' => 'index'    ,'plugin' => 'CustomMenu'));
Router::connect('/admin/menus/saveMenu', array('controller' => 'menus', 'action' => 'saveMenu' ,'plugin' => 'CustomMenu'));
Router::connect('/admin/menus/getMenu/*', array('controller' => 'menus', 'action' => 'getMenu'  ,'plugin' => 'CustomMenu'));
Router::connect('/adminpages/add', array('controller' => 'adminpages', 'action' => 'add'));
Router::connect('/adminpages/edit/*', array('controller' => 'adminpages', 'action' => 'edit'));
Router::connect('/adminpages/delete/*', array('controller' => 'adminpages', 'action' => 'delete'));
Router::connect('/adminpages/view/*', array('controller' => 'adminpages', 'action' => 'view'));
Router::connect('/adminpages/documents', array('controller' => 'adminpages', 'action' => 'documents'));
Router::connect('/adminpages/*', array('controller' => 'adminpages', 'action' => 'display'));
Router::connect('/pages/view/*', array('controller' => 'pages', 'action' => 'view'));
Router::connect('/pages/document/', array('controller' => 'pages', 'action' => 'document'));
Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
 /**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
