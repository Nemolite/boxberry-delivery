jQuery(document).ready(function($){
  function addPointCookie(name, value) {
		var date = new Date(new Date().getTime() + 3600 * 24 * 30 * 1000);
		setCookie(name, value, {
			expires: date.toUTCString()
		});
	}
  
  function check_is_cis(cis){    
    console.log('cis '+cis);
    if(cis == '0'){  
      $('#post-city-search').removeAttr('required');
      $('#post-code-field').addClass('hidden');
      $('input[name="post_code"], input[name="city_id"], input[name="city_name"]').val('0');
      addPointCookie('country_postcode', $('#billing_country').val());
      setTimeout(function(){
        $('body .checkout-steps .checkout-step.next .step-title').trigger('click');
        $('#shipping_method').removeAttr('style');
      }, 500);
      
    }else{
      $('#post-code-field').removeClass('hidden');
    }
  }
  
  $('#billing_country').on('change', function(){
    var option = $('#cis_country').find('option[value="'+$('#billing_country').val()+'"]');
    var cis = option.data('cis');
    $(this).attr('data-cis', cis);
    $('#post-code-field input').val('').trigger('change');
    $('input[name="billing_postcode"], input[name="billing_city"]').val('').trigger('change');    
    check_is_cis(cis);
  });
  
  $('body').on('click', '.btn.forward-btn', function(){
     if( ($('#boxdev_code_pvz').val()!=='') || ($('#boxdev_adr_pvz').val()!=='') ){
		 $('.checkout-steps .checkout-step.next .step-title').trigger('click'); 
	 	 $('.fix-warinig').hide();		 
	  } else {
		  $('.fix-warinig').show();
		  
	  }
  });
  function init_checkout(){
    var option = $('#cis_country').find('option[value="'+$('#billing_country').val()+'"]');
    var cis = option.data('cis');
    $('#billing_country').attr('data-cis', cis);
    $('#post-code-field input').val('').trigger('change');
    $('input[name="billing_postcode"], input[name="billing_city"]').val('').trigger('change');  
    check_is_cis(cis);
  }
  init_checkout();
});