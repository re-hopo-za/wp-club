<?php


namespace HwpClub\resources\ui;


use HwpClub\core\includes\Functions;
use HwpClub\core\includes\Users;

class FrontPartials
{


    /////===---- user information edit form ----===/////


    public static function editUserInformationForm()
    {
        return
            '<form  enctype="multipart/form-data" action="" method="post" id="save_img">[nonce]</form>
            <form class="edit-profile" id="edit-profile" action="#" method="GET" novalidate >
                <div>
                    <label for="image">تصویر</label>
                    <div class="image-con" id="change-profile-image">
                        [change-image]
                    </div>
                </div>
                <div>
                    <label for="name">نام</label>
                    <input id="input-name" type="text" placeholder="نام" required dir="auto" value="[name]" >
                </div>
                <div>
                    <label for="username"> نام کاربری</label>
                    <input id="input-username" type="text" placeholder="نام" required dir="auto" value="[username]" data-status="200" >
                    <div class="username-status-con">
                        <span id="username-status-text"> </span>
                        <div id="username-loading-con"></div>
                    </div>
                </div>
                <div>
                    <label for="instagram">شناسه اینستاگرام</label>
                    <input id="input-instagram" type="text" placeholder="12" dir="auto"  value="[instagram]" >
                </div> 
                <div>
                    <label for="biography">بیوگرافی </label> 
                    <textarea id="input-biography" type="text" placeholder="بیوگرافی">[biography]</textarea>
                </div>
                <div class="private-con">
                    <label for="input-private">خصوصی کردن اطلاعات </label>
                    <input id="input-private" type="checkbox" value="yes" [private] >
                </div>
                <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center" type="button" id="profile-saver">
                    '. Icons::arrowLeft() .'
                ذخیره
                </button>
            </form>
        ';
    }


    public static function profileAvatarSection()
    {
        return

            '<div id="hfl-user-panel" class="hfl-main-body">
                <p class="text-center">
                    <input id="input_image_my_account" width="96" height="96" name="profile_pic" class="change_profile_image"
                           type="file" style="display: none"/>
                    <span class="avatar"> 
                        <span id="change_profile_image">
                              [profile-image]
                        </span>
                    </span>
                </p> 
            </div>';
    }

    public static function showCasePage()
    {
        return
            '[profile]
            <div class="show-case-page">
                <div class="header">
                    '.Icons::chart().'
                    <h5> ویترین </h5>
                </div>
                
                <div class="ordering-by-score">  
                    <h5>رتبه بندی امتیازات</h5>
                    <div class="rank-mate-on-month"> 
                         [showCaseActiveUsersByRankMate]
                    </div>
                    <div class="active-users-on-month">
                         [showCaseActiveUsersOnMonth]
                    </div>  
                </div> 
                [showCaseNewUsersOnClub]
                [showCaseChallengeMate] 
            </div> 
        ';
    }

    public static function showCaseNewUsersOnClub()
    {
        return
            '<div class="ordering-by-new">  
                <div class="rank-mate-on-month"> 
                     [list]
                </div>  
            </div>
        ';
    }


    public static function showCaseChallengeMate()
    {
        return
            '<div class="ordering-by-rank-mate">  
                <div class="rank-mate-on-month"> 
                     [list]
                </div> 
                <div class="actions">
                    <a href="'.home_url('club/close-friend/challenge-mate/').'">
                      جزییات بیشتر
                    </a> 
                </div>
            </div>
        ';
    }


    public static function showCasePageItem()
    {
        return
            '<div class="show-case-page-item">
                <h5>[title]</h5>
                <div>  
                    [items]
                </div> 
            </div> 
        ';
    }

    public static function showCasePageItemActives()
    {
        return
            '<div class="show-case-page-item">
                <div class="top">
                    <h5>[title]</h5> 
                    <a href="[more]"> بیشتر </a>
                </div>
                <div>  
                    [items]
                </div> 
            </div> 
        ';
    }

    public static function pointsHistory()
    {
        return
            '[profile]
            <div class="points-list ">
                <div class="header">
                    '.Icons::chart().'
                    <h5 class="point-header">تاریخچه تراکنش امتیازات</h5>
                </div>
                <ul>
                   [items]
                </ul>
            </div>
        ';
    }

    public static function noPointsHistory()
    {
        return
            '<li class="[class-type]">
                <p class="title">موردی یافت نشد </p> 
                <div class="action">
                    <a href="'.home_url('/club/challenges-list/').'">لیست چالش ها</a>
                </div>
            </li>
        ';
    }

    public static function pointsHistoryRow()
    {
        return
            '<li class="[class-type]">
                <p class="title">[title]</p>
                <div class="details"> 
                    <p class="added-date"> 
                        <span> تاریخ ثبت</span> 
                        <span>[added-date]</span> 
                    </p>
                    <p class="score"> 
                        <span> نوع</span> 
                        <span class="[class-type]">[type]</span>
                    </p>
                    <p class="cost"> 
                        <span>مقدار </span> 
                        <span>[amount]</span>
                    </p> 
                    <p class="expire-date">
                        <span> تاریخ اعتبار</span> 
                        <span>[expire-date]</span> 
                    </p> 
                </div>
                [challenge-button] 
            </li>
        ';
    }

    public static function pointsHistoryChallengeButton()
    {
        return
            '<div class="action">
                    <a href="[single-url]">مشاهده چالش</a>
                </div>
        ';
    }

    public static function challengesMenu()
    {
        return
            '<ul class="nav nav-tabs challenges-menu-list" id="bootstrapTab" role="tablist">
                <li class="nav-item me-2" role="presentation">
                    <button class="nav-link [active-available]" id="available-tab" data-bs-toggle="tab" data-bs-target="#available" type="button" role="tab" aria-controls="available" aria-selected="true">چالش ها </button>
                </li>
                <li class="nav-item me-2 " role="presentation">
                    <button class="nav-link [active-active]" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="false"> فعال </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link [active-completed]" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">تکمیل شده</button>
                </li> 
            </ul> 
            <div class="tab-content border-top-0" id="bootstrapTabContent"> 
                <div class="tab-pane fade [active-available]" id="available" role="tabpanel" aria-labelledby="available-tab"> 
                    [other]
                </div>
                <div class="tab-pane fade [active-active]" id="active" role="tabpanel" aria-labelledby="active-tab"> 
                    [active]
                </div>
                <div class="tab-pane fade [active-completed]" id="completed" role="tabpanel" aria-labelledby="completed-tab"> 
                    [completed]
                </div> 
            </div> 
        ';
    }


    public static function challengesListContainer()
    {
        return
            '<div class="tab-content border-top-0 accordion accordion-style-three challenges-list" id="bootstrapTabContent"> 
                [items]
            </div>
        ';
    }

    public static function challengesListItem()
    {
        return
            '<div class="challenge-item [challenge-status]" id="[challenge-id]"> 
                <div class="top">
                    <div class="title">
                        <a href="[link]">
                            [title]
                        </a>
                    </div>
                    <div class="details">
                        <div class="top-right">
                            <p>
                                پاداش
                            </p>
                            <strong class="score"> <span>[score]</span>  <span> امتیاز</span></strong>  
                        </div>
                        <div class="top-center">
                            <p>
                                هزینه
                            </p>
                            [cost]
                        </div>
                        <div class="top-left">
                            [thumbnail]
                        </div>
                    </div>
                </div>
                <div class="middle">
                    [last-users]
                </div>
                <div class="bottom">
                    <section class="buttons challenge-action-button"> 
                        <button class="more-details" > جزئیات بیشتر </button>
                        [action-button]
                    </section> 
                    <div class="accordion-content">
                        <div class="challenge-progress-bar"> 
                            [progress-bar]
                        </div>
                        <div class="challenge-chat-button">
                            <button class="join-chat"> اتاق گفتگوی شرکت کنندگان </button>
                        </div> 
                        [content]  
                    </div>
                </div> 
            </div> 
        ';
    }


    public static function challengeTabsCon()
    {
        return
            '<div class=" challenge-tab-container">
                <ul class="nav mb-2 p-2"  role="tablist">
                    [menus] 
                </ul>
                <div class="tab-content p-3">
                    [contents] 
                </div>
            </div> 
        ';
    }


    public static function activityProgressBar()
    {
        return
            '<div class="bottom-top">
                <p>میزان مشارکت</p>
                <p> 
                    [act-count] 
                    <span>روز</span>
                </p>
            </div>
            <div class="progress-line">
                <progress value="[act-count]" max="[challenge-length]"> </progress>
            </div> 
        ';
    }


    public static function challengeTabsMenu()
    {
        return
            '<li class="nav-item" role="presentation">
                <button class="btn [active]" id="[id]-tab" data-bs-toggle="tab" data-bs-target="#[id]" type="button" role="tab" aria-controls="bootstrap" aria-selected="false">
                    [title]
                </button>
            </li>
        ';
    }

    public static function challengeTabsContent()
    {
        return
            '<div class="tab-pane fade [active]" id="[id]" role="tabpanel" aria-labelledby="[id]-tab"> 
                <div class="mb-0"> [content] </div>
            </div>
        ';
    }


    public static function challengeCommentContainer()
    {
        return
            '<div class="club-challenge-comments-list">
                <div class="comments-header">
                    <h4> نظرات </h4>
                </div>
                <div class="comments-form">
                    [comment-form]
                </div>
                <div class="comments-list">
                    [comment-list]
                </div>
            </div>
        ';
    }

    public static function noComment()
    {
        return
            '<div class="no-comments">
                <p> نظری برای این چالش یافت نشد </p></p>
            </div>
        ';
    }

    public static function commentForm()
    {
        return
            '<div class="comment-form"> 
                <div id="comment-form" data-challenge-id="[challenge-id]" > 
                    <div class="comment-form-element"> 
                        <textarea id="comment-field" placeholder="متن نظر " ></textarea>
                    </div>
                    <div class="btn">
                        <button class="form-submit-button" id="form-submit-button" > ارسال </button> 
                    </div> 
                </div>
            </div>
        ';
    }

    public static function commentField()
    {
        return
            '<p class="comment-form-comment">
                <label for="comment"> کامنت </label> <br/>
                <textarea id="comment" name="comment" aria-required="true"></textarea>
            </p>
        ';
    }


    public static function challengeUsersItem()
    {
        return
            '<div id="[user-id]">
                <div class="shadow-sm user-body"> 
                    <div class="right">
                       [avatar] 
                    </div>
                    <div class="left"> 
                        <span class="name">
                            [name]
                        </span>
                        <span class="score">
                            [score]
                        </span>  
                        <span class="rank">
                            [rank]
                        </span>  
                    </div>
                </div>
            </div>
        ';
    }

    public static function challengeCommentsItem()
    {
        return
            '<div id="[comment-id]">
                <div class="shadow-sm comment-body"> 
                    <div class="top">
                       [avatar]
                        <div>
                            <span class="name">
                                [name]
                            </span>

                        </div>
                    </div>
                    <div class="bottom">
                        <div class="content">
                            [content]
                        </div>
                        <div class="date">
                           [date]
                        </div> 
                    </div>
                </div>
            </div>
        ';
    }

    public static function lastUsersRegistered()
    {
        return
            '<section class="last-users"> 
                <h5> آخرین شرکت کنندگان</h5>
                <div>
                  [last-users]
                </div>
            </section>
        ';
    }

    public static function lastUsersRegisteredItem()
    {
        return
            '<a href="[user-link]">
                <div class="avatar">
                    [avatar]
                </div>  
            </a>
        ';
    }

    
    public static function challengeRegisterButton()
    {
        return
            '<button>
                <svg class="bi bi-cursor me-2" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                    <path fill-rule="evenodd" d="M14.082 2.182a.5.5 0 0 1 .103.557L8.528 15.467a.5.5 0 0 1-.917-.007L5.57 10.694.803 8.652a.5.5 0 0 1-.006-.916l12.728-5.657a.5.5 0 0 1 .556.103zM2.25 8.184l3.897 1.67a.5.5 0 0 1 .262.263l1.67 3.897L12.743 3.52 2.25 8.184z"></path>
                </svg> ثبت نام
            </button>
        ';
    }


    public static function challengeSingleCompleteButton( $challengeID )
    {
        return
            '<a class="open-challenge-single" href="'.home_url().'/club/challenge-single/'.$challengeID.'">
                تکمیل شده
            </a>';
    }


    public static function challengeSingleButton( $challengeID )
    {
        return
            '<a class="open-challenge-single" href="'.home_url().'/club/challenge-single/'.$challengeID.'">
                باز کردن
            </a>';
    }


    public static function challengeSingleStatusOnButton( $challengeStatus )
    {
        return
            '<p class="disable-challenge-single">
                '. $challengeStatus .'
            </a>';
    }


    public static function challengeSingle()
    {
        return
            '<div class="single-root">
                <div class="header"> 
                    <div class="details">
                        <h5>[title]</h5>
                        <div class="scores">
                            <p class="get-score">
                                <span class="rank">
                                    پاداش
                                </span>
                                <strong>
                                    [get-score]
                                </strong>
                            </p>
                            <p class="spend-score">
                                <span class="rank">
                                    هزینه
                                </span>
                                <strong>
                                    [spend-score]
                                </strong> 
                            </p>
                        </div>
                    </div>
                    <div class="cover">
                       [cover]
                    </div>  
                </div>
                <div class="action">
                    [action] 
                </div>
                <div class="tabs">
                    [tabs] 
                </div>
                <div class="top-buttons">
                    [top-button] 
                </div> 
                <div class="bottom-buttons">
                    [bottom-button] 
                </div> 
            </div>
        ';
    }


    public static function notAllowSingle()
    {
        return
            '<div class="single-root not-allow">
                <div class="header"> 
                    <div class="details">
                        <h5 class="alert-title-text"> عدم دسترسی</h5>  
                        <div class="scores">
                            <p class="get-score">
                                <span class="rank">
                                     پاداش
                                </span>
                                <strong>
                                    [get-score]
                                </strong>
                            </p>
                            <p class="spend-score">
                                <span class="rank">
                                    هزینه
                                </span>
                                <strong>
                                    [spend-score]
                                </strong> 
                            </p>
                        </div>
                    </div>
                    <div class="cover">
                       [cover]
                    </div>   
                </div> 
                <div class="action alert-text">
                    <strong>
                        شما هنوز در این چالش  ثبت نام نکرده اید
                    </strong>
                </div> 
            </div>
        ';
    }

    public static function challengeFormDescriptionsButton()
    {
        return
            '<button class="form-description-button" >
                <h5>[title]</h5>
                <section>[content]</section>
            </button>
        ';
    }

    public static function challengeSingleVideoPlayer()
    {
        return
            '<div class="single-video-con">
                <video id="hwp-club-video-watcher" width="100%" height="auto">
                    <source src="[link]" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <div class="hwp-club-video-watcher">
                    <button class="start" id="video-handler"> start </button> 
                    <button class="reset" id="video-reset"> restart </button> 
                </div> 
            </div> 
        ';
    }


    public static function challengeSingleAudioPlayer()
    {
        return
            '<div class="single-audio-con">
                <audio id="hwp-club-audio-watcher" >
                    <source src="[link]" type="audio/ogg">
                    Your browser does not support the audio element.
                </audio>
                <div class="hwp-club-audio-watcher">
                    <button class="start" id="audio-handler"> start </button>
                    <button class="reset" id="audio-reset"> restart </button>
                </div>
            </div> 
        ';
    }


    public static function homePage()
    {
        return
            '<div class="top-container">
                [userDetails]
                [scoreTimeline]
                [scoreNeedToNextRank]
                [scoreHandlerButtons]   
            </div>
            <div class="bottom-container" > 
                [homeButtons]
                [homeAdds]
                [lastChallenges]
                [showCase]
            </div>
        ';
    }

    public static function showCase()
    {
        return
            '<div class="show-case-con">
                <h5> ویترین </h5>
                <div class="show-case-items">
                    [activeUsers] 
                    [newUsers] 
                    [challengeMate] 
                    [rankMate] 
                </div>
            </div> 
        ';
    }

    public static function showCaseItem( $class )
    {
        return
            '<div class="'.$class.'">
                <h6>[title]</h6>
                <div>
                    [items]
                </div> 
            </div>
        ';
    }

    public static function usersListPage()
    {
        return
            '<div class="users-list">
                <div class="header">
                    '.Icons::user().'
                    <h5> کاربران </h5>
                </div> 
                <div class="list">   
                    [users]  
                </div>  
            </div>
        ';
    }

    public static function usersListPageItem()
    {
        return
            '<a href="[url]"> 
                <div class="profile">
                    [profile]
                </div>
                <div class="name">
                    <p>[name]</p>
                </div>
                <div class="score-progress">
                    <div style="width: [score]%"></div>
                </div>
            </a>
        ';
    }

    public static function userSingle()
    {
        return
            '<div class="user-single">
                <div class="header">
                    '.Icons::user().'
                    <h5> کاربران </h5>
                </div> 
                <div class="single">   
                    <div id="home-profile" class="home-profile-con" > 
                        <div class="user-profile">
                            <a href="javascript:void(0)" class="avatar-container">
                               [avatar]
                            </a>
                            <div class="user-info">
                                <h3>[name]</h3>
                                 <span data-title="طبقه">
                                     [rank]
                                 </span>
                            </div>
                            <div class="score-handle">
                                <a class="show-score" href="javascript:void(0)">
                                   <span> [has] </span>
                                   <span> امتیاز </span>
                                   '.Icons::smile().'
                                </a> 
                            </div> 
                        </div>  
                    </div> 
                    <div class="timeline-con">
                        <div class="header">
                            '.Icons::chart().'
                            <h5> میزان پیشرفت </h5>
                        </div> 
                        <div class="timeline">
                            [timeline]
                        </div>
                    </div>
                    <div class="biography-con">
                        <div class="header">
                            '.Icons::message().'
                            <h5> بیوگرافی </h5>
                        </div> 
                        <div class="biography-content">
                            [biography]
                        </div> 
                    </div>
                    <div class="challenges-con">
                        <div class="header">
                            '.Icons::target().'
                            <h5> چالش ها </h5>
                        </div> 
                        [challenges-sliders] 
                    </div>
                </div>  
            </div>
        ';
    }


    public static function challengesSliderHolder()
    {
        return
            '<div class="challenges-[type]">
                <h6> [title] </h6>
                [items]
            </div>
        ';
    }



    public static function userDetails()
    {
        return
            '<div id="home-profile" class="home-profile-con" > 
                <div class="user-profile">
                    <a href="'.home_url('club/edit-profile/').'" class="avatar-container">
                       [avatar]
                    </a>
                    <div class="user-info">
                        <h3>[name]</h3>
                         <span data-title="طبقه">
                             [rank]
                         </span>
                    </div>
                    <div class="score-handle">
                        <a class="show-score" href="'.home_url('/club/point-history').'">
                           <span> [has] </span>
                           <span> امتیاز </span>
                           '.Icons::smile().'
                        </a>
                        <a class="manage-scores" href="'.home_url('/club/increase-points').'"> 
                           '.Icons::optionWithDot().'
                           <span> مدیریت امتیاز </span>
                        </a>
                    </div> 
                </div>  
            </div> 
        ';
    }

    public static function scoreTimeline()
    {
        return
            '<div class="progress-con">
                <div class="progress">
                    <div class="progress-bar bg-info" role="progressbar" style="width:[reach]%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="progress-title" >
                    <span style="min-width: 20%;" data-title="همیارک" class="[first-child]"></span>
                    <span style="min-width: 30%;" data-title="همیاروند" class="[second-child]"></span>
                    <span style="min-width: 50%;" data-title="همیار‌تمام" class="[third-child]"></span>
                    <span style="min-width: 0%;" data-title="همیار‌پلاس" class="[forth-child]"></span>
                </div>
            </div> 
        ';
    }


    public static function scoreNeedToNextRank()
    {
        return
            '<div class="score-need-to-next-rank">
                <p> 
                    <span>شما برای ارتقاء به طبقه بالاتر  </span>
                    <strong>[need-score]</strong>
                    <span>امتیاز نیاز دارید</span> 
                </p> 
            </div> 
        ';
    }

    public static function scoreHandlersButtons()
    {
        return
            '<div class="score-handlers-buttons">
                <div class="increase-button">
                    <a href="'.home_url('/club/increase-points').'">
                        '.Icons::cart().' 
                        <span>
                            افزایش امتیاز
                        </span>
                    </a>
                </div>
                <div class="use-methods-button">
                    <a href="'.home_url('/club/use-points-methods').'">
                        '.Icons::gift().' 
                        <span>
                            استفاده از جوایز
                        </span>
                    </a>
                </div>
            </div>
        ';
    }

    public static function homeButtons()
    {
        return
            '<div class="home-buttons-con">
                <a href="[my-challenges-link]">
                    '.Icons::optionWitTick().' 
                    <span>
                        چالش های من
                    </span>
                </a>
                <a href="[my-notifications-link]">
                    '.Icons::alert().' 
                    <span>
                         پیام ها
                    </span> 
                    [notif-count] 
                     
                </a>
                <a href="[guide-line]">
                    '.Icons::question().' 
                    <span>
                         راهنما
                    </span>
                </a>
            </div>
        ';
    }

    public static function homeAdds()
    {
        return
            '<div class="home-adds-con">
                <h5>[title]</h5>
                <a href="[adds-link]">
                    <img src="[image-url]" alt="[title]">
                </a> 
            </div>
        ';
    }

    public static function notifCountBadge()
    {
        return
            '<span class="notif-count">
                [count]
            </span>
        ';
    }

    public static function homeLastChallenges()
    {
        return
            '<div class="home-last-challenges">
                <h5> آخرین چالش ها</h5>
                <div class="last-challenges-con">
                    <div class="top">
                        [first]
                        [second] 
                    </div>
                    <div class="bottom">
                        [third] 
                    </div>
                </div>
            </div>
        ';
    }

    public static function homeLastChallengesItem()
    {
        return
            '<a href="[link]">
                <img src="[url]" alt="[alt]">
            </a>
        ';
    }

    public static function usePointsMethods()
    {
        return
            '<div class="top">
                [profile]
            </div>
            <div class="bottom">
                <div class="description">
                    <div class="header">
                        '.Icons::gift().' 
                        <h5>استفاده از جوایز</h5>
                    </div>
                    <div class="body">
                        <p>شما میتوانید با استفاده از هر یک از روش های زیر امتیازات همیار کلاب را به شارژ مستقیم ریالی در همیار آکادمی تبدیل نموده و کیف پول خود را شارژ کنید </p>
                    </div>
                    <div class="credit-converter-list">
                        <ul>
                            <li> 
                                <h6>تبدیل امتیاز به شارژ ریالی</h6> 
                                <div>
                                    <a href="'.home_url('club/convert-point-to-credit-description').'">
                                        جزئیات بیشتر
                                    </a>
                                    <a href="'.home_url('club/credit-converter').'">
                                        شارژ کیف پول همیار
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <h6>درخواست مشاوره با علی حاج محمدی</h6>    
                                    [request-haji-cost]
                                </div> 
                                <div>  
                                    <a '.home_url('club/consulting-haji-mohamadi').'>
                                        جزئیات بیشتر
                                    </a> 
                                    <a href="[request-haji-link]" class="[request-haji-btn]">
                                        [request-haji-text]
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <h6>خرید خدمات مشاوره اینستاگرام و سایت</h6> 
                                    [request-consulting-cost]
                                </div>  
                                <div>
                                    <a href="'.home_url('club/buy-consulting-services').'">
                                        جزئیات بیشتر
                                    </a>
                                    <a href="[request-consulting-link]" class="[request-consulting-btn]">
                                        [request-consulting-text]
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <h6>ثبت نام در دورهمی همیار آکادمی </h6>
                                    [request-hamyar-cost]
                                </div>   
                                <div>
                                    <a href="'.home_url('club/register-in-hamyar-courses').'">
                                       جزئیات بیشتر 
                                    </a>
                                    <a href="[request-hamyar-link]" class="[request-hamyar-btn]"> 
                                        [request-hamyar-text]
                                    </a>
                                </div>
                            </li> 
                        </ul>
                    </div>
                </div>
            </div>
       ';
    }


    public static function increasePoints()
    {
        return
            '<div class="top">
                [profile]
            </div>
            <div class="bottom"> 
                <div class="description">
                    <div class="header">
                        '.Icons::alert().' 
                        <h5>افزایش امتیاز</h5>
                    </div>
                    <div class="body">
                        <p> شما علاوه بر شرکت در چالش های مربوط به طبقه خود میتوانید از پیشنهاد‌ های زیر برای افزایش امتیاز و ارتقای طبقه خود استفاده نمایید  </p>
                    </div>
                </div>
                <div class="credit-converter-list">
                    <h6>محصولات پیشنهادی</h6>
                    <ul>
                        [user-can-buy-products]
                    </ul>
                </div>
                <div class="last-user-registered">
                    [last-user-registered] 
                </div> 
            </div>
       ';
    }


    public static function userCanBuyHamyarProductsList()
    {
        return
            '<li>  
                <a href="[link]">
                    <p>تهیه [title] </p>
                    <strong>
                        <span>[score]</span>
                        امتیاز 
                    </strong>
                </a> 
            </li> 
        ';
    }


    public static function lastUsersRegisterOnChallenge()
    {
        return
            '<h6>تکمیل آموزش</h6>
            <div class="last-user-registered-cart"> 
                <div class="score-con">   
                    <p> [title] </p>
                    <strong>
                        <span>[score]</span>
                        امتیاز 
                    </strong> 
                </div> 
                <div class="last-users">
                    <div class="right">
                        <h5>آخرین شرکت کنندگان</h5>
                    </div>
                    <div class="left">
                        <div>
                            [users]
                        </div>
                    </div>
                </div>
                <div class="buttons">
                    <a href="[link]"> تکمیل چالش </a>
                    <a href="[single]">جزییات بیشتر</a>
                </div>
            </div>
        ';
    }


    public static function challengesSlider()
    {
        return
            '<div class="splide challenges-slider-[target]">
                <div class="splide__track">
                    <ul class="splide__list">
                     [items] 
                    </ul>
                </div> 
                <div class="my-slider-progress">
                    <div class="slider-progress-bar-[target]"></div>
                </div>
            </div> 
       ';
    }

    public static function challengesSliderItem()
    {
        return
            '<li class="splide__slide">
                <div class="single-hero-slide bg-img">
                    <div class="challenge-slide-content " style="background-image: url(\'[thumbnail]\')">
                        <h3 class="slide-title">[title]</h3> 
                        <a class="slide-link" href="[link]">رویت</a>
                    </div>
                </div>
            </li>
        ';
    }

    public static function closeFriends()
    {
        return
            '<div class="card  mb-3">
                <div class="card-body close-friends-con">
                    [title]
                    [score-mate]
                    [rank-mate]
                    [challenge-mate]
                </div>
            </div> 
        ';
    }


    public static function closeFriendsSlider()
    {
        return
            '<div class="splide close-friends-[target]-slider">
                <div class="more">
                    <a href="'.home_url().'/club/close-friend/[url]"> بیشتر </a>
                    <h5>[title]</h5>
                </div>
                <div class="splide__track slider-root">
                    <ul class="splide__list">
                       [items]
                    </ul> 
                </div>  
                <div class="my-slider-progress">
                    <div class="slider-progress-bar"></div>
                </div>
            </div> 
       ';
    }


    public static function closeFriendsSliderItem()
    {
        return
            '<a href="[user-link]" class="splide__slide">
                <div class="single-hero-slide bg-img">
                    <div class="close-friend-slide-content">
                        <div class="img">
                           [profile]
                        </div>
                        <h6 class="text-dark mb-5 fw-bold">[name]</h6> 
                    </div>
                </div>
            </a>
        ';
    }


    public static function closeFriendsSingleCon()
    {
        return
            '<div class="col-12 close-friend-single-con">
                <div class="card shadow-sm blog-list-card">
                    <div class="header">
                        <h5>[title]</h5>
                    </div>
                    <div>
                        [items]
                    </div> 
                </div>
            </div>
        ';
    }


    public static function closeFriendsSingleItem()
    {
        return
            '<a href="[user-link]" class="close-friend-item">
                <div class="img">
                   [profile]
                </div>
                <h6 class="text-dark mb-5 fw-bold">[name]</h6>
            </a>
        ';
    }


    public static function profileSingleDetails()
    {
        return
            '<div class="card user-info-card">
                <div class="card-body">
                    <div class="user-profile">
                        [avatar] 
                    </div>
                    <div class="user-info">
                        <div>
                            <h5>[name]</h5>
                            <p>[rank]</p> 
                        </div>
                        <span class="badge bg-warning rounded-pill">[score]</span>
                    </div>
                </div>
            </div>
        ';
    }


    public static function userDetailsPage()
    {
        return
            '<div class="user-single-con">
                <div class="profile-con" >
                [profile]
                </div>
                <div class="challenge-con">
                    <ul>
                        [challenges]
                    </ul>
                </div> 
            </div>
        ';
    }


    public static function profileChallengeListItem()
    {
        return
        '<div class="col-12">
            <div class="card shadow-sm blog-list-card">
                <div class="d-flex align-items-center">
                    <div class="card-blog-img position-relative" style="background-image: url([background])">
                        <span class="badge bg-warning text-dark position-absolute card-badge">[title]</span>
                    </div>
                    <div class="card-blog-content">
                        <span class="badge bg-danger rounded-pill mb-2 d-inline-block">[date]</span>
                        <a class="blog-title d-block mb-3 text-dark" href="page-blog-details.html">[amount]</a>
                        <a class="btn btn-primary btn-sm" href="[url]"> نمایش</a>
                    </div>
                </div>
            </div>
          </div>
        ';
    }
 

    public static function expireDateOnSpendScore()
    {
        return
            '<div class="expire-date-on-spend-score">
                <h6> امتیازات در حال منسوخ شدن</h6>
                <ul>
                    [items]
                </ul>
            </div>
        ';
    }


    public static function expireDateOnSpendScoreItem()
    {
        return
            '<li class="[type]"> 
                <div class="title">
                    <p> [title] </p> 
                </div> 
                <div class="details">
                    <div>
                        <span> [score] </span>
                        <span> امتیاز</span>
                    </div>
                    <div>
                        <span>معتبر تا </span>
                        <strong dir="ltr">[date]</strong>
                    </div>  
                </div>
            </li>
        ';
    }


    public static function lastUsersRegisteredInRank()
    {
        return
            '<div class="card user-info-card">
                <div class="card-body last-user-in-rank">
                    <div class="title">
                        <h6> لیست آخرین افراد أفزوده شده به طبقات </h6> 
                    </div>
                    <div class="user-info">
                        <div class="menus">
                            <a href="javascript:void(0)" class="active" id="rank-A"> A </a>
                            <a href="javascript:void(0)" id="rank-B"> B </a>
                            <a href="javascript:void(0)" id="rank-C"> C </a>
                            <a href="javascript:void(0)" id="rank-D"> D </a>
                            <a href="javascript:void(0)" id="rank-E"> E </a>
                        </div>
                        <div class="list-group"> 
                            [items]
                        </div> 
                    </div>
                </div>
            </div>
        ';
    }


    public static function lastUsersRegisteredInRankUl()
    {
        return
            '<div class="rank-[rank]">
                <h2>[rank]</h2>
                <ul class="list-group ">
                    [items]
                </ul>
            </div>
        ';
    }


    public static function lastUsersRegisteredInRankItem()
    {
        return
            '<li class="list-group-item">
                <div class="avatar">[avatar]</div>
                <p class="user-id">[user-id]</p>
                <p class="name">[name]</p> 
                <p class="score">[score]</p> 
            </li>
        ';
    }

    public static function convertCredit()
    {
        return
            '<div class="card shadow-sm blog-list-card credit-converter-con">
                <div class="custom-container">
                    <h6 class="mb-3 text-center"> تبدیل امتیاز به اعتبار کیف پول همیار</h6>
                    <div class="text-center px-4">
                        <img class="login-intro-img" src="'.HWP_CLUB_PUBLIC_ASSETS.'images/exchange.png" alt="exchange">
                    </div> 
                    <div class="register-form mt-4"> 
                        <div class="credit-form-converter">
                            <div>
                                <input type="number" id="score-amount"  placeholder="score" value="[score]" disabled>
                            </div>
                            <div class="form-group position-relative">
                                <input type="number"  id="credit-amount" placeholder="مقدار نیاز برای تبدیل  "> 
                            </div>
                            <button class="btn btn-primary w-100 submit" type="submit"> تبدیل </button>
                        </div>
                    </div>  
                    <div class="converted-live">
                        <p class="amount-shower-con">
                            <span class="amount-shower"></span>
                            <span class="monetary-unit"> هزار تومان </span>
                        </p>
                    </div>
                </div>
            </div> 
        ';
    }


    public static function chat($userObject)
    {
         return sprintf(CHAT_FRONT_ENDPOINT.'/login-user?mobile=%s&token=%s',$userObject->mobile,$userObject->token);
    }


    public static function root( $userObject ,$mainClass = '' )
    {
        $active = Functions::getActiveClass( $mainClass );
        $chat   = Functions::getChatUrl( $userObject );
        return
            self::header() .
            '<div class="container">
                <div class="row">
                    <div class="page-content-wrapper '.$mainClass.'">
                        [content]
                    </div>
                </div>
            </div>
            [side-nav]'
            .self::footer( $active ,$chat );
    }


    public static function cleanRoot()
    {
        return
            '<div class="container">
                <div class="row">
                    <div class="page-content-wrapper py-1 club-user-info-form">
                        [content]
                    </div>
                </div>
            </div>
            <style>body{background-color:initial}</style> 
        ';
    }

    public static function notificationCount()
    {
        return
            '<div class="club-notification-count"> 
                <span> [count] </span> 
            </div>
        ';
    }

    public static function notificationsList()
    {
        return
            '<table class="table mb-0 table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">تاریخ</th>
                        <th scope="col">عنوان</th>
                    </tr>
                </thead>
                <tbody>
                    [items]
                </tbody>
            </table>
        ';
    }


    public static function notificationsListItem()
    {
        return
            '<tr>
                <td class="notification-id">[id]</td>
                <td class="notification-date">[date]</td>
                <td class="notification-title">[title]</td> 
            </tr> 
        ';
    }

    public static function notificationsEmptyItem()
    {
        return
            '<tr>
                <td colspan="3">
                    <div class="text-center">
                        <h5> اطلاعیه ای برای شما وجود ندارد </h5>
                    </div>
                </td> 
            </tr> 
        ';
    }

    public static function noteList()
    {
        return
            '<div class="notes-list-con"> 
                <div class="notes-list-header">
                    <h5> لیست یادداشت ها </h5> 
                </div>
                <div class="notes-list-body">
                    [items]
                </div> 
            </div>
        ';
    }


    public static function noteItem()
    {
        return
            '<div class="notes-item">  
                <div class="notes-item-content"> 
                    <strong> [title] </strong>
                    <p> [content] </p>   
                    <textarea> [content] </textarea>   
                </div> 
                <div class="notes-item-action"> 
                    <button class="remove" data-note-id="[id]"> حذف </button>   
                    <button class="update" data-note-id="[id]"> ویرایش </button>    
                    <button class="update-save" data-note-id="[id]" style="display: none"> ذخیره </button>    
                </div>  
            </div> 
        ';
    }

    public static function noteEmptyItem()
    {
        return
            '<div class="notes-item">  
                <div class="empty-note">
                    <h5> شما تا کنون یادداشتی ذخیره نکرده اید </h5>
                </div> 
            </div> 
        ';
    }



    public static function sideNav()
    {
        return '
            <div class="dir-ltr">
                <div class="offcanvas offcanvas-start" id="affanOffcanvas" data-bs-scroll="true" tabindex="-1" aria-labelledby="affanOffcanvsLabel">
                    <button class="btn-close btn-close-white text-reset" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    <div class="offcanvas-body p-0">
                        <div class="sidenav-wrapper">
                            <div class="sidenav-profile">
                                <div class="sidenav-style1"></div>
                                <div class="user-profile">
                                    [profile-image]
                                </div>
                                <div class="profile-user-info user-info">
                                    <h6 class="user-name mb-0"> [name] </h6>
                                    <p>[rank] </p>
                                    <a class="show-score" href="'.home_url('/club/point-history').'">
                                        <span> [has] </span>
                                        <span> امتیاز </span>
                                        '.Icons::smile().'
                                    </a>
                                </div >
                            </div>
                            <ul class="sidenav-nav ps-0 club-side-nav-icons">
                                <li>
                                    <a href="'.home_url('club/edit-profile/').'">
                                        '. Icons::edit() .'
                                        <span>ویرایش  پروفایل</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="'.home_url('club/club-rules/').'">
                                        '. Icons::option() .'
                                        <span>قوانین و مقررات </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="'.home_url('club/point-history/').'">
                                        '. Icons::optionWitTick() .'
                                        <span>چالش های من</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="'.home_url('club/increase-points/').'">
                                        '. Icons::cart() .'
                                        <span>مدیریت امتیاز</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="'.home_url('club/notifications/').'">
                                        '. Icons::bell() .'
                                        <span>پیام ها </span>
                                        [notif-count] 
                                    </a>
                                </li>
                                <li>
                                    <a href="'.home_url('club/notes/').'">
                                        '. Icons::calender() .'
                                        <span>یادداشت ها </span> 
                                    </a>
                                </li>
                                <li>
                                    <a href="'.home_url('club/contact-us/').'">
                                        '. Icons::guard() .'
                                        <span>پشتیبانی</span>
                                    </a>
                                </li> 
                                <li>
                                    <a href="'.wp_logout_url( get_home_url() ).'">
                                        '. Icons::exit() .'
                                         <span> خروج  </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }


    public static function footer( $active ,$chat )
    {
        $userObject = Users::getUserObject( get_current_user_id() );
        return'
            <div class="footer-nav-area" id="footerNav">
                <div class="container px-0">
                    <div class="footer-nav position-relative">
                        <ul class="h-100 d-flex align-items-center justify-content-between ps-0">
                            <li class="'.$active[1].'">
                                <a href="'.home_url('club/').'" class="club-stroke-color">
                                    '. Icons::home() .'
                                    <span>خانه</span>
                                </a>
                            </li>
                            <li class="'.$active[2].'">
                                <a href="'.home_url('club/challenges-list').'" class="club-stroke-color">
                                    '. Icons::target() .'
                                    <span>چالش ها</span>
                                </a>
                            </li> 
                            <li class="'.$active[3].'">
                                <a href="'.$chat.'" class="club-fill-color">
                                    '. Icons::chat() .'
                                    <span>گفتگو</span>
                                </a>
                            </li>
                            <li class="'.$active[4].'">
                                <a href="'.home_url('club/showcase/').'" class="club-fill-color">
                                    '. Icons::chart() .'
                                    <span>ویترین</span>
                                </a>
                            </li>  
                            <li class="'.$active[5].'">
                                <a href="javascript:void(0)" class="club-fill-color" id="note-toggle-button">
                                    '. Icons::calender() .'
                                    <span>یادداشت</span>
                                </a>
                                <div class="note-toggle-menu">
                                    <div class="note-container">
                                        <div class="textarea-con">
                                            <input id="note-title" type="text"  placeholder="عنوان یادداشت">
                                            <textarea id="note-content" placeholder="متن یادداشت"></textarea> 
                                            <div class="" id="note-loader"></div> 
                                        </div>
                                        <div class="action"> 
                                            <button id="note-saver" class="active-save"> ذخیره </button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        ';
    }


    public static function header()
    {
        return '
            <div class="header-area" id="headerArea">
                <div class="container">
                    <div class="header-content header-style-five position-relative d-flex align-items-center justify-content-between">
                        <div class="logo-wrapper">
                            <a href="'.home_url('club/').'">
                                <img src="'.HWP_CLUB_PUBLIC_ASSETS.'images/header.png" alt="header-icon">
                            </a>
                        </div>
                        <div class="navbar--toggler" id="affanNavbarToggler" data-bs-toggle="offcanvas" data-bs-target="#affanOffcanvas" aria-controls="affanOffcanvas">
                            <span class="d-block"></span>
                            <span class="d-block"></span>
                            <span class="d-block"></span> 
                        </div>
                    </div>
                </div>
            </div>
        ';
    }


    public static function contactUsForm()
    {
        return
            '<div class="contact-us-form"> 
                <div class="header">
                    <h5> فرم تماس با ما </h5>
                </div>
                <div class="description">
                    [description]
                </div>
                <div class="form">
                    [form]
                </div>
            </div>
        ';
    }

    public static function page404()
    {
        return
            '<div class="err-404" >
                <img class="mb-4" src="'.HWP_CLUB_PUBLIC_ASSETS.'images/404.png" >
                <h4> متاسفانه <br> صفحه یافت نشد!</h4> 
                <a href="'.home_url('/club').'">
                    بازگشت به صفحه ی اصلی
                </a>
            </div>
        ';
    }
}
