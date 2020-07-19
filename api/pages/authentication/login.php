<?php
namespace vsm\api\pages\authentication;

require_once "../../lib/webconfig.php";

use vsm\api\lib\Database;
use vsm\bl\Authentication;
use vsm\dal\UserRepository;

$auth=new Authentication(UserRepository::getInstance());
if(isset($_POST['username']) && isset($_POST['password'])){
    $username = addslashes($_POST['username']);
    $password = addslashes($_POST['password']);
    if($auth->Login($username,$password)){
        header("Location: /");
    }
}elseif(isset($_SESSION['user'])){
    header("Location: /");
    die(0);
}



?>
<!doctype html5>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <?php include_once WEB_PATH.DS."assets/css.inc"; ?>
</head>

<body>



<div class="align-content-md-center">

   <div class="row">

        <div class="col-md-4"></div>
        <div class="col-md-4">
            <article class="card-body">
                <h4 class="card-title text-center mb-4 mt-1">Sign in</h4>
                <hr>
                <?php if($auth->err!=null) {?>
                <p class="text-danger text-center"><?php echo $auth->err; ?></p>
              <?php }  ?>
                <form action="login.php" method="post">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                            </div>
                            <input name="username" class="form-control" placeholder="username" type="text">
                        </div> <!-- input-group.// -->
                    </div> <!-- form-group// -->
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                            </div>
                            <input class="form-control" placeholder="******" name="password" type="password">
                        </div> <!-- input-group.// -->
                    </div> <!-- form-group// -->
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block"> Login  </button>
                    </div> <!-- form-group// -->

                </form>
            </article>
        </div> <!-- card.// -->

    </div>

</div>


</body>


<?php include_once  WEB_PATH.DS."assets/js.inc"; ?>


</html>
