<?php
 //Check that we're not being directly called
defined('LPWOOTRK_LOADED') or die;

/**
 * Initializes the autoloading process
 * 
 * @return void
 */
function lpwootrk_init_autoloader() {

}

/**
 * Returns the current environment accessor instance
 * 
 * @return \LivepaymentsWootracker\Env The current environment accessor instance
 */
function lpwootrk_get_env() {

}

/**
 * Returns the current environment plugin instance
 * 
 * @return \LivepaymentsWootracker\Plugin The current plugin instance
 */
function lpwootrk_plugin() {

}

/**
 * Runs the plug-in such that it integrates into WP workflow
 *
 * @return void
 */
function lpwootrk_run() {
    lpwootrk_plugin()->run();
}
