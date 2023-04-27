<?php
	defined('VIEW') ? null : define('VIEW', ROOT_PATH.'app'.DS.'views'.DS.'dashboard'.DS.'owner');
	defined('INCLUDES') ? null : define('INCLUDES', VIEW.DS.'partials');
  $panel = 'DASHBOARD';
	$section = 'STOCK-BOOK';
  $subSection = 'SALES';
  // var_dump($response->data);die();
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
  <title>Sales Receipt - <?php echo (isset($appData->name->full) === true) ? $appData->name->full:'Raseed'; ?></title>
  <!-- basepath -->
  <base href="<?php echo HTML_BASE_PATH; ?>" />

    <!--     Fonts and icons     -->
    <link
      href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700"
      rel="stylesheet"
    />
    <!-- Nucleo Icons -->
    <link href="./app/dist/dashboard/css/nucleo-icons.css" rel="stylesheet" />
    <link href="./app/dist/dashboard/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script
      src="https://kit.fontawesome.com/42d5adcbca.js"
      crossorigin="anonymous"
    ></script>
    <link href="./app/dist/dashboard/css/nucleo-svg.css" rel="stylesheet" />
    <!-- CSS Files -->
    <link
      id="pagestyle"
      href="./app/dist/dashboard/css/soft-ui-dashboard.css?v=1.0.3"
      rel="stylesheet"
    />
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
                    <div class="col-sm-12 text-center h3"></div>
                    <div class="col-sm-12 text-center">
                      <strong class="d-inline-block mt-2 float-start">
                        Receipt # <span class="text-muted">S<?php echo (isset($response->data->receipt->no) === true) ? $response->data->receipt->no:'N/A'; ?></span>
                        <br/>
                        <span class="text-muted float-start"><?php echo (isset($response->data->receipt->date) === true) ? $response->data->receipt->date:'N/A'; ?></span>
                      </strong>
                      <h3 class="d-inline-block text-center">
                        <strong class="d-inline-block mt-2">
                          <span class="text-muted"><?php echo (isset($response->data->store->name) === true) ? $response->data->store->name:'N/A'; ?></span>
                        </strong>
                      </h3>
                      <strong class="d-inline-block mt-2 float-end">
                        <span><?php echo (isset($response->data->receipt->date) === true) ? $response->data->receipt->date:'N/A'; ?></span>
                      </strong>
                    </div>
                  </div>
                  <div class="table-responsive mt-3">
                    <table class="table table-hover">
                      <thead>
                        <th class="text-center">Sr</th>
                        <th>Item</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Price</th>
                        <th class="text-center">Total</th>
                        <!-- <th>Action</th> -->
                      </thead>
                      <tbody id="cartItems">
                        <?php
                          if(isset($response->data->receipt->orderLine) === true AND is_array($response->data->receipt->orderLine) === true AND count($response->data->receipt->orderLine) > 0){
                            $count = 0; foreach($response->data->receipt->orderLine as $item){ $count++;
                              ?>
                                <tr>
                                  <td class="text-center"><?php echo ($count < 10) ? '0'.$count:$count; ?></td>
                                  <td>
                                    <?php
                                      echo (isset($item->itemName) === true) ? $item->itemName:'N/A';
                                      if(isset($item->variant->options) === true AND is_array($item->variant->options) === true AND count($item->variant->options) > 0){
                                        echo " (";
                                        $optCount = 0;
                                        foreach($item->variant->options as $opt){
                                          $optCount++;
                                          echo $opt->option.':'.$opt->value;
                                          if($optCount < count($item->variant->options)){
                                            echo ', ';
                                          }
                                        }
                                        echo ")";
                                      }
                                    ?>
                                  </td>
                                  <td class="text-center"><?php echo (isset($item->quantity) === true) ? $item->quantity:'N/A'; ?></td>
                                  <td class="text-center"><?php echo (isset($item->quantity) === true) ? $item->quantity:'N/A'; echo ' x '; echo (isset($item->price) === true) ? $item->price:'N/A'; ?></td>
                                  <td class="text-center"><?php echo (isset($item->price) === true AND isset($item->quantity) === true) ? Input::formatMoney(($item->price*$item->quantity)):'N/A'; ?></td>
                                  <!-- <td><span class="badge badge-danger" onclick="removeCartItem(this)">X</span></td> -->
                                </tr>
                              <?php
                            }
                          }
                        ?>
                        <tr>
                          <td colspan="3" style="border: none;"></td>
                          <td class="text-right mt-3 pt-5"><strong>Gross Total</strong></td>
                          <td class="text-left mt-3 pt-5" id="grossTotal"><?php echo (isset($response->data->receipt->bill->grossTotal) === true) ? Input::formatMoney($response->data->receipt->bill->grossTotal):'N/A'; ?></td>
                        </tr>
                        <?php if(isset($response->data->receipt->bill->discount) === true AND $response->data->receipt->bill->discount > 0){ ?>
                          <tr>
                            <td colspan="3" style="border: none;"></td>
                            <td class="text-right"><strong>Discount</strong></td>
                            <td class="text-left" id="grossTotal"><?php echo (isset($response->data->receipt->bill->discount) === true) ? Input::formatMoney($response->data->receipt->bill->discount):'N/A'; ?></td>
                          </tr>
                        <?php } ?>
                        <tr>
                          <td colspan="3" style="border: none;"></td>
                          <td class="text-right"><strong>Net Total</strong></td>
                          <td class="text-left" id="netTotal"><?php echo (isset($response->data->receipt->bill->netTotal) === true) ? Input::formatMoney($response->data->receipt->bill->netTotal):'N/A'; ?></td>
                        </tr>
                        <tr>
                          <td colspan="3" style="border: none;"></td>
                          <td class="text-right"><strong>Paid</strong></td>
                          <td class="text-left" id="grossTotal"><?php echo (isset($response->data->receipt->payment->first) === true) ? Input::formatMoney($response->data->receipt->payment->first):null; ?></td>
                        </tr>
                        <?php if(isset($response->data->receipt->bill->due) === true AND $response->data->receipt->bill->due > 0){ ?>
                          <tr>
                            <td colspan="3" style="border: none;"></td>
                            <td class="text-right"><strong>Due</strong></td>
                            <td class="text-left" id="grossTotal"><?php echo (isset($response->data->receipt->bill->due) === true) ? Input::formatMoney($response->data->receipt->bill->due):null; ?></td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                    <?php
                      if(isset($response->data->receipt->dues) === true AND in_array($response->data->receipt->dues, array(2,3)) === true){
                        if(isset($response->data->receipt->payment->entries) === true AND is_array($response->data->receipt->payment->entries) === true AND count($response->data->receipt->payment->entries) > 0){
                          ?>
                            <table class="table table-hover">
                              <thead>
                                <tr>
                                  <th colspan="5" class="text-center">
                                    Payment History
                                  </th>
                                </tr>
                                <tr>
                                  <th class="text-center">Sr</th>
                                  <th>Receiving</th>
                                  <th>Type</th>
                                  <th>Receiving Date</th>
                                  <th>Balance</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php
                                  $remaining = 0;
                                  $previous = 0;
                                  $sr = 0;
                                  foreach($response->data->receipt->payment->entries as $dueEntry){ $sr++;
                                    ?>
                                      <tr>
                                        <td class="text-center">
                                          <?php echo ($sr < 10) ? '0'.$sr:$sr; ?>
                                        </td>
                                        <td>
                                          <?php
                                            echo "<span class='text-success font-weight-bold'><i class='ti-plus text-small small'></i>&nbsp;";
                                            echo (isset($dueEntry->amount) === true) ? Input::formatMoney($dueEntry->amount):'N/A';
                                            echo "</span>";
                                          ?>
                                        </td>
                                        <td>
                                          <?php
                                            if(isset($dueEntry->major) === true){
                                              if($dueEntry->major == 3){
                                                echo 'First Payment';
                                              } elseif($dueEntry->major == 4){
                                                echo 'Due Payment';
                                              } else {
                                                echo 'N/A';
                                              }
                                            } else {
                                              echo 'N/A';
                                            }
                                          ?>
                                        </td>
                                        <td>
                                          <?php echo $dueEntry->date; ?>
                                        </td>
                                        <td class="text-warning font-weight-bold">
                                          <?php
                                            if($sr == 1){
                                              $remaining = ($response->data->receipt->bill->netTotal - $response->data->receipt->payment->total);
                                            } else {
                                              $remaining += $previous;
                                            }
                                            $previous = $dueEntry->amount;
                                            echo Input::formatMoney($remaining);
                                          ?>
                                        </td>
                                      </tr>
                                    <?php
                                  }
                                ?>
                              </tbody>
                            </table>
                          <?php
                        } else {
                          echo 'No further receivings.';
                        }
                      }
                    ?>
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

    <!-- Settings Plugin -->
    <?php require_once 'partials/settings.php'; ?>
    <!-- //Settings Pluign -->

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