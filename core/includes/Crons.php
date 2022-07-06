<?php

namespace HwpClub\core\includes;

class Crons
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
        $this->schedule();
        add_action( 'hamyar_club_activity_remainder' ,[$this ,'activityRemainder'] );
        add_action( 'hamyar_club_activity_remainder' ,[$this ,'removeUserFromChatroom'] );
    }


    protected function schedule()
    {
        if ( !wp_next_scheduled('hamyar_club_activity_remainder' ) ) {
            wp_schedule_event( time() , 'daily', 'hamyar_club_activity_remainder' );
        }
    }


    public function activityRemainder()
    {
        $activities = Forms::getUsersByTheirActivity();
        if ( !empty( $activities ) ){
            $mobiles = Users::getBulkMobile( array_column( $activities , 'user_id' ) );
            $titles  = Challenges::getBulkChallengesTitle( array_column( $activities , 'challenge_id' ) );
            $meta    = Challenges::getChallengesMeta( array_column( $activities , 'challenge_id' ) );
            foreach ( $activities as $activity ) {
                $mobile = Functions::indexChecker( $mobiles ,(int)$activity->user_id ,false );
                if ( $mobile ){
                    $title = Functions::indexChecker( $titles ,$activity->challenge_id ,false );
                    if ( $title && NotifAndNote::calculateSendNotification( $meta ,$activity ) ){
                        do_action( 'hamyar_club_notification_item' ,$activity->user_id ,$mobile ,$activity->challenge_id ,$title );
                    }
                }
            }
        }
    }


    public function removeUserFromChatroom()
    {
        global $wpdb;
        $challenges=$wpdb->get_results("select post_id from {$wpdb->postmeta} where meta_key = '_chat_room_length' and meta_value <> '0'",ARRAY_A);
        $challenges=array_column($challenges,'post_id');
        foreach ($challenges as $challenge){
            $chatroom_id=get_post_meta( $challenge ,'chatroom_id' ,true );
            if(empty($chatroom_id)){
                continue;
            }
            $chat_room_length=get_post_meta( $challenge ,'_chat_room_length' ,true );
            $users=get_post_meta($challenge,'challenge_registered_users',true);
            $users=maybe_unserialize($users);
            if(!is_array($users) || empty($users)){
                continue;
            }

            foreach ($users as $user => $time){
                $end_time=$time+((int)$chat_room_length*24*60*60);
                if ( $time>0 && time() > $end_time &&  time() < ($end_time + 60*60*24) ){
                   Chatroom::updateUserChatroom($user,$chatroom_id);
                }
            }
        }
    }



}


