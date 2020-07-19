<?php
namespace vsm\api\pages\account\type;
require_once "../../../lib/init.php";

use vsm\bl\SessionHandler\SessionHandler;
use vsm\dal\AccountTypeRepository;
use vsm\dal\UserRepository;

$menu_active="account";
if(!SessionHandler::getInstance()->isAppAllowed($menu_active)){
    header("Location: ../../errors/403.php");
    exit(0);
}

$page=1;
$limit=10;
$Repo = AccountTypeRepository::getInstance();
$RowsTotal = $Repo->TotalRows([],'');
$pages = 0;
if($RowsTotal > 0) {
    $pages = ceil((float)$RowsTotal / $limit);
    if (isset($_GET['page'])) {
        $page = intval($_GET['page']);
        $page = ($page <= 0) ? 1 : $page;
        $page = ($page > $pages) ? $pages : $page;
    }
}



$types=$Repo->Paginate($page,$limit);
$errMsg = null;
if(isset($_POST['_submitDelete']) && ($_POST['id']) && $_POST['action'] == 'delete'){

    $typeId = intval($_POST['id']);
    try {
        if($Repo->Remove($typeId)){
            header("Location: index.php");
        }
    }
    catch(\PDOException $e){
        $errMsg = $e->getMessage();
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
    <title>Account Type Management</title>
    <?php include_once WEB_PATH.DS."assets/css.inc"; ?>
</head>

<body>

<?php include_once WEB_PATH.DS."parts/header.inc";?>

<div class="container-fluid">
    <div class="row">
        <?php include_once WEB_PATH.DS."parts/sidebar.inc";?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">

            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

                <h1 class="h2">Account Types</h1>
                <span class="float-right">
                    <div class="dropdown">
                          <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Account Types Settings
                          </a>

                          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="create.php">New Type <i class="fa fa-plus"></i></a>
                            <a class="dropdown-item" href="../index.php">Manage Accounts <i class="fa fa-cog"></i></a>
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

            <div class="table-responsive-sm">

                <table class="table table-bordered">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Max Instances</th>
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
                        foreach ($types as $type){
                            $created_by = UserRepository::getInstance()->Find($type->created_by);
                            $modified_by = UserRepository::getInstance()->Find($type->modified_by);


                            ?>
                            <tr>
                                <td><?php echo $type->id ?></td>
                                <td><?php echo $type->name ?></td>
                                <td><?php echo $type->maxinstances ?></td>
                                <td><?php echo $type->is_active ?'Active':'Inactive' ?></td>
                                <td><?php echo $created_by?$created_by->username:'N/A' ?></td>
                                <td><?php echo $type->getCreatedAt() ?></td>
                                <td><?php echo $modified_by?$modified_by->username:"N/A" ?></td>
                                <td><?php echo $type->getModifiedAt() ?$type->getModifiedAt():"--" ?></td>
                                <td>

                                    <form action="" method="post" onsubmit="return validate();" class="mb-0">
                                        <input type="hidden" name="id" value="<?php echo $type->id ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <a type="button" class="btn btn-info" href="update.php?id=<?php echo $type->id ?>">Edit <i class="fa fa-edit"></i></a>
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
                            <span class="float-right"><?php echo (($page-1) * $limit ) + count($types) ; ?> of <?php echo $RowsTotal ?> Row(s)</span>
                            <nav aria-label="Page navigation example">
                                <ul class="pagination">
                                    <li class="page-item <?php echo (($page-1)<=0 || count($types)==0 )?'disabled':'' ?>">
                                        <a class="page-link " href="index.php?page=<?php echo $page-1 ;?>">Previous</a>
                                    </li>
                                    <?php

                                    for($i=1;$i<=$pages;$i++ ){
                                        ?>


                                        <li class="page-item <?php echo ($i==$page)?'active':''; ?>">
                                            <a class="page-link" href="index.php?page=<?php echo $i ;?>">
                                                <?php echo $i ?>
                                            </a>
                                        </li>

                                        <?php
                                    }
                                    ?>
                                    <li class="page-item <?php echo (($page+1)> $pages || count($types)==0 )?'disabled':'' ?>">
                                        <a class="page-link" href="index.php?page=<?php echo $page+1 ;?>">Next</a>
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
<?php include_once "../includes/js.inc"; ?>

</html>