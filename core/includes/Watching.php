<?php

namespace HwpClub\core\includes;

use HwpClub\resources\ui\FrontPartials;
use stdClass;

class Watching
{

    protected static $_instance = null;


    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    public static function getSpecific( $userID ,$courseID ,$lessonID )
    {
        return self::getFromDB( '
            user_id = '.$userID.' AND 
            course_id = '.$courseID.' AND 
            lesson_id = '.$lessonID
        );
    }


    public static function add( $mobile ,$courseID ,$lessonID ,$challengeID ,$duration ,$agent ,$completed = false )
    {
        $user_id = Users::userUserIDByMobile( $mobile );
        if ( self::validationInputs( $user_id ,$courseID ,$lessonID ,$challengeID ) === true && !array_key_exists( $courseID ,self::completedCoursesList( $user_id ) ) ){
            $old = self::getSpecific( $user_id ,$courseID ,$lessonID );

            Users::addToUserActiveChallengeMetaIfNotExists( $user_id ,$challengeID );
            if ( empty( $old ) ){
                self::checkCompleteChallenge( $user_id ,$courseID ,$challengeID );
                self::addToDB([
                    'user_id'      => $user_id,
                    'mobile'       => $mobile,
                    'course_id'    => $courseID,
                    'lesson_id'    => $lessonID,
                    'challenge_id' => $challengeID,
                    'completed'    => self::completedStatus( $completed ) ,
                    'viewed_list'  => self::watchedListFieldHandler( $old ,$duration ,$agent ,$completed )
                ]);
            }
            return self::updateDB( $old ,$duration ,$agent ,$completed );
        }
        return self::validationInputs( $user_id ,$courseID ,$lessonID ,$challengeID )->status;
    }


    public static function updateDB( $old ,$duration ,$agent ,$completed )
    {
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix.'club_users_watched_list'
            ,[
                'viewed_list' => self::watchedListFieldHandler( $old ,$duration ,$agent ,$completed ),
                'completed'   => self::checkCourseCompleted( $old ,$completed )
            ]
            ,[
                'id' => (int) $old[0]->id
            ]
        );
        if ( !is_wp_error( $wpdb ) && is_numeric( $result )){
            return true;
        }
        return false;
    }


    public static function getFromDB( $where )
    {
        global $wpdb;
        $table   = $wpdb->prefix.'club_users_watched_list';
        $where   = $where ? 'WHERE '.$where : '';
        $result  = $wpdb->get_results(
            "SELECT * FROM {$table} {$where} ;"
        );
        if( !is_wp_error( $result ) && !empty( $result ) ){
            return $result;
        }
        return [];
    }


    public static function addToDB( $data )
    {
        global $wpdb;
        $wpdb->insert(
            $wpdb->prefix.'club_users_watched_list' ,
            $data  ,
            ['%d','%s','%d','%d','%d','%d','%s']
        );
        if ( empty( $wpdb->last_error ) && is_numeric( $wpdb->insert_id ) ){
            return true;
        }
        return false;
    }


    public static function validationInputs( $userID ,$courseID ,$lessonID ,$challengeID )
    {
        if ( is_numeric( $userID ) ) {
            if ( is_numeric( $courseID ) && $courseID > 0 ) {
                if ( is_numeric( $lessonID ) && $lessonID > 0 ) {
                    if ( is_numeric( $challengeID ) && $challengeID > 0 ) {
                        return true;
                    }
                    return (object) ['status' => 'Challenge ID Is Not Valid'];
                }
                return (object) ['status' => 'Lesson ID Is Not Valid'];
            }
            return (object) ['status' => 'Course ID Is Not Valid'];
        }
        return (object) ['status' => 'Mobile Is Not Valid'];
    }


    public static function completedStatus( $completed )
    {
        if ( $completed ) {
            return strtotime('now' );
        }
        return '';
    }


    public static function watchedListFieldHandler( $old ,$duration ,$agent ,$completed )
    {
        date_default_timezone_set('Asia/Tehran');
        $time = strtotime('now' );
        if ( empty( $old ) ){
            return json_encode( [ $time => [
                'duration'  => $duration ,
                'agent'     => $agent  ,
                'completed' => $completed
            ] ] );
        }
        $list = json_decode( $old[0]->viewed_list );
        $list->$time = [
            'duration'  => $duration ,
            'agent'     => $agent  ,
            'completed' => $completed
        ];
        return json_encode( $list );
    }


    public static function checkCourseCompleted( $old ,$completed )
    {
        if ( $old[0]->completed != 1 ) {
            return $completed;
        }
        return 1;
    }


    public static function getSpecificCourse( $userID ,$courseID )
    {
        return self::getFromDB( '
            user_id = '.$userID.' AND 
            course_id = '.$courseID
        );
    }

    public static function all( $userID )
    {
        return self::getFromDB( '
            user_id = '.$userID
        );
    }


    public static function lastUsersActivityOnSpecificCourse( $userID ,$courseID  )
    {
        return self::getFromDB( ' 
            user_id <> '.$userID.' AND
            course_id = '.$courseID .' 
            GROUP BY user_id ORDER BY id DESC LIMIT 8'
        );
    }


    public static function checkCompleteChallenge( $userID ,$courseID ,$challengeID )
    {
        $course = self::watchingCoursesList( $challengeID );
        $count  = self::getSpecificCourse( $userID ,$courseID );
        if ( !empty( $course ) && ( count( $count ) + 1 ) >= $course->count ){
            $point = Points::getUserDetails( $userID );
            $challenge_meta = Challenges::getSingleChallengeMeta( $course->ID );
            if ( isset( $point->reach ) && isset( $challenge_meta['challenge_reach_points'] ) && (int) $challenge_meta['challenge_reach_points'] > 0 ){
                $amount  = (int) $challenge_meta['challenge_reach_points'];
                $params  = new stdClass();
                $params->title = $course->title;
                $params->challenge_id = $course->ID;
                $params->descriptions = $course->title .' تکمیل ';
                $params->rank     = Ranks::getRank( $point->reach + $amount )['slug'];
                $params->type     = 'credit';
                $params->amount   = $amount;
                $params->user_id  = $userID;
                $params->added_by = $userID;
                Points::addPoint( $userID ,$params );
                self::addToCompletedCoursesList( $userID ,$courseID );
            }
            Challenges::updateUserMetaChallenge( $userID ,Challenges::updateUserChallengeOnCompleted( $userID ,$course->ID ),false );
        }
    }




    public static function completeCourseForIncreasePoint( $userObject )
    {
        $courses  = self::watchingCoursesList();
        $all_rows = Watching::all( $userObject->ID );
        $last     = '';
        if( !empty ( $all_rows ) ){
            $items = self::watchedCoursesWrapperByCoursesID( $all_rows );
            if ( !empty( $items ) && !empty( array_intersect_key( $items ,$courses ) ) ){
                foreach ( $items as $courseID => $usersCount ){
                    $course = $courses[$courseID];
                    $last_users = Watching::lastUsersActivityOnSpecificCourse( $userObject->ID ,$courseID );
                    if ( !empty( $last_users ) ){
                        foreach ( $last_users as $user ){
                            $user_object = get_user_by('id' ,$user->user_id );
                            if ( isset( $user_object->ID ) ){
                                $item  = str_replace( '[avatar]' ,Users::getUserAvatar( $user_object->ID ,$user_object->user_email ) ,FrontPartials::lastUsersRegisteredItem() );
                                $last .= str_replace( '[user-link]' ,Users::getUserLink( $user_object->ID ) ,$item );
                            }
                        }
                    }
                    $ui = str_replace( '[title]'  ,$course->title ,FrontPartials::lastUsersRegisterOnChallenge() );
                    $ui = str_replace( '[score]'  ,50 ,$ui );
                    $ui = str_replace( '[link]'   ,$course->profile ,$ui );
                    $ui = str_replace( '[single]' ,home_url('club/guide-get-points/'),$ui );
                    return str_replace( '[users]' ,$last ,$ui );
                }
            }
        }
        return '';
    }


    public static function completedCoursesList( $userID )
    {
        $list = get_user_meta( $userID ,'completed_course_list' ,true );
        if ( !empty( $list ) ){
            return maybe_unserialize( $list );
        }
        return [];
    }


    public static function addToCompletedCoursesList( $userID ,$courseID )
    {
        $list = self::completedCoursesList( $userID );
        if ( !in_array( $courseID ,$list ) ){
            $list[ $courseID ] = strtotime( 'now' );
            update_user_meta( $userID ,'completed_course_list' ,$list );
        }
        return false;
    }


    public static function watchedCoursesWrapperByCoursesID( $allRows )
    {
        $items = [];
        if( !empty( $allRows ) ){
            foreach ( $allRows as $row ){
                if ( isset( $items[ $row->course_id ] ) ){
                    $items[ $row->course_id ] = $items[ $row->course_id ] + 1;
                }else{
                    $items[ $row->course_id ] = 0;
                }
            }
        }
        return $items;
    }


    public static function watchingCoursesList( $specific = false )
    {
        $items  = Challenges::watchingCoursesList();
        $output = [];
        if ( !empty( $items ) ){
            foreach ( $items as $item ){
                if ( $item->ID == $specific ){
                    return (object)[
                        'ID'        => $item->ID ,
                        'title'     => $item->post_title,
                        'link'      => get_post_meta( $item->ID ,'challenge_product_link' ,true ) ,
                        'count'     => get_post_meta( $item->ID ,'challenge_lessons_count' ,true ) ,
                        'poster'    => get_post_meta( $item->ID ,'_thumbnail_id' ,true ) ,
                        'profile'   => get_post_meta( $item->ID ,'challenge_profile_link' ,true ) ,
                    ];
                }
                $output[ get_post_meta( $item->ID ,'challenge_course_id' ,true ) ]  = (object)[
                    'ID'        => $item->ID ,
                    'title'     => $item->post_title,
                    'link'      => get_post_meta( $item->ID ,'challenge_product_link' ,true ) ,
                    'count'     => get_post_meta( $item->ID ,'challenge_lessons_count' ,true ) ,
                    'poster'    => get_post_meta( $item->ID ,'_thumbnail_id' ,true ) ,
                    'profile'   => get_post_meta( $item->ID ,'challenge_profile_link' ,true ) ,
                ];
            }
        }
        return $output;
    }


}