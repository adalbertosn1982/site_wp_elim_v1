<?php
/**
 * This class interacts with the WordPress database specifically
 * Created by PhpStorm.
 * User: production
 * Date: 4/15/15
 * Time: 10:21 PM
 */

class WNC_WPDB {

    function buildQuery ( $items ) {
        $values = array();
        global $wpdb;

        // We're preparing each DB item on it's own. Makes the code cleaner.
        foreach ( $items as $key => $value ) {
            //$values[] = $wpdb->prepare( "(%s,%s)", $key, $value );
            $values[] = $key . ',' . $value;
        }

        $query = "INSERT INTO orders (order_id, product_id, quantity) VALUES ";
        $query .= implode( ",\n", $values );

        return $query;
    }


    //Will only update 1 record at a time
    public function update( $table, $key, $id, $fields = array() ) {

        $set 	= null;
        $x		= 1;
        $thetype = '%s';
        $values = array();

        foreach ( $fields as $name => $value ) {
            //determine type for wordpress
            if (is_float($value)) $thetype = '%f';
            if (is_int($value)) $thetype = '%d';
            if (is_string($value)) $thetype = '%s';
            $set .= "{$name} = $thetype";
            if($x < count($fields) AND count($fields) > 1 ) {
                $set .= ', ';
            }
            $values[] = $value;
            $x++;
        }

        $set = rtrim( $set, ',' );

        if (is_float($id)) $keytype = '%f';
        if (is_string($id)) $keytype = '%s';
        if (is_int($id)) $keytype = '%d';

        array_push( $values, $id );

        global $wpdb;

        $sql = "UPDATE {$table} SET {$set} WHERE {$key} = {$keytype}";

        try {
            //echo $wpdb->prepare( $sql, $values );
           return $wpdb->query( $wpdb->prepare( $sql, $values) ) . '$values' ;
        }
        catch ( Exception $ex ) {
                return $ex->getMessage();
         }
    }

    //Will only update 1 record at a time
    public function select_in($table, $key , $ids = array(), $fields = array()) {

        $set 	= null;
        $x		= 1;

        foreach ( $fields as $name => $value ) {
            //determine type for wordpress
            $set .= $value;
            if($x < count($fields) AND count($fields) > 1 ) {
                $set .= ', ';
            }
            $x++;
        }

        $escape_set= '';
        foreach ( $ids as $id ) {
            foreach ( $id as $name => $value ) {
                $escape_set .= ' %s,';
                $id_param[] = $value;
            }
        }

        $set = rtrim( $set, ',' );
        $escape_set = rtrim( $escape_set , ',');


        $sql = "SELECT {$set} FROM {$table} WHERE {$key} IN ({$escape_set})";

        global $wpdb;

     $results = $wpdb->get_results($wpdb->prepare( $sql, $id_param ));

        return $results;

        if(!$this->query($sql, $fields)->error()) {
            return true;
        }

        return false;
    }

    public function query($sql, $params = array()) {

        $this->_error = false;

        $param_array = array();

            $x = 0;
            if(count($params)) {
                foreach($params as $param) {
                    $param_array[$x] = $param;
                    $x++;
                }
            }

        global $wpdb;

        if( count( $param_array ) ) {
            $result = $wpdb->query( $wpdb->prepare( $sql
          , $param_array )
            );
        }

            if($this->_query->execute()) {
                $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $this->_query->rowCount();
            } else {
                $this->_error = true;
            }

        return $this;
    }

}