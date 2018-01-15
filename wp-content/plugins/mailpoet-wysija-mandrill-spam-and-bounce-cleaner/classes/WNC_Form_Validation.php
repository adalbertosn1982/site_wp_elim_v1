<?php
/**
 * Created by PhpStorm.
 * User: production
 * Date: 1/23/15
 * Time: 6:48 AM
 */
class WNC_Form_Validation
{
    private $_errors = array();

    public static function expectedFields( $expected = array(), $source = array() , $removeSubmit = false ) {
        if ($removeSubmit) unset( $source['submit'] ); //kill submit value

        $build_array = array();

        foreach ( $expected as $key => $value ) {
            if ( !empty($source[$value]) ) {
                $build_array[$value] = $source[$value];
            }
        }

        return $build_array;
    }

    public function check( $source, $items = array(), $request_type = "POST" ) {

        //Check if valid request type (POST , GET, etc)
        if(!empty($request_type)) {
            $this->validRequest($request_type);
        }

        foreach($items as $item => $rules) {
            foreach($rules as $rule => $rule_value) {

                $value = trim($source[$item]);

                if( $rule === 'required' && $rule_value === true && empty($value) ) {
                    $this->addError( "{$item} is required." );
                } else if ( !empty($value) ) {

                    switch( $rule ) {
                        case 'min':
                            if( strlen($value) < $rule_value ) {
                                $this->addError("{$item} must be a minimum of {$rule_value} characters.");
                            }
                            break;
                        case 'max':
                            if( strlen($value) > $rule_value ) {
                                $this->addError("{$item} must be a maximum of {$rule_value} characters.");
                            }
                            break;
                        case 'matches':
                            if( $value != $source[$rule_value] ) {
                                $this->addError("{$rule_value} must match {$item}.");
                            }
                            break;
                    }

                }

            }
        }

        return $this;
    }

    public function addError($error) {
        $this->_errors[] = $error;
    }

    public function errors() {
        return $this->_errors;
    }

    public function isErrors() {
        if( !empty( $this->_errors ) ) {
            return true;
        } else {
            return false;
        }
    }

    public function email($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            $this->addError( 'Email is not valid.' );
            return false;
        }
    }

    public function validRequest( $request_type = 'POST' ) {
        if( $_SERVER['REQUEST_METHOD']==strtoupper( $request_type ) ) {
            return true;
        } else {
            $this->addError( 'Invalid request type.' );
            return false;
        }
    }

    public function validElement( $source, $allowed = array() ) {
        foreach( $allowed as $key => $val ) {
            if( $key==$source ) {
                return true;
                break;
            }
        }
        return false;
    }

}