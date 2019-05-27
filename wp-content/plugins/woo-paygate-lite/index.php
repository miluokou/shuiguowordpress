<?php
/*
 * Plugin Name: Woo PayGate Lite
 * Plugin URI: https://www.wpcom.cn/plugins/woo-paygate.html
 * Description: WooCommerce插件微信、支付宝支付网关（免费版）
 * Version: 1.0.1
 * Author: WPCOM
 * Author URI: https://www.wpcom.cn
*/

defined( 'ABSPATH' ) || exit;
define( 'WPGate_VERSION', '1.0.1' );
define( 'WPGate_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPGate_URI', plugins_url( '/', __FILE__ ) );

add_action( 'plugins_loaded', 'WPGate_init' );
function WPGate_init() {
    if( !class_exists('WC_Payment_Gateway') || class_exists('WPGate_Alipay') )  return;
    load_plugin_textdomain( 'wpcom', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/'  );

    require_once WPGate_DIR . 'includes/class-alipay.php';
    require_once WPGate_DIR . 'includes/class-wxpay.php';
    new WPGate_Wxpay();
    new WPGate_Alipay();
}

add_action( 'admin_menu', 'WPGate_scripts' );
function WPGate_scripts(){
    wp_enqueue_style("woo-paygate", WPGate_URI."/css/style.css", false, WPGate_VERSION, "all");
}

add_action( 'wp_enqueue_scripts', 'WPGate_enqueue_scripts' );
function WPGate_enqueue_scripts() {
    if( function_exists('is_checkout_pay_page') && is_checkout_pay_page() && !isset($_GET['pay_for_order']) ){
        $orderId = get_query_var ( 'order-pay' );
        $order = new WC_Order ( $orderId );
        $payment_method = method_exists($order, 'get_payment_method')?$order->get_payment_method():$order->payment_method;
        if( 'wxpay' == $payment_method ) {
            wp_enqueue_script( 'wpg-wechat', WPGate_URI . 'js/wechat.js' , array('jquery'), WPGate_VERSION );
        }
    }
    if( function_exists('is_checkout') && is_checkout() ){
        wp_enqueue_style( 'woo-paygate', WPGate_URI . 'css/style.css', false, WPGate_VERSION, 'all' );
    }
}