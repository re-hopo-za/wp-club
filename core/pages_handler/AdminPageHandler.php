<?php

namespace HwpClub\core\pages_handler;

use HwpClub\core\includes\Points;
use HwpClub\core\includes\Ranks;
use HwpClub\core\includes\Users;
use HwpClub\resources\ui\FrontPartials;

class AdminPageHandler
{


    public static function adminPage( $userObject )
    {
        return
            self::lastUsersRegisteredInRank( $userObject );
    }


    public static function lastUsersRegisteredInRank()
    {
        $list = '';
        foreach ( Ranks::rankList() as $rank => $score ){
            $users_list = Ranks::lastRank( $rank ,6 );
            if (!empty( $users_list ) ){
                $users = '';
                foreach ( $users_list as $user ){
                    $userObject  = get_user_by( 'id' ,$user->user_id );
                    $userDetails = Points::getUserDetails( $userObject->ID );
                    $profile     = str_replace( '[name]'    ,$userObject->display_name ,FrontPartials::lastUsersRegisteredInRankItem() );
                    $profile     = str_replace( '[avatar]'  ,Users::getUserAvatar( $userObject->ID ,$userObject->user_email ) ,$profile );
                    $profile     = str_replace( '[user-id]' ,$userObject->ID   ,$profile );
                    $users      .= str_replace( '[score]'   ,$userDetails->has ,$profile );
                }
                $ui     = str_replace( '[rank]' ,$rank ,FrontPartials::lastUsersRegisteredInRankUl() );
                $list  .= str_replace( '[items]' ,$users ,$ui );
            }
        }
        return str_replace( '[items]' ,$list ,FrontPartials::lastUsersRegisteredInRank() );
    }
}