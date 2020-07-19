<?php
namespace vsm\api\pages\user;
require_once "../../lib/init.php";

use vsm\bl\SessionHandler\SessionHandler;
use vsm\bl\User;
use vsm\dal\UserRepository;


$menu_active="user";
if(!SessionHandler::getInstance()->isAppAllowed($menu_active)){
    header("Location: ../errors/403.php");
    exit(0);
}


$Repo = UserRepository::getInstance();
$errMsg=null;


if(!empty($_POST)
    && isset($_POST['role_id'])
    && isset($_POST['username'])
    && isset($_POST['password'])
    && isset($_POST['firstname'])
    && isset($_POST['is_active'])
    && isset($_POST['lastname'])){

    $params = [
      'role_id' => intval($_POST['role_id']),
      'is_active' => intval($_POST['is_active']),
      'created_by' => $_SESSION['user']->id,
      'username' => addslashes($_POST['username']),
      'password' => sha1($_POST['password']),
      'firstname' => addslashes($_POST['firstname']),
      'lastname' => addslashes($_POST['lastname']),
    ];
    $mixed = new User();
    $mixed->Fill($params);

    try {
        if($Repo->Add($mixed)){
            header("Location: index.php");
        }
    }catch (\PDOException $e){
        $errMsg=$e->getMessage();
    }

}




?>
<!doctype html5>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User Management</title>
    <?php include_once WEB_PATH.DS."assets/css.inc"; ?>
</head>

<body>

<?php include_once WEB_PATH.DS."parts/header.inc";?>

<div class="container-fluid">
    <div class="row">
        <?php include_once WEB_PATH.DS."parts/sidebar.inc";?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">

            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

                <h1 class="h2">New User</h1>
                <span class="float-right">
                    <a href="index.php" class="btn btn-primary btn-sm">Back to list <i class="fa fa-list"></i></a>
                </span>
            </div>

            <?php if (isset($errMsg) && $errMsg!=null){ ?>

                <div class="alert alert-danger">
                    <b>Oups !</b> <?php echo $errMsg ?>
                </div>
            <?php } ?>


            <div class="row">

                <div class="col-md-10">
                    <form action="create.php" method="post" class="needs-validation" novalidate>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="firstname">First Name</label>
                                <input type="text" name="firstname" class="form-control" id="firstname" placeholder="eg. John">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="lastname">Last Name</label>
                                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="eg. Doe">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="role_id">Role</label>
                                <select name="role_id" id="role_id" class="form-control">
                                    <?php foreach (USER_TYPES as $id=>$name){
                                       ?>

                                        <option value="<?php echo $id ?>"><?php echo $name ?></option>

                                   <?php }?>
                                </select>
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="username">Username</label>
                                <input type="text" name="username" class="form-control" id="username" placeholder="Username for login" required>
                                <div class="invalid-feedback">
                                    Username is required
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Set Password" required>
                                <div class="invalid-feedback">
                                    Password is required
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="confirmPassword">Confirm</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
                                <div class="invalid-feedback">
                                    Password do not match
                                </div>
                            </div>

                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="is_active">Status</label>
                                <select name="is_active" id="is_active" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Register <i class="fa fa-save"></i></button>
                    </form>
                </div>

            </div>
        </main>
    </div>
</div>


</body>


<?php include_once WEB_PATH.DS."assets/js.inc"; ?>


</html>