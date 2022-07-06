<?php

namespace HwpClub\core\pages_handler;



use HwpClub\core\includes\Functions;
use HwpClub\core\includes\Points;
use HwpClub\resources\ui\FrontPartials;
use HwpClub\resources\ui\Icons;
use Jose\Component\Core\Util\Ecc\Point;

class PointsHandler
{

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
        // add_action('wp_ajax_club_update_user_profile' ,[$this , 'updateProfile']);
    }


    public static function getHistory( $userObject )
    {
        $items = Points::getUserPoints( $userObject->ID );
        $points  = '';
        if ( !empty( $items ) ){
            foreach ( $items as $item ){
                $ui    = str_replace( '[title]'       ,Functions::indexChecker( $item ,'title') ,FrontPartials::pointsHistoryRow() );
                $ui    = str_replace( '[amount]'      ,Functions::indexChecker( $item ,'amount') ,$ui );
                $ui    = str_replace( '[class-type]'  ,Functions::indexChecker( $item ,'type') ,$ui );
                $ui    = str_replace( '[added-date]'  ,self::getDateFormat( $item ,'created_at') ,$ui );
                $ui    = str_replace( '[expire-date]' ,self::getDateFormat( $item ,'expire_date') ,$ui );
                $ui    = str_replace( '[type]'        ,self::getTypeTranslate( $item ) ,$ui );

                if( is_numeric( $item->challenge_id ) && $item->challenge_id > 0 ){
                    $single_button = str_replace( '[single-url]' ,home_url('/club/challenge-single/'.$item->challenge_id ) ,FrontPartials::pointsHistoryChallengeButton() );
                    $ui = str_replace( '[challenge-button]' ,$single_button ,$ui );
                }else{
                    $ui = str_replace( '[challenge-button] ' ,'' ,$ui );
                }
                $points .= $ui;
            }
        }
        if ( empty( $points ) ){
            $points = FrontPartials::noPointsHistory();
        }
        $main = str_replace( '[profile]' ,HomeHandler::profileWidget( $userObject ) ,FrontPartials::pointsHistory() );
        return str_replace( '[items]' ,$points ,$main );
    }


    public static function getTypeIcon( $item )
    {
        $type = Functions::indexChecker( $item ,'type');
        if ( $type == 'credit' ){
            return Icons::plus();
        }
        return Icons::subtract();
    }


    public static function getTypeTranslate( $item )
    {
        $type = Functions::indexChecker( $item ,'type');
        if ( $type == 'credit' ){
            return 'افزایشی';
        }elseif ( $type  == 'subtract' ){
            return 'کاهشی';
        }
        return 'نامشخص';
    }


    public static function getCreatedDate( $item )
    {
        $date = Functions::indexChecker( $item ,'created_at');
        if ( !empty( $date ) ){
            return human_time_diff( time() , strtotime( $date ) );
        }
        return '-';
    }


    public static function getDateFormat( $item ,$key )
    {
        $date = Functions::indexChecker( $item ,$key );
        if ( !empty( $date ) ){
            return date_i18n('Y-m-d H:i' ,strtotime( $date ) );
        }
        return '-';
    }


    public static function creditConverter( $userObject )
    {
        $userDetails = Points::getUserDetails( $userObject->ID );
        if ( !empty( $userDetails ) ){
            if ( $userDetails->has > 0 ){
                return str_replace( '[score]' ,$userDetails->has ,FrontPartials::convertCredit() );
            }else{
                return 'شما امتیازی برای تبدیل ندارید';
            }
        }
        return false;
    }







}
