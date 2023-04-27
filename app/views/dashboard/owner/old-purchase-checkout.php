<?php
	defined('VIEW') ? null : define('VIEW', ROOT_PATH.'app'.DS.'views'.DS.'dashboard'.DS.'owner');
	defined('INCLUDES') ? null : define('INCLUDES', VIEW.DS.'includes');
	$panel = 'dashboard';
	$section = 'purchase';
  $subSection = 'check-out';
  if(isset($response->data->cart) === false OR is_null($response->data->cart) === true){
    Redirect::to('./dashboard/purchase/');
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Check Out - Dashboard | Raseed - An Intelligent Multi-Vendor Solution</title>

  <base href="<?php echo HTML_BASE_PATH; ?>" />

  <!-- plugins:css -->
  <link rel="stylesheet" href="app/dist/dashboard/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="app/dist/dashboard/vendors/base/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="app/dist/dashboard/css/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="app/assets/dashboard/images/favicon.png" />

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
                  <h4 class="font-weight-bold mb-0">Purchase Stock</h4>
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
                      <div class="col-sm-12">
                        <strong class="d-inline-block mt-2">
                          <span><?php echo date('Y-m-d'); ?></span>
                          <span class="text-muted"><?php echo date('H:i:s'); ?></span>
                        </strong>
                        <button class="btn btn-success float-right" type="submit" role="button" id="confirmButton" name="confirmPurchase" value="true">Confirm Purchase</button>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group row">
                          <label class="col-sm-12 col-form-label">Receipt No</label>
                          <div class="col-sm-12">
                            <input type="number" class="form-control" name="purchaseRaseedNo" value="<?php if(Input::postExists('purchaseRaseedNo') === true){ echo Input::getPost('purchaseRaseedNo'); } else { echo (isset($response->data->lastReceiptNo) === true AND $response->data->lastReceiptNo !== false) ? ($response->data->lastReceiptNo+1):null; } ?>" placeholder="e.g. 56852" required minlength="1" maxlength="32" autocomplete="off" />
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
                                if(isset($response->data->suppliers) === true AND is_array($response->data->suppliers) === true AND count($response->data->suppliers) > 0){
                                  foreach($response->data->suppliers as $supplier){
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
                            if(isset($response->data->cart->items) === true AND is_array($response->data->cart->items) === true AND count($response->data->cart->items) > 0){
                              $count = 0; foreach($response->data->cart->items as $cItem){ $count++;
                                ?>
                                  <tr>
                                    <td><?php echo ($count < 10) ? '0'.$count:$count; ?></td>
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
                            <textarea class="form-control" name="purchaseNote" rows="5" autocomplete="off" placeholder="Add purchase notes..."></textarea>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="table-responsive mt-3">
                          <table class="table table-hover">
                            <tbody>
                              <tr>
                                <td class="text-right"><strong>Gross Total</strong></td>
                                <td class="text-center" id="grossTotal"><?php echo $response->data->cart->bill->netTotal; ?></td>
                              </tr>
                              <tr>
                                <td class="text-right"><strong>Discount</strong></td>
                                <td class="text-center"><input type="number" step="0.01" name="purchaseDiscount" class="border shadow-sm px-2 py-1" value="<?php echo (Input::postExists('purchaseDiscount') === true) ? Input::getPost('purchaseDiscount'):'0'; ?>" min="0" max="<?php echo $response->data->cart->bill->netTotal; ?>" onload="updateTotal(this)" onkeypress="updateTotal(this)" onkeyup="updateTotal(this)" onchange="updateTotal(this)" /></td>
                              </tr>
                              <tr>
                                <td class="text-right"><strong>Net Total</strong></td>
                                <td class="text-center" id="netTotal"><?php echo $response->data->cart->bill->netTotal; ?></td>
                              </tr>
                              <tr>
                                <td class="text-right"><strong>Paid</strong></td>
                                <td class="text-center"><input type="number" step="0.01" id="paid" name="purchasePaid" class="border shadow-sm px-2 py-1" value="<?php echo (Input::postExists('purchasePaid') === true) ? Input::getPost('purchasePaid'):$response->data->cart->bill->netTotal; ?>" min="0" max="<?php echo $response->data->cart->bill->netTotal; ?>" /></td>
                              </tr>
                              <tr>
                                <td colspan="2" class="text-center"><button class="btn btn-success form-control" type="submit" role="button" id="confirmButton" name="confirmPurchase" value="true">Confirm Purchase</button></td>
                              </tr>
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