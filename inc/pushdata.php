<?php
/**
 * Модуль отправления данных
 * 
 */

add_action('woocommerce_admin_order_totals_after_total','boxdev_admin_order_totals_after_tax', 1);
function boxdev_admin_order_totals_after_tax( $order_id ){
     echo "= " . $order_id;
     $order = wc_get_order( $order_id );
     echo "<pre>";
     print_r($order);
     echo "</pre>";

     echo "<br>";
     echo "<br>";
     echo "<br>";

     echo "<pre>";
     print_r( $order->get_meta_data() );
     echo "</pre>";

     
     

       /**
     * Подготовка данных к отправке
     */
    $SDATA=array();

    $SDATA['order_id'] = $order_id;    
    // $SDATA['PalletNumber']='Номер палеты';
    // $SDATA['barcode']='Штрих-код заказа';
    //$SDATA['price']='Объявленная стоимость';
    $SDATA['payment_sum']=$order->get_total(); 
    $SDATA['delivery_sum']=$order->get_shipping_total();
    $SDATA['vid']='1';

    $address = '603140, Нижний Новгород г, Ленина пр-кт, д.31';

    

    if( boxdev_get_code_on_address($address) ) {
        $code_name = boxdev_get_code_on_address($address);  
    }
    $SDATA['shop']=array(
        'name'=>$code_name
        
    );
    $SDATA['customer']=array(
        'fio'=>$order->get_user(),
        'phone'=>$order->get_billing_phone(),
        'phone2'=>'Доп. номер телефона',
        'email'=>$order->get_billing_email(),
        'name'=>'Наименование организации',
        'address'=>$order->get_billing_address_1(),
        'inn'=>'ИНН',
        'kpp'=>'КПП',
        'r_s'=>'Расчетный счет',
        'bank'=>'Наименование банка',
        'kor_s'=>'Кор. счет',
        'bik'=>'БИК',

        'test_billing_address' => $order->get_formatted_billing_address(),
        'test_shipping_address' => $order->get_formatted_shipping_address(),

    );
    foreach ( $order->get_items() as $item_id => $item ) {
        $product_id = $item->get_product_id();
        $variation_id = $item->get_variation_id();
        $product = $item->get_product();
        $product_name = $item->get_name();
        $quantity = $item->get_quantity();
        $subtotal = $item->get_subtotal();
        $total = $item->get_total();
        $tax = $item->get_subtotal_tax();
        $taxclass = $item->get_tax_class();
        $taxstat = $item->get_tax_status();
        $allmeta = $item->get_meta_data();
        $somemeta = $item->get_meta( '_whatever', true );
        $product_type = $item->get_type();
     }

     echo "<pre>";
     print_r($item);
     echo "</pre>";
    
    $SDATA['items']=array(
        array(
            'id'=>$product_id,
            'name'=>$product_name,           
            'nds'=>0,
            'price'=>$total,
            'quantity'=>$quantity
        )
    );
    $SDATA['weights']=array(
        'weight'=>'Вес 1-ого места',
        'barcode'=>'Баркод 1-го места',
        'weight2'=>'Вес 2-ого места',
        'barcode2'=>'Баркод 2-го места',
        'weight3'=>'Вес 3-его места',
        'barcode3'=>'Баркод 3-го места',
        'weight4'=>'Вес 4-ого места',
        'barcode4'=>'Баркод 4-го места',
        'weight5'=>'Вес 5-ого места',
        'barcode5'=>'Баркод 5-го места'
    );

    /**
     * Отправка данных
     */

        // Предполагается что Вы уже создали массив $SDATA по описанному выше примеру.
    // Отправляем массив на сервер boxberry используя CURL.
    echo "Данные которые уйдут на boxberry";

   echo "<pre>";
   print_r($SDATA);
   echo "</pre>";
    
     

}
 ?>