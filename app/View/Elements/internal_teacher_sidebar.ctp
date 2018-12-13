<div class="navbar-header">
       <button type="button" class="navbar-toggle menu-toggle pull-right" data-toggle="collapse" 
          data-target="#menu">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
       </button>
</div>
<div class="collapse navbar-collapse menu-default" id="menu">
   <ul class="nav navbar-nav sidebar-item">
       <li class="has-submenu">
            <?php
            echo $this->Html->link('<i class="fa fa-dashboard"></i>'.__('Dashboard'),
                array(
                    'controller' => 'pages',
                    'action'     => 'home'
                ),
                array(
                    'escape'    => FALSE,
                )  
            );
            ?>
       </li>
       <li class="has-submenu">
           <?php
            echo $this->Html->link('<i class="fa fa-user"></i>'.__('Your Profile'),
                array(
                    'controller' => 'users',
                    'action'     => 'view'
                ),
                array(
                    'escape'    => FALSE,
                )  
            );
            ?>
       </li>
       <li class="has-submenu">
            <?php
            echo $this->Html->link('<i class="fa fa-car"></i>'.__('Your Driving Lessons'),
                array(
                    'controller' => 'drivingLessons',
                    'action'     => 'index'
                ),
                array(
                    'escape'    => FALSE,
                )  
            );
            ?>
       </li>
       <li class="has-submenu">
            <?php
            echo $this->Html->link('<i class="fa fa-car"></i>'.__('Today\'s Booking'),
                array(
                    'controller' => 'bookings',
                    'action'     => 'todaysBooking'
                ),
                array(
                    'escape'    => FALSE,
                )  
            );
            ?>
       </li>
       <li class="has-submenu">
            <?php
            echo $this->Html->link('<i class="fa fa-car"></i>'.__('Your Booked Tracks'),
                array(
                    'controller' => 'bookings',
                    'action'     => 'index'
                ),
                array(
                    'escape'    => FALSE,
                )  
            );
            ?>
       </li>  
       <li>
            <?php
            echo $this->Html->link('<i class="fa fa-car"></i>'.__('Book Track'),
                array(
                    'controller' => 'bookings',
                    'action'     => 'calendar',                    
                ),
                array(
                    'escape'    => FALSE,
                )  
            );
            ?>
       </li>  
       <li class="has-submenu">
            <?php
            echo $this->Html->link('<i class="fa fa-car"></i>'.__('Register Time'),
                array(
                    'controller' => 'users',
                    'action'     => 'registerTimeList'
                ),
                array(
                    'escape'    => FALSE,
                )  
            );
            ?>           
       </li>
       <li>
           <?php
           echo $this->Html->link('<i class="fa fa-list-alt"></i>'.__('Hourly Report'),
                array(
                    'controller' => 'bookings',
                    'action'     => 'hourlyReport'
                ),
                array(
                    'escape'    => FALSE,
                )  
            );
            ?>
       </li>
       <li>
           <?php
           echo $this->Html->link('<i class="fa fa-list-alt"></i>'.__('Documents'),
                array(
                    'controller' => 'pages',
                    'action'     => $options['home_page']
                ),
                array(
                    'escape'    => FALSE,
                )  
            );
            ?>
       </li>       
       <li>
           <?php
           echo $this->Html->link('<i class="fa fa-list-alt"></i>'.__('Logout'),
                array(
                    'controller' => 'users',
                    'action'     => 'logout'
                ),
                array(
                    'escape'    => FALSE,
                )  
            );
            ?>
       </li>
   </ul>
</div>
