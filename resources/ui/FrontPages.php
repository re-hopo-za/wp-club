<?php


namespace HwpClub\resources\ui;

use HwpClub\core\includes\Functions;
use HwpClub\core\pages_handler\AdminPageHandler;
use HwpClub\core\pages_handler\ChallengesHandler;
use HwpClub\core\pages_handler\CloseFriendsHandler;
use HwpClub\core\pages_handler\ContentPageHandler;
use HwpClub\core\pages_handler\MiscPageHandler;
use HwpClub\core\pages_handler\NotifAndNoteHandler;
use HwpClub\core\pages_handler\HomeHandler;
use HwpClub\core\pages_handler\PointsHandler;
use HwpClub\core\pages_handler\RegistrationHandler;
use HwpClub\core\pages_handler\UsersHandler;

class FrontPages
{


    public static function editUserInformationForm( $userObject )
    {
        return str_replace( '[content]' ,
            RegistrationHandler::editProfileForm( $userObject ) ,
            FrontPartials::root( $userObject ,'edit-user-page')
        );
    }


    public static function getUserPointHistory( $userObject )
    {
        return str_replace( '[content]' ,
            PointsHandler::getHistory( $userObject ) ,
            FrontPartials::root( $userObject ,'point-history') 
       );
    }

    public static function page404( $userObject )
    {
        return str_replace( '[content]' ,
            FrontPartials::page404() ,
            FrontPartials::root( $userObject ,'404')
        );
    }

    public static function home( $userObject )
    {
        return str_replace( '[content]' ,
            HomeHandler::home( $userObject ) ,
            FrontPartials::root( $userObject , 'home-page')
        );
    }

    public static function challengesList( $userObject )
    {
        return str_replace( '[content]' ,
            ChallengesHandler::list( $userObject ) ,
            FrontPartials::root( $userObject ,'challenges-list')
        );
    }

    public static function challengeSingle( $userObject ,$challengeID )
    {
        return str_replace( '[content]' ,
            ChallengesHandler::challengeSingle( $challengeID ,$userObject ) ,
            FrontPartials::root( $userObject ,'challenge-single')
        );
    }

    public static function challengeSingleComments( $userObject ,$challengeID )
    {
        return str_replace( '[content]' ,
            ChallengesHandler::challengeSingleComments( $challengeID ,$userObject ) ,
            FrontPartials::root( $userObject ,'challenge-single-comments')
        );
    }

    public static function challengeSingleUsers( $userObject ,$challengeID )
    {
        return str_replace( '[content]' ,
            ChallengesHandler::challengeSingleUsers( $challengeID ,$userObject ) ,
            FrontPartials::root( $userObject ,'challenge-single-users')
        );
    }


    public static function challengeSingleUser( $userObject ,$challengeID )
    {
        return str_replace( '[content]' ,
            ChallengesHandler::challengeSingle( $challengeID ,$userObject ) ,
            FrontPartials::root( $userObject ,'challenge-single-user')
        );
    }


    public static function users( $userObject ,$userID )
    {
        return str_replace( '[content]' ,
            UsersHandler::pageHandler( $userID ) ,
            FrontPartials::root( $userObject ,'users')
        );
    }


    public static function chat( $userObject )
    {
        return str_replace( '[content]' ,
            FrontPartials::chat($userObject) ,
            FrontPartials::root( $userObject ,'chat')
        );
    }

    public static function closeFriends( $userObject )
    {
        return str_replace( '[content]' ,
            CloseFriendsHandler::List( $userObject ) ,
            FrontPartials::root( $userObject ,'close-friends')
        );
    }
    
    public static function closeFriendSingle( $userObject ,$childPage )
    {
        return str_replace( '[content]' ,
            CloseFriendsHandler::closeFriendSingle( $userObject ,$childPage ) ,
            FrontPartials::root( $userObject ,'close-friend-single')
        );
    }

    public static function userSingle( $userObject ,$specificUser )
    {
        return str_replace( '[content]' ,
            HomeHandler::userSingle( $specificUser ) ,
            FrontPartials::root( $userObject ,'user-single')
        );
    }


    public static function creditConverter( $userObject )
    {
        return str_replace( '[content]' ,
            PointsHandler::creditConverter( $userObject ) ,
            FrontPartials::root( $userObject ,'credit-converter')
        );
    }

    public static function notifications( $userObject )
    {
        return str_replace( '[content]' ,
            NotifAndNoteHandler::notifList( $userObject ) ,
            FrontPartials::root( $userObject ,'notifications')
        );
    }

    public static function usePointsMethods( $userObject )
    {
        return str_replace( '[content]' ,
            MiscPageHandler::usePointsMethods( $userObject ) ,
            FrontPartials::root( $userObject ,'use-points-methods')
        );
    }

    public static function increasePoint( $userObject )
    {
        return str_replace( '[content]' ,
            MiscPageHandler::increasePoint( $userObject ) ,
            FrontPartials::root( $userObject ,'increase-point')
        );
    }


    public static function showcase( $userObject )
    {
        return str_replace( '[content]' ,
            MiscPageHandler::showcase( $userObject ) ,
            FrontPartials::root( $userObject ,'showcase')
        );
    }


    public static function notes( $userObject )
    {
        return str_replace( '[content]' ,
            NotifAndNoteHandler::notesList( $userObject ) ,
            FrontPartials::root( $userObject ,'notes')
        );
    }




    ////// Content Page //////

    public static function guideUpgradeRank( $userObject )
    {
        return str_replace( '[content]' ,
            ContentPageHandler::guideUpgradeRank( $userObject ) ,
            FrontPartials::root( $userObject ,'guide-upgrade-rank')
        );
    }

    public static function clubRules( $userObject )
    {
        return str_replace( '[content]' ,
            ContentPageHandler::clubRules( $userObject ) ,
            FrontPartials::root( $userObject ,'club-rules')
        );
    }

    public static function registerInHamyarCourses( $userObject )
    {
        return str_replace( '[content]' ,
            ContentPageHandler::registerInHamyarCourses( $userObject ) ,
            FrontPartials::root( $userObject ,'register-in-hamyar-courses')
        );
    }

    public static function buyConsultingServices( $userObject )
    {
        return str_replace( '[content]' ,
            ContentPageHandler::buyConsultingServices( $userObject ) ,
            FrontPartials::root( $userObject ,'buy-consulting-services')
        );
    }

    public static function consultingHajiMohamadi( $userObject )
    {
        return str_replace( '[content]' ,
            ContentPageHandler::consultingHajiMohamadi() ,
            FrontPartials::root( $userObject ,'consulting-haji-mohamadi')
        );
    }

    public static function convertPointToCreditDescription( $userObject )
    {
        return str_replace( '[content]' ,
            ContentPageHandler::convertPointToCreditDescription() ,
            FrontPartials::root( $userObject ,'convert-point-to-credit-description')
        );
    }

    public static function guideGetPoints( $userObject )
    {
        return str_replace( '[content]' ,
            ContentPageHandler::guideGetPoints() ,
            FrontPartials::root( $userObject ,'guide-get-points')
        );
    }

    public static function contactUs( $userObject )
    {
        return str_replace( '[content]' ,
            ContentPageHandler::contactUs() ,
            FrontPartials::root( $userObject ,'contact-us')
        );
    }




    public static function adminPanel( $userObject )
    {
        return str_replace( '[content]' ,
            AdminPageHandler::adminPage( $userObject ) ,
            FrontPartials::root( $userObject ,'admin-panel')
        );
    }
}







