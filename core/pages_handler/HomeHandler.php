<?php

namespace HwpClub\core\pages_handler;

use Elementor\User;
use HwpClub\core\includes\Challenges;
use HwpClub\core\includes\Points;
use HwpClub\core\includes\Ranks;
use HwpClub\core\includes\ShowCase;
use HwpClub\core\includes\Users;
use HwpClub\resources\ui\FrontPartials;

class HomeHandler
{


    public static function home( $userObject )
    {
        $home = str_replace( '[userDetails]'          ,self::profileWidget( $userObject ) ,FrontPartials::homePage() );
        $home = str_replace( '[scoreTimeline]'        ,self::rankTimeline( $userObject ) ,$home );
        $home = str_replace( '[scoreNeedToNextRank]'  ,self::scoreNeedToNextRank( $userObject ) ,$home );
        $home = str_replace( '[scoreHandlerButtons]'  ,FrontPartials::scoreHandlersButtons() ,$home );
        $home = str_replace( '[homeButtons]'          ,self::homeButtons( $userObject ) ,$home );
        $home = str_replace( '[homeAdds]'             ,self::homeAdds() ,$home );
        $home = str_replace( '[lastChallenges]'       ,self::lastChallenges( $userObject ) ,$home );
        return  str_replace( '[showCase]'             ,self::showCase( $userObject ) ,$home );

//        return $home.
//            self::expireDateOnSpendScore( $userObject ) .
    }


    public static function profileWidget( $userObject )
    {
        $profile = str_replace( '[name]'   ,$userObject->display_name ,FrontPartials::userDetails() );
        $profile = str_replace( '[avatar]' ,$userObject->avatar ,$profile );
        $profile = str_replace( '[has]'    ,$userObject->point->has ,$profile );
        $profile = str_replace( '[img]'    ,HWP_CLUB_PUBLIC_ASSETS.'/images/profile-background.png' ,$profile );
        return     str_replace( '[rank]'   ,Ranks::getRank( $userObject->point->reach )['translate'] ,$profile );
    }

    public static function rankTimeline( $userObject )
    {
        $reach = Ranks::getRankPercent( $userObject->point->reach );
        $ui  = str_replace( '[reach]'        ,$reach ,FrontPartials::scoreTimeline() );
        $ui  = str_replace( '[first-child]'  ,self::getClassName( $reach ,0 ,'first-child') ,$ui );
        $ui  = str_replace( '[second-child]' ,self::getClassName( $reach ,20 ,'second-child') ,$ui );
        $ui  = str_replace( '[third-child]'  ,self::getClassName( $reach ,50 ,'third-child') ,$ui );
        return str_replace( '[forth-child]'  ,self::getClassName( $reach ,100 ,'forth-child') ,$ui );
    }

    public static function getClassName( $hasScore ,$specificScore ,$class )
    {
        if ( $hasScore >= $specificScore ) {
            return $class;
        }
        return '';
    }


    public static function scoreNeedToNextRank( $userObject )
    {
        if ( $userObject->point->rank != 'A' ){
            $specific_score = Ranks::needScoreByRank( chr(ord( $userObject->point->rank ) - 1 ) );
            $need_score     = abs( $userObject->point->reach - $specific_score );
            return str_replace( '[need-score]' ,$need_score ,FrontPartials::scoreNeedToNextRank() );
        }
        return '';
    }


    public static function homeButtons( $userObject )
    {
        $ui  = str_replace( '[my-challenges-link]'    ,home_url('club/challenges-list?active') ,FrontPartials::homeButtons() );
        $ui  = str_replace( '[my-notifications-link]' ,home_url('club/notifications') ,$ui );
        $ui  = str_replace( '[guide-line]'            ,home_url('club/club-rules') ,$ui );
        return str_replace( '[notif-count]'          ,self::notifCount( $userObject ) ,$ui );
    }

    public static function notifCount( $userObject )
    {
        if ( !empty( $userObject->notifications ) && count( $userObject->notifications ) > 0 ){
            return str_replace( '[count]' ,count( $userObject->notifications )  ,FrontPartials::notifCountBadge() );
        }
        return '';
    }


    public static function homeAdds()
    {
        $adds = ContentPageHandler::homeAdds();
        if ( !empty( $adds ) ){
            $ui = str_replace(  '[title]'     ,$adds->post_title  ,FrontPartials::homeAdds() );
            $ui = str_replace(  '[adds-link]'  ,$adds->post_content ,$ui);
            return str_replace( '[image-url]' ,get_the_post_thumbnail_url( $adds->ID ,'full') ,$ui );
        }
        return '';
    }


    public static function lastChallenges( $userObject )
    {
        $challenges = Challenges::getChallenges();
        if ( !empty( $challenges ) ){
            $first_child  = $challenges[0];
            $second_child = $challenges[1] ?? [];
            $third_child  = $challenges[2] ?? [];
            $first_item   = $second_item = $third_item = '';
            $registered_challenges = array_merge( $userObject->challenges->active ,$userObject->challenges->completed );
            if ( count( $challenges ) > 1 ){

                $first_item = str_replace( '[url]'  ,get_the_post_thumbnail_url( $first_child->ID ) ,FrontPartials::homeLastChallengesItem() );
                $first_item = str_replace( '[alt]'  ,$first_child->post_title ,$first_item );
                if ( in_array( $first_child->ID ,$registered_challenges ) ){
                    $first_item = str_replace( '[link]' ,home_url('/club/challenge-single/'.$first_child->ID ) ,$first_item );
                }else{
                    $first_item = str_replace( '[link]' ,home_url('/club/challenges-list' ) ,$first_item );
                }
            }else{
                $third_item = str_replace( '[url]'  ,get_the_post_thumbnail_url( $first_child->ID ) ,FrontPartials::homeLastChallengesItem() );
                $third_item = str_replace( '[alt]'  ,$first_child->post_title ,$third_item );
                if ( in_array( $first_child->ID ,$registered_challenges ) ){
                    $third_item = str_replace( '[link]' ,home_url('/club/challenge-single/'.$first_child->ID ) ,$third_item );
                }else{
                    $third_item = str_replace( '[link]' ,home_url('/club/challenges-list' ) ,$third_item );
                }
            }

            if ( !empty( $second_child ) ){
                $second_item = str_replace( '[url]'  ,get_the_post_thumbnail_url( $second_child->ID ) ,FrontPartials::homeLastChallengesItem() );
                $second_item = str_replace( '[alt]'  ,$second_child->post_title ,$second_item );
                if ( in_array( $second_child->ID ,$registered_challenges ) ){
                    $second_item = str_replace( '[link]' ,home_url('/club/challenge-single/'.$second_child->ID ) ,$second_item );
                }else{
                    $second_item = str_replace( '[link]' ,home_url('/club/challenges-list' ) ,$second_item );
                }
            }

            if ( !empty( $third_child ) ){
                $third_item = str_replace( '[url]'  ,get_the_post_thumbnail_url( $third_child->ID ) ,FrontPartials::homeLastChallengesItem() );
                $third_item = str_replace( '[alt]'  ,$third_child->post_title ,$third_item );
                if ( in_array( $third_child->ID ,$registered_challenges ) ){
                    $third_item = str_replace( '[link]' ,home_url('/club/challenge-single/'.$third_child->ID ) ,$third_item );
                }else{
                    $third_item = str_replace( '[link]' ,home_url('/club/challenges-list' ) ,$third_item );
                }
            }

            $main = str_replace( '[first]'  ,$first_item  ,FrontPartials::homeLastChallenges() );
            $main = str_replace( '[second]' ,$second_item ,$main );
            return  str_replace( '[third]'  ,$third_item  ,$main );
        }
        return '';
    }


    public static function expireDateOnSpendScore( $userObject )
    {
        $items = '';
        $has_item = false;
        if ( !empty( $userObject->points  ) ){
            foreach ( $userObject->points as $point ){
                $status = self::pointDateStatus( $point->expire_date );
                if ( $status != 'green' ){
                    $item = str_replace( '[title]' ,$point->title  ,FrontPartials::expireDateOnSpendScoreItem() );
                    if ( $status== 'red' ){
                        $item   = str_replace( '[type]'  ,'red-status' ,$item );
                    }elseif ( $status == 'orange'  ){
                        $item   = str_replace( '[type]'  ,'orange-status' ,$item );
                    }elseif ( $status == 'yellow'  ){
                        $item   = str_replace( '[type]'  ,'yellow-status' ,$item );
                    }
                    $item   = str_replace( '[score]' ,$point->amount ,$item );
                    $items .= str_replace( '[date]'  ,$point->expire_date ,$item );
                    $has_item = true;
                }
            }
        }
        if ( $has_item ){
            return str_replace( '[items]' ,$items ,FrontPartials::expireDateOnSpendScore() );
        }
        return '';
    }



    public static function pointDateStatus( $date )
    {
        $last_7_days = date( 'Y-m-d H:i:s' ,strtotime('+7 days'));
        $last_5_days = date( 'Y-m-d H:i:s' ,strtotime('+5 days'));
        $last_3_days = date( 'Y-m-d H:i:s' ,strtotime('+3 days'));
        if ( $date <= $last_7_days ){
            if ( $date <= $last_3_days ){
                return 'red';
            }elseif ( $date <= $last_5_days ){
                return 'orange';
            }
            return 'yellow';
        }
        return 'green';
    }


    public static function userSingle( $userID )
    {
        if ( is_numeric( $userID ) ) {
            $user = get_user_by('id', $userID);
            if ($user) {
                $main  = str_replace( '[profile]' ,self::userSingleProfile( $user->ID ) ,FrontPartials::userDetailsPage() );
                return str_replace( '[challenges]' ,self::userSingleChallenges( $user->ID ) ,$main );
            }
        }
        return '';
    }

    public static function userSingleProfile( $userID )
    {
        $profile = '';
        if ( is_numeric( $userID ) ){
            $user = Users::getUserObject( $userID );
            if ( $user ){
                $point = Points::getUserDetails( $user->ID );
                $profile = str_replace( '[name]'   ,$user->display_name ,FrontPartials::profileSingleDetails() );
                $profile = str_replace( '[avatar]' ,$user->avatar ,$profile );
                $profile = str_replace( '[score]'  ,$point->has , $profile );
                $profile = str_replace( '[rank]'   ,Ranks::getRank( $point->has )['translate'] ,$profile );
            }
        }
        return $profile;
    }

    public static function userSingleChallenges( $userID )
    {
        $challengesU  = Challenges::getUserChallengeMeta( $userID );
        $items        = '';
        if ( !empty( $challengesU )){
            $challenges  = Challenges::getChallenges( array_merge( $challengesU->active ,$challengesU->completed ) );
            if ( !empty( $challenges ) ){
                $user_points = Points::getUserPointsWithSort( $userID );
                foreach ( $challenges as $challenge ){
                    $item   = str_replace( '[title]'  ,$challenge->post_title ,FrontPartials::profileChallengeListItem() );
                    $item   = str_replace( '[background]' ,get_the_post_thumbnail_url( $challenge->ID,'full') ,$item );
                    $item   = str_replace( '[date]'   ,self::getUserPointDate( $user_points ,$challenge->ID ) ,$item );
                    $item   = str_replace( '[amount]' ,self::getUserPointScore( $user_points ,$challenge->ID ) ,$item );
                    $items .= str_replace( '[url]'   ,home_url('/club/challenge-single/'.$challenge->ID ) ,$item );
                }
            }
        }
        return $items;
    }


    public static function getUserPointDate( $point ,$challengeID )
    {
        if ( isset( $point[$challengeID] ) ){
            return date( 'Y-m-d' ,strtotime( $point[$challengeID]->created_at ) );
        }
        return '';
    }


    public static function getUserPointScore( $point ,$challengeID )
    {
        if ( isset( $point[$challengeID] ) ){
            return $point[$challengeID]->amount;
        }
        return '';
    }


    public static function showCase( $userObject )
    {
        if ( isset( $userObject->ID ) ){
            $show_case = str_replace( '[activeUsers]'   ,self::activeUsers() ,FrontPartials::showCase() );
            $show_case = str_replace( '[newUsers]'      ,self::scoreMateUsers( $userObject )     ,$show_case );
            $show_case = str_replace( '[challengeMate]' ,self::challengeMateUsers( $userObject )      ,$show_case );
            return       str_replace( '[rankMate]'      ,self::rankMateUsers( $userObject ),$show_case );
        }
        return '';
    }

    public static function activeUsers()
    {
        $actives = ShowCase::getActiveUsers( 5 );
        if ( !empty( $actives ) ){
            $users = '';
            foreach ( $actives as $active ){
                if ( isset( $active->ID ) || isset( $active->user_email ) ){
                    $user   = str_replace( '[avatar]'  ,Users::getUserAvatar( $active->ID ,$active->user_email ) ,FrontPartials::lastUsersRegisteredItem() );
                    $users .= str_replace( '[user-link]' ,Users::getUserLink( $active->ID ) ,$user );
                }
            }
            $ui =  str_replace( '[title]'  ,'فعالترین اعضای کلاب در این ماه' ,FrontPartials::showCaseItem('active-mates') );
            return str_replace( '[items]'  ,$users ,$ui );
        }
        return '';
    }


    public static function scoreMateUsers( $userObject )
    {
        $score_mates = ShowCase::getCloseFriendsByScore( $userObject->ID ,$userObject->point->has ,5);
        if ( !empty( $score_mates ) ){
            $users = '';
            foreach ( $score_mates as $score_mate ){
                if ( isset( $score_mate->ID ) || isset( $score_mate->user_email ) ){
                    $user  = str_replace( '[avatar]'  ,Users::getUserAvatar( $score_mate->ID ,$score_mate->user_email ) ,FrontPartials::lastUsersRegisteredItem() );
                    $users .= str_replace( '[user-link]' ,Users::getUserLink( $score_mate->ID ) ,$user );
                }
            }
            $ui =  str_replace( '[title]'  ,'هم امتیازی' ,FrontPartials::showCaseItem('score-mates') );
            return str_replace( '[items]'  ,$users ,$ui );
        }
        return '';
    }


    public static function rankMateUsers( $userObject )
    {
        $rank_mates = ShowCase::getCloseFriendsByRank(  $userObject->ID ,$userObject->point->rank ,5 );
        if ( !empty( $rank_mates ) ){
            $users = '';
            foreach ( $rank_mates as $rank_mate ){
                if ( isset( $rank_mate->ID ) || isset( $rank_mate->user_email ) ){
                    $user  = str_replace( '[avatar]'  ,Users::getUserAvatar( $rank_mate->ID ,$rank_mate->user_email ) ,FrontPartials::lastUsersRegisteredItem() );
                    $users .= str_replace( '[user-link]' ,Users::getUserLink( $rank_mate->ID ) ,$user );
                }
            }
            $ui =  str_replace( '[title]'  ,'هم رنکی ' ,FrontPartials::showCaseItem('rank-mates') );
            return str_replace( '[items]'  ,$users ,$ui );
        }
        return '';
    }


    public static function challengeMateUsers( $userObject )
    {
        if ( isset( $userObject->challenges ) && isset( $userObject->challenges->active )){
            $challenge_mates = ShowCase::getChallengeMateList( $userObject->ID ,$userObject->challenges->active );
            if ( !empty( $challenge_mates ) ){
                $challenge_mates = array_slice( $challenge_mates ,0 ,5 );
                $users = '';
                foreach ( $challenge_mates as $challenge_mate ){
                    if ( isset( $challenge_mate->ID ) || isset( $challenge_mate->user_email ) ){
                        $user   = str_replace( '[avatar]'  ,Users::getUserAvatar( $challenge_mate->ID ,$challenge_mate->user_email ) ,FrontPartials::lastUsersRegisteredItem() );
                        $users .= str_replace( '[user-link]' ,Users::getUserLink( $challenge_mate->ID ) ,$user );
                    }
                }
                $ui =  str_replace( '[title]'  ,'هم چالشی' ,FrontPartials::showCaseItem('challenge-mates') );
                return str_replace( '[items]'  ,$users ,$ui );
            }
        }
        return '';
    }







}