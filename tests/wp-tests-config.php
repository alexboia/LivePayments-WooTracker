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

define('AUTH_KEY',         'i8{o5-0w)wU/}e,|e=u@d/wiDisZ8<BF2(My1g}qanBx,V6MH/_%(9US8pmD>I)%');
define('SECURE_AUTH_KEY',  ':1#v.(h_O3-o7/{l4vb*sc7MtXrsS]f{P!5#aM/G]Qq-o6>K+gjRfAP}P)pD`ryw');
define('LOGGED_IN_KEY',    '87!g4!M JRj+60ZmZ-}QhJ@WP7D^oonlcdqeu<C[v---z*SZwT5Las;+(w#`[#D~');
define('NONCE_KEY',        'Gb)%-|iD:w3CmU2CL/d&W@kDU~jvc,c:!uRh@afBekgjVU.0P)h)T<AgNd(Uk]{8');
define('AUTH_SALT',        'atz?GOh=n?YLgR(i_JY6/vF&Q0D1FGm-g{ZehdU|a*-Bp~V(mLU75ds1:Tb P&;c');
define('SECURE_AUTH_SALT', 'rzwEm9jUy#)io$Qh<9a?d-0C2%20A*w6`P0=GnAd)*5SFI>M^[4uk=,C u)}j+5p');
define('LOGGED_IN_SALT',   'j;tcr9kUX}549hy}by!oAS@eT+-)$=sA}Hea=!S#g<9X]IjmVF*^.F35h{r!c!c(');
define('NONCE_SALT',       'oZ-Wp8mFh(|Y>sR5-6@&Q*t4n&=IUkk32@XVdu95TX$7:zpoT:S5h;8<-j?IIrmd');

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

