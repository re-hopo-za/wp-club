<?php

namespace HwpClub\core\includes;


use HwpClub\resources\ui\FrontPartials;
use stdClass;

class Users
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
        add_action( 'manage_users_columns'         ,[$this ,'addFieldTitleToUserTable'] ,10 ,1 );
        add_action( 'manage_users_custom_column'   ,[$this ,'addFieldValueToUserTable'] ,10 ,3 );
        add_action( 'wp_ajax_club_add_user_point'  ,[$this ,'addPointManually']);
        add_action( 'wp_ajax_club_get_user_points' ,[$this ,'getUsersPointsAdmin']);
        add_action( 'wp_login'                     ,[$this ,'loginUser'] ,10 ,2 );
        add_action( 'init'                         ,[$this ,'resetChatLoginToken']);
    }

    public function resetChatLoginToken(){
        if($_SERVER['REQUEST_URI']=='/login/?chat_login=true' && is_user_logged_in()){
            self::generageToken(get_current_user_id());
            Chatroom::updateUserChatroom( get_current_user_id() );
            wp_redirect(FrontPartials::chat(get_current_user()));
        }
    }

    /**
     * check user first point on every login attempt
     * @param $user_login
     * @param $user
     * @return void
     */
    public function loginUser($user_login,$user)
    {
        $mobile=get_user_meta($user->ID,hf_user_mobile_meta_key(),true);
        Points::getAllPurchasedList( $mobile ,$user->ID);
    }

    public static function getUserObject( $currentUserID = true )
    {
        $user_id  = $currentUserID === true ? get_current_user_id() : $currentUserID;
        $user_row = self::userRow( $user_id );
        if ( $user_id > 0 && !empty( $user_row ) ){
            return (object) array_merge(
                ['ID'                => $user_id] ,
                ['first_name'        => $user_row->first_name]   ,
                ['last_name'         => $user_row->last_name]    ,
                ['username'          => $user_row->user_login]   ,
                ['display_name'      => $user_row->display_name] ,
                ['user_email'        => $user_row->user_email]   ,
                ['notifications'     => NotifAndNote::getUserNotifications( $user_id ) ] ,
                ['points'            => Points::getUserPoints( $user_id ) ],
                ['point'             => Points::getUserDetails( $user_id ) ],
                ['challenges'        => Challenges::getUserChallengeMetaForGlobal( $user_id ) ],
                ['avatar'            => self::getUserAvatar( $user_row->ID ,$user_row->user_email ) ] ,
                ['mobile'            => self::userMobile( $user_id ) ] ,
                ['start_score'       => self::firstScore( $user_id ) ],
                ['biography'         => self::userBiography( $user_id ) ],
                ['private'           => self::accountPrivate( $user_id ) ],
                ['token'             => self::getToken( $user_id ) ] ,
                ['completed_courses' => Watching::completedCoursesList( $user_id ) ] ,
            );
        }
        return false;
    }

    public static function userRow( $userID )
    {
        $user = get_user_by( 'id' ,$userID );
        if ( !empty( $user ) ){
            return $user;
        }
        return false;
    }

    public static function userMobile( $userID )
    {
        $mobile = get_user_meta( $userID ,'force_verified_mobile' ,true );
        if ( !empty( $mobile ) ){
            return $mobile;
        }
        return '';
    }

    public static function userBiography( $userID )
    {
        $bio = get_user_meta( $userID ,'biography' ,true );
        if ( !empty( $bio ) ){
            return $bio;
        }
        return '';
    }

    public static function firstScore( $userID )
    {
        $start_score = get_user_meta( $userID ,'first_score' ,true );
        if ( !empty( $start_score ) ){
            return true;
        }
        return false;
    }

    public static function accountPrivate( $userID )
    {
        $private = get_user_meta( $userID ,'account_private' ,true );
        if ( !empty( $private ) ){
            return true;
        }
        return false;
    }

    public static function userUserIDByMobile( $mobile )
    {
        global $wpdb;
        $results = $wpdb->get_var(
            "SELECT ID FROM {$wpdb->users} AS users
                   INNER JOIN {$wpdb->usermeta} AS meta ON users.ID = meta.user_id
                   WHERE meta.meta_key = 'force_verified_mobile' AND meta.meta_value = '{$mobile}' LIMIT 1;"
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ){
            return $results;
        }
        return [];
    }


    public static function getToken( $userID )
    {
        $token  = get_user_meta( $userID ,'club_user_token' ,true );
        if ( empty( $token ) ){
            $token = bin2hex( random_bytes( 41 ) );
            update_user_meta( $userID ,'club_user_token', $token );
        }
        return $token;
    }

    private static function generageToken($userID){
        $token = bin2hex( random_bytes( 41 ) );
        update_user_meta( $userID ,'club_user_token', $token );
        Chatroom::updateUserChatroom( $userID );
    }


    public function addFieldTitleToUserTable( $column ){
        $column['score']   = esc_html__('Score','hamyarClub');
        $column['rank']    = esc_html__('Rank','hamyarClub');
        $column['actions'] = esc_html__('Actions','hamyarClub');
        $column['points']  = esc_html__('Points','hamyarClub');
        return $column;
    }


    public function addFieldValueToUserTable( $value ,$columnName ,$userID ){
        switch ( $columnName ) {
            case 'score' :
                return self::getScore( $userID );
            case 'rank' :
                return self::getRank( $userID );
            case 'actions' :
                return self::actions( $userID );
            case 'points' :
                return self::points( $userID );
        }
        return $value;
    }


    public static function getScore( $userID )
    {
        $info = Points::getUserDetails( $userID );
        if (is_numeric( $info->reach ) ){
            return $info->reach;
        }
        return '-';
    }


    public static function getRank( $userID )
    {
        $info = Points::getUserDetails( $userID );
        if ( !empty( $info->rank ) ){
            return $info->rank;
        }
        return 'D';
    }


    public static function actions( $userID )
    {
        return '
            <div class="user-action-con" data-user-id="'.$userID.'">
                <div class="increase-credit">
                    <span class="dashicons dashicons-plus"></span>
                </div>
                 <p> اعتبار </p>
                <div class="decrease-credit">
                    <span class="dashicons dashicons-minus"></span>
                </div>
            </div>
            <div class="user-action-handler-con handler-con-'.$userID.'" data-credit-type="" data-user-id="'.$userID.'"> 
                <div>
                    <h3>
                        <span></span>
                        اعتبار
                    </h3>
                    <input type="text" class="amount" type="number" placeholder="مقدار">
                    <select name="rank-list" class="rank-list" >
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option> 
                    </select>
                    <textarea class="desc" > توضیحات </textarea>
                    <div class="save-user-activity">
                        <a href="javascript:void(0)"> ذخیره </a>
                    </div>
                </div> 
            </div>
        ';
    }


    public static function addPointManually()
    {
        if ( Functions::checkNonce( $_POST['nonce'] ,'admin_nonce' ,true) ){
            $user_id = intval( Functions::indexChecker(  $_POST ,'user_id' ) );
            $amount  = intval( Functions::indexChecker(  $_POST ,'amount' ) );
            $type    = Functions::indexChecker(  $_POST ,'type' ,false );
            $rank    = Functions::indexChecker(  $_POST ,'rank' ,false );
            $desc    = Functions::indexChecker(  $_POST ,'desc' );
            if ( $user_id > 0 && $amount > 0 && $type && $rank ){
                $params               = new stdClass();
                $params->title        = 'Add By Manually ' ;
                $params->challenge_id = 0;
                $params->descriptions = $desc;
                $params->rank         = $rank;
                $params->type         = $type;
                $params->amount       = $amount;
                $params->user_id      = $user_id;
                $params->added_by     = get_current_user_id();
                Points::addPoint( $user_id ,$params );
                wp_send_json_success( ['result' => 'Added'] );
            }
        }
        wp_send_json_error( ['Conflict'] );
    }




    public static function points( $userID )
    {
        return '
            <div class="user-points-list-con" data-user-id="'.$userID.'">
                 <span class="dashicons dashicons-menu-alt3"></span>   
            </div>
            <div class="user-points-list-handler-con handler-list-con-'.$userID.'" data-user-id="'.$userID.'"> 
                <div>
                    <h4> 
                        لیست امتیازات
                    </h4>
                    <table>
                        <thead>
                            <tr>
                                <th> آیدی چالش</th>
                                <th>عنوان </th>
                                <th> طبقه </th>
                                <th> نوع </th>
                                <th> مقدار </th>
                                <th> توسط </th>
                                <th> توضیحات </th>
                                <th> تاریخ ایجاد </th>
                                <th> تاریخ اعتبار </th>
                            </tr>
                        </thead>
                        <tbody>  
                        </tbody>
                    </table>
                </div> 
            </div>
        ';
    }


    public static function getUsersPointsAdmin()
    {
        $tr = '';
        if ( Functions::checkNonce( $_POST['nonce'] ,'admin_nonce' ,true ) && Functions::indexChecker( $_POST ,'user_id', false ) ){
            $points_items = Points::getUserPoints( intval( $_POST['user_id'] )   );
            if( !empty( $points_items ) ){
                foreach ( $points_items as $item ){
                    $tr .=
                        '<tr>
                            <th> '.$item->challenge_id.' </th>
                            <th> '.$item->title.' </th>
                            <th> '.$item->rank.' </th>
                            <th> '.$item->type.' </th>
                            <th> '.$item->amount.' </th>
                            <th> '.$item->added_by.' </th>
                            <th> '.$item->descriptions.' </th>
                            <th> '.$item->created_at.' </th>
                            <th> '.$item->expire_date.' </th>
                        </tr>
                    ';
                }
            }
            wp_send_json_success( ['result' => $tr ] );
        }
        wp_send_json_error( ['Conflict'] );
    }


    public static function getBulkMobile( $usersIDs )
    {
        global $wpdb;
        $imploded = Functions::prepareImplode( $usersIDs );
        $mobile   = [];
        if ( !empty( $imploded ) ){
            $results = $wpdb->get_results(
                "SELECT * FROM {$wpdb->usermeta}  WHERE user_id IN ($imploded) AND meta_key = 'force_verified_mobile'; "
            );
            if ( !empty( $results ) ){
                foreach ( $results as $result ){
                    $mobile[(int) $result->user_id ] = $result->meta_value;
                }
            }
        }
        return $mobile;
    }


    public static function getUserByMobile( $mobile )
    {
        if ( is_numeric( $mobile ) ){
            global $wpdb;
            $results = $wpdb->get_results(
                "SELECT * FROM {$wpdb->usermeta} WHERE meta_key = 'force_verified_mobile' AND meta_value ='{$mobile}'; "
            );
            if ( !empty( $results ) ){
                return $results[0];
            }
        }
        return [];
    }

    public static function getBulkUsersByID( $usersIDs )
    {
        $users    = [];
        if ( is_array( $usersIDs ) && !empty( $usersIDs ) ){
            $imploded = Functions::prepareImplode( $usersIDs );
            global $wpdb;
            $results = $wpdb->get_results(
                "SELECT * FROM {$wpdb->users} WHERE ID IN ( $imploded ) ; "
            );
            if ( !empty( $results ) ){
                foreach ( $results as $result ){
                    $users[(int) $result->ID ] = $result;
                }
            }
        }
        return $users;
    }


    public static function getAllUserMeta( $userID )
    {
        $all_meta = [];
        if ( is_numeric( $userID ) ){
            global $wpdb;
            $results = $wpdb->get_results(
                "SELECT * FROM {$wpdb->usermeta} WHERE user_id = $userID ;"
            );
            if ( !is_wp_error( $results ) && !empty( $results ) ){
               foreach ( $results as $meta ){
                   $all_meta[ $meta->meta_key ] = $meta->meta_value;
               }
            }
        }
        return $all_meta;
    }


    public static function getBulkUsersMeta( $usersIDs )
    {
        $all_meta = [];
        if ( is_array( $usersIDs ) ){
            global $wpdb;
            $imploded = Functions::prepareImplode( $usersIDs );
            $results = $wpdb->get_results(
                "SELECT * FROM {$wpdb->usermeta} WHERE user_id IN ( $imploded ) ; "
            );
            if ( !is_wp_error( $results ) && !empty( $results ) ){
                foreach ( $results as $meta ){
                    $all_meta[$meta->user_id][ $meta->meta_key ] = $meta->meta_value;
                }
            }
        }
        return $all_meta;
    }


    public static function getUserDetailsFromPointMeta( $meta )
    {
        if ( isset( $meta['user_point']) && !empty( $meta['user_point'] ) ){
            return json_decode( $meta['user_point'] );
        }
        $user_point = new stdClass();
        $user_point->has   = 0;
        $user_point->reach = 0;
        $user_point->rank  = 'E';
        return $user_point;
    }


    public static function getUserAvatar( $userID ,$userEmail )
    {
        if ( !empty( $userID ) || !empty( $userEmail ) ){
            $img = get_user_meta( $userID ,'profile_pic', true );
            if ( !empty( $img ) ) {
                $img_url = wp_get_attachment_image_src( (int) $img, 'thumbnail', false );
                $img_url = ( isset( $img_url[0] ) ) ? $img_url[0] : '';
                return '<img alt="" src="'.$img_url.'"  height="150" width="150">';
            }
            else {
                if ( function_exists( 'get_avatar' ) ) {
                    $avatar = get_avatar( $userEmail );
                    if( $avatar ){
                        return $avatar;
                    }
                }
                else {
                    return '<img src="http://www.gravatar.com/avatar/" '. md5( strtolower( $userEmail ) ).'>';
                }
            }
        }
        return '<img src="'.home_url().'/wp-content/plugins/hwp-club/resources/assets/public/images/default-profile.png" >';
    }

    public static function usernameChecker( $userID ,$username )
    {
        if ( is_numeric( $userID ) && !empty( $username ) ){
            global $wpdb;
            $results = $wpdb->get_results(
                "SELECT * FROM {$wpdb->users} WHERE user_login = '{$username}' AND ID <> {$userID}; "
            );
            if ( !is_wp_error( $results ) && !empty( $results ) ){
                return true;
            }
        }
        return false;
    }

    public static function updateUserTable( $userID ,$userData )
    {
        global $wpdb;
        $wpdb->update( $wpdb->users,
            $userData ,
            ['ID' => $userID ]
        );
        if ( !is_wp_error( $wpdb )  ){
            return true;
        }
        return false;
    }



    public static function getAllUsers( $limit = 100 ,$offset = 0 ,$sort = 'DESC' )
    {
        global $wpdb;
        $results =
            $wpdb->get_results(
                "SELECT users.* , meta_score.meta_value AS score  FROM {$wpdb->users} AS users 
                   INNER JOIN {$wpdb->usermeta} AS meta_score ON users.ID = meta_score.user_id AND meta_score.meta_key = 'user_point' 
                   INNER JOIN {$wpdb->usermeta} AS meta_level ON users.ID = meta_level.user_id AND meta_level.meta_key = 'wp_user_level' 
                   LEFT OUTER JOIN {$wpdb->usermeta} AS meta_private ON users.ID = meta_private.user_id AND meta_private.meta_key = 'account_private' 
                   WHERE meta_level.meta_value = 0 AND meta_private.meta_value IS NULL ORDER BY JSON_EXTRACT( meta_score.meta_value , '$.reach') {$sort} LIMIT {$offset} ,{$limit}
                ;"
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ){
            return $results;
        }
        return [];
    }


    public static function getUserLink( $userID )
    {
        if ( is_numeric( $userID ) ){
            return home_url('/club/users/'.$userID );
        }
        return home_url('/club/users/' );
    }



    public static function addToUserActiveChallengeMetaIfNotExists( $userID ,$challengeID )
    {
        $challenges = Challenges::getUserChallengeMetaForGlobal( $userID );
        if ( !in_array( $userID ,$challengeID ) ){
            $challenges->active[$challengeID ] = $challengeID;
            Challenges::updateUserMetaChallenge( $userID ,$challenges ,false );
        }
        return true;
    }


}