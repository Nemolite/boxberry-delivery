<?php
/**
 * Модуль получения данных
 * 
 */

function boxdev_pull_data() {  
    $url = 'http://api.boxberry.ru/json.php?token='.BOXBERRY_TOKEN.'&method=OrdersBalance';
    
    $handle = fopen($url, "rb");
    $contents = stream_get_contents($handle);
    fclose($handle);

    $data = json_decode($contents, true);
    if (count($data) <= 0 or $data['err']) {
        
        echo $data['err'];
    }else{
             
       
        /*
        Array(
        [0...n] => Array(
            [ID] => Номер заказа, присвоенный интернет-магазином,
            [Status] => Статус заказа,
            [Price] => Стоимость товаров,
            [Delivery_sum] => Стоимость доставки,
            [Payment_sum] => Сумма к оплате.
        ),
        )
        */
        ?>


<table>
<caption>Информацию по заказам, которые фактически переданы на доставку в Boxberry</caption>
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
        <?php for ( $boxdev_idx = 0; $boxdev_idx <= ($boxdev_repetitions); $boxdev_idx++ ) { ?>
            <tr>
                <td>
                <?php echo esc_html($data[$boxdev_idx]['ID']);?>  
                </td>
                <td>
                    <?php echo esc_html($data[$boxdev_idx]['Status']);?>    
                </td>
                <td>
                    <?php echo esc_html($data[$boxdev_idx]['Price']);?>   
                </td>
                <td>
                    <?php echo esc_html($data[$boxdev_idx]['Delivery_sum']);?>  
                </td>
                <td>
                <?php echo esc_html($data[$boxdev_idx]['Payment_sum']);?> 
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

 ?>