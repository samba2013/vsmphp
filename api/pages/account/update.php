<?php
namespace vsm\api\pages\account;
require_once "../../lib/init.php";

use vsm\bl\Account;
use vsm\bl\SessionHandler\SessionHandler;
use vsm\dal\AccountRepository;
use vsm\dal\AccountTypeRepository;
$menu_active="account";
if(!SessionHandler::getInstance()->isAppAllowed($menu_active)){
    header("Location: ../errors/403.php");
    exit(0);
}


if(isset($_GET['id']))
    $id = intval($_GET['id']);
elseif (isset($_POST['id']))
    $id = intval($_POST['id']);
else
    $id = 0;


$AccountRepository = AccountRepository::getInstance();
$OldItem = $AccountRepository->Find($id);
$errMsg=null;
if($OldItem !=NULL) {
    $TypeRepository = AccountTypeRepository::getInstance();

    if (!empty($_POST) && isset($_POST['name'])
        && isset($_POST['account_type_id'])
        && isset($_POST['id'])
        && isset($_POST['token']) && $OldItem->id == intval($_POST['id'])) {

        $name = addslashes($_POST['name']);
        $account_type_id = intval($_POST['account_type_id']);
        $status = intval($_POST['is_active']);
        $token = addslashes($_POST['token']);

        $value = new \DateTime("now");
        $value->setTimezone(new \DateTimeZone("UTC"));

        $userid= $_SESSION['user']->id;
        $OldItem->name = trim($name);
        $OldItem->account_type_id = $account_type_id;
        $OldItem->token = $token;
        $OldItem->is_active = $status;
        $OldItem->modified_at = $value->format("Y-m-d H:i:s");;
        $OldItem->modified_by = $userid;

        try {
            if ($AccountRepository->Update($OldItem)) {
                header("Location: index.php");
            }
        } catch (\PDOException $e) {
            unset($_POST);
            $errMsg = $e->getMessage();
        }

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

                <h1 class="h2">Update Account</h1>
                <span class="float-right">
                    <a href="index.php" class="btn btn-primary btn-sm">Back to list <i class="fa fa-list"></i></a>
                </span>
            </div>
            <?php if($OldItem==NULL){  ?>
                <div class="row">
                    <div class="alert alert-danger">
                        <b>Oops !</b> Account not found
                    </div>
                </div>
            <?php } else {?>
            <div class="row">
                <?php if($errMsg) {
                    ?>
                    <div class="alert alert-danger">
                        <b>Error !</b> <?php echo $errMsg ?>
                    </div>
                    <?php
                }?>

                <div class="col-md-10">
                    <form action="update.php" method="post" id="form-update" onsubmit="return validate();">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="name">Name</label>
                                <input type="text" name="name" value="<?php echo $OldItem->name ?>" placeholder="Account Name" class="form-control" id="name" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="account_type_id">Type</label>
                                <select name="account_type_id" class="form-control" id="account_type_id" required>
                                    <option value="" >Select Type</option>
                                    <?php
                                    foreach ($TypeRepository->OnlyActive() as $accountType){
                                        ?>
                                        <option <?php echo $OldItem->account_type_id == $accountType->id ? 'selected':'' ?> value="<?php echo $accountType->id ?>"><?php  echo $accountType->name ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="token">Api Token</label>
                                <input type="text" class="form-control"  name="token" value="<?php echo $OldItem->token ?>" id="token" placeholder="Eg. AXXXXXXXXXXXXXXXXX" required>
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="is_active">Status</label>
                                <select name="is_active" id="is_active" class="form-control">
                                    <option <?php echo $OldItem->is_active == 1?'selected':'' ?> value="1">Active</option>
                                    <option <?php echo $OldItem->is_active == 0?'selected':'' ?> value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $OldItem->id ?>">
                        <button type="submit" class="btn btn-primary">Save Changes <i class="fa fa-save"></i></button>


                    </form>
                </div>

            </div>
            <?php } ?>
        </main>
    </div>
</div>


</body>


<?php include_once WEB_PATH.DS."assets/js.inc"; ?>
<?php include_once "includes/js.inc"; ?>


</html>