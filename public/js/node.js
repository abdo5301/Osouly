



function log($data) {
    console.log($data);
}

function readNotification(){
    $('#count-notification-layout').hide();
    $.get($systemURL+'ajax',{
        'type' : 'readNotification'
    });
}

function ClickMove($selector,$CatName){

    $value = $($selector).val();
    $areaName = $($selector+" option:selected").text();


    if($value == '' || $value == 0){
        return false;
    }

    if($('#CUC_'+$value).val() == undefined){
        $('#area_ids-form-input').append('<option  ondblclick="$(this).remove();"  id="CUC_'+ $value +'" value="'+ $value +'">'+ $CatName +': '+$areaName +'</option>');
    }

    $('#area_ids-form-input option').prop('selected', true);
    return true;


}

function isset () {
    //  discuss at: http://locutus.io/php/isset/
    // original by: Kevin van Zonneveld (http://kvz.io)
    // improved by: FremyCompany
    // improved by: Onno Marsman (https://twitter.com/onnomarsman)
    // improved by: Rafał Kukawski (http://blog.kukawski.pl)
    //   example 1: isset( undefined, true)
    //   returns 1: false
    //   example 2: isset( 'Kevin van Zonneveld' )
    //   returns 2: true

    var a = arguments
    var l = a.length
    var i = 0
    var undef

    if (l === 0) {
        throw new Error('Empty isset')
    }

    while (i !== l) {
        if (a[i] === undef || a[i] === null) {
            return false
        }
        i++
    }

    return true
}

function copyToClipboard(text) {
    if (window.clipboardData && window.clipboardData.setData) {
        // IE specific code path to prevent textarea being shown while dialog is visible.
        return clipboardData.setData("Text", text);

    } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
        var textarea = document.createElement("textarea");
        textarea.textContent = text;
        textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in MS Edge.
        document.body.appendChild(textarea);
        textarea.select();
        try {
            return document.execCommand("copy");  // Security exception may be thrown by some browsers.
        } catch (ex) {
            console.warn("Copy to clipboard failed.", ex);
            return false;
        } finally {
            document.body.removeChild(textarea);
        }
    }
}

function getNextAreas($id,$typeID,$attrID,$selected){

    $.getJSON($systemURL+'ajax',{
        'type' : 'getNextAreas',
        'id'   : $id
    },function(response){
        console.log(response);
        if(response.type != false){
            $select = new Array;

            $select.push('<label for="area_id">'+response.type.name+'</label>');

            $select.push('<select id="area_id_my_type_'+response.type.id+'" name="area_id[]" onchange="getNextAreas($(this).val(),'+response.type.id+',\''+$attrID+'\')" class="form-control">');
            $select.push('<option value="">Select '+response.type.name+'</option>');

            $.each(response.areas,function(key,value){
                if($selected == value.id){
                    $select.push('<option selected="selected" value="'+value.id+'">'+value.name+'</option>');
                }else{
                    $select.push('<option value="'+value.id+'">'+value.name+'</option>');
                }
            });

            $select.push('</select>');

            if($('#divAreaID_'+response.type.id).attr('egpay') == 'select'){
                $('#divAreaID_'+response.type.id).html(
                    $select.join("\n")
                );

                $('[egpay="select"]').each(function(){
                    $catID = ($(this).attr('id')).replace('divAreaID_','');
                    if($catID > response.type.id){
                        $(this).remove();
                    }
                });
            }else{
                $($attrID).append(
                    '<div class="row">'+
                    '<div class="col-md-10">'+
                    '<fieldset egpay="select" id="divAreaID_'+response.type.id+'" class="form-group">'+
                    $select.join("\n")+
                    '</fieldset>'+
                    '</div>'+
                    '<div class="col-md-2" style="padding-top: 30px;">'+
                    '<a style="font-size: 18px;" onclick="ClickMove(\'#area_id_my_type_'+response.type.id+'\',\''+response.type.name+'\')" href="javascript:void(0)"> &gt;&gt; </a>'+
                    '</div>'+
                    '</div>'
                );
            }
        }else{
            $('[egpay="select"]').each(function(){
                $catID = ($(this).attr('id')).replace('divAreaID_','');
                console.log($typeID);
                if($catID > $typeID){
                    $(this).remove();
                }
            });
        }

        if(isset($runAreaLoop) && $runAreaLoop == true){
            if(isset($areaLoopData[$typeID])){
                $getSelectID = null;
                $nextTypeID = null;
                $nextAreaID = null;

                $areaLoopData.forEach(function(value,key){
                    if(key == $typeID){
                        $getSelectID = true;
                    }else if($getSelectID === true){
                        $nextTypeID = key;
                        $nextAreaID = value;
                        $getSelectID = null;
                    }
                });


                if($nextTypeID != null){
                    $('#area_id_my_type_'+$nextTypeID).val($nextAreaID).change();
                }else{
                    $runAreaLoop = false;
                }

            }

        }

    });

}


function in_array (needle, haystack, argStrict) { // eslint-disable-line camelcase
                                                  //  discuss at: http://locutus.io/php/in_array/
                                                  // original by: Kevin van Zonneveld (http://kvz.io)
                                                  // improved by: vlado houba
                                                  // improved by: Jonas Sciangula Street (Joni2Back)
                                                  //    input by: Billy
                                                  // bugfixed by: Brett Zamir (http://brett-zamir.me)
                                                  //   example 1: in_array('van', ['Kevin', 'van', 'Zonneveld'])
                                                  //   returns 1: true
                                                  //   example 2: in_array('vlado', {0: 'Kevin', vlado: 'van', 1: 'Zonneveld'})
                                                  //   returns 2: false
                                                  //   example 3: in_array(1, ['1', '2', '3'])
                                                  //   example 3: in_array(1, ['1', '2', '3'], false)
                                                  //   returns 3: true
                                                  //   returns 3: true
                                                  //   example 4: in_array(1, ['1', '2', '3'], true)
                                                  //   returns 4: false

    var key = ''
    var strict = !!argStrict

    // we prevent the double check (strict && arr[key] === ndl) || (!strict && arr[key] === ndl)
    // in just one for, in order to improve the performance
    // deciding wich type of comparation will do before walk array
    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return true
            }
        }
    } else {
        for (key in haystack) {
            if (haystack[key] == needle) { // eslint-disable-line eqeqeq
                return true
            }
        }
    }

    return false
}


function empty (mixedVar) {
    //  discuss at: http://locutus.io/php/empty/
    // original by: Philippe Baumann
    //    input by: Onno Marsman (https://twitter.com/onnomarsman)
    //    input by: LH
    //    input by: Stoyan Kyosev (http://www.svest.org/)
    // bugfixed by: Kevin van Zonneveld (http://kvz.io)
    // improved by: Onno Marsman (https://twitter.com/onnomarsman)
    // improved by: Francesco
    // improved by: Marc Jansen
    // improved by: Rafał Kukawski (http://blog.kukawski.pl)
    //   example 1: empty(null)
    //   returns 1: true
    //   example 2: empty(undefined)
    //   returns 2: true
    //   example 3: empty([])
    //   returns 3: true
    //   example 4: empty({})
    //   returns 4: true
    //   example 5: empty({'aFunc' : function () { alert('humpty'); } })
    //   returns 5: false

    var undef
    var key
    var i
    var len
    var emptyValues = [undef, null, false, 0, '', '0']

    for (i = 0, len = emptyValues.length; i < len; i++) {
        if (mixedVar === emptyValues[i]) {
            return true
        }
    }

    if (typeof mixedVar === 'object') {
        for (key in mixedVar) {
            if (mixedVar.hasOwnProperty(key)) {
                return false
            }
        }
        return true
    }

    return false
}


function formSubmit($url,$data,$success,$error){

    pageAlert('#form-alert-message','hide');

    $("[id$='-form-input']").removeClass('is-invalid');
    $("[id$='-form-error']").html('');

    var errorMesg = 'Some fields are invalid please fix them';
    if($global_lang == 'ar'){
        errorMesg = 'بعض الحقول غير صالحة يرجى إصلاحها';
    }

    if(isObject($data)){
        $options = {
            url: $url,
            type: 'POST',
            data: $data,
            processData: false,
            contentType: false,
            success: function($response){
                removeLoading();
                if($success){
                    $success($response);
                }
            },
            error: function($response){
                removeLoading();
                if($error){
                    $error($response);
                }


                pageAlert('#form-alert-message','error',errorMesg);

                var errors = $.parseJSON($response.responseText);
                $.each(errors.errors, function (key, value) {
                    $('#'+key+'-form-input').addClass('is-invalid');
                    $('#' + key + '-form-error').html(value.join('<br />'));
                });

            }
        };
    }else{
        $options = {
            url: $url,
            type: 'POST',
            data: $data,
            success: function($response){
                removeLoading();
                if($success){
                    $success($response);
                }
            },
            error: function($response){
                removeLoading();
                if($error){
                    $error($response);
                }

                pageAlert('#form-alert-message','error',errorMesg);

                var errors = $.parseJSON($response.responseText);
                $.each(errors.errors, function (key, value) {
                    $('#'+key+'-form-input').addClass('is-invalid');
                    $('#' + key + '-form-error').html(value.join('<br />'));
                });

            }
        }
    }

    addLoading();

    $.ajax($options).fail(function($response){
        removeLoading();
        if($error){
            $error($response);
        }

        pageAlert('#form-alert-message','error',errorMesg);

        var errors = $.parseJSON($response.responseText);
        $.each(errors.errors, function (key, value) {
            $('#'+key+'-form-input').addClass('is-invalid');
            $('#' + key + '-form-error').html(value.join('<br />'));
        });

    });

   /* $.post($url,$data,function($response){
        removeLoading();
        if($success){
            $success($response);
        }
    }).fail(function($response){
        removeLoading();
        if($error){
            $error($response);
        }

        pageAlert('#form-alert-message','error','Some fields are invalid please fix them');

        var errors = $.parseJSON($response.responseText);
        $.each(errors.errors, function (key, value) {
            $('#'+key+'-form-input').addClass('is-invalid');
            $('#' + key + '-form-error').html(value.join('<br />'));
        });

    });*/
}



function urlIframe($url,$headerTitle){


    if($url.includes('?')){
        $url = $url+"&without_navbar=true";
    }else{
        $url = $url+"?without_navbar=true";
    }

    $('#modal-iframe-url').height(($(window).height()-150)+"px");
    $('#modal-iframe-width').css('max-width',($(window).width()-100)+"px");

    $('#modal-iframe-title').text($headerTitle);


    $('#modal-iframe').modal('show');

    $('#modal-iframe-url').hide();

    $('#modal-iframe-url').attr('src',$url);
    $('#modal-iframe-image').show();

    // $('#modal-iframe-url').load(function(){
        $('#modal-iframe-image').hide();
        $('#modal-iframe-url').show();
    // });
}

function ajaxSelect2($formClass,$controllerFunction,$chars,$holder){

    if($chars == undefined){
        $chars = 1;
    }

    if($holder === undefined){
        $holder = "Select a value";
    }

    $($formClass).select2({
        ajax: {
            allowClear: true,
            placeholder: "Select a value",
            multiple: true,
            maximumSelectionSize: 1,

            method: 'GET',
            url: $systemURL+"ajax",
            dataType: 'json',
            delay: 500,
            data: function (params) {
                return {
                    type : $controllerFunction,
                    word: params.term
                };
            },
            processResults: function (data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data,
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: $chars,
        templateResult: function (data) {
            if (data.loading === true) { // adjust for custom placeholder values
                return data.text;
            }

            return data.value;
        },
        templateSelection: function(data, container){
            if(data.text != ''){
                return data.text;
            }
            return data.value;
        }
    });

}


function simpleAjaxSelect2($formClass,$controllerFunction,$chars,$holder,$lang){
    if($chars === undefined){
        $chars = 1;
    }

    if($holder === undefined){
        $holder = "Select a value";
    }

    if ($lang === undefined){
        $lang = 'ar';
    }

    $($formClass).select2({
        minimumInputLength: $chars,
        placeholder: $holder,
        allowClear: true,
        dropdownAutoWidth : true,
        dir : $lang === 'ar' ? 'rtl' : 'ltr',
        ajax: {
            url: $systemURL+"ajax",
            dataType: 'json',
            delay: 400,
            data: function (params) {
                return {
                    type : $controllerFunction,
                    word: params.term
                };
            },
            processResults: function (data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data,
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        templateResult: function (data) {
            if (data.loading === true) { // adjust for custom placeholder values
                return data.text;
            }

            return data.value;
        },
        templateSelection: function(data, container){
            if(data.text != ''){
                return data.text;
            }
            return data.value;
        }
    });

    }

function noAjaxSelect2($formClass,$holder,$lang){

    if($holder === undefined){
        $holder = "Select a value";
    }
    if ($lang === undefined){
        $lang = 'ar';
    }
    $($formClass).select2({
        placeholder: $holder,
        allowClear: true,
        dropdownAutoWidth: true,
        dir : $lang === 'ar' ? 'rtl' : 'ltr',

    });

}

function pageAlert($selector,$type,$message){

    if($type == 'hide'){
        $($selector).hide();
    }else if($type == 'error'){
        $($selector).html("<div class=\"alert alert-danger fade show\" role=\"alert\">\n" +
            "                                <div class=\"alert-icon\"><i class=\"flaticon-warning\"></i></div>\n" +
            "                                <div class=\"alert-text\">"+$message+"</div>\n" +
            "                                <div class=\"alert-close\">\n" +
            "                                    <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n" +
            "                                        <span aria-hidden=\"true\"><i class=\"la la-close\"></i></span>\n" +
            "                                    </button>\n" +
            "                                </div>\n" +
            "                            </div>").show();
     }else if($type == 'success'){
    $($selector).html("<div class=\"alert alert-success fade show\" role=\"alert\">\n" +
        "                                <div class=\"alert-icon\"><i class=\"flaticon-warning\"></i></div>\n" +
        "                                <div class=\"alert-text\">"+$message+"</div>\n" +
        "                                <div class=\"alert-close\">\n" +
        "                                    <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n" +
        "                                        <span aria-hidden=\"true\"><i class=\"la la-close\"></i></span>\n" +
        "                                    </button>\n" +
        "                                </div>\n" +
        "                            </div>").show();
}

}

function deleteRecord($routeName,$reload){

    if(!confirm("Do you want to delete this record?")){
        return false;
    }

    if($reload == undefined){
        $reload = 3000;
    }
    addLoading();

    $.post(
        $routeName,
        {
            '_method':'DELETE',
            '_token':$('meta[name="csrf-token"]').attr('content')
        },
        function(response){
            removeLoading();
            if(isJSON(response)){
                $data = response;
                if($data.status == true){
                    toastr.success($data.message, 'Success !', {"closeButton": true});
                    if($reload){
                        setTimeout(function(){location.reload();},$reload);
                    }
                }else{
                    toastr.error($data.message, 'Error !', {"closeButton": true});
                }
            }
        }
    )
}

function addLoading(){
    $.blockUI({ css: {
        border: 'none',
        padding: '15px',
        backgroundColor: '#000',
        '-webkit-border-radius': '10px',
        '-moz-border-radius': '10px',
        opacity: .5,
        color: '#fff'
    } });
}

function removeLoading(){
    $.unblockUI();
}

function isJSON(m) {
    if (typeof m == 'object') {
        try{ m = JSON.stringify(m); }
        catch(err) { return false; } }

    if (typeof m == 'string') {
        try{ m = JSON.parse(m); }
        catch (err) { return false; } }

    if (typeof m != 'object') { return false; }
    return true;

};

function addMultiRowParameter(){
    $data = "<div class=\"form-group row\">\n" +
        "                                    <div class=\"col-md-3\">\n" +
        "                                        <label>Value*</label>\n" +
        "                                        <input class=\"form-control\" id=\"options-value-0-form-input\" autocomplete=\"off\" name=\"options[value][]\" type=\"text\">\n" +
        "                                        <div class=\"invalid-feedback\" id=\"options-value-0-form-error\"></div>\n" +
        "                                    </div>\n" +
        "\n" +
        "                                    <div class=\"col-md-4\">\n" +
        "                                        <label>Name (Arabic)*</label>\n" +
        "                                        <input class=\"form-control\" id=\"options-name_ar-0-form-input\" autocomplete=\"off\" name=\"options[name_ar][]\" type=\"text\">\n" +
        "                                        <div class=\"invalid-feedback\" id=\"options-name_ar-0-form-error\"></div>\n" +
        "                                    </div>\n" +
        "\n" +
        "                                    <div class=\"col-md-4\">\n" +
        "                                        <label>Name (English)*</label>\n" +
        "                                        <input class=\"form-control\" id=\"options-name_en-0-form-input\" autocomplete=\"off\" name=\"options[name_en][]\" type=\"text\">\n" +
        "                                        <div class=\"invalid-feedback\" id=\"options-name_en-0-form-error\"></div>\n" +
        "                                    </div>\n" +
        "                                    <div class=\"col-md-1\">\n" +
        "                                        <label style=\"color: #FFF;\">-</label>\n" +
        "                                        <a href=\"javascript:void(0);\" onclick=\"removeMultiRowParameter($(this));\">\n" +
        "                                            <i class=\"flaticon-delete form-control\" style=\"color: red;\"></i>\n" +
        "                                        </a>\n" +
        "                                    </div>\n" +
        "                                </div>";


    $('.multi-row-data').append($data);
}



function removeMultiRowParameter($this){
    $this.closest('.row').remove();
}


