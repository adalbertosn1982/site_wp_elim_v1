<?php
/**
 * Created by PhpStorm.
 * User: production
 * Date: 2/8/15
 * Time: 2:47 PM
 */

class WNC_Search_Model{

    //Expected form fields
    protected $expected_fields = array( 'error_type', 'start_date', 'end_date' );

    //Expected mandrill error types
    protected $error_types = array( 'spam', 'bounce', 'soft-bounce', 'unsub', 'rejected' );

    //Expected Mandrill API json keys
    protected $mandrill_keys = array( 'ts', 'subject', 'state', 'email', 'sender' );

    /**
     * Queries database for date range and checks for transient data first, if no
     * transient data is found the mandrill API is called directly and stored in transient API for 4 days.
     * @return mixed array|string
     */
    function search() {
        //return value
        $data_table = array();

        //Sanity Check
        $validate = new WNC_Form_Validation();

        //validate expected fields
        $fields_result = $validate->expectedFields( $this->expected_fields, $_POST, true );

        //validate field requirements
        $validation = $validate->check( $fields_result, array(
            'start_date' => array(
                'required' => true,
                'min' => 10,
                'max' => 10),
            'end_date' => array(
                'required' => true,
                'min' => 10,
                'max' => 10),
            'error_type' => array(
                'required' => true,
                'min' => 4,
                'max' => 12)
        ), "POST");


        //validate error_type field against mandrill "status" types
        if( !$validation->validElement( strtolower( $fields_result['error_type'] ) , $this->error_types )) {
            $validation->addError( 'Invalid error type' );
        } else {
            $type = $fields_result['error_type'];
        }

        //Check that mandrill settings work
        $mandrill = new WNC_Mandrill;
        $smtp_data = $mandrill->get_smtp_settings();
        $api_key = $smtp_data['password'];
        $username = $smtp_data['username'];

        //test mandrill api key
        $response = $mandrill->testConnection( $api_key );

        if( !strchr( strtolower( $response ),'pong' ) ){
             // API key does not work
            $validation->addError( 'Invalid API Key. Please input valid key in Mandrill Settings tab.' );
        }

        //validate dates
        $dateTime = new WNC_DateTime();

        if( !$dateTime->valid_date( $fields_result['start_date'], '-' ) | !$dateTime->valid_date( $fields_result['end_date'], '-' ) ) {
            $validation->addError( 'Please enter valid date range.' );
        }

        if ( !empty( $fields_result ) && !$validate->isErrors() ) {

            $wnc_mandrill  = new WNC_Mandrill;
            $smtp_data = $wnc_mandrill->get_smtp_settings();

            //Format dates to be YYYY-MM-DD for Mandrill
            $start_date = $wnc_mandrill->mandrill_format_date( $fields_result['start_date'], '-');
            $end_date = $wnc_mandrill->mandrill_format_date( $fields_result['end_date'], '-');

            $api_username = $smtp_data['username'];
            $api_type = $_POST['error_type'];

            //Search for Date Range
            global $wpdb;
            $tablename = $wpdb->prefix . "wnc_mandrill_search";

            $sql = $wpdb->prepare( "SELECT * FROM $tablename
            WHERE wnc_api_username = %s AND wnc_api_type = %s
            AND wnc_start_date <= %s AND wnc_end_date >= %s"
            , $api_username, $api_type, $start_date, $end_date );

            $results = $wpdb->get_results( $sql , ARRAY_A );

            if( !empty( $results ) ) {
                $results_dates  = array();

                //get difference of days in current search
                $current_diff = $dateTime->date_difference( $start_date , $end_date );

                $difference = array();

                foreach ( $results as $key => $value ) {
                    //add key difference to association array $results
                    $results[$key]['date_difference'] = $dateTime->date_difference(  $results[$key]['wnc_start_date'] , $results[$key]['wnc_end_date'] ) ;

                    $results_dates[$key] = $results[$key]['date_difference'];
                }

                //Find closest date difference in results to form search difference
                if( array_multisort($results_dates, SORT_ASC, $results) ) {
                    foreach ($results as $a) {
                        if ($a['date_difference'] >= $current_diff){
                            $trans_start_date = $a['wnc_start_date'];
                            $trans_end_date = $a['wnc_end_date'];
                            break;
                        }
                    }
                } else { // Use default result index : 0
                    $trans_start_date = $results[0]['wnc_start_date'];
                    $trans_end_date = $results[0]['wnc_end_date'];
                }

                /*var_dump($results);
                echo "current diff: " .  $current_diff . "<br>";
                echo $trans_start_date;
                echo $trans_end_date;*/

                //Check transient data for search range
                $check_transient = get_transient( 'wnc_' . $fields_result['error_type'] . '_' . $trans_start_date . '_' . $trans_end_date);
            }

            //if query results and transient exist, use transient data
            if( $check_transient && !empty( $results ) ) {
                //Build data table for browser rendering
                $return_data = $this->build_data_table( $this->mandrill_keys, 'ts', $check_transient, $start_date, $end_date ) ;

               /* var_dump( $data_table );

               echo "<h2>Transient Data:</h2>";
               var_dump($check_transient);*/
             } else {  //Query mandrill API for fresh data
               $errors = $mandrill->searchErrors( $fields_result['error_type'], $start_date, $end_date, '', '', $api_key );

               //echo "<h2>DIRECT API RESULTS:</h2>";
               //var_dump($errors);

               //Determine if Exception or Mandrill spam/email error list
               if( $errors instanceof Exception ) {
                  $mandrill_api_error = 'A mandrill error occurred: ' . get_class( $errors ) . ' - ' . $errors->getMessage();
               } else if( !empty( $errors ) ) {
                  set_transient( 'wnc_' . $fields_result['error_type'] . '_' . $start_date . '_' . $end_date, $errors, 96 * HOUR_IN_SECONDS ); //4 days

                  //Store mandrill data in transient api
                  $wpdb->query( $wpdb->prepare(
                                "
                  INSERT INTO $tablename
                  ( wnc_start_date, wnc_end_date, wnc_api_username, wnc_api_type )
                  VALUES ( %s, %s, %s, %s )
                  ", $start_date, $end_date, $username,  strtolower( $fields_result['error_type'] )
                  ) );

                   // Create data table to return
                   $return_data = $this->build_data_table( $this->mandrill_keys, 'ts', $errors, $start_date, $end_date ) ;

               }
             }
    }
        if( $validation->isErrors() ) {
            //var_dump($validation->errors());
            $data_table['errors'] =  $validation->errors();
            echo json_encode ( $data_table['errors'] );
            return json_encode ( $data_table['errors'] );
        }

        //create nonce for form
        $nonce_json = array();
        array_push($nonce_json, wp_create_nonce( 'csrf-nonce-search-update' ));

        $data_table['nonce'] = $nonce_json;
        $data_table['data_table'] =  $return_data;

        session_start();
        $_SESSION['expected_data'] = $return_data;

        echo json_encode( $data_table );
        return json_encode( $data_table );
  }

    /**
     * Updates MailPoet tables: wp_wysija_user_list and wp_wysija_user so Mandrill errored emails
     * are disabled in future newsletter blasts.
     * @return mixed array|string
     */
    function search_update() {
        session_start();

        $session_emails = array();
        $valid_emails = array();

        if( isset( $_SESSION['expected_data'] ) ) {
            $session_data =  $_SESSION['expected_data'];
        } else {
            echo "error: Session expired. Please re-do your search and try again.";
            return 'error';
        }


        if( isset( $_POST['check'] ) ) {
            $form_emails  = $_POST['check'];
        } else {
            echo "error: No form fields are checked. Please check atleast one item to be removed from email list.";
            return 'error';
        }

        //iterate through expected emails and create array of approved emails
        foreach( $session_data as $item ) {
            foreach ( $item as $key => $value ) {
                if ($key == 'email') {
                    array_push($session_emails, $value);
                }
            }
        }

        $new_array = array();

        //iterate form submitted emails for updating and verify they were in session data
        foreach ( $form_emails as $key => $value ) {
            if( in_array( $value, $session_emails )) {
                //array_push($valid_emails, $value);
                $new_array = array( 'email' => $value );
                $valid_emails[] = $new_array;
            }
        }

        global $wpdb;

        $date = new DateTime();

        $wp = new WNC_WPDB;

        $user_ids =  $wp->select_in( $wpdb->prefix . 'wysija_user',  'email', $valid_emails, array( 'user_id', 'email') );

        foreach ( $user_ids as $user_id )
        {
            if($wp->update( $wpdb->prefix . 'wysija_user', 'user_id', $user_id->user_id , array('status' => '-1' ) ) > 0 ) {

                $wp->update( $wpdb->prefix . 'wysija_user_list', 'user_id', $user_id->user_id , array('unsub_date' => $date->getTimestamp() ) );

                echo '<p>User ' . $user_id->email . ' has been unsubscribed from Mail Poet. </p>';
            } else {
                echo "<p>User $user_id->email is already unsubscribed </p>";
            }

        }

    }

    /**
     * Iterate through json object and build html data table
     * @param array $expected expected keys in json object
     * @param object $json json object
     * @return string
     */
    function build_data_table ( $expected = array(), $time_field, $json, $start, $end ) {

        // decode $json if it is a json object
        if( !is_array( $json ) ) {
            $json = json_decode( $json, true );
        }

        $loop_count = 0;
        $parsed = array();

        $dateTime = new WNC_DateTime();

        foreach($json as $json_results) {
            //Check that time is within range of search
            if($json_results[$time_field]) {
                if($dateTime->within_range($start,$end, $json_results[$time_field])) { // we're in date range
                    foreach( $expected as $key ) {
                        if(isset($json_results[$key])) {
                            if($key=='ts') { //time specific node
                                $parsed[$loop_count][$key] = date('m/d/Y', $json_results[$key]);
                            } else {
                                $parsed[$loop_count][$key] = $json_results[$key];
                            }
                        }
                    }

                    //increment only when requirements are satisfied
                    $loop_count++;
                }
            }
        }

        return $parsed;
    }

}