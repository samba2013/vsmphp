<?php
namespace vsm\api\pages\account;
require_once "../../lib/init.php";

use vsm\bl\SessionHandler\SessionHandler;
use vsm\dal\AccountRepository;
use vsm\dal\AccountTypeRepository;
use vsm\dal\UserRepository;

$menu_active="account";
if(!SessionHandler::getInstance()->isAppAllowed($menu_active)){
    header("Location: ../errors/403.php");
    exit(0);
}


$page= (isset($_GET['page']))?intval($_GET['page']):0;
$query=(isset($_GET['q']) && !empty($_GET['q']))?addslashes($_GET['q']):'';

$limit=10;
$columns = ['name'];

$Repo = AccountRepository::getInstance();
$RowsTotal = $Repo->TotalRows($columns,$query);
$pages = 0;

if($RowsTotal > 0) {
    $pages = ceil((float)$RowsTotal / $limit);
    $page = ($page <= 0) ? 1 : $page;
    $page = ($page > $pages) ? $pages : $page;
}
$queryLink="";
if(!empty($query)){
    $queryLink="&q=".$query;
}


$accounts=$Repo->SearchAndPaginate($columns,$query,$page,$limit);

$errMsg = null;
if(isset($_POST['_submitDelete']) && ($_POST['id']) && $_POST['action'] == 'delete'){

    $accountId = intval($_POST['id']);
    try {
        if($Repo->Remove($accountId)){
            header("Location: index.php");
        }
    }
    catch(\PDOException $e){
        $errMsg = $e->getMessage();
    }

}
$AppConfig=parse_ini_file(WEB_PATH.DS.'lib'.DS.'app.ini');
$defaultPassword=isset($AppConfig['cloud_default_password'])?$AppConfig['cloud_default_password']:"";
$jsResponse="";
if(isset($_POST['default_password']) && strlen($_POST['default_password'])>8){
    $defaultPassword=filter_input(INPUT_POST,"default_password",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if(file_put_contents(WEB_PATH.DS.'lib'.DS.'app.ini',"cloud_default_password=".$defaultPassword)){
        $jsResponse=<<<EOL
<script>
$(function() {
  alert("Succesfully password changed !");
})
</script>
EOL;

    }else{
        $jsResponse=<<<EOL
<script>
$(function() {
  alert("Password not changed !");
})
</script>
EOL;
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

                <h1 class="h2">Accounts</h1>

                <span class="float-right mr-6">

                    <div class="dropdown dropleft">
                          <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Account Settings
                          </a>

                          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="create.php">New Account <i class="fa fa-plus"></i></a>
                            <a class="dropdown-item" href="type/index.php">Manage Types <i class="fa fa-cog"></i></a>
                            <a class="dropdown-item" href="javascript:void(0);" data-toggle="modal" data-target="#changePassword">Change Default password <i class="fa fa-key"></i></a>
                          </div>
                        </div>
                </span>
            </div>
            <div class="row">
                <?php if($errMsg) {
                    ?>
                    <div class="alert alert-danger">
                        <b>Error !</b> <?php echo $errMsg ?>
                    </div>
                    <?php
                }?>
            </div>


            <div class="row">
                <div class="col-md-4 offset-8">
                    <form action="" method="get">
                          <div class="input-group">

                            <input type="text" value="<?php echo $query;?>" name="q" class="form-control" placeholder="search for value" aria-label="Text input with segmented dropdown button">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-outline-secondary">Search <i class="fa fa-search"></i></button>
                            </div>

                        </div>
                    </form>
                </div>

            </div>

            <div class="table-responsive-sm">

                <table class="table table-bordered">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Active</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th>Modified By</th>
                        <th>Modified At</th>
                        <th>Actions</th>
                    </tr>
                    <tbody>

                    <?php
                        if($RowsTotal>0){
                            foreach ($accounts as $account){
                                $type = AccountTypeRepository::getInstance()->Find($account->account_type_id);
                                $created_by = UserRepository::getInstance()->Find($account->created_by);
                                $modified_by = UserRepository::getInstance()->Find($account->modified_by);


                                ?>
                                <tr>
                                    <td><?php echo $account->id ?></td>
                                    <td><?php echo $account->name ?></td>
                                    <td><?php echo $type->name ?></td>
                                    <td><?php echo $account->is_active ?'Active':'Inactive' ?></td>
                                    <td><?php echo $created_by->username ?></td>
                                    <td><?php echo $account->getCreatedAt() ?></td>
                                    <td><?php echo $modified_by?$modified_by->username:"--" ?></td>
                                    <td><?php echo $account->getModifiedAt() ?$account->getModifiedAt():"--" ?></td>
                                    <td>

                                        <form action="" method="post" onsubmit="return validate();" class="mb-0">
                                               <input type="hidden" name="id" value="<?php echo $account->id ?>">
                                               <input type="hidden" name="action" value="delete">
                                                <a type="button" class="btn btn-success" href="view.php?id=<?php echo $account->id ?>">View <i class="fa fa-eye"></i></a>
                                                <a type="button" class="btn btn-info" href="update.php?id=<?php echo $account->id ?>">Edit <i class="fa fa-edit"></i></a>
                                               <button type="submit" class="btn  btn-danger" name="_submitDelete">
                                                   Delete <i class="fa fa-trash"></i
                                               </button>

                                        </form>

                                    </td>
                                </tr>

                                <?php
                            }
                        }
                        else{
                            ?>


                            <tr>
                                <td colspan="9" class="text-center">No records</td>
                            </tr>
                            <?php
                        }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="9">
                                <span class="float-right"><?php echo (($page-1) * $limit ) + count($accounts) ; ?> of <?php echo $RowsTotal ?> Row(s)</span>
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination">
                                        <li class="page-item <?php echo (($page-1)<=0 || count($accounts)==0 )?'disabled':'' ?>">
                                            <a class="page-link " href="index.php?page=<?php echo ($page-1).$queryLink ;?>">Previous</a>
                                        </li>
                                        <?php

                                            for($i=1;$i<=$pages;$i++ ){
                                            ?>


                                                <li class="page-item <?php echo ($i==$page)?'active':''; ?>">
                                                    <a class="page-link" href="index.php?page=<?php echo $i.$queryLink ;?>">
                                                        <?php echo $i ?>
                                                    </a>
                                                </li>

                                            <?php
                                        }
                                        ?>
                                        <li class="page-item <?php echo (($page+1)> $pages || count($accounts)==0 )?'disabled':'' ?>">
                                            <a class="page-link" href="index.php?page=<?php echo ($page+1).$queryLink ;?>">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </main>
    </div>
</div>


</body>


<?php include_once WEB_PATH.DS."assets/js.inc"; ?>
<?php include_once "includes/js.inc"; ?>


<div class="modal fade" id="changePassword" tabindex="-1" role="dialog" aria-labelledby="Change default password" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <form name="changePasswordForm" id="changePasswordForm"  action="" method="post" class="needs-validation" novalidate>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Change default password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">


                    <div class="form-group">
                        <label for="default_password">Default Password </label>
                        <input type="text" class="form-control" id="default_password" value="<?php echo $defaultPassword; ?>" name="default_password" placeholder="default password eg. serv"
                               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                        <div class="invalid-feedback">
                            Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close <i class="fa fa-ban"></i></button>
                    <button type="submit" class="btn btn-primary" id="saveInfo">Save <i class="fa fa-save"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>


</html>

<?php
echo $jsResponse;
?>