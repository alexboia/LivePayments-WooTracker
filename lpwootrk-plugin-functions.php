<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

 //Check that we're not being directly called
defined('LPWOOTRK_LOADED') or die;

/**
 * Initializes the autoloading process
 * 
 * @return void
 */
function lpwootrk_init_autoloader() {
	require_once LPWOOTRK_LIB_DIR . '/Autoloader.php';
	\LivepaymentsWootracker\Autoloader::init(LPWOOTRK_LIB_DIR, array(
		'LivepaymentsWootracker' => array(
			'separator' => '\\',
			'libDir' => LPWOOTRK_LIB_DIR
		)
	));
}

/**
 * Constructs the standard AJAX response structure 
 *  returned by admin-ajax.php ajax actions.
 * Optionally, additional properties can be added, as an associative array
 * 
 * @param array $additionalProps Additional properties to add.
 * @return \stdClass A new standard response instance
 */
function lpwootrk_get_ajax_response($additionalProps = array()) {
	$response = new \stdClass();
	$response->success = false;
	$response->message = null;

	foreach ($additionalProps as $key => $value) {
		$response->$key = $value;
	}

	return $response;
}

/**
 * Appends the given error to the given message if WP_DEBUG is set to true; 
 * otherwise returns the original message
 * 
 * @param string $message
 * @param string|Exception|WP_Error $error
 * @return string The processed message
 */
function lpwootrk_append_error($message, $error) {
	if (defined('WP_DEBUG') && WP_DEBUG) {
		if ($error instanceof \Exception) {
			$message .= sprintf(': %s (%s) in file %s line %d', $error->getMessage(), $error->getCode(), $error->getFile(), $error->getLine());
		} else if (!empty($error)) {
			$message .= ': ' . $error;
		}
	}
	return $message;
}

/**
 * Increase script execution time limit and maximum memory limit
 * 
 * @param int $executionTimeMinutes The execution time in minutes, to raise the limit to. Defaults to 5 minutes.
 * @return void
 */
function lpwootrk_increase_limits($executionTimeMinutes = 10) {
	if (function_exists('set_time_limit')) {
		@set_time_limit($executionTimeMinutes * 60);
	}
	if (function_exists('ini_set')) {
		@ini_set('memory_limit', WP_MAX_MEMORY_LIMIT);
	}
}

/**
 * Encodes and outputs the given data as JSON and sets the appropriate headers
 * 
 * @param mixed $data The data to be encoded and sent to client
 * @param boolean $die Whether or not to halt script execution. Defaults to true.
 * @return void
 */
function lpwootrk_send_json(\stdClass $data, $die = true) {
	$data = json_encode($data);
	header('Content-Type: application/json');
	if (extension_loaded('zlib') && function_exists('ini_set')) {
		@ini_set('zlib.output_compression', false);
		@ini_set('zlib.output_compression_level', 0);
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
			header('Content-Encoding: gzip');
			$data = gzencode($data, 8, FORCE_GZIP);
		}
	}
 
	echo $data;
	if ($die) {
	   exit;
	}
}

if (!function_exists('write_log')) {
	function write_log ($message)  {
	   if (is_array($message) || is_object($message)) {
			ob_start();
			var_dump($message);
			$message = ob_get_clean();
			error_log($message);
	   } else {
			error_log($message);
	   }
	}
}

/**
 * @return \LivepaymentsWootracker\Settings 
 */
function lpwootrk_get_settings() {
	return \LivepaymentsWootracker\Settings::getInstance();
 }

/**
 * Returns the current environment accessor instance
 * 
 * @return \LivepaymentsWootracker\Env The current environment accessor instance
 */
function lpwootrk_get_env() {
    static $env = null;
   
    if ($env === null) {
        $env = new \LivepaymentsWootracker\Env();
    }

    return $env;
}

/**
 * Returns the current environment plugin instance
 * 
 * @return \LivepaymentsWootracker\Plugin The current plugin instance
 */
function lpwootrk_plugin() {
    static $plugin = null;

    if ($plugin === null) {
        $plugin = new \LivepaymentsWootracker\Plugin(array(
            'mediaIncludes' => array(
                'refPluginsPath' => LPWOOTRK_PLUGIN_MAIN,
                'scriptsInFooter' => true
            )
        ));
    }

    return $plugin;
}

/**
 * Runs the plug-in such that it integrates into WP workflow
 *
 * @return void
 */
function lpwootrk_run() {
    lpwootrk_plugin()->run();
}
