<?php

namespace HwpClub\core\pages_handler;

use HwpClub\core\includes\Challenges;
use HwpClub\core\includes\Points;
use HwpClub\core\includes\Ranks;
use HwpClub\core\includes\ShowCase;
use HwpClub\core\includes\Users;
use HwpClub\core\includes\Watching;
use HwpClub\resources\ui\FrontPartials;

class MiscPageHandler
{



    public static function usePointsMethods( $userObject )
    {
        $ui = str_replace( '[profile]' ,HomeHandler::profileWidget( $userObject ) ,FrontPartials::usePointsMethods() );

        $haji_challenge       = Challenges::singleChallenge( 5980 );
        $consulting_challenge = Challenges::singleChallenge( 5981 );
        $hamyar_challenge     = Challenges::singleChallenge( 5983 );

        if ( in_array( 5980 ,Challenges::mergeChallenges( $userObject ) ) ){
            $ui = str_replace( '[request-haji-link]' ,home_url('club/challenge-single/5980') ,$ui );
            $ui = str_replace( '[request-haji-btn]'  ,'registered',$ui );
            $ui = str_replace( '[request-haji-text]' ,'مشاهده' ,$ui );
        }elseif ( $userObject->point->has >= $haji_challenge->meta['challenge_cost_point'] ){
            $ui = str_replace( '[request-haji-link]' ,home_url('club/challenge-single/5980') ,$ui );
            $ui = str_replace( '[request-haji-btn]'  ,'allow-register',$ui );
            $ui = str_replace( '[request-haji-text]' ,'ثبت درخواست' ,$ui );
        }else{
            $ui = str_replace( '[request-haji-link]' ,'javascript:void(0)' ,$ui );
            $ui = str_replace( '[request-haji-btn]'  ,'not-allow-register',$ui );
            $ui = str_replace( '[request-haji-text]' ,'کمبود موجودی' ,$ui );
        }
        if ( in_array( 5981 ,Challenges::mergeChallenges( $userObject ) ) ){
            $ui = str_replace( '[request-consulting-link]' ,home_url('club/challenge-single/5981') ,$ui );
            $ui = str_replace( '[request-consulting-btn]'  ,'registered',$ui );
            $ui = str_replace( '[request-consulting-text]' ,'مشاهده' ,$ui );
        }elseif ( $userObject->point->has >= $consulting_challenge->meta['challenge_cost_point'] ){
            $ui = str_replace( '[request-consulting-link]' ,home_url('club/challenge-single/5981') ,$ui );
            $ui = str_replace( '[request-consulting-btn]'  ,'allow-register',$ui );
            $ui = str_replace( '[request-consulting-text]' ,'ثبت درخواست' ,$ui );
        }else{
            $ui = str_replace( '[request-consulting-link]' ,'javascript:void(0)' ,$ui );
            $ui = str_replace( '[request-consulting-btn]'  ,'not-allow-register',$ui );
            $ui = str_replace( '[request-consulting-text]' ,'کمبود موجودی' ,$ui );
        }
        if ( in_array( 5983 ,Challenges::mergeChallenges( $userObject ) ) ){
            $ui = str_replace( '[request-hamyar-link]' ,home_url('club/challenge-single/5983') ,$ui );
            $ui = str_replace( '[request-hamyar-btn]'  ,'registered',$ui );
            $ui = str_replace( '[request-hamyar-text]' ,'مشاهده' ,$ui );
        }elseif ( $userObject->point->has >= $hamyar_challenge->meta['challenge_cost_point'] ){
            $ui = str_replace( '[request-hamyar-link]' ,home_url('club/challenge-single/5983') ,$ui );
            $ui = str_replace( '[request-hamyar-btn]'  ,'allow-register',$ui );
            $ui = str_replace( '[request-hamyar-text]' ,'ثبت درخواست' ,$ui );
        }else{
            $ui = str_replace( '[request-hamyar-link]' ,'javascript:void(0)' ,$ui );
            $ui = str_replace( '[request-hamyar-btn]'  ,'not-allow-register',$ui );
            $ui = str_replace( '[request-hamyar-text]' ,'کمبود موجودی' ,$ui );
        }

        $ui =  str_replace( '[request-haji-cost]'       ,ChallengesHandler::challengeCostShower( $haji_challenge->meta ) ,$ui );
        $ui =  str_replace( '[request-consulting-cost]' ,ChallengesHandler::challengeCostShower( $consulting_challenge->meta ) ,$ui );
        return str_replace( '[request-hamyar-cost]'     ,ChallengesHandler::challengeCostShower( $hamyar_challenge->meta ) ,$ui );
    }



    public static function increasePoint( $userObject )
    {
        $page = str_replace( '[profile]' ,HomeHandler::profileWidget( $userObject ) ,FrontPartials::increasePoints() );
        $page = str_replace( '[user-can-buy-products]' ,self::getUserCanBuyProduct() ,$page );
        return  str_replace( '[last-user-registered]'  ,Watching::completeCourseForIncreasePoint( $userObject ) ,$page );
    }


    public static function getUserCanBuyProduct()
    {
        $list = '';
        $products = [
            'product-1' => (object) [
                'title' => 'دوره آموزش اینستاگرام | کسب درآمد از اینستاگرام پیشرفته',
                'link'  => HAMYAR_ENDPOINT.'/course/instagram/',
                'score' => 3000000
            ],
            'product-2' => (object) [
                'title' => '۰ تا ۱۰۰ آموزش کسب و کار اینترنتی، دوره جامع وبمستران هوشمند ۲۲',
                'link'  => HAMYAR_ENDPOINT.'/course/smartwebmasters/',
                'score' => 5000000
            ],
            'product-3' => (object) [
                'title' => '۰ تا ۱۰۰ آموزش گرافیک',
                'link'  => HAMYAR_ENDPOINT.'/course/graphic/',
                'score' => 1500000
            ],
        ];
        if ( !empty( $products ) ){
            foreach ( $products as $product ){
                $page  = str_replace( '[title]' ,$product->title ,FrontPartials::userCanBuyHamyarProductsList() );
                $page  = str_replace( '[link]'  ,$product->link  ,$page );
                $list .= str_replace( '[score]' ,Points::getScoreByProductPrice( $product->score ) ,$page );
            }
        }
        return $list;
    }


    public static function showcase( $userObject )
    {
        $ui  = str_replace( '[profile]'  ,HomeHandler::profileWidget( $userObject ) ,FrontPartials::showCasePage() );
        $ui  = str_replace( '[showCaseActiveUsersByRankMate]' ,self::showCaseActiveUsersByRankMate( $userObject ) ,$ui );
        $ui  = str_replace( '[showCaseActiveUsersOnMonth]'    ,self::showCaseActiveUsersOnMonth() ,$ui );
        $ui  = str_replace( '[showCaseNewUsersOnClub]'        ,self::showCaseNewUsersOnClub( $userObject ) ,$ui );
        return str_replace( '[showCaseChallengeMate]'         ,self::showCaseChallengeMate( $userObject ) ,$ui );
    }

    public static function showCaseActiveUsersByRankMate( $userObject )
    {
        $users = ShowCase::activeUsersByRankMate( $userObject->point->rank ,5 );
        $list  = '';
        if ( !empty( $users ) ) {
            foreach ($users as $user) {
                $item  = str_replace( '[avatar]' ,Users::getUserAvatar( $user->ID ,$user->user_email ) ,FrontPartials::lastUsersRegisteredItem() );
                $list .= str_replace( '[user-link]' ,Users::getUserLink( $user->ID ) ,$item );
            }
            $ui =  str_replace('[title]' ,'فعالترین اعضای ' . Ranks::getRankTranslated($userObject->point->rank) . ' در این ماه ',FrontPartials::showCasePageItem());
            return str_replace('[items]' ,$list, $ui);

        }
        return $list;
    }

    public static function showCaseActiveUsersOnMonth()
    {
        $users = ShowCase::getActiveUsers();
        $list  = '';
        if ( !empty( $users ) ){
            foreach ( $users as $user ){
                $item  = str_replace( '[avatar]' ,Users::getUserAvatar( $user->ID ,$user->user_email ) ,FrontPartials::lastUsersRegisteredItem() );
                $list .= str_replace( '[user-link]' ,Users::getUserLink( $user->ID ) ,$item );
            }
            $ui  = str_replace('[title]', 'فعالترین اعضای کلاب در این ماه ' , FrontPartials::showCasePageItem() );
            return str_replace('[items]', $list, $ui );
        }
        return $list;
    }

    public static function showCaseNewUsersOnClub( $userObject )
    {
        $users = ShowCase::newUsersOnChallengeMate( $userObject->point->rank ,5 );
        $list  = '';
        if ( !empty( $users ) ){
            foreach ( $users as $user ){
                $item  = str_replace( '[avatar]' ,Users::getUserAvatar( $user->ID ,$user->user_email ) ,FrontPartials::lastUsersRegisteredItem() );
                $list .= str_replace( '[user-link]' ,Users::getUserLink( $user->ID ) ,$item );
            }
            $ui  = str_replace('[title]', 'جدیدترین ورودی های ' . Ranks::getRankTranslated($userObject->point->rank ) ,FrontPartials::showCasePageItemActives() );
            $ui  = str_replace('[more]' , Users::getUserLink( null ),$ui );
            return str_replace('[list]' ,str_replace('[items]', $list, $ui ) ,FrontPartials::showCaseNewUsersOnClub() );
        }
        return $list;
    }

    public static function showCaseChallengeMate( $userObject )
    {
        $users = ShowCase::getChallengeMateList( $userObject->ID ,$userObject->challenges->active );
        $list  = '';
        if ( !empty( $users ) ){
            foreach ( $users as $user ){
                $item  = str_replace( '[avatar]' ,Users::getUserAvatar( $user->ID ,$user->user_email ) ,FrontPartials::lastUsersRegisteredItem() );
                $list .= str_replace( '[user-link]' ,Users::getUserLink( $user->ID ) ,$item );
            }
            $ui  = str_replace('[title]', 'هم چالشی ' ,FrontPartials::showCasePageItem() );
            return str_replace('[list]' ,str_replace('[items]', $list, $ui ) ,FrontPartials::showCaseChallengeMate() );
        }
        return $list;
    }









}