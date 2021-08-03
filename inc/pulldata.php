<?php
/**
 * Модуль получения данных
 * 
 */

function boxdev_pull_data() { 
    $token = boxdev_get_key(); 
    $url = 'http://api.boxberry.ru/json.php?token='.$token.'&method=OrdersBalance';
    
    $handle = fopen($url, "rb");
    $contents = stream_get_contents($handle);
    fclose($handle);

    $data = json_decode($contents, true);
    if (count($data) <= 0 ) {
        
        echo "Данные по отправлениям отсутствуют";
    }else{ 
        ?>

<table>
<caption>
    <h3>Информация по заказам, которые фактически переданы на доставку в Boxberry</h3>
    
</caption>
  <tr>
    <th>Номер заказа,<br> присвоенный интернет-магазином</th>
    <th>Статус заказа</th>
    <th>Стоимость товаров</th>
    <th>Стоимость доставки</th>
    <th>Сумма к оплате</th>    
  </tr>
  
    <?php 
      
        $boxdev_repetitions = ( $data ) ? count( $data ) : 0;
            if (0!==$boxdev_repetitions) {
    ?>
        <?php foreach ($data as $data_show)  { ?>
            <tr>
                <td>
                <?php echo esc_html($data_show['ID']);?>  
                </td>
                <td>
                    <?php echo esc_html($data_show['Status']);?>    
                </td>
                <td>
                    <?php echo esc_html($data_show['Price']);?>   
                </td>
                <td>
                    <?php echo esc_html($data_show['Delivery_sum']);?>  
                </td>
                <td>
                <?php echo esc_html($data_show['Payment_sum']);?> 
                </td>
            </tr>
        <?php } ?>    
    
 
        <?php
       
        } else {
        echo "Товаров переданных на доставку не имеется";
        }
        ?>
  
</table>
       <?php
    }
}

/**
 *  Получение кода ПВЗ по адресу
 */

function boxdev_get_code_on_address( $address ){
    
    $url='http://api.boxberry.ru/json.php?token='.BOXBERRY_TOKEN.'&method=ListPoints';
    $handle = fopen($url, "rb");
    $contents = stream_get_contents($handle);
    fclose($handle);
    $data=json_decode($contents,true);
    if(count($data)<=0 )
        {
           $cod_zero = 0; 
           return $cod_zero;
        
        }
    else
    {
             
        foreach ($data as $val){
            if ($address==$val['Address']){
               
                return $val['Code'];

            }       
        }
    }
}

function boxdev_get_meta_box_from_order( $order_id ){    

    global $wpdb;
    $table_name = $wpdb->prefix . 'woocommerce_order_itemmeta';
    $results = $wpdb->get_results( 'SELECT * FROM '.$table_name. ' WHERE order_item_id='.$order_id, ARRAY_A );
    return $results;
}

function boxdev_get_key(){
    $boxdev = new WC_Boxdev_Shipping_Method();
    return $boxdev->getKey();
}

function boxdev_get_url(){
    $boxdev = new WC_Boxdev_Shipping_Method();
    return $boxdev->getApiUrl();
}
?>