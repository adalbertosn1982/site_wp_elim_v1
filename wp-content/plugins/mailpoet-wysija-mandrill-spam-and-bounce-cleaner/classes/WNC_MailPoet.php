<?php
/**
 * Created by PhpStorm.
 * User: production
 * Date: 1/19/15
 * Time: 12:22 AM
 */

class WNC_MailPoet{

    protected $options = array();
    protected $campaigns = array();

    function __construct() {
    }

    function get_options( $option_name = 'wysija' ) {
        $encoded_option = get_option($option_name);
        $this->options = unserialize( base64_decode( $encoded_option ) );
        return $this->options;
    }

    function get_Mandrill() {
        if(empty($this->options)) {
            $this->get_options();
        }
    }

    function get_campaigns() {
        //$this->campaigns = $this->some_function();
    }

    function __get($property) {
        if(property_exists($this,$property)) {
            return $this->$property;
        }
    }


    /** Updates user status in the wysija_user table
     * @param string $status string
     * @param $user_email mixed string|array
     * @return void
     */
    function set_user_status( $status = '-1', $user_email ) {

        //Store mandrill data in transient api
        $wpdb->query( $wpdb->prepare(
            "
                  INSERT INTO $tablename
                  ( wnc_start_date, wnc_end_date, wnc_api_username, wnc_api_type )
                  VALUES ( %s, %s, %s, %s )
                  ", $start_date, $end_date, $username,  strtolower( $fields_result['error_type'] )
        ) );

        //Update status of emails in WordPress newsletter
        if(!empty($rows)) {

            foreach($rows as $emails) {

                $sql = "SELECT * FROM wp_wysija_user WHERE email = :email";
                $q2 = $db2->prepare($sql);

                $q2->execute(array('email' => $emails['email']));

                if($q2->rowCount() > 0) {
                    $q2->setFetchMode(PDO::FETCH_ASSOC);
                    while($rows2[] = $q2->fetch()) {
                    }
                    foreach($rows2 as $userids) {
                        $timestamp = $date->getTimestamp();
                        $userid = $userids['user_id'];
                        //echo $userid;

                        if(!empty($userid)) {
                            $update_userlist .= "UPDATE wp_wysija_user_list SET unsub_date='$timestamp' WHERE user_id='$userid';" . "\n";
                            $update_users .= "UPDATE wp_wysija_user SET status='-1' WHERE user_id='$userid';" . "\n";
                            $group_str .= $userid . ',';
                        }
                    }
                    unset($rows2);
                    unset($q2);
                }
            }

            if(!empty($update_users)) {

                $group_str = rtrim($group_str, ',');
                $group_str = '(' . $group_str . ')';
                $file = file_put_contents('ids.txt',$group_str);
                //WHERE user_id in (" . $group_str . "))

                /* echo $update_userlist;
                 echo $update_users;*/

                $file = file_put_contents('userlist.txt',$update_userlist);
                $file = file_put_contents('users.txt',$update_users);


                $db3 = new PDO("mysql:host=localhost;port=3306;dbname=mandrill_wp",'root','root');
                $q3 = $db3->prepare($update_userlist);
                $q3->execute();

                $q3 = $db3->prepare($update_users);
                $q3->execute();
                $q3->closeCursor();
                //Execute updates

            }
        }
    }
}