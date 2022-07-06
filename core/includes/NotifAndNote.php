<?php

namespace HwpClub\core\includes;

use DateTimeZone;
use HwpClub\core\pages_handler\NotifAndNoteHandler;

class NotifAndNote
{


    private static $params;
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
        add_action( 'wp_ajax_club_submit_note' ,[$this ,'ajaxAddNote']);
        add_action( 'wp_ajax_club_remove_note' ,[$this ,'ajaxRemoveNote']);
        add_action( 'wp_ajax_club_update_note' ,[$this ,'ajaxUpdateNote']);

        add_action('hamyar_club_notification_item' ,[$this ,'notificationItem'] ,10 ,4 );
    }


    public static function getNotes( $userID )
    {
        if ( $userID > 0 ){
            global $wpdb;
            $table   = $wpdb->prefix . 'club_notes';
            $results = $wpdb->get_results(
                "SELECT * FROM {$table} WHERE `user_id` = {$userID} "
            );
            if ( !is_wp_error( $results ) && !empty( $results ) ) {
                return $results;
            }
        }
        return [];
    }


    public static function addNote( $content ,$title ,$url ,$userID )
    {
        if ( !empty( $content ) && $userID > 0 ){
            $content = sanitize_text_field( $content );
            $title   = sanitize_text_field( $title );
            $url     = sanitize_text_field( $url );
            global $wpdb;
            $wpdb->insert( $wpdb->prefix.'club_notes' , [
                'user_id'    => $userID  ,
                'content'    => $content ,
                'title'      => $title ,
                'url'        => $url ,
                'created_at' => date('Y-m-d H:i:s')
            ],
                ['%d','%s','%s','%s','%s'] );
            if ( empty( $wpdb->last_error ) && is_numeric( $wpdb->insert_id ) ){
                return true;
            }
        }
        return false;
    }


    public static function removeNote( $noteID ,$userID )
    {
        if ( is_numeric( $noteID ) ){
            global $wpdb;
            $table = $wpdb->prefix . 'club_notes';
            $results = $wpdb->delete(
                $table ,
                ['id' => $noteID , 'user_id' => $userID ] ,
                ['%d' ,'%d']
            );
            if ( !is_wp_error( $results ) && is_numeric( $results )) {
                return true;
            }
        }
        return false;
    }


    public static function checkAccessOnNote( $userID ,$noteID )
    {
        global $wpdb;
        $table   = $wpdb->prefix . 'club_notes';
        $results = $wpdb->get_results(
            "SELECT * FROM {$table} WHERE user_id = {$userID} AND id = {$noteID} "
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ) {
            return true;
        }
        return false;
    }


    public static function updateNote( $noteID ,$userID ,$content )
    {
        if ( !empty( $content ) && $userID > 0 ){
            $content = sanitize_text_field( $content );
            global $wpdb;
            $table  = $wpdb->prefix . 'club_notes';
            $data   = [ 'content' => $content ];
            $format = [ '%s' ];
            $where  = [ 'id' => $noteID ];
            $where_format = [ '%d' ];
            $wpdb->update( $table ,$data ,$where ,$format ,$where_format );
            if ( empty( $wpdb->last_error ) && is_numeric( $wpdb->insert_id ) ){
                return true;
            }
        }
        return false;
    }


    public function ajaxAddNote()
    {
        if ( Functions::checkNonce( $_POST['nonce'] ,'club_nonce' ) ){
            $title   = Functions::indexChecker( $_POST ,'title');
            $content = Functions::indexChecker( $_POST ,'content');
            $url     = Functions::indexChecker( $_POST ,'url');
            if ( self::checkNoteLength( $content ) ){
                $status = self::addNote( $content ,$title ,$url ,get_current_user_id() );
                if ( $status ){
                    wp_send_json_success(['result' => 'saved']);
                }
            }
        }
        wp_send_json_error(['result' => 'error']);
    }


    public static function ajaxRemoveNote()
    {
        if ( Functions::checkNonce( $_POST['nonce'] ,'club_nonce' ) ) {
            $note_id = Functions::indexChecker($_POST, 'note_id');
            $user_id = get_current_user_id();
            if ( is_numeric( $note_id ) && $note_id > 0 && $user_id > 0 ) {
                $results = self::removeNote( $note_id ,$user_id );
                if ( !is_wp_error( $results ) && is_numeric( $results )) {
                    wp_send_json_success( ['result' => 'removed'] );
                }
            }
        }
        wp_send_json_error(['result' => 'error']);
    }


    public static function ajaxUpdateNote()
    {
        if ( Functions::checkNonce( $_POST['nonce'] ,'club_nonce' ) ) {
            $note_id = Functions::indexChecker($_POST, 'note_id');
            $content = Functions::indexChecker($_POST, 'content');
            $user_id = get_current_user_id();
            if ( is_numeric( $note_id ) && $note_id > 0 && $user_id > 0 && self::checkNoteLength( $content ) ) {
                if ( self::checkAccessOnNote( $user_id ,$note_id ) ){
                    $results = self::updateNote( $note_id ,$user_id ,$content );
                    if ( $results ) {
                        wp_send_json_success( ['result' => 'updated' ,'content' => NotifAndNoteHandler::notesItems( $user_id ) ] );
                    }
                }
            }
        }
        wp_send_json_error(['result' => 'error']);
    }


    public static function checkNoteLength( $note )
    {
        if ( !empty( $note ) && strlen( $note ) <= 2000 ){
            return true;
        }
        return false;
    }









    ////// Notification //////

    public static function notificationItem( $userID ,$mobile ,$challengeID ,$title )
    {
        $status = self::addToNotificationTable([
            'user_id'      => $userID      ,
            'mobile'       => $mobile      ,
            'challenge_id' => $challengeID ,
            'title'        => $title
        ],[
            '%d','%d','%d','%s' ,
        ]);
        if ( $status ){
            Forms::updateStatus(
                [ 'notification_status' => 1 ],
                ['%s'] ,
                $challengeID.$userID
            );
        }
    }


    public static function addToNotificationTable( $data ,$format )
    {
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix.'club_notifications_items',
            $data   ,
            $format
        );
        if ( !is_wp_error( $result ) && !empty( $result ) ){
            return $wpdb->insert_id;
        }
        return false;
    }


    public static function getUserNotifications( $userID )
    {
        global $wpdb;
        $table = $wpdb->prefix.'club_notifications_items';
        $results = $wpdb->get_results(
            "SELECT * FROM {$table} WHERE user_id = {$userID} AND date_read IS NULL ;"
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ){
            return $results;
        }
        return [];
    }


    public static function calculateSendNotification( $meta ,$activity )
    {
        if ( isset( $meta[$activity->challenge_id] ) && isset( $meta[$activity->challenge_id]['notification_interval'] ) && !empty( $meta[$activity->challenge_id]['notification_interval'] ) ){
            date_default_timezone_set('Asia/Tehran');
            $interval = (int) $meta[$activity->challenge_id]['notification_interval'];
            if ( $interval > 0 ){
                $date = new \DateTime();
                $date->setTimezone( new DateTimeZone('Asia/Tehran'));
                $date->setTimestamp( strtotime( $activity->date_created ) );
                $date->modify("+{$interval} hours");
                if ( $date->getTimestamp() > strtotime('now') ){
                    return false;
                }
            }
        }
        return true;
    }



    public static function updateNotification( $userID )
    {
        if ( is_numeric( $userID ) ){
            global $wpdb;
            $wpdb->update(
                $wpdb->prefix.'club_notifications_items' ,
                [ 'date_read' => current_time( 'mysql' ) ] ,
                [ 'user_id'   => $userID ]  ,
                ['%s'] ,
                [ '%d' ]
            );
            if ( is_wp_error( $wpdb ) ){
                return true;
            }
        }
        return false;
    }



}