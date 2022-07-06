<?php

namespace HwpClub\core\includes;

use stdClass;
use WP_REST_Request;
use WP_REST_Server;

class RestRoute
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
        add_action('rest_api_init' ,[$this ,'routes' ]);
    }

    public function routes()
    {
        register_rest_route( 'club' ,'/create-challenges' ,[
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => [ $this ,'createChallengesHandler' ],
            'permission_callback' => [ $this ,'authentication' ]
        ]);

        register_rest_route( 'club' , '/watched-course'  ,[
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => [ $this ,'watchedCourseHandler' ],
            'permission_callback' => [ $this ,'authentication' ]
        ]);

        register_rest_route(  'club' , '/add-point' , [
            'methods'  => WP_REST_Server::CREATABLE  ,
            'callback' => [ $this ,'addPointRest' ]  ,
            'args'     => self::argsValidator('add-point') ,
            'permission_callback' => [ $this , 'authentication' ]
        ]);
    }


    public function watchedCourseHandler( WP_REST_Request $request )
    {
        $params       = (object) $request->get_params();
        $mobile       = Functions::indexChecker( $params ,'mobile' );
        $course_id    = Functions::indexChecker( $params ,'course_id' );
        $lesson_id    = Functions::indexChecker( $params ,'lesson_id' );
        $completed    = Functions::indexChecker( $params ,'completed' );
        $duration     = Functions::indexChecker( $params ,'duration' );
        $agent        = Functions::indexChecker( $params ,'agent' );
        $challenge_id = Functions::indexChecker( $params ,'challenge_id' );
        if ( !empty( get_post( $challenge_id ) ) ){
            $status = Watching::add( $mobile ,$course_id ,$lesson_id ,$challenge_id ,$duration ,$agent ,$completed );
            if ( $status === true ) {
                wp_send_json_success( [ 'status' => 'success' ,'message' => 'انجام شد' ] );
            }else if ( $status === false ){
                wp_send_json_error( [ 'status' => 'error' ,'message' => 'خطا هنگام ذخیره' ] );
            }
            wp_send_json_error( [ 'status' => 'error' ,'message' => $status ] );
        }
        wp_send_json_error( [ 'status' => 'error' ,'message' => 'جزو لیست نیست' ] );
    }


    public function createChallengesHandler( WP_REST_Request $request )
    {
        $params       = (object) $request->get_params();
        update_option($params);
        $title        = Functions::indexChecker( $params ,'title' );
        $ch_length    = Functions::indexChecker( $params ,'length' ,30 );
        $le_count     = Functions::indexChecker( $params ,'count'  );
        $slug         = Functions::indexChecker( $params ,'slug' );
        $dashboard    = Functions::indexChecker( $params ,'dashboard' );
        $thumbnails   = Functions::indexChecker( $params ,'thumbnails' );
        $course_id    = Functions::indexChecker( $params ,'course_id' );
        if ( !empty( $title ) ){
            $challenge_id = wp_insert_post([
                'post_type'   => 'challenges',
                'post_title'  => $title,
                'post_status' => 'publish',
            ]);
            if ( !is_wp_error( $challenge_id ) && is_numeric( $challenge_id ) ) {
                if( !empty( $thumbnails ) ){
                    $image_id = $this->uploadImage( $thumbnails );
                    update_post_meta( $challenge_id, '_thumbnail_id', $image_id );
                }
                $chat_room = wp_insert_post( [
                    'post_type'   => 'chat_room',
                    'post_title'  => $title,
                    'post_status' => 'publish',
                ]);

                update_field( 'challenge_length'        ,$ch_length ,$challenge_id );
                update_field( 'challenge_form_type'     , 'watching_challenge', $challenge_id );
                update_field( 'challenge_lessons_count' ,$le_count ,$challenge_id );
                update_field( 'challenge_profile_link'  ,$dashboard ,$challenge_id );
                update_field( 'challenge_product_link ' ,$slug ,$challenge_id );
                update_field( 'challenge_form_id'       , 5 , $challenge_id );
                update_field( 'challenge_private_mode'  , 'on' , $challenge_id );
                update_field( 'challenge_chat_room'     , $chat_room , $challenge_id );
                update_field( 'challenge_need_points'   , 0 , $challenge_id );
                update_field( 'challenge_reach_points'  , 5 , $challenge_id );
                update_field( 'challenge_course_id'     , $course_id , $challenge_id );

                $chat_room_id = Chatroom::createChatroom( $challenge_id ,get_post( $challenge_id ) );
                update_post_meta( $challenge_id ,'_enable_chat_room' , 'on' );
                update_post_meta( $challenge_id ,'_chat_room_text'   , $chat_room_id );
                update_post_meta( $challenge_id ,'_chat_room_length' , $ch_length );

                return new \WP_REST_Response( [
                    'status'       => 'success',
                    'challenge_id' => $challenge_id,
                ], 201 );
            }
            return new \WP_REST_Response([
                'status' => 'error',
                'message' => 'nok',
            ], 500 );
        }
        return new \WP_REST_Response([
            'status'  => 'error',
            'message' => 'Invalid data',
        ], 403 );
    }


    protected function uploadImage( $image ){
        include_once( ABSPATH . 'wp-admin/includes/image.php' );
        $uniq_name   = date('dmY').''.(int) microtime(true);
        $filename    = $uniq_name.'.png';
        $upload_dir  = wp_upload_dir();
        $upload_file = $upload_dir['path'] . '/' . $filename;
        $save_file   = fopen( $upload_file , 'w' );
        fwrite( $save_file ,$image );
        fclose( $save_file );

        $wp_filetype = wp_check_filetype(basename($filename), null );
        $attachment  = [
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => $filename,
            'post_content'   => '',
            'post_status'    => 'inherit'
        ];

        $attach_id = wp_insert_attachment( $attachment ,$upload_file );
        $image_new = get_post( $attach_id );
        $full_size_path = get_attached_file( $image_new->ID );
        $attach_data    = wp_generate_attachment_metadata( $attach_id ,$full_size_path );
        wp_update_attachment_metadata( $attach_id, $attach_data );
        return $attach_id;
    }


    public static function addPointRest( WP_REST_Request $request )
    {
        $body    = (object) $request->get_params();
        $user_db = Users::getUserByMobile( $body->mobile );
        if ( !empty( $user_db ) ){
            $user_id = $user_db->user_id;
            if ( !empty( $user_id ) ){
                $details = Points::getUserDetails( $user_id );
                if ( !empty( $details ) ){
                    $amount = Points::calculateProductPoint( $body->amount );
                    if ( $body->type == 'credit' ){
                        $rank = Ranks::getRank( $details->has + $amount )['slug'];
                    }else{
                        $rank = Ranks::getRank( $details->has - $amount )['slug'];
                    }
                    $params               = new stdClass();
                    $params->title        = $body->title;
                    $params->challenge_id = '';
                    $params->descriptions = $body->description ?? '';
                    $params->rank         = $rank;
                    $params->type         = $body->type;
                    $params->amount       = $amount;
                    $params->user_id      = $user_id;
                    $params->added_by     = $user_id;
                    Points::addPoint( $user_id ,$params );
                    wp_send_json_success( ['result' => 'added'] );
                }
            }
        }else{
            $user_id  = hfl_get_user_id( $body->mobile );
            $response = Points::getAllPurchasedList( $body->mobile ,$user_id );
            if ( is_numeric( $response ) && $response > 0 ) {
                wp_send_json_success( ['result' => 'added'] );
            }
        }
        wp_send_json( ['result' => 'error'] ,403 );
    }


    public static function authentication( WP_REST_Request $request ){
        if( $request->get_param('key') !== HWP_CLUB_REST_KEY ){
            wp_send_json( ['status' => 'error' ,'message' => 'کلید معتبر نیست ' ] );
        }
        return true;
    }


    public function argsValidator( $which )
    {
        $args = [];
        if ( $which == 'add-point' )
        {
            $args['mobile'] = [
                'required'           => true            ,
                'description'        => 'شماره موبایل ' ,
                'type'               => 'number'        ,
                'sanitize_callback'  => function( $value ){
                    return Functions::sanitizer( $value ,'sanitize_text_field,trim' );
                },
                'validate_callback'  => function( $value ){
                    return is_numeric( $value ) && strlen( $value ) > 10 ;
                },
            ];
            $args['amount'] = [
                'required'           => true            ,
                'description'        => 'مقدار اعتبار ' ,
                'type'               => 'number'        ,
                'sanitize_callback'  => function( $value ){
                    return Functions::sanitizer($value ,'sanitize_text_field,trim' );
                },
                'validate_callback'  => function( $value ){
                    return is_numeric( $value );
                },
            ];
            $args['title'] = [
                'required'           => true            ,
                'description'        => 'عنوان افزایش اعتبار ' ,
                'type'               => 'string'        ,
                'sanitize_callback'  => function( $value ){
                    return Functions::sanitizer($value ,'sanitize_text_field,trim' );
                },
                'validate_callback'  => function( $value ){
                    return strlen( $value ) > 0 && strlen( $value ) < 200;
                },
            ];
            $args['description'] = [
                'required'           => false            ,
                'description'        => 'توضیحات افزایش اعتبار ' ,
                'type'               => 'string'         ,
                'sanitize_callback'  => function( $value ){
                    return Functions::sanitizer( $value ,'sanitize_text_field,trim' );
                },
                'validate_callback'  => function( $value ){
                    return true;
                },
            ];
            $args['type'] = [
                'required'           => false    ,
                'description'        => 'نوع'  ,
                'type'               => 'string' ,
                'sanitize_callback'  => function( $value ){
                    return Functions::sanitizer( $value ,'sanitize_text_field,trim' );
                },
                'validate_callback'  => function( $value ){
                    return in_array( $value ,['credit','subtract'] );
                },
            ];
        }
        return $args;
    }


}