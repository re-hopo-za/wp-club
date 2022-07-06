<?php
/*
 * Template Name: Panel Template
 * Description:  Template Page For Panel Page.
 */

namespace HwpClub\resources\ui;

use HwpClub\core\includes\Functions;
use HwpClub\core\includes\Users;


class ClubUrlHandler
{
    protected static $_instance = null;
    public static function get_instance()
    {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        self::home( Functions::getCurrentPage() );
    }


    public static function home( $page )
    {
        $final      = '';
        $userObject = Users::getUserObject();
        if( !$userObject ){
            wp_redirect( home_url( '/login' ) );
            exit;
        }
        if( !$userObject->start_score ){
            \wp_safe_redirect('/user-info');
        }
        get_header();


        if ( empty( $page[1] ) ) {
            $page = $page[0];
            if ($page == 'home') {
                $final = FrontPages::home( $userObject );
            }
            elseif ($page == 'point-history') {
                $final = FrontPages::getUserPointHistory( $userObject );
            }
            elseif ($page == 'edit-profile') {
                $final = FrontPages::editUserInformationForm( $userObject );
            }
            elseif ($page == 'club-rules') {
                $final = FrontPages::clubRules( $userObject );
            }
            elseif ($page == 'challenges-list') {
                $final = FrontPages::challengesList( $userObject );
            }
            elseif ($page == 'chat') {
                $final = FrontPages::chat( $userObject );
            }
            elseif ($page == 'notifications') {
                $final = FrontPages::notifications( $userObject );
            }
            elseif ($page == 'use-points-methods') {
                $final = FrontPages::usePointsMethods( $userObject );
            }
            elseif ($page == 'increase-points') {
                $final = FrontPages::increasePoint( $userObject );
            }
            elseif ($page == 'showcase') {
                $final = FrontPages::showcase( $userObject );
            }
            elseif ($page == 'notes') {
                $final = FrontPages::notes( $userObject );
            }
            elseif ($page == 'close-friends') {
                $final = FrontPages::closeFriends( $userObject );
            }
            elseif ($page == 'users') {
                $final = FrontPages::users( $userObject ,null );
            }



            /// posts pages
            elseif ($page == 'guide-upgrade-rank') {
                $final = FrontPages::guideUpgradeRank( $userObject );
            }
            elseif ($page == 'guide-get-points') {
                $final = FrontPages::guideGetPoints( $userObject );
            }
            elseif ($page == 'credit-converter') {
                $final = FrontPages::creditConverter( $userObject );
            }
            elseif ($page == 'register-in-hamyar-courses') {
                $final = FrontPages::registerInHamyarCourses( $userObject );
            }
            elseif ($page == 'buy-consulting-services') {
                $final = FrontPages::buyConsultingServices( $userObject );
            }
            elseif ($page == 'consulting-haji-mohamadi') {
                $final = FrontPages::consultingHajiMohamadi( $userObject );
            }
            elseif ($page == 'convert-point-to-credit-description') {
                $final = FrontPages::convertPointToCreditDescription( $userObject );
            }
            elseif ($page == 'contact-us') {
                $final = FrontPages::contactUs( $userObject );
            }
            elseif ( current_user_can('administrator') && $page == 'admin-panel') {
                $final = FrontPages::adminPanel( $userObject );
            }
        }
        elseif ( $page[0] == 'challenge-single' )
        {
            if ( empty( $page[2] ) ){
                $final = FrontPages::challengeSingle( $userObject ,$page[1] );
            }
            elseif ( $page[2]  == 'comments' ) {
                $final = FrontPages::challengeSingleComments( $userObject ,$page[1] );
            }
            elseif ( $page[2]  == 'users' ){
                $final = FrontPages::challengeSingleUsers( $userObject ,$page[1] );
            }
        }
        elseif ( $page[0] == 'close-friend' ) {
            $final = FrontPages::closeFriendSingle( $userObject ,$page[1] );
        }
        elseif ( $page[0] == 'users') {
            $final = FrontPages::users( $userObject ,$page[1] );
        }


        if ( !empty( $final ) ){
            echo str_replace( '[side-nav]' ,Functions::replaceUserInfoOnNav( $userObject ) ,$final );
        }else{
            echo FrontPages::page404( $userObject );
        }
        get_footer();
    }












}



ClubUrlHandler::get_instance();
