<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */



// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', "monarchydb");

/** MySQL database username */
define('DB_USER', "monarchyadmin");

/** MySQL database password */
define('DB_PASSWORD', "n3wv0yag3");

/** MySQL hostname */
define('DB_HOST', "213.171.200.46");

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'QnD+;a(:~:qEzub=%~K5V-;eCnk9DCXjKkZ|c7;xC9+9P]pZhuf2^bav>]|x$,Qq');
define('SECURE_AUTH_KEY',  'srO:Ow!+t4d09zd+OOy04+tL+>BlbKH%?kWnSCF2EG!H~^}6J:Z#.{_kob7LO2r&');
define('LOGGED_IN_KEY',    'Pq4Sk|~-R&?A?X<m]91HXWB;viPGO3Wsj@u{K-`xMmd]3d.#kOT`7@@rm<o$,GL{');
define('NONCE_KEY',        ' *mcPOdw9Wd4+Bc>+<Fh/9nFSzSiIWG,x*0oTNHR,=aa<0qz.*%+NWQ/PxueF5gQ');
define('AUTH_SALT',        'WjV]2-d*H+CY:`}|5<|caPP^l3$4|{b_M]@4s|+@+sO&{:.PW4>JK.|,:<rvHJ)>');
define('SECURE_AUTH_SALT', 'U!,$&.@`[+RX=y-&Vg5*cW+pe}F dY|@%kmD`Wje<c+f9@_(Aqp+S^0sQJ{*u?~Y');
define('LOGGED_IN_SALT',   '>.z_/s 3R.}k?Ff+]LOgtN ?i#VbQzjf`314@rjXq-SN#AZ!8YxDH?5?{[N-,8?Z');
define('NONCE_SALT',       '2]uB#HmV-9+pEpTxHAEFPHyzv_!ke( 1~G1.:zfl2%ya<?!#[oDe6j-l0u`#+z6z');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_sit';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
