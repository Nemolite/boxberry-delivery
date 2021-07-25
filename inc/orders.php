<?php
/**
 * Модуль для работы с checkout
 * 
 */

/**
 * Получение кода ПВЗ из скрытого поля на странице оформления заказа
 */
add_action( 'woocommerce_checkout_update_order_meta', 'boxdev_save_custom_checkout_hidden_field' );
function boxdev_save_custom_checkout_hidden_field( $order_id ) {
    if ( ! empty( $_POST['boxdev_code_pvz'] ) ) {
        update_post_meta( $order_id, '_boxdev_code_pvz', sanitize_text_field( $_POST['boxdev_code_pvz'] ) );
    }

    if ( ! empty( $_POST['boxdev_adr_pvz'] ) ) {
        $boxdev_adr_pvz = sanitize_text_field( $_POST['boxdev_adr_pvz'] );
        $boxdev_code_pvz = boxdev_get_code_on_address( $boxdev_adr_pvz );
        update_post_meta( $order_id, '_boxdev_code_pvz', $boxdev_code_pvz  );
    } 
}

/**
 * Показать код ПВЗ на странице уже оформленного закза
 */
add_action( 'woocommerce_order_details_after_customer_details', 'boxdev_display_verification_id_in_customer_order', 10 );
function boxdev_display_verification_id_in_customer_order( $order ) {
    // compatibility with WC +3
    $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;

    echo '<p class="boxdev_code_pvz"><strong>'.__('Код ПВЗ', 'boxdev') . ':</strong> ' . get_post_meta( $order_id, '_boxdev_code_pvz', true ) .'</p>';
}

/**
 * Показать код ПВЗ на странице закзов в админке
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'boxdev_display_verification_id_in_admin_order_meta', 10, 1 );
function boxdev_display_verification_id_in_admin_order_meta( $order ) {
    // compatibility with WC +3
    $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
    echo '<p class="boxdev_code_pvz"><strong>'.__('Код ПВЗ', 'boxdev') . ':</strong><span id="order_code_pvz">' . get_post_meta( $order_id, '_boxdev_code_pvz', true ) .'</span></p>';
}
?>