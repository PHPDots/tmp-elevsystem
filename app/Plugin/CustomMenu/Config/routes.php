<?php

Router::connect('admin/menus'            , array('controller' => 'menus', 'action' => 'index'    ,'plugin' => 'CustomMenu'));
Router::connect('admin/menus/saveMenu'   , array('controller' => 'menus', 'action' => 'saveMenu' ,'plugin' => 'CustomMenu'));
Router::connect('admin/menus/getMenu/*'  , array('controller' => 'menus', 'action' => 'getMenu'  ,'plugin' => 'CustomMenu'));