<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'test' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         ',9Tfn5]c[mkN:-G#<>1|eJKy91t,/xC!8MJ2[JTnS=jt6E3,Or]?}*Bp!@AXQC.P' );
define( 'SECURE_AUTH_KEY',  'cjo5(ud<X}rD15zh|>o%4FZbN8mIVx4Yz2;s~#a4NMk*6a=<VNR8##XvKLb K[n ' );
define( 'LOGGED_IN_KEY',    'a^[.J6B!s6ZP~2M`Xp8.4-1^~;cJAh[`iq;Pw^^&iZo_YEhAy(zb,Y*_LK`J.d3}' );
define( 'NONCE_KEY',        'T%i@3>~%I|l&nZCIA:5C;8WKQo{[d)EEPf)*%Hq?`6hDkAI,FB70^1IGyHn#3|bN' );
define( 'AUTH_SALT',        '^2_0C17w:Gxu)|[K, MCyPR<U1]R`jZF9bE6&T#e1^;*h=nid)0>wq|G}W.Dm@J#' );
define( 'SECURE_AUTH_SALT', '_|50@F5Vgy*a|sf_1*ak$uU$#!2lJg+kYp  e1iW~iTJ_L0vWM3v?nHG-5OD?U4O' );
define( 'LOGGED_IN_SALT',   'KQ^?.~a)MIq0{W#*hHX7zQy#]bk{ZNmD&Ei*A[1HG/@L9})Wf]yG_<k~yRI*B|&c' );
define( 'NONCE_SALT',       ';]SmSRDi}N=|ZFZ;} qSIcf}Y;Mk!@B4N@xnsb*J.1IUJh>?#VH%hbr//:>/Xyr=' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
