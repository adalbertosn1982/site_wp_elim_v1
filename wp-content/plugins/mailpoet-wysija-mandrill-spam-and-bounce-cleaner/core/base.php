<?php
/**
 * Created by PhpStorm.
 * User: production
 * Date: 1/19/15
 * Time: 2:58 AM
 */
require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'constants.php' );
require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'autoloader.php' );
require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'loader.php' );

    function on_activation()
    {
        if ( ! current_user_can( 'activate_plugins' ) )
            return;
        /*$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        check_admin_referer( "activate-plugin_{$plugin}" );*/

        //Create table
       $this->create_table();
    }

    function on_deactivation()
    {
        if ( ! current_user_can( 'activate_plugins' ) )
            return;
        $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        check_admin_referer( "deactivate-plugin_{$plugin}" );
    }

    function on_uninstall()
    {
        if ( ! current_user_can( 'activate_plugins' ) )
            return;

        // Important: Check if the file is the one
        // that was registered during the uninstall hook.
        if ( __FILE__ != WP_UNINSTALL_PLUGIN )
            return;

        // Delete table

    }

    //Create table upon plugin activation
    function create_table () {
        global $wpdb;
        global $wnc_db_version;

        $table_name = $wpdb->prefix . "wnc_mandrill_search";

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
      wnc_search_id mediumint(9) NOT NULL AUTO_INCREMENT,
      wnc_start_date date DEFAULT '0000-00-00 00:00:00' NOT NULL,
      wnc_end_Date date DEFAULT '0000-00-00 00:00:00' NOT NULL,
      wnc_api_username VARCHAR(100) NOT NULL,
      wnc_api_type date DEFAULT '0000-00-00 00:00:00' NOT NULL,
      UNIQUE KEY id (wnc_search_id)
    ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        add_option( "wnc_db_version", $wnc_db_version );
    }

    function update() {
        global $jal_db_version;
        if ( get_site_option( 'jal_db_version' ) != $jal_db_version ) {
            //jal_install();
        }
    }

    //Load CSS + JS
    function wnc_load_custom_wp_admin_style() {
        wp_register_style( 'custom_wp_admin_css', '/wp-content/plugins/' . WNC_PLG_FOLDER_NAME . '/css/style.css',false);
        wp_enqueue_style( 'custom_wp_admin_css' );

        wp_enqueue_script( 'ui-widget' , '/wp-content/plugins/' . WNC_PLG_FOLDER_NAME .'/js/jquery.ui.widget.js', false, null );
        wp_enqueue_script( 'ui-datepicker' , '/wp-content/plugins/' . WNC_PLG_FOLDER_NAME .'/js/jquery.ui.datepicker.js', false, null );

        wp_enqueue_script('jquery-ui-core');

        wp_register_style( 'jquery-custom-ui', '/wp-content/plugins/' . WNC_PLG_FOLDER_NAME . '/css/jquery-ui.css',false);
        wp_enqueue_style( 'jquery-custom-ui' );

        //DataTable
        wp_register_style( 'custom_css_datatable', '/wp-content/plugins/' . WNC_PLG_FOLDER_NAME . '/css/jquery.dataTables.min.css',false);
        wp_enqueue_style( 'custom_css_datatable' );

        wp_enqueue_script ( 'custom_js_datatable' , '/wp-content/plugins/' . WNC_PLG_FOLDER_NAME . '/js/jquery.dataTables.js', false, null);
    }
    add_action( 'admin_enqueue_scripts', 'wnc_load_custom_wp_admin_style' );

    //Load AJAJ
    function wnc_load_ajax() {

        wp_enqueue_script( 'ajax-script',  '/wp-content/plugins/' . WNC_PLG_FOLDER_NAME .'/js/wnc_ajaj.js', array('jquery') );

        wp_localize_script( 'ajax-script', 'ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => '' ) );
    }
    add_action( 'admin_footer', 'wnc_load_ajax' ); // Write our JS below here

    // AJAJ callback function
   function wnc_ajax_callback() {
        $api_key = $_POST['api_key'];
        //echo $api_key;

        $mandrill = new WNC_Mandrill;

        $response = $mandrill->testConnection($api_key);

        if(strchr(strtolower($response),'pong')){
            $response = "Connection settings work! Good to go!";
        }
        echo $response;
        wp_die();
    }
    add_action( 'wp_ajax_my_action', 'wnc_ajax_callback' );

    // AJAJ Search callback function
    function wnc_ajax_search_callback() {
        wnc_initialize_cleaner();
        wp_die();
    }
    add_action( 'wp_ajax_search_action', 'wnc_ajax_search_callback' );

    // AJAJ Mail Poet update function
    function wnc_ajax_update_callback() {
        wnc_initialize_cleaner();
        wp_die();
    }
    add_action( 'wp_ajax_update_action', 'wnc_ajax_update_callback' );

// sub menu for Mail Poet
    function wnc_initial() {
        if ( class_exists('WYSIJA') ) {
            add_action( 'admin_menu', 'wnc_register_my_custom_menu_page' , 1000 );
        }
    }

    function wnc_register_my_custom_menu_page()
    {
        add_submenu_page( 'wysija_campaigns', 'Mandrill Cleaner', 'Mandrill Cleaner', 'wysija_config', '/wysija-cleaner', 'wnc_initialize_cleaner' );
    }

    //Menu Item "Mandrill Cleaner" handler
    function wnc_initialize_cleaner()
    {
        $loader = new Loader($_POST);
        $controller =  $loader->CreateController();
        $controller->ExecuteAction();
    }

    register_activation_hook(   __FILE__, 'create_table' );
    register_deactivation_hook( __FILE__, 'on_deactivation' );
    register_uninstall_hook(    __FILE__, 'on_uninstall' );

    add_action( 'plugins_loaded', 'wnc_initial' );

    function register_session(){
        if( !session_id() )
            session_start();
    }
    add_action('init','register_session');