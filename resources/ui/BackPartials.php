<?php

class BackPartials
{


    /////===---- global elements ----===/////

    public static function pageContainer($deviceClass = 'mobile', $additionalClasss = '')
    {
        return '
            <div class="panel-' . $deviceClass . '-container">
                <div class="panel-' . $deviceClass . '-root ' . $additionalClasss . '"> 
                    [page]
                </div> 
            </div>
       ';
    }

    public static function footerMenus($messageCount = ' ')
    {
        return '
            <div class="panel-footer-menus">
                 <div class="panel-menu [1]"> 
                    ' . Icons::oclock() . '
                </div> 
                <div class="panel-menu [2]"> 
                    ' . Icons::list() . '
                </div> 
                <div class="panel-menu [3]">
                    <span> ' . $messageCount . '</span>
                    ' . Icons::bell() . '
                </div> 
                <div class="panel-menu [4]"> 
                    ' . Icons::message() . '
                </div> 
            </div>
        ';
    }


    public static function bottomTicket()
    {
        return
            '<div class="panel-bottom-ticket-con"> 
                <div>
                    ' . Icons::message() . '
                </div>
             </div>';
    }


    /////===---- course single ----===/////

    public static function videoCourseTitle()
    {
        return '
            <div class="panel-video-course-title">
                <div class="panel-right">
                    <p class=""> [course-title] </p>
                    <h3> [lesson-title] </h3>
                </div>
                <div class="panel-left">
                    <div class="panel-svg-con">
                        ' . Icons::arrowLeft() . '
                    </div> 
                </div>
            </div>
       ';
    }


    public static function videoElement()
    {
        return '
            <div class="panel-video-con">
                <div class="video-root">
                    <video src="" width="100%" poster="[poster]" controls > 
                        [source]
                    </video>
                </div> 
            </div>
       ';
    }


    public static function licenseText()
    {
        return ' 
            <div class="panel-license-con"> 
                <p> کد لایسنس دوره :</p>
                <span> [license] </span>
            </div>
       ';
    }


    public static function coursesContainer()
    {
        return '
            <div class="panel-courses-con">  
                <ul class="panel-season-con" >
                    [seasons]
                </ul>
            </div>
        ';
    }


    public static function courseSeasons()
    {
        return '
            <li class="panel-seasons-item [active]"> 
                <div class="panel-seasons-details"> 
                    <div class="download">
                        ' . Icons::download() . '
                    </div>
                    <h3>[title]</h3>
                    <div class="arrow">
                        ' . Icons::arrowDown() . '
                    </div>
                </div>
                <ul>
                    [lessons]
                </ul>
            </li>
        ';
    }

    public static function courseLessons()
    {
        return '
            <li class="panel-lesson-item [active]"> 
                <div class="panel-lesson-details">
                    <div class="download">
                        ' . Icons::download() . '
                    </div>
                    <h3> [title] </h3>
                    <div class="time">
                        <time> [time] </time> 
                        ' . Icons::simpleClock() . '
                    </div> 
                </div> 
            </li>
        ';
    }


    /////===---- courses list ----===/////
    public static function coursesProfile()
    {
        return
            '<div class="panel-courses-profile">
                <div class="panel-profile-con">
                    <div class="panel-profile-icon">
                        ' . Icons::user() . '
                    </div>
                    <div class="panel-profile-name">
                        <span>[name]</span>
                    </div>
                </div>
                <div class="panel-bell-icon-con">
                    <div class="panel-bell-icon"> 
                        <span>[notif-count]</span>
                        ' . Icons::bell() . ' 
                    </div> 
                </div>
             </div>';
    }


    public static function coursesTimeline()
    {
        return
            '<div class="panel-timeline-con">
                <div class="panel-timeline-scroller">  
                    <div class="panel-timeline-item">
                        <div class="panel-timeline-bullet">
                            <span class="bullet"></span>
                        </div>
                        <div class="panel-timeline-details">
                            ' . Icons::studentHat() . '
                            <span> دوره ها</span>
                        </div>
                    </div>
                     <div class="panel-timeline-item active">
                        <div class="panel-timeline-bullet">
                            <span class="bullet"></span>
                        </div>
                        <div class="panel-timeline-details">
                            ' . Icons::money() . '
                            <span> مالی</span>
                        </div>
                    </div>
                    <div class="panel-timeline-item">
                        <div class="panel-timeline-bullet">
                            <span class="bullet"></span>
                        </div>
                        <div class="panel-timeline-details">
                            ' . Icons::handShake() . '
                            <span> فرصت های شغلی</span>
                        </div>
                    </div>
                    <div class="panel-timeline-item">
                        <div class="panel-timeline-bullet">
                            <span class="bullet"></span>
                        </div>
                        <div class="panel-timeline-details">
                            ' . Icons::studentHat() . '
                            <span> کسب و کار</span>
                        </div>
                    </div>
                </div>
             </div>';
    }

    public static function coursesList()
    {
        return
            '<div class="panel-courses-lsit">
                <h3>دوره های شما </h3>
                <ul>
                    [courses]
                </ul> 
             </div>';
    }

    public static function courseItem()
    {
        return
            '<li>
                <p class="course-item" data-course-id="[course-id]">
                    [item]
                </p>
             </li> ';
    }


    /////===---- quizzes list ----===/////

    public static function quizzesTitle()
    {
        return '
            <div class="panel-quizzes-title">
                <div class="panel-right"> 
                    <h3> [quize-title] </h3>
                </div>
                <div class="panel-left">
                    <div class="panel-svg-con">
                        ' . Icons::arrowLeft() . '
                    </div> 
                </div>
            </div>
       ';
    }


    public static function quizzesList()
    {
        return '
            <div class="panel-quizzes-list"> 
                <ul class="panel-left"> 
                    [quizzes-list]
                </ul>
            </div>
       ';
    }

    public static function quizzesListItem()
    {
        return '
            <li class="panel-quizzes-item [status] [data-force] "> 
                <div class="top">
                    <h4>[name]</h4>
                </div>
                <div class="bottom">
                    [quize-count]
                    [quize-score]
                    [quize-status] 
                </div>
            </li> 
       ';
    }

    public static function quizeCount()
    {
        return
            '<div class="quize-count">
                <span> تعداد سوال </span>
                <span> : </span>
                <span> [quize-count] </span> 
            </div>';
    }

    public static function quizeScore()
    {
        return
            '<div class="auize-score">
                <span> نمره </span> 
                <span> [score-get] </span> 
                <span> از </span>
                <span> [score-from] </span> 
            </div>';
    }

    public static function quizeStatus()
    {
        return
            '<div class="quize-status">
                <span> وضعیت </span>
                <span> : </span>
                <span> [status] </span> 
            </div>';
    }


    /////===---- quize single ----===/////

    public static function quizeSingleTitle()
    {
        return '
            <div class="panel-quizzes-title">
                <div class="panel-right">
                    <h5> [course-title] </h5>
                    <h3> [quize-title] </h3>
                </div>
                <div class="panel-left">
                    <div class="panel-svg-con">
                        ' . Icons::arrowLeft() . '
                    </div> 
                </div>
            </div>
       ';
    }

    public static function quizeButtons()
    {
        return
            '<div class="quize-pagination-buttons">
                <button class="backward disabled">
                   ' . Icons::next() . ' 
                </button>
                <button class="forward">
                   ' . Icons::back() . '
                </button> 
            </div>';
    }

    public static function quizeSubmitButton()
    {
        return
            '<div class="quize-submit-button"> 
                <div class="holder">
                    <button class="submit"> 
                          ثبت کردن
                    </button>
                </div> 
            </div>';
    }


    public static function acceptedSection()
    {
        return
            '<div class="quize-accepted-con"> 
                <div class="svg-con">
                     ' . Icons::smile() . '
                </div>
                <h3>
                    تبریک میکم شما در این آزمون قبول شدید
                </h3>
                <div class="got-score-con">
                    <span> نمره شما : </span>
                    <div>
                        <span> [got-score]  </span>
                        <span> از </span>
                        <span> [from-score] </span>
                    </div>
                </div>
            </div>';
    }


    /////===---- fisical ----===/////

    public static function fisicalMain()
    {
        return '
            <div class="panel-fisical-tabs"> 
                <div class="fisical-tabs-menu"> 
                    [fisical-menus]  
                </div>
                <div class="fisical-tabs-content">
                    [fisical-contents]
                </div>
            </div> 
        ';
    }


    public static function fisicalTabButton($url, $name, $active = '')
    {
        $url = home_url() . '/my/fisical/' . $url;
        return '
            <a href="' . $url . '" onclick="return false;" class="fisical-tabs-item ' . $active . '">  
                ' . $name . '
            </a> 
        ';
    }

    public static function ordersList()
    {
        return '
            <div class="panel-orders-list">  
                <table> 
                    <thead>
                        <tr class="header"> 
                            <th class="id">شناسه </th>
                            <th class="date">تاریخ</th>
                            <th class="status">وضعیت</th> 
                            <th class="total">مجموع</th>  
                        </tr>
                    </thead>
                    <tbody>
                        [orders-list]
                    </tbody> 
                </table>
            </div>';
    }

    public static function ordersItem()
    {
        return '
            <tr class="panel-orders-item [class-status]">   
                <td class="id">[order-id]</td>
                <td class="date">[order-date]</td>
                <td class="status">[order-status]</td> 
                <td class="total">[order-total]</td>    
            </tr> 
            <tr class="child hider"> 
                 [item-courses]  
            </tr>
       ';
    }


    public static function ordersCourseList()
    {
        return '
            <td colspan="4" class="item-courses " id="[order-id]"> 
                [courses] 
            </td>
       ';
    }

    public static function ordersCourseItem()
    {
        return '
            <div class="panel-course-item" >   
                 <p class="title">
                    <strong> دوره  </strong>
                    <span>[course-title]</span>
                 </p> 
                 <p class="price">
                    <strong> قیمت  </strong>
                    <span>[course-price]</span> 
                 </p>    
            </div> 
       ';
    }


    /////===---- instalment ----===/////

    public static function instalmentList()
    {
        return '
            <div class="panel-instalment-item-list"> 
                <table> 
                    <thead>
                        <tr class="header"> 
                            <th>شناسه </th>
                            <th>تاریخ</th>
                            <th>قیمت کل</th> 
                            <th>پرداخت شده</th>  
                            <th> جزییات پرداخت</th>   
                        </tr>
                    </thead>
                    <tbody>
                        [instalments-list]
                    </tbody> 
                </table> 
            </div> 
       ';
    }

    public static function instalmentItem()
    {
        return '
            <tr class="instalment-item [status]">   
                 <td class="id">[id]</td>
                 <td class="date">[date]</td>
                 <td class="total">[total]</td>  
                 <td class="paid">[paid]</td>   
                 <td class="details [id]"> جزییات</td>   
            </tr> 
            [instalment-detals]
       ';
    }


    public static function instalmentPaymentItems()
    {
        return '
            <tr class="panel-instalment-payment-item hider" >  
                <td class="instalment-container" colspan="6">
                    <div class="instalment-description">
                        <h5>[title]</h5>
                        <div class="header"> 
                            <strong class="price">مبلغ</strong>   
                            <strong class="date">تاریخ</strong>
                            <strong class="kind">وضعیت پرداختی</strong>
                            <strong class="description">توضیحات</strong>
                            <strong class="action">عملیات</strong>
                        </div>
                        [instalment-payed-list]
                    </div>
                </td>
            </tr> 
       ';
    }

    public static function instalmentPayedList()
    {
        return
            '<div class="content">
                 <p class="price">[price]</p>   
                 <p class="date">[date]</p>
                 <p class="kind">[kind]</p>
                 <p class="description">[description]</p>
                 [action]
            </div>';
    }

    /////===---- transactions ----===/////
    public static function transactionsList()
    {
        return '
            <div class="panel-transactions-item-list">
                <table>
                    <thead> 
                        <tr>
                            <th class="id">شناسه </th>
                            <th class="id">افزایش مبلغ </th>
                            <th class="id">کسر مبلغ </th>
                            <th class="date">جزییات</th>
                            <th class="status">تاریخ</th>  
                        </tr>
                    </thead>
                    <tbody>
                         [credits-list]
                    </tbody> 
                </table>
            </div> 
       ';
    }

    public static function transactionsItem()
    {
        return '
            <tr class="transactions-item">   
                <td class="id">[id]</td>
                <td class="increase">[increase]</td>
                <td class="decrease">[decrease]</td>
                <td class="details">[details]</td>  
                <td class="date">[date]</td>    
            </tr> 
       ';
    }


    /////===---- credits ----===/////
    public static function creditsList()
    {
        return '
            <div class="panel-credits-item-list">
                <div class="wallet-amount"> 
                    <h5>موجودی اعتباری</h5>    
                    <p>  [wallet-amount] </p>    
                </div>
                <div class="last-credit">  
                    [last-credit] 
                </div>
                <table>
                    <h5 class="table-title">لیست اعتبارات</h5>    
                    <thead> 
                        <tr>
                            <th class="id">شناسه </th>
                            <th class="id">افزایش مبلغ </th>
                            <th class="id">کسر مبلغ </th>
                            <th class="date">جزییات</th>
                            <th class="status">تاریخ</th> 
                            <th class="total">اعتبارتا </th>  
                            <th class="total"> قابل برداشت </th>   
                        </tr>
                    </thead>
                    <tbody>
                         [credits-list]
                    </tbody> 
                </table>
            </div> 
       ';
    }

    public static function creditItem()
    {
        return '
            <tr class="credit-item">   
                <td class="id">[id]</td>
                <td class="increase">[increase]</td>
                <td class="decrease">[decrease]</td>
                <td class="details">[details]</td>  
                <td class="date">[date]</td>  
                <td class="remain-date">[remain-date]</td>   
                <td class="withdraw">[withdraw]</td>   
            </tr> 
       ';
    }


    /////===---- request Money ----===/////
    public static function requestMoney()
    {
        return '
            <div class="panel-request-money">
                <div class="request-money-con"> 
                    <p>[request-money-title]</p>
                </div>
                <div class="request-money-form">
                     [request-money-form]
                </div> 
            </div> 
       ';
    }




    /////===---- Profile Page ----===/////
    public static function profilePage()
    {
        return '
            <div class="panel-profile">
                <div class="profile-title"> 
                    <p>[profile-title]</p>
                </div>
                <div class="profile-form">
                     [profile-form]
                </div> 
            </div> 
       ';
    }



    /////===---- style ----===/////

    public static function headerHide()
    {
        return
            '<style>
                header.container , .menu_shadow{
                    display:none!important
                }
            </style>';
    }


    public static function footerHide()
    {
        return
            '<style>
                footer#footer{
                    display:none!important
                }
            </style>';
    }


}