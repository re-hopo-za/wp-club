

jQuery(function ($){

    let ajax_url   = club_object.club_ajax_url;
    let club_nonce = club_object.club_nonce;


    $(document).on('click' ,'.user-action-con div' ,function (){
        let user_id = $(this).parent().data('user-id');
        let type    = $(this).hasClass('increase-credit') ? 'increase' : 'decrease';
        showHandler( user_id ,type );
    });


    function showHandler( section_id ,type ){
        let section = $(document).find('.handler-con-'+section_id );
        if ( section.length && section.is(':hidden') ){
            $(document).find('.user-action-handler-con').hide();
            section.data('credit-type' ,type ).show();
            $(document).find('.user-action-handler-con h3 span').html( type == 'increase' ? 'افزایش' : 'کاهش' );
        }else{
            $(document).find('.user-action-handler-con').data('credit-type' ,'' ).hide();
        }
    }


    $(document).on('click' ,'.save-user-activity' ,function (){
        let $this   = $(this);
        let parent  = $this.parent().parent();
        let user_id = parent.data('user-id');
        let type    = parent.data('credit-type');
        let amount  = parent.find('.amount').val();
        let rank    = parent.find('.rank-list').val();
        let desc    = parent.find('.desc').val();
        type        = type === 'increase' ? 'credit' : 'subtract';
        if ( user_id && type && amount && rank && confirm('ذخیره شود ؟ ') ){
            $this.find('a').text('در حال ثبت ...');
            $.ajax({
                url      : ajax_url,
                dataType : "json"  ,
                method   : 'POST'  ,
                data: {
                    action  : 'club_add_user_point' ,
                    nonce   : club_nonce ,
                    user_id : user_id ,
                    type    : type ,
                    amount  : amount ,
                    rank    : rank ,
                    desc    : desc ,
                },
            }).always( function ( XHR ,textStatus ,XHRorError ) {
                if ( textStatus === 'success' ){
                    parent.hide();
                    parent.data('credit-type' ,'');
                    parent.find('.amount').val('');
                    parent.find('.rank-list').val('');
                    parent.find('.desc').val('');
                    $this.find('a').text('ذخیره');
                }else if( textStatus === 'error' ){
                    alert('خطا در برقراری ارتباط با سرور');
                }
            });
        }
    });




    //////  get users points in admin page /////
    $(document).on('click' ,'.user-points-list-con span' ,function (){
        let user_id = $(this).parent().data('user-id');
        showPointsHandler( user_id );
    });


    function showPointsHandler( user_id  ){
        let section = $(document).find('.handler-list-con-'+user_id );
        if ( section.length && section.is(':hidden') ){
            $(document).find('.user-points-list-handler-con').hide();
            section.show();
            getPointsList( user_id );
        }else{
            $(document).find('.user-points-list-handler-con').hide();
        }
    }


    function getPointsList( user_id ){
        if ( user_id ){
            $.ajax({
                url      : ajax_url,
                dataType : "json"  ,
                method   : 'POST'  ,
                data: {
                    action  : 'club_get_user_points' ,
                    nonce   : club_nonce ,
                    user_id : user_id
                },
            }).always( function ( XHR ,textStatus ,XHRorError ) {
                console.log(XHR)
                if ( textStatus === 'success' ){
                    let section = $(document).find('.handler-list-con-'+user_id +' table tbody').html(XHR.data.result );
                }else if( textStatus === 'error' ){
                    alert('خطا در برقراری ارتباط با سرور');
                }
            });
        }
    }










});