<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'blog' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'uk.ZgKV{2V>46^u`5Qd{J+?1 iLTN}*c!i]*ctITZkroQ$Z)n^%oFje9@@^)!6~[' );
define( 'SECURE_AUTH_KEY',  ':gI24x|2&<QO<a21p#<rLt_?WYTlfj!3.n1;-H8;$Fe;%724!C$%Nm25@e[K*U:=' );
define( 'LOGGED_IN_KEY',    'y#v~4Nr@9cbEA!_bTYG#c1KE6E$ )=}%j/Y9D@dn>lwsPJhyTt.7AhXE`D1B(CJc' );
define( 'NONCE_KEY',        'zy{ X-2,)|F+)wA k,!8j[IuwDC/OQ %e}u&O]_~F$1ItV1-8p&U(:naQ)gbCU0#' );
define( 'AUTH_SALT',        'ZN54IMOV|3n>yAM7r# 0+jN1N_qJKN|s{pW:(wiK{yR1+TtuAzrDeqNlOlF6Tw+O' );
define( 'SECURE_AUTH_SALT', 'RO13MdTRmq*heimR+s~lRyLk;Myw8@KG(B+Adr2?HIM8aMm]!r{z4LRl-XX:PEzq' );
define( 'LOGGED_IN_SALT',   'gqyI6Q,s?F+CVbmJp;~e*myCG^mm-$J`[^4uXt#/35f2f]1(q{3~<9wHAix<p:p*' );
define( 'NONCE_SALT',       'H&C!xIUm[kLaTs]?WXoUY&nGMhvXL0qWpjWGH|xEG++IFzUAB@:0k}z66c7Mlj2J' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);


/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
