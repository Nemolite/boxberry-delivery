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
    if (count($data) <= 0 ) {
        
        echo "Данные по отправлениям отсутствуют";
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

function boxdev_pull_info_test(){
    
    $url='http://api.boxberry.ru/json.php?token='.BOXBERRY_TOKEN.'&method=ListPoints';
    $handle = fopen($url, "rb");
    $contents = stream_get_contents($handle);
    fclose($handle);
    $data=json_decode($contents,true);
    if(count($data)<=0 )
        {
        // если произошла ошибка и ответ не был получен:
        
        }
    else
    {
      // все отлично, ответ получен, теперь в массиве $data,
      // список всех ПВЗ в следующем формате:
      /*
      $data[0...n]=array(
           'Code'=>'Код в базе boxberry',
           'Name'=>'Наименование ПВЗ',
           'Address'=>'Полный адрес',
           'Phone'=>'Телефон или телефоны',
           'WorkShedule'=>'График работы',
           'TripDescription'=>'Описание проезда',
           'DeliveryPeriod'=>'Срок доставки' (срок доставки из Москвы, дней),
           'CityCode'=>'Код города в boxberry',
           'CityName'=>'Наименование города',
           'TariffZone'=>'Тарифная зона' (город отправления - Москва),
           'Settlement'=>'Населенный пункт',
           'Area'=>'Регион',
           'Country'=>'Страна',
           'GPS'=>'Координаты gps',
           'OnlyPrepaidOrders'=>'Если значение "Yes" - точка работает только с полностью оплаченными заказами',
           'Acquiring'=>'Если значение "Yes" - Есть возможность оплаты  платежными (банковскими) картами',
           'DigitalSignature'=>'Если значение "Yes" - Подпись получателя будет хранится в системе boxberry в электронном виде',
           'AddressReduce' => 'Короткий адрес',
           'TypeOfOffice' => 'Тип пункта выдачи: 1-ПВЗ, 2-СПВЗ',
           'NalKD' => 'Осуществляет курьерскую доставку',
           'CountryCode' => 'Код страны в Boxberry',
           'Metro' => 'Станция метро',
           'VolumeLimit' => 'Ограничение объема',
           'LoadLimit' => 'Ограничение веса, кг',
      );

      например:
      echo $data[0]['Name'];
      echo $data[5]['Code'];
      */
        foreach ($data as $val){
            if ('603140, Нижний Новгород г, Ленина пр-кт, д.31'==$val['Address']){
                echo $val['Address'];
                echo " ";
                echo $val['Code'];
                echo " ";
                echo $val['Name'];
                echo "<br>";
            }       
        }
    }
}


function boxdev_get_code_on_address( $address ){
    
    $url='http://api.boxberry.ru/json.php?token='.BOXBERRY_TOKEN.'&method=ListPoints';
    $handle = fopen($url, "rb");
    $contents = stream_get_contents($handle);
    fclose($handle);
    $data=json_decode($contents,true);
    if(count($data)<=0 )
        {
        // если произошла ошибка и ответ не был получен:
        
        }
    else
    {
      // все отлично, ответ получен, теперь в массиве $data,
      // список всех ПВЗ в следующем формате:
      /*
      $data[0...n]=array(
           'Code'=>'Код в базе boxberry',
           'Name'=>'Наименование ПВЗ',
           'Address'=>'Полный адрес',
           'Phone'=>'Телефон или телефоны',
           'WorkShedule'=>'График работы',
           'TripDescription'=>'Описание проезда',
           'DeliveryPeriod'=>'Срок доставки' (срок доставки из Москвы, дней),
           'CityCode'=>'Код города в boxberry',
           'CityName'=>'Наименование города',
           'TariffZone'=>'Тарифная зона' (город отправления - Москва),
           'Settlement'=>'Населенный пункт',
           'Area'=>'Регион',
           'Country'=>'Страна',
           'GPS'=>'Координаты gps',
           'OnlyPrepaidOrders'=>'Если значение "Yes" - точка работает только с полностью оплаченными заказами',
           'Acquiring'=>'Если значение "Yes" - Есть возможность оплаты  платежными (банковскими) картами',
           'DigitalSignature'=>'Если значение "Yes" - Подпись получателя будет хранится в системе boxberry в электронном виде',
           'AddressReduce' => 'Короткий адрес',
           'TypeOfOffice' => 'Тип пункта выдачи: 1-ПВЗ, 2-СПВЗ',
           'NalKD' => 'Осуществляет курьерскую доставку',
           'CountryCode' => 'Код страны в Boxberry',
           'Metro' => 'Станция метро',
           'VolumeLimit' => 'Ограничение объема',
           'LoadLimit' => 'Ограничение веса, кг',
      );

      например:
      echo $data[0]['Name'];
      echo $data[5]['Code'];
      */
        foreach ($data as $val){
            if ($address==$val['Address']){
               
                return $val['Code'];

            }       
        }
    }
}
?>