<?php
namespace vsm\api\parts;
use vsm\bl\SessionHandler\SessionHandler;

?>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="#">VSM 1.0</a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <ul class="navbar-nav ml-md-auto">
        <li class="nav-item dropdown">
            <a class="nav-item nav-link dropdown-toggle mr-md-2" href="#" id="bd-versions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user"></i> Welcome, <?php echo SessionHandler::getInstance()->getUser()->username; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-md-right">
                <a class="dropdown-item" href="<?php echo PAGES_PATH.DS ?>authentication/logout.php"><i class="fa fa-sign-out-alt"></i> Sign out</a>

            </div>
        </li>
    </ul>
</nav>