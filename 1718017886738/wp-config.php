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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

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
define( 'AUTH_KEY',         '@r/v|#T_u^Pw1o8!9hG=yqNb_^JYr!]j4Mh b h1pZKg|5$8x6=+Xi5%]K[`8NQw' );
define( 'SECURE_AUTH_KEY',  '[Tx/LS|x(h7(Pn>H3~3T)y& F00.8~Ep _<5tX(~nmbo<c,eb}i<)cIcvMQI@v$y' );
define( 'LOGGED_IN_KEY',    ',aThhVgxfz8oc)D*@JXgXYsi%9VyOhe-u U*1P]VG;[u9Cix39@#c_;X<<)hr#Nh' );
define( 'NONCE_KEY',        '[iJLz<qp%DksOx_?~EH|ofTZ]{F~UT0 xdu~fR,-cva:_Wo%uC>Zt+lpI%{lB:i#' );
define( 'AUTH_SALT',        'g9xDIWiw:$7HTC|jipFx}@p^h2UA#m1IRQ.S+3Rbtz<nxz6,eZ 7<wtf^ V@a8|z' );
define( 'SECURE_AUTH_SALT', 'YI,7>?e p,J5z<mN<xBM8q04r=alD[7nRL(<cMvIVed};AC+|[64|?a2p*):rZ9R' );
define( 'LOGGED_IN_SALT',   'oj(@5^N.C:kA>n6U03R31/)kKxYQ--:2j/Qgr)Ee O9Z<WQvkf2p%OB5g<5sj#tU' );
define( 'NONCE_SALT',       'jv6f$N!I:o/5(29qzetilWi}G).NJoYS81|4:$ka4z!]7J-VFY*|`FGFRgFGJeC@' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpstg0_'; // Changed by WP STAGING

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
define('WP_LANG_DIR', __DIR__ . '/wp-content/languages');
define('WP_HOME', 'https://localhost/wordpress/1718017886738');
define('WP_SITEURL', 'https://localhost/wordpress/1718017886738');
define('WP_CACHE', false);
define('DISABLE_WP_CRON', false);
if ( ! defined( 'WP_ENVIRONMENT_TYPE' ) ) {
    define('WP_ENVIRONMENT_TYPE', 'staging');
}
define('WP_DEVELOPMENT_MODE', 'all');
if ( ! defined( 'WPSTAGING_DEV_SITE' ) ) {
    define('WPSTAGING_DEV_SITE', true);
}
define('UPLOADS', 'wp-content/uploads');
define('WP_PLUGIN_DIR', __DIR__ . "/wp-content/plugins");
define('WP_PLUGIN_URL', 'https://localhost/wordpress/1718017886738/wp-content/plugins');
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
