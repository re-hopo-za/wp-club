<?php

namespace HwpClub\core\includes;

class Ranks
{

    public static function rankList()
    {
        return [ 'A' => 1000 ,'B' => 500 ,'C' => 200 ,'D' => 0  ];
    }



    public static function lastRank( $rank ,$limit )
    {
        if ( ctype_alpha( $rank ) ) {
            global $wpdb;
            $table   = $wpdb->prefix . 'club_rank_list_history';
            $results = $wpdb->get_results(
                "SELECT * FROM {$table} WHERE `rank` = '{$rank}' ORDER BY created_at  LIMIT {$limit} "
            );
            if ( !is_wp_error( $results ) && !empty( $results ) ) {
                return $results;
            }
        }
        return [];
    }


    public static function maybeInsertRank( $userID ,$rank ,$oldData )
    {
        self::insertRank( $userID ,$rank ,$oldData );
        if ( $rank != $oldData->rank && is_numeric( $userID ) && ctype_alpha( $rank ) ){
            global $wpdb;
            $wpdb->insert($wpdb->prefix . 'club_rank_list_history', [
                'user_id' => $userID,
                'rank'    => strtoupper( $rank )
                ],
                ['%d', '%s' ]);
            if ( empty( $wpdb->last_error ) && is_numeric( $wpdb->insert_id ) ) {
                return true;
            }
        }
        return false;
    }

    public static function insertRank( $userID ,$rank )
    {
        global $wpdb;
        if ( empty( self::specificUserHasRank( $userID ) ) ){
            $wpdb->insert($wpdb->prefix . 'club_rank_list_history', [
                'user_id' => $userID,
                'rank'    => array_key_last( self::rankList() )
            ],
           ['%d' ,'%s' ]);
        }
        if ( empty( $wpdb->last_error ) && is_numeric( $wpdb->insert_id ) ) {
            return true;
        }
        return false;
    }

    public static function specificUserHasRank( $userID )
    {
        global $wpdb;
        $table   = $wpdb->prefix . 'club_rank_list_history';
        $results = $wpdb->get_results(
            "SELECT * FROM {$table} WHERE `user_id` = '{$userID}'; "
        );
        if ( empty( $wpdb->last_error ) && !empty( $results ) ) {
            return true;
        }
        return false;
    }

    public static function getRankTranslated( $rank )
    {
        switch ( $rank ){
            case $rank == 'A':
                return 'همیار‌پلاس';
            case $rank == 'B':
                return 'همیار‌تمام';
            case $rank == 'C':
                return 'همیاروند';
            case $rank == 'D':
                return 'همیارک';
            default :
                return 'همیاری';
        }
    }

    public static function needScoreByRank( $rank )
    {
        return self::rankList()[$rank];
    }


    public static function getRank( $points )
    {
        $rank_slug = ['translate' => 'همیارک' ,'slug' =>'D'];
        switch ( $points ){
            case $points >199 && $points <= 499 :
                $rank_slug = ['translate' => 'همیاروند' ,'slug' =>'C'];
                break;
            case $points > 499 && $points <= 999 :
                $rank_slug = ['translate' => 'همیار‌تمام' ,'slug' =>'B'];
                break;
            case $points > 999 :
                $rank_slug = ['translate' => 'همیار‌پلاس' ,'slug' =>'A'];
                break;
        }
       return $rank_slug;
    }


    public static function getRankPercent( $points )
    {
        if( $points > 1000 ) return 100;
        return $points/10;
    }





}