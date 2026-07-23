<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          '*&j$6wJ9O`pJ!$7/c&`h0Sk[?UND!@4b]L1<+}wM#MsEM6739JOz3/UJuM[:>(BV' );
define( 'SECURE_AUTH_KEY',   'J[T3L1j>P**gi3n9WNH^k<7K~_:~OoV]g+BpbmzcdTGz!QRe+fkF=avi8]Kq%7H4' );
define( 'LOGGED_IN_KEY',     'x42qK.1z[D^A-tM!:i<#0<jhISpAJ5S(yXgN3dpP^S+kcJkR6mUU*YWiCZBcIqQ,' );
define( 'NONCE_KEY',         '1puiR&5|f(4z1||/<W/39OtUqla^GnYS>^MmOV.J?6W`55MD+N$Ym~(EAE+q@et_' );
define( 'AUTH_SALT',         '*;^aN;v;AW[{YqGyUk>eS_y2adO8^C7ETZbeRobmi^BJM123~nd$~-OHmjS7$6Y0' );
define( 'SECURE_AUTH_SALT',  'xz#fY~Ipo+$HY/rwAvY^Zd7qe&(3Fk-I>5*2i(,TJ.gYe`2I`G7E;Kv0b%U-|Kti' );
define( 'LOGGED_IN_SALT',    'Oz&6;>Y/cIk3d)rb)oGNz`5lkC8%*,_/S<%so)(L;wr>~Iz_:s50c?|8Xuo.PQ|9' );
define( 'NONCE_SALT',        '{Z|pflGua.>)e[!{)sWmYKRzad-}DvD|U>4q8k)P>@kSSU)OtAlI?qk]|=ROROS_' );
define( 'WP_CACHE_KEY_SALT', 'tje*P5A}#iEB0G>Rx..Qxrj<WS+@,XHFO/C]9M;g7]ZKZ*}1[:{5.E#d![65rrt ' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
