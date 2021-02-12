#!/usr/bin/env bash

PLUGIN_SLUG=$1
PLUGIN_GENERIC_PREFIX=$2
PLUGIN_NAME=$3

# courtesy of https://linuxhint.com/bash_lowercase_uppercase_strings/
# new feature in bash 4
# ^^ - converts the entire string to uppercase
# ^ - converts just the first character to uppercase
PLUGIN_GENERIC_PREFIX_UPPER=${PLUGIN_GENERIC_PREFIX^^}
PLUGIN_TEXT_DOMAIN=$PLUGIN_SLUG

PLUGIN_DIR=$(pwd)
PLUGIN_TEMP_DIR="$PLUGIN_DIR/temp"
PLUGIN_ASSETS_DIR="$PLUGIN_DIR/assets"
PLUGIN_BIN_DIR="$PLUGIN_DIR/bin"
PLUGIN_BUILD_DIR="$PLUGIN_DIR/build"
PLUGIN_DATA_DIR="$PLUGIN_DIR/data"
PLUGIN_HELP_DIR="$PLUGIN_DIR/help"
PLUGIN_LANG_DIR="$PLUGIN_DIR/lang"
PLUGIN_LIB_DIR="$PLUGIN_DIR/lib"
PLUGIN_MEDIA_DIR="$PLUGIN_DIR/media"
PLUGIN_SCREENSHOTS_DIR="$PLUGIN_DIR/screenshots"
PLUGIN_TESTS_DIR="$PLUGIN_DIR/tests"
PLUGIN_VIEWS_DIR="$PLUGIN_DIR/views"

compute_plugin_namespace() {
	# IFS is an internal variable that determines 
	# how Bash recognizes word boundaries; 
	# the default value of IFS is white space; 
	# if you set it to some other value, 
	# reset it to default whitespace.
	# courtesy of https://www.tutorialkart.com/bash-shell-scripting/bash-split-string/
	IFS='-'
	read -ra namespace_parts <<< "$PLUGIN_SLUG"
	IFS=' '
	for ns_part in "${namespace_parts[@]}"
	do
		echo -n ${ns_part^}
	done
}

ensure_dirs() {
	# see explanation here: https://linuxize.com/post/bash-functions/
    local dirs=("$@")

    for dir in "${dirs[@]}"
    do
        if [ ! -d $dir ]
        then
            mkdir "$dir"
        fi
    done
}

ensure_plugin_root_dirs() {
    echo "Creating plug-in directory structure..."

    local root_dirs=($PLUGIN_ASSETS_DIR $PLUGIN_BIN_DIR 
        $PLUGIN_BUILD_DIR 
        $PLUGIN_DATA_DIR 
        $PLUGIN_HELP_DIR 
        $PLUGIN_LANG_DIR
        $PLUGIN_LIB_DIR
        $PLUGIN_MEDIA_DIR
        $PLUGIN_SCREENSHOTS_DIR
        $PLUGIN_TESTS_DIR
        $PLUGIN_VIEWS_DIR)

	# the thing to note here is that
	# we are NOT passing an array, but merely expanding the array, 
	# so that each element is a function argument
	# see explanation here: https://askubuntu.com/questions/674333/how-to-pass-an-array-as-function-argument
	# we then retrieve these using the $@ construct, which,
	# when double quoted, "$@" expands to separate strings - "$1" "$2" "$n".
	# see explanation here: https://linuxize.com/post/bash-functions/
    ensure_dirs "${root_dirs[@]}"
}

ensure_plugin_assets_dirs() {
    echo "Create plugin assets directory structure..."

    local asset_dirs=("$PLUGIN_ASSETS_DIR/default"
        "$PLUGIN_ASSETS_DIR/en_US"
        "$PLUGIN_ASSETS_DIR/ro_RO")

    ensure_dirs "${asset_dirs[@]}"
}

ensure_plugin_build_dirs() {
    echo "Creating plugin build directory structure..."

    local build_dirs=("$PLUGIN_BUILD_DIR/compat-info"
        "$PLUGIN_BUILD_DIR/hook-doc"
        "$PLUGIN_BUILD_DIR/output"
        "$PLUGIN_BUILD_DIR/tmp"
        "$PLUGIN_BUILD_DIR/wp-plugin-dir-svn")

    ensure_dirs "${build_dirs[@]}"
}

ensure_plugin_data_dirs() {
    echo "Creating plugin data directory structure..."

    local data_dirs=("$PLUGIN_DATA_DIR/help"
        "$PLUGIN_DATA_DIR/setup")

    ensure_dirs "${data_dirs[@]}"
}

ensure_plugin_help_dirs() {
    echo "Creating plugin help directory structure..."

    local help_dirs=("$PLUGIN_HELP_DIR/src"
        "$PLUGIN_HELP_DIR/templates"
        "$PLUGIN_HELP_DIR/tools")

    ensure_dirs "${help_dirs[@]}"
}

ensure_plugin_lib_dirs() {
    echo "Creating plugin lib directory structure..."

    local lib_dirs=("$PLUGIN_LIB_DIR/3rdParty"
        "$PLUGIN_HELP_DIR/validator")

    ensure_dirs "${lib_dirs[@]}"
}

ensure_plugin_media_dirs() {
    echo "Creating plugin media directory structure..."

    local media_dirs=("$PLUGIN_MEDIA_DIR/css"
        "$PLUGIN_MEDIA_DIR/img"
        "$PLUGIN_MEDIA_DIR/js"
		"$PLUGIN_MEDIA_DIR/js/3rdParty")

    ensure_dirs "${media_dirs[@]}"
}

ensure_plugin_tests_dir() {
    echo "Creating plugin tests directory structure..."

    local tests_dir=("$PLUGIN_TESTS_DIR/lib"
        "$PLUGIN_TESTS_DIR/lib/3rdParty"
        "$PLUGIN_TESTS_DIR/stubs"
        "$PLUGIN_TESTS_DIR/stubs/phpunit"
        "$PLUGIN_TESTS_DIR/stubs/wordpress-tests-lib")

    ensure_dirs "${tests_dir[@]}"
}

ensure_all_plugin_dirs() {
	ensure_plugin_root_dirs
	ensure_plugin_assets_dirs
	ensure_plugin_build_dirs
	ensure_plugin_data_dirs
	ensure_plugin_help_dirs
	ensure_plugin_lib_dirs
	ensure_plugin_media_dirs
	ensure_plugin_tests_dir
}

ensure_plugin_core_files() {
    echo "Creating plugin core files..."

	# the three plug-in core files are:
	#	- {plugin-prefix}-plugin-header.php - plug-in constants
	#	- {plugin-prefix}-plugin-functions.php - global plug-in functions
	#	- {plugin-prefix}-plugin-main.php - plug-in main file

    local header_file_name="$PLUGIN_GENERIC_PREFIX-plugin-header.php"
    local functions_file_name="$PLUGIN_GENERIC_PREFIX-plugin-functions.php"
    local main_file_name="$PLUGIN_GENERIC_PREFIX-plugin-main.php"
	local plugin_namespace=$(compute_plugin_namespace)

    local core_files=("$PLUGIN_DIR/$header_file_name"
        "$PLUGIN_DIR/$functions_file_name"
        "$PLUGIN_DIR/$main_file_name")

    for cf in "${core_files[@]}"
    do
        if [ ! -f $cf ]
        then
            touch "$cf"
        fi
    done

	# init header file content
	# to avoid heredoc expanding of variables
	# one must single-quote EOF: 'EOF'
	# courtesy of: https://stackoverflow.com/questions/27920806/how-to-avoid-heredoc-expanding-variables
	cat <<- 'EOF' > "$header_file_name"
		<?php
	EOF

	# add basic code
	cat <<- EOF >> "$header_file_name"
		//Check that we're not being directly called
		defined('ABSPATH') or die;

		/**
		 * Marker constant for establihing that 
		 *  $PLUGIN_NAME core has been loaded.
		 * All other files must check for the existence 
		 *  of this constant  and die if it's not present.
		 * 
		 * @var boolean ${PLUGIN_GENERIC_PREFIX_UPPER}_LOADED Set to true
		 */
		define('${PLUGIN_GENERIC_PREFIX_UPPER}_LOADED', true);

		/**
		 * Contains the plug-in identifier, used internally in various places, 
		 *  such as an option prefix.
		 * 
		 * @var boolean ${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_ID Set to true
		 */
		define('${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_ID', '${PLUGIN_GENERIC_PREFIX}');

		/**
		 * The absolute path to the plug-in's installation directory.
		 *  Eg. /whatever/public_html/wp-content/plugins/whatever-plugin.
		 * 
		 * @var string ${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_ROOT The computed path
		 */
		define('${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_ROOT', __DIR__);

		/**
		 * The name of the directory in which the plug-in is installed.
		 *  Eg. $PLUGIN_SLUG.
		 * 
		 * @var string ${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_ROOT_NAME The name of the directory
		 */
		define('${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_ROOT_NAME', basename(${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_ROOT));

		/**
	 	 * The absolute path to this file - the plug-in header file
		 * 
		 * @var string ${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_HEADER
		 */
		define('${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_HEADER', __FILE__);

		/**
	 	 * The absolute path to the main plug-in file - $main_file_name
		 * 
		 * @var string ${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_MAIN
		 */
		define('${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_MAIN', ${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_ROOT . '/$main_file_name');

		/**
	 	 * The absolute path to the plug-in's functions file - $functions_file_name
		 * 
		 * @var string ${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_FUNCTIONS
		 */
		define('${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_FUNCTIONS', ${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_ROOT . '/$functions_file_name');

		/**
		 * The absolute path to the plug-in's library - lib - directory.
		 *  This is where all the PHP dependencies are stored.
		 *  Eg. /whatever/public_html/wp-content/plugins/whatever-plugin/lib.
		 * 
		 * @var string ${PLUGIN_GENERIC_PREFIX_UPPER}_LIB_DIR The computed path
		 */
		define('${PLUGIN_GENERIC_PREFIX_UPPER}_LIB_DIR', ${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_ROOT . '/lib');

		/**
		 * The absolute path to the plug-in's views - views - directory.
		 *  This is where all the templates are stored.
		 *  Eg. /whatever/public_html/wp-content/plugins/whatever-plugin/views.
		 * 
		 * @var string ${PLUGIN_GENERIC_PREFIX_UPPER}_VIEWS_DIR The computed path
		 */
		define('${PLUGIN_GENERIC_PREFIX_UPPER}_VIEWS_DIR', ${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_ROOT . '/views');

		/**
		 * The absolute path to the plug-in's translation files - lang - directory.
		 *  This is where all the translation files (.po, .mo, .pot) are stored.
		 *  Eg. /whatever/public_html/wp-content/plugins/whatever-plugins/lang.
		 * 
		 * @var string ${PLUGIN_GENERIC_PREFIX_UPPER}_LANG_DIR The computed path
		 */
		define('${PLUGIN_GENERIC_PREFIX_UPPER}_LANG_DIR', ${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_ROOT . '/lang');

		/**
		 * The absolute path to the plug-in's own data files - data - directory.
		 *  This is where all the data files that are bundled 
		 * 	(that is, not generated during normal usage) 
		 *	with the plug-in are stored.
		 *  Eg. /whatever/public_html/wp-content/plugins/whatever-plugins/data.
		 * 
		 * @var string ${PLUGIN_GENERIC_PREFIX_UPPER}_DATA_DIR The computed path
		 */
		define('${PLUGIN_GENERIC_PREFIX_UPPER}_DATA_DIR', ${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_ROOT . '/data');

		/**
		 * The current version of $PLUGIN_NAME.
		 *  Eg. 0.1.0.
		 * 
		 * @var string ${PLUGIN_GENERIC_PREFIX_UPPER}_VERSION The current version
		 */
		define('${PLUGIN_GENERIC_PREFIX_UPPER}_VERSION', '0.1.0');
	EOF

	# init functions file content
	cat <<- 'EOF' > "$functions_file_name"
		<?php
	EOF

	cat <<- EOF >> "$functions_file_name"
		 //Check that we're not being directly called
		defined('${PLUGIN_GENERIC_PREFIX_UPPER}_LOADED') or die;

		/**
		 * Initializes the autoloading process
		 * 
		 * @return void
		 */
		function ${PLUGIN_GENERIC_PREFIX}_init_autoloader() {

		}

		/**
		 * Returns the current environment accessor instance
		 * 
		 * @return \\${plugin_namespace}\Env The current environment accessor instance
		 */
		function ${PLUGIN_GENERIC_PREFIX}_get_env() {
		    static \$env = null;
   
		    if (\$env === null) {
		        \$env = new \\${plugin_namespace}\Env();
		    }

		    return \$env;
		}

		/**
		 * Returns the current environment plugin instance
		 * 
		 * @return \\${plugin_namespace}\Plugin The current plugin instance
		 */
		function ${PLUGIN_GENERIC_PREFIX}_plugin() {
		    static \$plugin = null;

		    if (\$plugin === null) {
		        \$plugin = new \\${plugin_namespace}\Plugin(array(
		            'mediaIncludes' => array(
		                'refPluginsPath' => ${PLUGIN_GENERIC_PREFIX_UPPER}_PLUGIN_MAIN,
		                'scriptsInFooter' => true
		            )
		        ));
		    }

		    return \$plugin;
		}

		/**
		 * Runs the plug-in such that it integrates into WP workflow
		 *
		 * @return void
		 */
		function ${PLUGIN_GENERIC_PREFIX}_run() {
		    ${PLUGIN_GENERIC_PREFIX}_plugin()->run();
		}
	EOF

	# init main file content
	cat <<- 'EOF' > "$main_file_name"
		<?php
	EOF
	
	# create plug-in manifest
	cat <<- EOF >> "$main_file_name"
		/**
		 * Plugin Name: $PLUGIN_NAME
		 * Author: Alexandru Boia
		 * Author URI: http://alexboia.net
		 * Version: 0.1.0
		 * Description: 
		 * License: New BSD License
		 * Plugin URI: 
		 * Text Domain: $PLUGIN_TEXT_DOMAIN
		 */

	EOF

	# add basic code
	cat <<- EOF >> "$main_file_name"
		//Check that we're not being directly called
		defined('ABSPATH') or die;

		require_once __DIR__ . '/$header_file_name';
		require_once __DIR__ . '/$functions_file_name';

	EOF
}

ensure_plugin_tests_files() {
    echo "Creating plugin tests files..."

    wp_config="../../../wp-config.php"   
    wp_tests_config="$PLUGIN_TESTS_DIR/wp-tests-config.php"

    cat <<- 'EOF' > "$wp_tests_config"
		<?php
		/* Path to the WordPress codebase you'd like to test. Add a forward slash in the end. */
		if ( defined( 'WP_RUN_CORE_TESTS' ) && constant('WP_RUN_CORE_TESTS') == true ) {
		    define( 'ABSPATH', dirname( __FILE__ ) . '/build/' );
		} else {
		    define( 'ABSPATH', '/tmp/wordpress/' );
		}

		/*
		 * Path to the theme to test with.
		 *
		 * The 'default' theme is symlinked from test/phpunit/data/themedir1/default into
		 * the themes directory of the WordPress installation defined above.
		 */
		define( 'WP_DEFAULT_THEME', 'default' );

		/*
		 * Test with multisite enabled.
		 * Alternatively, use the tests/phpunit/multisite.xml configuration file.
		 */
		// define( 'WP_TESTS_MULTISITE', true );

		/*
		 * Force known bugs to be run.
		 * Tests with an associated Trac ticket that is still open are normally skipped.
		 */
		// define( 'WP_TESTS_FORCE_KNOWN_BUGS', true );

		// Test with WordPress debug mode (default).
		define( 'WP_DEBUG', true );

	EOF

	cat <<- 'EOF' >> "$wp_tests_config"
		/*
		 * This configuration file will be used by the copy of WordPress being tested.
		 * wordpress/wp-config.php will be ignored.
		 *
		 * WARNING WARNING WARNING!
		 * These tests will DROP ALL TABLES in the database with the prefix named below.
		 * DO NOT use a production database or one that is shared with something else.
		 */

		define( 'DB_NAME', '' );
	EOF

	egrep "(DB_USER)|(DB_PASSWORD)|(DB_HOST)|(DB_CHARSET)|(DB_COLLATE)" "$wp_config" | tee -a "$wp_tests_config" > /dev/null

	cat <<- 'EOF' >> "$wp_tests_config"
	
		/**
		 * Authentication Unique Keys and Salts.
		 *
		 * Change these to different unique phrases!
		 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
		 */

	EOF

	wget https://api.wordpress.org/secret-key/1.1/salt/ -q -O - >> "$wp_tests_config"

	cat <<- 'EOF' >> "$wp_tests_config"
	
		/**
		 * Table prefix.
		 * Only numbers, letters, and underscores please!
		 */

	EOF

	egrep "(table_prefix)" "$wp_config" | tee -a "$wp_tests_config" > /dev/null

	cat <<- 'EOF' >> "$wp_tests_config"

		define( 'WP_TESTS_DOMAIN', 'example.org' );
		define( 'WP_TESTS_EMAIL', 'admin@example.org' );
		define( 'WP_TESTS_TITLE', 'Test Blog' );

		define( 'WP_PHP_BINARY', 'php' );

		define( 'WPLANG', '' );

	EOF
}

generate_guard_file_contents() {
	# generate a redirect path of the specified level
	# given by the first ($1) parameter;
	# $1 is relative to the wp-content/plugins directory
	# so we must statically add two more levels to
	# go up to the site's root
	# see: https://stackoverflow.com/questions/54396599/bash-printf-how-to-understand-zero-dot-s-0-s-syntax
	# and: https://wiki-dev.bash-hackers.org/commands/builtin/printf
	local redirect_path=$(printf "../%.0s" $(seq 1 $1))
	cat <<- EOF
		<?php
		header('Location: ../../${redirect_path}index.php');
		exit;
	EOF
}

ensure_guard_files() {
	local current_dir=$1
	local current_level=$2

	pushd $current_dir > /dev/null

	local guard_file="$current_dir/index.php"
	
	if [ ! -f $guard_file ]
	then
		touch $guard_file
	fi

	generate_guard_file_contents $current_level > $guard_file

	# only recurse if requested
	if [ "$3" =	 true ]
	then
		for csub in */
		do
			if [ -d $csub ]
			then
				local process_csub="$current_dir/$csub"
				# $(...) is command substitution, 
				# but $(()) is arithmetic an expansion
				# courtesy of: https://linuxize.com/post/bash-increment-decrement-variable/
				ensure_guard_files "$process_csub" $((current_level+1)) true
			fi
		done
	fi

	popd > /dev/null
}

download_and_install_mysqlidb() {
	# color coding and tabs use control characters
	# so echo must be called with -e flag
	# coloring can be achieved using escape sequences
	# \e[{FormatCode}m where FormatCode describes what format is applied
	# (coloring but not only)
	# courtesy of: https://misc.flogisoft.com/bash/tip_colors_and_formatting
	echo -e "\t\e[36mInstalling mysqlidb library..."

	local version=$1
	local mysqlidb_temp_archive="./mysqlidb.zip"

	pushd $PLUGIN_TEMP_DIR > /dev/null

	# -q - means quite mode
	# -O specifies output file
	wget "https://github.com/ThingEngineer/PHP-MySQLi-Database-Class/archive/$version.zip" -q -O "$mysqlidb_temp_archive" > /dev/null

	if [ -f $mysqlidb_temp_archive ]
	then
		if [ ! -d "./mysqlidb" ]
		then
			mkdir "./mysqlidb"
		fi

		# unzip doesnt really allow us to skip JUST the
		# top directory: -j will flatten the entire archive
		# effectively removing all folder structure;
		# however, bsdtar, DOES allow us to do exactly this
		# by specifying --strip-components=1;
		# courtesy of https://unix.stackexchange.com/a/72854
		bsdtar --strip-components=1 -xf $mysqlidb_temp_archive -C ./mysqlidb > /dev/null

		cp ./mysqlidb/MysqliDb.php "$PLUGIN_LIB_DIR/3rdParty/MysqliDb.php"
	fi

	rm $mysqlidb_temp_archive
	rm -rf ./mysqlidb

	popd > /dev/null
}

download_and_install_jquery_blockui() {
	echo -e "\t\e[36mInstalling jQuery blockUI library..."

	local version=$1
	local jquery_blockui_temp_archive="./jquery.blockui.zip"
	local destination_dir="$PLUGIN_MEDIA_DIR/js/3rdParty"

	pushd $PLUGIN_TEMP_DIR > /dev/null

	wget "https://github.com/malsup/blockui/archive/$version.zip" -q -O "$jquery_blockui_temp_archive" > /dev/null

	if [ -f $jquery_blockui_temp_archive ]
	then
		if [ ! -d "./jquery-blockui" ]
		then
			mkdir "./jquery-blockui"
		fi
		
		bsdtar --strip-components=1 -xf $jquery_blockui_temp_archive -C ./jquery-blockui > /dev/null

		cp ./jquery-blockui/jquery.blockUI.js "$destination_dir"
	fi

	rm $jquery_blockui_temp_archive
	rm -rf ./jquery-blockui

	popd > /dev/null
}

download_and_install_urijs() {
	echo -e "\t\e[36mInstalling URI JS library..."

	local version=$1
	local urijs_temp_archive="./urijs.zip"
	local destination_dir="$PLUGIN_MEDIA_DIR/js/3rdParty/urijs"

	pushd $PLUGIN_TEMP_DIR > /dev/null

	wget "https://github.com/medialize/URI.js/archive/$version.zip" -q -O "$urijs_temp_archive" > /dev/null

	if [ -f $urijs_temp_archive ]
	then
		if [ ! -d "./urijs" ]
		then
			mkdir "./urijs"
		fi

		bsdtar --strip-components=1 -xf $urijs_temp_archive -C ./urijs > /dev/null

		if [ ! -d $destination_dir ]
		then
			mkdir $destination_dir
		fi

		cp ./urijs/src/IPV6.js "$destination_dir"
		cp ./urijs/src/jquery.URI.js "$destination_dir"
		cp ./urijs/src/jquery.URI.min.js "$destination_dir"
		cp ./urijs/LICENSE.txt "$destination_dir"
		cp ./urijs/src/punycode.js "$destination_dir"
		cp ./urijs/src/SecondLevelDomains.js "$destination_dir"
		cp ./urijs/src/URI.fragmentQuery.js "$destination_dir"
		cp ./urijs/src/URI.fragmentURI.js "$destination_dir"
		cp ./urijs/src/URI.js "$destination_dir"
		cp ./urijs/src/URI.min.js "$destination_dir"
		cp ./urijs/src/URITemplate.js "$destination_dir"
	fi

	rm $urijs_temp_archive
	rm -rf ./urijs

	popd > /dev/null
}

download_and_install_toastrjs() {
	echo -e "\t\e[36mInstalling toastr JS library..."

	local version=$1
	local toastrjs_temp_archive="./toastrjs.zip"
	local destination_dir="$PLUGIN_MEDIA_DIR/js/3rdParty/toastr"

	pushd $PLUGIN_TEMP_DIR > /dev/null

	wget "https://github.com/CodeSeven/toastr/archive/$version.zip" -q -O "$toastrjs_temp_archive" > /dev/null

	if [ -f $toastrjs_temp_archive ]
	then
		if [ ! -d "./toastrjs" ]
		then
			mkdir "./toastrjs"
		fi

		bsdtar --strip-components=1 -xf $toastrjs_temp_archive -C ./toastrjs > /dev/null

		if [ ! -d $destination_dir ]
		then
			mkdir $destination_dir
		fi

		cp ./toastrjs/toastr.css "$destination_dir"
		cp ./toastrjs/toastr.js "$destination_dir"
		cp ./toastrjs/build/toastr.js.map "$destination_dir"
		cp ./toastrjs/build/toastr.min.css "$destination_dir"
		cp ./toastrjs/build/toastr.min.js "$destination_dir"
	fi

	rm $toastrjs_temp_archive
	rm -rf ./toastrjs

	popd > /dev/null
}

download_and_install_tippedjs() {
	echo -e "\t\e[36mInstalling tipped JS library..."

	local version=$1
	local tippedjs_temp_archive="./tippedjs.zip"
	local destination_dir="$PLUGIN_MEDIA_DIR/js/3rdParty/tipped"

	pushd $PLUGIN_TEMP_DIR > /dev/null

	wget "https://github.com/staaky/tipped/archive/$version.zip" -q -O "$tippedjs_temp_archive" > /dev/null

	if [ -f $tippedjs_temp_archive ]
	then
		if [ ! -d "./tippedjs" ]
		then
			mkdir "./tippedjs"
		fi

		bsdtar --strip-components=1 -xf $tippedjs_temp_archive -C ./tippedjs > /dev/null

		if [ ! -d $destination_dir ]
		then
			mkdir $destination_dir
		fi

		if [ ! -d "$destination_dir/css" ]
		then
			mkdir "$destination_dir/css"
		fi

		if [ ! -d "$destination_dir/js" ]
		then
			mkdir "$destination_dir/js"
		fi

		cp ./tippedjs/dist/css/* "$destination_dir/css"
		cp ./tippedjs/dist/js/* "$destination_dir/js"
	fi

	rm $tippedjs_temp_archive
	rm -rf ./tippedjs

	popd > /dev/null
}

download_and_install_select2js() {
	echo -e "\t\e[36mInstalling select2 JS library..."

	local version=$1
	local select2_temp_archive="./select2.zip"
	local destination_dir="$PLUGIN_MEDIA_DIR/js/3rdParty/select2"

	pushd $PLUGIN_TEMP_DIR > /dev/null

	wget "https://github.com/select2/select2/archive/$version.zip" -q -O "$select2_temp_archive" > /dev/null

	if [ -f $select2_temp_archive ]
	then
		if [ ! -d "./select2" ]
		then
			mkdir "./select2"
		fi

		bsdtar --strip-components=1 -xf $select2_temp_archive -C ./select2 > /dev/null

		if [ ! -d $destination_dir ]
		then
			mkdir $destination_dir
		fi

		if [ ! -d "$destination_dir/css" ]
		then
			mkdir "$destination_dir/css"
		fi

		if [ ! -d "$destination_dir/js" ]
		then
			mkdir "$destination_dir/js"
		fi

		cp -r ./select2/dist/css/* "$destination_dir/css"
		cp -r ./select2/dist/js/* "$destination_dir/js"
	fi

	rm $select2_temp_archive
	rm -rf ./select2

	popd > /dev/null
}

download_and_install_kitejs() {
	echo -e "\t\e[36mInstalling kite JS library..."

	local kitejs_temp_archive="./kitejs.zip"
	local destination_dir="$PLUGIN_MEDIA_DIR/js/3rdParty"

	pushd $PLUGIN_TEMP_DIR > /dev/null

	wget "https://storage.googleapis.com/google-code-archive-source/v2/code.google.com/kite/source-archive.zip" -q -O "$kitejs_temp_archive" > /dev/null

	if [ -f $kitejs_temp_archive ]
	then
		if [ ! -d "./kitejs" ]
		then
			mkdir "./kitejs"
		fi

		bsdtar --strip-components=1 -xf $kitejs_temp_archive -C ./kitejs > /dev/null

		cp ./kitejs/trunk/kite.js "$destination_dir"
	fi

	rm $kitejs_temp_archive
	rm -rf ./kitejs

	popd > /dev/null
}

ensure_all_guard_files() {
	echo "Creating index.php guard files for public directories..."

	ensure_guard_files $PLUGIN_DIR 1 false
	ensure_guard_files $PLUGIN_LIB_DIR 2 true
	ensure_guard_files $PLUGIN_DATA_DIR 2 true
	ensure_guard_files $PLUGIN_MEDIA_DIR 2 true
	ensure_guard_files $PLUGIN_VIEWS_DIR 2 true
}

ensure_all_plugin_dependencies() {
	echo "Installing plug-in dependencies..."

	download_and_install_mysqlidb "v2.9.3"
	download_and_install_jquery_blockui "2.70"
	download_and_install_urijs "v1.19.2"
	download_and_install_toastrjs "2.1.1"
	download_and_install_tippedjs "v4.7.0"
	download_and_install_select2js "4.0.13"
	download_and_install_kitejs

	echo -e "\e[0mDone installing dependencies"
}

ensure_all_plugin_files() {
	ensure_plugin_core_files
	ensure_plugin_tests_files
	ensure_all_plugin_dependencies
	ensure_all_guard_files
}

ensure_temp_dir() {
	if [ ! -d $PLUGIN_TEMP_DIR ]
	then
		mkdir $PLUGIN_TEMP_DIR
	fi
}

cleanup_temp_dir() {
	if [ -d $PLUGIN_TEMP_DIR ]
	then
		rm -rf $PLUGIN_TEMP_DIR/* > /dev/null
		rm -rf $PLUGIN_TEMP_DIR > /dev/null
	fi
}

ensure_temp_dir

ensure_all_plugin_dirs
ensure_all_plugin_files

cleanup_temp_dir