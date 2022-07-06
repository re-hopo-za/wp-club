<?php

namespace HwpClub\core\includes;


use Hashids\Hashids;
use HwpClub\core\pages_handler\NotifAndNoteHandler;
use HwpClub\resources\ui\FrontPartials;

class Functions
{




    protected static $_instance = null;
    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    public static function indexChecker( $parameters ,$index ,$default = '' )
    {
        if ( is_array( $parameters ) && isset($parameters[$index]) && !empty($parameters[$index] ) ) {
            return $parameters[$index];
        }elseif ( is_object( $parameters ) && isset($parameters->$index ) && !empty($parameters->$index ) ){
            return $parameters->$index;
        }
        return $default;
    }


    public static function getAllOrders( $userID )
    {
        $args = [
            'posts_per_page' =>  -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post_type'      => 'shop_order',
            'meta_key'       => '_customer_user',
            'meta_value'     => $userID ,
            'post_status'    => array_keys( self::woocommerceOrderStatus() )
        ];
        $orders = get_posts( $args );
        if ( !empty( $orders ) ){
            return $orders;
        }
        return [];
    }


    public static function woocommerceOrderStatus()
    {
        return [
           'wc-pending'    => _x( 'Pending payment', 'Order status', 'woocommerce' ),
           'wc-processing' => _x( 'Processing', 'Order status', 'woocommerce' ),
           'wc-on-hold'    => _x( 'On hold', 'Order status', 'woocommerce' ),
           'wc-completed'  => _x( 'Completed', 'Order status', 'woocommerce' ),
           'wc-cancelled'  => _x( 'Cancelled', 'Order status', 'woocommerce' ),
           'wc-refunded'   => _x( 'Refunded', 'Order status', 'woocommerce' ),
           'wc-failed'     => _x( 'Failed', 'Order status', 'woocommerce' )
        ];
    }


    public static function tabChecker( $ui ,$item )
    {
        if ( !empty( $ui ) && !empty( $item ) && strpos( $ui , $item ) ){
            return true;
        }
        return false;
    }


    public static function getCurrentPage()
    {
        $params       = self::requestedParams( $_SERVER['REQUEST_URI'] );
        $main_param   = self::indexChecker( $params , 1 , false );
        $second_param = self::indexChecker( $params , 2 , false );
        $third_param  = self::indexChecker( $params , 3 , false );
        $forth_param  = self::indexChecker( $params , 4 , false );
        $user_id      = get_current_user_id();
        if ( $user_id > 0 && $main_param == 'club' ){
            if ( $second_param == '' ){
                return ['home' ,null ];
            }else{
                return [$second_param ,$third_param ,$forth_param ];
            }
        }
        return ['login' ,null ];
    }


    public static function requestedParams( $http )
    {
        if ( !empty( $http ) ){
            if ( strpos( $http, '?' ) ){
                $http = substr( $http, 0, strpos( $http, '?' ) );
            }
            if( substr( $http, -1 ) == '/' ){
                $http =  substr( $http, 0, -1 );
            }
            if( strpos( $http, '/' ) !== false ){
                $http = explode('/' ,$http );
                if ( isset( $http[0] ) && empty( $http[0] ) ){
                    unset( $http[0] );
                }
                return $http;
            }
        }
        return [];
    }



    public static function frontNonceChecker()
    {
        if ( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'],'club_nonce') ){
            wp_send_json_error('invalid nonce',403  );
        }
        return true;
    }


    public static function replaceUserInfoOnNav( $userObject )
    {
        $main = str_replace( '[profile-image]' ,$userObject->avatar ,FrontPartials::sideNav() );
        $main = str_replace( '[notif-count]'   ,NotifAndNoteHandler::notificationsCount( $userObject ) ,$main );
        $main = str_replace( '[name]'          ,$userObject->display_name ,$main );
        $main = str_replace( '[has]'           ,$userObject->point->has ,$main );
        return str_replace(  '[rank]'          ,Ranks::getRank( $userObject->point->has )['translate'] ,$main );
    }


    public static function prepareImplode( $IDs )
    {
        if ( !empty( $IDs ) ){
            return implode( ',' , $IDs );
        }
        return '';
    }



    public static function encryptID( $id ){
        date_default_timezone_set('Asia/Tehran');
        $hashID = new Hashids( strtotime( 'tomorrow', strtotime( 'today', time() ) ) );
        return $hashID->encode( $id );
    }


    public static function decryptID( $hashedID ){
        date_default_timezone_set('Asia/Tehran');
        $hashID    = new Hashids( strtotime( 'tomorrow', strtotime( 'today', time() ) ) );
        $hashed_id = isset( $hashID->decode( $hashedID )[0] ) ? $hashID->decode( $hashedID )[0] : '';
        if ( is_numeric( $hashed_id ) and  $hashed_id > 0 ){
            return $hashed_id;
        }else{
            return false;
        }
    }


    public static function sanitizer( $value, $functions ){
        $functions = explode(',', $functions );
        foreach ( $functions as $function ) {
            if ( function_exists( $function ) ) {
                $value = $function( $value );
            }
        }
        return $value;
    }


    public static function checkNonce( $nonce ,$action ,$isAdmin = false )
    {
        if ( isset( $nonce ) && wp_verify_nonce( $nonce ,$action ) ){
            if ( !$isAdmin || current_user_can('administrator') ){
                return true;
            }
        }
        wp_send_json_error('invalid nonce',403  );
    }


    public static function remotePost( $url ,$body )
    {
        $response = wp_remote_post( $url,[
                'method'      => 'POST',
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'sslverify'   => false,
                'body'        => json_encode($body) ,
                'cookies'     => [],
                 'headers' => [
                     'content-type' => 'application/json'
                 ],
            ]
        );
        if ( !is_wp_error( $response ) && isset( $response['body'] ) ){
            $response = json_decode( $response['body'] );
            if ( isset( $response->_id )){
                return  $response->_id ;
            }
            if ( isset( $response->status ) && $response->status == 'success' ){
                return  true;
            }
        }
        return false;
    }


    public static function getActiveClass( $class )
    {
        $active = [ 1 => '' ,2 =>'' ,3 => '' ,4 => '' ,5 => ''] ;
        if ( $class == 'home-page' ){
            $active[1] = 'active';
        }
        elseif ( $class == 'challenges-list' ) {
            $active[2] = 'active';
        }
        elseif ( $class == 'chat' ) {
            $active[3] = 'active';
        }
        elseif ( $class == 'showcase' ) {
            $active[4] = 'active';
        }
        elseif ( $class == 'notes' ) {
            $active[5] = 'active';
        }
        return $active;
    }

    public static function getChatUrl( $userObject )
    {
        if ( !empty( $userObject->mobile ) && !empty( $userObject->token ) ){
            return sprintf(CHAT_FRONT_ENDPOINT.'/login-user?mobile=%s&token=%s' ,$userObject->mobile ,$userObject->token );
        }
        return 'javascript:void(0)';
    }






}
