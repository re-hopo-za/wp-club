<?php

namespace HwpClub\core\includes;

use DateTime;
use HwpClub\core\pages_handler\ChallengesHandler;
use stdClass;

class Forms
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
        add_filter('gform_field_content_1_26'     ,[$this ,'gravityFormFieldContent'] ,10 ,5 );
        add_filter('gform_pre_replace_merge_tags' ,[$this ,'gravityFormReplaceTags']  ,10 ,7 );

        add_action( 'wp_ajax_handler_user_activity' ,[$this ,'userActivity']);
        add_action( 'gform_after_submission'        ,[$this ,'calculateChallengeFormScoreOnGravitySubmit' ] ,10, 2 );

        add_action( 'ipt_fsqm_hook_save_success' ,[$this ,'showChallengeFormId_8']     , 10, 1);
        add_action( 'ipt_fsqm_hook_save_success' ,[$this ,'showChallengeFormId_9']     , 10, 1);
        add_action( 'ipt_fsqm_hook_save_success' ,[$this ,'showChallengeFormId_10']    , 10, 1);
        add_action( 'ipt_fsqm_hook_save_success' ,[$this ,'showChallengeFormId_13']    , 10, 1);
        add_action( 'ipt_fsqm_hook_save_success' ,[$this ,'addFormDescriptionOneForm'] , 10, 1);
        add_action( 'ipt_fsqm_hook_save_success' ,[$this ,'calculateChallengeFormScoreOnEFormSubmit' ] ,10, 2 );
    }

    public function addFormDescriptionOneForm( $form ){
        $form->settings['submission']['success_message'] .= self::descriptionsButton( (int) $form->name );
    }


    public function showChallengeFormId_13( $data ){
        $form_id = $data->form_id;
        if($form_id!==13) return ;
        ob_start();
        self::shareScripts( $data );
        $data->settings['submission']['success_message'] .= ob_get_clean();
    }

    public function showChallengeFormId_8( $data ){
        $form_id = $data->form_id;
        if($form_id!==8) return ;
        ob_start();
        ?>
        <script>
            let arrResult = [
                Number.parseInt(document.getElementById("EI").innerHTML) ,
                Number.parseInt(document.getElementById("SN").innerHTML) ,
                Number.parseInt(document.getElementById("TF").innerHTML) ,
                Number.parseInt(document.getElementById("JP").innerHTML)
            ];
            document.getElementById("MBTI").innerHTML = (arrResult[0] > 0 ? 'E' : 'I')+(arrResult[0] > 0 ? 'S' : 'N')+(arrResult[0] > 0 ? 'T' : 'F')+(arrResult[0] > 0 ? 'J' : 'P');
        </script>
        <?php
        $data->settings['submission']['success_message'] .= ob_get_clean();
    }


    public function showChallengeFormId_9( $data ){
        if( !isset( $data->form_id ) || $data->form_id !== 9 )return;
        ob_start();
        ?>
        <div id="form-id-9"> </div>
        <script>
            let arr = [
                Number.parseFloat(document.getElementById("1-TF").innerHTML),
                Number.parseFloat(document.getElementById("2-GM").innerHTML),
                Number.parseFloat(document.getElementById("3-AU").innerHTML),
                Number.parseFloat(document.getElementById("4-SE").innerHTML),
                Number.parseFloat(document.getElementById("5-EC").innerHTML),
                Number.parseFloat(document.getElementById("6-SV").innerHTML),
                Number.parseFloat(document.getElementById("7-CH").innerHTML),
                Number.parseFloat(document.getElementById("8-LS").innerHTML)
            ];
            var chart = Highcharts.chart('form-id-9',  {
                chart: {
                    type: 'column' ,
                    events: {}
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: 'نمودار پرسشنامه لنگرگاه‌های مسیر شغلی'},
                xAxis: {
                    categories: ['لنگر فنی-تکنیکی','لنگر مدیریت عمومی','•  لنگر خودمختاری و استقلال','•  لنگر امنیت و ثبات','•  لنگر خلاقیت و کارآفرینی','•  لنگر خدمت و تعهد','•  لنگر جستجوی چالش','•  لنگر سبک زندگی']
                },
                yAxis: {min: 0, max: 30, endOnTick:false , tickInterval: 1, allowDecimals: false, title: {text: 'نمره'}} ,
                legend: {enabled: false} ,
                plotOptions: {
                    series  : { borderWidth: 0, dataLabels: {enabled: true, format: '{point.y}%'} } },
                tooltip : { pointFormat: ' <b>{point.y}</b><br/>', shared: true}, series: [{data: arr, name: null}]
            });
        </script>
        <?php
        self::shareScripts( $data );
        $data->settings['submission']['success_message'] .= ob_get_clean();
    }


    public function showChallengeFormId_10($data)
    {
        if( !isset( $data->form_id ) || $data->form_id !== 10 )return;
        ob_start();
        ?>
        <div id="form-id-10"> </div>
        <script>
            let arr = [
                Number.parseFloat(document.getElementById("1-PS").innerHTML),
                Number.parseFloat(document.getElementById("2-HA").innerHTML),
                Number.parseFloat(document.getElementById("3-IN").innerHTML),
                Number.parseFloat(document.getElementById("4-ST").innerHTML),
                Number.parseFloat(document.getElementById("5-RT").innerHTML),
                Number.parseFloat(document.getElementById("6-ES").innerHTML),
                Number.parseFloat(document.getElementById("7-IR").innerHTML),
                Number.parseFloat(document.getElementById("8-OP").innerHTML),
                Number.parseFloat(document.getElementById("9-SR").innerHTML),
                Number.parseFloat(document.getElementById("10-IC").innerHTML),
                Number.parseFloat(document.getElementById("11-SA").innerHTML),
                Number.parseFloat(document.getElementById("12-FL").innerHTML),
                Number.parseFloat(document.getElementById("13-RE").innerHTML),
                Number.parseFloat(document.getElementById("14-EM").innerHTML),
                Number.parseFloat(document.getElementById("15-AS").innerHTML)
            ];
            Highcharts.chart('form-id-10', {
                chart: {type: 'column'},
                title: {text: 'نمودار آزمون هوش هیجانی'},
                xAxis: {categories: ['حل مسئله', 'شادمانی', 'استقلال', 'تحمل فشار روانی', 'خودشکوفایی', 'خودآگاهی هیجانی', 'واقع‌گرایی', 'روابط بین فردی', 'خوش‌بینی', 'احترام به خود', 'خویشتن‌داری', 'انتعطاف‌پذیری', 'مسئولیت‌پذیری اجتماعی', 'همدلی', 'خودابرازی']},
                yAxis: {min: 0, max: 30, endOnTick: false, tickInterval: 1, allowDecimals: false, title: {text: 'نمره'}},
                legend: {enabled: false},
                plotOptions: {series: {borderWidth: 0, dataLabels: {enabled: true, format: '{point.y}%'}}},
                tooltip: {pointFormat: ' <b>{point.y}</b><br/>', shared: true},
                series: [{data: arr, name: null}]
            })
        </script>
        <?php
        self::shareScripts( $data );
        $data->settings['submission']['success_message'] .= ob_get_clean();
    }


    public static function addMediaEntry( $userID ,$challengeID )
    {
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix.'club_users_activity_records',
            [
                'user_id'      => $userID ,
                'challenge_id' => $challengeID ,
                'date_created' => date('Y-m-d H:i:s') ,
                'agent'        => $_SERVER['HTTP_USER_AGENT'] ,
                'uniq_id'      => $challengeID.$userID ,
            ],[
                '%d','%d','%s','%s','%s','%d',
            ]
        );
        if ( !is_wp_error( $result ) && !empty( $result ) ){
            return $wpdb->insert_id;
        }
        return false;
    }


    public static function checkUserActivityFromDB( $challengeID ,$userID )
    {
        global $wpdb;
        $table   = $wpdb->prefix.'club_users_activity_records';
        $results = $wpdb->get_results(
            "SELECT * FROM {$table} WHERE challenge_id = {$challengeID} AND user_id = {$userID} ;"
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ){
            return $results;
        }
        return [];
    }

    public static function userActivity()
    {
        if ( Functions::checkNonce( $_GET['nonce'] ,'club_nonce' ) && isset( $_GET['challenge_id'] ) ){
            $user_object    = Users::getUserObject();
            $challenge_id   = Functions::decryptID( $_GET['challenge_id'] );
            $user_meta      = $user_object->challenges;
            $challenge_meta = Challenges::getSingleChallengeMeta( $challenge_id );


            if ( $_GET['type'] == 'text' ){
                $length = (int) $challenge_meta['challenge_daily_text'];
                $type_desc = 'read text type challenge';
                $type_title = 'Text';

            }else if( $_GET['type'] == 'media' ){
                $length = (int) $challenge_meta['challenge_media_src'];
                $type_desc = 'watch or listen media type challenge';
                $type_title = 'Media';

            }else {
                $length = (int) $challenge_meta['challenge_length'];
                $type_desc = 'see view type';
                $type_title = 'View';
            }

            if ( $challenge_id > 0 && in_array( $challenge_id ,$user_meta->active ) ){
                $activity  = Forms::checkUserActivityFromDB( $challenge_id ,$user_object->ID  ,false );
                $last_item = !empty( $activity ) ? end( $activity ) : [];
                $row_count = count( $activity );
                if ( ChallengesHandler::challengeMetaStatus( $challenge_meta ,$user_object ,true ) ){
                    if ( !empty( $last_item ) ){
                        if ( isset( $last_item->date_created ) ){
                            date_default_timezone_set('Asia/Tehran');
                            date_default_timezone_set('Asia/Tehran');
                            $date = new DateTime( $last_item->date_created );
                            $date->modify('+1 day');
                            if ( $date->getTimestamp() < strtotime( 'now' ) ){
                                $row_count = count( $activity ) + 1;
                                if ( $row_count <= $length ){
                                    Forms::addMediaEntry( $user_object->ID ,$challenge_id );
                                }
                            }
                        }
                    }else{
                        $row_count = 1;
                        Forms::addMediaEntry( $user_object->ID ,$challenge_id );
                    }
                    if ( $row_count >= $length ){
                        $user_details = Points::getUserDetails( $user_object->ID );
                        $params = new stdClass();
                        $params->title = $type_title;
                        $params->challenge_id = $challenge_id;
                        $params->descriptions = $type_desc;
                        $params->rank = Ranks::getRank( $user_details->has + Functions::indexChecker( $challenge_meta ,'challenge_reach_points' ,0 ) )['slug'];
                        $params->type = 'credit';
                        $params->amount = Functions::indexChecker( $challenge_meta ,'challenge_reach_points' ,0 );
                        $params->user_id = $user_object->ID;
                        $params->added_by = $user_object->ID;
                        $params->challenge_id = $challenge_id;
                        Points::addPoint( $user_object->ID ,$params );
                        Challenges::updateUserMetaChallenge( $user_object->ID ,Challenges::updateUserChallengeOnCompleted( $user_object->ID ,$challenge_id ),false );
                        wp_send_json(['result' => 'completed']);
                    }
                }
            }
            wp_send_json(['result' => '' ]);
        }
        wp_send_json(['result' => 'n_effected']);
    }


    public static function getUsersByTheirActivity()
    {
        global $wpdb;
        $table = $wpdb->prefix.'club_users_activity_records';
        $results = $wpdb->get_results(
            "SELECT uniq_id ,MAX(id) id FROM {$table} WHERE notification_status = 0 GROUP BY uniq_id;"
        );
        if ( !is_wp_error( $results ) && !empty( $results ) ){
            $IDs = array_column( $results ,'id' );
            $results = $wpdb->get_results(
                "SELECT * FROM {$table} WHERE id IN (".implode( ',' ,$IDs ).")
                       AND  DATE_FORMAT( `date_created` ,'%Y%m%d') < DATE_FORMAT( DATE( NOW() - INTERVAL 1 DAY ) ,'%Y%m%d') LIMIT 100;"
            );
            if ( !is_wp_error( $results ) && !empty( $results ) ){
                return $results;
            }
        }
        return [];
    }


    public static function updateStatus( $data ,$format ,$uniq_id )
    {
        if ( is_numeric( $uniq_id ) && is_array( $data ) ){
            global $wpdb;
            $wpdb->update(
                $wpdb->prefix.'club_users_activity_records' ,
                $data   ,
                [ 'uniq_id' => $uniq_id ]  ,
                $format ,
                [ '%d' ]
            );
            if ( is_wp_error( $wpdb ) ){
                return true;
            }
        }
        return false;
    }


    public static function calculateChallengeFormScoreOnGravitySubmit( $entry ,$form )
    {
        $form_id = $entry['form_id'];
        if ( $form_id > 0 ){
            $challenge_id   =(int) $form['title'];
            $challenge_meta = Challenges::getSingleChallengeMeta( $challenge_id );
            $user_object    = Users::getUserObject();
            $challenge      = get_post( $challenge_id );
            if ( is_numeric( $form_id ) && ChallengesHandler::challengeMetaStatus( $challenge_meta ,$user_object ,true ) ){
                if ( count( Challenges::checkFormOnDb( 'gf_entry' ,$form_id ,$user_object->ID ,false ) ) >= $challenge_meta['challenge_length'] ){
                    if ( isset( $challenge_meta['challenge_reach_points'] ) && (int) $challenge_meta['challenge_reach_points'] > 0 ){
                        $amount = (int) $challenge_meta['challenge_reach_points'];
                        $user_details = Points::getUserDetails( $user_object->ID );
                        $params = new stdClass();
                        $params->title = $challenge->post_title;
                        $params->challenge_id = $challenge_id;
                        $params->descriptions = $challenge->post_excerpt;
                        $params->rank = Ranks::getRank( $user_details->reach + $amount )['slug'];
                        $params->type = 'credit';
                        $params->amount = $amount;
                        $params->user_id = $user_object->ID;
                        $params->added_by = $user_object->ID;
                        Points::addPoint( $user_object->ID ,$params );
                    }
                    Challenges::updateUserMetaChallenge( $user_object->ID ,Challenges::updateUserChallengeOnCompleted( $user_object->ID ,$challenge_id ),false);
                }
            }
        }
    }


    public static function calculateChallengeFormScoreOnEFormSubmit( $form )
    {
        $form_id        = $form->form_id;
        $challenge_id   = (int) $form->name;
        $user_object    = Users::getUserObject();
        $challenge      = get_post( $challenge_id );
        $challenge_meta = Challenges::getSingleChallengeMeta( $challenge_id );
        if ( is_numeric( $form_id ) && ChallengesHandler::challengeMetaStatus( $challenge_meta ,$user_object ,true ) ){
            if ( count( Challenges::checkFormOnDb( 'fsq_data' ,$form_id ,$user_object->ID ,false ) ) >= $challenge_meta['challenge_length'] ){
                if ( isset( $challenge_meta['challenge_reach_points'] ) && (int) $challenge_meta['challenge_reach_points'] > 0 ){
                    $amount = (int) $challenge_meta['challenge_reach_points'];
                    $params = new stdClass();
                    $params->title = $challenge->post_title;
                    $params->challenge_id = $challenge_id;
                    $params->descriptions = $challenge->post_excerpt;
                    $params->rank = Ranks::getRank( $user_object->reach + $amount )['slug'];
                    $params->type = 'credit';
                    $params->amount = $amount;
                    $params->user_id = $user_object->ID;
                    $params->added_by = $user_object->ID;
                    Points::addPoint( $user_object->ID ,$params );
                }
                Challenges::updateUserMetaChallenge( $user_object->ID ,Challenges::updateUserChallengeOnCompleted( $user_object->ID ,$challenge_id ),false );
            }
        }
    }


    public static function descriptionsButton( $challengeID )
    {
        if ( is_numeric( $challengeID ) ){
            $meta = ChallengesHandler::descriptionsButton( $challengeID );
            return
                '<div class="challenge-desc">
                    '.$meta.'
                </div>
        ';
        }
        return '';
    }

    public static function shareScripts( $data )
    {
        if ( !empty( $data ) && isset( $data->name ) && is_numeric( $data->name ) ){
            $challengeID  = $data->name;
            $all_meta  = Challenges::getChallengesMeta( [$challengeID] );
            $meta      = $all_meta[$challengeID];
            $challenge = Challenges::getSingleChallenge( $challengeID );
            $title     = $challenge->post_title;
            $desc      = Functions::indexChecker( $meta ,'challenge_share_description' );
            ?>
                <script>
                    let title = '<?php echo $title; ?>';
                    let desc  = '<?php echo $desc; ?>';
                    let svgString = chart.getSVG();
                    let share_button = document.getElementById("single-challenge-share");
                    share_button.style.display = 'block';
                    let challenge_id = share_button.getAttribute('data-challenge-id');
                    share_button.addEventListener( "click" ,function(e){
                        share( svgString ,title ,desc );
                    } ,false );
                    async function share( svgString ,title ,text ) {
                        svgString2Image(svgString, 800, 600, 'png' ,async function ( pngData ) {
                            try {
                                const blob = await (await fetch(pngData)).blob();
                                const file = new File([blob], 'fileName.png', { type: blob.type });
                                await navigator.share({
                                    title: title,
                                    text: text,
                                    files: [file],
                                    url: window.location.href
                                })
                            }catch (error) {
                                console.log( error );
                            }
                        });
                    }
                    function svgString2Image( svgString ,width ,height ,format ,callback ) {
                        format = format ? format : 'png';
                        let svgData = 'data:image/svg+xml;base64,' + btoa( unescape( encodeURIComponent( svgString ) ) );
                        let canvas = document.createElement('canvas');
                        let context = canvas.getContext('2d');
                        canvas.width = width;
                        canvas.height = height;
                        let image = new Image();
                        image.onload = function () {
                            context.clearRect(0, 0, width, height);
                            context.drawImage(image, 0, 0, width, height);
                            let pngData = canvas.toDataURL('image/' + format);
                            callback( pngData );
                        };
                        image.src = svgData;
                    }
                </script>
            <?php
        }
    }


    public function gravityFormFieldContent( $field_content ,$field ,$value ,$zero ,$form_id )
    {
        return str_replace("type='file'", 'type="file" accept="image/*"' ,$field_content );
    }


    public function gravityFormReplaceTags( $text ,$form ,$lead ,$url_encode ,$esc_html ,$nl2br ,$format )
    {
        if ( !empty( $text ) && strpos( $text, '{point}' ) !== false ) {
            $script    = '<script>setTimeout( function(){ window.location.reload(); },5000 ); </script>';
            $old_point = Points::getUserDetails( get_current_user_id() );
            if ( $old_point !== '' ) {
                $old_point = json_decode( $old_point );
                return str_replace('{point}', $old_point->has . $script ,$text );
            }
        }
        return $text;
    }






}