<?php
/**
 * Plugin Name:       Hamyar Club
 * Version:           1.0.0
 * Author:            Hamyar Technical Team
 * Text Domain:       hamyarclub
 * Domain Path:       /core/languages/
 */

namespace HwpClub;


use HwpClub\core\includes\Forms;
use HwpClub\core\includes\Challenges;
use HwpClub\core\includes\Chatroom;
use HwpClub\core\includes\Crons;
use HwpClub\core\includes\Endpoint;
use HwpClub\core\includes\Enqueues;
use HwpClub\core\includes\NotifAndNote;
use HwpClub\core\includes\Points;
use HwpClub\core\includes\RestRoute;
use HwpClub\core\includes\Users;
use HwpClub\core\includes\Watching;
use HwpClub\core\pages_handler\RegistrationHandler;

class HwpClub
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
        self::defined();
        self::include();
        self::run();
    }


    public static function defined()
    {
        define( 'HWP_CLUB_ROOT'            , plugin_dir_path( __FILE__ ) );
        if(!defined('CHAT_BACK_ENDPOINT')) {
            define( 'CHAT_BACK_ENDPOINT', 'http://chat.club.hamyar.co' );
        }
        if(!defined('HAMYAR_ENDPOINT')) {
            define( 'HAMYAR_ENDPOINT', 'https://hamyar.co' );
        }
        if(!defined('CHAT_FRONT_ENDPOINT')) {
            define( 'CHAT_FRONT_ENDPOINT', 'http://chat.club.hamyar.co:3000' );
        }
        define( 'HWP_CLUB_CORE'            , HWP_CLUB_ROOT . 'core/');
        define( 'HWP_CLUB_INCLUDES'        , HWP_CLUB_CORE . 'includes/');
        define( 'HWP_CLUB_UI'              , HWP_CLUB_ROOT . 'resources/ui/');
        define( 'HWP_CLUB_UPLOAD_PATH'     , HWP_CLUB_ROOT . 'resources/assets/public/uploads/');
        define( 'HWP_CLUB_PAGES_HANDLER'   , HWP_CLUB_CORE . 'pages-handler/');
        define( 'HWP_CLUB_ADMIN_ASSETS'    , plugin_dir_url( __FILE__ ) . 'resources/assets/admin/');
        define( 'HWP_CLUB_PUBLIC_ASSETS'   , plugin_dir_url( __FILE__ ) . 'resources/assets/public/');
        define( 'HWP_CLUB_VERSION'         ,'1.0.0' );
        define( 'HWP_CLUB_DEVELOPER_MODE'  , true );
        define( 'HWP_CLUB_SCRIPTS_VERSION' , HWP_CLUB_DEVELOPER_MODE ? time() : HWP_CLUB_VERSION );
        define( 'HWP_CLUB_REST_KEY'        ,'50c3340644ead669de2bdad723d601cb508def21');

    }


    public static function include()
    {
        require_once HWP_CLUB_ROOT .'vendor/autoload.php';
    }


    public static function run()
    {
        RestRoute::get_instance();
        Watching::get_instance();
        Chatroom::get_instance();
        Forms::get_instance();
        Crons::get_instance();
        Endpoint::get_instance();
        Enqueues::get_instance();
        Challenges::get_instance();
        Users::get_instance();
        Points::get_instance();
        RegistrationHandler::get_instance();
        NotifAndNote::get_instance();
    }

}
 HwpClub::get_instance();
