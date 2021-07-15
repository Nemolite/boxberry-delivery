<?php
/**
 * Модуль отправления данных
 * 
 */

 function boxdev_push_data_test() {
     /**
     * Подготовка данных к отправке
     */
    $SDATA=array();
    $SDATA['updateByTrack']='Трекинг-код ранее созданной посылки';
    $SDATA['order_id']='ID заказа в ИМ';
    $SDATA['PalletNumber']='Номер палеты';
    $SDATA['barcode']='Штрих-код заказа';
    $SDATA['price']='Объявленная стоимость';
    $SDATA['payment_sum']='Сумма к оплате';
    $SDATA['delivery_sum']='Стоимость доставки';
    $SDATA['vid']='Тип доставки (1/2)';
    $SDATA['shop']=array(
        'name'=>'Код ПВЗ',
        'name1'=>'Код пункта поступления'
    );
    $SDATA['customer']=array(
        'fio'=>'ФИО получателя',
        'phone'=>'Номер телефона',
        'phone2'=>'Доп. номер телефона',
        'email'=>'E-mail для оповещений',
        'name'=>'Наименование организации',
        'address'=>'Адрес',
        'inn'=>'ИНН',
        'kpp'=>'КПП',
        'r_s'=>'Расчетный счет',
        'bank'=>'Наименование банка',
        'kor_s'=>'Кор. счет',
        'bik'=>'БИК'
    );
    $SDATA['kurdost'] = array(
        'index' => 'Индекс',
        'citi' => 'Город',
        'addressp' => 'Адрес получателя',
        'timesfrom1' => 'Время доставки, от',
        'timesto1' => 'Время доставки, до',
        'timesfrom2' => 'Альтернативное время, от',
        'timesto2' => 'Альтернативное время, до',
        'timep' => 'Время доставки текстовый формат',
        'delivery_date' => "Дата доставки от +1 день до +5 дней от текущий даты (только для доставки по Москве, МО и Санкт-Петербургу)",
        'comentk' => 'Комментарий'
    );
    
    $SDATA['items']=array(
        array(
            'id'=>'ID товара в БД ИМ',
            'name'=>'Наименование товара',
            'UnitName'=>'Единица измерения',
            'nds'=>'Процент НДС',
            'price'=>'Цена товара',
            'quantity'=>'Количество'
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
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://api.boxberry.ru/json.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, array(
    'token'=>'XXXXXXXXXX',
    'method'=>'ParselCreate',
    'sdata'=>json_encode($SDATA)
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$data = json_decode(curl_exec($ch),1);
if($data['err'] or count($data)<=0)
{
    // если произошла ошибка и ответ не был получен.
    echo $data['err'];
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
}
   
 }

function boxdev_push_data() {
    echo "token";
}

 ?>