"use strict";
// Class definition

var KBootstrapDatepicker = function () {

    var arrows;
    var RLClass;
    if (KUtil.isRTL()) {
        arrows = {
            leftArrow: '<i class="la la-angle-right"></i>',
            rightArrow: '<i class="la la-angle-left"></i>'
        }
        RLClass = 'right';
    } else {
        arrows = {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        }
        RLClass = 'left';
    }
    
    // Private functions
    var demos = function () {
        // minimum setup
        $('.k_datepicker_1, #k_datepicker_1_validate').datepicker({
            rtl: KUtil.isRTL(),
            todayHighlight: true,
            orientation: "bottom left",
            templates: arrows,
            format: 'yyyy-mm-dd'
        });


        $('.k_datetimepicker_2').datetimepicker({
            rtl: KUtil.isRTL(),
            todayHighlight: true,
            orientation: "bottom "+RLClass,
            templates: arrows,
            format: 'yyyy-mm-dd hh:ii:ss'
        });

        // minimum setup for modal demo
        $('#k_datepicker_1_modal').datepicker({
            rtl: KUtil.isRTL(),
            todayHighlight: true,
            orientation: "bottom left",
            templates: arrows,
            format: 'yyyy-mm-dd'

        });

        // input group layout 
        $('#k_datepicker_2, #k_datepicker_2_validate').datepicker({
            rtl: KUtil.isRTL(),
            todayHighlight: true,
            orientation: "bottom left",
            templates: arrows,
            format: 'yyyy-mm-dd'

        });

        // input group layout for modal demo
        $('#k_datepicker_2_modal').datepicker({
            rtl: KUtil.isRTL(),
            todayHighlight: true,
            orientation: "bottom left",
            templates: arrows,
            format: 'yyyy-mm-dd'

        });

        // enable clear button 
        $('#k_datepicker_3, #k_datepicker_3_validate').datepicker({
            rtl: KUtil.isRTL(),
            todayBtn: "linked",
            clearBtn: true,
            todayHighlight: true,
            templates: arrows,
            format: 'yyyy-mm-dd'

        });

        // enable clear button for modal demo
        $('#k_datepicker_3_modal').datepicker({
            rtl: KUtil.isRTL(),
            todayBtn: "linked",
            clearBtn: true,
            todayHighlight: true,
            templates: arrows,
            format: 'yyyy-mm-dd'

        });

        // orientation 
        $('#k_datepicker_4_1').datepicker({
            rtl: KUtil.isRTL(),
            orientation: "top left",
            todayHighlight: true,
            templates: arrows,
            format: 'yyyy-mm-dd'

        });

        $('#k_datepicker_4_2').datepicker({
            rtl: KUtil.isRTL(),
            orientation: "top right",
            todayHighlight: true,
            templates: arrows,
            format: 'yyyy-mm-dd'

        });

        $('#k_datepicker_4_3').datepicker({
            rtl: KUtil.isRTL(),
            orientation: "bottom left",
            todayHighlight: true,
            templates: arrows,
            format: 'yyyy-mm-dd'

        });

        $('#k_datepicker_4_4').datepicker({
            rtl: KUtil.isRTL(),
            orientation: "bottom right",
            todayHighlight: true,
            templates: arrows,
            format: 'yyyy-mm-dd'

        });

        // range picker
        $('.k_datepicker_5,#k_datepicker_5').datepicker({
            rtl: KUtil.isRTL(),
            todayHighlight: true,
            templates: arrows,
            format: 'yyyy-mm-dd'

        });

         // inline picker
        $('#k_datepicker_6,.k_datepicker_6').datepicker({
            rtl: KUtil.isRTL(),
            todayHighlight: true,
            templates: arrows,
            format: 'yyyy-mm-dd'

        });
    }

    return {
        // public functions
        init: function() {
            demos(); 
        }
    };
}();

jQuery(document).ready(function() {    
    KBootstrapDatepicker.init();
});