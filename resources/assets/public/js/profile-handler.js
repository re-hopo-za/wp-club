jQuery(function ( $ ){

    const username_input  = document.getElementById('input-username');
    const username_status = document.getElementById('username-status-text');
    const loading_con     = document.getElementById('username-loading-con');


    let loader =
        '<svg style="margin: auto; background: rgb(255, 255, 255); display: block; shape-rendering: auto;" width="18px" height="18px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">\n' +
        '   <path d="M10 50A40 40 0 0 0 90 50A40 43.3 0 0 1 10 50" fill="#e15b64" stroke="none">\n' +
        '       <animateTransform attributeName="transform" type="rotate" dur="0.15337423312883436s" repeatCount="indefinite" keyTimes="0;1" values="0 50 51.65;360 50 51.65"></animateTransform>\n' +
        '   </path> </svg>';
    let p_img;
    let body=$('body');
    $(document).on('click' ,'#change_profile_image',function () {
        console.log(6546574);
        var elem=$(this);
        $('.change_profile_image').trigger('click').change(function(){
            var input=this;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#change_profile_image').html('<input type="file" class="change_profile_image2"><img id="p_img" src="'+e.target.result+'" />').removeAttr('id');
                    p_img=$('#p_img').croppie({
                        enableExif: true,
                        viewport: {
                            width: 200,
                            height: 200,
                            type: 'square'
                        },
                        boundary: {
                            height: 400
                        },
                        enableOrientation: true
                    });
                    $('.croppie-container').append('<span id="rotate_profile_image"><span class="dashicons dashicons-image-rotate"></span></span><span id="upload_profile_image">انتخاب تصویر</span><span id="upload_profile_image_ok">تایید</span>');
                    elem.off('click');
                }
                reader.readAsDataURL(input.files[0]);
            }
        });
    });

    body.on('click','#upload_profile_image',function () {
        $('.change_profile_image2').trigger('click').change(function(){
            var input=this;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var change_profile_image2=$('.change_profile_image2');
                    change_profile_image2.next('div.croppie-container').remove();
                    change_profile_image2.parent('span').append('<img id="p_img" src="'+e.target.result+'" />')
                    p_img=$('#p_img').croppie({
                        enableExif: true,
                        viewport: {
                            width: 200,
                            height: 200,
                            type: 'square'
                        },
                        boundary: {
                            height: 400
                        },
                        enableOrientation: true
                    });
                    $('.croppie-container').append('<span id="rotate_profile_image"><span class="dashicons dashicons-image-rotate"></span></span><span id="upload_profile_image">انتخاب تصویر</span><span id="upload_profile_image_ok">تایید</span>');
                }
                reader.readAsDataURL(input.files[0]);
            }
        });
    });
    body.on('click','#rotate_profile_image',function(){
        p_img.croppie('rotate', 90);});
    body.one('click','#upload_profile_image_ok',function () {
        $('#rotate_profile_image').fadeOut(200);
        $('#upload_profile_image').fadeOut(200);
        $(this).fadeOut(200).removeAttr('id');
        $(this).text('در حال بارگزرای...').fadeIn(200)

        p_img.croppie('result', {
            type: 'base64',
            size: 'viewport',
            circle:false
        }).then(function (resp) {
            // Split the base64 string in data and contentType
            var block = resp.split(";");
            // Get the content type
            var contentType = block[0].split(":")[1];// In this case "image/gif"
            // get the real base64 content of the file
            var realData = block[1].split(",")[1];// In this case "iVBORw0KGg...."
            // Convert to blob
            var blob = b64toBlob(realData, contentType);

            // Create a FormData and append the file
            var fd = new FormData(document.getElementById("save_img"));
            fd.append("profile_pic", blob);
            fd.append("action", 'hfl_change_profile_image');
            $.ajax({
                url:hamyar_feature_public._ajax_url,
                data:fd,
                type:"POST",
                contentType:false,
                processData:false,
                cache:false,
                dataType:"json", // Change this according to your response from the server.
                error:function(err){
                    console.error(err);
                    alert('خطای نامعلوم');
                    $('.dig_load_overlay').removeAttr('style');
                },
                success:function(data){
                    if (data.success===true){
                        location.reload();
                    }else{
                        $('.dig_load_overlay').removeAttr('style');
                        alert(data.data);
                    }
                }
            });
        });
    });




    body.on('click','#profile-saver',function ()
    {
        let form      = $(document).find('#edit-profile');
        let name      = form.find('#input-name').val();
        let instagram = form.find('#input-instagram').val();
        let _private  = form.find('#input-private').is(':checked');
        let biography = form.find('#input-biography').val();
        let username  = username_input.value;
        if ( name && username_input.dataset.status == 200 ){
            $.ajax({
                url  : club_object.club_ajax_url,
                type : "POST",
                dataType: "json",
                data: {
                    'action'  : 'club_update_user_profile' ,
                    'nonce'   : club_object.club_nonce ,
                    name      : name ,
                    instagram : instagram,
                    private   : _private,
                    biography : biography,
                    username  : username
                } ,
                error:function(err){
                    alert('خطای نامعلوم');
                    $('.dig_load_overlay').removeAttr('style');
                },
                success:function(data){
                    if ( data.success === true ){
                        location.reload();
                    }else{
                        $('.dig_load_overlay').removeAttr('style');
                        alert(data.data);
                    }
                }
            });
        }
    });



    username_input.addEventListener('input', updateUsername );
    function updateUsername(e) {
        let cleaned = e.target.value.replace(/[^\w\s]/gi , '');
        cleaned     = cleaned.replace(/\s/g , '');
        e.target.value = cleaned;
        loading_con.innerHTML = loader;
        setTimeout(() => {
            $.ajax({
                url  : club_object.club_ajax_url,
                type : "POST",
                dataType: "json",
                data: {
                    'action'   : 'club_checker_username_status' ,
                    'nonce'    : club_object.club_nonce ,
                    'username' : cleaned
                } ,
            }).always( function ( XHR ,textStatus ,XHRorError ) {
                if ( textStatus === 'success' ){
                    username_status.textContent = XHR.data.message;
                    if ( XHR.data.status === 404 ){
                        username_status.textContent = '';
                        e.target.style.borderColor = '#4EE30C';
                        e.target.dataset.status = '200';
                    }else if ( XHR.data.status === 403  ){
                        username_status.textContent = 'این نام کاربری قبلا ثبت شده است';
                        e.target.style.borderColor = '#E30C0E';
                        e.target.dataset.status = '403';
                    }
                }else if( textStatus === 'error' ){
                    username_status.textContent ='خطای نامعلوم';
                    e.target.dataset.status = '500';
                }
                loading_con.innerHTML = '';
            });
        }, 500 );
    }



});

/**
 * Convert a base64 string in a Blob according to the data and contentType.
 *
 * @param b64Data {String} Pure base64 string without contehfl_submit_formhfl_submit_formntType
 * @param contentType {String} the content type of the file i.e (image/jpeg - image/png - text/plain)
 * @param sliceSize {Int} SliceSize to process the byteCharacters
 * @see http://stackoverflow.com/questions/16245767/creating-a-blob-from-a-base64-string-in-javascript
 * @return Blob
 */
function b64toBlob(b64Data, contentType, sliceSize) {
    contentType = contentType || '';
    sliceSize = sliceSize || 512;

    var byteCharacters = atob(b64Data);
    var byteArrays = [];

    for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
        var slice = byteCharacters.slice(offset, offset + sliceSize);

        var byteNumbers = new Array(slice.length);
        for (var i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i);
        }

        var byteArray = new Uint8Array(byteNumbers);

        byteArrays.push(byteArray);
    }

    var blob = new Blob(byteArrays, {type: contentType});
    return blob;
}





