<?php
/**
 * Класс (наследник класса WC_Shipping_Local_Point( WC_Shipping_Method  ) ) 
 */

/**
 * Подключаем API Boxberry, который находится в корне. 
 * Для того чтобы использовать его методы
 */ 
include_once(WP_CONTENT_DIR . '/../api/Boxberry/src/autoload.php');

/**
 * Подключаем woocommerce 
 * Для того чтобы использовать класс WC_Shipping_Method
 */ 
include_once(WP_CONTENT_DIR .'/plugins/woocommerce/woocommerce.php');

/**
 * Подключаем класс WC_Shipping_Local_Point 
 * Для того чтобы использовать наследовать его методы 
 */
include_once(WP_CONTENT_DIR . '/plugins/woocommerce/includes/shipping/local-point/class-wc-shipping-local-point.php');

if ( ! class_exists( 'WC_Boxdev_Shipping_Method' ) ) {
 
	class WC_Boxdev_Shipping_Method extends WC_Shipping_Local_Point {

        private $boxdev_key;
        private $boxdev_api_url;

        /**
		 * Конструктор класса
		 */
		public function __construct() {
			$this->id = 'boxdev_shipping_method';
            $this->method_title = __( 'BOXBERRY - автоматическое создание доставки', 'woocommerce' );
			$this->title = 'Способ доставки';
			$this->method_description = 'Описание способа доставки';
			$this->enabled = 'yes'; 
			$this->init();
		}

        public function getKey(){
            return $this->boxdev_key;
        }

        public function getApiUrl(){
            return $this->boxdev_api_url;
        }
 
		/**
	 	 * Инциализация настроек
		 */
		public function init() {

            $this->init_form_fields();
			$this->init_settings();
		    // Boxberry
            $this->boxdev_key   = $this->get_option( 'boxdev_key' );           
            $this->boxdev_api_url 		  = $this->get_option( 'boxdev_api_url' );            
		/**
	 	 * Сохраняем настройки
		 */
			add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		}

        /**
         * Поля которые буду выведены для заполнения
         *
         * @return void
         */
        public function init_form_fields() {
			global $woocommerce;
			$this->form_fields = array( 
                             
                /* boxdev */
                'boxdev_key' => array(
                'title' 		=> __( 'Boxberry API Key', 'boxdev' ),
                'type' 			=> 'text',
                'default' 	    => '79067.pjpqddde',
                ),
                'boxdev_api_url' => array(
                'title' 		=> __( 'Boxberry API Url', 'boxdev' ),
                'description' 	=> '',
                'type' 			=> 'text',
                'default'		=> 'https://api.boxberry.ru/json.php',
                )
            );    
        }

        /**
         * Вывод полей для заполнения,в табе BOXBERRY - автоматическое создание доставки
         *
         * @return void
         */
        public function admin_options() {
            global $woocommerce; ?>
            <h3><?php echo $this->method_title; ?></h3>
            <p><?php _e( 'Раздел для заполнения данных', 'woocommerce' ); ?></p>
                <table class="form-table">
                    <?php 
                    /**
                     * Метод класса WC_Shipping_Method 
                     */
                    $this->generate_settings_html(); 
                    ?>
                </table> <?php
                show($_COOKIE);
               
             
            }

        public function calculate_shipping( $package = array() ) {	
               
            }    

        public function boxdev_autocreate(){

            /**
             * Получаем $city_id
             */
            $check = $this->init_checked();
            if ( $check !== false && !in_array( $pointName,$check ) ){
                $point_name = $check[0];
            }    



            $package = $this->package;
            $postcode = $package['destination']['postcode'];
            $country = $package['destination']['country'];
            $city_id = getShippint_cityID($postcode, $point_name, $country);

            /**
             * Получение кода ПВЗ
             */
            
            $client = new Boxberry\Client\Client();
			$client->setApiUrl($this->boxdev_api_url);
			$client->setKey($this->boxdev_key);
					
			$target_point_code = $this->boxberryGetPointCode($client, $city_id);
         

            return $target_point_code;
            
        }    

           
        
		
	}
} 

?>