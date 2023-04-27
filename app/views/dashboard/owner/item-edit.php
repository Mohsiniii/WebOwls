<?php
defined('VIEW') ? null : define('VIEW', ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner');
defined('INCLUDES') ? null : define('INCLUDES', VIEW . DS . 'partials');
$panel = 'DASHBOARD';
$section = 'ITEMS';
$subSection = 'VIEW';
$storeItem = (isset($response->data->item) === true) ? $response->data->item : null;
if (isset($storeItem->id) === false) {
  Redirect::to('./dashboard/item/view/all');
}
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
  <title>Dashboard - <?php echo (isset($appData->name->full) === true) ? $appData->name->full : 'Raseed'; ?></title>
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
        <div class="col-lg-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <!-- alert -->
              <?php require_once 'partials/alert.php'; ?>
              <!-- //alert -->

              <small class="card-title d-block">
                <strong><?php echo (isset($storeItem->item->name) === true) ? $storeItem->item->name : null; ?></strong>
                <br />
                <span class="text-muted mr-3 pt-2 float-start">
                  <i class="fa fa-sm fa-bookmark"></i>&nbsp;
                  <?php echo (isset($response->data->item->brand->name) === true) ? $response->data->item->brand->name : null; ?>
                </span>
                <span class="text-muted pt-2 float-start">
                  <i class="fa fa-sm fa-tags"></i>&nbsp;
                  <?php echo (isset($storeItem->category->name) === true) ? $storeItem->category->name : null; ?>
                </span>
                <div class="ms-auto text-end">
                  <a class="btn btn-link text-success px-3 mb-0" href="./dashboard/item/add-variants?item=<?php echo $response->data->item->id; ?>"><i class="fas fa-plus text-success me-2" aria-hidden="true"></i>Variant</a>
                  <a class="btn btn-link text-info px-3 mb-0" href="./dashboard/item/edit/<?php echo $response->data->item->id; ?>"><i class="fas fa-pencil-alt text-info me-2" aria-hidden="true"></i>Edit</a>
                  <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="./dashboard/item/remove/<?php $storeItem->id; ?>" onclick="return confirm('Are you sure you want to remove this item?');"><i class="far fa-trash-alt me-2" aria-hidden="true"></i>Delete</a>
                </div>
              </small>
              <br />
              <?php if (is_null($storeItem) === false) { ?>
                <form class="form-sample" action="./dashboard/item/edit/<?php echo $storeItem->id; ?>" method="POST" enctype="multipart/form-data">
                  <!-- <p class="card-description">Item Details</p> -->
                  <div class="row mt-3 mb-5 justify-content-center">
                    <div class="col-sm-12 col-md-10 col-lg-8">
                      <div class="row shadow p-5">
                        <div class="col-sm-12">
                          <!-- alert -->
                          <?php require_once 'partials/alert.php'; ?>
                          <!-- //alert -->
                        </div>
                        <div class="col-md-12">
                          <div class="form-group row">
                            <div class="col-sm-3">
                              <label class="col-form-label"><b>Item</b></label>
                            </div>
                            <div class="col-sm-9">
                              <input type="text" class="form-control" name="itemName" value="<?php if (Input::postExists('itemName') === true) {
                                                                                                echo Input::getPost('itemName');
                                                                                              } else {
                                                                                                echo (isset($storeItem->item->name) === true) ? $storeItem->item->name : 'N/A';
                                                                                              } ?>" required minlength="2" maxlength="50" autofocus autocomplete="off" />
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group row">
                            <div class="col-sm-3">
                              <label class="col-form-label"><b>Category</b></label>
                            </div>
                            <div class="col-sm-9">
                              <input type="text" class="form-control" name="itemCategory" value="<?php if (Input::postExists('itemCategory') === true) {
                                                                                                    echo Input::getPost('itemCategory');
                                                                                                  } else {
                                                                                                    echo (isset($storeItem->category->name) === true) ? $storeItem->category->name : 'N/A';
                                                                                                  } ?>" required minlength="2" maxlength="50" autocomplete="off" />
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group row">
                            <div class="col-sm-3">
                              <label class="col-form-label"><b>Brand</b></label>
                            </div>
                            <div class="col-sm-9">
                              <input type="text" class="form-control" name="itemBrand" value="<?php if (Input::postExists('itemBrand') === true) {
                                                                                                echo Input::getPost('itemBrand');
                                                                                              } else {
                                                                                                echo (isset($storeItem->brand->name) === true) ? $storeItem->brand->name : 'N/A';
                                                                                              } ?>" required minlength="2" maxlength="50" autocomplete="off" />
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group row">
                            <div class="col-sm-3">
                              <label class="col-form-label"><b>General / Salt</b></label>
                            </div>
                            <div class="col-sm-9">
                              <input type="text" class="form-control" name="itemGeneral" value="<?php if (Input::postExists('itemGeneral') === true) {
                                                                                                  echo Input::getPost('itemGeneral');
                                                                                                } else {
                                                                                                  echo (isset($storeItem->general) === true) ? $storeItem->general : '';
                                                                                                } ?>" minlength="2" maxlength="100" autocomplete="off" />
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <input type="hidden" name="itemUnits" value="<?php echo (isset($storeItem->units) === true) ? $storeItem->units : '1'; ?>" />
                          <button class="btn btn-success float-end" type="submit" name="editItem" value="true">Update Item</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              <?php } else {
                echo "<div class='card-header small'>Item not found. <a href='./dashboard/item/view/all>View Inventory</a></div>";
              } ?>
            </div>
          </div>
        </div>
      </div>
      <?php require_once 'partials/footer.php'; ?>
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
    function addOption() {
      var option = document.createElement('div');
      option.className = 'col-md-4';
      var optInner = document.createElement('div');
      optInner.className = 'form-group row';
      var optLabel = document.createElement('label');
      optLabel.className = 'col-form-label';
      optLabel.appendChild(document.createTextNode('Option'));
      var optInputBox = document.createElement('div');
      optInputBox.className = 'col-sm-12';
      var optInput = document.createElement('input');
      optInput.type = 'text';
      optInput.className = 'form-control';
      optInput.name = 'option[]';
      optInput.minLength = '2';
      optInput.maxLength = '100';
      optInput.autocomplete = 'off';
      optInputBox.appendChild(optInput);
      optInner.appendChild(optLabel);
      optInner.appendChild(optInputBox);
      option.appendChild(optInner);
      var wrapper = document.getElementById('optionsWrapper');
      wrapper.appendChild(option);
    }
  </script>
</body>

</html>