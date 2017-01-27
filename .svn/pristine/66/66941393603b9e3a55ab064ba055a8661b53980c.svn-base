var displayNotify = function(type, msg) {
    switch(type) {
        default:
            $('div[id=notify-container]').noty({
                layout : 'topRight',
                theme : 'noty_theme_simpla',
                type : 'alert',
                text : msg,
                timeout : 6000
            });
            break;
        case 'warn':
            $('div[id=notify-container]').noty({
                layout : 'topRight',
                theme : 'noty_theme_simpla',
                type : 'warning',
                text : msg,
                timeout : 6000
            });
            break;
        case 'error':
            $('div[id=notify-container]').noty({
                layout : 'topRight',
                theme : 'noty_theme_simpla',
                type : 'error',
                text : msg,
                timeout : 6000
            });
            break;
        case 'alert':
            $('div[id=notify-container]').noty({
                layout : 'topRight',
                theme : 'noty_theme_simpla',
                type : 'alert',
                text : msg,
                timeout : 5000
            });
            break;
        case 'notify':
            $('div[id=notify-container]').noty({
                layout : 'topRight',
                theme : 'noty_theme_simpla',
                type : 'notification',
                text : msg,
                timeout : 5000
            });
            break;
        case 'success':
            $('div[id=notify-container]').noty({
                layout : 'topRight',
                theme : 'noty_theme_simpla',
                type : 'success',
                text : msg,
                timeout : 5000
            });
            break;
    }
};

var ajaxError = function(request, type, errorThrown) {
    //$.unblockUI();
    var message = "There was an error with the AJAX request.\n";
    switch (type) {
        case 'timeout':
            message += "The request timed out.";
            break;
        case 'notmodified':
            message += "The request was not modified but was not retrieved from the cache.";
            break;
        case 'parsererror':
            message += "XML/Json format is bad.";
            break;
        default:
            message += "HTTP Error (" + request.status + " " + request.statusText + ").";
            if(request.status == 403){ //si ajaxerror devuelve = HTTP Error (403 Forbidden) redirecciona a Login
                alert('Session Expired');
                window.location.replace('/site/index');
            }
    }
    message += "\n";
    displayNotify('error', message);
};

var ajaxSuccess = function(data, newValue) {
    console.log(JSON.stringify(data));
    if (data.requestresult == 'ok') {
        if (data.redirecturl) {
            window.location.replace(data.redirecturl);
        } else {
            if (data.message) {
                displayNotify('success', data.message);
            } else {
                displayNotify('success', 'Transaction seems ok');
            }
        }
    } else {
        displayNotify('error', data.message);
    }
};

var ShowRequiredField = function(id) {
    if ($('span[id="Notification' + id + '"]').hasClass('success')) {
        $('span[id="Notification' + id + '"]').removeClass('success');
    }
    $('span[id="Notification' + id + '"]').addClass('error');
    $('span[id="Notification' + id + '"]').html(' This field is Required!!');
    $('span[id="Notification' + id + '"]').show(500);
}

var HideValidationField = function(id) {
    if ($('span[id="Notification' + id + '"]').hasClass('success')) {
        $('span[id="Notification' + id + '"]').removeClass('success');
    }
    if ($('span[id="Notification' + id + '"]').hasClass('error')) {
        $('span[id="Notification' + id + '"]').removeClass('error');
    }
    $('span[id="Notification' + id + '"]').html('');
    $('span[id="Notification' + id + '"]').hide(500);
}

$.fn.equalHeights = function(minHeight, maxHeight) {
    tallest = (minHeight) ? minHeight : 0;
    this.each(function() {
        if ($(this).height() > tallest) {
            tallest = $(this).height();
        }
    });
    if ((maxHeight) && tallest > maxHeight)
        tallest = maxHeight;
    return this.each(function() {
        $(this).height(tallest).css({
            'overflow' : 'visible'
            });
    });
};

var templateSetup = new Array();


/**
 * Add a new template function
 * @param function func the function to be called on a jQuery object
 * @param boolean prioritary set to true to call the function before all others (optional)
 * @return void
 */

$.fn.templateDecorations = function(){
    $(this).find('input[type=radio].mini-switch, input[type=checkbox].mini-switch').hide().after('<span class="mini-switch-replace"></span>').next().click(function() {
        $(this).prev().click();
    });
    $(this).find('select[chzn="simpleselect"]').chosen();
    $(this).find('select[chzn="multipleselect"]').chosen();
    $(this).find('select[chzn="simpleselectallowdeselect"]').chosen({
        allow_single_deselect : true
    });
    $(this).find('select[chzn="twosidemultipleselect"]').simpla_Multiselect();
    $('[tooltip="tooltip"]').tooltip();
}

$.fn.addTemplateSetup = function(func, prioritary) {
    if (prioritary) {
        templateSetup.unshift(func);
    } else {
        templateSetup.push(func);
    }
};

$.fn.applyTemplateSetup = function() {
    var max = templateSetup.length;
    for (var i = 0; i < max; ++i) {
        templateSetup[i].apply(this);
    }
    return this;
};

// Common (mobile and standard) template setup
$.fn.addTemplateSetup(function() {

    // Check all checkboxes when the one in a table head is checked:
    $('.check-all').click(function() {
        $(this).parent().parent().parent().parent().find("input[type='checkbox']").attr('checked', $(this).is(':checked'));
    });
    $('a[rel*=modal]').facebox();
    // Applies modal window to any link with attribute rel="modal"

    $(this).templateDecorations();

    this.find('input[datetimepicker]').datetimepicker({
        dateFormat : "yy-mm-dd",
        timeFormat : "hh:mm:ss"
    });

    this.find('input[datepicker]').datepicker({
        dateFormat : "yy-mm-dd"
    });


    $('div[row="shortcut-buttons-set"]').equalHeights();

   /*
    $.extend( $.fn.dataTable.defaults, {
        "aLengthMenu": [
            [25, 50, 100, 200, -1],
            [25, 50, 100, 200, "All"]
        ],
        "bStateSave": true,
        "fnStateLoad": function (oSettings, oData) {
            var o;
            $.ajax({
                type: "POST",
                url: "/datatables/loadstate?id=" + $(this).attr('id'),
                async: false,
                }).done(function(data) {
                    o = $.parseJSON(data);
            });
            return o;
            },
        "fnStateSave": function (oSettings, oData) {
            $.ajax({
                type: "POST",
                url: "/datatables/savestate?id=" + $(this).attr('id'),
                data: {
                    state : JSON.stringify(oData)
                },
            });
        },
        "bInfo": true,
        "bProcessing": false,
        "bAutoWidth": false,
        "sPaginationType": "full_numbers",
        "iDisplayLength": 25,
        "fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
            return "Showing " + iStart +" to "+ iEnd + " of " + iTotal + " Entries";
        },
        "sDom": 'T<"clear">lfrtip',
        "oTableTools": {
            "sSwfPath": "<?php echo Yii::app()->theme->baseUrl;?>/swf/copy_csv_xls_pdf.swf"
        }
    }); */

    /*
    $('table').bind('processing', function (e, oSettings, bShow) {
        if (bShow) {
            $(this).block();
        } else {
            $(this).unblock();
        }
    });
    */

    $.unblockUI();
});

