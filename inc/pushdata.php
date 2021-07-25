<?php
/**
 * Модуль отправления данных
 * 
 */

add_action('woocommerce_admin_order_totals_after_total','boxdev_admin_order_totals_after_tax', 1);
function boxdev_admin_order_totals_after_tax( $order_id ){     

    /**
     * Вывод кнопки отправления
     */       
    echo '<div id="boxdev-block">';
    echo '<input name="boxdev_order_id" id="boxdev_order_id" data-order_id = "'.$order_id.'" type="hidden" />';  
    echo '<p id="boxdev-messege"></p>';
    echo '<input id="boxdev-send" class="button" type="button" value="Отправить на доставку в Boxberry">';    
    echo '</div>';

}

/**
 * Отправление данных по API 
 * 
 */
add_action('wp_ajax_boxdev_order_id', 'boxdev_action_order_id'); 
function boxdev_action_order_id(){ 
   

    if ( isset( $_POST['boxdev_order_id'] ) ) {
        $order_id = sanitize_text_field( $_POST['boxdev_order_id'] );       

    }

    /**
     * Получение данных
     */
    $order = wc_get_order( $order_id );     

    $boxdev_local_point = new WC_Shipping_Local_Point();
   
    $defaultWeight = $boxdev_local_point->get_option('default_weight');
    $defaultHeight = $boxdev_local_point->get_option( 'default_height' );
    $defaultWidth = $boxdev_local_point->get_option( 'default_width' );
    $defaultLength = $boxdev_local_point->get_option( 'default_length' );

    $boxberry_from = $boxdev_local_point->get_option( 'boxberry_from' );

    $token = boxdev_get_key();
    $apiurl = boxdev_get_url();

    /**
    * Подготовка данных к отправке
    */   
   $SDATA=array();

   $SDATA['order_id'] = $token; // token

   $SDATA['order_id'] = $order_id;     // Номер заказа в интернет-магазине  
   $SDATA['payment_sum']=$order->get_total(); // Сумма к оплате с получателя
   $SDATA['price']=$order->get_total();       // Объявленная стоимость
   $SDATA['delivery_sum']=$order->get_shipping_total(); // Стоимость доставки объявленная получателю
   $SDATA['vid']='1'; // 1- Доставка до пункта выдачи (ПВЗ)
   
  
   $SDATA['shop']=array( //
       'name'=> get_post_meta( $order_id, '_boxdev_code_pvz', true ), // Код пункта выдачи   
       'name1'=> $boxberry_from, // Код пункта приема  

       
   );
   $SDATA['customer']=array(
       'fio'=>$order->get_formatted_billing_address(), // ФИО получателя
       'phone'=>$order->get_billing_phone(),           // Номер телефона получателя,        
       'email'=>$order->get_billing_email(),  // E-mail получателя для оповещений             
       'address'=>$order->get_billing_address_1(), // Адрес 

   );  

   $total_weight = 0;
   $total_height = 0;
   $total_width = 0;
   $total_length = 0;
  
   foreach ( $order->get_items() as $item_id => $item ) {
       $product_id = $item->get_product_id();       
       $product_name = $item->get_name();
       $quantity = $item->get_quantity();  
       $total = $item->get_total();        
       
       $product = wc_get_product( $product_id );     
       $total_weight += $product->get_weight();
       $total_height  += $product->get_length();  // z    
       $total_width += $product->get_width(); // x 
       $total_length += $product->get_height(); // y
       
       
       $SDATA['items'][$item_id]=array(
           array(
               'id'=>$product_id,
               'name'=>$product_name,           
               'nds'=>0,
               'price'=>$total,
               'quantity'=>$quantity
           )
       );
         
    }      
   
   $weight = ($total_weight * $quantity) + $defaultWeight;
   $sdata_z = ($total_height+ $defaultHeight) * 0.1;
   $sdata_x = ($total_width  + $defaultWidth) * 0.1;
   $sdata_y = ( ($total_length * $quantity ) + $defaultLength) * 0.1;
  
   $SDATA['weights']=array(
       'weight'=>$weight,
       'z'=>$sdata_z,
       'x'=>$sdata_x,
       'y'=>$sdata_y,        
   ); 

   /**
    * Отправление данных
    */

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiurl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        'token'=>$token,
        'method'=>'ParselCreate',
        'sdata'=>json_encode($SDATA)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = json_decode(curl_exec($ch),1);
    if( count($data)<=0 )
    {
        echo "Отправление не создано";
    }
    else
    {
        // все отлично, ответ получен, теперь в массиве $data.
        // (если был указан штрих-код то переменная label будет отсутствовать).
        /*
        $data=array(
            'track'=>'XXXXXXXX', // Трекинг код для посылки.
            'label'=>'http://' // Ссылка на скачивание PDF файла с этикетками.
        );
        */

        show($data);

    }    
    wp_die();
}
 ?>