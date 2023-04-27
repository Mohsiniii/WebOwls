<?php
defined('VIEW') ? null : define('VIEW', ROOT_PATH . 'app' . DS . 'views' . DS . 'dashboard' . DS . 'owner');
defined('INCLUDES') ? null : define('INCLUDES', VIEW . DS . 'partials');
$panel = 'DASHBOARD';
$section = 'SALE';
$subSection = 'START';
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
  <style>
    .link-icon-active {
      color: white !important;
    }
  </style>
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
              <!-- <h4 class="card-title">Horizontal Two column</h4> -->
              <form class="form-sample" id="shopingForm" action="./dashboard/sale" method="POST" enctype="multipart/form-data">
                <!-- <p class="card-description">Item Details</p> -->
                <div class="row mt-3">
                  <div class="col-md-12 col-lg-6">
                    <div class="form-group row px-3">
                      <label class="col-sm-12 col-form-label">Choose Item</label>
                      <div class="col-sm-12">
                        <select class="form-control js-example-basic-single w-100" id="saleItem" onchange="updatePrice(this)" required>
                          <?php
                          foreach ($response->data->items as $sItem) {
                            foreach ($sItem->variants as $siv) {
                          ?>
                              <option value="<?php echo $siv->id; ?>">
                                <?php echo $sItem->item->name; ?>
                                <?php echo ", {$sItem->brand->name}" ?>
                                <?php
                                echo ", (";
                                $counter = 0;
                                foreach ($siv->options as $vOpt) {
                                  $counter++;
                                  echo $vOpt->option . ': ' . $vOpt->value . ' ' . $vOpt->unit;
                                  if ($counter < count($siv->options)) {
                                    echo ', ';
                                  }
                                }
                                echo ")";
                                ?>
                              </option>
                          <?php
                            }
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-lg-3">
                    <div class="form-group row">
                      <label class="col-sm-12 col-form-label" title="sale Quantity">Quantity <small>(in units)</small></small></label>
                      <div class="col-sm-12">
                        <input type="number" step="0.01" class="form-control" id="saleQuantity" value="1" onkeypress="keyPress(event)" min="0" required autocomplete="off" />
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-lg-3">
                    <div class="form-group row">
                      <label class="col-sm-12 col-form-label" title="Sale Price">Sale Price <small>/ per item</small></label>
                      <div class="col-sm-12">
                        <input type="number" step="0.01" class="form-control" id="salePrice" onkeypress="keyPress(event)" min="0.1" required autocomplete="off" />
                      </div>
                    </div>
                  </div>
                </div>
              </form>
              <form class="form-sample px-3" action="./dashboard/sale/checkout" method="POST" enctype="multipart/form-data">
                <div class="row">
                  <div class="col-sm-12">
                    <button class="btn btn-sm btn-outline-primary float-start" type="button" role="button" onclick="addToCart()">Add To Cart</button>
                    <button class="btn btn-success float-end" type="button" role="button" id="checkOutButton" name="checkOutSale" value="true" disabled>Check Out</button>
                  </div>
                </div>
                <div class="table-responsive mt-3">
                  <table class="table table-hover">
                    <thead>
                      <th>Item</th>
                      <th>Quantity</th>
                      <th>Price</th>
                      <th>Total</th>
                      <th>Action</th>
                    </thead>
                    <tbody id="cartItems"></tbody>
                  </table>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <?php require_once 'partials/footer.php'; ?>
      <!-- //Footer  -->

    </div>
  </main>

  <!-- Settings -->
  <?php require_once 'partials/settings.php'; ?>
  <!-- //Settings -->

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
    window.onload = function() {
      updatePrice(document.getElementById('saleItem'));
    };

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

    function isJson(obj) {
      try {
        var jsonObj = JSON.parse(obj);
        return true;
      } catch (err) {
        return false;
      }
    }
    var cartItems = [];

    function addToCart() {
      // grab item data
      var x = document.getElementById('saleItem');
      var saleItem = x.options[x.selectedIndex];
      var saleQuantity = document.getElementById('saleQuantity');
      var salePrice = document.getElementById('salePrice');

      // check if they are empty
      if (saleItem.value.length > 0 && saleQuantity.value > 0 && salePrice.value > 0) {
        var cartItemDetails = {
          saleItem: saleItem.text,
          saleItemID: saleItem.value,
          saleQuantity: saleQuantity.value,
          salePrice: salePrice.value
        };
        // create tr
        var tr = document.createElement('tr');
        tr.classList.add('text-center');
        // create td for item
        var tdItem = document.createElement('td');
        tdItem.classList.add('text-start');
        var inputItem = document.createElement('input');
        inputItem.type = 'hidden';
        inputItem.name = 'saleItem[]';
        inputItem.value = saleItem.text;
        var inputItemID = document.createElement('input');
        inputItemID.type = 'hidden';
        inputItemID.name = 'saleItemID[]';
        inputItemID.value = saleItem.value;
        tdItem.appendChild(inputItem);
        tdItem.appendChild(inputItemID);
        tdItem.appendChild(document.createTextNode(saleItem.text));
        // create td for quantity
        var tdQuantity = document.createElement('td');
        var inputQuantity = document.createElement('input');
        inputQuantity.type = 'number';
        inputQuantity.name = 'saleQuantity[]';
        inputQuantity.min = '1';
        inputQuantity.className = 'form-control';
        inputQuantity.value = saleQuantity.value;
        inputQuantity.setAttribute('onkeypress', 'updateCartItem(this)');
        inputQuantity.setAttribute('onkeyup', 'updateCartItem(this)');
        inputQuantity.setAttribute('onchange', 'updateCartItem(this)');
        tdQuantity.appendChild(inputQuantity);
        // create td for price
        var tdPrice = document.createElement('td');
        var inputPrice = document.createElement('input');
        inputPrice.type = 'number';
        inputPrice.name = 'salePrice[]';
        inputPrice.min = '1';
        inputPrice.className = 'form-control';
        inputPrice.value = salePrice.value;
        inputPrice.setAttribute('onkeypress', 'updateCartItem(this)');
        inputPrice.setAttribute('onkeyup', 'updateCartItem(this)');
        inputPrice.setAttribute('onchange', 'updateCartItem(this)');
        tdPrice.appendChild(inputPrice);
        // create td for price
        var tdTotalPrice = document.createElement('td');
        tdTotalPrice.className = 'totalPrice';
        tdTotalPrice.appendChild(document.createTextNode(parseFloat(saleQuantity.value) * parseFloat(salePrice.value)));
        // create td for remove
        var tdRemove = document.createElement('td');
        var removeSpan = document.createElement('span');
        removeSpan.className = 'border px-3 py-2 btn btn-link';
        removeSpan.setAttribute("onclick", "removeCartItem(this)");
        removeSpan.appendChild(document.createTextNode('X'));
        tdRemove.appendChild(removeSpan);
        // create hidden input
        var hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'saleItems[]'
        hiddenInput.value = JSON.stringify(cartItemDetails);
        // append all tds to tr
        tr.appendChild(tdItem);
        tr.appendChild(tdQuantity);
        tr.appendChild(tdPrice);
        tr.appendChild(tdTotalPrice);
        tr.appendChild(tdRemove);
        tr.appendChild(hiddenInput);
        // append tr to cart table
        document.getElementById('cartItems').appendChild(tr);
        document.getElementById('shopingForm').reset();
        updateCheckOutButton();
        x.focus();
        updatePrice(document.getElementById('saleItem'));
      } else {
        if (saleItem.value.length <= 0) {
          alert('Please choose an item.');
        } else if (saleQuantity.value <= 0) {
          alert('Please enter quantity.');
          document.getElementById('saleQuantity').focus();
        } else if (salePrice.value <= 0) {
          alert('Please enter price.');
          document.getElementById('salePrice').focus();
        }
      }
    }

    function removeCartItem(elem) {
      elem.parentElement.parentElement.remove();
      updateCheckOutButton();
    }

    function updateCheckOutButton() {
      if (document.getElementById('cartItems').childElementCount > 0) {
        var checkOut = document.getElementById('checkOutButton');
        checkOut.type = 'submit';
        checkOut.disabled = false;
      } else {
        document.getElementById('checkOutButton').disabled = true;
      }
    }

    function updateCartItem(elem) {
      var tr = elem.parentElement.parentElement;
      var quantity = tr.querySelector('[name="saleQuantity[]"]').value;
      var price = tr.querySelector('[name="salePrice[]"]').value;
      var tdTotalPrice = tr.querySelector('.totalPrice');
      tdTotalPrice.innerHTML = parseFloat(quantity) * parseFloat(price);
    }

    function updatePrice(select) {
      var formdata = new FormData();
      formdata.append("sivID", select.value);
      var ajax = new XMLHttpRequest();
      ajax.upload.addEventListener("progress", function(event) {
        // progress function
      }, false);
      ajax.addEventListener("load", function() {
        // completeHandler
        if (isJson(event.target.responseText) === true) {
          var jsonObj = JSON.parse(event.target.responseText);
          var price = jsonObj.data;
          document.getElementById('salePrice').value = price.discounted;
        }
      }, false);
      ajax.addEventListener("error", function() {
        // errorHandler
        alert('Failed to fetch price.');
      }, false);
      ajax.addEventListener("abort", function() {
        // abortHandler
        alert('Failed to fetch price.');
      }, false);
      ajax.open("POST", "./api/fetch-siv-price", true);
      ajax.send(formdata);
    }
  </script>
</body>

</html>