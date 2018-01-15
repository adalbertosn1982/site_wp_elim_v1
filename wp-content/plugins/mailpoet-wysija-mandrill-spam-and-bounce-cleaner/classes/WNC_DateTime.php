<?php
/**
 * Created by PhpStorm.
 * User: production
 * Date: 2/8/15
 * Time: 3:23 PM
 */

class WNC_DateTime {

    /**
     * Returns true if date is valid, false if date is invalid
     * @param DateTime $start date in Y-m-d format
     * @param string $delimiter Delimiter used to separate month, day, year in date.
     * @return bool
     */
    public function valid_date( $date, $delimiter ){
        $split_date  = explode( $delimiter, $date );

        return checkdate( $split_date[0], $split_date[1], $split_date[2] );
    }

    /**
     * Returns number of days between two dates
     * @param DateTime $start date in Y-m-d format
     * @param DateTime $end  date in Y-m-d format
     * @return int
     */
    public function date_difference ( $start , $end ) {

        $date1 = new DateTime($start);
        $date2 = new DateTime($end);

        $diff = $date2->diff($date1)->format("%a");

        return $diff;
    }


    /**
     * Check if user supplied date is within start/end range.
     * @param DateTime $start_date date in Y-m-d format
     * @param DateTime $end_date  date in Y-m-d format
     * @param int $date_from_user timestamp
     * @return bool
     */
    public function within_range ( $start_date, $end_date, $date_from_user ) {
        {
            // Convert to timestamp
            $start_ts = strtotime($start_date);
            $end_ts = strtotime($end_date);

            $user_ts = $date_from_user; //strtotime($date_from_user);

            // Check that user date is between start & end
            return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
        }
    }

}