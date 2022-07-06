<?php

namespace HwpClub\core\pages_handler;

use HwpClub\core\includes\Ranks;
use HwpClub\core\includes\Users;
use HwpClub\resources\ui\FrontPartials;

class UsersHandler
{


    public static function pageHandler( $userID )
    {
        if ( !empty( $userID ) && is_numeric( $userID ) ){
            return self::single( $userID );
        }
        return self::list();
    }


    public static function list()
    {
        $users = '';
        $row_users = Users::getAllUsers();
        if ( !empty( $row_users ) ){
            foreach ( $row_users as $user ){
                $main   = str_replace( '[profile]' ,Users::getUserAvatar( $user->ID ,$user->user_email ) ,FrontPartials::usersListPageItem() );
                $main   = str_replace( '[name]'    ,$user->display_name ,$main );
                $main   = str_replace( '[url]'     ,home_url('/club/users/'.$user->ID ) ,$main );
                $users .= str_replace( '[score]'   ,self::scoreJsonDecode( $user ) ,$main );
            }
        }
        return str_replace( '[users]' ,$users ,FrontPartials::usersListPage() );
    }


    public static function single( $userID )
    {
        if ( !empty( $userID ) && is_numeric( $userID ) ){
            $the_user = Users::getUserObject( $userID );
            if( !$the_user->private ){
                if ( !empty( $the_user ) ){
                    $main = str_replace( '[avatar]' ,Users::getUserAvatar( $the_user->ID ,$the_user->user_email ) ,FrontPartials::userSingle() );
                    $main = str_replace( '[timeline]' ,HomeHandler::rankTimeline( $the_user ) ,$main );
                    $main = str_replace( '[name]' ,$the_user->display_name ,$main );
                    $main = str_replace( '[rank]' ,Ranks::getRank( $the_user->point->reach )['translate'] ,$main );
                    $main = str_replace( '[has]'  ,$the_user->point->has ,$main );
                    $main = str_replace( '[biography]' ,$the_user->biography ,$main );
                    return  str_replace( '[challenges-sliders]' ,self::sliderSection( $userID ) ,$main );
                }
            }else{
                return '<div class="alert alert-danger">'.__('This user is private.','hwp').'</div>';
            }
        }
        return '';
    }

    public static function sliderSection( $userID )
    {
        $actives   = '';
        $completed = '';
        if ( is_numeric( $userID ) ){
            $sliders = ChallengesHandler::challengeSlider( $userID );
            if ( !empty( $sliders['actives'] ) ){
                $actives = str_replace( '[items]'  ,$sliders['actives'] ,FrontPartials::challengesSlider() );
                $actives = str_replace( '[target]' ,'actives' ,$actives );
                $actives = str_replace( '[items]'  ,$actives ,FrontPartials::challengesSliderHolder() );
                $actives = str_replace( '[type]'   ,'actives' ,$actives );
                $actives = str_replace( '[title]'  ,'چالش های فعال' ,$actives );
            }
            if ( !empty( $sliders['completed'] ) ){
                $completed = str_replace( '[items]' ,$sliders['completed'] ,FrontPartials::challengesSlider() );
                $completed = str_replace( '[target]' ,'completed' ,$completed );
                $completed = str_replace( '[items]' ,$completed ,FrontPartials::challengesSliderHolder() );
                $completed = str_replace( '[type]'  ,'completed' ,$completed );
                $completed = str_replace( '[title]' ,'چالش های تکمیل شده' ,$completed );
            }
        }
        return $actives . $completed;
    }




    public static function scoreJsonDecode( $userDB )
    {
        if ( isset( $userDB->score ) && !empty( $userDB->score ) ){
            $score = json_decode( $userDB->score );
            if ( is_object( $score ) && isset( $score->reach ) ){
                $score = (int) $score->reach / 10;
                return $score > 100 ? 100 :  $score;
            }
        }
        return 0;
    }



}