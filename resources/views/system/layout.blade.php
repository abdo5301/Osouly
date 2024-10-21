<!DOCTYPE html>

<html @if(App::getLocale() == 'en') lang="en" direction="ltr" @else lang="ar" direction="rtl" @endif>

<!-- begin::Head -->
<head>
    <meta charset="utf-8" />
    <title>{{ucfirst(request()->route()->getActionMethod())}} - {{__('Osouly System')}}</title>

    <meta name="description" content="{{__('Osouly System')}}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
        WebFont.load({
            google: {"families":["Poppins:300,400,500,600,700"]},
            active: function() {
                sessionStorage.fonts = true;
            }
        });

    </script>

    <!--end::Web font -->

@if(App::getLocale() == 'en')

    <!--begin::Page Vendors Styles -->
        <link href="{{asset('assets/vendors/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />

        <!--begin:: Global Mandatory Vendors -->
        <link href="{{asset('assets/vendors/general/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet" type="text/css" />

        <link href="{{asset('assets/vendors/general/tether/dist/css/tether.css')}}" rel="stylesheet" type="text/css" />

@else

        <link href="https://fonts.googleapis.com/css?family=Cairo&display=swap" rel="stylesheet">

    <style>
        @import url(//fonts.googleapis.com/earlyaccess/droidarabickufi.css);
        body{
            font-family: 'Cairo', sans-serif !important;
        }
        th{
            font-weight: bold !important;
        }
    </style>
    <!--begin::Page Vendors Styles -->
        <link href="{{asset('assets/vendors/custom/datatables/datatables.bundle.rtl.css')}}" rel="stylesheet" type="text/css" />

        <!--begin:: Global Mandatory Vendors -->
        <link href="{{asset('assets/vendors/general/perfect-scrollbar/css/perfect-scrollbar.rtl.css')}}" rel="stylesheet" type="text/css" />

        <link href="{{asset('assets/vendors/general/tether/dist/css/tether.rtl.css')}}" rel="stylesheet" type="text/css" />

    @endif



    <link href="{{asset('assets/vendors/general/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/general/bootstrap-datetime-picker/css/bootstrap-datetimepicker.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/general/bootstrap-timepicker/css/bootstrap-timepicker.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/general/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/general/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/general/bootstrap-select/dist/css/bootstrap-select.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/general/nouislider/distribute/nouislider.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/general/owl.carousel/dist/assets/owl.carousel.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/general/owl.carousel/dist/assets/owl.theme.default.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/general/dropzone/dist/dropzone.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/general/summernote/dist/summernote.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/general/bootstrap-markdown/css/bootstrap-markdown.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/general/animate.css/animate.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/general/toastr/build/toastr.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/general/morris.js/morris.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/general/sweetalert2/dist/sweetalert2.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/general/socicon/css/socicon.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/custom/vendors/line-awesome/css/line-awesome.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/custom/vendors/flaticon/flaticon.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/custom/vendors/flaticon2/flaticon.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendors/custom/vendors/fontawesome5/css/all.min.css')}}" rel="stylesheet" type="text/css" />

    <!--end:: Global Optional Vendors -->

    <!--begin::Global Theme Styles -->
    <!--begin::Global Theme Styles -->
    @if(App::getLocale() == 'en')
        <link href="{{asset('assets/demo/default/base/style.bundle.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/demo/default/skins/header/base/light.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/demo/default/skins/header/menu/light.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/demo/default/skins/brand/brand.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/demo/default/skins/aside/brand.css')}}" rel="stylesheet" type="text/css" />
    @else
        <link href="{{asset('assets/demo/default/base/style.bundle.rtl.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/demo/default/skins/header/base/light.rtl.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/demo/default/skins/header/menu/light.rtl.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/demo/default/skins/brand/brand.rtl.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/demo/default/skins/aside/brand.rtl.css')}}" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="//cdn.rawgit.com/morteza/bootstrap-rtl/v3.3.4/dist/css/bootstrap-rtl.min.css">

    @endif



    <link href="{{asset('css/node.css')}}" rel="stylesheet" type="text/css" />
    <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css" rel="stylesheet" type="text/css" />


    <!--end::Layout Skins -->
    <link rel="shortcut icon" href="{{asset('assets/media/logos/favicon.ico')}}" />


    <style>


        #modal-iframe-url{
            width: 100%;
            border: none;
        }


        #modal-iframe-width{
            max-width:95% !important;
        }

        .color-red{
            color: red !important;
        }

        .hidden-menu{
            display: none !important;
        }


        .note-editor.note-frame .note-editing-area .note-editable{
            padding-top: 20px;
        }
    </style>
    @yield('header')

    <style>

        body {
            font-size: {{setting('font_size')}}px !important;
            font-weight: {{setting('font_weight')}} !important;
        }


        .k-aside-menu .k-menu__nav > .k-menu__item > .k-menu__heading .k-menu__link-text, .k-aside-menu .k-menu__nav > .k-menu__item > .k-menu__link .k-menu__link-text {
            font-size: {{setting('menu_font_size')}}px !important;
            font-weight: {{setting('menu_font_weight')}} !important;
        }
        .k-aside-menu .k-menu__nav > .k-menu__item .k-menu__submenu .k-menu__item > .k-menu__heading .k-menu__link-text, .k-aside-menu .k-menu__nav > .k-menu__item .k-menu__submenu .k-menu__item > .k-menu__link .k-menu__link-text{
            font-size: {{setting('menu_font_size')}}px !important;
            font-weight: {{setting('menu_font_weight')}} !important;
        }

    </style>
    <style>
{{--    for limited words descriptions     --}}

        .more_info {
            position: relative;
            cursor: pointer;
        }

.more_info .title {
    position: absolute;
    top: 15px; /*must overlap parent element otherwise pop-up doesn't stay open when rolloing over '*/
    border: 1px solid rgba(0, 0, 0, 0.2);
    background-color: white;
    box-shadow: 1px 1px 3px;
    border-radius:4px;
    padding: 8px;
    left: 0;
    max-width: 240px;
    min-width: 180px;
    z-index: 100;
}
    </style>

    <script>
       // {{--$systemURL = 'http://<?php echo explode('.',str_replace(['http://','https://'],'',request()->root()))[0] ; ?>.mongez.org/system/';--}}
        $systemURL = '<?php echo env('APP_URL').'/system/'; ?>';
        $global_lang = '{{App::getLocale()}}';
    </script>
</head>

<!-- end::Head -->

<!-- Dynamic Modal -->

<div style="z-index:10000000000" class="modal fade text-xs-left" id="modal-iframe" role="dialog" aria-labelledby="myModalLabe" aria-hidden="true">
    <div class="modal-dialog" id="modal-iframe-width" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card-body">
                    <div class="card-block">
                        <div class="row" style="text-align: center;">
                            <img id="modal-iframe-image" src="{{asset('assets/loading.gif')}}">
                            <iframe id="modal-iframe-url" style="display: none;" src=""></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Dynamic Modal -->


<!-- begin::Body -->

<body @if(request('without_navbar') == 'true') class="k-header--fixed k-header-mobile--fixed k-aside--enabled k-aside--fixed k-aside--minimize" @else class="k-header--fixed k-header-mobile--fixed k-aside--enabled k-aside--fixed" @endif>

<!-- begin:: Page -->

<!-- begin:: Header Mobile -->
<div id="k_header_mobile" class="k-header-mobile  k-header-mobile--fixed ">
    <div class="k-header-mobile__logo">
        <a href="{{route('system.dashboard')}}">
            <img alt="Logo" src="{{asset('assets/media/logos/osouly-logo.png')}}" />
        </a>
    </div>
    <div class="k-header-mobile__toolbar">
        <button class="k-header-mobile__toolbar-toggler k-header-mobile__toolbar-toggler--left" id="k_aside_mobile_toggler"><span></span></button>
        <button class="k-header-mobile__toolbar-topbar-toggler" id="k_header_mobile_topbar_toggler"><i class="flaticon-more"></i></button>
    </div>
</div>

<!-- end:: Header Mobile -->
<div class="k-grid k-grid--hor k-grid--root">
    <div class="k-grid__item k-grid__item--fluid k-grid k-grid--ver k-page">

        <!-- begin:: Aside -->
        <button class="k-aside-close " id="k_aside_close_btn"><i class="la la-close"></i></button>
        <div class="k-aside  k-aside--fixed 	k-grid__item k-grid k-grid--desktop k-grid--hor-desktop" id="k_aside">

            <!-- begin:: Aside -->
            <div class="k-aside__brand	k-grid__item " id="k_aside_brand">
                <div class="k-aside__brand-logo">
                    <a href="{{route('system.dashboard')}}">
                        <img alt="Logo" src="{{asset('assets/media/logos/osouly-logo.png')}}" />
                    </a>
                </div>
                <div class="k-aside__brand-tools">
                    <button class="k-aside__brand-aside-toggler k-aside__brand-aside-toggler--left" id="k_aside_toggler"><span></span></button>
                </div>
            </div>
            <!-- end:: Aside -->

            <!-- begin:: Aside Menu -->
            <div class="k-aside-menu-wrapper	k-grid__item k-grid__item--fluid" id="k_aside_menu_wrapper">
                <div id="k_aside_menu" class="k-aside-menu " data-kmenu-vertical="1" data-kmenu-scroll="1" data-kmenu-dropdown-timeout="500">
                    <ul class="k-menu__nav ">

                        <li class="k-menu__item" aria-haspopup="true"><a href="{{route('system.dashboard')}}" class="k-menu__link "><i class="k-menu__link-icon flaticon2-graphic"></i><span class="k-menu__link-text">{{__('Dashboard')}}</span></a></li>
                        <li class="k-menu__item {{seeMenu('system.calendar.index',true)}}" aria-haspopup="true"><a href="{{route('system.calendar.index')}}" class="k-menu__link "><i class="k-menu__link-icon flaticon2-calendar-2"></i><span class="k-menu__link-text">{{__('Calendar')}}</span></a></li>

                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.ads.index,system.ads.create,system.ads.edit,system.ads.show,system.slider.index,system.slider.create,system.slider.edit,system.slider.show,system.page.index,system.page.create,system.page.edit,system.page.show,system.service.index,system.service.create,system.service.edit,system.service.show,system.package.index,system.package.create,system.package.edit,system.package.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon2-cardiogram"></i><span class="k-menu__link-text">{{__('Website')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">

                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.page.index,system.page.create,system.page.edit,system.page.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Pages')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item {{seeMenu('system.page.index,system.page.show')}}" aria-haspopup="true">
                                                    <a href="{{route('system.page.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item {{seeMenu('system.page.create,system.page.edit')}}" aria-haspopup="true">
                                                    <a href="{{route('system.page.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.service.index,system.service.create,system.service.edit,system.service.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Services')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item  {{seeMenu('system.service.index,system.service.show')}}" aria-haspopup="true">
                                                    <a href="{{route('system.service.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item  {{seeMenu('system.service.create,system.service.edit')}}" aria-haspopup="true">
                                                    <a href="{{route('system.service.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.package.index,system.package.create,system.package.edit,system.package.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Packages')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item  {{seeMenu('system.package.index,system.package.show')}}" aria-haspopup="true">
                                                    <a href="{{route('system.package.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item  {{seeMenu('system.package.create,system.package.edit')}}" aria-haspopup="true">
                                                    <a href="{{route('system.package.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <li class="k-menu__item  k-menu__item--submenu  {{seeMenu('system.slider.index,system.slider.create,system.slider.edit,system.slider.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Sliders')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item  {{seeMenu('system.slider.index,system.slider.show')}}" aria-haspopup="true">
                                                    <a href="{{route('system.slider.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item  {{seeMenu('system.slider.create,system.slider.edit')}}" aria-haspopup="true">
                                                    <a href="{{route('system.slider.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.ads.index,system.ads.create,system.ads.edit,system.ads.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Ads')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item  {{seeMenu('system.ads.index,system.ads.show')}}" aria-haspopup="true">
                                                    <a href="{{route('system.ads.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item  {{seeMenu('system.ads.create,system.ads.edit')}}" aria-haspopup="true">
                                                    <a href="{{route('system.ads.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>




                                </ul>
                            </div>
                        </li>



                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.owner.index,system.owner.create,system.owner.edit,system.owner.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon-users"></i><span class="k-menu__link-text">{{__('Owners')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>

                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.owner.index,system.owner.show')}}" aria-haspopup="true">
                                        <a href="{{route('system.owner.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}} @if(numModelRows('Client',['type'=>'owner','status'=>'pending']) > 0 )<span style="@if(App::getLocale() == 'ar') margin-right @else margin-left @endif: 25px;" class="k-badge  k-badge--danger k-badge--inline k-badge--pill"> {{numModelRows('Client',['type'=>'owner','status'=>'pending'])}} </span> @endif</span>
                                        </a>
                                    </li>
                                    <li class="k-menu__item {{seeMenu('system.owner.create,system.owner.edit')}}" aria-haspopup="true">
                                        <a href="{{route('system.owner.create')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.renter.index,system.renter.create,system.renter.edit,system.renter.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon-users"></i><span class="k-menu__link-text">{{__('Renters')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>

                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.renter.index,system.renter.show')}}" aria-haspopup="true">
                                        <a href="{{route('system.renter.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}  @if(numModelRows('Client',['type'=>'renter','status'=>'pending']) > 0 )<span style="@if(App::getLocale() == 'ar') margin-right @else margin-left @endif: 25px;" class="k-badge  k-badge--danger k-badge--inline k-badge--pill"> {{numModelRows('Client',['type'=>'renter','status'=>'pending'])}} </span> @endif</span>
                                        </a>
                                    </li>
                                    <li class="k-menu__item {{seeMenu('system.renter.create,system.renter.edit')}}" aria-haspopup="true">
                                        <a href="{{route('system.renter.create')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.both.index,system.both.create,system.both.edit,system.both.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon-users"></i><span class="k-menu__link-text">{{__('Owners & Renters')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>

                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.both.index,system.both.show')}}" aria-haspopup="true">
                                        <a href="{{route('system.both.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}} @if(numModelRows('Client',['type'=>'both','status'=>'pending']) > 0 )<span style="@if(App::getLocale() == 'ar') margin-right @else margin-left @endif: 25px;" class="k-badge  k-badge--danger k-badge--inline k-badge--pill"> {{numModelRows('Client',['type'=>'both','status'=>'pending'])}} </span> @endif</span>
                                        </a>
                                    </li>
                                    <li class="k-menu__item {{seeMenu('system.both.create,system.both.edit')}}" aria-haspopup="true">
                                        <a href="{{route('system.both.create')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.property.index,system.property.create,system.property.edit,system.property.show,system.property.upload-excel,system.property-type.index,system.property-type.create,system.property-type.edit,system.data-source.index,system.data-source.create,system.data-source.edit,system.purpose.index,system.purpose.create,system.purpose.edit,system.property-features.index,system.property-features.create,system.property-features.edit,system.property.special-properties,system.property-dues.index,system.property-dues.create,system.property-dues.edit,system.property-dues.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon-buildings"></i><span class="k-menu__link-text">{{__('Properties')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.property.index,system.property.show')}}" aria-haspopup="true">
                                        <a href="{{route('system.property.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View All')}} @if( numModelRows('Property',['publish'=>0]) > 0 )<span style="@if(App::getLocale() == 'ar') margin-right @else margin-left @endif: 25px;" class="k-badge  k-badge--danger k-badge--inline k-badge--pill"> {{numModelRows('Property',['publish'=>0])}} </span> @endif</span>
                                        </a>
                                    </li>

                                    <li class="k-menu__item {{seeMenu('system.property.create,system.property.edit')}}" aria-haspopup="true">
                                        <a href="{{route('system.property.create')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                        </a>
                                    </li>

                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.property-dues.index,system.property-dues.create,system.property-dues.edit,system.property-dues.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Property Dues')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item {{seeMenu('system.property-dues.index,system.property-dues.show')}}" aria-haspopup="true">
                                                    <a href="{{route('system.property-dues.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item {{seeMenu('system.property-dues.create,system.property-dues.edit')}}" aria-haspopup="true">
                                                    <a href="{{route('system.property-dues.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.property-features.index,system.property-features.create,system.property-features.edit',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Property Features')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item {{seeMenu('system.property-features.index,system.property-features.show')}}" aria-haspopup="true">
                                                    <a href="{{route('system.property-features.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item {{seeMenu('system.property-features.create,system.property-features.edit')}}" aria-haspopup="true">
                                                    <a href="{{route('system.property-features.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.property-type.index,system.property-type.create,system.property-type.edit',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Property Types')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item {{seeMenu('system.property-type.index,system.property-type.show')}}" aria-haspopup="true">
                                                    <a href="{{route('system.property-type.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item {{seeMenu('system.property-type.create,system.property-type.edit')}}" aria-haspopup="true">
                                                    <a href="{{route('system.property-type.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    {{--<li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.data-source.index,system.data-source.create,system.data-source.edit',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Data Source')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>--}}
                                    {{--<div class="k-menu__submenu "><span class="k-menu__arrow"></span>--}}
                                    {{--<ul class="k-menu__subnav">--}}
                                    {{--<li class="k-menu__item {{seeMenu('system.data-source.index')}}" aria-haspopup="true">--}}
                                    {{--<a href="{{route('system.data-source.index')}}" class="k-menu__link ">--}}
                                    {{--<i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>--}}
                                    {{--</a>--}}
                                    {{--</li>--}}
                                    {{--<li class="k-menu__item {{seeMenu('system.data-source.create')}}" aria-haspopup="true">--}}
                                    {{--<a href="{{route('system.data-source.create')}}" class="k-menu__link ">--}}
                                    {{--<i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>--}}
                                    {{--</a>--}}
                                    {{--</li>--}}
                                    {{--</ul>--}}
                                    {{--</div>--}}
                                    {{--</li>--}}


                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.purpose.index,system.purpose.create,system.purpose.edit',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Purpose')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item {{seeMenu('system.purpose.index')}}" aria-haspopup="true">
                                                    <a href="{{route('system.purpose.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item {{seeMenu('system.purpose.create')}}" aria-haspopup="true">
                                                    <a href="{{route('system.purpose.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>


                                    {{--<li class="k-menu__item {{seeMenu('system.property.upload-excel')}}" aria-haspopup="true">--}}
                                        {{--<a href="{{route('system.property.upload-excel')}}" class="k-menu__link ">--}}
                                            {{--<i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Upload Excel')}}</span>--}}
                                        {{--</a>--}}
                                    {{--</li>--}}

                                </ul>
                            </div>
                        </li>

                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.special-property.index,system.special-property.create,system.special-property.edit,system.special-property.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon-star"></i><span class="k-menu__link-text">{{__('Special Properties')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.special-property.index,system.special-property.show')}}" aria-haspopup="true">
                                        <a href="{{route('system.special-property.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                        </a>
                                    </li>

                                    <li class="k-menu__item {{seeMenu('system.special-property.create,system.special-property.edit')}}" aria-haspopup="true">
                                        <a href="{{route('system.special-property.create')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>


                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.facility-companies.index,system.facility-companies.create,facility-companies.facility-companies.edit,system.facility-companies.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon-computer"></i><span class="k-menu__link-text">{{__('Facility Companies')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.facility-companies.index,system.facility-companies.show')}}" aria-haspopup="true">
                                        <a href="{{route('system.facility-companies.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                        </a>
                                    </li>

                                    <li class="k-menu__item {{seeMenu('system.facility-companies.create,system.facility-companies.edit')}}" aria-haspopup="true">
                                        <a href="{{route('system.facility-companies.create')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.request.index,system.request.create,system.request.edit,system.request.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon-search-1"></i><span class="k-menu__link-text">{{__('Requests')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.request.index,system.request.show')}}" aria-haspopup="true">
                                        <a href="{{route('system.request.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                        </a>
                                    </li>

                                    <li class="k-menu__item {{seeMenu('system.request.create,system.request.edit')}}" aria-haspopup="true">
                                        <a href="{{route('system.request.create')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.client-package.index,system.client-package.create,system.client-package.edit,system.client-package.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon2-box"></i><span class="k-menu__link-text">{{__('Client Packages')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.client-package.index,system.client-package.show')}}" aria-haspopup="true">
                                        <a href="{{route('system.client-package.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                        </a>
                                    </li>

                                    <li class="k-menu__item {{seeMenu('system.client-package.create,system.client-package.edit')}}" aria-haspopup="true">
                                        <a href="{{route('system.client-package.create')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>


                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.dues.index,system.dues.create,system.dues.edit,system.dues.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon2-lock"></i><span class="k-menu__link-text">{{__('Dues and Deductions')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item  {{seeMenu('system.dues.index,system.dues.show')}}" aria-haspopup="true">
                                        <a href="{{route('system.dues.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                        </a>
                                    </li>

                                    <li class="k-menu__item  {{seeMenu('system.dues.create,system.dues.edit')}}" aria-haspopup="true">
                                        <a href="{{route('system.dues.create')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.invoice.index,system.invoice.create,system.invoice.edit,system.invoice.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon2-list-2"></i><span class="k-menu__link-text">{{__('Invoices')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item  {{seeMenu('system.invoice.index,system.invoice.show')}}" aria-haspopup="true">
                                        <a href="{{route('system.invoice.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                        </a>
                                    </li>

{{--                                    <li class="k-menu__item  {{seeMenu('system.invoice.create,system.invoice.edit')}}" aria-haspopup="true">--}}
{{--                                        <a href="{{route('system.invoice.create')}}" class="k-menu__link ">--}}
{{--                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>--}}
{{--                                        </a>--}}
{{--                                    </li>--}}
                                </ul>
                            </div>
                        </li>

                        <li class="k-menu__item {{seeMenu('system.transaction.index,system.transaction.show',true)}}" aria-haspopup="true"><a href="{{route('system.transaction.index')}}" class="k-menu__link "><i class="k-menu__link-icon flaticon-arrows"></i><span class="k-menu__link-text">{{__('Transactions')}}</span></a></li>

                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.client-transaction.index,system.client-transaction.create,system.client-transaction.edit,system.client-transaction.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon-arrows"></i><span class="k-menu__link-text">{{__('Client Transactions')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item  {{seeMenu('system.client-transaction.index,system.client-transaction.show')}}" aria-haspopup="true">
                                        <a href="{{route('system.client-transaction.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                        </a>
                                    </li>

                                    <li class="k-menu__item  {{seeMenu('system.client-transaction.create,system.client-transaction.edit')}}" aria-haspopup="true">
                                        <a href="{{route('system.client-transaction.create')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>


                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.locker.index,system.locker.create,system.locker.edit,system.locker.show,system.income-reasons.index,system.income-reasons.create,system.income-reasons.edit,system.income-reasons.show,system.outcome-reasons.index,system.outcome-reasons.create,system.outcome-reasons.edit,system.outcome-reasons.show,system.income.index,system.income.create,system.income.edit,system.income.show,system.outcome.index,system.outcome.create,system.outcome.edit,system.outcome.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon2-list"></i><span class="k-menu__link-text">{{__('Payments Tree')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">

                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.locker.index,system.locker.create,system.locker.edit,system.locker.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Locker')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item  {{seeMenu('system.locker.index,system.locker.show')}}" aria-haspopup="true">
                                                    <a href="{{route('system.locker.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item  {{seeMenu('system.locker.create,system.locker.edit')}}" aria-haspopup="true">
                                                    <a href="{{route('system.locker.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.income-reasons.index,system.income-reasons.create,system.income-reasons.edit,system.income-reasons.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Income Reasons')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item  {{seeMenu('system.income-reasons.index,system.income-reasons.show')}}" aria-haspopup="true">
                                                    <a href="{{route('system.income-reasons.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item  {{seeMenu('system.income-reasons.create,system.income-reasons.edit')}}" aria-haspopup="true">
                                                    <a href="{{route('system.income-reasons.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.outcome-reasons.index,system.outcome-reasons.create,system.outcome-reasons.edit,system.outcome-reasons.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Outcome Reasons')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item  {{seeMenu('system.outcome-reasons.index,system.outcome-reasons.show')}}" aria-haspopup="true">
                                                    <a href="{{route('system.outcome-reasons.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item  {{seeMenu('system.outcome-reasons.create,system.outcome-reasons.edit')}}" aria-haspopup="true">
                                                    <a href="{{route('system.outcome-reasons.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.income.index,system.income.create,system.income.edit,system.income.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Incomes')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item  {{seeMenu('system.income.index,system.income.show')}}" aria-haspopup="true">
                                                    <a href="{{route('system.income.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item  {{seeMenu('system.income.create,system.income.edit')}}" aria-haspopup="true">
                                                    <a href="{{route('system.income.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.outcome.index,system.outcome.create,system.outcome.edit,system.outcome.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Outcomes')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item  {{seeMenu('system.outcome.index,system.outcome.show')}}" aria-haspopup="true">
                                                    <a href="{{route('system.outcome.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item  {{seeMenu('system.outcome.create,system.outcome.edit')}}" aria-haspopup="true">
                                                    <a href="{{route('system.outcome.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>


                                </ul>
                            </div>
                        </li>

                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.contract-template.index,system.contract-template.create,system.contract-template.edit,system.contract-template.show,system.contract.index,system.contract.create,system.contract.edit,system.contract.show',true)}} " aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon2-contract"></i><span class="k-menu__link-text">{{__('Contracts')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.contract.index,system.contract.show')}}" aria-haspopup="true">
                                        <a href="{{ route('system.contract.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                        </a>
                                    </li>
                                    {{--<li class="k-menu__item  {{seeMenu('system.contract.create,system.contract.edit',true)}}" aria-haspopup="true">--}}
                                        {{--<a href="{{route('system.contract.create')}}" class="k-menu__link ">--}}
                                            {{--<i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>--}}
                                        {{--</a>--}}
                                    {{--</li>--}}
                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.contract-template.index,system.contract-template.create,system.contract-template.edit,system.contract-template.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Contract Templates')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item  {{seeMenu('system.contract-template.index,system.contract-template.show')}}" aria-haspopup="true">
                                                    <a href="{{route('system.contract-template.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item  {{seeMenu('system.contract-template.create,system.contract-template.edit',true)}}" aria-haspopup="true">
                                                    <a href="{{route('system.contract-template.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                </ul>
                            </div>
                        </li>

                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.payment-methods.index,system.payment-methods.create,system.payment-methods.edit,system.payment-methods.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon2-list-3"></i><span class="k-menu__link-text">{{__('Payment Methods')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">

                                    <li class="k-menu__item {{seeMenu('system.payment-methods.index,system.payment-methods.show')}}" aria-haspopup="true">
                                        <a href="{{route('system.payment-methods.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                        </a>
                                    </li>

                                    <li class="k-menu__item {{seeMenu('system.payment-methods.create,system.payment-methods.edit')}}" aria-haspopup="true">
                                        <a href="{{route('system.payment-methods.create')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </li>



                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.sms.index,system.sms.create',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon2-send"></i><span class="k-menu__link-text">{{__('SMS')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.sms.index')}}" aria-haspopup="true">
                                        <a href="{{route('system.sms.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                        </a>
                                    </li>

                                    <li class="k-menu__item {{seeMenu('system.sms.create')}}" aria-haspopup="true">
                                        <a href="{{route('system.sms.create')}}" class="k-menu__link">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Send SMS')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="k-menu__item {{seeMenu('system.push-notifications.create',true)}}" aria-haspopup="true"><a href="{{route('system.push-notifications.create')}}" class="k-menu__link "><i class="k-menu__link-icon flaticon2-send"></i><span class="k-menu__link-text">{{__('Push Notifications')}} </span></a></li>


                        <li class="k-menu__item {{seeMenu('system.contact.index,system.contact.show',true)}}" aria-haspopup="true"><a href="{{route('system.contact.index')}}" class="k-menu__link "><i class="k-menu__link-icon flaticon2-talk"></i><span class="k-menu__link-text">{{__('Contact Us')}}  @if( numModelRows('Contactus',['is_read'=>0]) > 0 )<span style="@if(App::getLocale() == 'ar') margin-right @else margin-left @endif: 25px;" class="k-badge  k-badge--danger k-badge--inline k-badge--pill"> {{numModelRows('Contactus',['is_read'=>0])}} </span> @endif </span></a></li>


                        <li class="k-menu__item {{seeMenu('system.ticket.index,system.ticket.show,system.ticket.create',true)}}" aria-haspopup="true"><a href="{{route('system.ticket.index')}}" class="k-menu__link "><i class="k-menu__link-icon flaticon2-talk"></i><span class="k-menu__link-text">{{__('TS Tickets')}}  @if( numModelRows('Ticket',['status'=>'new']) > 0 )<span style="@if(App::getLocale() == 'ar') margin-right @else margin-left @endif: 25px;" class="k-badge  k-badge--danger k-badge--inline k-badge--pill"> {{numModelRows('Ticket',['status'=>'new'])}} </span> @endif</span></a></li>




                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.newsletter.index,system.newsletter.create,system.newsletter.edit,system.newsletter.show,system.campaign.index,system.campaign.create,system.campaign.edit,system.campaign.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon2-new-email"></i><span class="k-menu__link-text">{{__('Newsletter')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.newsletter.index')}}" aria-haspopup="true">
                                        <a href="{{route('system.newsletter.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                        </a>
                                    </li>

                                    <li class="k-menu__item {{seeMenu('system.newsletter.create')}}" aria-haspopup="true">
                                        <a href="{{route('system.newsletter.create')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                        </a>
                                    </li>

                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.campaign.index,system.campaign.create,system.campaign.edit,system.campaign.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Campaigns')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item {{seeMenu('system.campaign.index,system.campaign.show')}}" aria-haspopup="true">
                                                    <a href="{{route('system.campaign.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item {{seeMenu('system.campaign.create,system.campaign.edit')}}" aria-haspopup="true">
                                                    <a href="{{route('system.campaign.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                </ul>
                            </div>
                        </li>

                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.importer.index,system.importer.create,system.importer.edit,system.importer.show,system.importer.staff',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon-download"></i><span class="k-menu__link-text">{{__('Importer')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.importer.index,system.importer.show')}}" aria-haspopup="true">
                                        <a href="{{route('system.importer.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                        </a>
                                    </li>
                                    <li class="k-menu__item {{seeMenu('system.importer.create,system.importer.edit')}}" aria-haspopup="true">
                                        <a href="{{route('system.importer.create')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                        </a>
                                    </li>
                                    <li class="k-menu__item {{seeMenu('system.importer.staff,system.importer.staff')}}" aria-haspopup="true">
                                        <a href="{{route('system.importer.staff')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Staff Data')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>


                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.maintenance.index,system.maintenance.create,system.maintenance.edit,system.maintenance.show,system.maintenance-category.index,system.maintenance-category.create,system.maintenance-category.edit,system.maintenance-category.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon2-help"></i><span class="k-menu__link-text">{{__('Maintenance')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.maintenance.index,system.maintenance.show')}}" aria-haspopup="true">
                                        <a href="{{route('system.maintenance.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                        </a>
                                    </li>

                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.maintenance-category.index,system.maintenance-category.create,system.maintenance-category.edit,system.maintenance-category.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Maintenance Categories')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item  {{seeMenu('system.maintenance-category.index,system.maintenance-category.show')}}" aria-haspopup="true">
                                                    <a href="{{route('system.maintenance-category.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item  {{seeMenu('system.maintenance-category.create,system.maintenance-category.edit',true)}}" aria-haspopup="true">
                                                    <a href="{{route('system.maintenance-category.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    {{--<li class="k-menu__item {{seeMenu('system.maintenance.create,system.maintenance.edit')}}" aria-haspopup="true">--}}
                                        {{--<a href="{{route('system.maintenance.create')}}" class="k-menu__link ">--}}
                                            {{--<i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>--}}
                                        {{--</a>--}}
                                    {{--</li>--}}

                                </ul>
                            </div>
                        </li>



                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.call.index,system.call-status.index,system.call-status.create,system.call-status.edit,system.call-purpose.index,system.call-purpose.create,system.call-purpose.edit',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon2-phone"></i><span class="k-menu__link-text">{{__('Calls')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.call.index')}}" aria-haspopup="true">
                                        <a href="{{route('system.call.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                        </a>
                                    </li>

                                    {{--@foreach($call_purpose_menu as $key => $value)--}}
                                        {{--<li class="k-menu__item {{seeMenu('system.call.index')}}" aria-haspopup="true">--}}
                                            {{--<a href="{{route('system.call.index',['call_purpose_id'=>$value->id])}}" class="k-menu__link ">--}}
                                                {{--<i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{$value->{'name_'.\App::getLocale()} }} <span style="@if(App::getLocale() == 'ar') margin-right @else margin-left @endif: 10px;" class="k-badge  k-badge--success k-badge--inline k-badge--pill"> {{ (!staffCan('call-manage-all')) ?  numModelRows('Call',['call_purpose_id'=>$value->id,'created_by_staff_id'=>Auth::id()]) : numModelRows('Call',['call_purpose_id'=>$value->id])}}  </span></span>--}}
                                            {{--</a>--}}
                                        {{--</li>--}}
                                    {{--@endforeach--}}

                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.call-status.index,system.call-status.create,system.call-status.edit',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Call Status')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item {{seeMenu('system.call-status.index')}}" aria-haspopup="true">
                                                    <a href="{{route('system.call-status.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item {{seeMenu('system.call-status.create')}}" aria-haspopup="true">
                                                    <a href="{{route('system.call-status.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.call-purpose.index,system.call-purpose.create,system.call-purpose.edit',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Call Action')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item {{seeMenu('system.call-purpose.index')}}" aria-haspopup="true">
                                                    <a href="{{route('system.call-purpose.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item {{seeMenu('system.call-purpose.create')}}" aria-haspopup="true">
                                                    <a href="{{route('system.call-purpose.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                </ul>
                            </div>
                        </li>



                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.area-type.index,system.area-type.create,system.area-type.edit,system.area.index,system.area.create,system.area.edit',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon2-map"></i><span class="k-menu__link-text">{{__('Locations')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.area-type.index,system.area-type.create,system.area-type.edit',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Area Types')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item {{seeMenu('system.area-type.index')}}" aria-haspopup="true">
                                                    <a href="{{route('system.area-type.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                <li class="k-menu__item {{seeMenu('system.area-type.create,system.area-type.edit')}}" aria-haspopup="true">
                                                    <a href="{{route('system.area-type.create')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.area.index,system.area.create,system.area.edit',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><span class="k-menu__link-text">{{__('Location')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                                        <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                            <ul class="k-menu__subnav">
                                                <li class="k-menu__item {{seeMenu('system.area.index')}}" aria-haspopup="true">
                                                    <a href="{{route('system.area.index')}}" class="k-menu__link ">
                                                        <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                                    </a>
                                                </li>
                                                {{--<li class="k-menu__item {{seeMenu('system.area.create,system.area.edit')}}" aria-haspopup="true">--}}
                                                    {{--<a href="{{route('system.area.create')}}" class="k-menu__link ">--}}
                                                        {{--<i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>--}}
                                                    {{--</a>--}}
                                                {{--</li>--}}
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.bank.index,system.bank.create,system.bank.edit,system.bank.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon-piggy-bank"></i><span class="k-menu__link-text">{{__('Banks')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.bank.index,system.bank.show')}}" aria-haspopup="true">
                                        <a href="{{route('system.bank.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                        </a>
                                    </li>

                                    <li class="k-menu__item {{seeMenu('system.bank.create,system.bank.edit')}}" aria-haspopup="true">
                                        <a href="{{route('system.bank.create')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.bank-branch.index,system.bank-branch.create,system.bank-branch.edit,system.bank-branch.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon-network"></i><span class="k-menu__link-text">{{__('Banks Branches')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.bank-branch.index,system.bank-branch.show')}}" aria-haspopup="true">
                                        <a href="{{route('system.bank-branch.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                        </a>
                                    </li>

                                    <li class="k-menu__item {{seeMenu('system.bank-branch.create,system.bank-branch.edit')}}" aria-haspopup="true">
                                        <a href="{{route('system.bank-branch.create')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.report.credit,system.report.total-dues',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon-search-1"></i><span class="k-menu__link-text">{{__('Reports')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.report.credit')}}" aria-haspopup="true">
                                        <a href="{{route('system.report.credit')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Credits')}}</span>
                                        </a>
                                    </li>

                                    <li class="k-menu__item {{seeMenu('system.report.total-dues')}}" aria-haspopup="true">
                                        <a href="{{route('system.report.total-dues')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Dues Total')}}</span>
                                        </a>
                                    </li>

                                    <li class="k-menu__item {{seeMenu('system.report.match')}}" aria-haspopup="true">
                                        <a href="{{route('system.report.match')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('تقرير تطابق الارصدة')}}</span>
                                        </a>
                                    </li>


                                </ul>
                            </div>
                        </li>


                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.staff.index,system.staff.create,system.staff.edit,system.staff.show',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon-user-settings"></i><span class="k-menu__link-text">{{__('Staff')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.staff.index,system.staff.show')}}" aria-haspopup="true">
                                        <a href="{{route('system.staff.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                        </a>
                                    </li>
                                    <li class="k-menu__item {{seeMenu('system.staff.create')}}" aria-haspopup="true">
                                        <a href="{{route('system.staff.create')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="k-menu__item  k-menu__item--submenu {{seeMenu('system.permission-group.index,system.permission-group.create,system.permission-group.edit',true)}}" aria-haspopup="true" data-kmenu-submenu-toggle="hover"><a href="javascript:;" class="k-menu__link k-menu__toggle"><i class="k-menu__link-icon flaticon-visible"></i><span class="k-menu__link-text">{{__('Permission Group')}}</span><i class="k-menu__ver-arrow la la-angle-right"></i></a>
                            <div class="k-menu__submenu "><span class="k-menu__arrow"></span>
                                <ul class="k-menu__subnav">
                                    <li class="k-menu__item {{seeMenu('system.permission-group.index')}}" aria-haspopup="true">
                                        <a href="{{route('system.permission-group.index')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('View')}}</span>
                                        </a>
                                    </li>
                                    <li class="k-menu__item {{seeMenu('system.permission-group.create')}}" aria-haspopup="true">
                                        <a href="{{route('system.permission-group.create')}}" class="k-menu__link ">
                                            <i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">{{__('Create')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="k-menu__item {{seeMenu('system.setting.index')}}" aria-haspopup="true"><a href="{{route('system.setting.index')}}" class="k-menu__link "><i class="k-menu__link-icon flaticon2-settings"></i><span class="k-menu__link-text">{{__('Setting')}}</span></a></li>
                        {{--<li class="k-menu__item {{seeMenu('system.activity-log.index,system.activity-log.show')}}" aria-haspopup="true"><a href="{{route('system.activity-log.index')}}" class="k-menu__link "><i class="k-menu__link-icon flaticon2-cardiogram"></i><span class="k-menu__link-text">{{__('Activity Log')}}</span></a></li>--}}
                        {{--<li class="k-menu__item {{seeMenu('system.setting.index')}}" aria-haspopup="true">--}}
                            {{--<a href="{{route('system.staff.auth-sessions')}}" class="k-menu__link ">--}}
                                {{--<i class="k-menu__link-icon flaticon-user-settings"></i><span class="k-menu__link-text">{{__('Auth Sessions')}}</span>--}}
                            {{--</a>--}}
                        {{--</li>--}}








                    </ul>
                </div>
            </div>

            <!-- end:: Aside Menu -->

            <!-- begin:: Aside -->
            <div class="k-aside__footer		k-grid__item" id="k_aside_footer">
                <div class="k-aside__footer-nav">
                    <div class="k-aside__footer-item">
                        <a href="{{route('system.setting.index')}}" class="btn btn-icon"><i class="flaticon2-gear"></i></a>
                    </div>
                    <div class="k-aside__footer-item">
                        <a href="{{route('system.property.create')}}" class="btn btn-icon"><i class="flaticon-buildings"></i></a>
                    </div>
                    <div class="k-aside__footer-item">
                        <a href="{{route('system.request.create')}}" class="btn btn-icon"><i class="flaticon-search-1"></i></a>
                    </div>
                    <div class="k-aside__footer-item">
                        <a href="{{route('system.calendar.index')}}" class="btn btn-icon"><i class="flaticon2-calendar-2"></i></a>
                    </div>
                </div>
            </div>

            <!-- end:: Aside -->
        </div>

        <!-- end:: Aside -->
        <div class="k-grid__item k-grid__item--fluid k-grid k-grid--hor k-wrapper" id="k_wrapper">

            <!-- begin:: Header -->
            <div id="k_header" class="k-header k-grid__item  k-header--fixed ">

                <!-- begin: Header Menu -->
                <button class="k-header-menu-wrapper-close" id="k_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
                <div class="k-header-menu-wrapper" id="k_header_menu_wrapper">
                    <div id="k_header_menu" class="k-header-menu k-header-menu-mobile ">
                        <ul class="k-menu__nav ">
                            <li class="k-menu__item  k-menu__item--submenu k-menu__item--rel">
                                <a href="javascript:;" onclick="Swal.fire('Osouly System','Developed By Eg.Click Team','info')" class="k-menu__link k-menu__toggle">
                                    <b>{{setting('company_name')}}</b>
                                </a>

                            </li>
                        </ul>
                    </div>
                </div>
                <!-- end: Header Menu -->

                <!-- begin:: Header Topbar -->
                <div class="k-header__topbar">



                    <!--begin: Notifications -->
                    {{--<div class="k-header__topbar-item dropdown">--}}
                        {{--<div class="k-header__topbar-wrapper" onclick="readNotification();" data-toggle="dropdown" data-offset="30px -2px">--}}
                            {{--<span class="k-header__topbar-icon"><i class="flaticon2-bell-alarm-symbol"></i></span>--}}
                            {{--@php--}}
                                {{--$notificationCount = Auth::user()->unreadNotifications()->count();--}}
                                {{--$notifications = Auth::user()->notifications()->limit(10)->get();--}}
                            {{--@endphp--}}
                            {{--@if($notificationCount)--}}
                                {{--<div id="count-notification-layout" style="font-size: 10px;--}}
                                    {{--min-width: 10px;--}}
    {{--color: #fff;--}}
    {{--white-space: nowrap;--}}
    {{--vertical-align: baseline;--}}
    {{--background-color: #C45C5C;--}}

    {{--font-weight: normal;--}}
    {{--padding: 0;--}}
    {{--position: absolute;--}}
    {{--left: 9px;--}}
    {{--text-align: center;--}}
    {{--top: 21px;--}}
    {{--font-family: 'Pontano Sans', sans-serif;--}}
    {{--height: 15px;--}}
    {{--width: 15px;--}}
    {{--line-height: 15px;--}}
    {{--display: block;--}}
    {{--border-radius: 50%;--}}
    {{---moz-border-radius: 50%;--}}
    {{---webkit-border-radius: 50%;">--}}
                                {{--{{$notificationCount}}</div>--}}
{{--                                <span class="k-badge k-badge--dot k-badge--notify k-badge--sm k-badge--brand"></span>--}}
                            {{--@endif--}}
                        {{--</div>--}}
                        {{--<div class="dropdown-menu dropdown-menu-fit @if(App::getLocale() == 'en') dropdown-menu-right @else dropdown-menu-left @endif dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">--}}
                            {{--<div class="k-head" style="background-image: url({{asset('assets/media/misc/head_bg_sm.jpg')}})">--}}
                                {{--<h3 class="k-head__title">{{__('Notifications')}}</h3>--}}
                                {{--<div class="k-head__sub"><span class="k-head__desc">{{__(':count new notifications',['count'=> $notificationCount])}}</span></div>--}}
                            {{--</div>--}}
                            {{--<div style="overflow-y: auto !important;" class="k-notification " data-scroll="true" data-height="270" data-mobile-height="220">--}}
                                {{--@foreach($notifications as $key => $value)--}}
                                    {{--<a href="{!! iif(isset($value->data['url']) && !empty($value->data['url']) ,route('system.notifications.url',$value->id),'javascript:void(0);') !!}" class="k-notification__item {!! iif($value->read_at,'k-notification__item--read') !!}">--}}
                                        {{--<div class="k-notification__item-icon">--}}
                                            {{--<i class="flaticon2-line-chart k-font-success"></i>--}}
                                        {{--</div>--}}
                                        {{--<div class="k-notification__item-details">--}}
                                            {{--<div class="k-notification__item-title">--}}
                                                {{--{{$value->data['description']}}--}}
                                            {{--</div>--}}
                                            {{--<div class="k-notification__item-time">--}}
                                                {{--{{$value->created_at->diffForHumans()}}--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</a>--}}
                                {{--@endforeach--}}

                            {{--</div>--}}
                            {{--<div class="k-footer">--}}
                                {{--<a href="javascript:void(0);" onclick="urlIframe('{{route('system.notifications.index')}}')">--}}
                                    {{--<h6>{{__('View All')}}</h6>--}}
                                {{--</a>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    <!--end: Notifications -->


                    <!--begin: Language bar -->
                    <div class="k-header__topbar-item k-header__topbar-item--langs">
                        <div class="k-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px -2px">
									<span class="k-header__topbar-icon">
										<i class="fa fa-language"></i>
									</span>
                        </div>
                        <div class="dropdown-menu dropdown-menu-fit @if(App::getLocale() == 'en') dropdown-menu-right @else dropdown-menu-left @endif dropdown-menu-anim dropdown-menu-top-unround">
                            <ul class="k-nav k-margin-t-10 k-margin-b-10">
                                <li class="k-nav__item">
                                    <a href="{{route('system.dashboard',['language'=>'en','backByLanguage'=>'true'])}}" class="k-nav__link">
                                        <span class="k-nav__link-icon"></span>
                                        <span class="k-nav__link-text">English</span>
                                    </a>
                                </li>
                                <li class="k-nav__item">
                                    <a href="{{route('system.dashboard',['language'=>'ar','backByLanguage'=>'true'])}}" class="k-nav__link">
                                        <span class="k-nav__link-icon"></span>
                                        <span class="k-nav__link-text">عربي</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!--end: Language bar -->

                    <!--begin: User bar -->
                    <div class="k-header__topbar-item k-header__topbar-item--user">
                        <div class="k-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px -2px">
                            <div class="k-header__topbar-user">
                                <span class="k-header__topbar-welcome k-hidden-mobile">Hi,</span>
                                <span class="k-header__topbar-username k-hidden-mobile">{{Auth::user()->fullname}}</span>
                                <img alt="Pic" src="{{asset('assets/media/users/300_25.jpg')}}" />

                                <!--use below badge element instead the user avatar to display username's first letter(remove k-hidden class to display it) -->
                                <span class="k-badge k-badge--username k-badge--lg k-badge--brand k-hidden">A</span>
                            </div>
                        </div>
                        <div class="dropdown-menu dropdown-menu-fit @if(App::getLocale() == 'en') dropdown-menu-right @else dropdown-menu-left @endif dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-md">
                            <div class="k-user-card k-margin-b-50 k-margin-b-30-tablet-and-mobile" style="background-image: url({{asset('assets/media/misc/head_bg_sm.jpg')}})">
                                <div class="k-user-card__wrapper">
                                    <div class="k-user-card__details">
                                        <div class="k-user-card__name">{{Auth::user()->fullname}}</div>
                                        <div class="k-user-card__position">{{Auth::user()->job_title}}</div>
                                    </div>
                                </div>
                            </div>
                            <ul class="k-nav k-margin-b-10">
                                <li class="k-nav__item">
                                    <a href="{{route('system.staff.change-password')}}" class="k-nav__link">
                                        <span class="k-nav__link-icon"><i class="flaticon2-calendar-3"></i></span>
                                        <span class="k-nav__link-text">{{__('Change Password')}}</span>
                                    </a>
                                </li>

                                <li class="k-nav__item k-nav__item--custom k-margin-t-15">
                                    <a href="{{url('system/logout')}}" class="btn btn-outline-metal btn-hover-brand btn-upper btn-font-dark btn-sm btn-bold">{{__('Sign Out')}}</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!--end: User bar -->

                </div>

                <!-- end:: Header Topbar -->
            </div>

            <!-- end:: Header -->
@yield('content')


        <!-- begin:: Footer -->
            <div class="k-footer	k-grid__item k-grid k-grid--desktop k-grid--ver-desktop">
                <div class="k-footer__copyright">
                    {{date('Y')}}&nbsp;&copy;&nbsp;<a href="https://www.osouly.org" target="_blank" class="k-link">Osouly</a>
                </div>
{{--                <div class="k-footer__menu">--}}
{{--                    <a href="http://keenthemes.com/keen" target="_blank" class="k-footer__menu-link k-link">About</a>--}}
{{--                    <a href="http://keenthemes.com/keen" target="_blank" class="k-footer__menu-link k-link">Team</a>--}}
{{--                    <a href="http://keenthemes.com/keen" target="_blank" class="k-footer__menu-link k-link">Contact</a>--}}
{{--                </div>--}}
            </div>

            <!-- end:: Footer -->
        </div>
    </div>
</div>

<!-- end:: Page -->

<!-- begin::Scrolltop -->
<div id="k_scrolltop" class="k-scrolltop">
    <i class="la la-arrow-up"></i>
</div>

<!-- end::Scrolltop -->


<!-- begin::Global Config -->
<script>
    var KAppOptions = {
        "colors": {
            "state": {
                "brand": "#5d78ff",
                "metal": "#c4c5d6",
                "light": "#ffffff",
                "accent": "#00c5dc",
                "primary": "#5867dd",
                "success": "#34bfa3",
                "info": "#36a3f7",
                "warning": "#ffb822",
                "danger": "#fd3995",
                "focus": "#9816f4"
            },
            "base": {
                "label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
                "shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
            }
        }
    };
</script>

<!-- end::Global Config -->

<!--begin:: Global Mandatory Vendors -->
<script src="{{asset('assets/vendors/general/jquery/dist/jquery.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/popper.js/dist/umd/popper.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/bootstrap/dist/js/bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/js-cookie/src/js.cookie.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/moment/min/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/tooltip.js/dist/umd/tooltip.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/perfect-scrollbar/dist/perfect-scrollbar.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/sticky-js/dist/sticky.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/wnumb/wNumb.js')}}" type="text/javascript"></script>

<!--end:: Global Mandatory Vendors -->

<!--begin:: Global Optional Vendors -->
<script src="{{asset('assets/vendors/general/jquery-form/dist/jquery.form.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/block-ui/jquery.blockUI.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/custom/theme/framework/vendors/bootstrap-datepicker/init.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/bootstrap-datetime-picker/js/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/bootstrap-timepicker/js/bootstrap-timepicker.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/custom/theme/framework/vendors/bootstrap-timepicker/init.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/bootstrap-maxlength/src/bootstrap-maxlength.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/custom/vendors/bootstrap-multiselectsplitter/bootstrap-multiselectsplitter.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/bootstrap-select/dist/js/bootstrap-select.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/typeahead.js/dist/typeahead.bundle.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/handlebars/dist/handlebars.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/inputmask/dist/jquery.inputmask.bundle.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/inputmask/dist/inputmask/inputmask.date.extensions.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/inputmask/dist/inputmask/inputmask.numeric.extensions.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/inputmask/dist/inputmask/inputmask.phone.extensions.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/nouislider/distribute/nouislider.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/owl.carousel/dist/owl.carousel.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/autosize/dist/autosize.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/clipboard/dist/clipboard.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/dropzone/dist/dropzone.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/summernote/dist/summernote.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/markdown/lib/markdown.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/bootstrap-markdown/js/bootstrap-markdown.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/custom/theme/framework/vendors/bootstrap-markdown/init.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/jquery-validation/dist/jquery.validate.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/jquery-validation/dist/additional-methods.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/custom/theme/framework/vendors/jquery-validation/init.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/toastr/build/toastr.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/raphael/raphael.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/morris.js/morris.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/chart.js/dist/Chart.bundle.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/custom/vendors/bootstrap-session-timeout/dist/bootstrap-session-timeout.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/custom/vendors/jquery-idletimer/idle-timer.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/waypoints/lib/jquery.waypoints.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/counterup/jquery.counterup.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/es6-promise-polyfill/promise.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/sweetalert2/dist/sweetalert2.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/custom/theme/framework/vendors/sweetalert2/init.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/jquery.repeater/src/lib.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/jquery.repeater/src/jquery.input.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/jquery.repeater/src/repeater.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/vendors/general/dompurify/dist/purify.js')}}" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" type="text/javascript"></script>
<!--end:: Global Optional Vendors -->

<!--begin::Global Theme Bundle -->
<script src="{{asset('assets/demo/default/base/scripts.bundle.js')}}" type="text/javascript"></script>

<!--end::Global Theme Bundle -->

<!--begin::Page Vendors -->
<script src="{{asset('assets/vendors/custom/datatables/datatables.bundle.js')}}" type="text/javascript"></script>

<!--end::Page Vendors -->

<!--begin::Page Scripts -->
<script src="{{asset('assets/demo/default/custom/components/datatables/data-sources/ajax-server-side.js')}}" type="text/javascript"></script>

<!--end::Page Scripts -->

<!--begin::Global App Bundle -->
<script src="{{asset('assets/app/scripts/bundle/app.bundle.js')}}" type="text/javascript"></script>

<script src="{{asset('js/node.js')}}" type="text/javascript"></script>


{{-- start abdo edit --}}



    <script>
        $(document).ready(function() {

            $('.text_editor').summernote({
                height:300,
            });

            @if(App::getLocale() == 'ar')
            $(".k_datetimepicker_1 ,.k_datetimepicker_2").click(function(){
                var dateLeft = $(".datetimepicker").css("left");
                $(".datetimepicker").css('left',0);
                $(".datetimepicker").css('right',dateLeft);

            });
            @endif
        });

    </script>


{{--end of abdo edit--}}

<script>
    $(document).on('click','.more_info',function(e) {
        var $title = $(this).find(".title");
        if (!$title.length) {
            $(this).append('<span class="title">' + $(this).attr("title") + '</span>');
        } else {
            $title.remove();
        }
    });
</script>

@yield('footer')






<!--end::Global App Bundle -->
</body>

<!-- end::Body -->
</html>
