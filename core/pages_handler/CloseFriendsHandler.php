<?php

namespace HwpClub\core\pages_handler;

use HwpClub\core\includes\ShowCase;
use HwpClub\core\includes\Users;
use HwpClub\resources\ui\FrontPartials;

class CloseFriendsHandler
{


    public static function List( $userObject ,$showTitle = true )
    {
        $main = str_replace( '[title]' , self::titleReplacer( $showTitle ) ,FrontPartials::closeFriends() );
        $main = str_replace( '[score-mate]' ,
            self::sliderHandler( ShowCase::single( $userObject ,'score' ) ,'score-mate' ) ,$main );
        $main = str_replace( '[rank-mate]' ,
            self::sliderHandler( ShowCase::single( $userObject ,'rank' ) ,'rank-mate' ) ,$main );
        return  str_replace( '[challenge-mate]' ,
            self::sliderHandler( ShowCase::single( $userObject ,'challenge' ) ,'challenge-mate' ) ,$main );
    }

    public static function titleReplacer( $show )
    {
        if ( $show ){
            return '<h5 class="title">  افراد نزدیک  </h5>';
        }
        return '';
    }
    public static function sliderHandler( $users ,$url )
    {
        if ( !empty( $users ) ) {
            $slides = '';
            foreach ( $users as $user ){
                $slide   = str_replace( '[name]'      ,$user->display_name ,FrontPartials::closeFriendsSliderItem() );
                $slide   = str_replace( '[profile]'   ,Users::getUserAvatar( $user->ID ,$user->user_email ) ,$slide );
                $slide   = str_replace( '[user-link]' ,Users::getUserLink( $user->ID ) ,$slide );
                $slide   = str_replace( '[user-name]' ,$user->meta ,$slide );
                $slides .= $slide;
            }
            $main = str_replace( '[items]'  ,$slides ,FrontPartials::closeFriendsSlider() );
            $main = str_replace( '[title]'  ,self::mateTranslate( $url ) ,$main);
            $main = str_replace( '[target]' ,$url ,$main );
            return  str_replace( '[url]'    ,$url ,$main );
        }
        return '';
    }


    public static function closeFriendSingle( $userObject ,$child )
    {
        $users  = ShowCase::single( $userObject ,self::getSingleSlug( $child ) );
        $items = '';
        if ( !empty( $list ) ){
            foreach ( $users as $user ){
                $item   = str_replace( '[name]' ,$user->display_name ,FrontPartials::closeFriendsSingleItem() );
                $item   = str_replace( '[user-link]' ,Users::getUserLink( $user->ID ) ,$item );
                $items .= str_replace( '[profile]' ,Users::getUserAvatar( $user->ID ,$user->user_email ) ,$item );
            }
        }
        $main = str_replace( '[title]' ,self::mateTranslate( $child ) ,FrontPartials::closeFriendsSingleCon() );
        return  str_replace( '[items]' ,$items ,$main );
    }

    public static function getSingleSlug( $child )
    {
        if ( !empty( $child ) ){
            $slug = explode( '-' ,$child );
            if ( isset( $slug[0] ) && in_array( $slug[0] ,['score','rank','challenge'] ) ){
                return $slug[0];
            }
        }
        return false;
    }


    public static function mateTranslate( $mate )
    {
        switch ( $mate ){
            case 'score-mate':
                return 'هم امتیازی';
            case 'rank-mate':
                return 'هم رنکی';
            case 'challenge-mate':
                return 'هم چالشی';
        }
        return '';
    }


}