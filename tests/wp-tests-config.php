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

/*
 * This configuration file will be used by the copy of WordPress being tested.
 * wordpress/wp-config.php will be ignored.
 *
 * WARNING WARNING WARNING!
 * These tests will DROP ALL TABLES in the database with the prefix named below.
 * DO NOT use a production database or one that is shared with something else.
 */

define( 'DB_NAME', '' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', '127.0.0.1' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 */

define('AUTH_KEY',         'gkAU_6X/XF6/4Qyz}Y<Mg7*|$BcYk4L2KttXwZLA]<#Vkmdfi,=JjDaeT?I_uKJ:');
define('SECURE_AUTH_KEY',  '3<|Sp}OOOiK%RG_v*+vOG<{ HX^)WyG4M[;Z486K>~8~J4hgC8a-zyCk}hG|aY~:');
define('LOGGED_IN_KEY',    'tgZl+N[=?qnz)nVy&D+;xCwLIc`@0|,U<r9,|Go|#9$>T5JFw2byO@N 5ndW>M3d');
define('NONCE_KEY',        '.H34H_Be^kd_I_|_1&RUkwZ_PKI|(]D?hz1/]UaoniS[m -3Liz(/D=5rQ2LWixF');
define('AUTH_SALT',        '6[lJ>$e4Ynbulxxz,nrNw{~{}@1|/,-KZDE+!mylD-+={jHApcB6UDc$8Y8-a:Yw');
define('SECURE_AUTH_SALT', '/Yar42d-kgklEgJ/f+h!$)Qy|FG)@<O5rIIWRA,_Bw[`;6e[viKjCq=_+r]q<3@u');
define('LOGGED_IN_SALT',   'X{zN/vl!QfJ(d#{WG@=~RmRQd(QH8|k.|FcaR,>k`DoS&Q+9K(uYIU@-|x+6F<)0');
define('NONCE_SALT',       'Ua%(zgp0AN@E4rh|85aycOEtFV{*Mf51F^1:gwh5hGUpSRe^`|}W-~dHMMv!3 8t');

/**
 * Table prefix.
 * Only numbers, letters, and underscores please!
 */

$table_prefix = 'wp_';

define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );

define( 'WP_PHP_BINARY', 'php' );

define( 'WPLANG', '' );

