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
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1:3309');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'X{;Pac95[3`V$JYuQv+MszaE4U /~kI-}^~ Ta?m3.|Ag|nx_U?5H{j#b~;f^_rI');
define('SECURE_AUTH_KEY',  '~Mju(-!5[uavwd%:mI{!5bqkMG)@(BCplVY0lDU)n~BvSN?6>5%MEG<oh^/bsbPq');
define('LOGGED_IN_KEY',    'V!%Hl`4L%g+i@Yw(v/M%CKG@}6jMCkjEC]irV{LfPNn9 z#m}Ov:U,Nj];M%ENhk');
define('NONCE_KEY',        'E}1PJ!%7pS;w&m1:BJh4f,_-n9e0gD@-.kz@XL9QedUT_XOka8BGPm`~&]{[uNpn');
define('AUTH_SALT',        '8mZn+ EdxxXS@`NeiYte}$YODtUCnx0*klLX#fp %hGDN,0C9iQ!MN(.N0:]T%<j');
define('SECURE_AUTH_SALT', 'Z@H|PnP;!Z6R:T^ttWe5 .)6.MF[,AH7 qLK`>b8<AK}0bM>I3#*_iQJzO0oe_QK');
define('LOGGED_IN_SALT',   'Q):*dHy:`^]0XbcPpou:Vj0sp/F+(BA+|Y?!;:E[:kFE{0Q()d/7/zKfUiL7UlP<');
define('NONCE_SALT',       'UR55Wtc9pqq3O]sBniLwMA/Pq;hR@c<w!}fPnc2HraQ:W~C-x/3y-R.YHq+Ptn>H');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
