<?php
namespace vsm\api;

require_once "lib/init.php";

use vsm\bl\SessionHandler\SessionHandler;
header("Location: ".PAGES_PATH.DS."cloud/");
exit(0);
$menu_active="dashboard";
?>
<!doctype html5>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Home</title>
        <?php include_once WEB_PATH.DS."assets/css.inc"; ?>
    </head>

    <body>

    <?php include_once WEB_PATH.DS."parts/header.inc";?>

    <div class="container-fluid">
        <div class="row">
            <?php include_once WEB_PATH.DS."parts/sidebar.inc";?>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>



                </div>




            </main>
        </div>
    </div>


    </body>


    <?php include_once  WEB_PATH.DS."assets/js.inc"; ?>


</html>
