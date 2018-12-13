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
            echo $this->Html->link('<i class="fa fa-dashboard"></i>'.__('Forside'),
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
            echo $this->Html->link('<i class="fa fa-user"></i>'.__('Din profil'),
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
            echo $this->Html->link('<i class="fa fa-car"></i>'.__('Køretimer'),
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
            echo $this->Html->link('<i class="fa fa-car"></i>'.__('Banetider'),
                array(
                    'controller' => 'users',
                    'action'     => 'drivingLessons'
                ),
                array(
                    'escape'    => FALSE,
                )  
            );
            ?>
       </li>
       <li class="has-submenu">
            <?php
            echo $this->Html->link('<i class="fa fa-money"></i>'.__('Din Økonomi'),
                array(
                    'controller' => 'users',
                    'action'     => 'studentCharges'
                ),
                array(
                    'escape'    => FALSE,
                )  
            );
            ?>
       </li>
       <!-- <li class="has-submenu">
            <?php
            echo $this->Html->link('<i class="fa fa-shopping-cart"></i>'.__('Products'),
                array(
                    'controller' => 'users',
                    'action'     => 'products'
                ),
                array(
                    'escape'    => FALSE,
                )  
            );
            ?>
       </li> -->
       <li class="has-submenu">
           <?php
           echo $this->Html->link('<i class="fa fa-list-alt"></i>'.__('Dokumenter'),
                array(
                    'controller' => 'pages',
                    'action'     => 'document'
                ),
                array(
                    'escape'    => FALSE,
                )  
            );
            ?>
       </li>
       <li class="has-submenu">
           <?php
           echo $this->Html->link('<i class="fa fa-power-off"></i>'.__('Logout'),
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
