<?php

namespace HwpClub\core\includes;

use stdClass;

class Points
{
    private static $params;
    protected static $_instance = null;
    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        add_action( 'wp_ajax_club_convert_credit' ,[$this ,'convertCredit']);
    }


    public static function calculateProductPoint( $price )
    {
        if ( $price > 0 ){
            $point = (($price/1000000)*50)+10;
            return $point;
        }
        return 1;
    }

    public static function getUserDetails( $userID )
    {
        $point = self::getMeta( $userID );
        if( !empty( $point ) ) return $point;
        return self::resetPointMeta( $userID );
    }

    public static function resetPointMeta( $userID )
    {
        $plus_points  = 0;
        $minus_points = 0;
        $items        = self::getUserPoints( $userID );
        if ( !empty( $items ) ){
            foreach ( $items as $item ){
                if ( $item->type == 'credit' ){
                    $plus_points  += (int) $item->amount;
                }else{
                    $minus_points += (int) $item->amount;
                }
            }
        }
        $final_points = $plus_points - $minus_points ;
        $rank  = Ranks::getRank( $plus_points )['slug'];
        $point = "'has',$final_points,'reach',$plus_points ,'rank','$rank'";
        return self::insertMeta( $userID ,$point );
    }


    public static function getMeta( $userID )
    {
        if ( $userID > 0 ){
            global $wpdb;
            $results = $wpdb->get_results(
                "SELECT * FROM {$wpdb->usermeta} WHERE meta_key = 'user_point' AND user_id = {$userID};"
            );
            if ( !is_wp_error( $results ) && !empty( $results ) ){
                return json_decode( $results[0]->meta_value );
            }
        }
        return null;
    }

    public static function insertMeta( $userID ,$userData )
    {
        if ( $userID > 0 ){
            global $wpdb;
            $results = $wpdb->query(
                "INSERT INTO {$wpdb->usermeta} ( user_id ,meta_key ,meta_value )
                        VALUES ({$userID} ,'user_point' ,JSON_OBJECT({$userData}) );"
            );
            if ( !is_wp_error( $results ) && !empty( $results ) ){
                return self::getMeta( $userID );
            }
        }
        return null;
    }


    public static function deleteUserPointMeta( $userID  )
    {
        if ( $userID > 0 ){
            delete_user_meta( $userID, 'user_point' );
        }
    }


    public static function calcStartUserPoint( $userID ,$data )
    {
        $free_product      = $data['free_product']*1;
        $purchased_product = $data['purchased_product']*10;
        $full_payment      = $data['full_payment']/1000000*50;
        $register_date     = $data['register_date']*5;
        $register_form     = 10;
        $club_argument     = 10;
        $point  = $free_product + $purchased_product + $full_payment + $register_date + $register_form + $club_argument;
        $params = new stdClass();
        $params->title         = 'فرم ثبت نام';
        $params->rank          = Ranks::getRank( $point )['slug'];
        $params->type          = 'credit';
        $params->amount        = $point;
        $params->user_id       = $userID;
        $params->added_by      = $userID;
        $params->challenge_id  = '';
        $params->descriptions  = 'کامل کردن فرم ثبت نام';
        self::addPoint( $userID ,$params );
        self::getUserDetails( $userID );
        return $point;
    }


    public static function getUserPoints( $userID ,$front = true )
    {
        global $wpdb;
        $table = $wpdb->prefix.'club_users_points';
        if ( $front ){
            $where = " WHERE user_id = {$userID} ";
        }else{
            $where = " LIMIT 20  ";
        }
        $results = $wpdb->get_results(
            "SELECT * FROM {$table} {$where} ;"
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ){
            return $results;
        }
        return [];
    }


    //// Points
    public static function addPoint( $user_id, $params )
    {
        Ranks::maybeInsertRank( $user_id ,$params->rank ,self::getUserDetails( $user_id ) );
        global $wpdb;
        $wpdb->insert( $wpdb->prefix.'club_users_points' , [
            'user_id'      => $params->user_id       ,
            'challenge_id' => $params->challenge_id  ,
            'title'        => $params->title         ,
            'rank'         => $params->rank          ,
            'type'         => $params->type          ,
            'amount'       => $params->amount        ,
            'added_by'     => $params->added_by      ,
            'descriptions' => $params->descriptions  ,
            'created_at'   => date('Y-m-d H:i:s') ,
            'expire_date'  => date('Y-m-d H:i:s' ,strtotime('+30 days'))
        ],
            ['%d','%d','%s','%s','%s','%d','%d','%s','%s','%s'] );
        if ( empty( $wpdb->last_error ) && is_numeric( $wpdb->insert_id ) ){
            self::deleteUserPointMeta( $user_id );
            self::resetPointMeta( $user_id );
            return true;
        }
        return false;
    }


    public static function getUserPointsWithSort( $userID )
    {
        $userPoints  = self::getUserPoints( $userID );
        $hasPoints = [];
        if ( !empty( $userPoints ) ){
            foreach ( $userPoints as $point ){
                if ( $point->type == 'credit' ){
                    $hasPoints[$point->challenge_id] = $point;
                }
            }
        }
        return $hasPoints;
    }



    public static function convertCredit()
    {
        if ( Functions::checkNonce( $_POST['nonce'] ,'club_nonce' ) && isset( $_POST['amount'] ) ){
            $amount  = $_POST['amount'];
            $user_id = get_current_user_id();
            $details = self::getUserDetails( $user_id );
            if ( !empty( $details ) && isset( $details->has ) && $details->has > 0 && $details->has >= $amount ){
                if ( self::addCreditToHamyarUser( $user_id, $amount ) ){
                    $params               = new stdClass();
                    $params->title        = 'تبدیل اعتبار';
                    $params->challenge_id = '' ;
                    $params->descriptions = self::createDescription( $amount );
                    $params->rank         = Ranks::getRank( $details->has - $amount )['slug'];
                    $params->type         = 'subtract';
                    $params->amount       = $amount;
                    $params->user_id      = $user_id;
                    $params->added_by     = $user_id;
                    $result = Points::addPoint( $user_id ,$params );
                    if ( $result ){
                        wp_send_json_success(['result' => 'success']);
                    }
                }
            }
        }
        wp_send_json_error(['result' => 'error']);
    }


    public static function addCreditToHamyarUser( $userID ,$amount )
    {
        if ( is_numeric( $userID ) && $amount > 0 ){
            $mobile  = get_user_meta( $userID ,'force_verified_mobile' ,true );
            if ( !empty( $mobile ) ){
                $body     = [
                    'key'    => HWP_CLUB_REST_KEY ,
                    'mobile' => $mobile ,
                    'amount' => ((int)$amount)*1000
                ];
                if ( Functions::remotePost( HAMYAR_ENDPOINT.'/wp-json/club/add-credit' ,$body ) ){
                    return true;
                }
            }
        }
        return false;
    }


    public static function createDescription( $amount )
    {
        $credit  = $amount * 10;
        return
            'تبدیل
            '.$credit.
            'امتیاز به '.
            $amount .
            ' اعتبار'
        ;
    }


    public static function getAllPurchasedList( $mobile,$user_id )
    {
        $old_point=get_user_meta( $user_id ,'hamyar_get_request' ,true );
        if ( !empty( $old_point ) ){
            return $old_point;
        }

        $url = add_query_arg([
            'key'    => HWP_CLUB_REST_KEY,
            'mobile' => $mobile
        ] ,HAMYAR_ENDPOINT.'/wp-json/club/get-point/' );
        $arg = ['timeout' => 20, 'sslverify' => false];
        $response = wp_remote_get( $url, $arg );
        if ( !is_wp_error( $response )  ) {
            $response_code = wp_remote_retrieve_response_code( $response );
            if ( $response_code == 200 ) {
                $response_body = json_decode( wp_remote_retrieve_body( $response ), true );
                $point= Points::calcStartUserPoint( $user_id ,$response_body );
                update_user_meta( $user_id ,'hamyar_get_request' ,$point );
                return $point;
            }
        }
        return false;
    }


    public static function getUserID( $mobile )
    {
        global $wpdb;
        $results = $wpdb->get_var(
            "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = 'force_verified_mobile' AND meta_value = '{$mobile}' LIMIT 1;"
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ){
            return (int) $results;
        }
        return [];
    }



    public static function getScoreByProductPrice( $price )
    {
        if ( $price > 0 ){
            return $price/1000000 * 50;
        }
        return 0;
    }


    public static function addSubtractPoint( $postMeta ,$postID ,$userObject )
    {
        if ( isset( $postMeta[$postID]['challenge_cost_point']  ) &&  $postMeta[$postID]['challenge_cost_point'] > 0 ){
            $params = new stdClass();
            $params->title = get_the_title( $postID );
            $params->challenge_id = $postID;
            $params->descriptions = 'Subtract On Register On Challenge ID : '.$postID;
            $params->rank = Ranks::getRank( $userObject->point->reach )['slug'];
            $params->type = 'subtract';
            $params->amount = $postMeta[$postID]['challenge_cost_point'];
            $params->user_id = $userObject->ID;
            $params->added_by = $userObject->ID;
            $params->challenge_id = $postID;
            return Points::addPoint( $userObject->ID ,$params );
        }
        return false;
    }

}
