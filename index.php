<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define( 'WP_USE_THEMES', true );

/** Loads the WordPress Environment and Template */
//https://www.solagirl.net/woo-ecommerce-extend-payment-gateways.html
//https://www.solagirl.net/woocommerce-alipay-plugin.html
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
