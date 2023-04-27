<?php
	defined('VIEW') ? null : define('VIEW', ROOT_PATH.'app'.DS.'views'.DS.'dashboard'.DS.'owner');
	defined('INCLUDES') ? null : define('INCLUDES', VIEW.DS.'includes');
	$panel = 'dashboard';
	$section = 'stock-book';
  $subSection = 'purchases';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Purchase Receipt - Dashboard | Raseed - An Intelligent Multi-Vendor Solution</title>

  <base href="<?php echo HTML_BASE_PATH; ?>" />

  <!-- favicon -->
  <?php include './app/views/includes/favicon.php'; ?>

  <!-- plugins:css -->
  <link rel="stylesheet" href="app/dist/dashboard/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="app/dist/dashboard/vendors/base/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="app/dist/dashboard/css/style.css">
  <!-- endinject -->

  <!-- css for icons -->
  <link href='https://use.fontawesome.com/releases/v5.8.1/css/all.css' rel="stylesheet">
  <!-- style for timeline -->
  <link rel="stylesheet" href="app/dist/dashboard/css/timeline.css">
  <!-- style for select2 -->
  <link rel="stylesheet" href="app/dist/dashboard/css/select2.min.css">
</head>
<body>
  <div class="container-scroller">

    <!-- partial:partials/_navbar.html -->

    <!-- Top Navbar -->
    <?php require_once 'includes/top-navbar.php'; ?>
    <!-- //Top Navbar -->

    <!-- partial -->

    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_sidebar.html -->
      
      <!-- Left Sidebar -->
      <?php require_once 'includes/left-side-bar.php'; ?>
      <!-- //Left Sidebar -->
      
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h4 class="font-weight-bold mb-0">Purchase Receipt</h4>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12 grid-margin">
              <div class="card">
                <div class="card-body">
                  <!-- alert -->
                  <?php require_once 'includes/alert.php'; ?>
                  <!-- //alert -->
                  
                  <form class="form-sample" action="./dashboard/purchase/confirm" method="POST" enctype="multipart/form-data">
                    <div class="row">
                      <div class="col-sm-12 text-center h3">
                        
                      </div>
                      <div class="col-sm-12 text-center">
                        <strong class="d-inline-block mt-2 float-left">
                          Receipt # <span class="text-muted">P<?php echo (isset($response->data->receipt->no) === true) ? $response->data->receipt->no:'N/A'; ?></span>
                        </strong>
                        <h3 class="d-inline-block text-center">
                          <strong class="d-inline-block mt-2">
                            <span class="text-muted"><?php echo (isset($response->data->store->name) === true) ? $response->data->store->name:'N/A'; ?></span>
                          </strong>
                        </h3>
                        <strong class="d-inline-block mt-2 float-right">
                          <span><?php echo (isset($response->data->receipt->date) === true) ? $response->data->receipt->date:'N/A'; ?></span>
                        </strong>
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
                            if(isset($response->data->receipt->orderLine) === true AND is_array($response->data->receipt->orderLine) === true AND count($response->data->receipt->orderLine) > 0){
                              $count = 0; foreach($response->data->receipt->orderLine as $item){ $count++;
                                ?>
                                  <tr>
                                    <td><?php echo ($count < 10) ? '0'.$count:$count; ?></td>
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
                        </tbody>
                      </table>
                    </div>
                    <div class="row">
                      <div class="col-sm-6">
                        <?php if(isset($response->data->receipt->notes) === true){ ?>
                          <p class="mt-2 p-3 rounded" style="border: 1px dashed #c9ccd7;">
                            <b class="d-inline-block mb-2">Additional Notes:</b><br/>
                            <?php echo (isset($response->data->receipt->notes) === true) ? nl2br($response->data->receipt->notes):null; ?>
                          </p>
                        <?php } ?>
                      </div>
                      <div class="col-sm-6">
                        <div class="table-responsive mt-3">
                          <table class="table table-hover">
                            <tbody>
                              <tr>
                                <td class="text-right"><strong>Gross Total</strong></td>
                                <td class="text-left" id="grossTotal"><?php echo (isset($response->data->receipt->bill->grossTotal) === true) ? Input::formatMoney($response->data->receipt->bill->grossTotal):'N/A'; ?></td>
                              </tr>
                              <?php if(isset($response->data->receipt->bill->discount) === true AND $response->data->receipt->bill->discount > 0){ ?>
                                <tr>
                                  <td class="text-right"><strong>Discount</strong></td>
                                  <td class="text-left" id="grossTotal"><?php echo (isset($response->data->receipt->bill->discount) === true) ? Input::formatMoney($response->data->receipt->bill->discount):'N/A'; ?></td>
                                </tr>
                              <?php } ?>
                              <tr>
                                <td class="text-right"><strong>Net Total</strong></td>
                                <td class="text-left" id="netTotal"><?php echo (isset($response->data->receipt->bill->netTotal) === true) ? Input::formatMoney($response->data->receipt->bill->netTotal):'N/A'; ?></td>
                              </tr>
                              <tr style="border-bottom: 1px solid #c9ccd7;">
                                <td class="text-right"><strong>Paid</strong></td>
                                <td class="text-left" id="grossTotal"><?php echo (isset($response->data->receipt->payment->first) === true) ? Input::formatMoney($response->data->receipt->payment->first):null; ?></td>
                              </tr>
                              <?php if(isset($response->data->receipt->bill->due) === true AND $response->data->receipt->bill->due > 0){ ?>
                                <tr style="border-bottom: 1px solid #c9ccd7;">
                                  <td class="text-right"><strong>Due</strong></td>
                                  <td class="text-left" id="grossTotal"><?php echo (isset($response->data->receipt->bill->due) === true) ? Input::formatMoney($response->data->receipt->bill->due):null; ?></td>
                                </tr>
                              <?php } ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        
        <!-- partial:partials/_footer.html -->
        <?php require_once 'includes/footer.php'; ?>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- plugins:js -->
  <script src="app/dist/dashboard/vendors/base/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page-->
  <script src="app/dist/dashboard/vendors/chart.js/Chart.min.js"></script>
  <!-- End plugin js for this page-->
  <!-- inject:js -->
  <script src="app/dist/dashboard/js/off-canvas.js"></script>
  <script src="app/dist/dashboard/js/hoverable-collapse.js"></script>
  <script src="app/dist/dashboard/js/template.js"></script>
  <script src="app/dist/dashboard/js/todolist.js"></script>
  <!-- endinject -->
  <!-- select2 -->
  <script src="app/dist/dashboard/js/select2.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('.js-example-basic-single').select2();
    });
  </script>
  <!-- Custom js for this page-->
  <script src="app/dist/dashboard/js/dashboard.js"></script>
  <!-- End custom js for this page-->
  <script type="text/javascript">
    function keyPress(e){
      var key;
      if(window.event){
        key = e.keyCode;
      } else if(e.which){
        key = e.which;
      }
      if(key === 13){
        addToCart();
      }
    }
    function removeCartItem(elem){
      elem.parentElement.parentElement.remove();
      updateCheckOutButton();
    }
    function updateCheckOutButton(){
      if(document.getElementById('cartItems').childElementCount > 0){
        var confirm = document.getElementById('confirmButton');
        checkOut.type = 'submit';
        checkOut.disabled = false;
      } else {
        document.getElementById('confirmButton').disabled = true;
      }
    }
    function updateTotal(elem){
      var netTotal    = document.getElementById('netTotal');
      var grossTotal  = document.getElementById('grossTotal');
      var newTotal    = grossTotal.innerHTML-elem.value;
      netTotal.innerHTML = newTotal;
      updatePaid(newTotal);
    }
    function updatePaid(amount){
      document.getElementById('paid').value = amount;
    }
  </script>
</body>

</html>