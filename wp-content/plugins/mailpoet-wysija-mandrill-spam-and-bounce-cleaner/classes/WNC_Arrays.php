<?php
/**
 * Created by PhpStorm.
 * User: production
 * Date: 1/23/15
 * Time: 4:53 AM
 */

Class WNC_Arrays {

    function remove_empty_values( $myArray = array() ) {
        foreach( $myArray as $key => $value )
        {
            if( empty( $value ) )
            {
                unset( $myArray[$key] );
            }
        }

        return $myArray;
    }

    /**
     * Finds closest value in array to $number
     * @param array $array array of numbers
     * @param integer $number number to find closest value to
     * @param bool $assoc [optional]
     * Tells us if we are using an association array
     * @param string $assoc_key [optional]
     * The association key to use.
     * @return mixed bool|integer
     */
    public function closest( $array, $number  ) {

        sort($array);
        foreach ($array as $a) {
                if ($a >= $number) return $a;
        }
        return false;
}

    public function multi_closest ( $array = array() , $main_array = array(), $assoc_key,  $number ) {

        // Sort the data with date_difference ascending
        // Add $data as the last parameter, to sort by the common key
       if( array_multisort($array, SORT_ASC, $main_array) ) {

           foreach ($array as $a) {
                   if ($a[$assoc_key] >= $number) return $a;
           }
       }
        return false;
    }


    /**Function to iterate through primary and secondary json objects
     * $primary =  array('id', 'address1','address2','city','region','zipcode','payment_method','payment_terms');
    $contain = array('email', 'first_name', 'last_name');
    //Ex:  iteratePrimaryContain($data,$primary,$contain,'Affiliate','AffiliateUser');
     **/

    /**
     * Finds closest value in array to $number
     * @param array $array array of numbers
     * @param integer $number number to find closest value to
     * @param bool $assoc [optional]
     * Tells us if we are using an association array
     * @param string $assoc_key [optional]
     * The association key to use.
     * @return mixed bool|integer
     */

    public function iterateJSONArray($json_object, $primary = array(), $secondary = array(), $primary_object_name, $secondary_object_name) {

        $array_count = 0;
        foreach($json_object as $json_results) {
            foreach($json_results as $json_data) {
                foreach($json_data as $wonka) {
                    //primary array holds expected return values from main call
                    foreach($primary as $key => $value) {
                        if($wonka[$value]) {
                            //echo 'got it ' . $wonka[$value];
                            $result[$primary_object_name][$array_count][$value] = $wonka[$value];
                        }
                    }

                    if($json_data[$secondary_object_name]) {
                        foreach($json_data[$secondary_object_name] as $contain_obj) {
                            //contain array holds expected return values from contain call
                            foreach($secondary as $secondary_value) {
                                if(isset($contain_obj[$secondary_value])) {
                                    //echo 'yee::::' . $contain_obj[$contain_value];
                                    $result[$primary_object_name][$array_count][$secondary_object_name][$secondary_value] = $contain_obj[$secondary_value];
                                }
                            }
                        }
                    }

                }
                $array_count++;
            }
        }

        if(count($result)){
            return $result;
        }else {
            return false;
        }
    }

    /**
     * Iterate through json object and extract specific keys
     * @param $expected array expected keys in json object
     * @param $json object  json object
     * @return array
     */
    function parseJson( $expected = array(), $json ) {
        $parsed = array();
        $loop_count = 0;

        $json = json_decode( $json,true );

        foreach($json as $json_results) {
            foreach( $expected as $key ) {
                if(isset($json_results[$key])) {
                    $parsed[$loop_count][$key] = $json_results[$key];
                }
            }
            $loop_count++;
        }
    }

}