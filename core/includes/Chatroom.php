<?php

namespace HwpClub\core\includes;



class Chatroom
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
//        add_action( 'wp_insert_post' ,[$this ,'createChatroom'] ,10 ,3 );
//        add_action('user_register', [$this, 'registerUserChatSystem'],10,1); //no need to signup with empty challenge

    }


    public static function createChatroom( $postID ,$post ,$update=false )
    {
//        if ( !wp_is_post_revision( $postID ) && $post->post_type == 'challenge' && $post->post_status == 'publish' ) {
            $image_url = get_the_post_thumbnail_url( $postID ,'full' );
            $body = [
                'api_secret'  => '963ac56e667eb968b070422ed81232c0963a0ba4',
                'title'       => $post->post_title ,
                'image_url'   => $image_url ,
                'slug'        => 'challenge-'.$postID ,
                'description' => 'گروه ' . $post->post_title,
            ];
            $response = Functions::remotePost( CHAT_BACK_ENDPOINT.'/chatroom/create' ,$body );
            return $response;
//        }
    }


    public static function updateUserChatroom( $userID ,$removeChatroomID ='' )
    {
        $user_object = Users::getUserObject( $userID );
        if(empty($user_object->challenges)){
            $user_object->challenges= new \stdClass();
        }

        $chatroom_ids = [];
        $challenge_list=[];

        if(isset($user_object->challenges->active)){
            $challenge_list+=$user_object->challenges->active;
        }
        if(isset($user_object->challenges->completed)){
            $challenge_list+=$user_object->challenges->completed;
        }
        foreach ($challenge_list as $post_id){
            $chatroom_id=get_post_meta( $post_id ,'_chat_room_id' ,true );
            if(empty($chatroom_id)){
                continue;
            }
            $chat_room_length=get_post_meta( $post_id ,'_chat_room_length' ,true );
            if($chat_room_length>0){
                $users=Challenges::challengeUsers( $post_id ,true );
                if(!is_array($users) || empty($users)){
                    continue;
                }
                $user_chatroom_start_time=(int)$users[$userID];
                if($user_chatroom_start_time > 0 && time() > ($user_chatroom_start_time+(60*60*24*$chat_room_length))){
                    continue;
                }
            }
            if ( $chatroom_id !== $removeChatroomID){
                $chatroom_ids[] = $chatroom_id;
            }
        }
        if ( is_numeric( $user_object->mobile )){
            $avatar=preg_match('/src="(.*?)"/',$user_object->avatar,$matches);
            if(!empty($matches[1])){
                $avatar=$matches[1];
            }
            $body = [
                'api_secret'    => '963ac56e667eb968b070422ed81232c0963a0ba4',
                'mobile'        => $user_object->mobile,
                'token'         => $user_object->token ,
                'fullname'  => $user_object->display_name,
                'avatar_url'        => $avatar,
                'permissions'   => [
                    'chatrooms' =>  $chatroom_ids ,
                ]
            ];
            Functions::remotePost( CHAT_BACK_ENDPOINT.'/authenticate-user' ,$body );
        }
    }





}