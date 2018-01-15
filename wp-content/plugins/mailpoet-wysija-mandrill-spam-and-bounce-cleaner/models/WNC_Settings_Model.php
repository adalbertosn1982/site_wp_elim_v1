<?php
/**
 * Created by PhpStorm.
 * User: production
 * Date: 1/30/15
 * Time: 1:42 AM
 */

class WNC_Settings_Model{

    private $opt_names;
    private $smtp_data;

    function __construct() {

    }

    //Get Mandrill API SMTP Settings
    function getSettings() {

        $return_arr = array();

        $wnc_mandrill = new WNC_Mandrill;

        $smtp_data = $wnc_mandrill->get_smtp_settings();

        $smtp_clean = new WNC_Arrays;
        $smtp_data = $smtp_clean->remove_empty_values( $smtp_data );
        $return_arr['smtp_data'] = $smtp_data;

        return $return_arr;
    }

    //Save Settings as WP Options
    function save_settings() {

        $return_arr = array();

        $wnc_mandrill = new WNC_Mandrill;
        $opt_names = $wnc_mandrill->__get('opt_names');

        $expected_fields = $opt_names;

        // Read user posted values
        foreach( $_POST as $key => $val ) {
            if( !empty( $val ) ) {
                switch( $key ) {
                    case 'smtp_host' :
                        $smtp_data['host'] = $val;
                        break;
                    case 'smtp_login' :
                        $smtp_data['username'] = $val;
                        break;
                    case 'smtp_password' :
                        $smtp_data['password'] = $val;
                        break;
                    case 'smtp_port' :
                        $smtp_data['port'] = $val;
                        break;
                }
                // Save the posted value in WP database
                update_option( $opt_names[$key], $val );

                $return_arr['notification'] ='settings saved.';
            }
        }

        $smtp_data = $wnc_mandrill->get_smtp_settings();

        //Clean empty values from array
        $return_arr['smtp_data'] = $smtp_data;

        return $return_arr;
    }

}