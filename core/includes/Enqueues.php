<?php

namespace HwpClub\core\includes;

class Enqueues
{

    protected static $_instance = null;
    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    public function __construct( $page = 'main' )
    {
        if ( is_admin() ){
            add_action('admin_enqueue_scripts' ,[$this ,'adminEnqueues'] );
        }else{
            add_action('wp_enqueue_scripts'    ,[$this ,'frontEnqueues'] );
        }
    }


    public static function adminEnqueues()
    {
        wp_enqueue_script(
            'hwp_admin_club_js' ,
            HWP_CLUB_ADMIN_ASSETS.'js/club-admin.js' ,
            ['jquery'] ,
            HWP_CLUB_SCRIPTS_VERSION,true
        );
        wp_localize_script(
            'hwp_admin_club_js' ,
            'club_object' ,
            [
                'club_ajax_url'  => admin_url( 'admin-ajax.php' ) ,
                'club_home_url'  => home_url(),
                'club_nonce'     => wp_create_nonce('admin_nonce')
            ]
        );
        wp_enqueue_style(
            'hwp_admin_club_css' ,
            HWP_CLUB_ADMIN_ASSETS.'css/club-admin.css' ,
            false,
            HWP_CLUB_SCRIPTS_VERSION
        );
    }


    public static function frontEnqueues( $page = null )
    {
        if ( get_page_template_slug() == 'ClubUrlHandler.php' && empty( $page ) ){
            $page    = Functions::getCurrentPage();
            $depends = ['jquery','hwp_bootstrap_js' ,'izi_toast_js'];
            if ( empty( $page[1] ) ) {
                $page = $page[0];
                if ($page == 'edit-profile') {
                    self::croppieScript();
                    self::profileHandlerScript();
                    self::croppieStyle();
                    self::mainLocalizeScript();

                } elseif ($page == 'close-friends') {
                    self::splideSliderScript();
                    self::splideSliderStyle();
                    self::activeScript(['hwp_splide_js']);
                    $depends[] = 'hwp_splide_js';
                }
            }elseif ( $page[0] == 'users') {
                self::splideSliderScript();
                self::splideSliderStyle();

            }elseif ( $page[0] == 'challenge-single' ){
                self::highchartsScript();
                self::highchartsExportingScript();
                $depends[] = 'highcharts_js';
                $depends[] = 'export_highcharts_js';
            }

            self::bootstrapScript(['jquery']);
            self::fontInit();
            self::bootstrapStyle();
            self::indexStyle();
            self::iziToastStyle();
            self::mainStyle();
            self::iziToastScript();
            self::mainScript( $depends );
            self::mainLocalizeScript();
        }elseif ( $page == 'user-info' ){
            self::fontInit();
            self::gravityScript( ['jquery'] );
            self::tomSelectScript();
            self::tomSelectStyle();
            self::indexStyle();
            self::gravityStyle();
            self::indexStyle();
            self::mainLocalizeScript();
        }

    }


    public static function mainScript( $dependencies = null )
    {
        wp_enqueue_script(
            'hwp_club_js' ,
            HWP_CLUB_PUBLIC_ASSETS.'js/club.js' ,
             $dependencies ,
            HWP_CLUB_SCRIPTS_VERSION,true
        );
    }

    public static function bootstrapScript( $dependencies = null )
    {
        wp_enqueue_script(
            'hwp_bootstrap_js' ,
            HWP_CLUB_PUBLIC_ASSETS.'js/bootstrap.bundle.min.js' ,
            $dependencies ,
            HWP_CLUB_SCRIPTS_VERSION ,
            true
        );
    }

    public static function profileHandlerScript( $dependencies = null )
    {
        wp_enqueue_script(
            'hwp_club_js' ,
            HWP_CLUB_PUBLIC_ASSETS.'js/profile-handler.js' ,
            $dependencies ,
            HWP_CLUB_SCRIPTS_VERSION ,
            true
        );
    }

    public static function gravityScript( $dependencies = null )
    {
        wp_enqueue_script(
            'hwp_gravity_js' ,
            HWP_CLUB_PUBLIC_ASSETS.'js/register-gravity.js' ,
             $dependencies,
            HWP_CLUB_SCRIPTS_VERSION ,
            true
        );
    }

    public static function croppieScript( $dependencies = null )
    {
        wp_enqueue_script(
            'hwp_croppie_js' ,
            HWP_CLUB_PUBLIC_ASSETS.'js/croppie-2.6.4.js' ,
            $dependencies ,
            HWP_CLUB_SCRIPTS_VERSION
        );
    }

    public static function splideSliderScript( $dependencies = null )
    {
        wp_enqueue_script(
            'hwp_splide_js' ,
            HWP_CLUB_PUBLIC_ASSETS.'js/splide.min.js' ,
            $dependencies ,
            HWP_CLUB_SCRIPTS_VERSION
        );
    }

    public static function activeScript( $dependencies = null )
    {
        wp_enqueue_script(
            'hwp_active_js' ,
            HWP_CLUB_PUBLIC_ASSETS.'js/active.js' ,
            $dependencies ,
            HWP_CLUB_SCRIPTS_VERSION,
            true
        );
    }

    public static function tomSelectScript( $dependencies = null )
    {
        wp_enqueue_script(
            'tom_select_js' ,
            HWP_CLUB_PUBLIC_ASSETS. 'js/tom-select.complete.js' ,
            $dependencies ,
            HWP_CLUB_SCRIPTS_VERSION
            ,true
        );
    }

    public static function iziToastScript( $dependencies = null )
    {
        wp_enqueue_script(
            'izi_toast_js' ,
            HWP_CLUB_PUBLIC_ASSETS. 'js/izi-toast.js' ,
            $dependencies ,
            HWP_CLUB_SCRIPTS_VERSION ,
            true
        );
    }

    public static function highchartsScript( $dependencies = null )
    {
        wp_enqueue_script(
            'highcharts_js' ,
            HWP_CLUB_PUBLIC_ASSETS. 'js/highcharts.js' ,
            $dependencies ,
            HWP_CLUB_SCRIPTS_VERSION
        );
    }

    public static function highchartsExportingScript( $dependencies = null )
    {
        wp_enqueue_script(
            'export_highcharts_js' ,
            HWP_CLUB_PUBLIC_ASSETS. 'js/export-highcharts.js' ,
            $dependencies ,
            HWP_CLUB_SCRIPTS_VERSION
        );
    }


    public static function mainLocalizeScript()
    {
        wp_localize_script(
            'hwp_club_js' ,
            'club_object' ,
            [
                'club_ajax_url'  => admin_url( 'admin-ajax.php' ) ,
                'club_home_url'  => home_url(),
                'club_captcha'   => (function_exists('hamyar_feature_recaptcha_site_key') ) ? hamyar_feature_recaptcha_site_key() : '' ,
                'club_nonce'     => wp_create_nonce('club_nonce')
            ]
        );
        return 'hwp_club_js';
    }


    public static function fontInit()
    {
        wp_enqueue_style(
            'hwp_font_init_css' ,
            HWP_CLUB_PUBLIC_ASSETS.'css/font-init.css' ,
            false,
            HWP_CLUB_SCRIPTS_VERSION
        );
    }



    public static function mainStyle()
    {
        wp_enqueue_style(
            'hwp_club_css' ,
            HWP_CLUB_PUBLIC_ASSETS.'css/club.css' ,
            false,
            HWP_CLUB_SCRIPTS_VERSION
        );
    }


    public static function bootstrapStyle()
    {
        wp_enqueue_style(
            'hwp_bootstrap_css' ,
            HWP_CLUB_PUBLIC_ASSETS.'css/bootstrap.min.css' ,
            false,
            HWP_CLUB_SCRIPTS_VERSION
        );
    }


    public static function boostrapIcons()
    {
        wp_enqueue_style(
            'hwp_bootstrap_icons_css' ,
            HWP_CLUB_PUBLIC_ASSETS.'css/bootstrap-icons.css' ,
            false,
            HWP_CLUB_SCRIPTS_VERSION
        );
    }


    public static function croppieStyle( $dependencies = null )
    {
        wp_enqueue_style(
            'hwp_croppie_css' ,
            HWP_CLUB_PUBLIC_ASSETS.'css/croppie.2.6.4.min.css' ,
            $dependencies ,
            HWP_CLUB_SCRIPTS_VERSION
        );
    }

    public static function rangeSliderStyle()
    {
        wp_enqueue_style(
            'hwp_range_slider_css' ,
            HWP_CLUB_PUBLIC_ASSETS.'css/rangeslider.css' ,
            false,
            HWP_CLUB_SCRIPTS_VERSION
        );
    }

    public static function splideSliderStyle()
    {
        wp_enqueue_style(
            'hwp_splide_css' ,
            HWP_CLUB_PUBLIC_ASSETS.'css/splide.min.css' ,
            false,
            HWP_CLUB_SCRIPTS_VERSION
        );
    }


    public static function gravityStyle()
    {
        wp_enqueue_style(
            'hwp_gravity_css' ,
            HWP_CLUB_PUBLIC_ASSETS.'css/gravity.min.css' ,
            [] ,
            HWP_CLUB_SCRIPTS_VERSION ,
        );
    }

    public static function tomSelectStyle()
    {
        wp_enqueue_style(
            'tom_select_css' ,
            HWP_CLUB_PUBLIC_ASSETS . 'css/tom-select.css' ,
            false,
            HWP_CLUB_SCRIPTS_VERSION
        );
    }

    public static function indexStyle()
    {
        wp_enqueue_style(
            'hwp_index_css' ,
            HWP_CLUB_PUBLIC_ASSETS.'css/style.css' ,
            false,
            HWP_CLUB_SCRIPTS_VERSION
        );
    }


    public static function iziToastStyle()
    {
        wp_enqueue_style(
            'hwp_izi_toast_css' ,
            HWP_CLUB_PUBLIC_ASSETS.'css/izi-toast.css' ,
            false,
            HWP_CLUB_SCRIPTS_VERSION
        );
    }









}
