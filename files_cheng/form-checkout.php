<?php
	/**
		* Checkout Form
		*
		* This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
		*
		* HOWEVER, on occasion WooCommerce will need to update template files and you
		* (the theme developer) will need to copy the new files to your theme to
		* maintain compatibility. We try to do this as little as possible, but it does
		* happen. When this occurs the version of the template file will be bumped and
		* the readme will list any important changes.
		*
		* @see 	    https://docs.woocommerce.com/document/template-structure/
		* @author 		WooThemes
		* @package 	WooCommerce/Templates
		* @version     2.3.0
	*/
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	
	wc_print_notices();
	
	//do_action( 'woocommerce_before_checkout_form', $checkout );
	
	// If checkout registration is disabled and not logged in, the user cannot checkout
	if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
		echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
		return;
	}
	
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
	
	<h1>Оформление заказа</h1>
	
	<div class="checkout-container">
		<div class="checkout-steps">
			
			<div class="checkout-step active" data-step="1">
				<div class="step-title">
					<div class="num">1</div>
					<div class="txt">
						<span>Регион доставки <a href="#">Изменить</a></span>
						<span class="description">Выберите регион доставки</span>
					</div>
				</div>
				<div class="step-content">
          <div class="checkout-country" id="country-field">
            <p class="form-row form-row-wide">
							<label for="country-input">Страна <abbr class="required" title="обязательно">*</abbr></label>
							<div class="woocommerce-inputs">
								<div class="input checkout-select"> 
                  <select id="cis_country" name="country_select" required>
                    <?php $no_sng = array();?>
                    <optgroup class="country-cis">
                      <option value="RU" data-cis="1">Россия</option>
                      <?php if(have_rows('country', 'options')): ?>
                        <?php while(have_rows('country', 'options')): the_row(); ?>
                          <?php if(get_sub_field('is_sng')):?>
                          <option value="<?php the_sub_field('value');?>" data-cis="1"><?php the_sub_field('name');?></option>
                          <?php else: 
                          $no_sng[] = array(
                            'value' => get_sub_field('value'),
                            'name' => get_sub_field('name')
                          );
                          ?>
                          <?php endif;?>
                        <?php endwhile;?>                        
                      <?php endif;?>
                    </optgroup>
                    <?php if($no_sng): ?>
                    <optgroup class="country-other">
                      <?php foreach($no_sng as $item):?>
                      <option value="<?php echo $item['value'];?>" data-cis="0"><?php echo $item['name'];?></option>
                      <?php endforeach;?>
                    </optgroup>
                    <?php endif;?>
                  </select>
									<span class="value"></span>
								</div>
							</div>
						</p>
					</div>
					<div class="checkout-post-code" id="post-code-field">            
						<p class="form-row form-row-wide">
							<label class="for-post-code" for="post-code-input">Индекс <abbr class="required" title="обязательно">*</abbr>
								<span class="post-code-help hover">
									<span>Для получения заказа в нашем магазине или для доставки курьером по городу Москва следует указать индекс 101000. <br>При необходимости отправки заказа достаточно указать индекс главного почтамта Вашего города, в случае, если Вы не можете вспомнить непосредственно Ваш индекс.</span>
								</span>
							</label>
              <label class="for-post-city hidden" for="post-city-input">Город <abbr class="required" title="обязательно">*</abbr>								
							</label>
							<div class="woocommerce-inputs">
								<div id="opcity-field-code" class="input">
									<input type="number" class="input-text" name="post_code_search" placeholder="Введите индекс своего города" id="post-code-search" required>
									<span class="value"></span>
								</div>
                <div id="opcity-field-city" class="input hidden">
									<input type="text" class="input-text" name="post_city_search" placeholder="Введите название своего города" id="post-city-search" >
									<span class="value"></span>
								</div>
								<input type="hidden" name="post_code" readonly>
								<input type="hidden" name="city_id" readonly>
								<input type="hidden" name="city_name" readonly>								
							</div>
							<div class="post-code-tip for-post-code">Например, 101000 — индекс главного почтамта Москвы</div>
              <button id="post-code-button" disabled class="btn next-btn" type="button">Подтвердить</button>
						</p>
					</div>
				</div>
			</div>
			
			<div class="checkout-step next" data-step="2">
				<div class="step-title">
					<div class="num">2</div>
					<div class="txt">
						<span>Доставка <a href="#">Изменить</a></span>
						<span class="description">Выберите способ доставки</span>
					</div>
				</div>
				
				<div class="step-content">
					<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
					<?php wc_cart_totals_shipping_html(); ?>
					<?php endif; ?>
					
					<?php if (have_rows('delivery_points', 1967)) : ?>
					<style>
					.fix-warinig {
						width:100%;
						height: 20px;
						display:none;
					}

					.fix-warinig p {
						text-align: center;
						color: red;
						font-size: 16px;
						margin-top: 10px;
					}
					</style>
					<div class="fix-warinig">
					<p>Выберите адрес пункта выдачи</p>
					</div>
					<div class="local-point-map" style="display:none">
						<div class="form-row form-row-wide">
							<label for="local_point_address">Адрес пункта выдачи</label>
							<div class="input">
								<input type="text" class="input-text" name="local_point_address" id="local_point_address" placeholder="выберите пункт или начните вводить адрес">
								<div class="point-address-clear">×</div>
							</div>
							<input type="hidden" 
							  name="boxdev_code_pvz" 
							  id="boxdev_code_pvz" 
							  value=""							  
							>
							<input type="hidden" 
							  name="boxdev_adr_pvz" 
							  id="boxdev_adr_pvz" 
							  value=""							  
							>
							<div class="clear"></div>
							<div class="local-point-error woocommerce-error" style="display:none; text-align:left; margin-top:20px">Поле адреса пункта выдачи должно быть заполнено.</div>
						</div>
						
						<div class="dt-map">
							<div id="dt-company-tabs" class="dt-company-tabs">
								
								<?php while (have_rows('delivery_points', 1967)) : the_row(); ?>
								<div class="dt-tab" data-point="<?php the_sub_field('point_id'); ?>">
									<div class="dt-logo">
										<img src="<?php the_sub_field('point_img'); ?>" alt="<?php the_sub_field('point_name'); ?>">
									</div>
									<div class="dt-text">
										<span><?php the_sub_field('point_name'); ?></span>
										<span class="dt-detail"><p>Нажмите чтобы рассчитать</p></span>
									</div>
								</div>
								<?php endwhile; ?>								
								
							</div>
							
							<div id="company-map"></div>	
						</div>
						
					</div>
					<?php endif; ?>		
					<button class="btn forward-btn" type="button">Подтвердить</button>
				</div>
			</div>
			
			<div class="checkout-step" data-step="3">
				<div class="step-title">
					<div class="num">3	</div>
					<div class="txt">
						<span>Оплата <a href="#">Изменить</a></span>
						<span class="description">Выберите способ оплаты</span>
					</div>
				</div>
				<div class="step-content">
					<?php wc_get_template( 'checkout/payment.php', array( 'checkout' => WC()->checkout() ) ); ?>
          <button class="btn forward-btn" type="button">Подтвердить</button>
				</div>
			</div>
			
			<div class="checkout-step" data-step="4">
				<div class="step-title">
					<div class="num">4</div>
					<div class="txt">
						<span>Контактные данные</span>
						<span class="description">Укажите свои контактные данные, чтобы мы знали кому доставить заказ</span>
					</div>
				</div>
				
				<div class="step-content">
					<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>
					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
					
					<div id="customer_details">
						
						<?php do_action( 'woocommerce_checkout_billing' ); ?>
						<?php do_action( 'woocommerce_checkout_shipping' ); ?>
						
						<?php 
							$hours = array(7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21);
							
							$delivery_select_option = '';
							foreach ($hours as $hour) {
								if ($hour < 10) {
									$hour = '0' . $hour;
								}
								$delivery_select_option .= '<option value="' . $hour . ':00">' . $hour . ':00</option>';
							} 
						?>
						
						<div id="billing_delivery_fields" style="display:none">
							<p class="form-row form-row-first" id="billing_delivery_from_field">
								<label for="billing_delivery_from">Время ожидания с</label>
								<select name="billing_delivery_from" id="billing_delivery_from" data-field="from" class="select">
									<?php echo $delivery_select_option; ?>
								</select>
							</p>
							
							<p class="form-row form-row-last row-clear" id="billing_delivery_to_field">
								<label for="billing_delivery_to">до</label>
								<select name="billing_delivery_to" id="billing_delivery_to" data-field="to" class="select">
									<?php echo $delivery_select_option; ?>
								</select>
							</p>
						</div>
						
					</div>
					<div class="clear"></div>
					
					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
					<?php endif; ?>
				</div>
        
			</div>
			
		</div>
		
		<div class="form-row place-order submit-order-block">
			
			<div class="submit-order-text">Нажимая на кнопку “Оформить заказ”, Вы даете согласие на обработку своих персональных данных.</div>
			
			<noscript>
				<?php _e( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the <em>Update Totals</em> button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ); ?>
				<br/><input type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>" />
			</noscript>
			
			<?php wc_get_template( 'checkout/terms.php' ); ?>
			
			<?php do_action( 'woocommerce_review_order_before_submit' ); ?>
			
			<?php $order_button_text = "Оформить заказ"; ?>
			
			<?php echo apply_filters( 'woocommerce_order_button_html', '<button disabled type="submit" onclick="yaCounter25099067.reachGoal(\'knppodtvzak\');" class="submit-order-button" name="woocommerce_checkout_place_order" id="place_order" data-value="' . esc_attr( $order_button_text ) . '" >' . esc_attr( $order_button_text ) . '</button>' ); ?>
			
			<?php do_action( 'woocommerce_review_order_after_submit' ); ?>
			
			<?php wp_nonce_field( 'woocommerce-process_checkout' ); ?>
		</div>
		
	</div>
	
	<div class="checkout-cart" id="checkout-fixed-cart">
		<div class="checkout-cart-content">
			<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
			
			<div id="order_review" class="woocommerce-checkout-review-order">
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			</div>
			
			<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
		</div>	
	</div>	
	
</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
