<?php
//Check that we're not being directly called
defined('ABSPATH') or die;

/**
 * Marker constant for establihing that 
 *  LivePayments Woo Tracker core has been loaded.
 * All other files must check for the existence 
 *  of this constant  and die if it's not present.
 * 
 * @var boolean LPWOOTRK_LOADED Set to true
 */
define('LPWOOTRK_LOADED', true);

/**
 * The absolute path to the plug-in's installation directory.
 *  Eg. /whatever/public_html/wp-content/plugins/whatever-plugin.
 * 
 * @var string LPWOOTRK_PLUGIN_ROOT The computed path
 */
define('LPWOOTRK_PLUGIN_ROOT', __DIR__);

/**
 * The name of the directory in which the plug-in is installed.
 *  Eg. livepayments-wootracker.
 * 
 * @var string LPWOOTRK_PLUGIN_ROOT_NAME The name of the directory
 */
define('LPWOOTRK_PLUGIN_ROOT_NAME', basename(LPWOOTRK_PLUGIN_ROOT));

/**
 	 * The absolute path to this file - the plug-in header file
 * 
 * @var string LPWOOTRK_PLUGIN_HEADER
 */
define('LPWOOTRK_PLUGIN_HEADER', __FILE__);

/**
 	 * The absolute path to the main plug-in file - lpwootrk-plugin-main.php
 * 
 * @var string LPWOOTRK_PLUGIN_MAIN
 */
define('LPWOOTRK_PLUGIN_MAIN', LPWOOTRK_PLUGIN_ROOT . '/lpwootrk-plugin-main.php');

/**
 	 * The absolute path to the plug-in's functions file - lpwootrk-plugin-functions.php
 * 
 * @var string LPWOOTRK_PLUGIN_FUNCTIONS
 */
define('LPWOOTRK_PLUGIN_FUNCTIONS', LPWOOTRK_PLUGIN_ROOT . '/lpwootrk-plugin-functions.php');

/**
 * The absolute path to the plug-in's library - lib - directory.
 *  This is where all the PHP dependencies are stored.
 *  Eg. /whatever/public_html/wp-content/plugins/whatever-plugin/lib.
 * 
 * @var string LPWOOTRK_LIB_DIR The computed path
 */
define('LPWOOTRK_LIB_DIR', LPWOOTRK_PLUGIN_ROOT . '/lib');

/**
 * The current version of LivePayments Woo Tracker.
 *  Eg. 0.1.0.
 * 
 * @var string LPWOOTRK_VERSION The current version
 */
define('LPWOOTRK_VERSION', '0.1.0');
