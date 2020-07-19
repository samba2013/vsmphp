<?php
namespace vsm\api\pages\user;
require_once "../../lib/init.php";

use vsm\bl\QueryBuilder\QueryBuilder;
use vsm\bl\SessionHandler\SessionHandler;
use vsm\bl\User;
use vsm\dal\UserRepository;
use function Sodium\add;

$menu_active="user";
if(!SessionHandler::getInstance()->isAppAllowed($menu_active)){
    header("Location: ../errors/403.php");
    exit(0);
}


$page= (isset($_GET['page']))?intval($_GET['page']):0;
$query=(isset($_GET['q']) && !empty($_GET['q']))?addslashes($_GET['q']):'';

/**
 * Repository of Users
 */
$Repo = UserRepository::getInstance();

/**
 * Delete User
 */
$errMsg = null;
if(isset($_POST['_submitDelete']) && ($_POST['id']) && $_POST['action'] == 'delete'){
    $userId = intval($_POST['id']);
    try {
        if($Repo->Remove($userId)){
            header("Location: index.php");
        }
    }
    catch(\PDOException $e){
        $errMsg = $e->getMessage();
    }
}

/**
 * Paginate and search
 */

$queryLink="";
$limit=5;

$columns = ['username','firstname','lastname'];

$RowsTotal = $Repo->TotalRows($columns,$query);
$pages = 0;

if($RowsTotal > 0) {
    $pages = ceil((float)$RowsTotal / $limit);
    $page = ($page <= 0) ? 1 : $page;
    $page = ($page > $pages) ? $pages : $page;
}

if(!empty($query)){
    $queryLink="&q=".$query;
}

$users=$Repo->SearchAndPaginate($columns,$query,$page,$limit);





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

                <h1 class="h2">Users</h1>
                <span class="float-right">
                    <div class="dropdown">
                          <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="fa fa-users"></i> User Settings
                          </a>

                          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="create.php"><i class="fa fa-user-plus"></i> New User </a>
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
                       ` <div class="input-group">

                                <input type="text" value="<?php echo $query;?>" name="q" class="form-control" placeholder="search for value" aria-label="Text input with segmented dropdown button">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-outline-secondary">Search <i class="fa fa-search"></i></button>
                                </div>

                        </div>`
                    </form>
                </div>

            </div>

            <div class="table-responsive-sm">

                <table class="table table-bordered">
                    <tr>
                        <th>ID</th>
                        <th>First name</th>
                        <th>Last name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Active</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th>Modified By</th>
                        <th>Modified At</th>
                        <th>Actions</th>
                    </tr>
                    <tbody>

                    <?php
                        if(count($users)){
                                foreach ($users as $user){
                                    $createdby=$Repo->Find($user->created_by);
                                    $modifiedby=$Repo->Find($user->modified_by);

                                    ?>
                                <tr>
                                    <td><?php echo $user->id ?></td>
                                    <td><?php echo $user->firstname ?></td>
                                    <td><?php echo $user->lastname?></td>
                                    <td><?php echo $user->username ?></td>
                                    <td><?php echo USER_TYPES[$user->role_id] ?></td>
                                    <td><?php echo $user->is_active ?'Active':'Inactive' ?></td>
                                    <td><?php echo ($createdby==null)?"--":$createdby->username; ?></td>
                                    <td><?php echo $user->getCreatedAt() ?></td>
                                    <td><?php echo ($modifiedby==null)?"--":$modifiedby->username; ?></td>
                                    <td><?php echo $user->getModifiedAt() ?$user->getModifiedAt():"--" ?></td>
                                    <td>
                                        <form action="" method="post" onsubmit="return validate();" class="mb-0">

                                            <a type="button" class="btn btn-info" href="update.php?id=<?php echo $user->id ?>">Edit <i class="fa fa-edit"></i></a>
                                            <?php if($_SESSION['user']->id !== $user->id) { ?>
                                            <input type="hidden" name="id" value="<?php echo $user->id ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn  btn-danger" name="_submitDelete">
                                                Delete <i class="fa fa-trash"></i
                                            </button>
                                            <?php } ?>

                                        </form>

                                    </td>
                                </tr>
                    <?php
                                }
                        }else{
                            ?>


                            <tr>
                                <td colspan="11" class="text-center">No records</td>
                            </tr>
                            <?php
                        }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="11">
                                <span class="float-right"><?php echo (($page-1) * $limit ) + count($users) ; ?> of <?php echo $RowsTotal ?> Row(s)</span>
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination">
                                        <li class="page-item <?php echo (($page-1)<=0 || count($users)==0 )?'disabled':'' ?>">
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
                                        <li class="page-item <?php echo (($page+1)> $pages || count($users)==0 )?'disabled':'' ?>">
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

</html>