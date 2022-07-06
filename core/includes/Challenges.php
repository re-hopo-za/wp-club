<?php

namespace HwpClub\core\includes;


use HwpClub\core\pages_handler\ChallengesHandler;
use stdClass;

class Challenges
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
        add_action( 'init'                                  ,[$this ,'createChallengePostType'] ,0 );
        add_action( 'init'                                  ,[$this ,'createChallengeCategory'] ,0 );
        add_action( 'manage_challenges_posts_columns'       ,[$this ,'setCustomEditColumn'] ,10 ,1 );
        add_action( 'manage_challenges_posts_custom_column' ,[$this ,'setCustomColumn'] ,10 ,2 );
        add_action( 'wp_ajax_club_register_challenge'       ,[$this ,'registerOnChallenge']);
        add_action( 'wp_ajax_club_submit_comment'           ,[$this ,'submitComment']);
        add_action( 'add_meta_boxes'                        ,[$this, 'addMetaBox']   ,10  );
        add_action( 'save_post_challenges'                  ,[$this, 'postSaveMeta'] ,10 ,2 );
    }



    public static function createChallengePostType()
    {
        $labels = [
            'name'                => _x( 'Challenges', 'Post Type General Name', 'hamyarclub' ),
            'singular_name'       => _x( 'Challenge' , 'Post Type Singular Name', 'hamyarclub' ),
            'menu_name'           => __( 'Challenges', 'hamyarclub' ),
            'parent_item_colon'   => __( 'Parent Challenge', 'hamyarclub' ),
            'all_items'           => __( 'All Challenges', 'hamyarclub' ),
            'view_item'           => __( 'View Challenge', 'hamyarclub' ),
            'add_new_item'        => __( 'Add New Challenge', 'hamyarclub' ),
            'add_new'             => __( 'Add New', 'hamyarclub' ),
            'edit_item'           => __( 'Edit Challenge', 'hamyarclub' ),
            'update_item'         => __( 'Update Challenge', 'hamyarclub' ),
            'search_items'        => __( 'Search Challenge', 'hamyarclub' ),
            'not_found'           => __( 'Not Found', 'hamyarclub' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'hamyarclub' ),
        ];
        $args = [
            'label'               => __( 'challenges', 'hamyarclub' ),
            'description'         => __( 'Club Challenge', 'hamyarclub' ),
            'labels'              => $labels,
            'supports'            => [ 'title' ,'editor' ,'thumbnail' ,'comments' ,'custom-fields' ] ,
            'taxonomies'          => [ 'groups' ],
            'hierarchical'        => false  ,
            'public'              => true   ,
            'show_ui'             => true   ,
            'show_in_menu'        => true   ,
            'show_in_nav_menus'   => true   ,
            'show_in_admin_bar'   => true   ,
            'menu_position'       => 5      ,
            'can_export'          => true   ,
            'has_archive'         => true   ,
            'exclude_from_search' => false  ,
            'publicly_queryable'  => true   ,
            'capability_type'     => 'post' ,
            'show_in_rest'        => true   ,
        ];
        register_post_type( 'challenges', $args );
    }



    public static function  createChallengeCategory()
    {
        $labels = [
            'name'              => _x( 'Categories', 'taxonomy general name', 'hamyarclub' ),
            'singular_name'     => _x( 'Category', 'taxonomy singular name', 'hamyarclub' ),
            'search_items'      => __( 'Search Categories', 'hamyarclub' ),
            'all_items'         => __( 'All Categories', 'hamyarclub' ),
            'parent_item'       => __( 'Parent Categories', 'hamyarclub' ),
            'parent_item_colon' => __( 'Parent Categories:', 'hamyarclub' ),
            'edit_item'         => __( 'Edit Category', 'hamyarclub' ),
            'update_item'       => __( 'Update Category', 'hamyarclub' ),
            'add_new_item'      => __( 'Add New Category', 'hamyarclub' ),
            'new_item_name'     => __( 'New Category Name', 'hamyarclub' ),
            'menu_name'         => __( 'Category', 'hamyarclub' ),
        ];
        $args = [
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => [ 'slug' => 'categories' ]
        ];
        register_taxonomy( 'challenges-cat',['challenges'] , $args );
    }


    public static function updateUserChallengeOnCompleted( $userID ,$challengeID )
    {
        $challenges = [];
        if ( $userID ){
            $challenges = self::getUserChallengeMeta( $userID );
            if ( isset( $challenges->active ) && in_array( $challengeID ,$challenges->active ) ){
                $challenges->active = array_flip( $challenges->active );
                unset( $challenges->active[$challengeID ] );
                $challenges->completed[$challengeID] = $challengeID;
            }
        }
        return $challenges;
    }

    public static function updateUserMetaChallenge( $userID ,$userMeta ,$updateChat )
    {
        if ( $userID > 0 ){
            global $wpdb;
            $active    = implode(',' ,$userMeta->active );
            $completed = implode(',' ,$userMeta->completed );
            $value = 'JSON_OBJECT( "active" ,JSON_ARRAY('.$active.') ,"completed" ,JSON_ARRAY('.$completed.') )';
            if ( empty( self::getUserChallengeMeta( $userID ) ) ){
                $results = $wpdb->query(
                    "INSERT INTO {$wpdb->usermeta} ( user_id ,meta_key ,meta_value ) 
                        VALUES ({$userID} ,'hwp_challenges_list' ,{$value} );"
                );
            }else{
                $results = $wpdb->query(
                    "UPDATE {$wpdb->usermeta} SET meta_value={$value} WHERE user_id={$userID} AND meta_key ='hwp_challenges_list' ;" );
            }
            if ( !is_wp_error( $results ) && !empty( $results ) ){
                if ( $updateChat ){
                    Chatroom::updateUserChatroom( $userID );
                }
                return $results;
            }
        }
        return false;
    }


    public static function getUserChallengeMeta( $userID )
    {
        if ( $userID > 0 ){
            global $wpdb;
            $results = $wpdb->get_results(
                "SELECT * FROM {$wpdb->usermeta} WHERE meta_key = 'hwp_challenges_list' AND user_id = {$userID};"
            );
            if ( !is_wp_error( $results ) && !empty( $results ) ){
                return json_decode( $results[0]->meta_value );
            }
        }
        return [];
    }


    public static function getUserChallengeMetaForGlobal( $userID )
    {
        $challenges = self::getUserChallengeMeta( $userID );
        if ( empty( $challenges ) ){
            $challenges = new stdClass();
            $challenges->active    = [];
            $challenges->completed = [];
        }
        return $challenges;
    }



    public static function setCustomEditColumn( $columns ) {
        unset( $columns['comments'] );
        unset( $columns['taxonomy-genre'] );
        $columns['gravity_id'] = 'gravity id';
        return $columns;
    }


    public static function setCustomColumn( $columnID , $postID ) {
        switch ( $columnID ) {
            case 'gravity_id' :
                echo '<span style="text-align: center">'.get_post_meta( $postID , 'gravity_id' ,true ) .'</span>';
                break;
        }
    }


    public function registerOnChallenge()
    {
        if ( Functions::checkNonce( $_POST['nonce'] ,'club_nonce' ) ){
            $user_object = Users::getUserObject();
            $post_id     = Functions::indexChecker( $_POST ,'challenge_id' );
            $post_meta   = self::getChallengesMeta( [$post_id] );
            if ( Functions::indexChecker( $post_meta ,$post_id ,false ) ){
                $user_meta = self::challengeEmptyObject( self::getUserChallengeMeta( $user_object->ID ) );
                if ( ChallengesHandler::challengeMetaStatus( $post_meta[$post_id] ,$user_object ) ){
                    if ( !isset( $user_meta->completed[$post_id] ) ){
                        if ( !in_array( $post_id ,$user_meta->active ) && ( !isset( $post_meta[$post_id]['challenge_cost_point'] ) || $post_meta[$post_id]['challenge_cost_point'] <= $user_object->point->has ) ){
                            $user_meta->active[$post_id] = $post_id ;
                            self::updateUserMetaChallenge( $user_object->ID ,$user_meta,true );
                            self::updateChallengeUsersList( $post_id ,$user_object->ID );
                            Points::addSubtractPoint( $post_meta ,$post_id ,$user_object );
                            wp_send_json( ['result' => home_url('/club/challenge-single/'.$post_id ) ] ,200 );
                        }
                    }
                }
            }
        }
        wp_send_json(['result' => 'bad request'] ,403 );
    }


    public static function challengeEmptyObject( $userMeta )
    {
        if ( empty( $userMeta ) ){
            $empty_object = new stdClass();
            $empty_object->active    = [];
            $empty_object->completed = [];
            return $empty_object;
        }
        return $userMeta;
    }


    public static function insertChallenges( $userID ,$userData )
    {
        if ( $userID > 0 ){
            global $wpdb;
            $results = $wpdb->query(
                "INSERT INTO {$wpdb->usermeta} ( user_id ,meta_key ,meta_value ) 
                        VALUES ({$userID} ,'user_point' ,JSON_OBJECT({$userData}) );"
            );
            if ( !is_wp_error( $results ) && !empty( $results ) ){
                return $results;
            }
        }
        return $userData;
    }


    public static function getSingleChallenge( $challengeID )
    {
        if ( is_numeric( $challengeID ) ){
            $challenge = get_post( $challengeID );
            if ( !empty( $challenge ) && $challenge->post_type == 'challenges' ){
                return $challenge;
            }
        }
        return [];
    }


    public static function getChallengesMeta( $challengesIDs )
    {
        global $wpdb;
        $ids   = Functions::prepareImplode( $challengesIDs );
        $items = [];
        if ( !empty( $ids ) ){
            $results = $wpdb->get_results(
                "SELECT * FROM {$wpdb->postmeta} WHERE post_id IN ($ids);"
            );
            if ( !is_wp_error( $results ) && !empty( $results ) ){
                foreach ( $results as $result ){
                    $items[ $result->post_id ][$result->meta_key] = $result->meta_value;
                }
            }
        }
        return $items;
    }


    public static function getChallenges( $userChallenges = false  )
    {
        global $wpdb;
        $where = '';
        if ( !empty( $userChallenges ) ){
            $where = 'AND ID IN ('.Functions::prepareImplode( $userChallenges ).')';
        }
        $results = $wpdb->get_results(
            "SELECT post.* FROM {$wpdb->posts} AS post
                   INNER JOIN {$wpdb->postmeta} AS meta ON post.ID = meta.post_id
                   WHERE post.post_type = 'challenges' AND ( meta.meta_key = 'challenge_private_mode' AND meta.meta_value = '0' ) AND
                   (post_status <> 'trash' AND post_status <> 'draft' AND post_status <> 'auto-draft') {$where} ORDER BY ID DESC;"
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ){
            return $results;
        }
        return [];
    }


    public static function checkFormOnDb( $table ,$formID ,$userID ,$today = true )
    {
        global $wpdb;
        $date    = $table == 'gf_entry' ? 'date_created' : 'date';
        $creator = $table == 'gf_entry' ? 'created_by' : 'user_id';
        $table   = $wpdb->prefix.$table;
        $today   = $today ? ' AND DATE('.$date.') = CURDATE() ' : '';
        $results = $wpdb->get_results(
            "SELECT * FROM {$table} WHERE form_id = {$formID} AND {$creator} = {$userID} {$today};"
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ){
            return $results;
        }
        return [];
    }




    public static function challengeComments( $challengeID )
    {
        $comments = [];
        if ( is_numeric( $challengeID ) ){
            $result = get_comments([
                'post_id' => (int) $challengeID,
            ]);
            if ( !empty( $result ) ){
                foreach ( $result as $item ){
                    if ( $item->user_id > 0 ){
                        $comments[] = (object) [
                            'ID'        => $item->comment_ID,
                            'name'      => $item->comment_author,
                            'email'     => $item->comment_author_email,
                            'comment'   => $item->comment_content,
                            'date'      => $item->comment_date,
                            'approved'  => $item->comment_approved,
                            'parent'    => $item->comment_parent,
                            'user_id'   => $item->user_id,
                        ];
                    }
                }
            }
        }
        return $comments;
    }


    public static function singleChallenge( $challengeID )
    {
        if ( is_numeric( $challengeID ) ){
            $challenge = get_post( $challengeID );
            if ( !empty( $challenge ) ){
                return (object) [
                    'post' => $challenge ,
                    'meta' => self::getSingleChallengeMeta( $challengeID )
                ];
            }
        }
        return [];
    }


    public static function challengeUsers( $challengeID ,$full = false )
    {
        if ( is_numeric( $challengeID ) ){
            $meta = get_post_meta( $challengeID ,'challenge_registered_users' ,true );
            if ( !empty( $meta ) ){
                $meta = maybe_unserialize( $meta );
                if ( is_array( $meta ) ){
                    if ( $full ) return $meta;
                    return array_keys( $meta );
                }
            }
        }
        return [];
    }


    public static function getSingleChallengeMeta( $challengeID )
    {
        global $wpdb;
        $meta = [];
        $results = $wpdb->get_results(
            "SELECT * FROM {$wpdb->postmeta} WHERE post_id = {$challengeID};"
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ){
            foreach ( $results as $result ){
                $meta[$result->meta_key] = $result->meta_value;
            }
        }
        return $meta;
    }


    public static function getUserCountInSpecificChallenges( $challengeID )
    {
        global $wpdb;
        $meta = [];
        $results = $wpdb->get_results(
            "SELECT * FROM {$wpdb->usermeta} WHERE meta_key ='hwp_challenges_list' AND JSON_CONTAINS( meta_value ,'{$challengeID}' ,'$.active' ) > 0;"
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ){
            return $results;

        }
        return [];
    }


    public static function updateChallengeUsersList( $challengeID ,$userID )
    {
        $meta       = get_post_meta( $challengeID ,'challenge_registered_users' ,true );
        $chatLength = get_post_meta($challengeID,'_chat_room_length',true);

        $endDate = -1;
        if( is_numeric( $chatLength ) && $chatLength > 0 ){
            $endDate = time();
        }

        if( empty( $meta ) ){
            add_post_meta( $challengeID ,'challenge_registered_users' ,[$userID => $endDate] );
        }else{
            $meta   = maybe_unserialize( $meta );
            $meta[$userID] = $endDate;
            update_post_meta( $challengeID ,'challenge_registered_users' ,$meta );
        }
    }



    public static function getBulkChallengesTitle( $challengesIDs )
    {
        global $wpdb;
        $implode = Functions::prepareImplode( $challengesIDs );
        $titles  = [];
        if ( !empty( $implode ) ){
            $results = $wpdb->get_results(
                "SELECT * FROM {$wpdb->posts} WHERE ID IN ($implode); "
            );
            if ( !empty( $results ) ){
                foreach ( $results as $result ){
                    $titles[ $result->ID ] = $result->post_title;
                }
            }
        }
        return $titles;
    }


    public static function submitComment()
    {
        if ( Functions::checkNonce( $_POST['nonce'] ,'club_nonce' ) ){
            $challenge_id = Functions::indexChecker( $_POST ,'challenge_id' ,0 );
            $comment_body = Functions::indexChecker( $_POST ,'comment' ,'' );
            if ( $challenge_id > 0 && !empty( $comment_body ) ){
                $userObject = Users::getUserObject( get_current_user_id() );
                if ( in_array( $challenge_id ,self::mergeChallenges( $userObject ) ) ){
                    $data = [
                        'comment_post_ID'      => $challenge_id,
                        'comment_content'      => $comment_body,
                        'user_id'              => $userObject->ID,
                        'comment_author'       => $userObject->display_name,
                        'comment_author_email' => $userObject->user_email
                    ];
                    $comment_id = wp_insert_comment( $data );
                    if ( ! is_wp_error( $comment_id ) ) {
                        wp_send_json_success( ['status' => 'success' ,'comment_id' => $comment_id] );
                    }

                }
            }
        }
        wp_send_json_error( ['status' => 'error' ,'comment_id' => ''] );
    }


    public static function mergeChallenges( $userObject )
    {
        if ( isset( $userObject->challenges->active ) && isset( $userObject->challenges->completed ) ){
            $challenges = array_merge( $userObject->challenges->active ,$userObject->challenges->completed );
            if ( !empty( $challenges ) && is_array( $challenges ) ){
                return $challenges;
            }
        }
        return [];
    }


    public function addMetaBox() {
        add_meta_box(
            'general_metabox',
            'تنظیمات چت روم',
            [ $this, 'postMetaBoxCallback' ],
            'challenges',
            'side'
        );
    }

    public static function postMetaBoxCallback() {
        global $post;
        wp_nonce_field('_general_nonce', '_general_nonce');

        ?>
        <label>
            فعال سازی چت روم
            <input type="checkbox" name="_enable_chat_room" value="on" <?php checked(get_post_meta($post->ID, '_enable_chat_room', true), 'on')?> >
        </label>
        <br>
        <br>
        <?php if(get_post_meta($post->ID, '_enable_chat_room', true)=='on'): ?>
            <label>
                آی دی چت روم در سرور
                <p>
                    <input readonly type="text" name="_chat_room_id" value="<?php echo get_post_meta($post->ID, '_chat_room_id', true) ?>">
                </p>
            </label>
            <br>
            <label>
                مدت زمان چت روم برای هر کاربر
                <p>
                    <input type="number" min="0" name="_chat_room_length" value="<?php echo get_post_meta($post->ID, '_chat_room_length', true) ?>">
                    <br>
                    0 برای بی نهایت
                </p>
            </label>
        <?php  endif; ?>
        <?php

    }

    public function postSaveMeta( $post_id ,$post ) {
        $nonce_name = $_POST[ '_general_nonce' ] ?? '';

        if (!wp_verify_nonce($nonce_name, '_general_nonce')
            || !current_user_can('edit_post', $post_id)
            || wp_is_post_autosave($post_id)
            || wp_is_post_revision($post_id)
        ) {
            return;
        }

        if (isset($_POST['_enable_chat_room']) && !empty($_POST['_enable_chat_room'])) {
            if(get_post_meta($post_id, '_chat_room_id', true)==''){
                update_post_meta($post_id, '_enable_chat_room', 'on');
                $chat_room_id=Chatroom::createChatroom($post_id,$post);
                update_post_meta($post_id, '_chat_room_id', $chat_room_id);
            }
            update_post_meta($post_id, '_chat_room_length', (int)$_POST['_chat_room_length']);
        }
    }


    public static function watchingCoursesList()
    {
        global $wpdb;
        $results = $wpdb->get_results(
            "SELECT post.* FROM {$wpdb->posts} AS post
                   INNER JOIN {$wpdb->postmeta} AS meta ON post.ID = meta.post_id
                   WHERE post.post_type = 'challenges' AND ( meta.meta_key = 'challenge_private_mode' AND meta.meta_value = 'on' ) AND
                   (post_status <> 'trash' AND post_status <> 'draft' AND post_status <> 'auto-draft') ORDER BY ID DESC;"
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ){
            return $results;
        }
        return [];
    }







}