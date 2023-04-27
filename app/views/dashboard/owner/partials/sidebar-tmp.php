<!-- Cashbook -->
<li class="nav-item">
  <a data-bs-toggle="collapse" href="#menuCashBook" class="nav-link <?php echo (isset($section) === true and $section == 'CASH-BOOK') ? 'active' : null; ?>" aria-controls="menuDues" role="button" aria-expanded="false">
    <div class="
              icon icon-shape icon-sm
              shadow
              border-radius-md
              bg-white
              text-center
              d-flex
              align-items-center
              justify-content-center
              me-2
            ">
      <svg width="12px" height="12px" viewBox="0 0 40 44" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <title>document</title>
        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
          <g transform="translate(-1870.000000, -591.000000)" fill="#FFFFFF" fill-rule="nonzero">
            <g transform="translate(1716.000000, 291.000000)">
              <g transform="translate(154.000000, 300.000000)">
                <path class="color-background" d="M40,40 L36.3636364,40 L36.3636364,3.63636364 L5.45454545,3.63636364 L5.45454545,0 L38.1818182,0 C39.1854545,0 40,0.814545455 40,1.81818182 L40,40 Z" opacity="0.603585379"></path>
                <path class="color-background" d="M30.9090909,7.27272727 L1.81818182,7.27272727 C0.814545455,7.27272727 0,8.08727273 0,9.09090909 L0,41.8181818 C0,42.8218182 0.814545455,43.6363636 1.81818182,43.6363636 L30.9090909,43.6363636 C31.9127273,43.6363636 32.7272727,42.8218182 32.7272727,41.8181818 L32.7272727,9.09090909 C32.7272727,8.08727273 31.9127273,7.27272727 30.9090909,7.27272727 Z M18.1818182,34.5454545 L7.27272727,34.5454545 L7.27272727,30.9090909 L18.1818182,30.9090909 L18.1818182,34.5454545 Z M25.4545455,27.2727273 L7.27272727,27.2727273 L7.27272727,23.6363636 L25.4545455,23.6363636 L25.4545455,27.2727273 Z M25.4545455,20 L7.27272727,20 L7.27272727,16.3636364 L25.4545455,16.3636364 L25.4545455,20 Z"></path>
              </g>
            </g>
          </g>
        </g>
      </svg>
    </div>
    <span class="nav-link-text ms-1">Cash-Book</span>
  </a>
  <div class="collapse <?php echo (isset($section) === true and $section == 'CASH-BOOK') ? 'show' : null; ?>" id="menuCashBook">
    <ul class="nav ms-4 ps-3">
      <li class="nav-item">
        <a class="nav-link <?php echo (isset($section) === true and isset($subSection) === true and $section == 'CASH-BOOK' and $subSection == 'PURCHASES') ? 'active' : null; ?>" href="./dashboard/cash-book/purchases">
          <span class="sidenav-mini-icon"> S </span>
          <span class="sidenav-normal">
            Purchases <b class="caret"></b></span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo (isset($section) === true and isset($subSection) === true and $section == 'CASH-BOOK' and $subSection == 'SALES') ? 'active' : null; ?>" href="./dashboard/cash-book/sales">
          <span class="sidenav-mini-icon"> S </span>
          <span class="sidenav-normal">
            Sales <b class="caret"></b></span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo (isset($section) === true and isset($subSection) === true and $section == 'CASH-BOOK' and $subSection == 'ALL') ? 'active' : null; ?>" href="./dashboard/cash-book/all">
          <span class="sidenav-mini-icon"> S </span>
          <span class="sidenav-normal">
            All <b class="caret"></b></span>
        </a>
      </li>
    </ul>
  </div>
</li>