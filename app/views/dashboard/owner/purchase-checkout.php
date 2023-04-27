<?php
defined('VIEW') ? null : define('VIEW', ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner');
defined('INCLUDES') ? null : define('INCLUDES', VIEW . DS . 'partials');
$panel = 'DASHBOARD';
$section = 'PURCHASE';
$subSection = 'CHECKOUT';
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
        <div class="col-12 grid-margin">
          <!-- alert -->
          <?php require_once 'partials/alert.php'; ?>
          <!-- //alert -->
          <div class="card">
            <div class="card-body">
              <form class="form-sample" action="./dashboard/purchase/confirm" method="POST" enctype="multipart/form-data">
                <div class="row">
                  <div class="col-sm-12">
                    <strong class="d-inline-block mt-2">
                      <span><?php echo date('Y-m-d'); ?></span>
                      <span class="text-muted"><?php echo date('H:i:s'); ?></span>
                    </strong>
                    <button class="btn btn-success float-end" type="submit" role="button" id="confirmButton" name="confirmPurchase" value="true">Confirm Purchase</button>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group row">
                      <label class="col-sm-12 col-form-label">Receipt No</label>
                      <div class="col-sm-12">
                        <input type="number" class="form-control" name="purchaseRaseedNo" value="<?php if (Input::postExists('purchaseRaseedNo') === true) {
                                                                                                    echo Input::getPost('purchaseRaseedNo');
                                                                                                  } else {
                                                                                                    echo (isset($response->data->lastReceiptNo) === true and $response->data->lastReceiptNo !== false) ? ($response->data->lastReceiptNo + 1) : null;
                                                                                                  } ?>" placeholder="e.g. 56852" required min="<?php if (Input::postExists('purchaseRaseedNo') === true) {
                                                                                                                                                  echo Input::getPost('purchaseRaseedNo');
                                                                                                                                                } else {
                                                                                                                                                  echo (isset($response->data->lastReceiptNo) === true and $response->data->lastReceiptNo !== false) ? ($response->data->lastReceiptNo + 1) : null;
                                                                                                                                                } ?>" minlength="1" maxlength="32" autocomplete="off" />
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group row">
                      <label class="col-sm-12 col-form-label">Supplier Name</label>
                      <div class="col-sm-12">
                        <input type="text" class="form-control" name="supplierName" list="storeSuppliers" value="<?php echo Input::getPost('supplierName'); ?>" placeholder="e.g. Abc Xyz" required minlength="3" maxlength="31" autocomplete="off" />
                        <datalist id="storeSuppliers">
                          <?php
                          if (isset($response->data->suppliers) === true and is_array($response->data->suppliers) === true and count($response->data->suppliers) > 0) {
                            foreach ($response->data->suppliers as $supplier) {
                              echo "<option value='{$supplier->name}'>{$supplier->name}</option>";
                            }
                          }
                          ?>
                        </datalist>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group row">
                      <label class="col-sm-12 col-form-label">Supplier Contact</label>
                      <div class="col-sm-12">
                        <input type="number" class="form-control" name="supplierContact" value="<?php echo Input::getPost('supplierContact'); ?>" placeholder="e.g. 03001234567" minlength="6" maxlength="15" autocomplete="off" />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="table-responsive mt-3">
                  <table class="table table-hover">
                    <thead>
                      <th>Sr</th>
                      <th>Item</th>
                      <th class="text-center">Quantity</th>
                      <th class="text-center">Price</th>
                      <th class="text-center">Total</th>
                      <!-- <th>Action</th> -->
                    </thead>
                    <tbody id="cartItems">
                      <?php
                      if (isset($response->data->cart->items) === true and is_array($response->data->cart->items) === true and count($response->data->cart->items) > 0) {
                        $count = 0;
                        foreach ($response->data->cart->items as $cItem) {
                          $count++;
                      ?>
                          <tr>
                            <td><?php echo ($count < 10) ? '0' . $count : $count; ?></td>
                            <td><?php echo $cItem->purchaseItem; ?></td>
                            <td class="text-center"><?php echo $cItem->purchaseQuantity; ?></td>
                            <td class="text-center"><?php echo $cItem->purchasePrice; ?></td>
                            <td class="text-center"><?php echo $cItem->subTotal; ?></td>
                            <!-- <td><span class="badge badge-danger" onclick="removeCartItem(this)">X</span></td> -->
                          </tr>
                      <?php
                        }
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group row">
                      <label class="col-sm-12 col-form-label" title="Purchase Note">Add Note <small>(Optional)</small></small></label>
                      <div class="col-sm-12">
                        <textarea class="form-control" style="resize: none;" name="purchaseNote" rows="5" autocomplete="off" placeholder="Add purchase notes..."></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="table-responsive mt-3">
                      <table class="table table-hover">
                        <tbody>
                          <tr>
                            <td class="text-right"><strong>Gross Total</strong></td>
                            <td class="text-left" id="grossTotal"><?php echo $response->data->cart->bill->netTotal; ?></td>
                          </tr>
                          <tr>
                            <td class="text-right"><strong>Discount</strong></td>
                            <td class="text-left"><input type="number" step="0.01" name="purchaseDiscount" class="form-control" value="<?php echo (Input::postExists('purchaseDiscount') === true) ? Input::getPost('purchaseDiscount') : '0'; ?>" min="0" max="<?php echo $response->data->cart->bill->netTotal; ?>" onload="updateTotal(this)" onkeypress="updateTotal(this)" onkeyup="updateTotal(this)" onchange="updateTotal(this)" /></td>
                          </tr>
                          <tr>
                            <td class="text-right"><strong>Net Total</strong></td>
                            <td class="text-left" id="netTotal"><?php echo $response->data->cart->bill->netTotal; ?></td>
                          </tr>
                          <tr>
                            <td class="text-right"><strong>Paid</strong></td>
                            <td class="text-left"><input type="number" step="0.01" id="paid" name="purchasePaid" class="form-control" value="<?php echo (Input::postExists('purchasePaid') === true) ? Input::getPost('purchasePaid') : $response->data->cart->bill->netTotal; ?>" min="0" max="<?php echo $response->data->cart->bill->netTotal; ?>" /></td>
                          </tr>
                        </tbody>
                      </table>
                      <a class="btn btn-danger float-start m-2" href="./dashboard/purchase/start">Cancel Purchase</a>
                      <button class="btn btn-success float-start m-2" type="submit" role="button" id="confirmButton" name="confirmPurchase" value="true">Confirm Purchase</button>
                    </div>
                  </div>
                </div>
              </form>
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

  <!-- End custom js for this page-->
  <script type="text/javascript">
    function keyPress(e) {
      var key;
      if (window.event) {
        key = e.keyCode;
      } else if (e.which) {
        key = e.which;
      }
      if (key === 13) {
        addToCart();
      }
    }

    function updateTotal(elem) {
      var netTotal = document.getElementById('netTotal');
      var grossTotal = document.getElementById('grossTotal');
      var newTotal = grossTotal.innerHTML - elem.value;
      netTotal.innerHTML = newTotal;
      updatePaid(newTotal);
    }

    function updatePaid(amount) {
      document.getElementById('paid').value = amount;
    }
  </script>
</body>

</html>