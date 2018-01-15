<?php
/**
 * Created by PhpStorm.
 * User: production
 * Date: 1/19/15
 * Time: 12:22 AM
 * Gather Plugin data for manipulation
 */

require_once(WNC_CLASSES . 'Mandrill/Mandrill.php');

class WNC_Mandrill {

    protected $limit = '20';

    protected $smtp_settings = array (
        'username' => '',
        'password' => '',
        'host' => '',
        'port' => ''
    );

    protected  $opt_names = array(
        'username' => 'wnc_mandrill_username',
        'password' => 'wnc_mandrill_password',
        'host' => 'wnc_mandrill_host',
        'port' => 'wnc_mandrill_port');

    function searchErrors( $status = 'spam', $start_date, $end_date, $tags, $senders, $api_key ) {

        $mandrill = new Mandrill( $api_key );

        $api_keys = array();
        array_push( $api_keys , $api_key );

        try {

            $query = 'state: ' . strtolower($status) ;

            $result = $mandrill->messages->search( $query, $start_date, $end_date, $tags, $senders, $api_keys, $this->limit );

        } catch( Mandrill_Error $e ) {
            return $e;
        }

        return $result;
    }


    function testConnection( $api_key ) {
        try {
            $mandrill = new Mandrill( $api_key );
            $result = $mandrill->users->ping();
            return $result;
            /*
            PONG!*/
        } catch(Mandrill_Error $e) {
            // Mandrill errors are thrown as exceptions
            echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
            // A mandrill error occurred: Mandrill_Invalid_Key - Invalid API key
            //throw $e;
        }

    }

    function get_smtp_settings() {
        $this->smtp_settings['username']  = get_option( $this->opt_names['username'] );
        $this->smtp_settings['password']  = get_option( $this->opt_names['password'] );
        $this->smtp_settings['host']     = get_option( $this->opt_names['host'] );
        $this->smtp_settings['port']     = get_option( $this->opt_names['port'] );

        return $this->smtp_settings;
    }

    function getApiKey() {
        return get_option( $this->opt_names['password'] );
    }

    function __get( $property ) {
        if(property_exists($this,$property)) {
            return $this->$property;
        }
    }

    function __set( $property, $value ){
        if(property_exists($this, $property)){
            $this->$property = $value;
        }

        return $this;
    }

    function removeOptions() {
        foreach( $this->opt_names as $key => $value ) {
            delete_option( $value );
        }
    }

    /*-** Helper **-*/

    /**
     * Formats string for mandrill api
     * @return string date Y-m-d format
     */

    public function mandrill_format_date( $date, $delimiter, $format = "Y-m-d" ) {

        $newdate = explode( $delimiter, $date );

        $strtodate = $newdate[2] .'-'. $newdate[0] .'-'. $newdate[1];

        $newdate = date( $format, strtotime( $strtodate ) );

        return $newdate;

    }

}