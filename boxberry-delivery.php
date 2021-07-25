<?php
/**
* Plugin Name: boxberry-delivery (boxdev)
* Plugin URI: https://github.com/Nemolite/boxberry-delivery
* Description: Отправка и получение данных о заказах на Boxberry 
* Version: 1.0.0
* Author: Nemolite
* Author URI: http://vandraren.ru/
* License: GPL2
*/

defined('ABSPATH') || exit;

/**
 * Подключение скриптов и стилей
 */

function script_and_style_boxdev(){
  wp_enqueue_style( 'boxdev-style',  plugins_url('assets/css/style.css', __FILE__));
  wp_enqueue_script( 'boxdev-script', plugins_url('assets/js/boxdev.js', __FILE__),array(),'1.0.0','in_footer');
}
add_action( 'wp_enqueue_scripts', 'script_and_style_boxdev' );

/**
 * Подключение скриптов и стилей для админки
 */

function script_and_style_boxdev_admin(){
	wp_enqueue_style( 'boxdev-adminatyle',  plugins_url('assets/css/style-admin.css', __FILE__));
	wp_enqueue_script( 'boxdev-adminscript', plugins_url('assets/js/boxdev-admin.js', __FILE__),array(),'1.0.0','in_footer');

	wp_localize_script( 'boxdev-adminscript', 'myajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
  }
  add_action( 'admin_enqueue_scripts', 'script_and_style_boxdev_admin' );

 /**
  * Helper
  */ 

 function show($array) {
   echo "<pre>";
   print_r($array);
   echo "</pre>";

 }

/**
 * Модуль создания меню
 */
require "inc/menu.php";  

/**
 *  Модуль для работы с checkout
 */
require "inc/orders.php";  


/**
 * Модуль получения данных
 */
require "inc/pulldata.php";

/**
 * Модуль отправления данных
 */
require "inc/pushdata.php";

/**
 * Класс (наследник класса WC_Shipping_Local_Point( WC_Shipping_Method  ) ) 
 * 
 */
require "inc/class-boxdev.php";

add_filter( 'woocommerce_shipping_methods', 'boxdev_add_shipping_class' );

/**
 * Для того чтобы класс был виден в системе
 *
 * @param array $methods
 * @return array $methods
 */
function boxdev_add_shipping_class( $methods ) {
	$methods[ 'truemisha_shipping_method' ] = 'WC_Boxdev_Shipping_Method'; 
	return $methods;
}

?>