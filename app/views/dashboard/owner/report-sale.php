<?php
defined('VIEW') ? null : define('VIEW', ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner');
defined('INCLUDES') ? null : define('INCLUDES', VIEW . DS . 'partials');
$panel = 'DASHBOARD';
$section = 'REPORTS';
$subSection = 'SALES';
$report = (isset($response->data->report) === true) ? $response->data->report : null;
// var_dump($report);die();
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
  <!-- data tables -->
  <!-- <link rel="stylesheet" href="app/dist/plugins/DataTables/DataTables-1.10.25/css/jquery.dataTables.min.css" /> -->
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
      <div class="card">
        <div class="card-body">
          <!-- <h4 class="card-title">Cash-Book History</h4> -->
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h4 class="font-weight-bold mb-0">
                    <?php echo (isset($report->type) === true) ? ucfirst(strtolower($report->type)) : null; ?>
                    Sale Report
                  </h4>
                </div>
                <div>
                  <div class="btn-group" role="group" aria-label="Basic example">
                    <button type="button" class="btn btn-<?php echo (isset($report->type) === true and $report->type == 'DAILY') ? null : 'outline-'; ?>primary" onclick="location.href = './dashboard/report/sale/?type=Daily';">Daily</button>
                    <button type="button" class="btn btn-<?php echo (isset($report->type) === true and $report->type == 'MONTHLY') ? null : 'outline-'; ?>primary" onclick="location.href = './dashboard/report/sale/?type=Monthly';">Monthly</button>
                    <button type="button" class="btn btn-<?php echo (isset($report->type) === true and $report->type == 'YEARLY') ? null : 'outline-'; ?>primary" onclick="location.href = './dashboard/report/sale/?type=Yearly';">Yearly</button>
                  </div>
                  <!-- <a href="./dashboard/item/add/" type="button" class="btn btn-primary btn-icon-text btn-rounded">
                    <i class="ti-plus btn-icon-prepend"></i>New Item
                  </a> -->
                </div>
              </div>
            </div>
          </div>
          <!-- Sale Stats -->
          <div class="row mb-3">
            <div class="col-sm-6 col-md-4 grid-margin stretch-card mt-3">
              <div class="card">
                <div class="card-body">
                  <p class="card-title text-md-left text-xl-left">Sales Amount</p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-md-left justify-content-xl-between align-items-center">
                    <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0">
                      <?php echo (isset($report->data->totalAmount) === true) ? Input::formatMoney($report->data->totalAmount) : 'N/A'; ?>
                    </h3>
                    <!-- <i class="ti-shopping-cart icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i> -->
                  </div>
                  <p class="mb-0 mt-2">
                    <span class="text-black ml-1"><small>(<?php echo date('M Y'); ?>)</small></span>
                  </p>
                </div>
                <div class="card-body">
                  <p class="card-title text-md-left text-xl-left">Sales Discount</p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-md-left justify-content-xl-between align-items-center">
                    <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0">
                      <?php echo (isset($report->data->totalDiscount) === true) ? Input::formatMoney($report->data->totalDiscount) : 'N/A'; ?>
                    </h3>
                    <!-- <i class="ti-shopping-cart icon-md text-muted mb-0 mb-md-3 mb-xl-0"></i> -->
                  </div>
                  <p class="mb-0 mt-2">
                    <span class="text-black ml-1"><small>(<?php echo date('M Y'); ?>)</small></span>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-md-8 grid-margin stretch-card mt-3">
              <div class="card">
                <div class="card-body" style="min-height: 315px !important;">
                  <p class="card-title text-md-left text-xl-left">
                    Most Sold Products
                    <span class="text-black ml-1"><small>(<?php echo date('M Y'); ?>)</small></span>
                  </p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-md-left justify-content-xl-between align-items-center">
                    <div class="col-md-12 col-xl-12">
                      <div class="table-responsive mb-3 mb-md-0">
                        <table class="table table-borderless report-table">
                          <?php
                          if (isset($report->data->topItems) === true and is_array($report->data->topItems) and count($report->data->topItems) > 0) {
                            $arrayKeys = array_keys($report->data->topItems);
                            $max = $report->data->topItems[$arrayKeys[0]]->quantity;
                            foreach ($report->data->topItems as $mpi) {
                              $percentage = (($mpi->quantity / $max) * 100);
                              $storeItem = $mpi->siVariant->item;
                              $siVariant = $mpi->siVariant;
                          ?>
                              <tr>
                                <td class="text-muted">
                                  <?php
                                  echo (isset($storeItem->item->name) === true) ? "<b>" . $storeItem->item->name . "</b>" : "N/A";
                                  if (is_array($siVariant->options) === true and count($siVariant->options) > 0) {
                                    foreach ($siVariant->options as $opt) {
                                      echo ', ' . $opt->option . ': ' . $opt->value;
                                      echo (is_null($opt->unit) === false) ? ' ' . $opt->unit : null;
                                    }
                                  }
                                  ?>
                                </td>
                                <td class="w-100 px-0">
                                  <div class="progress progress-md mx-4">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $percentage; ?>%" aria-valuenow="<?php echo ($mpi->quantity) ? $mpi->quantity : '0'; ?>" aria-valuemin="0" aria-valuemax="<?php echo $max; ?>"></div>
                                  </div>
                                </td>
                                <td>
                                  <h5 class="font-weight-bold mb-0"><?php echo ($mpi->quantity) ? $mpi->quantity : null; ?></h5>
                                </td>
                              </tr>
                          <?php
                            }
                          } else {
                            echo "<div class='card-header small'>'Data not found for " . strtolower($report->type) . " report.</div>";
                          }
                          ?>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Sale Entries -->
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">
                    <?php echo (isset($report->type) === true) ? ucfirst(strtolower($report->type)) : null; ?>
                    Sales History
                  </h4>
                  <!-- <p class="card-description">
                    Add class <code>.table-striped</code>
                  </p> -->
                  <?php if (isset($report->data->entries) === true and is_array($report->data->entries) === true and count($report->data->entries) > 0) { ?>
                    <div class="table-responsive">
                      <table class="table table-hover" id="example">
                        <thead>
                          <tr>
                            <th>
                              Invoice #
                            </th>
                            <th>
                              Customer
                            </th>
                            <th>
                              Gross Total
                            </th>
                            <th>
                              Discount
                            </th>
                            <th>
                              Net Total
                            </th>
                            <th>
                              Date
                            </th>
                            <th>
                              Actions
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($report->data->entries as $entry) { ?>
                            <tr>
                              <td>
                                <?php echo (isset($entry->receiptNo) === true) ? 'S' . $entry->receiptNo : 'N/A'; ?>
                              </td>
                              <td>
                                <?php echo (is_null($entry->buyer) === false and isset($entry->buyer->name) === true) ? $entry->buyer->name : 'N/A'; ?>
                              </td>
                              <td>
                                <?php echo (isset($entry->bill->grossTotal) === true) ? Input::formatMoney($entry->bill->grossTotal) : 'N/A'; ?>
                              </td>
                              <td>
                                <?php echo (isset($entry->bill->discount) === true) ? Input::formatMoney($entry->bill->discount) : 'N/A'; ?>
                              </td>
                              <td>
                                <?php echo (isset($entry->bill->netTotal) === true) ? Input::formatMoney($entry->bill->netTotal) : 'N/A'; ?>
                              </td>
                              <td>
                                <?php echo $entry->date; ?>
                              </td>
                              <td>
                                <a href="./dashboard/sale/receipt/<?php echo $entry->id; ?>" target="_blank">Sale Receipt</a>
                              </td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  <?php } else {
                    echo "<div class='card-header small'>Stock book is empty for sales.</div>";
                  } ?>
                </div>
              </div>
            </div>
          </div>
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
  <!-- jQuery -->
  <!-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script> -->
  <!-- //jQuery -->

  <!-- DataTables -->
  <!-- <script src="app/dist/plugins/DataTables/DataTables-1.10.25/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript">
    // $(document).ready(function() {
    //   $('#myDataTable').DataTable({
    //     "order": [
    //       [4, 'desc']
    //     ]
    //   });
    // });
  </script> -->

</body>

</html>