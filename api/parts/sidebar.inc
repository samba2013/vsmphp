<?php
namespace vsm\api\parts;
use vsm\bl\SessionHandler\SessionHandler;

?>

<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="sidebar-sticky pt-3">
        <ul class="nav flex-column">
            <!--<li class="nav-item">
                <a class="nav-link <?php /*echo (isset($menu_active) && $menu_active=='dashboard')?'active':''; */?> " href="/">
                    <i class="fa fa-home"></i> Dashboard <span class="sr-only">(current)</span>
                </a>
            </li>-->
            <?php if(SessionHandler::getInstance()->isAppAllowed('cloud')) { ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo (isset($menu_active) && $menu_active=='cloud')?'active':''; ?> " href="<?php echo APP_HOST.DS?>pages/cloud/">
                        <i class="fa fa-server"></i>
                        Clouds
                    </a>
                </li>
            <?php } ?>
            <?php if(SessionHandler::getInstance()->isAppAllowed('account')) { ?>
                <li class="nav-item ">
                    <a class="nav-link <?php echo (isset($menu_active) && $menu_active=='account')?'active':''; ?> " href="<?php echo APP_HOST.DS?>pages/account/">
                        <i class="fa fa-cloud"></i>
                        Accounts
                    </a>
                </li>
            <?php } ?>

            <?php if(SessionHandler::getInstance()->isAppAllowed('user')) { ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo (isset($menu_active) && $menu_active=='user')?'active':''; ?> " href="<?php echo APP_HOST.DS?>pages/user/">
                        <i class="fa fa-users"></i>
                        Users
                    </a>
                </li>
            <?php } ?>

        </ul>
    </div>
</nav>
