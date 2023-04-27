<?php
defined('VIEW') ? null : define('VIEW', ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner');
defined('INCLUDES') ? null : define('INCLUDES', VIEW . DS . 'partials');
$panel = 'DASHBOARD';
$section = 'STAKEHOLDERS';
$subSection = 'SUPPLIERS';
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

              <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                  <h4 class="card-title">Supplier's Purchases History</h4>
                  <?php if (isset($response->data->supplies) === true and is_array($response->data->supplies) === true and count($response->data->supplies) > 0) { ?>
                    <div class="table-responsive">
                      <table class="table table-hover" id="example">
                        <thead>
                          <tr>
                            <th>
                              Invoice #
                            </th>
                            <th>
                              Supplier
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
                              Paid
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
                          <?php foreach ($response->data->supplies as $entry) { ?>
                            <tr>
                              <td>
                                <?php echo (isset($entry->receiptNo) === true) ? 'S' . $entry->receiptNo : 'N/A'; ?>
                              </td>
                              <td>
                                <?php echo (is_null($entry->supplier) === false and isset($entry->supplier->name) === true) ? $entry->supplier->name : 'N/A'; ?>
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
                                <?php
                                echo (isset($entry->payment->total) === true) ? Input::formatMoney($entry->payment->total) : 'N/A';
                                ?>
                              </td>
                              <td>
                                <?php echo $entry->date; ?>
                              </td>
                              <td>
                                <a href="./dashboard/purchase/receipt/<?php echo $entry->id; ?>" target="_blank">Purchase Receipt</a>
                              </td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  <?php } else {
                    echo "<di class='card-header small'>Stock book is empty for this supplier.</di>";
                  } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php require_once 'partials/footer.php'; ?>
    </div>
  </main>
  <?php require_once 'partials/settings.php'; ?>
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
</body>

</html>