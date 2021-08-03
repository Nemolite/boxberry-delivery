var myMap;
var zoom = 10;
var delay = 700;
var defaultCoord = ['55.580986', '37.64418'];
var cookieDate = new Date(new Date().getTime() + 3600 * 24 * 30 * 1000);
var apiURL = '/api/points/PointsList.php';
var loading = '<img style="width:20px" src="/img/loading.svg" alt="" />';
var localPickpointMarkers = [];

jQuery(document).ready(function() {
	var $ = jQuery;
	
	window.executeYMap = function () {
        executeYMap();
	};
	
	window.delivery_points_execute = function (cityId, map, point, zip) {
        delivery_points_execute(cityId, map, point, zip);
	};
	
	// Points tab
	$("body").on("click", ".dt-company-tabs .dt-tab", function() {
		$(".dt-company-tabs .dt-tab").removeClass('active');
		
		startLoading();
		
		if (!$(this).hasClass('active')) {
			$(this).addClass('active');
		}
		
		var point = $(this).data('point');
		
		setCookie('local_point_title', point, {
			expires: cookieDate.toUTCString()
		});	
		
		$('body').trigger('update_checkout');
	});
	
	function executeYMap() {
		if (isLocalPointMethod()) {
			if (getCookie('city_shipping_id')) {
				var cityIds = getCookie('city_shipping_id').replace(/\s/g, '').split(',');
				var zip = getCookie('city_postcode');
				var map = getCookie('city_map');
				var point = getCookie('local_point_title');
				
				if (point) {
					// Set active point tab
					$('.dt-company-tabs .dt-tab').removeClass('active');
					$('.dt-company-tabs .dt-tab[data-point="' + point + '"]').addClass('active');
					delivery_points_execute(cityIds, map, point, zip);
				}
			}
		}
	}
	
	function showPointTab(pointName) {
		$('.dt-company-tabs .dt-tab[data-point="' + pointName + '"]').show();
	}
	
	function removePointTab(pointName) {
		$('.dt-company-tabs .dt-tab[data-point="' + pointName + '"]').removeClass('active').hide();
	}
	
	// Select point
	$("body").on("click", "#company-map .point-button", function() {
		
		var value = $(this).parent().find('.point-value').text();
		
		var codpvz = $(this).parent().find('.point-number').text();	
		
		// Save field and close
		$('#local_point_address, #billing_new_fild21').val(value);

		$('#boxdev_code_pvz').val(codpvz);	
		
		$('#local_point_address').prop('readonly', true);
		$('.local-point-map .point-address-clear').addClass('active');
		
		myMap.balloon.close();
	});
	
	// Local point address autocomplete reset
	$("body").on("click", ".local-point-map .point-address-clear", function() {
		$('#local_point_address').prop('readonly', false);
		$('#local_point_address, #billing_new_fild21').val('');	

		$('#boxdev_code_pvz').val('');			
		
		$('.local-point-map .point-address-clear').removeClass('active');
	});
	
	// Local point address autocomplete
	localPointSearch();
	
	// Local point address autocomplete
	function localPointSearch() {
		var searchRequest;
		
		$("#local_point_address").autoComplete({
			minChars: 3,
			cache: false,
			source: function(term, suggest){
				term = term.toLowerCase();
				var choices = localPickpointMarkers;
				
				var suggestions = [];
				for (i = 0; i < choices.length; i++)
				if (~(choices[i]+' '+choices[i]).toLowerCase().indexOf(term)) {
					suggestions.push(choices[i]);
					suggest(suggestions);
				}
			},
			renderItem: function (item, search){
				var value = item.split('|');
				return '<div class="autocomplete-suggestion" data-adrpvz="' + value[1] + '" data-coord="' + value[2] + '" data-point="' + value[0] + '">' + value[1] + '</div>';
			},
			onSelect: function(e, term, item){
				var coord = item.data('coord');
				var point = item.data('point');
				var adrpvz = item.data('adrpvz');
				
				var address = item.text();
				var value = point + " - " + address;
				$('#local_point_address, #billing_new_fild21').val(value);
				
				$('#boxdev_adr_pvz').val(adrpvz);
				
				$('#local_point_address').prop('readonly', true);
				$('.local-point-map .point-address-clear').addClass('active');
				
				if (coord) {
					var map = coord.split(',');
					var zoom = 18;
					myMap.setCenter([map[0], map[1]], zoom);
				}
			}
		});
	}
	
	// Load map
	function loadMap(cityId, map, point) {
		if (typeof ymaps !== 'undefined') {
			ymaps.ready(function () {
				myMap = new ymaps.Map("company-map", {
					center: [map[0], map[1]],
					zoom: zoom,
					controls: []
					}, {
					searchControlProvider: 'yandex#search'
				});
				
				loadPoints(myMap.geoObjects, cityId, point);
			});
		}
		else{
			$('.dt-map').html('<div class="woocommerce-error">Яндекс карта не загрузилась. Попробуйте перезагрузить страницу.</div>');
		}
	}
	
	// Loading mark of points
	function delivery_points_execute(cityId, map, point) {
		map = map.split(',');
		
		if ($.isArray(map)){
			if (myMap != null) {	
				// Reset map and add new points
				resetPoints(myMap.geoObjects);
				
				setTimeout(function(){
					myMap.setCenter([map[0], map[1]], zoom);
					loadPoints(myMap.geoObjects, cityId, point);
				}, 700);
        
			}
			else{
				loadMap(cityId, map, point);
			}
		}
		else{
			if (myMap != null) {
				myMap.setCenter(defaultCoord, zoom);
			}
			else{
				loadMap(2, defaultCoord, false, false);
			}
			
			stopLoading();
		}
	}
	
	function isLocalPointMethod() {
		return $('#shipping_method #shipping_method_0_local_point').is(':checked') && $('.checkout-steps .checkout-step[data-step="2"]').hasClass('active');
	}
	
	// Reset points
	function resetPoints(object) {
		if (myMap != null) {
			object.removeAll();
		}
	}
	
	function getDefaultPoint() {
		return $(".dt-company-tabs .dt-tab:visible:first").data('point');
	}
	
	function startLoading() {
		$('#company-map').append('<div class="map-loader"></div>');
	}
	
	function stopLoading() {
		$('#company-map').find('.map-loader').remove(); 
	}
	
	// Load point
	function loadPoints(object, city_id, point) {
		if ($.isArray(city_id)) {
			
			var method = 'points';
			var city_id;
			var color;
			
			if (point == 'cdek') {
				city_id = city_id[0];
				color = 'islands#greenDotIconWithCaption';
			}
			else if (point == 'pickpoint') {
				city_id = city_id[2];
				color = 'islands#orangeDotIconWithCaption';
			}
			else if (point == 'boxberry') {
				city_id = city_id[1];
				color = 'islands#redDotIconWithCaption';
			}
			else if (point == 'dellin') {
				city_id = city_id[5];
				color = 'islands#orangeDotIconWithCaption';
			}
			
			$.ajax({
				type: "POST",
				url: apiURL,
				data: {delivery: point, method: method, city_id: city_id},
				dataType: "json",
				success: function(data) {
					if (data['points_list'] && data['points_list'].length > 0) {
						var points = data['points_list'];
						localPickpointMarkers = [];	
						
						for (i = 0; i < points.length; i++) {
							var map = points[i]['map'];
							var coord = map.split(',');
							var address = points[i]['address'];
							var name = points[i]['name'];
							var time = points[i]['time'];
							var number = points[i]['number'];
							object.add(new ymaps.Placemark([coord[0], coord[1]], {
								balloonContent: mapContent(point, name, time, address, number),
								iconCaption: ''
								}, {
								preset: color
							}));
							
							localPickpointMarkers.push(point.toUpperCase() + '|' + address + "|" + coord);
						}
					}
					else{
						//removePointTab(point);
						console.log(data['error']);
					}
				},
				error: function (request, status, error) {
					alert(error);
				}
			});
		}
		
		stopLoading();
	}
	
	// Map content
	function mapContent(delivery, name, time, address, number) {
		var deliveryValue = delivery.toUpperCase();
		var addressValue = address ? address : '';
		var numberValue = number ? ' (' + number + ')' : '';
		var cityName = ' - ' + $('#post-code-field input[name="city_name"]').val() + ', ';
		
		var html = '<div class="point-info">';
		html += '<div class="point-name">Самовывоз в пункте выдачи заказов ' + name + '</div>';
		html += '<div class="point-value" style="display:none">' + deliveryValue + " " +  cityName + " " + addressValue + '</div>';
		html += '<div class="point-number" style="display:none">' + number + '</div>';
		if (time) html += '<div class="point-time"><span>часы работы:</span> <span>' + time + '</span></div>';
		if (address) html += '<div class="point-address"><span>адрес:</span> <span>' + address + '</span></div>';
		html += '<div class="point-button button">Выбрать</div>';
		html += '</div>';
		return html;
	}
	function hidden_dt_tab(trigger_cart){
    if(trigger_cart==true){
      setTimeout(function(){
          $('.dt-company-tabs .dt-tab').each(function(){
            if(!$(this).hasClass('active')){
              $(this).find('.dt-text .dt-detail').html('<p>Нажмите чтобы рассчитать</p>');
              Cookies.remove('local_point_data');
            }        
          });
       }, 3000);
    }
  }
  
	$('.woocommerce').on('click', '.woocommerce-cart-form .cart-table-submit input[name="update_cart"]', function(){
    var trigger_cart = true;
    $('body').on('updated_checkout', function(){
      hidden_dt_tab(trigger_cart);
      trigger_cart = false;
    });    
  });
    
});			