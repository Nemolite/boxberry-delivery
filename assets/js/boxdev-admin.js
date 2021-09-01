( function( $ ) {
/**
 * Передача данных для создание Отправления
 * 
 */
 $(`#boxdev-send`).on('click', function() { 
    
     let boxdev_order_id = $(`#boxdev_order_id`).attr("data-order_id");                 
    jQuery.ajax({
            url: myajax.ajaxurl,
            type: 'POST',                               
            data: {
                action: 'boxdev_order_id',
                boxdev_order_id: boxdev_order_id                                 
            },
            success:function( request ){                                   
             if (request!==''){
              $(`#boxdev-messege`).text('Отправление на BoxBerry создано. Обновите для сохраниения номера акта и получения ссылки ');
              $(`#shipping_post_rusia_id`).val(request);
              $(`#boxdev-send`).attr("disabled", "true");  
             } else {
              $(`#boxdev-messege`).text('Отправление на BoxBerry  не создано. Что-то пошло не так '); 
             }
                
               
            },
            error:function( err ){                             
               console.log(err);
            }  
        });

});

/**
 * Блокировка кнопки, после отправлени  shipping_post_rusia_id
 * 
 */
 if( $(`#shipping_post_rusia_id`).val() ) {
    $(`#boxdev-send`).attr("disabled", "true"); 
 }

  
} )( jQuery );


