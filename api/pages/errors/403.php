<?php
namespace vsm\api\pages\errors;

require_once "../../lib/init.php";

use vsm\bl\SessionHandler\SessionHandler;



?>
<!doctype html5>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Access Denied</title>
    <?php include_once WEB_PATH.DS."assets/css.inc"; ?>
</head>
<body>


<div class="page-wrap d-flex flex-row align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center">
                <span class="display-4 d-block">Permission Denied</span>
                <span class="display-1 d-block">403</span>
                <div class="mb-4 lead">The page you are looking for is not allowed</div>
                <a href="/" class="btn btn-link">Back to Home</a>
            </div>
        </div>
    </div>
</div>


</body>


<?php include_once  WEB_PATH.DS."assets/js.inc"; ?>


</html>
