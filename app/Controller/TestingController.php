<?php

class TestingController extends AppController{
    
    public $host    = "http://192.168.1.17:4444/wd/hub";
    public $domain  = "http://192.168.1.17/lisabeth/";    
    public $inputs  = array();
    public $i       = 1;
    public $capabilities;
    public $driver;
    
    public function beforeFilter() {
        parent::beforeFilter();
        
        include_once APP . DS . 'Lib/Selenium/__init__.php';
        
        $this->Auth->allow(); 
        
        
    }
    
    public function chrome(){
        
        $this->capabilities = DesiredCapabilities::chrome();
        $this->driver       = RemoteWebDriver::create($this->host, $this->capabilities, 2000);
        
        $this->driver->get($this->domain);
        
        $this->i = 1;
        
        $chromeArgs = array(
            'User'          => array(
                'firstname' => 'Test',
                'lastname'  => 'Third',
                'emailId'   => 'test3@blackid.in',
                'phoneNo'   => '91-256-65479321',
                'role'      => 'Student',
                'username'  => 'test3',
                'pass'      => 'test3',                
            ),
            'Booking'       => array(
                'area'              => 'BookingAreaGotri',
                'date'              => '//*[@id="calendar_10"]/div/div/table/tbody/tr[4]/td[5]',
                'slot'              => '//*[@id="bookingsCt"]/div/table/tbody/tr[3]/td[2]/a',
                'student'           => 'te',
                'selected_student'  => '//*[@id="ui-id-1"]/li[4]/a[@class="ui-corner-all"]',
                'type'              => 'BookingStatusConfirmed'
            ),
            'browser'       => 'Chrome',
        );
        
        
        $this->login('admin','admintest','Chrome');
        $this->login('admin','pass','Chrome');
        $this->addUser($chromeArgs);
        $this->booking($chromeArgs);
        $this->logout();
        
        $this->capabilities = DesiredCapabilities::firefox();
        $this->driver       = RemoteWebDriver::create($this->host, $this->capabilities, 2000);
        
        $this->driver->get($this->domain);
        
        $this->i = 1;
        
        $firefoxArgs = array(
            'User'          => array(
                'firstname' => 'Test',
                'lastname'  => 'Fourth',
                'emailId'   => 'test4@blackid.in',
                'phoneNo'   => '91-256-65479321',
                'role'      => 'Student',
                'username'  => 'test4',
                'pass'      => 'test4',                
            ),
            'Booking'       => array(
                'area'              => 'BookingAreaGotri',
                'date'              => '//*[@id="calendar_10"]/div/div/table/tbody/tr[4]/td[5]',
                'slot'              => '//*[@id="bookingsCt"]/div/table/tbody/tr[5]/td[2]/a',                
                'student'           => 'te',
                'selected_student'  => '//*[@id="ui-id-1"]/li[5]/a[@class="ui-corner-all"]',
                'type'              => 'BookingStatusConfirmed'
            ),
            'browser'   => 'FireFox',
        );
        
        $this->login('admin','admintest','FireFox');
        $this->login('admin','pass','FireFox');
        $this->addUser($firefoxArgs);
        $this->booking($firefoxArgs);
        $this->logout();
        
    }
    
    public function firefox(){
        $this->capabilities = DesiredCapabilities::firefox();
        $this->driver       = RemoteWebDriver::create($this->host, $this->capabilities, 2000);
        
        $this->driver->get($this->domain);
        
        $this->login('admin','admintest');
        $this->login('admin','pass');
        $this->addUser();
        $this->logout();
    }
    
    private function login($username,$pass,$browser){
        
        $this->driver->takeScreenshot(APP.DS. "screenshots/{$browser}/Lisabeth_login_{$this->i}.png");
        
        $this->inputs['username']  = $this->driver->findElement(WebDriverBy::id('UserUsername'));
        $this->inputs['username']->clear();
        $this->inputs['username']->sendKeys($username);

        $this->inputs['password']  = $this->driver->findElement(WebDriverBy::id('UserPassword'));
        $this->inputs['password']->clear();
        $this->inputs['password']->sendKeys($pass);

        $this->inputs['login']  = $this->driver->findElement(WebDriverBy::className('fRight'));
        $this->inputs['login']->click();
        
    }
    
    private function addUser($args){
        
        $this->driver->get($this->domain.'users/add');
        
        $this->inputs['formSubmit'] = $this->driver->findElement(WebDriverBy::id('formSubmit'));
        $this->inputs['formSubmit']->click();
        
        $this->driver->wait(60);
        
        $this->driver->takeScreenshot(APP.DS. "screenshots/{$args['browser']}/Lisabeth_user_add_{$this->i}.png");
        
        $this->inputs['UserFirstname']  = $this->driver->findElement(WebDriverBy::id('UserFirstname'));
        $this->inputs['UserFirstname']->sendKeys($args['User']['firstname']);
        
        $this->inputs['UserLastname']  = $this->driver->findElement(WebDriverBy::id('UserLastname'));
        $this->inputs['UserLastname']->sendKeys($args['User']['lastname']);
        
        $this->inputs['UserEmailId']  = $this->driver->findElement(WebDriverBy::id('UserEmailId'));
        $this->inputs['UserEmailId']->sendKeys($args['User']['emailId']);
        
        $this->inputs['UserPhoneNo']  = $this->driver->findElement(WebDriverBy::id('UserPhoneNo'));
        $this->inputs['UserPhoneNo']->sendKeys($args['User']['phoneNo']);
        
        $this->inputs['UserRole']  = $this->driver->findElement(WebDriverBy::id('UserRole'));
        $this->inputs['UserRole']->sendKeys($args['User']['role']);
       
        $this->inputs['UserUsername']  = $this->driver->findElement(WebDriverBy::id('UserUsername'));
        $this->inputs['UserUsername']->clear();
        $this->inputs['UserUsername']->sendKeys($args['User']['username']);
        
        $this->inputs['UserPassword']  = $this->driver->findElement(WebDriverBy::id('UserPassword'));
        $this->inputs['UserPassword']->clear();
        $this->inputs['UserPassword']->sendKeys($args['User']['pass']);
        
        $this->inputs['formSubmit'] = $this->driver->findElement(WebDriverBy::id('formSubmit'));
        $this->inputs['formSubmit']->click();
        
        $this->driver->wait(60);
        
        $this->i    = $this->i + 1;
        
        $this->driver->takeScreenshot(APP.DS. "screenshots/{$args['browser']}/Lisabeth_user_add_{$this->i}.png");
        
        $this->driver->get($this->domain.'users');
        
        $this->driver->wait(60);
        
    }
    
    private function booking($args){
        
        $this->driver->get($this->domain.'bookings/calendar');
        
        $this->i    = 1;
        
        $this->inputs['area']   = $this->driver->findElement(WebDriverBy::id($args['Booking']['area']));
        $this->inputs['area']->click();
        
        $this->inputs['date']   = $this->driver->findElement(WebDriverBy::xpath($args['Booking']['date']));
        $this->inputs['date']->click();
        
        $this->driver->takeScreenshot(APP.DS. "screenshots/{$args['browser']}/Lisabeth_booking_calendar_{$this->i}.png");
        
        $parentWindows          = $this->driver->getWindowHandle();
        
        $this->driver->wait(60)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::xpath($args['Booking']['slot'])));
        
        $this->inputs['slot']   = $this->driver->findElement(WebDriverBy::xpath($args['Booking']['slot']));
        $this->inputs['slot']->click();
        
        $windowHandles          = $this->driver->getWindowHandles();
        
        foreach($windowHandles as $key => $windowHandle){
            if($windowHandle == $parentWindows){
                unset($windowHandles[$key]);
            }else{
                
                $this->driver->switchTo()->window($windowHandle);
                
                $this->driver->wait(60)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('BookingBriefDescription')));
                
                $this->inputs['formSubmit'] = $this->driver->findElement(WebDriverBy::id('formSubmit'));
                $this->inputs['formSubmit']->click();
                
                ++$this->i;
                
                $this->driver->wait(60);                
                
                $this->driver->takeScreenshot(APP.DS. "screenshots/{$args['browser']}/Lisabeth_booking_calendar_{$this->i}.png");
                
                $this->inputs['BookingBriefDescription']    = $this->driver->findElement(WebDriverBy::id('BookingBriefDescription'));
                $this->inputs['BookingBriefDescription']->sendKeys('Contrary to popular belief, Lorem Ipsum is not simply random text.');
                
                $this->inputs['BookingFullDescription']     = $this->driver->findElement(WebDriverBy::id('BookingFullDescription'));
                $this->inputs['BookingFullDescription']->sendKeys("Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.");
                
                $this->inputs['BookingSelectStudent']       = $this->driver->findElement(WebDriverBy::id('BookingSelectStudent'));
                $this->inputs['BookingSelectStudent']->sendKeys($args['Booking']['student']);
                
                $this->driver->wait(60)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::xpath($args['Booking']['selected_student'])));
                
                $this->inputs['BookingSelectedStudent']     = $this->driver->findElement(WebDriverBy::xpath($args['Booking']['selected_student']));
                $this->inputs['BookingSelectedStudent']->click();
                
                $this->inputs['type']    = $this->driver->findElement(WebDriverBy::id($args['Booking']['type']));
                $this->inputs['type']->click();
                
                ++$this->i;
                
                $this->driver->wait(60);
                
                $this->driver->takeScreenshot(APP.DS. "screenshots/{$args['browser']}/Lisabeth_booking_calendar_{$this->i}.png");
                
                $this->driver->wait(60);
                
                $this->inputs['formSubmit'] = $this->driver->findElement(WebDriverBy::id('formSubmit'));
                $this->inputs['formSubmit']->click();
                
                $this->driver->wait(60);
                
                ++$this->i;
                
                $this->driver->takeScreenshot(APP.DS. "screenshots/{$args['browser']}/Lisabeth_booking_calendar_{$this->i}.png");
                
                $this->driver->wait(60);
                
                $this->driver->get($this->domain.'bookings');
                
                $this->driver->wait(60);
                
            }
        }
        
    }
    
    private function logout(){
        
        $this->driver->wait(60)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::xpath('//*[@id="container"]/header/ul/li/a')));
        
        $this->inputs['container'] = $this->driver->findElement(WebDriverBy::xpath('//*[@id="container"]/header/ul/li/a'));
        $this->inputs['container']->click();
        
        $this->driver->wait(60)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::xpath('//*[@id="container"]/header/ul/li/div/div/a[2]')));
        
        $this->inputs['logout'] = $this->driver->findElement(WebDriverBy::xpath('//*[@id="container"]/header/ul/li/div/div/a[2]'));
        $this->inputs['logout']->click();
        
        $this->driver->quit();
    }
}
