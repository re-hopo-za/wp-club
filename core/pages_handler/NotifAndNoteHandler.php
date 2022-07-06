<?php

namespace HwpClub\core\pages_handler;

use HwpClub\core\includes\NotifAndNote;
use HwpClub\resources\ui\FrontPartials;

class NotifAndNoteHandler
{


    public static function notifList( $userObject )
    {
        $items = '';
        if( !empty( $userObject->notifications ) ){
            $index = 1;
            foreach ( $userObject->notifications as $notif ){
                $item   = str_replace('[id]'    ,$index ,FrontPartials::notificationsListItem() );
                $item   = str_replace('[date]'  ,$notif->date_created ,$item );
                $items .= str_replace('[title]' ,$notif->title ,$item );
                $index++;
            }
        }
        if ( empty( $items ) ){
            $items = FrontPartials::notificationsEmptyItem();
        }
        NotifAndNote::updateNotification( $userObject->ID );
        return str_replace('[items]' ,$items ,FrontPartials::notificationsList() );
    }


    public static function notificationsCount( $userObject  )
    {
        if ( isset( $userObject->notifications ) ){
            $count = count( $userObject->notifications );
            if ( $count > 0 ){
                return str_replace('[count]' ,$count ,FrontPartials::notificationCount() );
            }
        }
        return '';
    }


    public static function notesList( $userObject )
    {
        return str_replace('[items]' ,self::notesItems( $userObject->ID ) ,FrontPartials::noteList() );
    }


    public static function notesItems( $userID )
    {
        $notes = NotifAndNote::getNotes( $userID);
        $items = '';
        if( !empty( $notes ) ){
            foreach ( $notes as $note ){
                $item   = str_replace('[id]'      ,$note->id ,FrontPartials::noteItem() );
                $item   = str_replace('[title]'   ,$note->title   ,$item );
                $items .= str_replace('[content]' ,$note->content ,$item );
            }
        }
        if ( empty( $items ) ){
            $items = FrontPartials::noteEmptyItem();
        }
        return $items;
    }







}
