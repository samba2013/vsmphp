<?php
namespace vsm\api\pages\account;
require_once "../../lib/init.php";

use vsm\api\lib\Database;
use vsm\bl\Account;
use vsm\bl\AccountType;
use vsm\bl\SessionHandler\SessionHandler;
use vsm\dal\AccountRepository;
use vsm\dal\AccountTypeRepository;
use vsm\dal\UserRepository;

$menu_active="account";
if(!SessionHandler::getInstance()->isAppAllowed($menu_active)){
    header("Location: ../errors/403.php");
    exit(0);
}

if(isset($_GET['id']))
    $id = intval($_GET['id']);
else
    $id = 0;


$AccountRepository = AccountRepository::getInstance();
$Account = $AccountRepository->Find($id);
$AccountType = null;
$errMsg=null;
if($Account !=NULL) {
    $TypeRepository = AccountTypeRepository::getInstance();
    $AccountType = $TypeRepository->Find($Account->account_type_id);
}




?>
<!doctype html5>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Account Management</title>
    <?php include_once WEB_PATH.DS."assets/css.inc"; ?>
</head>

<body>

<?php include_once WEB_PATH.DS."parts/header.inc";?>

<div class="container-fluid">
    <div class="row">
        <?php include_once WEB_PATH.DS."parts/sidebar.inc";?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">

            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

                <h1 class="h2">View Account</h1>
                <span class="float-right">
                    <div class="dropdown">
                          <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Account Settings
                          </a>

                          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="create.php">New Account <i class="fa fa-plus"></i></a>
                            <a class="dropdown-item" href="type/index.php">Manage Types <i class="fa fa-cog"></i></a>
                          </div>
                        </div>
                </span>
            </div>

            <?php
            if($Account !== NULL){
            ?>

            <div class="card">
                <div class="card-header">
                    Account #<?php echo $Account->id?>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $Account->name?></h5>
                    <div class="row">
                        <div class="col-md-3">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td class="float-right">Type : </td>
                                        <td class="font-weight-bold"><?php echo $AccountType->name?></td>
                                    </tr>
                                    <tr>
                                        <td class="float-right">Token : </td>
                                        <td><code><?php echo $Account->token?></code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <a href="index.php" class="btn btn-primary">Go Back</a>
                </div>
            </div>
            <?php }else{ ?>

            <?php } ?>


        </main>
    </div>
</div>


</body>


<?php include_once WEB_PATH.DS."assets/js.inc"; ?>


</html>