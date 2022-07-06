<?php

namespace HwpClub\core\pages_handler;




use HwpClub\resources\ui\FrontPartials;

class ContentPageHandler
{

    public static function guideUpgradeRank( $userObject )
    {
        $post = self::getPostBySlug('guide-upgrade-rank');
        if (!empty( $post ) ){
            return $post['post']->post_content;
        }
        return false;
    }

    public static function clubRules( $userObject )
    {
        $post = self::getPostBySlug('club-rules');
        if (!empty( $post ) ){
            return $post['post']->post_content;
        }
        return false;
    }


    public static function homeAdds()
    {
        $post = self::getPostBySlug('home-adds');
        if (!empty( $post ) ){
            return $post['post'];
        }
        return false;
    }

    public static function registerInHamyarCourses()
    {
        $post = self::getPostBySlug('register-in-hamyar-courses');
        if (!empty( $post ) ){
            return $post['post']->post_content;
        }
        return false;
    }

    public static function buyConsultingServices()
    {
        $post = self::getPostBySlug('buyConsultingServices');
        if (!empty( $post ) ){
            return $post['post']->post_content;
        }
        return false;
    }

    public static function consultingHajiMohamadi()
    {
        $post = self::getPostBySlug('consulting-haji-mohamadi');
        if (!empty( $post ) ){
            return $post['post']->post_content;
        }
        return false;
    }

    public static function convertPointToCreditDescription()
    {
        $post = self::getPostBySlug('convert-point-to-credit-description');
        if (!empty( $post ) ){
            return $post['post']->post_content;
        }
        return false;
    }

    public static function guideGetPoints()
    {
        $post = self::getPostBySlug('guide-get-points');
        if (!empty( $post ) ){
            return $post['post']->post_content;
        }
        return false;
    }

    public static function contactUs()
    {
        $post = self::getPostBySlug('contact-us');
        if (!empty( $post ) ){
            $main = str_replace( '[description]' ,$post['post']->post_content ,FrontPartials::contactUsForm() );
            return  str_replace( '[form]' ,do_shortcode('[gravityform id="11" title="false" description="true" ajax="true"]' ) ,$main );
        }
        return '';
    }





    public static function getPostBySlug( $theSlug )
    {
        if( !empty( $theSlug ) ){
            $post = get_posts([
                'name'        => $theSlug  ,
                'post_type'   => 'post'    ,
                'post_status' => 'publish' ,
                'numberposts' => 1
            ]);
            if ( !is_wp_error( $post ) && !empty( $post ) ){
                return [
                    'post' => $post[0] ,
                    'meta' => get_post_meta( $post[0]->ID )
                ];
            }
        }
        return [];
    }



}