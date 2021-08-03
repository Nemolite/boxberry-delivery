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

        public $boxdev_from;
        public $boxdev_default_weight;
        public $boxdev_default_height;
        public $boxdev_default_width;
        public $boxdev_default_length;

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
		    

            $this->boxdev_key                 = $this->get_option( 'boxdev_key' );           
            $this->boxdev_api_url 		      = $this->get_option( 'boxdev_api_url' );            
            $this->boxdev_from 		          = $this->get_option( 'boxdev_from' ); 
            $this->boxdev_default_weight 		  = $this->get_option( 'boxdev_default_weight' ); 
            $this->boxdev_default_height 		  = $this->get_option( 'boxdev_default_height' );
            $this->boxdev_default_width 	      = $this->get_option( 'boxdev_default_width' );
            $this->boxdev_default_length 		  = $this->get_option( 'boxdev_default_length' );
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
                ),
                'boxdev_from' => array(
                    'title' 		=> __( 'Пункт приема заказа', 'boxdev' ),
                    'description' 	=> '10.042 -  Москва Ленинградское
                    125212, Москва г, Ленинградское ш, д.58, строение 26, пав. 99',
                    'type' 			=> 'text',
                    'default'		=> '10.042',
                ),
                'boxdev_default_weight' => array(
                    'title' 		=> __( 'Default Weight - Начальный вес (кг)', 'boxdev' ),
                    'description' 	=> 'Для расчета доставки, по умолчанию - 0,1 кг',
                    'type' 			=> 'text',
                    'default'		=> '0,1',
                ),
                'boxdev_default_height' => array(
                    'title' 		=> __( 'Default Height - Начальная высота (см) ', 'boxdev' ),
                    'description' 	=> 'Для расчета доставки, по умолчанию - 1 см',
                    'type' 			=> 'text',
                    'default'		=> '1',
                ),
                'boxdev_default_width' => array(
                    'title' 		=> __( 'Default Width - Начальная ширина (см) ', 'boxdev' ),
                    'description' 	=> 'Для расчета доставки, по умолчанию - 1 см',
                    'type' 			=> 'text',
                    'default'		=> '1',
                ),
                'boxdev_default_length' => array(
                    'title' 		=> __( 'Default Length - Начальная длина (см) ', 'boxdev' ),
                    'description' 	=> 'Для расчета доставки, по умолчанию - 1 см',
                    'type' 			=> 'text',
                    'default'		=> '1',
                ),
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
            <p><?php _e( 'Настройки', 'woocommerce' ); ?></p>
                <table class="form-table">
                    <?php 
                    /**
                     * Метод класса WC_Shipping_Method 
                     */
                    $this->generate_settings_html(); 
                    ?>
                </table> <?php          
        
            }

        
        public function calculate_shipping( $package = array() ) {	
               // Не используется, но нужен

            }                  
		
	}
} 

?>