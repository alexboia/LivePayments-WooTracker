<?php
/**
 * Plugin Name: LivePayments Woo Tracker
 * Author: Alexandru Boia
 * Author URI: http://alexboia.net
 * Version: 0.1.0
 * Description: 
 * License: New BSD License
 * Plugin URI: 
 * Text Domain: livepayments-wootracker
 */

/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

//Check that we're not being directly called
defined('ABSPATH') or die;

require_once __DIR__ . '/lpwootrk-plugin-header.php';
require_once __DIR__ . '/lpwootrk-plugin-functions.php';

lpwootrk_init_autoloader();
lpwootrk_run();