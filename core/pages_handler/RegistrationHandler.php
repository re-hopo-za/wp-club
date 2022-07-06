<?php

namespace HwpClub\core\pages_handler;

use HwpClub\core\includes\Chatroom;
use HwpClub\core\includes\Enqueues;
use HwpClub\core\includes\Functions;
use HwpClub\core\includes\Users;
use HwpClub\resources\ui\FrontPartials;

class RegistrationHandler
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
        add_action('wp_ajax_club_update_user_profile'     ,[$this  ,'updateProfile']);
        add_action('wp_ajax_club_checker_username_status' ,[$this  ,'usernameChecker']);
        add_shortcode('club_user_information'    ,[$this  ,'userInformation']);
        add_action('gform_after_submission'          ,[$this  ,'updateUserMetaOnGravitySubmit' ] ,10, 2 );
    }


    public static function userInformation()
    {
        $all_meta = Users::getUserObject();
        if ( !$all_meta->start_score ) {
            Enqueues::frontEnqueues('user-info');
            if ( class_exists('GFForms') ){
                return str_replace( '[content]' ,self::gravityForm() ,FrontPartials::cleanRoot()  );
            }
            return 'خطا هنگام بارگزاری فرم';
        }
        wp_safe_redirect( home_url('/club') );
        exit;
    }


    public static function gravityForm()
    {
        return do_shortcode('[gravityform id="1" title="false" description="true" ajax="true"]' );
    }


    public static function editProfileForm( $userObject )
    {
        $all_meta = Users::getAllUserMeta( $userObject->ID );
        $profile  = str_replace( '[profile-image]' ,$userObject->avatar ,FrontPartials::profileAvatarSection() );
        $profile  = str_replace( '[change-image]'  ,$profile ,FrontPartials::editUserInformationForm() );
        $profile  = str_replace( '[nonce]'         ,self::createNonce() ,$profile );
        $profile  = str_replace( '[instagram]'     ,self::getUserMeta( $all_meta ,'instagram_account' ) ,$profile );
        $profile  = str_replace( '[biography]'     ,self::getUserMeta( $all_meta ,'biography' ) ,$profile );
        $profile  = str_replace( '[private]'       ,self::getUserMeta( $all_meta ,'account_private' ) ,$profile );
        $profile  = str_replace( '[username]'      ,$userObject->username ,$profile );
        return      str_replace( '[name]'          ,$userObject->display_name ,$profile );
    }

    public static function createNonce()
    {
        return wp_nonce_field('save_img', '_wp_profile_nonce');
    }


    public static function updateProfile()
    {
        Functions::frontNonceChecker();
        $user_id = get_current_user_id( );
        Users::updateUserTable( $user_id ,[
            'display_name'  => $_POST['name'],
            'user_nicename' => $_POST['name'] ,
            'user_login'    => $_POST['username']
        ]);
        update_user_meta( $user_id ,'instagram_account' ,Functions::indexChecker( $_POST ,'instagram' ) );
        update_user_meta( $user_id ,'biography'         ,Functions::indexChecker( $_POST ,'biography' ) );
        update_user_meta( $user_id ,'account_private'   ,self::checkPrivateAccount( Functions::indexChecker( $_POST ,'private' ) ) );
        Chatroom::updateUserChatroom( $user_id );
        wp_send_json([
            'success'=>true
        ]);
    }

    public static function checkPrivateAccount( $private )
    {
        if ( !empty( $private ) && $private == 'true' ){
            return 'checked="checked"';
        }
        return null;
    }



    public static function getUserMeta( $allMeta ,$metaKey )
    {
        if( isset( $allMeta[$metaKey] ) ){
            return $allMeta[$metaKey];
        }
        return '';
    }


    public static function updateUserMetaOnGravitySubmit( $entry ,$form )
    {
        if( isset( $form['id'] ) && $form['id'] == 1 ){
            $user_id   = get_current_user_id();
            $biography = Functions::indexChecker( $entry, '38', null );
            $private   = Functions::indexChecker( $entry, '39', null );
            if ( !empty( $private ) ){
                update_user_meta( $user_id ,'account_private' ,'checked="checked"' );
            }else{
                update_user_meta( $user_id ,'account_private' ,'' );
            }
            update_user_meta( $user_id ,'biography' ,$biography );
            update_user_meta( $user_id ,'first_score' ,time() );
            Chatroom::updateUserChatroom( $user_id );
        }
    }


    public static function usernameChecker()
    {
        Functions::frontNonceChecker();
        $username = Functions::indexChecker( $_POST ,'username' );
        if( !Users::usernameChecker( get_current_user_id() ,$username ) ){
            wp_send_json_success([ 'status' => 404 , 'message'=>'این نام کاربری موجود نمیباشد' ]);
        }else{
            wp_send_json_success([ 'status' => 403 , 'message'=>'این نام کاربری قبلا ثبت شده است' ]);
        }
    }
}




