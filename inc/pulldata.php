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

/** Модуль получения актов */

function boxdev_pull_data_akt() {
?>
<p>Выберите период для выборки актов</p>
    <form action="" method="post">
        <div class="form-field">
            <label for="date_from"><?php esc_html_e( 'От', 'boxdev' ); ?></label>
            <input name="date_from" id="date_from" type="date" value="" />                                
        </div>
        <div class="form-field">
            <label for="date_to"><?php esc_html_e( 'До', 'boxdev' ); ?></label>
            <input name="date_to" id="date_to" type="date" value="" />                                
        </div>
        <button><?php esc_html_e( 'Выбрать', 'boxdev' ); ?></button>

    </form>
<?php   

    if (isset($_POST['date_from'])){        
        $date_from = new DateTime(sanitize_text_field($_POST['date_from']));
        $date_from_new =  $date_from->format('Ymd');
    }  

    if (isset($_POST['date_to'])){        
        $date_to = new DateTime(sanitize_text_field($_POST['date_to']));
        $date_to_new = $date_to->format('Ymd');
    }    

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
   
    $url='http://api.boxberry.ru/json.php?token='.BOXBERRY_TOKEN.'&method=ParselSendStory&from='. $date_from_new .'&to='. $date_to_new;
    $handle = fopen($url, "rb");
    $contents = stream_get_contents($handle);
    fclose($handle);
    $data=json_decode($contents,true);
    if(count($data)<=0) {
        // если произошла ошибка и ответ не был получен:
        echo "Акты за данный период отсутствуют";
    }else{
       
    
        // все отлично, ответ получен, теперь в массиве $data:
        /*
        $data[0..n]=array(
            'track'=>'XXXXXX,XXXXXX,XXXXXX', // список трекинг кодов посылок в акте,
            'label'=>'http://', // ссылка на скачивание акта, если доступна,
            'date'=>'2015.11.25' // дата создания посылки в формате YYYY.MM.DD HH:MM:SS.
        );
        */
?>
        <table>
<caption>
    <h3>Информация по заказам, Акты по доставкам в Boxberry </h3>   
    
</caption>

  <tr>
    <th>Норма актов</th>
    <th>Дата</th>
    <th>Акты для скачивания</th>
    
  </tr>
  
    <?php 
      
        $boxdev_repetitions = ( $data ) ? count( $data ) : 0;
            if (0!==$boxdev_repetitions) {
    ?>
        <?php foreach ($data as $data_akt_show)  { ?>
            <tr>
                <td>
                <?php 
                $str_track = esc_html($data_akt_show['track']);
                $arr_track = explode(",", $str_track);
                foreach($arr_track as $val){
                    echo $val;
                    echo "<br>";
                }
                ?>  
                </td>
                <td>
                    <?php echo esc_html($data_akt_show['date']);?>    
                </td>
                <td>
                <a href="<?php echo esc_html($data_akt_show['label']);?>">
	                <button>Скачать файл</button>
                </a>
                  
                </td>
               
            </tr>
        <?php } ?>    
    
 
        <?php
       
        } else {
        echo "Акты  по доставкам за указанный период не имеются";
        }
        ?>
  
</table>
<?php
    }  

}

 ?>