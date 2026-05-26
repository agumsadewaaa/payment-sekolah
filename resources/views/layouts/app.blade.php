<!DOCTYPE html>
<html lang="en">

<head>
	<!--Title-->
	<title>SMK YPE SAMPANG</title>

	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="author" content="DexignZone">
	<meta name="robots" content="index, follow">

	<meta name="keywords" content="Mophy, Payment Admin Dashboard, Bootstrap Template, FrontEnd, Web Application, Payment Management, Responsive Design, User Experience, Customizable, Modern UI, Dashboard Template, Admin Panel, Bootstrap 4, HTML5, CSS3, JavaScript, Finance, Payment Gateway, Admin Template, UI Kit, SASS, SCSS, CRM, Analytics, Responsive Dashboard, responsive admin dashboard, sales dashboard, ui kit, web app, Admin Dashboard, Template, Admin, CMS pages, Authentication, FrontEnd Integration, Web Application UI, Bootstrap Framework, User Interface Kit, Financial Dashboard, SASS Integration, Customizable Template, Payment Gateway, HTML5/CSS3, CRM Dashboard, Analytics Dashboard, Admin Dashboard UI, Mobile-Friendly Design, UI Components, Dashboard Widgets, Dashboard Framework, Data Visualization, User Experience (UX), Dashboard Widgets, Real-time Analytics, Cross-Browser Compatibility, Interactive Charts, Payment Processing, Performance Optimization, Multi-Purpose Template, Efficient Admin Tools, Task Management, Modern Web Technologies, Payment Tracking, Responsive Tables, Dashboard Widgets, Invoice Management, Access Control, Modular Design, Payment History, Trend Analysis, User-Friendly Interface">

	<meta name="description" content="Explore the power of Mophy – a sleek and feature-rich Payment Admin Dashboard Bootstrap Template with a seamlessly integrated FrontEnd. Effortlessly manage your payment processes and elevate user experiences with this modern, responsive, and customizable solution. Unlock a world of possibilities in payment administration and frontend design with Mophy – your key to streamlined and visually stunning web applications.">

	<meta property="og:title" content="Mophy - Payment Admin Dashboard Bootstrap Template + FrontEnd | DexignZone">
	<meta property="og:description" content="Explore the power of Mophy – a sleek and feature-rich Payment Admin Dashboard Bootstrap Template with a seamlessly integrated FrontEnd. Effortlessly manage your payment processes and elevate user experiences with this modern, responsive, and customizable solution. Unlock a world of possibilities in payment administration and frontend design with Mophy – your key to streamlined and visually stunning web applications.">
	<meta property="og:image" content="https://mophy.dexignzone.com/xhtml/social-image.png">

	<meta name="format-detection" content="telephone=no">

	<meta name="twitter:title" content="Mophy - Payment Admin Dashboard Bootstrap Template + FrontEnd | DexignZone">
	<meta name="twitter:description" content="Explore the power of Mophy – a sleek and feature-rich Payment Admin Dashboard Bootstrap Template with a seamlessly integrated FrontEnd. Effortlessly manage your payment processes and elevate user experiences with this modern, responsive, and customizable solution. Unlock a world of possibilities in payment administration and frontend design with Mophy – your key to streamlined and visually stunning web applications.">
	<meta name="twitter:image" content="https://mophy.dexignzone.com/xhtml/social-image.png">
	<meta name="twitter:card" content="summary_large_image">

	<!-- MOBILE SPECIFIC -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Favicon icon -->
	<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon.png') }}">

	<!-- Daterange picker -->
	<link href="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
	<!-- Clockpicker -->
	<link href="{{ asset('vendor/clockpicker/css/bootstrap-clockpicker.min.css') }}" rel="stylesheet">
	<!-- asColorpicker -->
	 <link href="{{ asset('vendor/jquery-ascolorpicker/css/ascolorpicker.min.css') }}" rel="stylesheet">
	<!-- Material color picker -->
	<link href="{{ asset('vendor/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}" rel="stylesheet">
	<!-- Pick date -->
	<link rel="stylesheet" href="{{ asset('vendor/pickadate/themes/default.css') }}">
	<link rel="stylesheet" href="{{ asset('vendor/pickadate/themes/default.date.css') }}">

	<!-- Datatable -->
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <!-- Custom Stylesheet -->
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables/responsive/responsive.css') }}" rel="stylesheet">

	<link href="{{ asset('vendor/jqvmap/css/jqvmap.min.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('vendor/chartist/css/chartist.min.css') }}">
	<!-- Vectormap -->
	<link href="{{ asset('vendor/jqvmap/css/jqvmap.min.css') }}" rel="stylesheet">
	<link href="{{ asset('vendor/owl-carousel/owl.carousel.css') }}" rel="stylesheet">
	<link href="{{ asset('css/style.css') }}" rel="stylesheet">

	<style>
	    .brand-text {
            color: black;
            font-size: 15px;
            font-weight: 600;
        }
        
        /* Buat supaya hilang ketika collapse */
        #main-wrapper.menu-toggle .brand-text{
            display: none;
        }
	</style>
</head>

<body>

	<!--*******************
        Preloader start
    ********************-->
	<div id="preloader">
		<div class="sk-three-bounce">
			<div class="sk-child sk-bounce1"></div>
			<div class="sk-child sk-bounce2"></div>
			<div class="sk-child sk-bounce3"></div>
		</div>
	</div>
	<!--*******************
        Preloader end
    ********************-->

    <div id="main-wrapper">
        <!-- Main Header -->
        <div class="nav-header">
			<a href="#" class="brand-logo">
				<img class="logo-abbr" src="{{ asset('logo.jpg') }}" alt="">
				<center><span class="brand-text">&nbsp;&nbsp;SMK YPE Sampang</span></center>
			</a>

			<div class="nav-control">
				<div class="hamburger">
					<span class="line"></span><span class="line"></span><span class="line"></span>
				</div>
			</div>
		</div>

        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="dashboard_bar">
								<div class="input-group search-area d-lg-inline-flex d-none">
									<div class="input-group-append">
										<button class="input-group-text search_icon search_icon"><i class="flaticon-381-search-2"></i></button>
									</div>
									<input type="text" class="form-control" placeholder="Search here...">
								</div>
                            </div>
                        </div>
                        <ul class="navbar-nav header-right">
							<!-- <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link  ai-icon" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
                                    <svg width="20" height="20" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path fill-rule="evenodd" clip-rule="evenodd" d="M12.6001 4.3008V1.4C12.6001 0.627199 13.2273 0 14.0001 0C14.7715 0 15.4001 0.627199 15.4001 1.4V4.3008C17.4805 4.6004 19.4251 5.56639 20.9287 7.06999C22.7669 8.90819 23.8001 11.4016 23.8001 14V19.2696L24.9327 21.5348C25.4745 22.6198 25.4171 23.9078 24.7787 24.9396C24.1417 25.9714 23.0147 26.6 21.8023 26.6H15.4001C15.4001 27.3728 14.7715 28 14.0001 28C13.2273 28 12.6001 27.3728 12.6001 26.6H6.19791C4.98411 26.6 3.85714 25.9714 3.22014 24.9396C2.58174 23.9078 2.52433 22.6198 3.06753 21.5348L4.20011 19.2696V14C4.20011 11.4016 5.23194 8.90819 7.07013 7.06999C8.57513 5.56639 10.5183 4.6004 12.6001 4.3008ZM14.0001 6.99998C12.1423 6.99998 10.3629 7.73779 9.04973 9.05099C7.73653 10.3628 7.00011 12.1436 7.00011 14V19.6C7.00011 19.817 6.94833 20.0312 6.85173 20.2258C6.85173 20.2258 6.22871 21.4718 5.57072 22.7864C5.46292 23.0034 5.47412 23.2624 5.60152 23.4682C5.72892 23.674 5.95431 23.8 6.19791 23.8H21.8023C22.0445 23.8 22.2699 23.674 22.3973 23.4682C22.5247 23.2624 22.5359 23.0034 22.4281 22.7864C21.7701 21.4718 21.1471 20.2258 21.1471 20.2258C21.0505 20.0312 21.0001 19.817 21.0001 19.6V14C21.0001 12.1436 20.2623 10.3628 18.9491 9.05099C17.6359 7.73779 15.8565 6.99998 14.0001 6.99998Z" fill="#3E4954"/>
									</svg>
									<span class="badge light text-white bg-primary rounded-circle">12</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <div id="DZ_W_Notification1" class="widget-media dz-scroll p-3 height380">
										<ul class="timeline">
											<li>
												<div class="timeline-panel">
													<div class="media me-2">
														<img src="{{ asset('images/avatar/1.jpg') }}" alt="image" width="50">
													</div>
													<div class="media-body">
														<h6 class="mb-1">Dr sultads Send you Photo</h6>
														<small class="d-block">29 July 2024 - 02:26 PM</small>
													</div>
												</div>
											</li>
											<li>
												<div class="timeline-panel">
													<div class="media me-2 media-info">
														KG
													</div>
													<div class="media-body">
														<h6 class="mb-1">Resport created successfully</h6>
														<small class="d-block">29 July 2024 - 02:26 PM</small>
													</div>
												</div>
											</li>
											<li>
												<div class="timeline-panel">
													<div class="media me-2 media-success">
														<i class="fa fa-home"></i>
													</div>
													<div class="media-body">
														<h6 class="mb-1">Reminder : Treatment Time!</h6>
														<small class="d-block">29 July 2024 - 02:26 PM</small>
													</div>
												</div>
											</li>
											 <li>
												<div class="timeline-panel">
													<div class="media me-2">
														<img src="{{ asset('images/avatar/1.jpg') }}" alt="image" width="50">
													</div>
													<div class="media-body">
														<h6 class="mb-1">Dr agum Send you Photo</h6>
														<small class="d-block">29 July 2024 - 02:26 PM</small>
													</div>
												</div>
											</li>
											<li>
												<div class="timeline-panel">
													<div class="media me-2 media-danger">
														KG
													</div>
													<div class="media-body">
														<h6 class="mb-1">Resport created successfully</h6>
														<small class="d-block">29 July 2024 - 02:26 PM</small>
													</div>
												</div>
											</li>
											<li>
												<div class="timeline-panel">
													<div class="media me-2 media-primary">
														<i class="fa fa-home"></i>
													</div>
													<div class="media-body">
														<h6 class="mb-1">Reminder : Treatment Time!</h6>
														<small class="d-block">29 July 2024 - 02:26 PM</small>
													</div>
												</div>
											</li>
										</ul>
									</div>
                                    <a class="all-notification" href="javascript:void(0)">See all notifications <i class="ti-arrow-right"></i></a>
                                </div>
                            </li>
							<li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link bell bell-link" href="javascript:void(0)">
                                    <svg width="20" height="20" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path fill-rule="evenodd" clip-rule="evenodd" d="M25.6666 8.16666C25.6666 5.5895 23.5771 3.5 21 3.5C17.1161 3.5 10.8838 3.5 6.99998 3.5C4.42281 3.5 2.33331 5.5895 2.33331 8.16666V23.3333C2.33331 23.8058 2.61798 24.2305 3.05315 24.4113C3.48948 24.5922 3.99115 24.4918 4.32481 24.1582C4.32481 24.1582 6.59281 21.8902 7.96714 20.517C8.40464 20.0795 8.99733 19.8333 9.61683 19.8333H21C23.5771 19.8333 25.6666 17.7438 25.6666 15.1667V8.16666ZM23.3333 8.16666C23.3333 6.87866 22.2891 5.83333 21 5.83333C17.1161 5.83333 10.8838 5.83333 6.99998 5.83333C5.71198 5.83333 4.66665 6.87866 4.66665 8.16666V20.517L6.31631 18.8673C7.19132 17.9923 8.37899 17.5 9.61683 17.5H21C22.2891 17.5 23.3333 16.4558 23.3333 15.1667V8.16666ZM8.16665 15.1667H17.5C18.144 15.1667 18.6666 14.644 18.6666 14C18.6666 13.356 18.144 12.8333 17.5 12.8333H8.16665C7.52265 12.8333 6.99998 13.356 6.99998 14C6.99998 14.644 7.52265 15.1667 8.16665 15.1667ZM8.16665 10.5H19.8333C20.4773 10.5 21 9.97733 21 9.33333C21 8.68933 20.4773 8.16666 19.8333 8.16666H8.16665C7.52265 8.16666 6.99998 8.68933 6.99998 9.33333C6.99998 9.97733 7.52265 10.5 8.16665 10.5Z" fill="#3E4954"/>
									</svg>
									<span class="badge light text-white bg-primary rounded-circle">5</span>
                                </a>
							</li> -->
                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
									<div class="header-info">
										<span class="text-black">Hello, <strong>{{ Auth::user()->name }}</strong></span>
										<p class="fs-12 mb-0">{{ Auth::user()->roles->pluck('name')->first() ?? 'User' }}</p>
									</div>
                                    <img src="{{ asset('profil.png') }}" width="20" alt="">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="{{ route('profile.edit') }}" class="dropdown-item ai-icon">
                                        <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        <span class="ms-2">Profile & Settings</span>
                                    </a>
                                    <a href="#" class="dropdown-item ai-icon" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                        <span class="ms-2">Logout </span>
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Left side column. contains the logo and sidebar -->
        @include('layouts.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-body default-height">
            @yield('content')
        </div>

        <!-- Main Footer -->
        <div class="footer">
            <div class="copyright">
                <p>Copyright © Designed &amp; Developed by <a href="http://dexignzone.com/" target="_blank">DexignZone</a> <span class="current-year">2025</span></p>
            </div>
        </div>
    </div>

	<!--**********************************
        Scripts
    ***********************************-->
	<!-- Required vendors -->
	<script src="{{ asset('vendor/global/global.min.js') }}"></script>
	<script src="{{ asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
	<script src="{{ asset('vendor/chart-js/chart.bundle.min.js') }}"></script>
	<script src="{{ asset('vendor/owl-carousel/owl.carousel.js') }}"></script>

	<!-- Chart piety plugin files -->
	<script src="{{ asset('vendor/peity/jquery.peity.min.js') }}"></script>

	<!-- Apex Chart -->
	<script src="{{ asset('vendor/apexchart/apexchart.js') }}"></script>

	<!-- Datatable -->
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/responsive/responsive.js') }}"></script>
    <script src="{{ asset('js/plugins-init/datatables.init.js') }}"></script>

	<!-- Dashboard 1 -->
	<script src="{{ asset('js/dashboard/dashboard-1.js') }}"></script>

	<script src="{{ asset('js/custom.min.js') }}"></script>
	<script src="{{ asset('js/deznav-init.js') }}"></script>
	
	<!-- Bootstrap Toast (Native) -->

	<!-- momment js is must -->
	<script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
	<!-- Daterangepicker -->
	<script src="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
	<!-- Material color picker -->
	<script src="{{ asset('vendor/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>
	<!-- pickdate -->
	<script src="{{ asset('vendor/pickadate/picker.js') }}"></script>
	<script src="{{ asset('vendor/pickadate/picker.time.js') }}"></script>
	<script src="{{ asset('vendor/pickadate/picker.date.js') }}"></script>
	
	

	<script>
		function carouselReview() {
			/*  testimonial one function by = owl.carousel.js */
			jQuery('.testimonial-one').owlCarousel({
				loop: true,
				margin: 10,
				autoplay: true,
				nav: false,
				center: true,
				rtl:true,
				dots: false,
				navText: ['<i class="fas fa-caret-left"></i>', '<i class="fas fa-caret-right"></i>'],
				responsive: {
					0: {
						items: 2
					},
					400: {
						items: 3
					},
					700: {
						items: 5
					},
					991: {
						items: 6
					},

					1200: {
						items: 4
					},
					1600: {
						items: 5
					}
				}
			})
		}

		jQuery(window).on('load', function () {
			setTimeout(function () {
				carouselReview();
				
			}, 1000);
		});
		

		jQuery(document).ready(function(){
			setTimeout(function(){
				dezSettingsOptions.version = 'light';
				new dezSettings(dezSettingsOptions);

				setCookie('version','light');

			},1500)
		});
	</script>

	<script>
		$(document).ready(function() {
			$('.selectpicker').selectpicker({
				liveSearch: true,
				liveSearchNormalize: true,
				liveSearchPlaceholder: 'Cari...',
				noneResultsText: 'Tidak ditemukan',
			});
		});
	</script>

	@stack('scripts')
	
	{{-- Bootstrap Alert akan ditampilkan di halaman --}}
</body>

</html>