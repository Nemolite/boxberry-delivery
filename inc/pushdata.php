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
    $url_label = get_post_meta( $order_id, '_boxdev_url_label', true );
    if(!empty($url_label) ){
        echo '<p id="boxdev-messege"><a href="' .$url_label. '">Ссылка на скачивание PDF файла с этикетками</a></p>'; 
    }  
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
        $order_id = sanitize_text_field( json_decode($_POST['boxdev_order_id'] ) );       

    }

    $order = wc_get_order( $order_id );  

    $SDATA = boxdev_prepare_sdata( $order, $order_id );  

   
    $token = boxdev_get_key();
    $apiurl = boxdev_get_url();

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
      update_post_meta( $order_id, '_boxdev_url_label', $data['label']  );
      echo $data['track'];
     // echo $data;
    }    
    wp_die();
}

/**
 * Подготовка данных для отправки
 * 
 */
function boxdev_prepare_sdata( $order, $order_id ){
    
    $boxdev_local_point = new WC_Boxdev_Shipping_Method();  
    
    // Пункт приема , по умолчанию 10.042
    $boxberry_from = $boxdev_local_point->get_option( 'boxdev_from' );
    
   // Инициализация      
   $SDATA=array();
   
  // $SDATA['order_id'] = $order_id;     // Номер заказа в интернет-магазине  
  // При оплате заказа на сайте сумму к оплате с получаетля не выставлять
  
  $order_total = "0";
  if (($order->get_status() == 'processing')&&($order->get_payment_method() == "nal") ) {
    $order_total = $order->get_total();
  } 

   $SDATA['order_id'] = $order_id;     // Номер заказа в интернет-магазине  
   $SDATA['payment_sum']=$order_total; // Сумма к оплате с получателя
   $SDATA['price']= 0;      // $order->get_total(); Объявленная стоимость- всегда ноль
   $SDATA['delivery_sum']=$order->get_shipping_total(); // Стоимость доставки объявленная получателю
   $SDATA['vid']='1'; // 1- Доставка до пункта выдачи (ПВЗ)
   
   $SDATA['issue']='0'; // - без вскрытия
   
   $SDATA['shop']=array( 
       'name'=> get_post_meta( $order_id, '_boxdev_code_pvz', true ), // Код пункта выдачи   
       'name1'=> $boxberry_from, // Код пункта приема         
   );

   $fio = $order->get_billing_last_name()." ".$order->get_billing_first_name()." ".get_post_meta( $order_id, '_billing_new_fild11', true );
   $SDATA['customer']=array(
       'fio'=>$fio, // ФИО получателя
       'phone'=>$order->get_billing_phone(),           // Номер телефона получателя,            
       // 'email'=>$order->get_billing_email(),  // E-mail получателя для оповещений             
       'address'=>$order->get_billing_address_1(), // Адрес 

   );   
 
    $items_inner = array();

   foreach ( $order->get_items() as $item_id => $item ) {
       $product_id = $item->get_product_id();       
       $product_name = $item->get_name();
       $quantity = $item->get_quantity();  
       $total = $item->get_total();
  
        $items_inner[] = array(               
            'name'=>$product_name,          
            'price'=>$total,
            'quantity'=>$quantity
        );
       
    } 
    
    $SDATA['items'] = $items_inner;  

    // Функция получения веса

    $weight = boxdev_calculation_weights( $order );    

    if (300<$weight) {
        $weight_new = $weight; 
     }  else {
        $weight_new = 300; 
     }   

    $height = boxdev_calculation_heights( $order );
    $width  = boxdev_calculation_widths( $order );
    $length = boxdev_calculation_lengths( $order );


   $SDATA['weights']=array(
       'weight'=>$weight_new,
       'y' => $height,
       'x' => $width,
       'z' => $length,
           
   ); 

   return $SDATA;
}

/**
 * Расчет веса
 */
function boxdev_calculation_weights( $order ){

    $boxdev_local_point = new WC_Boxdev_Shipping_Method();   
    // Первоначальный вес
    $defaultWeight = floatval( $boxdev_local_point->boxdev_default_weight );
    
    // Инициализация
    $total_weight = 0;

    foreach ( $order->get_items() as $item ) {
        $product_id = $item->get_product_id();
        $product = wc_get_product( $product_id );
        $weight_one_product = floatval( $product->get_weight() ); // вес одного товара         
       
        $quantity = $item->get_quantity();  // Количество товара

       $total_weight_one_product = $weight_one_product * $quantity; 
       $total_weight += $total_weight_one_product; 
       
        
     } 
     // Переводим в граммы 

    if ( $total_weight < $defaultWeight ) {
        $res_weight = $defaultWeight;
    } else {
        $res_weight = $total_weight;
    }

    $res_weight =$res_weight * 1000;
    return $res_weight;    
}  

/**
 * Расчет высоты Y
 */
function boxdev_calculation_heights( $order ){

    $boxdev_local_point = new WC_Boxdev_Shipping_Method();   
    // Первоначальный вес
    $defaultHeight = floatval($boxdev_local_point->boxdev_default_height);
    
    // Инициализация
    $total_height = 0;

    foreach ( $order->get_items() as $item ) {
        $product_id = $item->get_product_id();
        $product = wc_get_product( $product_id );
        $height_one_product = floatval( $product->get_height() ); // высота одного товара мм        
       
        $quantity = $item->get_quantity();  // Количество товара

       $total_height_one_product = $height_one_product * $quantity; 
       $total_height += $total_height_one_product; // мм   
       
        
     } 
        // переводим в см и округляем в большую сторону

        $total_height = ceil($total_height / 10);

     // Добавляем запас в 1 см
    $height = (floatval($total_height) + $defaultHeight);

    return $height;
    
} 

/**
 * Расчет ширины Х
 */
function boxdev_calculation_widths( $order ){

    $boxdev_local_point = new WC_Boxdev_Shipping_Method();   
    // Первоначальный вес
    $defaultWidth = floatval($boxdev_local_point->boxdev_default_width);
    
    // Инициализация
    $total_width = 0;

    foreach ( $order->get_items() as $item ) {
        $product_id = $item->get_product_id();
        $product = wc_get_product( $product_id );
        $width_one_product = floatval( $product->get_width() ); // ширина одного товара мм        
     
        $total_width += $width_one_product; // мм           
        
     } 

        // переводим в см и округляем в большую сторону

        $total_width = ceil($total_width / 10);

     // Добавляем запас в 1 см
    $width = (floatval($total_width) + $defaultWidth);

    return $width;
    
} 

/**
 * Расчет длины Z
 */
function boxdev_calculation_lengths( $order ){

    $boxdev_local_point = new WC_Boxdev_Shipping_Method();   
    // Первоначальный вес
    $defaultLength = floatval($boxdev_local_point->boxdev_default_length);
    
    // Инициализация
    $total_length = 0;

    foreach ( $order->get_items() as $item ) {
        $product_id = $item->get_product_id();
        $product = wc_get_product( $product_id );
        $length_one_product = floatval( $product->get_length() ); // длина одного товара мм        
             
        $total_length += $length_one_product; // мм            
        
     } 

        // переводим в см и округляем в большую сторону

        $total_length = ceil( $total_length / 10);

     // Добавляем запас в 1 см
    $length = (floatval($total_length) + $defaultLength);

    return $length;
    
} 
?>