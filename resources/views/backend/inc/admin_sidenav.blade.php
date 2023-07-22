<div class="aiz-sidebar-wrap">
    <div class="aiz-sidebar left c-scrollbar">
        <div class="aiz-side-nav-logo-wrap">
            <a href="{{ route('admin.dashboard') }}" class="d-block text-left">
                @if (get_setting('system_logo_white') != null)
                    <img class="mw-100" src="{{ uploaded_asset(get_setting('system_logo_white')) }}"
                        class="brand-icon" alt="{{ get_setting('site_name') }}">
                @else
                    <img class="mw-100" src="{{ static_asset('assets/img/logo-white.png') }}"
                        class="brand-icon" alt="{{ get_setting('site_name') }}">
                @endif
            </a>
        </div>
        <div class="aiz-side-nav-wrap">
            <ul class="aiz-side-nav-list" data-toggle="aiz-side-menu">
                

                <!-- Quotation -->
                @can('quotation_create')
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <g id="Group_23" data-name="Group 23" transform="translate(-126 -590)">
                                <path id="Subtraction_31" data-name="Subtraction 31"
                                    d="M15,16H1a1,1,0,0,1-1-1V1A1,1,0,0,1,1,0H4.8V4.4a2,2,0,0,0,2,2H9.2a2,2,0,0,0,2-2V0H15a1,1,0,0,1,1,1V15A1,1,0,0,1,15,16Z"
                                    transform="translate(126 590)" fill="#707070" />
                                <path id="Rectangle_93" data-name="Rectangle 93"
                                    d="M0,0H4A0,0,0,0,1,4,0V4A1,1,0,0,1,3,5H1A1,1,0,0,1,0,4V0A0,0,0,0,1,0,0Z"
                                    transform="translate(132 590)" fill="#707070" />
                            </g>
                        </svg>
                        <span class="aiz-side-nav-text">{{ translate('Quotation') }}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <!--Submenu-->

                    <ul class="aiz-side-nav-list level-2">
                        @can('quotation_create')
                            <li class="aiz-side-nav-item">
                                <a href="{{route('quotation.home')}}"
                                    class="aiz-side-nav-link {{ areActiveRoutes(['quotation.home']) }}">
                                    <span class="aiz-side-nav-text">
                                        {{ translate('Quotation Create') }}
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('quotation_list')
                            <li class="aiz-side-nav-item">
                                <a href="{{route('quotation.list')}}"
                                    class="aiz-side-nav-link {{ areActiveRoutes(['quotation.home']) }}">
                                    <span class="aiz-side-nav-text">
                                        {{ addon_is_activated('multi_vendor') ? translate('Quotation List') : translate('Quotation List') }}
                                    </span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
                @endcan



            </ul><!-- .aiz-side-nav -->
        </div><!-- .aiz-side-nav-wrap -->
    </div><!-- .aiz-sidebar -->
    <div class="aiz-sidebar-overlay"></div>
</div><!-- .aiz-sidebar -->
