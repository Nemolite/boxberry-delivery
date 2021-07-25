<?php
/**
 * Создание пункта Меню
 */
if ( is_admin() ) {
    add_action( 'admin_menu', 'add_boxdev_menu_entry', 100 );
  }
  
  function add_boxdev_menu_entry() {
    
    $page_title = __( 'Boxberry' );
    $menu_title = __( 'Boxberry' );
    $capability = 'manage_woocommerce';
    $menu_slug = 'boxdev_menu';
    $function = 'register_boxdev_menu_admin';
    $icon_url = 'dashicons-tickets-alt';
    $position = 37;

    add_menu_page ( 
        $page_title, 
        $menu_title, 
        $capability, 
        $menu_slug, 
        $function, 
        $icon_url, 
        $position 
    );
  }
  
  function register_boxdev_menu_admin() {
  ?>
    <div class="wrap woocommerce">                  
        <?php boxdev_pull_data();?>
    </div>
  <?php
  }

?>