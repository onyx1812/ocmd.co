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
define( 'DB_NAME', 'ocmd' );

/** MySQL database username */
define( 'DB_USER', 'mysql' );

/** MySQL database password */
define( 'DB_PASSWORD', 'mysql' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
// define( 'AUTH_KEY',         '/]~c/%#~#WrtPMg+s:;gfT|v|{-5Y+9d@%Z|zU0W4d*H^ju$3QYr?edCM}?mjim~' );
// define( 'SECURE_AUTH_KEY',  '04ALY)@l4@A{$(Jj{!j@K]|sY( kMK646&.T4R7I)ZjDlxN7X.cX)pth]{f2GnE2' );
// define( 'LOGGED_IN_KEY',    'D*W]>luJCR9_d0}&}3nbri7s Q]8EMsB;pg+-+Z) BbgZ5)|l%e`r6ECvB4$/HI.' );
// define( 'NONCE_KEY',        '*IEIp,v>v+r;G E)uLJzp;X4@CpDp0j:/:>{IIrP?ZI1P!H9QYVkwe4h{=G)2vR5' );
// define( 'AUTH_SALT',        'sY,XQml?uVh|co&$6c~eb6l[~-Ik|G2./INrT4Uov=&g!jynvm7h6t/Yk?)s!#yb' );
// define( 'SECURE_AUTH_SALT', 'A%fwW2[a3 Nja^oO~g;s/bPM,<iKE~]:=D3M3|[Da;{&M#F:7F|+f>:[f66R&Hl&' );
// define( 'LOGGED_IN_SALT',   '}XvI6I2EP`lp7tX>rCpc*~Y;*~`v1?+S@*ZA2uWwsrf$zvRzm#+RkCf5Zn`8wCf,' );
// define( 'NONCE_SALT',       'ts2d<L@wIa%?gj8nO8nF!n+ YQHN&w`+Lkf@EIg95+YX!k#xl:*+q$rqm%CXmV)H' );

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
