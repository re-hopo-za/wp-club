jQuery(function ($){

    let ajax_url   = club_object.club_ajax_url;
    let club_nonce = club_object.club_nonce;

    $(document).on('change' ,'#darkSwitch' ,function (){
        if ( $(document).find('html').data('theme') === 'light' ){
            document.documentElement.setAttribute("data-theme", "dark");
            localStorage.setItem("theme", "dark");
            $(document).find('html').data('theme' ,'dark');
        }else{
            document.documentElement.setAttribute("data-theme", "light");
            localStorage.setItem("theme", "light");
            $(document).find('html').data('theme' ,'light');
        }
    });


    $(document).on('click' ,'.challenge-action-button .register' ,function (){
        let $this = $(this);
        iziToast.question({
            timeout: 20000,
            close: true,
            overlay: true,
            zindex: 9999999,
            progressBarColor : '#12C900',
            overlayColor : 'rgba(0, 0, 0, 0.3)',
            overlayClose :true ,
            title: 'ثبت نام در این چالش ',
            titleColor:'#999',
            position: 'center',
            icon : '',
            rtl: true,
            backgroundColor :'#fff' ,
            buttons: [
                ['<button class="accept-register-challenge"><b >ثبت نام میکنید ؟</b></button>', function ( instance, toast ) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    let challenge_id = $this.data('challenge-id');
                    if ( challenge_id && !isNaN( challenge_id ) ){
                        $.ajax({
                            url      : ajax_url,
                            dataType : "json"  ,
                            method   : 'POST'  ,
                            data: {
                                action       : 'club_register_challenge' ,
                                challenge_id : challenge_id ,
                                nonce        : club_nonce
                            },
                        }).always( function ( XHR ,textStatus ,XHRorError ) {
                            if ( textStatus === 'success' ){
                                window.location.replace( XHR.result );
                            }else if( textStatus === 'error' ){
                                iziToast.error({
                                    title: 'خطا',
                                    message: 'خطا هنگام ثبت نام',
                                });
                            }
                        });
                    }
                }, true],
                ['<button class="reject-register-challenge">خیر</button>', function (instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                }],
            ],
        });
    });


    if (document.querySelectorAll('.challenges-slider-actives').length > 0 ) {
        var splide = new Splide( '.challenges-slider-actives' ,{
            direction: 'rtl',
            type   : 'loop',
        } );
        var bar = splide.root.querySelector( '.slider-progress-bar-actives' );
        splide.on( 'mounted move', function () {
            var end = splide.Components.Controller.getEnd() + 1;
            bar.style.width = String( 100 * ( splide.index + 1 ) / end ) + '%';
        } );
        splide.mount();
    }


    if (document.querySelectorAll('.challenges-slider-completed').length > 0 ) {
        var splide = new Splide( '.challenges-slider-completed' ,{
            direction: 'rtl',
            type   : 'loop',
        } );
        var bar = splide.root.querySelector( '.slider-progress-bar-completed' );
        splide.on( 'mounted move', function () {
            var end = splide.Components.Controller.getEnd() + 1;
            bar.style.width = String( 100 * ( splide.index + 1 ) / end ) + '%';
        } );
        splide.mount();
    }


    if (document.querySelectorAll('.close-friends-score-mate-slider').length > 0 ) {
        var splide = new Splide('.close-friends-score-mate-slider' ,{
            direction: 'rtl',
            type   : 'loop',
            perPage: 3,
        } );
        var bar = splide.root.querySelector( '.slider-progress-bar' );
        splide.on( 'mounted move', function () {
            var end = splide.Components.Controller.getEnd() + 1;
            bar.style.width = String( 100 * ( splide.index + 1 ) / end ) + '%';
        } );
        splide.mount();
    }

    if (document.querySelectorAll('.close-friends-rank-mate-slider').length > 0 ) {
        var splide = new Splide('.close-friends-rank-mate-slider' ,{
            direction: 'rtl',
            type   : 'loop',
            perPage: 3,
        } );
        var bar = splide.root.querySelector( '.slider-progress-bar' );
        splide.on( 'mounted move', function () {
            var end = splide.Components.Controller.getEnd() + 1;
            bar.style.width = String( 100 * ( splide.index + 1 ) / end ) + '%';
        } );
        splide.mount();
    }

    if (document.querySelectorAll('.close-friends-challenge-mate-slider').length > 0 ) {
        var splide = new Splide('.close-friends-challenge-mate-slider' ,{
            direction: 'rtl',
            type   : 'loop',
            perPage: 3,
        } );
        var bar = splide.root.querySelector( '.slider-progress-bar' );
        splide.on( 'mounted move', function () {
            var end = splide.Components.Controller.getEnd() + 1;
            bar.style.width = String( 100 * ( splide.index + 1 ) / end ) + '%';
        } );
        splide.mount();
    }



    // admin page
    $(document).on('click' ,'.last-user-in-rank .menus a' ,function (){
        $('.last-user-in-rank .menus a').removeClass('active');
        $(this).addClass('active');
        let rank = $(this).attr('id');
        $('.last-user-in-rank .list-group>div').hide();
        $('.last-user-in-rank .list-group>div.'+rank ).show();
    });



    // credit converter
    $(document).on('keyup' ,'.credit-converter-con #credit-amount' ,function (){
        let parent = $(this).parent().parent();
        let amount = parseInt( parent.find('#score-amount').val() );
        let credit = parseInt( $(this).val() );
        if ( credit < 1) $(this).val( 1 );
        if ( credit > amount ) $(this).val( amount );
        if( amount && amount > 0 ){
            let live = credit / 10;
            $(document).find('.converted-live').show().find('.amount-shower').text( live );
        }
    });



    $(document).on('click' ,'.credit-converter-con .submit' ,function (){
        let parent = $(this).parent().parent();
        let amount = parent.find('#credit-amount').val();
        if ( amount && amount > 0 && confirm('تبدیل شود ؟ ') ){
            $.ajax({
                url      : ajax_url,
                dataType : "json"  ,
                method   : 'POST'  ,
                data: {
                    action   : 'club_convert_credit' ,
                    amount   : amount ,
                    nonce    : club_nonce
                },
            }).always( function ( XHR ,textStatus ) {
                if ( textStatus === 'success' ){
                    window.location.reload()
                }else if( textStatus === 'error' ){
                    iziToast.error({
                        title: 'خطا',
                        message: 'خطا هنگام ثبت نام',
                    });
                }
            });
        }
    });

    $(document).on('click' ,'.challenges-list .more-details' ,function (){
        let $this = $(this);
        if ( $this.hasClass('active') ) {
            $this.removeClass('active');
            $this.parent().siblings('.accordion-content').slideUp();
        }else {
            $this.addClass('active');
            $this.parent().siblings('.accordion-content').slideDown();
        }
    });



    $(document).on('click' ,'#form-submit-button' ,function (){
        let form = $(this).parent().parent();
        let challenge_id = parseInt( form.data('challenge-id') );
        let comment      =  $('textarea#comment-field').val();
        if ( challenge_id && comment &&  confirm( ' ثبت شود ؟؟') ){
            $.ajax({
                url      : ajax_url,
                dataType : "json"  ,
                method   : 'POST'  ,
                data: {
                    action   : 'club_submit_comment' ,
                    challenge_id : challenge_id ,
                    comment  : comment ,
                    nonce    : club_nonce
                },
            }).always( function ( XHR ,textStatus ) {
                if ( textStatus === 'success' ){
                    window.location.reload()
                }else if( textStatus === 'error' ){
                    iziToast.error({
                        title: 'خطا',
                        message: 'خطا هنگام ذخیره نظر',
                    });
                }
            });
        }
    });


    let note_section = $(document).find('.note-toggle-menu');
    let note_loader  = $(document).find('#note-loader');
    let note_content = $(document).find('#note-content');
    let note_title   = $(document).find('#note-title');
    let save_status  = true;
    function note_closer( close = false ){
        console.log(note_section)
        if ( close ){
            note_section.hide();
        }else {
            if ( note_section.is(':visible') ) {
                note_section.hide();
            }else {
                note_section.show();
            }
        }
    }


    $(document).on('click' ,'#note-closer' ,function (){
        note_closer( true );
        console.log(445)
    });

    $(document).on('click' ,'#note-toggle-button' ,function (){
        note_closer();
        console.log(445)
    });

    $(document).on('click' ,'#note-saver' ,function (){
        let $this   = $(this);
        let content = note_content.val();
        let title   = note_title.val();
        if ( content && content.length > 0 && save_status ){
            save_status = false;
            $this.removeClass( 'active-save');
            note_loader.addClass( 'note-loader');
            $.ajax({
                url      : ajax_url,
                dataType : "json"  ,
                method   : 'POST'  ,
                data: {
                    action  : 'club_submit_note' ,
                    content : content ,
                    title   : title ,
                    nonce   : club_nonce ,
                    url     : window.location.href
                },
            }).always( function ( XHR ,textStatus ) {
                if ( textStatus === 'success' ){
                    $this.addClass( 'active-save');
                    note_loader.removeClass( 'note-loader');
                    note_content.val('');
                    note_closer( true );
                }else if( textStatus === 'error' ){
                    iziToast.error({
                        title: 'خطا',
                        message: 'خطا هنگام ذخیره نظر',
                    });
                }
                save_status = true;
            });
        }
    });


    $(document).on('click' ,'.notes-item-action .remove' ,function (){
        let $this = $(this);
        if ( confirm('حذف شود ؟') ){
            let note_id = parseInt( $this.data('note-id') );
            $.ajax({
                url      : ajax_url,
                dataType : "json"  ,
                method   : 'POST'  ,
                data: {
                    action  : 'club_remove_note' ,
                    note_id : note_id ,
                    nonce  : club_nonce
                },
            }).always( function ( XHR ,textStatus ) {
                if ( textStatus === 'success' ){
                    $this.parent().parent().remove();
                }else if( textStatus === 'error' ){
                    iziToast.error({
                        title: 'خطا',
                        message: 'خطا هنگام حذف یادداشت',
                    });
                }
            });
        }
    });


    $(document).on('click' ,'.notes-item-action .update' ,function (){
        let $this     = $(this);
        let content   = $this.parent().siblings('.notes-item-content');
        let paragraph = content.children('p');
        let textarea  = content.children('textarea');
        let save_btn  = $this.siblings('.notes-item-action .update-save');
        $this.removeClass('update').addClass('cancel-update').text('لغو ویرایش');
        paragraph.hide();
        textarea.show();
        save_btn.show();
    });

    $(document).on('click' ,'.notes-item-action .cancel-update' ,function (){
        let $this     = $(this);
        let content   = $this.parent().siblings('.notes-item-content');
        let paragraph = content.children('p');
        let textarea  = content.children('textarea');
        let save_btn  = $this.siblings('.notes-item-action .update-save');
        $this.removeClass('cancel-update').addClass('update').text(' ویرایش');
        paragraph.show();
        textarea.hide();
        save_btn.hide();
    });


    $(document).on('click' ,'.notes-item-action .update-save' ,function (){
        let $this     = $(this);
        let content   = $this.parent().siblings('.notes-item-content ').children('textarea').val();
        if ( content && content.length > 5 && confirm('ذخیره شود ؟') ){
            let note_id = parseInt( $this.data('note-id') );
            $.ajax({
                url      : ajax_url,
                dataType : "json"  ,
                method   : 'POST'  ,
                data: {
                    action  : 'club_update_note' ,
                    note_id : note_id ,
                    content : content ,
                    nonce   : club_nonce
                },
            }).always( function ( XHR ,textStatus ) {
                if ( textStatus === 'success' ){
                    if ( XHR.data.content ){
                        $(document).find('.notes-list-body').html( XHR.data.content );
                    }
                }else if( textStatus === 'error' ){
                    iziToast.error({
                        title: 'خطا',
                        message: 'خطا هنگام به روز رسانی یادداشت',
                    });
                }
            });
        }
    });



    $(document).on('click' ,'.form-description-button' ,function (){
        let $this = $(this);
        $this.children('section').slideToggle();
    });




});



