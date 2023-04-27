<?php
defined('VIEW') ? null : define('VIEW', ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner');
defined('INCLUDES') ? null : define('INCLUDES', VIEW . DS . 'partials');
$panel = 'DASHBOARD';
$section = 'ITEMS';
$subSection = 'ADD';
?>
<!--
=========================================================
* Soft UI Dashboard - v1.0.3
=========================================================

* Product Page: https://www.creative-tim.com/product/soft-ui-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)

* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- title -->
  <title>Add Variants - <?php echo (isset($appData->name->full) === true) ? $appData->name->full : 'Raseed'; ?></title>
  <!-- basepath -->
  <base href="<?php echo HTML_BASE_PATH; ?>" />

  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="./app/dist/dashboard/css/nucleo-icons.css" rel="stylesheet" />
  <link href="./app/dist/dashboard/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="./app/dist/dashboard/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="./app/dist/dashboard/css/soft-ui-dashboard.css?v=1.0.3" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-100">

  <!-- sidebar -->
  <?php require_once 'partials/sidebar.php'; ?>
  <!-- // sidebar -->

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <!-- Navbar -->
    <?php require_once 'partials/navbar.php'; ?>
    <!-- End Navbar -->

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12 grid-margin">
          <div class="card">
            <div class="card-body">
              <!-- alert -->
              <?php require_once 'partials/alert.php'; ?>
              <!-- //alert -->

              <!-- item head -->
              <div class="row">
                <div class="col-sm-12">
                  <h4>
                    <?php echo (isset($response->data->item->item->name) === true) ? $response->data->item->item->name : null; ?>
                    <a href="./dashboard/item/view/all/" type="button" class="btn btn-primary btn-icon-text btn-rounded float-end">
                      <i class="ti-palette menu-icon btn-icon-prepend"></i>Items
                    </a>
                  </h4>
                  <span class="text-muted small mr-3">
                    <i class="fa fa-sm fa-bookmark"></i>&nbsp;
                    <?php echo (isset($response->data->item->brand->name) === true) ? $response->data->item->brand->name : null; ?>
                  </span>
                  <span class="text-muted small">
                    &nbsp;
                    <i class="fa fa-sm fa-tags"></i>&nbsp;
                    <?php echo (isset($response->data->item->category->name) === true) ? $response->data->item->category->name : null; ?>
                  </span>
                </div>
                <div class="col-sm-6">
                  General / Salt:
                  <?php echo (isset($response->data->item->general) === true and is_null($response->data->item->general) === false) ? $response->data->item->general : 'N/A'; ?>
                </div>
                <div class="col-sm-6">
                  Units in Box/Pack:
                  <?php echo (isset($response->data->item->units) === true and is_null($response->data->item->units) === false) ? $response->data->item->units : 'N/A'; ?>
                </div>
              </div>
              <!-- //item head -->

              <div id="dummyVariant" class="d-none card border my-3 p-3">
                <div class="row mt-3">
                  <div class="col-12 mb-2">
                    <h5 class="text-center"><b><u>New Variant</u></b></h5>
                    <span class="badge badge-danger float-end border" style="cursor: pointer;" onclick="removeVariant(this)">
                      <i class="far fa-trash-alt fa-2x text-danger" aria-hidden="true"></i>
                    </span>
                  </div>
                  <?php foreach ($response->data->options as $opt) { ?>
                    <div class="col-12">
                      <div class="row d-flex justify-content-center">
                        <div class="col-md-6 col-lg-4">
                          <div class="form-group row">
                            <label class="" title="Required"><?php echo $opt->option->name; ?><sup>*</sup></label>
                            <div class="col-sm-12">
                              <input type="text" class="form-control" name="iov<?php echo $opt->id; ?>[]" placeholder="Enter <?php echo ucfirst(strtolower($opt->option->name)); ?>" minlength="1" maxlength="100" required autocomplete="off" />
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                          <div class="form-group row">
                            <label class="" title="Optional">Unit</label>
                            <div class="col-sm-12">
                              <input type="text" class="form-control" name="iovu<?php echo $opt->id; ?>[]" placeholder="Unit For <?php echo ucfirst(strtolower($opt->option->name)); ?>" minlength="1" maxlength="50" autocomplete="off" />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php } ?>
                </div>
                <div class="row d-flex justify-content-center">
                  <div class="col-md-6 col-lg-4">
                    <div class="form-group row">
                      <label class="col-form-label" title="Required">Sale Price<sup>*</sup> <small>/ per item</small></label>
                      <div class="col-sm-12">
                        <input type="number" step="0.01" class="form-control" name="ioPrice[]" min="0.1" required autocomplete="off" />
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-lg-4">
                    <div class="form-group row">
                      <label class="col-form-label" title="Optional">Discount %</label>
                      <div class="col-sm-12">
                        <input type="number" step="0.1" class="form-control" name="ioDiscount[]" min="0" max="100" value="0" required autocomplete="off" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- <h4 class="card-title">Horizontal Two column</h4> -->
              <form class="form-sample" action="./dashboard/item/add-variants?item=<?php echo $response->data->item->id; ?>" method="POST" enctype="multipart/form-data">
                <!-- <p class="card-description">Item Details</p> -->
                <div id="variantsWrapper">
                  <div id="variant" class="card my-3 border p-3">
                    <div class="row mt-3">
                      <div class="col-12 mb-2">
                        <h5 class="text-center"><b><u>New Variant</u></b></h5>
                      </div>
                      <?php foreach ($response->data->options as $opt) { ?>
                        <div class="col-12">
                          <div class="row d-flex justify-content-center">
                            <div class="col-md-6 col-lg-4">
                              <div class="form-group row">
                                <label class="col-form-label" title="Required"><?php echo $opt->option->name; ?><sup>*</sup></label>
                                <div class="col-sm-12">
                                  <input type="text" class="form-control" name="iov<?php echo $opt->id; ?>[]" placeholder="Enter <?php echo ucfirst(strtolower($opt->option->name)); ?>" minlength="1" maxlength="100" required autocomplete="off" />
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                              <div class="form-group row">
                                <label class="col-form-label" title="Optional">Unit</label>
                                <div class="col-sm-12">
                                  <input type="text" class="form-control" name="iovu<?php echo $opt->id; ?>[]" placeholder="Unit For <?php echo ucfirst(strtolower($opt->option->name)); ?>" minlength="1" maxlength="50" autocomplete="off" />
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      <?php } ?>
                    </div>
                    <div class="row d-flex justify-content-center">
                      <div class="col-md-6 col-lg-4">
                        <div class="form-group row">
                          <label class="col-form-label" title="Required">Sale Price<sup>*</sup> <small>/ per item</small></label>
                          <div class="col-sm-12">
                            <input type="number" step="0.01" class="form-control" name="ioPrice[]" min="0.1" required autocomplete="off" />
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6 col-lg-4">
                        <div class="form-group row">
                          <label class="col-form-label" title="Optional">Discount %</label>
                          <div class="col-sm-12">
                            <input type="number" step="0.1" class="form-control" name="ioDiscount[]" min="0" max="100" value="0" required autocomplete="off" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12 justify-content-right">
                    <button type="submit" role="button" class="btn btn-success ml-3 float-end" name="addItemVariant" value="true">Add Variants</button>
                    <button type="button" role="button" class="btn btn-info text-white mr-3" onclick="addMoreVariant()">Add More</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <?php require_once 'partials/footer.php'; ?>
      <!-- //Footer -->
    </div>
  </main>
  <div class="fixed-plugin">
    <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
      <i class="fa fa-cog py-2"> </i>
    </a>
    <div class="card shadow-lg">
      <div class="card-header pb-0 pt-3">
        <div class="float-start">
          <h5 class="mt-3 mb-0">Soft UI Configurator</h5>
          <p>See our dashboard options.</p>
        </div>
        <div class="float-end mt-4">
          <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
            <i class="fa fa-close"></i>
          </button>
        </div>
        <!-- End Toggle Button -->
      </div>
      <hr class="horizontal dark my-1" />
      <div class="card-body pt-sm-3 pt-0">
        <!-- Sidebar Backgrounds -->
        <div>
          <h6 class="mb-0">Sidebar Colors</h6>
        </div>
        <a href="javascript:void(0)" class="switch-trigger background-color">
          <div class="badge-colors my-2 text-start">
            <span class="badge filter bg-gradient-primary active" data-color="primary" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-dark" data-color="dark" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
          </div>
        </a>
        <!-- Sidenav Type -->
        <div class="mt-3">
          <h6 class="mb-0">Sidenav Type</h6>
          <p class="text-sm">Choose between 2 different sidenav types.</p>
        </div>
        <div class="d-flex">
          <button class="btn bg-gradient-primary w-100 px-3 mb-2 active" data-class="bg-transparent" onclick="sidebarType(this)">
            Transparent
          </button>
          <button class="btn bg-gradient-primary w-100 px-3 mb-2 ms-2" data-class="bg-white" onclick="sidebarType(this)">
            White
          </button>
        </div>
        <p class="text-sm d-xl-none d-block mt-2">
          You can change the sidenav type just on desktop view.
        </p>
        <!-- Navbar Fixed -->
        <div class="mt-3">
          <h6 class="mb-0">Navbar Fixed</h6>
        </div>
        <div class="form-check form-switch ps-0">
          <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)" />
        </div>
        <hr class="horizontal dark my-sm-4" />
        <a class="btn bg-gradient-dark w-100" href="https://www.creative-tim.com/product/soft-ui-dashboard-pro">Free Download</a>
        <a class="btn btn-outline-dark w-100" href="https://www.creative-tim.com/learning-lab/bootstrap/license/soft-ui-dashboard">View documentation</a>
        <div class="w-100 text-center">
          <a class="github-button" href="https://github.com/creativetimofficial/soft-ui-dashboard" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star creativetimofficial/soft-ui-dashboard on GitHub">Star</a>
          <h6 class="mt-3">Thank you for sharing!</h6>
          <a href="https://twitter.com/intent/tweet?text=Check%20Soft%20UI%20Dashboard%20made%20by%20%40CreativeTim%20%23webdesign%20%23dashboard%20%23bootstrap5&amp;url=https%3A%2F%2Fwww.creative-tim.com%2Fproduct%2Fsoft-ui-dashboard" class="btn btn-dark mb-0 me-2" target="_blank">
            <i class="fab fa-twitter me-1" aria-hidden="true"></i> Tweet
          </a>
          <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.creative-tim.com/product/soft-ui-dashboard" class="btn btn-dark mb-0 me-2" target="_blank">
            <i class="fab fa-facebook-square me-1" aria-hidden="true"></i>
            Share
          </a>
        </div>
      </div>
    </div>
  </div>
  <!--   Core JS Files   -->
  <script src="./app/dist/dashboard/js/core/popper.min.js"></script>
  <script src="./app/dist/dashboard/js/core/bootstrap.min.js"></script>
  <script src="./app/dist/dashboard/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="./app/dist/dashboard/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="./app/dist/dashboard/js/plugins/chartjs.min.js"></script>
  <script>
    var win = navigator.platform.indexOf("Win") > -1;
    if (win && document.querySelector("#sidenav-scrollbar")) {
      var options = {
        damping: "0.5",
      };
      Scrollbar.init(document.querySelector("#sidenav-scrollbar"), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="./app/dist/dashboard/js/soft-ui-dashboard.min.js?v=1.0.3"></script>

  <!-- Custom JS for Add Item -->
  <script type="text/javascript">
    function addMoreVariant() {
      var variantsWrapper = document.getElementById('variantsWrapper');
      var tmpVariant = document.getElementById('dummyVariant');
      var cloneVariant = tmpVariant.cloneNode(true);
      cloneVariant.classList.remove('d-none');
      cloneVariant.classList.add('d-block');
      variantsWrapper.appendChild(cloneVariant);
    }

    function removeVariant(elem) {
      elem.parentNode.parentNode.parentNode.remove();
    }
  </script>
</body>

</html>