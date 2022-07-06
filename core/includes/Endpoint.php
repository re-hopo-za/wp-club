<?php


namespace HwpClub\core\includes;



class Endpoint
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
        add_action('init' ,[$this ,'addRewrite'] ,99 );
        add_filter('theme_page_templates' ,[$this ,'addTemplate'] ,10 ,4 );
        add_filter('template_include' ,[$this ,'loadTemplate'] );
    }




    public function addTemplate( $post_templates ,$wp_theme ,$post ,$post_type )
    {
        $post_templates['ClubUrlHandler.php'] = 'Club';
        return $post_templates;
    }


    public function loadTemplate( $template ){
        if ( get_page_template_slug() === 'ClubUrlHandler.php') {
            if ( $theme_file = locate_template( ['ClubUrlHandler.php'] ) ) {
                $template = $theme_file;
            }else {
                $template = HWP_CLUB_UI .'ClubUrlHandler.php';
            }
        }
        return $template;
    }


    public function addRewrite(){
        add_rewrite_rule('club/([0-9]*)', 'index.php/club?club=$1');
    }

}


