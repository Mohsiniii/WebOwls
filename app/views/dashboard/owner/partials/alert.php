<!-- alert -->
  <?php if(Session::exists('successMessage') === true AND is_null(Session::get('successMessage')) === false AND empty(Session::get('successMessage')) === false){ ?>
    <div class="m-l-25 m-r--38 m-lr-0-xl">
      <div class="row">
        <div class="col-lg-12">
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="row">
              <div class="col-sm-10 col-md-11">
                <strong>Alert:</strong>
                <span class="text-white"><?php echo Session::flash('successMessage'); ?></span>
              </div>
              <div class="col-sm-2 col-md-1 float-end">
                <button type="button" class="close btn btn-sm px-3 py-1 bg-white text-success float-end" data-bs-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true" style="font-size: 23px;">&times;</span>
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5"></div>
      </div>
    </div>
  <?php } elseif(isset($response->message->error) === true AND is_null($response->message->error) === false AND ( (is_array($response->message->error) === true AND count($response->message->error) > 0) OR (is_string($response->message->error) === true AND strlen($response->message->error) > 0))){ ?>
    <div class="m-l-25 m-r--38 m-lr-0-xl">
      <div class="row">
        <div class="col-lg-12 pr-0">
          <div class="alert alert-danger alert-dismissible fade show text-white" role="alert">
            <div class="row">
              <div class="col-sm-10 col-md-11">
                <strong>Alert:</strong>
                <?php
                  if(is_array($response->message->error) === true){
                    if(count($response->message->error) > 0){
                      foreach($response->message->error as $e){
                        echo $e ."<br/>";
                      }
                    }
                  } else {
                    echo $response->message->error;
                  }
                ?>
              </div>
              <div class="col-sm-2 col-md-1 float-end">
                <button type="button" class="close btn btn-sm px-3 py-1 bg-white text-danger float-end" data-bs-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true" style="font-size: 23px;">&times;</span>
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5"></div>
      </div>
    </div>
  <?php } ?>
  <!-- //alert -->