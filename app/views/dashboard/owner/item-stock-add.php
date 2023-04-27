<?php
	defined('VIEW') ? null : define('VIEW', ROOT_PATH.'app'.DS.'views'.DS.'dashboard'.DS.'owner');
	defined('INCLUDES') ? null : define('INCLUDES', VIEW.DS.'includes');
	$panel = 'dashboard';
	$section = 'items';
  $subSection = 'add';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Add Variants Stock - Dashboard | Raseed - An Intelligent Multi-Vendor Solution</title>

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
                  <h4 class="font-weight-bold mb-0">Add Variants Stock</h4>
                </div>
                <div>
                  <a href="./dashboard/item/view/all/" type="button" class="btn btn-primary btn-icon-text btn-rounded">
                    <i class="ti-palette menu-icon btn-icon-prepend"></i>Items
                  </a>
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

                  <!-- item head -->
                  <div class="col-sm-12 mt-4">
                    <h4>
                      <?php echo (isset($response->data->item->item->name) === true) ? $response->data->item->item->name:null; ?>
                    </h4>
                    <span class="text-muted">
                      <i class="fa fa-sm fa-tags"></i>&nbsp;
                      <?php echo (isset($response->data->item->category->name) === true) ? $response->data->item->category->name:null; ?>
                    </span>
                  </div>
                  <!-- //item head -->

                  <!-- <h4 class="card-title">Horizontal Two column</h4> -->
                  <form class="form-sample" action="./dashboard/item/add-stock?item=<?php echo (isset($response->data->item->id) === true) ? $response->data->item->id:null; ?>" method="POST" enctype="multipart/form-data">
                    <!-- <p class="card-description">Item Details</p> -->
                    <div class="row mt-3">
                      <?php if(isset($response->data->variants) === true AND is_array($response->data->variants) === true AND count($response->data->variants) > 0){ ?>
                        <?php foreach($response->data->variants as $var){ ?>
                          <div class="col-12 mb-2">
                            <h5 class="text-center"><strong>Variant</strong></h5>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group row">
                              <?php foreach($var->options as $vop){ ?>
                                <div class="col-sm-4 col-lg-3">
                                  <strong><?php echo $vop->option; ?></strong><br/>
                                  <p><?php echo $vop->value; ?></p>
                                </div>
                              <?php } ?>
                              <div class="col-md-4">
                                <div class="form-group row">
                                  <label class="col-sm-5 col-form-label font-weight-bold">Quantity</label>
                                  <div class="col-sm-7">
                                    <input type="number" step="1" class="form-control" name="sivStock<?php echo $var->id; ?>" min="0" required autocomplete="off" />
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6 col-lg-4">
                                <div class="form-group row">
                                  <label class="col-sm-5 col-form-label" title="Sale Price">
                                    <strong>Purchase Price</strong>
                                    <small>/ per item</small>
                                  </label>
                                  <div class="col-sm-7">
                                    <input type="number" step="0.01" class="form-control" name="sivPrice<?php echo $var->id; ?>" min="0.1" required autocomplete="off" />
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        <?php } ?>
                      <?php } else { ?>
                        <div class="col-md-12">
                          You have not added any variant yet.
                          <a href="./dashboard/item/add-variants?item=<?php echo $response->data->item ?>">Add Variants</a>
                        </div>
                      <?php } ?>
                    </div>
                    <div class="row">
                      <div class="col-sm-12">
                        <button class="btn btn-success float-right" name="addItemVariantStock" value="true">Finish</button>
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
  <!-- Custom js for this page-->
  <script src="app/dist/dashboard/js/dashboard.js"></script>
  <!-- End custom js for this page-->
</body>

</html>