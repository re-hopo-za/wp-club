<?php

namespace HwpClub\core\pages_handler;


use DateTime;
use HwpClub\core\includes\Forms;
use HwpClub\core\includes\Challenges;
use HwpClub\core\includes\Functions;
use HwpClub\core\includes\Points;
use HwpClub\core\includes\Users;
use HwpClub\resources\ui\FrontPartials;
use HwpClub\resources\ui\Icons;


class ChallengesHandler
{
    public static function list( $userObject )
    {
        $items = Challenges::getChallenges();
        $meta  = Challenges::getChallengesMeta( array_column( $items ,'ID' ) );
        $list  = self::challengesItems( $items ,$meta ,$userObject );

        $active_items    = str_replace( '[items]' ,$list['1_active']    ,FrontPartials::challengesListContainer() );
        $other_items     = str_replace( '[items]' ,$list['2_available'] .$list['4_unavailable'] . $list['5_deactivated']  ,FrontPartials::challengesListContainer() );
        $completed_items = str_replace( '[items]' ,$list['3_completed'] ,FrontPartials::challengesListContainer() );

        $ui  = str_replace( '[other]'     ,$other_items     ,FrontPartials::challengesMenu()  );
        $ui  = str_replace( '[active]'    ,$active_items    ,$ui );
        $ui  = str_replace( '[completed]' ,$completed_items ,$ui );

        if( $list['1_active'] ){
            $ui  = str_replace( '[active-active]'    ,'active show' ,$ui );
        }elseif ( !empty( !empty( $list['2_available'] .$list['4_unavailable'] . $list['5_deactivated'] ) ) ){
            $ui  = str_replace( '[active-available]' ,'active show' ,$ui );

        }elseif ( !empty( $completed_items ) ){
            $ui  = str_replace( '[active-completed]' ,'active show' ,$ui );
        }

        $ui  = str_replace( '[active-active]'    ,'' ,$ui );
        $ui  = str_replace( '[active-available]' ,'' ,$ui );
        return str_replace( '[active-completed]' ,'' ,$ui );
    }


    public static function challengesItems( $challenges ,$postMeta ,$userObject  )
    {
        $items = self::challengesPrepareItems( $challenges ,$postMeta ,$userObject );
        $list  = [ '1_active' => '', '2_available' =>'' ,'3_completed' =>'' ,'4_unavailable' =>'' ,'5_deactivated' =>'' ,'6_wrong' => '' ];
        if ( !empty( $items ) ) {
            foreach ( $items as $status => $challenges ){
                foreach ( $challenges as $challenge ){
                    if ( self::currentUserCanSeenPrivateChallenge( $userObject ,$postMeta ,$challenge->ID ) ){
                        $meta = $postMeta[$challenge->ID];
                        $ui = str_replace( '[title]' ,$challenge->post_title ,FrontPartials::challengesListItem() );
                        $ui = str_replace( '[status]' ,self::challengeStatusTranslated( $status) ,$ui );
                        $ui = str_replace( '[action-button]' ,self::actionButtonReplacer( $status ,$challenge->ID ,$meta ,$userObject ) ,$ui );
                        $ui = str_replace( '[cost]'   ,self::challengeCostShower( $meta ) ,$ui );
                        $ui = str_replace( '[challenge-status]' ,$status ,$ui );
                        $ui = str_replace( '[challenge-id]' ,$challenge->ID ,$ui );
                        $ui = str_replace( '[content]' ,self::challengeTabCreator( $meta ,$challenge->ID ) ,$ui );
                        $ui = str_replace( '[score]' ,self::getChallengeReachPoint( $meta ),$ui );
                        $ui = str_replace( '[users-count]' ,count( Challenges::getUserCountInSpecificChallenges( $challenge->ID ) ) ,$ui );
                        $ui = str_replace( '[last-users]' ,self::lastRegisteredUsers( $meta ) ,$ui );
                        $ui = str_replace( '[register-button]' ,self::itemRegisterButton( $status ,$challenge->ID ) ,$ui );
                        $ui = str_replace( '[link]' ,home_url('/club/challenge-single/'.$challenge->ID ) ,$ui );
                        $ui = str_replace( '[progress-bar]' ,self::progressBar( $status ,$meta ,$challenge->ID ,$userObject->ID ) ,$ui );
                        $list[$status] .= str_replace( '[thumbnail]' ,self::thumbnailShower( $challenge->ID ) ,$ui );
                    }
                }
            }
        }
        return $list;
    }


    public static function currentUserCanSeenPrivateChallenge( $userObject ,$postMeta ,$challengeID )
    {
        if ( isset( $postMeta[$challengeID]['challenge_private_mode'] ) && $postMeta[$challengeID]['challenge_private_mode'] == 1 ){
            if ( in_array( $challengeID ,$userObject->challenges->active ) || in_array( $challengeID ,$userObject->challenges->completed ) ){
                return true;
            }
            return false;
        }
        return true;
    }


    public static function progressBar( $status ,$meta ,$challengeID ,$userID )
    {
        if ( $status == '1_active' ){
            $length = self::getChallengeActionLength( $meta );
            if ( $length > 0 ){
                $count = self::getUserActionCount( $challengeID ,$meta ,$userID  );
                if ( $count > 0 ){
                    $progress = str_replace( '[act-count]'   ,$count ,FrontPartials::activityProgressBar() );
                    return str_replace( '[challenge-length]' ,$meta['challenge_length'] ,$progress );
                }else{
                    return 'بدون فعالیت';
                }
            }
        }
        return '';
    }


    public static function getChallengeActionLength( $meta )
    {
        if ( isset( $meta['challenge_form_type'] ) ){
            $type = $meta['challenge_form_type'];
            if( $type == 'text' ){
                return (int) $meta['challenge_daily_text'];
            }elseif ( $type == 'audio' || $type == 'video' || $type == 'view' ){
                return (int) $meta['challenge_media_src'];
            }elseif ( $type == 'gravity_form' || $type == 'eform' ){
                return (int) $meta['challenge_length'];
            }
        }
        return 0;
    }


    public static function getUserActionCount( $challengeID ,$meta ,$userID )
    {
        $act_count = 0;
        if( in_array( $meta['challenge_form_type'] ,['text' ,'view' ,'audio' ,'video' ] ) ){
            $activity = Forms::checkUserActivityFromDB( $challengeID ,$userID );
            if( !empty( $activity ) ){
                $act_count = count( $activity );
            }
        }elseif ( $meta['challenge_form_type'] == 'gravity_form' && isset( $meta['challenge_form_id'] ) && !empty( $meta['challenge_form_id'] ) ){
            $activity = Challenges::checkFormOnDb( 'gf_entry' ,$meta['challenge_form_id'] ,$userID,false );
            if( !empty( $activity ) ){
                $act_count = count( $activity );
            }
        }elseif ( $meta['challenge_form_type'] == 'eform' && isset( $meta['challenge_form_id'] ) && !empty( $meta['challenge_form_id'] ) ){
            $activity = Challenges::checkFormOnDb( 'fsq_data' ,$meta['challenge_form_id'] ,$userID,false );
            if( !empty( $activity ) ){
                $act_count = count( $activity );
            }
        }
        return $act_count;
    }


    public static function actionButtonReplacer( $status ,$challengeID ,$postMeta ,$userObject )
    {
        if ( $status == '2_available' ){
            if ( isset( $postMeta['challenge_cost_point'] ) && $postMeta['challenge_cost_point'] > $userObject->point->has ){
                return
                    '<button class="unavailable" >
                       کمبود موجودی
                    </button>
                ';
            }else{
                return
                    '<button class="register" data-challenge-id="'.$challengeID.'">
                    ثبت نام    
                    </button>
                ';
            }

        }elseif ( $status == '1_active'  ) {
            return
                '<a class="continue" href="'.home_url('/club/challenge-single/').$challengeID.'">
                    ادامه چالش
                </a>';
        }elseif ( $status == '3_completed' ) {
            return
                '<a class="completed" href="'.home_url('/club/challenge-single/').$challengeID.'">
                   تکمیل شده 
                </a>';
        }
        return
            '<button class="unavailable" >
                 غیر فعال
            </button>
        ';
    }


    public static function getChallengeReachPoint( $meta )
    {
        if( !empty( $meta ) && isset( $meta['challenge_reach_points'] ) && $meta > 0 ){
            return (int) $meta['challenge_reach_points'];
        }
        return 0;
    }



    public static function challengeCostShower( $meta )
    {
        if ( isset( $meta['challenge_cost_point'] ) &&  $meta['challenge_cost_point'] >  0 ){
            return '<strong class="challenge-cost"><span>'.$meta['challenge_cost_point'].'</span> <span> امتیاز</span></strong>';
        }
        return '<span class="challenge-cost"> رایگان </span>';
    }


    public static function challengeSingle( $challengeID ,$userObject )
    {
        $all_meta  = Challenges::getChallengesMeta( [$challengeID] );
        $meta      = $all_meta[$challengeID];
        $challenge = Challenges::getSingleChallenge( $challengeID );

        if ( in_array( $challengeID ,[5980,5981,5983] ) && !in_array( $challengeID ,Challenges::mergeChallenges( $userObject ) ) ){
            if ( self::challengeMetaStatus( $meta ,$userObject ) && ( !isset( $meta['challenge_cost_point'] ) || $meta['challenge_cost_point'] <= $userObject->point->has )){
                $userObject->challenges->active[$challengeID] = $challengeID;
                Challenges::updateUserMetaChallenge( $userObject->ID ,$userObject->challenges,true );
                Challenges::updateChallengeUsersList( $challengeID ,$userObject->ID );
                Points::addSubtractPoint( $all_meta ,$challengeID ,$userObject );
                wp_redirect( home_url('/club/challenge-single/'.$challengeID ) );
                exit();
            }
        }elseif( !empty( $challenge ) && in_array( $challengeID ,Challenges::mergeChallenges( $userObject ) ) ){
            $ui =  str_replace( '[title]'       ,$challenge->post_title ,FrontPartials::challengeSingle() );
            $ui =  str_replace( '[get-score]'   ,self::singleHandleScore( $meta ,'challenge_reach_points' ) ,$ui );
            $ui =  str_replace( '[spend-score]' ,self::singleHandleScore( $meta ,'challenge_cost_point' ) ,$ui );
            $ui =  str_replace( '[cover]'       ,self::thumbnailShower( $challengeID ,100 ) ,$ui );
            $ui =  str_replace( '[action]'      ,self::loadSingleChallengeForm( $meta ,$challengeID ,$userObject ) ,$ui );
            $ui =  str_replace( '[tabs]'        ,self::challengeTabCreator( $meta ,$challengeID ,true ) ,$ui );
            $ui =  str_replace( '[top-button]'  ,self::singleChatTopButtonHandler( $meta ,$challengeID ) ,$ui );
            return str_replace( '[bottom-button]' ,self::singleShareButtonHandler( $meta ,$challengeID ) ,$ui );
        }else{
            $ui =  str_replace( '[get-score]'   ,self::singleHandleScore( $meta ,'challenge_reach_points' ) ,FrontPartials::notAllowSingle()  );
            $ui =  str_replace( '[spend-score]' ,self::singleHandleScore( $meta ,'challenge_cost_point' ) ,$ui );
            return str_replace( '[cover]'       ,self::thumbnailShower( $challengeID ,100 ) ,$ui );
        }
        return false;
    }


    public static function singleShareButtonHandler( $all_meta ,$challengeID )
    {
        if ( isset( $all_meta['challenge_share_status'] ) && $all_meta['challenge_share_status'] == 1 ){
            return
                '<div>
                    <a style="display: none" href="javascript:void(0)" id="single-challenge-share" class="single-challenge-share" data-challenge-id="'.$challengeID.'">  
                        اشتراک گذاری
                    </a>
                </div>  
            ';
        }
        return '';
    }


    public static function singleChatTopButtonHandler( $all_meta ,$challengeID )
    {
        $return = ' <div class="club-border-top"> ';
        if ( isset( $all_meta['_chat_room_id'] ) && !empty( $all_meta['_chat_room_id'] ) ){
            $return .=
                '<a href="'.CHAT_FRONT_ENDPOINT.'/chat/challenge-'.$challengeID.'"> 
                     '.Icons::chat().'
                     اتاق گفتگوی کاربران چالش
                </a> 
            ';
        }
        $return .=
            '<a href="'.home_url().'/club/challenge-single/'.$challengeID.'/comments"> 
                 '.Icons::optionWithDot().'
                 نظرات شرکت کنندگان
            </a> 
        ';
        return $return .'</div>';
    }


    public static function singleHandleScore( $meta ,$key )
    {
        if ( isset( $meta[$key] ) && $key == 'challenge_reach_points' ){
            return $meta[$key].' <strong> امتیاز </strong>';
        }elseif ( isset( $meta[$key] ) && $key == 'challenge_cost_point' && $meta[$key] > 0 ){
            return $meta[$key].' <strong> امتیاز </strong>';
        }elseif (isset( $meta[$key] ) && $key == 'challenge_cost_point' ){
            return '<strong class="free-cost"> رایگان </strong>';
        }
         return '';
    }


    public static function challengeSingleComments( $challengeID ,$userObject )
    {
        $comments  = Challenges::challengeComments( $challengeID );
        $items     = '';
        if ( !empty( $comments ) ){
            foreach ( $comments as $comment ){
                $item   = str_replace( '[comment-id]' ,$comment->ID ,FrontPartials::challengeCommentsItem() );
                $item   = str_replace( '[avatar]'     ,Users::getUserAvatar( $comment->user_id ,$comment->email ) ,$item );
                $item   = str_replace( '[name]'       ,Functions::indexChecker( $comment ,'name' ,'بدون نام' ) ,$item );
                $item   = str_replace( '[date]'       ,$comment->date  ,$item );
                $items .= str_replace( '[content]'    ,$comment->comment  ,$item );
            }
        }
        if ( !empty( $items ) ){
            $item = str_replace( '[comment-list]' ,$items ,FrontPartials::challengeCommentContainer() );
        }else{
            $item = str_replace( '[comment-list]' ,FrontPartials::noComment() ,FrontPartials::challengeCommentContainer() );
        }
        if ( in_array( $challengeID ,Challenges::mergeChallenges( $userObject ) ) ){
            $item = str_replace( '[comment-form]' ,FrontPartials::commentForm() ,$item );
            $item = str_replace( '[challenge-id]' ,$challengeID ,$item );
        }else{
            $item = str_replace( '[comment-form]' ,' ' ,$item );
        }
        return $item;
    }


    public static function challengeSingleUsers( $challengeID ,$userObject )
    {
        $items     = '';
        $users = Users::getBulkUsersByID( Challenges::challengeUsers( $challengeID ) );
        if ( !empty( $users ) ){
            $meta = Users::getBulkUsersMeta( array_column( $users ,'ID' ) );
            foreach ( $users as $user ){
                $user_meta = $meta[$user->ID];
                $details   = Users::getUserDetailsFromPointMeta( $user_meta );
                $item   = str_replace( '[user-id]' ,$user->ID ,FrontPartials::challengeUsersItem() );
                $item   = str_replace( '[avatar]'  ,Users::getUserAvatar( $user->ID ,$user->user_email ) ,$item );
                $item   = str_replace( '[name]'    ,$user->display_name ,$item );
                $item   = str_replace( '[score]'   ,$details->has ,$item );
                $items .= str_replace( '[rank]'    ,$details->rank ,$item );
            }
        }
        if ( empty( $items ) ){
            return '<div class="no-users">No comments yet</div>';
        }
        return '<div class="club-challenge-users-list">'. $items .'</div>';
    }


    public static function lastRegisteredUsers( $meta )
    {
        $items = '';
        if ( isset( $meta['challenge_registered_users'] ) && !empty( $meta['challenge_registered_users'] ) ){
            $last_users = maybe_unserialize( $meta['challenge_registered_users'] );
            if( is_array( $last_users ) ){
                $last_users = array_slice( array_keys( $last_users ) ,0 ,5 );
            }
            if ( !empty( $last_users ) && is_array( $last_users ) ) {
                foreach ( $last_users as $user ) {
                    $userObject = get_user_by('id' ,$user );
                    if ( isset( $userObject->ID ) ){
                        $userDetails  = Points::getUserDetails( $userObject->ID );
                        $score  = $userDetails->has ?? 0;
                        $item   = str_replace( '[avatar]' ,Users::getUserAvatar( $userObject->ID ,$userObject->user_email ) ,FrontPartials::lastUsersRegisteredItem() );
                        $items .= str_replace( '[user-link]' ,Users::getUserLink( $userObject->ID ) ,$item );
                    }
                }
                return str_replace( '[last-users]' ,$items ,FrontPartials::lastUsersRegistered() );
            }
        }
        return '';
    }


    public static function challengeTabCreator( $postMeta ,$challengeID ,$single = false )
    {
        if ( !$single || !in_array( $postMeta['challenge_form_type'] ,['eform' ,'gravity_form' ] ) ){
            $menus    = self::getAllTabsMenus( $postMeta ,$challengeID ,$single );
            $contents = self::getAllTabsContent( $postMeta ,$challengeID ,$single );
            $tabs     = str_replace( '[menus]' ,$menus ,FrontPartials::challengeTabsCon() );
            return  str_replace( '[contents]' ,$contents ,$tabs );
        }
        return '';
    }

    public static function getAllTabsMenus( $postMeta ,$challengeID ,$single )
    {
        $menus    = '';
        $handler  = 0;
        $meta_key = $single ? 'challenge_descriptions_tabs_single' : 'challenge_descriptions_tabs';
        if ( !empty( $postMeta ) ){
            foreach( $postMeta  as $key => $value ){
                if ( preg_match('#^'.$meta_key.'#', $key ) === 1  && strpos( $key ,'_title' ) ){
                    $menu   = str_replace( '[id]'     ,'challenge-tab-handler-'.$handler.$challengeID   ,FrontPartials::challengeTabsMenu() );
                    $menu   = str_replace( '[active]' ,$handler === 0 ? 'active show' : '' ,$menu );
                    $menus .= str_replace( '[title]'  ,$value ,$menu );
                    $handler++;
                }
            }
        }
        return $menus;
    }


    public static function getAllTabsContent( $postMeta ,$challengeID ,$single )
    {
        $contents = '';
        $handler  = 0;
        $meta_key = $single ? 'challenge_descriptions_tabs_single' : 'challenge_descriptions_tabs';
        if ( !empty( $postMeta ) ){
            foreach( $postMeta  as $key => $value ){
                if ( preg_match('#^'.$meta_key.'#', $key ) === 1  && strpos( $key ,'_content' ) > 0 ){
                    $content   = str_replace( '[id]'      ,'challenge-tab-handler-'.$handler.$challengeID ,FrontPartials::challengeTabsContent() );
                    $content   = str_replace( '[active]'  ,$handler === 0 ? 'active show' : '' ,$content );
                    $contents .= str_replace( '[content]' ,self::replaceChallengeContent( $value ,$postMeta ) ,$content );
                    $handler++;
                }
            }
        }
        return $contents;
    }


    public static function replaceChallengeContent( $content ,$allMeta )
    {
        if ( strpos( $content ,'[information]' ) !== false ){
            $content  = self::timePrinter( $allMeta ,'start' ,'زمان شروع ' );
            $content .= self::timePrinter( $allMeta ,'end' ,'زمان اتمام' );
            if ( isset( $allMeta['challenge_need_points'] ) && !empty( $allMeta['challenge_need_points'] ) ){
                $content .= self::showMetaIfNotEmpty( $allMeta ,'challenge_need_points' ,'میزان امتیاز لازمه');
            }
            if ( isset( $allMeta['challenge_lost_point_on_cancel'] ) && !empty( $allMeta['challenge_lost_point_on_cancel'] ) ){
                $content .= self::showMetaIfNotEmpty( $allMeta ,'challenge_lost_point_on_cancel' ,'امتیاز منفی حین انصراف');
            }
            if ( isset( $allMeta['challenge_min_user'] ) && !empty( $allMeta['challenge_min_user'] ) ){
                $content .= self::showMetaIfNotEmpty( $allMeta ,'challenge_min_user' ,'حداقل کاربر برای شروع چالش');
            }
            if ( isset( $allMeta['challenge_max_user'] ) && !empty( $allMeta['challenge_max_user'] ) ){
                $content .= self::showMetaIfNotEmpty( $allMeta ,'challenge_max_user' ,'حداکثر کاربر برای  ثبت نامی');
            }
            if ( empty( $content ) ){
                return '<div class="empty-information"> بدون مشخصات </div>';
            }
        }
        return $content;
    }



    public static function thumbnailShower( $postID ,$size = 100 )
    {
        $thumbnail = get_the_post_thumbnail( $postID ,[$size,$size]  );
        if ( !empty( $thumbnail ) ){
            return $thumbnail;
        }
        return '<img src="'.HWP_CLUB_PUBLIC_ASSETS.'images/no-thumbnail.png" alt="no-thumbnail">';
    }


    public static function timePrinter( $meta ,$index ,$title )
    {
        if ( isset( $meta['challenge_date_'.$index] ) && !empty( $meta['challenge_date_'.$index] ) ){
            $start = date_i18n( 'Y/m/d' ,strtotime( Functions::indexChecker( $meta ,'challenge_date_'.$index ) ) ) .' '.
                Functions::indexChecker( $meta,'challenge_time_'.$index );
            return
                '<p class="'.'challenge_date_'.$index.'">
                    <span>'.$title.'</span>
                    <span>'.$start.'</span>
                </p>
            ';
        }
        return '';
    }


    public static function showMetaIfNotEmpty( $meta ,$index ,$title )
    {
        if ( isset( $meta[$index] ) && !empty( $meta[$index] ) ){
            return
                '<p class="'.$index.'">
                    <span>'.$title.'</span>
                    <span>'.$meta[$index].'</span>
                </p>
            ';
        }
        return '';
    }


    public static function itemRegisterButton( $status ,$challengeID )
    {
        if ( $status == '1_active' ){
            return FrontPartials::challengeSingleButton( $challengeID );
        }elseif ( $status == '2_available' ){
            return FrontPartials::challengeRegisterButton();
        }elseif ( $status == '3_completed' ){
            return FrontPartials::challengeSingleCompleteButton( $challengeID );
        }elseif ( $status == '4_unavailable' ){
            return FrontPartials::challengeSingleStatusOnButton('غیر قابل دسترس');
        }
        return FrontPartials::challengeSingleStatusOnButton('غیر فعال' );
    }


    public static function challengeStatusTranslated( $status )
    {
        $translated = '';
        switch ( $status ){
            case  '1_active':
                $translated = ' فعال ';
                break;
            case  '2_available':
                $translated = ' قابل ثبت نام ';
                break;
            case  '3_completed':
                $translated = ' تکمیل شده';
                break;
            case  '4_unavailable':
                $translated = ' غیر قابل ثبت نام ';
                break;
            case  '5_deactivated':
                $translated = ' غیر فعال';
                break;
        }
        return $translated;
    }

    public static function challengesPrepareItems( $items ,$postMeta ,$userObject  )
    {
        $list = ['1_active' => [], '2_available' =>[] ,'3_completed' => [] ,'4_unavailable' => [] ,'5_deactivated' => [] ,'6_wrong' => [] ];
        if ( !empty( $items )) {
            foreach ( $items as $item ){
                $list[ self::challengeStatus( $item ,$postMeta ,$userObject ) ][] = $item;
            }
        }
        return $list;
    }


    public static function challengeStatus( $postObject ,$postMeta ,$userObject )
    {
        $user_challenges = $userObject->challenges;
        $user_points     = $userObject->point;
        $limit_point     = isset($postMeta[$postObject->ID]['challenge_need_points'] ) ?
            (int) $postMeta[$postObject->ID]['challenge_need_points'] : 0;
        if ( $postObject->post_status == 'publish' && isset( $user_challenges->active ) && in_array(  $postObject->ID ,$user_challenges->active ) )
        {
            return '1_active';
        }
        elseif ( isset( $user_challenges->completed ) && in_array( $postObject->ID ,$user_challenges->completed ) )
        {
            return '3_completed';
        }
        elseif (isset($postMeta[$postObject->ID]['challenge_limit_rank']) && $user_points->rank <= $postMeta[$postObject->ID]['challenge_limit_rank'] && $postObject->post_status == 'publish' && $user_points->has >= $limit_point && self::challengeMetaStatus( $postMeta[$postObject->ID] ,$userObject ) )
        {
            return '2_available';
        }
        elseif ( !self::challengeMetaStatus( $postMeta[$postObject->ID] ,$userObject ) )
        {
            return '5_deactivated';
        }
        elseif ( $user_points->has <= $limit_point ||  $user_points->rank > $postMeta[$postObject->ID]['challenge_limit_rank'] || self::challengeMetaStatus( $postMeta[$postObject->ID] ,$userObject ) )
        {
            return '4_unavailable';
        }
        return '6_wrong';
    }


    public static function challengeMetaStatus( $postMeta ,$userObject ,$submitForm = false )
    {
        $registered_users = maybe_unserialize( Functions::indexChecker( $postMeta ,'challenge_registered_users' ,[] ) );
        $user_count = 0;
        if( is_array( $registered_users ) ){
            $registered_users = array_keys( $registered_users );
            $user_count       = count( $registered_users );
        }
        if ( $submitForm &&
            ( empty( $postMeta['challenge_time_start'] ) || $postMeta['challenge_time_start'] <= date_i18n('H:i') ) &&
            ( empty( $postMeta['challenge_time_end'] ) || $postMeta['challenge_time_end'] >= date_i18n('H:i') ) &&
            ( empty( $postMeta['challenge_min_user'] ) || $postMeta['challenge_min_user'] >= $user_count ) ){
            return true;
        }else if(
            !$submitForm &&
            ( empty( $postMeta['challenge_date_start'] ) || $postMeta['challenge_date_start'] <= date_i18n('Ymd') ) &&
            ( empty( $postMeta['challenge_date_end'] ) || $postMeta['challenge_date_end'] >= date_i18n('Ymd') ) &&
            ( empty( $postMeta['challenge_max_user'] ) || $postMeta['challenge_max_user'] > $user_count ) &&
            ( empty( $postMeta['challenge_cost_point'] ) || $postMeta['challenge_cost_point'] <= $userObject->point->has ) &&
            ( empty( $postMeta['challenge_limit_rank'] ) || $postMeta['challenge_limit_rank'] >= $userObject->point->rank ) ){
            return true;
        }
        return false;
    }




    public static function banner( $allMeta ,$title )
    {
        if ( isset( $allMeta['challenge_banner'] ) && !empty( $allMeta['challenge_banner'] ) ){
            $banner = wp_get_attachment_image_src( $allMeta['challenge_banner'] )[0];
            return '<img src="'.$banner.'" alt="'.$title.'" />';
        }
        return '';
    }


    public static function loadSingleChallengeForm( $allMeta ,$challengeID ,$userObject )
    {
        $type = $allMeta['challenge_form_type'];
        if ( $type == 'gravity_form' ){
            return self::checkGravityForm( $userObject ,$challengeID ,$allMeta );
        }elseif ( $type == 'video' ){
            return self::loadVideo( $allMeta ,$challengeID ,$userObject->ID );
        }elseif ( $type == 'audio' ){
            return self::loadAudio( $allMeta ,$challengeID ,$userObject->ID );
        }elseif ( $type == 'text' ){
            return self::loadText( $allMeta ,$challengeID ,$userObject->ID );
        }elseif ( $type == 'view' ){
            return self::viewCounterScript( $challengeID ,$userObject->ID ,$allMeta['challenge_length'] );
        }elseif ( $type == 'eform' ){
            return self::checkEForm( $userObject ,$challengeID ,$allMeta );
        }
        return '';
    }


    public static function returnMediaLink(  $userActivity ,$allMeta )
    {
        $count = 0;
        $last_item = [];
        if ( !empty( $userActivity ) ){
            $count = count( $userActivity );
            $last_item = end($userActivity );
        }
        if( !empty( $userActivity ) ){
            $last_item = end($userActivity );
        }
        if ( !empty( $last_item ) && isset( $last_item->date_created ) ){
            date_default_timezone_set('Asia/Tehran');
            $date = new DateTime( $last_item->date_created );
            $date->modify('+1 day');
            if ( $date->getTimestamp() > strtotime( 'now' ) || (int) $allMeta['challenge_media_src'] <= $count  ){
                $count =(int) $count - 1 ;
            }
        }
        if ( isset( $allMeta['challenge_media_src'] ) && (int) $allMeta['challenge_media_src'] >= $count ){
            $link =  'challenge_media_src_'.$count.'_link';
            if ( isset( $allMeta[$link] ) ){
                return $allMeta[$link];
            }
        }
        return '';
    }


    public static function checkNewRequestToMediaActivity( $userActivity  )
    {
        $last_item = [];
        if( !empty( $userActivity ) ){
            $last_item = end($userActivity );
        }
        if ( !empty( $last_item ) && isset( $last_item->date_created ) ){
            date_default_timezone_set('Asia/Tehran');
            $date = new DateTime( $last_item->date_created );
            $date->modify('+1 day');
            if ( $date->getTimestamp() > strtotime( 'now' ) ){
                return false;
            }
        }
        return true;
    }

    public static function loadVideo( $allMeta ,$challengeID ,$userID )
    {
        $activity = Forms::checkUserActivityFromDB( $challengeID, $userID,false );
        $link     = self::returnMediaLink( $activity ,$allMeta );
        if ( !empty( $link ) ){
            $is_new = self::checkNewRequestToMediaActivity( $activity );
            $main   = str_replace( '[link]' ,$link  ,FrontPartials::challengeSingleVideoPlayer());
            return $main . self::videoWatcherScript( $challengeID ,$is_new );
        }
        return '';
    }


    public static function loadAudio( $allMeta ,$challengeID ,$userID )
    {
        $activity = Forms::checkUserActivityFromDB( $challengeID, $userID,false );
        $link     = self::returnMediaLink( $activity ,$allMeta );
        if ( !empty( $link ) ) {
            $is_new = self::checkNewRequestToMediaActivity( $activity );
            $main = str_replace('[link]', $link, FrontPartials::challengeSingleAudioPlayer());
            return $main . self::audioWatcherScript( $challengeID, $userID, $is_new );
        }
        return '';
    }



    public static function loadText( $allMeta ,$challengeID ,$userID )
    {
        $activity  = Forms::checkUserActivityFromDB( $challengeID, $userID,false );
        $is_new    = self::checkNewRequestToMediaActivity( $activity );
        $count     = 0;
        $last_item = [];
        $return    = '';

        if ( !empty( $activity ) ){
            $count = count( $activity );
            $last_item = end($activity );
        }

        if ( !empty( $last_item ) && isset( $last_item->date_created ) ){
            date_default_timezone_set('Asia/Tehran');
            $date = new DateTime( $last_item->date_created );
            $date->modify('+1 day');
            if ( $date->getTimestamp() > strtotime( 'now' ) || (int) $allMeta['challenge_daily_text'] <= $count ){
                $count =(int) $count - 1 ;
            }
        }
        if ( isset( $allMeta['challenge_daily_text'] ) && (int) $allMeta['challenge_daily_text'] >= $count ){
            if ( isset( $allMeta['challenge_daily_text_'.$count.'_text'] ) ){
                $return = '<p style="padding:10px"> '.$allMeta['challenge_daily_text_'.$count.'_text'].' </p>';
            }
        }

        if ( $is_new ){
            $return .= ' <script> 
                function read() {
                    const xhttp = new XMLHttpRequest(); 
                    let data = "'.admin_url( "admin-ajax.php" ).'?action=handler_user_activity&type=text&nonce='.wp_create_nonce("club_nonce").'&challenge_id='.Functions::encryptID($challengeID).'";
                    xhttp.open("GET", data , true );
                    xhttp.send();
                    xhttp.onload  = function() {
                       let jsonResponse = JSON.parse( xhttp.responseText ); 
                       if ( jsonResponse.result === "completed"){
                            iziToast.success({
                                title: "اعلان",
                                message: "این چالش به اتمام رسید",
                            }); 
                       }else if( jsonResponse.result === "recorded"){
                            iziToast.success({
                                title: "اعلان",
                                message: "فعالیت شما ذخیره شد",
                            });  
                       }else if( jsonResponse.result === "n_effected"){
                             iziToast.error({
                                title: "خطا",
                                message: "خطا هنگام ذخیره اطلاعات",
                            });   
                        } 
                    }; 
                }
                read();
                </script>
            ';
        }
        return $return;
    }



    public static function loadGravity( $gravityID )
    {
        if ( is_numeric( $gravityID ) ){
            return do_shortcode('[gravityform id="'.$gravityID.'" title="false" description="true" ajax="true"]' );
        }
        return '';
    }


    public static function loadEForm( $eFormID )
    {
        if ( is_numeric( $eFormID ) ){
            return do_shortcode('[ipt_fsqm_form id="'.$eFormID.'" ]' );
        }
        return '';
    }




    public static function checkGravityForm( $userObject ,$challengeID ,$meta )
    {
        if ( in_array( $challengeID ,$userObject->challenges->completed ) ){
            return 'چالش به اتمام رسیده است';
        }elseif ( isset( $meta['challenge_form_id'] ) && is_numeric( $meta['challenge_form_id'] ) ){
            if ( !empty( Challenges::checkFormOnDb( 'gf_entry' ,$meta['challenge_form_id'] ,$userObject->ID  ) ) ){
                if ( isset( $meta['challenge_alert_text'] ) && !empty( $meta['challenge_alert_text'] ) ){
                    return $meta['challenge_alert_text'];
                }
                return 'زمان انجام فعالیت فرا نرسیده است';
            }
            return self::loadGravity( $meta['challenge_form_id'] );
        }
        return 'فرم یافت نشد';
    }


    public static function checkEForm( $userObject ,$challengeID ,$meta )
    {
        if ( in_array( $challengeID ,$userObject->challenges->completed ) ){
            return 'چالش به اتمام رسیده است';
        }elseif ( isset( $meta['challenge_form_id'] ) && is_numeric( $meta['challenge_form_id'] ) ){
            return self::loadEForm( $meta['challenge_form_id'] );

            if ( !empty( Challenges::checkFormOnDb( 'fsq_data' ,$meta['challenge_form_id'] ,$userObject->ID  ) ) ){
                if ( isset( $meta['challenge_alert_text'] ) && !empty( $meta['challenge_alert_text'] ) ){
                    return $meta['challenge_alert_text'];
                }
                return 'زمان انجام فعالیت فرا نرسیده است';
            }
        }
        return 'فرم یافت نشد';
    }


    public static function videoWatcherScript( $challengeID ,$newRequest )
    {
        $return =
            '<script>
                let video  = document.getElementById("hwp-club-video-watcher"); 
                let play_pause = document.getElementById("video-handler");  
                let video_reset = document.getElementById("video-reset");  
                let interval = 0;
                let setInterVal = 0;
                let is_saved = true;
                video_reset.addEventListener("click", function() {   
                        video.pause();
                        video.currentTime = 0;
                        play_pause.innerHTML = "play"; 
                        clearInterVal();
                        interval = 0;
                });     
                play_pause.addEventListener("click", function() {  
                    if (video.paused) {  
                        video.play(); 
                        setInterValFunc();
                        play_pause.innerHTML = "pause"; 
                    } else { 
                        clearInterVal();
                        video.pause(); 
                        play_pause.innerHTML = "start"; 
                    }
                });     
                video.addEventListener("ended", function (){
                    clearInterval( setInterVal );    
                    play_pause.innerHTML = "start";
                    if ( video.duration <= interval ){
                         read();
                    }
                }); 
                function setInterValFunc(){
                    setInterVal = setInterval(function() {     
                      interval += 1; 
                  }, 10 ); 
                } 
                function clearInterVal(){
                    clearInterval(setInterVal);  
                } 
                
                function read() { 
                    if ( is_saved ){ 
            ';
            if ( $newRequest ){
                $return .= ' 
                    const xhttp = new XMLHttpRequest(); 
                    let data = "'.admin_url( "admin-ajax.php" ).'?action=handler_user_activity&type=media&nonce='.wp_create_nonce("club_nonce").'&challenge_id='.Functions::encryptID($challengeID).'";
                    xhttp.open("GET", data , true );
                    xhttp.onload  = function() {
                       let jsonResponse = JSON.parse( xhttp.responseText ); 
                       if ( jsonResponse.result === "completed"){
                            iziToast.success({
                                title: "اعلان",
                                message: "این چالش به اتمام رسید",
                            }); 
                       }else if( jsonResponse.result === "recorded"){
                            iziToast.success({
                                title: "اعلان",
                                message: "فعالیت شما ذخیره شد",
                            });  
                       }else if( jsonResponse.result === "n_effected"){
                             iziToast.error({
                                title: "خطا",
                                message: "خطا هنگام ذخیره اطلاعات",
                            });   
                       }  
                    }; 
                    xhttp.send();
               ';
            }
            $return .= 'is_saved = false; } } </script> ';
        return  $return;
    }



    public static function audioWatcherScript( $challengeID ,$userID ,$new_request )
    {
        $return =
            '<script>
                let audio  = document.getElementById("hwp-club-audio-watcher"); 
                let play_pause = document.getElementById("audio-handler");  
                let audio_reset = document.getElementById("audio-reset");  
                let interval = 0;
                let setInterVal =0;
                audio_reset.addEventListener("click", function() {   
                        audio.pause();
                        audio.currentTime = 0;
                        play_pause.innerHTML = "play"; 
                        clearInterVal();
                        interval = 0;
                });     
                play_pause.addEventListener("click", function() {  
                    if (audio.paused) {  
                        audio.play(); 
                        setInterValFunc();
                        play_pause.innerHTML = "pause"; 
                    } else { 
                        clearInterVal();
                        audio.pause(); 
                        play_pause.innerHTML = "start"; 
                    }
                });     
                audio.addEventListener("ended", function (){
                    clearInterval( setInterVal );    
                    play_pause.innerHTML = "start";
                    if ( audio.duration <= interval ){
                         read();
                    }
                }); 
                function setInterValFunc(){
                    setInterVal = setInterval(function() {     
                      interval += 1; 
                  }, 10 ); 
                } 
                function clearInterVal(){
                    clearInterval(setInterVal);  
                }  
                function read() { 
            ';

        if ( $new_request ){
            $return .= ' 
                const xhttp = new XMLHttpRequest(); 
                let data = "'.admin_url( "admin-ajax.php" ).'?action=handler_user_activity&nonce='.wp_create_nonce("club_nonce").'&challenge_id='.Functions::encryptID($challengeID).'";
                xhttp.open("GET", data , true );
                xhttp.onload  = function() {
                    let jsonResponse = JSON.parse( xhttp.responseText ); 
                    if ( jsonResponse.result === "completed"){
                        iziToast.success({
                            title: "اعلان",
                            message: "این چالش به اتمام رسید",
                        }); 
                    }else if( jsonResponse.result === "recorded"){
                        iziToast.success({
                            title: "اعلان",
                            message: "فعالیت شما ذخیره شد",
                        });  
                    }else if( jsonResponse.result === "n_effected"){
                         iziToast.error({
                            title: "خطا",
                            message: "خطا هنگام ذخیره اطلاعات",
                        });   
                    }   
                }; 
                xhttp.send(); 
            ';
        }
        return $return .= ' } </script> ';
    }



    public static function viewCounterScript( $challengeID ,$userID ,$challengeLength )
    {
        if ( count( Forms::checkUserActivityFromDB( $challengeID ,$userID ) ) <= $challengeLength ){
            return ' 
            <script> 
                function read() { 
                    const xhttp = new XMLHttpRequest(); 
                    let data = "'.admin_url( "admin-ajax.php" ).'?action=handler_user_activity&type=view&nonce='.wp_create_nonce("club_nonce").'&challenge_id='.Functions::encryptID($challengeID).'";
                    xhttp.open("GET", data , true );
                    xhttp.onload  = function() {
                    let jsonResponse = JSON.parse( xhttp.responseText ); 
                    if ( jsonResponse.result === "completed"){
                        iziToast.success({
                            title: "اعلان",
                            message: "این چالش به اتمام رسید",
                        }); 
                    }else if( jsonResponse.result === "recorded"){
                        iziToast.success({
                            title: "اعلان",
                            message: "فعالیت شما ذخیره شد",
                        });  
                    }else if( jsonResponse.result === "n_effected"){
                         iziToast.error({
                            title: "خطا",
                            message: "خطا هنگام ذخیره اطلاعات",
                        });   
                    }    
                    xhttp.send(); 
                }
                read();
            </script> ';
        }
        return '';
    }



    public static function descriptionsButton( $challengeID )
    {
        $post_meta = Challenges::getSingleChallengeMeta( $challengeID );
        $items = '';
        if (!empty( $post_meta ) ) {
            foreach ( $post_meta as $key => $value ) {
                if ( preg_match('#^challenge_eform_result_description#', $key ) === 1 && strpos( $key, '_title' ) ) {
                    $item = str_replace('[title]'   ,$value, FrontPartials::challengeFormDescriptionsButton() );
                    $item = str_replace('[content]' ,self::getDescriptionsButtonContent( $post_meta ,$key ) ,$item );
                    $items .= $item;
                }
            }
        }
        return $items;
    }



    public static function getDescriptionsButtonContent( $postMeta ,$metaKey )
    {
        $number = (int) filter_var( $metaKey, FILTER_SANITIZE_NUMBER_INT );
        if ( is_numeric( $number ) ){
            foreach( $postMeta  as $key => $value ){
                if ( preg_match('#^challenge_eform_result_description_'.$number.'#', $key ) === 1  && strpos( $key ,'_content' ) > 0 ){
                    return $value;
                }
            }
        }
        return '';
    }



    public static function challengeSlider( $userID )
    {
        $sliders = [ 'actives' => '' ,'completed' => '' ];
        $list    = Challenges::getUserChallengeMeta( $userID );

        if ( !empty( $list ) ){
            if ( !empty( $list->active ) ){
                $challenges  = Challenges::getChallenges( $list->active );
                if ( !empty( $challenges ) ){
                    foreach ( $challenges as $challenge ){
                        $slide_items = str_replace( '[title]'     ,$challenge->post_title ,FrontPartials::challengesSliderItem() );
                        $slide_items = str_replace( '[excerpt]'   ,$challenge->post_excerpt ,$slide_items );
                        $slide_items = str_replace( '[thumbnail]' ,get_the_post_thumbnail_url( $challenge->ID ,'full')  ,$slide_items );
                        $sliders['actives']  .= str_replace( '[link]'    ,home_url('/club/challenge-single/'.$challenge->ID ) ,$slide_items );
                    }
                }
            }

            if ( !empty( $list->completed ) ){
                $challenges  = Challenges::getChallenges( $list->completed );
                if ( !empty( $challenges ) ){
                    foreach ( $challenges as $challenge ){
                        $slide_items = str_replace( '[title]'   ,$challenge->post_title ,FrontPartials::challengesSliderItem() );
                        $slide_items = str_replace( '[excerpt]' ,$challenge->post_excerpt ,$slide_items );
                        $slide_items = str_replace( '[thumbnail]' ,get_the_post_thumbnail_url( $challenge->ID ,'full')  ,$slide_items );
                        $sliders['completed']  .= str_replace( '[link]'    ,home_url('/club/challenge-single/'.$challenge->ID ) ,$slide_items );
                    }
                }
            }
        }
        return  $sliders;
    }



}
