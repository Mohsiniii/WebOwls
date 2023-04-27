<?php
defined('VIEW') ? null : define('VIEW', ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner');
defined('INCLUDES') ? null : define('INCLUDES', VIEW . DS . 'partials');
$panel = 'DASHBOARD';
$section = 'DUES';
$subSection = 'SALES';
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
                  <h4 class="card-title">Dues History for Sales</h4>
                  <!-- <p class="card-description">
                    Add class <code>.table-striped</code>
                  </p> -->
                  <?php if (isset($response->data->dues) === true and is_array($response->data->dues) === true and count($response->data->dues) > 0) { ?>
                    <div class="table-responsive">
                      <table class="table table-hover" id="myDataTable">
                        <thead>
                          <tr class="align-middle">
                            <th class="text-left">
                              Invoice #
                            </th>
                            <th>
                              Buyer
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
                              Dues
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
                          <?php foreach ($response->data->dues as $entry) { ?>
                            <tr class="align-middle">
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
                                <?php
                                echo (isset($entry->payment->total) === true) ? Input::formatMoney($entry->payment->total) : 'N/A';
                                ?>
                              </td>
                              <td>
                                <span id="saleDues<?php echo $entry->id ?>" class="bg-warning p-2 font-weight-bold">
                                  <?php
                                  echo (isset($entry->bill->netTotal) === true and isset($entry->payment->total) === true) ? Input::formatMoney(($entry->bill->netTotal - $entry->payment->total)) : 'N/A';
                                  ?>
                                </span>
                              </td>
                              <td>
                                <?php echo date('Y-m-d', strtotime($entry->date)); ?>
                              </td>
                              <td>
                                <button type="button" role="button" class="btn btn-sm btn-primary" data-sale="<?php echo (isset($entry->id) === true) ? $entry->id : null; ?>" data-amount="<?php echo (isset($entry->bill->netTotal) === true and isset($entry->payment->total) === true) ? Input::roundMoney(($entry->bill->netTotal - $entry->payment->total)) : null; ?>" onclick="receiveDues(this)">Receive</button>
                              </td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  <?php } else {
                    echo "<di class='card-header small'>There is not any pending due.</di>";
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

  <!-- Sweet Alert -->
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.16/dist/sweetalert2.all.min.js"></script>
  <script type="text/javascript">
    function receiveDues(sale) {
      var saleID = sale.getAttribute("data-sale");
      var saleDue = sale.getAttribute("data-amount");
      saleDue = parseFloat(saleDue);

      Swal.fire({
        title: 'Enter Receiving Amount',
        html: `<input type="number" id="amount" class="swal2-input" placeholder="Receiving Amount">`,
        confirmButtonText: 'Receive',
        focusConfirm: false,
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        allowEnterKey: true,
        width: 400,
        allowOutsideClick: true,
        allowEscapeKey: true,
        preConfirm: () => {
          const amount = Swal.getPopup().querySelector('#amount').value
          if (!amount) {
            Swal.showValidationMessage(`Please enter amount.`)
          } else if (isNumeric(amount) === false) {
            Swal.showValidationMessage(`Please enter valid amount.`)
          } else if (amount > saleDue) {
            Swal.showValidationMessage(`Enter amount within the limit.`)
          }
          return {
            amount: amount
          }
        }
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: 'Receiving Dues...',
            timerProgressBar: false,
            showConfirmButton: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });

          var formdata = new FormData();
          formdata.append("dues", true);
          formdata.append("action", 'receive');
          formdata.append("saleID", saleID);
          formdata.append("amount", result.value.amount)

          var ajax = new XMLHttpRequest();
          ajax.upload.addEventListener("progress", function(event) {
            // progress function
            var percent = (event.loaded / event.total) * 100;
            percent = Math.round(percent) + "%";
          }, false);
          ajax.addEventListener("load", function() {
            // completeHandler
            var response = event.target.responseText;
            console.log(response);
            if (isJsonString(response) === true) {
              response = JSON.parse(response);
              if (response.status === true) {
                var remainingDues = parseFloat(saleDue) - parseFloat(result.value.amount);
                remainingDues = parseFloat(remainingDues);
                remainingDues = remainingDues.toFixed(2);
                document.getElementById('saleDues' + saleID).innerText = remainingDues;
                sale.setAttribute("data-amount", remainingDues);
                Swal.fire({
                  title: 'Success!',
                  html: 'Dues: ' + response.message.success,
                  icon: 'success'
                });
              } else {
                Swal.fire({
                  title: 'Failed!',
                  html: response.message.error,
                  icon: 'error'
                });
              }
            } else {
              Swal.fire({
                title: 'Failed!',
                html: 'Failed to receive dues.',
                icon: 'error'
              });
            }
          }, false);
          ajax.addEventListener("error", function() {
            // errorHandler
          }, false);
          ajax.addEventListener("abort", function() {
            // abortHandler
          }, false);
          ajax.open("POST", "./dashboard/dues/receive/sale", true);
          ajax.send(formdata);
        }

      });

    }

    function isNumeric(num) {
      if (parseInt(num)) {
        return true;
      } else if (parseFloat(num)) {
        return true;
      } else {
        return false;
      }
    }

    function isJsonString(str) {
      try {
        JSON.parse(str);
      } catch (e) {
        return false;
      }
      return true;
    }
  </script>
</body>

</html>