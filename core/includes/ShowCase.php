<?php

namespace HwpClub\core\includes;

use mysqli;

class ShowCase
{



    public static $mate = [];

    protected static $_instance = null;
    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    public static function single( $userObject ,$childPage )
    {
        $user_points = Points::getUserDetails( $userObject->ID );
        if ( !empty( $user_points ) ){
            if ( $childPage == 'rank' ){
                return self::getCloseFriendsByRank( $userObject->ID ,$user_points->rank ,100 );
            }
            elseif ( $childPage == 'score' ){
                return self::getCloseFriendsByScore( $userObject->ID ,$user_points->has );
            }
            elseif ( $childPage == 'challenge' ){
                $user_challenges = Challenges::getUserChallengeMeta( $userObject->ID );
                if ( isset( $user_challenges->active ) && !empty( $user_challenges->active ) ){
                    return self::getChallengeMateList( $userObject->ID ,$user_challenges->active );
                }
            }
        }
        return '';
    }


    public static function getCloseFriendsByRank( $userID ,$rank ,$limit = 20 )
    {
        global $wpdb;
        $results = $wpdb->get_results(
            "SELECT users.* ,meta.meta_value AS meta FROM {$wpdb->users} AS users 
                    INNER JOIN {$wpdb->usermeta} AS meta ON users.ID = meta.user_id 
                    WHERE meta_key = 'user_point' AND JSON_EXTRACT( meta_value, '$.rank') = '{$rank}' AND users.ID <> {$userID} LIMIT {$limit};"
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ){
            return $results;
        }
        return [];
    }

    public static function getCloseFriendsByScore( $userID ,$score ,$limit = 100 )
    {
        global $wpdb;
        $min = abs( $score - 50 );
        $max = abs( $score + 50 );
        $results = $wpdb->get_results(
            "SELECT users.* ,meta.meta_value AS meta FROM {$wpdb->users} AS users 
                    INNER JOIN {$wpdb->usermeta} AS meta ON users.ID = meta.user_id 
                    WHERE meta_key = 'user_point' AND JSON_EXTRACT( meta_value, '$.has') BETWEEN {$min} AND {$max} AND users.ID <> {$userID} LIMIT {$limit}"
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ){
            return $results;
        }
        return [];
    }


    public static function getChallengeMateList( $userID ,$actives ,$page = 0 )
    {
        $users = self::closeFriendsByChallenges( $userID ,$page );
        if ( !empty( $users ) ){
            foreach ( $users as $user ){
                if ( count( array_intersect( $actives ,json_decode( $user->meta )->active ) ) ){
                    self::$mate[] = $user;
                }
            }
            if ( count( self::$mate ) > 5 ){
                return self::$mate;
            }else{
                self::getChallengeMateList( $userID ,$actives ,$page + 500 );
            }
        }
        return self::$mate;
    }



    public static function closeFriendsByChallenges( $userID ,$page )
    {
        global $wpdb;
        $results = $wpdb->get_results(
            "SELECT users.* ,meta.meta_value AS meta FROM {$wpdb->users} AS users 
                    INNER JOIN {$wpdb->usermeta} AS meta ON users.ID = meta.user_id 
                    WHERE meta_key = 'hwp_challenges_list' AND JSON_LENGTH ( JSON_EXTRACT( meta_value, '$.active') )
                    AND users.ID <> {$userID} LIMIT 500 OFFSET {$page};"
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ){
            return $results;
        }
        return [];
    }


    public static function getActiveUsers( $limit = 100 )
    {
        global $wpdb;
        $results = $wpdb->get_results(
            "SELECT users.* ,meta.meta_value AS meta FROM {$wpdb->users} AS users 
                    INNER JOIN {$wpdb->usermeta} AS meta ON users.ID = meta.user_id 
                    WHERE meta_key = 'hwp_challenges_list' ORDER BY JSON_LENGTH ( JSON_EXTRACT( meta_value, '$.active') ) DESC limit {$limit};"
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ){
            return $results;
        }
        return [];
    }


    public static function activeUsersByRankMate( $rank ,$limit = 20 )
    {
        global $wpdb;
        $results = $wpdb->get_results(
            "SELECT users.* ,meta.meta_value AS meta FROM {$wpdb->users} AS users 
                    INNER JOIN {$wpdb->usermeta} AS meta ON users.ID = meta.user_id 
                    WHERE meta_key = 'user_point' AND JSON_EXTRACT( meta_value, '$.rank') = '{$rank}'
                    ORDER BY JSON_EXTRACT( meta_value, '$.reach') DESC  LIMIT {$limit};"
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ){
            return $results;
        }
        return [];
    }


    public static function newUsersOnChallengeMate( $rank ,$limit = 20 )
    {
        global $wpdb;
        $rank_table = $wpdb->prefix . 'club_rank_list_history';
        $results = $wpdb->get_results(
            "SELECT users.* FROM {$wpdb->users} AS users 
                    INNER JOIN {$rank_table} AS meta ON users.ID = meta.user_id 
                    WHERE meta.rank = '{$rank}' ORDER BY ID DESC LIMIT {$limit};"
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ){
            return $results;
        }
        return [];
    }









}