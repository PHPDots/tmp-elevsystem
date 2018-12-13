<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after core.php
 *
 * This file should load/create any application wide configuration settings, such as
 * Caching, Logging, loading additional configuration files.
 *
 * You should also use this file to include any files that provide global functions/constants
 * that your application uses.
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
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

// Setup a 'default' cache configuration for use in the application.
Cache::config('default', array('engine' => 'File'));

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 *
 * App::build(array(
 *     'Model'                     => array('/path/to/models/', '/next/path/to/models/'),
 *     'Model/Behavior'            => array('/path/to/behaviors/', '/next/path/to/behaviors/'),
 *     'Model/Datasource'          => array('/path/to/datasources/', '/next/path/to/datasources/'),
 *     'Model/Datasource/Database' => array('/path/to/databases/', '/next/path/to/database/'),
 *     'Model/Datasource/Session'  => array('/path/to/sessions/', '/next/path/to/sessions/'),
 *     'Controller'                => array('/path/to/controllers/', '/next/path/to/controllers/'),
 *     'Controller/Component'      => array('/path/to/components/', '/next/path/to/components/'),
 *     'Controller/Component/Auth' => array('/path/to/auths/', '/next/path/to/auths/'),
 *     'Controller/Component/Acl'  => array('/path/to/acls/', '/next/path/to/acls/'),
 *     'View'                      => array('/path/to/views/', '/next/path/to/views/'),
 *     'View/Helper'               => array('/path/to/helpers/', '/next/path/to/helpers/'),
 *     'Console'                   => array('/path/to/consoles/', '/next/path/to/consoles/'),
 *     'Console/Command'           => array('/path/to/commands/', '/next/path/to/commands/'),
 *     'Console/Command/Task'      => array('/path/to/tasks/', '/next/path/to/tasks/'),
 *     'Lib'                       => array('/path/to/libs/', '/next/path/to/libs/'),
 *     'Locale'                    => array('/path/to/locales/', '/next/path/to/locales/'),
 *     'Vendor'                    => array('/path/to/vendors/', '/next/path/to/vendors/'),
 *     'Plugin'                    => array('/path/to/plugins/', '/next/path/to/plugins/'),
 * ));
 *
 */

/**
 * Custom Inflector rules can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. Make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 *
 * CakePlugin::loadAll(); // Loads all plugins at once
 * CakePlugin::load('DebugKit'); //Loads a single plugin named DebugKit
 *
 */

/**
 * You can attach event listeners to the request lifecycle as Dispatcher Filter. By default CakePHP bundles two filters:
 *
 * - AssetDispatcher filter will serve your asset files (css, images, js, etc) from your themes and plugins
 * - CacheDispatcher filter will read the Cache.check configure variable and try to serve cached content generated from controllers
 *
 * Feel free to remove or add filters as you see fit for your application. A few examples:
 *
 * Configure::write('Dispatcher.filters', array(
 *		'MyCacheFilter', //  will use MyCacheFilter class from the Routing/Filter package in your app.
 *		'MyPlugin.MyFilter', // will use MyFilter class from the Routing/Filter package in MyPlugin plugin.
 * 		array('callable' => $aFunction, 'on' => 'before', 'priority' => 9), // A valid PHP callback type to be called on beforeDispatch
 *		array('callable' => $anotherMethod, 'on' => 'after'), // A valid PHP callback type to be called on afterDispatch
 *
 * ));
 */
Configure::write('Dispatcher.filters', array(
	'AssetDispatcher',
	'CacheDispatcher'
));

/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
	'engine' => 'File',
	'types' => array('notice', 'info', 'debug'),
	'file' => 'debug',
));
CakeLog::config('error', array(
	'engine' => 'File',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
));


CakePlugin::load('CustomMenu'   ,array('routes'    => true));
CakePlugin::load('ThemeOptions' ,array('routes'    => true));

Configure::write('perPageDropDown',array(
    10  =>  sprintf(__("%d page"),10),    
    20  =>  sprintf(__("%d page"),20), 
    50  =>  sprintf(__("%d page"),50), 
    100 =>  sprintf(__("%d page"),100), 
));

Configure::write('validate',array(
    'mini'      => 3,
    'min'       => 4,
    'nameMax'   => 30,
    'max'       => 16,
    'brief'     => 50,    
    'address'   => 10,
)); 

Configure::write('confirmationStatuses',array(
    'temporary' => __('Temporary'),
    'confirmed' => __('Confirmed')
));

Configure::write('repetationType',array(
    'no'        => __('No'),
    'daily'     => __('Daily'),
    'weekly'    => __('Weekly'),
    'monthly'   => __('Monthly'),
    'yearly'    => __('Yearly')
));

Configure::write('month',array(
    '1'         => 'January',
    '2'         => 'February',
    '3'         => 'March',
    '4'         => 'April',
    '5'         => 'May',
    '6'         => 'June',
    '7'         => 'July',
    '8'         => 'August',
    '9'         => 'September',
    '10'        => 'October',
    '11'        => 'November',
    '12'        => 'December'
));

Configure::write('danishMonths',array(
    'January'   => 'Januar',
    'February'  => 'Februar',
    'March'     => 'Marts',
    'April'     => 'April',
    'May'       => 'Maj',
    'June'      => 'Juni',
    'July'      => 'Juli',
    'August'    => 'August',
    'September' => 'September',
    'October'   => 'Oktober',
    'November'  => 'November',
    'December'  => 'December'
));

Configure::write('bookingType',array(
    'driving'   => __('Driving'),
    'test'      => __('Test'),
    'Køreprøve'      => __('Køreprøve'),
    'Køretime'      => __('Køretime'),
));

Configure::write('lessonTime',array(   
    60    => __('Single (1 Hour)'),    
    1    => __('Single (1 Hour)'),    
    120   => __('Double (2 Hours)'),
    2   => __('Double (2 Hours)')
));

Configure::write('lessonStatus',array(
    'confirmed'     => __('Confirmed'),
    'not_confimed'  => __('Not Confirmed')
));

Configure::write('priceType',array(
    'area'      => __('Area'),
    'driving'   => __('Driving Lesson'),
    'test'      => __('Driving Test'),
    'theory'    => __('Theory')
));

Configure::write('weekDanish',array(
    'Mon' => 'Man',
    'Tue' => 'Tirs',
    'Wed' => 'Ons',
    'Thu' => 'Tors',
    'Fri' => 'Fre',
    'Sat' => 'Lør',
    'Sun' => 'Søn',
));


Configure::write('live_edits',array(
    'bookings_gettimebookings'  => "1",
    'users_autosuggest'  => "1",
    'bookings_add'  => "1",
    'bookings_edit'  => "1",
));

//The time should be in second
Configure::write('live_edit_time',array(
    'time'  => 300,
));