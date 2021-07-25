( function( $ ) {
/**
 * Передача данных для создание Отправления
 * 
 */
 $(`#boxdev-send111`).on('click', function() { 
     let boxdev_order_id = $(`#boxdev_order_id`).attr("data-order_id");                 
    jQuery.ajax({
            url: myajax.ajaxurl,
            type: 'POST',
            data: {
                action: 'boxdev_order_id111',
                boxdev_order_id: boxdev_order_id                                 
            },
            success:function( request ){               
               alert( request );

            },
            error:function( err ){               
               alert ( err );  
               console.log(err);

            }  
        });

});
  
} )( jQuery );


