<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Base Path -->
    <base href="<?php echo HTML_BASE_PATH; ?>" />
    <!-- Fsavicon -->
    <?php // include './app/views/partials/favicon.php'; 
    ?>
    <!-- Title -->
    <title>Web Owls | Software Design, Development, and Maintenance Services</title>

    <!-- Organization Meta Tags -->
    <meta name="organization" content="Web Owls">
    <meta name="author" content="Web Owls">
    <meta name="publisher" content="Web Owls">
    <meta name="contact" content="info@webowls.com">
    <meta name="copyright" content="Web Owls 2023">
    <meta name="description" content="Web Owls is a software company that provides a wide range of software design, development, and maintenance services to clients worldwide.">
    <!-- Developer Meta Tags -->
    <meta name="developer" content="Mohsin Ahmed">
    <meta name="contact" content="mohsinahmed@webowls.com">
    <meta name="description" content="Mohsin Ahmed is a software designer and developer at Web Owls with expertise in software designing, development, and programming.">
    <!-- Keywords -->
    <meta name="keywords" content="software company, software development, software maintenance services, custom application development, mobile app development, web development, e-commerce development, software integration, bug fixing, performance optimization, security enhancements">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="./app/assets/images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./app/assets/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./app/assets/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="./app/assets/images/favicon/site.webmanifest">
    <!-- Bundle -->
    <link rel="stylesheet" href="./app/dist/main/vendor/css/bundle.min.css">
    <!-- Plugin Css -->
    <link rel="stylesheet" href="./app/dist/main/vendor/css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="./app/dist/main/vendor/css/owl.carousel.min.css">
    <link rel="stylesheet" href="./app/dist/main/vendor/css/swiper.min.css">
    <!-- Fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="./app/dist/main/vendor/css/cubeportfolio.min.css">
    <!-- Revolution Slider CSS Files -->
    <link rel="stylesheet" href="./app/dist/main/consulting/css/navigation.css">
    <link rel="stylesheet" href="./app/dist/main/consulting/css/settings.css">
    <!-- Slick CSS Files -->
    <link rel="stylesheet" href="./app/dist/main/vendor/css/slick.css">
    <link rel="stylesheet" href="./app/dist/main/vendor/css/slick-theme.css">
    <!-- Select -->
    <link rel="stylesheet" href="./app/dist/main/vendor/css/select2.min.css">
    <!-- Style Sheet -->
    <link rel="stylesheet" href="./app/dist/main/consulting/css/style.css">
    <!-- Custom Style CSS File -->
    <link rel="stylesheet" href="./app/dist/main/consulting/css/custom.css">
</head>

<body data-spy="scroll" data-target=".navbar-nav" data-offset="90">

    <!-- Loader -->
    <div class="loader" id="loader-fade">
        <div class="loader-container">
            <ul class="loader-box">
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
            </ul>
        </div>
    </div>
    <!-- Loader ends -->

    <!-- Header start -->
    <?php require_once 'partials/header.php'; ?>
    <!-- Header end -->

    <!-- Carousel Section start -->
    <?php require_once 'partials/carousel.php'; ?>
    <!-- Carousel Section end -->

    <!-- About start -->
    <section id="aboutus" class="bg-light-gray3">
        <div class="container">
            <div class="row">
                <div class="container">
                    <div class="main-title style-two d-flex justify-content-md-around align-items-center flex-column flex-md-row text-center text-md-left wow fadeIn" data-wow-delay="300ms">
                        <div class="mb-4 mb-md-0">
                            <h5> About company </h5>
                            <h2>Web Owls is a software company, offers various software development and maintenance services.</h2>

                            <a href="javascript:void(0)" class="btn-setting btn-scale btn-blue text-white">learn more</a>
                        </div>
                        <div class="ml-md-4 pl-md-2">
                            <p class="mb-3">Web Owls is a software company that provides a wide range of software development and maintenance services to clients worldwide. The company is dedicated to creating innovative software solutions that cater to the diverse needs of businesses across different industries.</p>
                            <p class="mb-3">With a team of skilled developers, designers, and engineers, Web Owls is capable of delivering cutting-edge software products that are tailored to meet the unique requirements of each client. The company's software development services cover everything from custom application development, mobile app development, web development, e-commerce development, to software integration and maintenance.</p>
                            <p>Web Owls understands that technology is continuously evolving, and businesses need to keep up with the changes to remain competitive. The company, therefore, provides software maintenance services to ensure that clients' software products are up-to-date and functioning optimally at all times. The maintenance services cover bug fixing, software updates, performance optimization, security enhancements, and more.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 col-sm-12 mb-xs-2rem">
                    <div class="about-box center-block wow zoomIn" data-wow-delay="400ms">
                        <div class="about-opacity-icon"> <i class="fa-solid fa-code" aria-hidden="true"></i> </div>
                        <div class="about-main-icon pb-4">
                            <i class="fa-solid fa-code" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">Custom Development</h5>
                    </div>
                </div>
                <div class="col-md-3 col-sm-12 mb-xs-2rem">
                    <div class="about-box active center-block wow zoomIn" data-wow-delay="500ms">
                        <div class="about-opacity-icon"> <i class="fa-solid fa-shop" aria-hidden="true"></i> </div>
                        <div class="about-main-icon pb-4">
                            <i class="fa-solid fa-shop" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">E-Commerce </h5>
                    </div>
                </div>
                <div class="col-md-3 col-sm-12">
                    <div class="about-box center-block wow zoomIn" data-wow-delay="700ms">
                        <div class="about-opacity-icon"> <i class="fa-solid fa-desktop" aria-hidden="true"></i> </div>
                        <div class="about-main-icon pb-4">
                            <i class="fa-solid fa-desktop" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">CMS Development</h5>
                    </div>
                </div>
                <div class="col-md-3 col-sm-12 mb-xs-2rem">
                    <div class="about-box center-block wow zoomIn" data-wow-delay="600ms">
                        <div class="about-opacity-icon"> <i class="fa fa-chart-line" aria-hidden="true"></i> </div>
                        <div class="about-main-icon pb-4">
                            <i class="fa fa-chart-line" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">Maintenance Services</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- About ends -->

    <!-- Stats start -->
    <section id="skills" class="half-section p-0 bg-change bg-blue">
        <h2 class="d-none">heading</h2>
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-12 p-0 order-lg-2">
                    <div class="hover-effect">
                        <img alt="stats" src="./app/assets/main/consulting/img/split-stats.jpg" class="about-img">
                    </div>
                </div>

                <div class="col-lg-6 col-md-12 p-lg-0">
                    <div class="split-container-setting stats style-three">
                        <div class="main-title mb-5 text-lg-left wow fadeIn" data-wow-delay="300ms">
                            <h5 class="font-18"> Check Our Skills </h5>
                            <h2> We can <b>make</b> better <b>things</b> for you </h2>
                        </div>
                        <ul class="text-left">
                            <li class="custom-progress">
                                <h6 class="font-18 mb-0 text-capitalize">HTML & CSS <small>(Bootstrap/Materialize)</small> <span class="float-right"><b class="font-secondary font-weight-500 numscroller">85</b>%</span></h6>

                                <div class="progress">
                                    <div class="progress-bar bg-white" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </li>
                            <li class="custom-progress">
                                <h6 class="font-18 mb-0 text-capitalize">PHP & MySQL <small>(Laravel/CodeIgniter)</small><span class="float-right"><b class="font-secondary font-weight-500 numscroller">90</b>%</span></h6>

                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped bg-white" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </li>
                            <li class="custom-progress">
                                <h6 class="font-18 mb-0 text-capitalize">JavaScript (jQuery, AJAX, Node.js)<span class="float-right"><b class="font-secondary font-weight-500 numscroller">80</b>%</span></h6>

                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped bg-white" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </li>
                            <li class="custom-progress mb-0">
                                <h6 class="font-18 mb-0 text-capitalize">Server Administration/Management <span class="float-right"><b class="font-secondary font-weight-500 numscroller">85</b>%</span></h6>

                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped bg-white" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Stats ends -->

    <!-- Team start -->
    <?php require_once 'partials/team.php'; ?>
    <!-- Team ends -->

    <!-- Service start -->
    <section class="half-section p-0 bg-change bg-green">
        <h2 class="d-none">heading</h2>
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-12 p-lg-0 order-lg-2">
                    <div class="split-container-setting style-three text-center text-lg-left">
                        <div class="main-title mb-5 text-lg-left wow fadeIn" data-wow-delay="300ms">
                            <h5 class="font-18"> What you are looking for </h5>
                            <h2 class="mb-0"> We specialize in <br> <b>eCommerce</b> solutions </h2>
                        </div>
                        <p class="color-white mb-5">At Web Owls, we specialize in providing cutting-edge ecommerce solutions to businesses of all sizes. Our team of experts delivers custom ecommerce solutions tailored to meet our clients' specific needs, helping them to increase sales and grow their online presence.</p>

                        <a href="javascript:void(0)" class="btn-setting btn-transparent btn-hvr-blue color-white">learn more</a>

                    </div>
                </div>

                <div class="col-lg-6 col-md-12 p-0">
                    <div class="hover-effect">
                        <img alt="stats" src="./app/assets/main/consulting/img/split-service.jpg" class="about-img">
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- Service ends -->

    <!-- Projects/Cases start -->
    <?php // require_once 'partials/projects.php'; 
    ?>
    <!-- Projects/Cases ends -->

    <!-- Price start -->
    <section id="prices" class="bg-light-gray price-style2">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="main-title wow fadeIn" data-wow-delay="300ms">
                        <h5> Effective and economical packages </h5>
                        <h2> choose <b>best price</b> plan</h2>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sodales lobortis vehicula. Aliquam sodales turpis a neque sagittis.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12 text-center pr-lg-0 md-mb-5 wow fadeInLeft">
                    <div class="price-item price-transform basic-plan">
                        <div class="price-box clearfix">
                            <div class="price-package">
                                <h3 class="mb-2rem">basic</h3>
                            </div>
                            <div class="price-icon">
                                <i class="fa fa-lightbulb"></i>
                            </div>
                        </div>

                        <div class="price">
                            <h2 class="position-relative"><span class="dollar">$</span><span class="color-green">75</span><span class="month"> /project</span></h2>
                            <p class="price-sub-heading">Single Page Website / Portfolio</p>
                        </div>
                        <div class="price-features bg-green">
                            <h4 class="mb-0 text-capitalize">basic features</h4>
                        </div>
                        <div class="price-description">
                            <p class="bg-light-gray2">Attractive Landing Page</p>
                            <p class="bg-light-gray">Connect Domain</p>
                            <p class="bg-light-gray2">Hosting Management</p>
                        </div>
                        <div class="text-center">
                            <a href="javascript:void(0)" class="btn-setting btn-green btn-hvr-transparent-grey color-black">learn more</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 text-center px-lg-0 md-mb-5 wow fadeInUp">
                    <div class="price-item standard-plan">
                        <div class="price-box clearfix">
                            <div class="price-icon mb-2rem">
                                <i class="fa fa-laptop"></i>
                            </div>
                            <div class="price-package">
                                <h3 class="mb-0">standard</h3>
                            </div>
                        </div>

                        <div class="price">
                            <h2 class="position-relative"><span class="dollar">$</span><span class="color-black">300</span><span class="month"> /project</span></h2>
                            <p class="price-sub-heading">A super package for starter</p>
                        </div>
                        <div class="price-features bg-blue">
                            <h4 class="mb-0 text-capitalize">standard features</h4>
                        </div>
                        <div class="price-description">
                            <p class="bg-light-gray2">Personal/Business Website</p>
                            <p class="bg-light-gray">Ideal For Small Scale Business</p>
                            <p class="bg-light-gray2">Admin Panel (CMS)</p>
                            <p class="bg-light-gray">Connect Domain</p>
                            <p class="bg-light-gray2">Hosting Management</p>
                        </div>
                        <div class="text-center">
                            <a href="javascript:void(0)" class="btn-setting btn-blue btn-hvr-transparent-grey color-black">learn more</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12 text-center pl-lg-0 wow fadeInRight">
                    <div class="price-item price-transform">
                        <div class="price-box clearfix">
                            <div class="price-package">
                                <h3 class="mb-2rem">advance</h3>
                            </div>
                            <div class="price-icon">
                                <i class="fa fa-briefcase"></i>
                            </div>
                        </div>

                        <div class="price">
                            <h2 class="position-relative"><span class="dollar">$</span><span class="color-green">500</span><span class="month"> /month</span></h2>
                            <p class="price-sub-heading">A perfect package for E-Commerce</p>
                        </div>
                        <div class="price-features bg-green">
                            <h4 class="mb-0 text-capitalize">advance features</h4>
                        </div>
                        <div class="price-description">
                            <p class="bg-light-gray2">Standard Services +</p>
                            <p class="bg-light-gray">Online Store / eCommerce Platform</p>
                            <p class="bg-light-gray2">Ideal For Medium/Large Scale Business</p>
                        </div>
                        <div class="text-center">
                            <a href="javascript:void(0)" class="btn-setting btn-green btn-hvr-transparent-grey color-black">learn more</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Price ends -->

    <!-- Clients start -->
    <?php // require_once 'partials/clients.php'; 
    ?>
    <!-- Clients ends -->

    <!-- Request start -->
    <?php // require_once 'partials/request.php'; 
    ?>
    <!--Request ends-->

    <!-- Request Boxes start -->
    <?php // require_once 'partials/request-boxes.php'; 
    ?>
    <!-- Request Boxes end -->

    <!-- Blog start -->
    <?php // require_once 'partials/blog.php'; 
    ?>
    <!-- Blog ends -->

    <!-- Brands starts -->
    <?php // require_once 'partials/brands.php'; 
    ?>
    <!-- Brands ends -->

    <!-- Contact & Map starts -->
    <?php // require_once 'partials/contact-us.php'; 
    ?>
    <!-- Contact & Map ends -->

    <!-- Footer starts -->
    <footer class="bg-light-gray footer-transform-padding pt-0">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12 text-center">
                    <p class="copyrights px-0 py-5" style="font-weight: bold;">&copy; <span class="text-success">Web</span> <span class="text-info">Owls</span> Made with Love and Passion.</p>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer ends -->


    <!-- JavaScript -->
    <script src="./app/dist/main/vendor/js/bundle.min.js"></script>

    <!-- Plugin Js -->
    <script src="./app/dist/main/vendor/js/jquery.fancybox.min.js"></script>
    <script src="./app/dist/main/vendor/js/owl.carousel.min.js"></script>
    <script src="./app/dist/main/vendor/js/swiper.min.js"></script>
    <script src="./app/dist/main/vendor/js/jquery.cubeportfolio.min.js"></script>
    <script src="./app/dist/main/vendor/js/jquery.appear.js"></script>
    <script src="./app/dist/main/vendor/js/parallaxie.min.js"></script>
    <script src="./app/dist/main/vendor/js/wow.min.js"></script>
    <script src="./app/dist/main/vendor/js/select2.min.js"></script>
    <!-- Slick JS File -->
    <script src="./app/dist/main/vendor/js/slick.min.js"></script>

    <!-- REVOLUTION JS FILES -->
    <script src="./app/dist/main/vendor/js/jquery.themepunch.tools.min.js"></script>
    <script src="./app/dist/main/vendor/js/jquery.themepunch.revolution.min.js"></script>
    <!-- SLIDER REVOLUTION EXTENSIONS -->
    <script src="./app/dist/main/vendor/js/extensions/revolution.extension.actions.min.js"></script>
    <script src="./app/dist/main/vendor/js/extensions/revolution.extension.carousel.min.js"></script>
    <script src="./app/dist/main/vendor/js/extensions/revolution.extension.kenburn.min.js"></script>
    <script src="./app/dist/main/vendor/js/extensions/revolution.extension.layeranimation.min.js"></script>
    <script src="./app/dist/main/vendor/js/extensions/revolution.extension.migration.min.js"></script>
    <script src="./app/dist/main/vendor/js/extensions/revolution.extension.navigation.min.js"></script>
    <script src="./app/dist/main/vendor/js/extensions/revolution.extension.parallax.min.js"></script>
    <script src="./app/dist/main/vendor/js/extensions/revolution.extension.slideanims.min.js"></script>
    <script src="./app/dist/main/vendor/js/extensions/revolution.extension.video.min.js"></script>

    <!-- Google Map Api -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAgIfLQi8KTxTJahilcem6qHusV-V6XXjw"></script>
    <script src="./app/dist/main/consulting/js/maps.min.js"></script>

    <!--contact form-->
    <script src="./app/dist/main/vendor/js/contact_us.js"></script>

    <!-- custom script -->
    <script src="./app/dist/main/consulting/js/script.js"></script>

</body>

</html>