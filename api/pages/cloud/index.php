<?php
namespace vsm\api\pages\cloud;
require_once "../../lib/init.php";

use vsm\api\lib\Database;
use vsm\api\lib\Helpers\VsmAssistance;
use vsm\dal\AccountRepository;
use vsm\dal\AccountTypeRepository;
use vsm\dal\CloudRepository;

$menu_active="cloud";


$repo = CloudRepository::getInstance();
$accountRepo = AccountRepository::getInstance();
$accountTypeRepo = AccountTypeRepository::getInstance();
$types = [];
foreach ($accountTypeRepo->OnlyActive() as $type){
    $types[$type->id]=[
            'name'=>$type->name,
            'maxinstances'=>$type->maxinstances,
            'id'=>$type->id
    ];
}



$allaccounts=$accountRepo->OnlyActive();
$allregions = $repo->getRegion()->getList();
$allos = $repo->getOS()->getList();



?>
<!doctype html5>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cloud Management</title>
    <?php include_once WEB_PATH.DS."assets/css.inc"; ?>
    <?php include_once "includes/css.inc"; ?>
</head>

<body>

<?php include_once WEB_PATH.DS."parts/header.inc";?>

<div class="container-fluid">
    <div class="row">
        <?php include_once WEB_PATH.DS."parts/sidebar.inc";?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">

            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

                <h1 class="h2">Clouds</h1>

            </div>

            <div class="alert alert-info alert-dismissible" style="display: none" role="alert" id="alert-info">
                <strong>Task Completed!</strong> <span id="text-info"></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <form action="" method="post" class="form-inline" >

                        <label class="my-1 mr-2 font-weight-bold" for="account_id">Accounts</label>
                        <select class="custom-select my-1 mr-sm-2" name="account_id" id="account_id">
                            <option selected>Choose...</option>
                            <?php
                                foreach ($allaccounts as $account){
                            ?>
                                    <option data-typeId="<?php echo $account->account_type_id ?>" value="<?php echo $account->id ?>"><?php echo $account->name ?></option>
                            <?php } ?>
                        </select>

                        <label class="my-1 mr-2 font-weight-bold" for="region">Region</label>
                        <select class="custom-select my-1 mr-2" name="region" id="region">
                            <option value="-1">All</option>
                            <?php foreach ($allregions as $id=>$region) {?>
                                <option  value="<?php echo $region->DCID ?>"><?php echo $region->country ?> <?php echo $region->name ?></option>
                            <?php } ?>
                        </select>

                        <button type="button" id="refreshTable" class="btn btn-primary my-1  mr-2"><i class="fa fa-list"></i> Get Servers</button>
                        <button type="button" class="btn btn-primary my-1 mr-1" disabled data-toggle="modal" data-target="#serverCreate" id="CreateServer"><i class="fa fa-plus"></i> Create new</button>

                        <div id="PlansLoader" style="display: none">
                            <div   class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>

            </div>



            <div class="row">
                <div class="col-md-4 mb-2">

                    <div class="dropdown" id="actionMulti">
                        <button class="btn btn-warning dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-cogs"></i> Action
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item _StopSelected" data-action="stop" href="javascript:void(0)">Stop <i class="fa fa-stop-circle"></i></a>
                            <a class="dropdown-item _StartSelected" data-action="start" href="javascript:void(0)">Start <i class="fa fa-play-circle"></i></a>
                            <a class="dropdown-item _RebootSelected" data-action="reboot" href="javascript:void(0)">Reboot <i class="fa fa-retweet"></i></a>
                            <a class="dropdown-item _DestroySelected" data-action="destroy" href="javascript:void(0)">Destroy <i class="fa fa-trash"></i></a>
                        </div>
                        <input type="hidden" name="subids" id="subids" value="">
                    </div>
                </div>
            </div>
            <table id="clouds" class="table table-bordered" style="width:100%">
                <thead>
                <tr>
                    <th class="text-center"><input type="checkbox" name="select_all" value="-1" id="cloud-select-all"></th>
                    <th>ID</th>
                    <th>Label</th>
                    <th>Region</th>
                    <th>IPv4</th>
                    <th>IPv6</th>
                    <th>RDNS</th>
                    <th>Domain</th>
                    <th>Power Status</th>
                    <th>Server Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>
            <div id="Loader" style="display: none" class="text-center">
                <div class="d-flex justify-content-center">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>

                </div>
                <p>
                    <b>Wait Please ..</b>
                </p>

            </div>


    </div>
        </main>
    </div>
</div>


</body>


<!-- Modal -->
<div class="modal fade" id="serverCreate" tabindex="-1" role="dialog" aria-labelledby="Create new server" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form name="deployInstances" id="deployInstances"  action="" method="post" class="needs-validation" novalidate>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New Server</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="server_name">Name</label>
                        <input type="text" class="form-control" id="server_name" name="server_name" placeholder="server name eg. serv" required>
                        <div class="invalid-feedback">
                            Name is required
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="start_with">Start With</label>
                        <input type="text" class="form-control" id="start_with" name="start_with" placeholder="start with eg. 001" required>
                        <div class="invalid-feedback">
                            Start with number is required
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="server_domain">Domain</label>
                        <input type="text" class="form-control" id="server_domain" name="server_domain" placeholder="server domain eg. domain.com">
                    </div>
                    <div class="form-group">
                        <label for="machine_id">Machine</label>
                        <select class="form-control" id="machine_id" name="machine_id" required>
                        </select>
                        <div class="invalid-feedback">
                            Machine is required
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="os_id">OS</label>
                        <select class="form-control" id="os_id" name="os_id" required>
                            <?php foreach ($allos as $os) {?>
                                <option  value="<?php echo $os->OSID ?>"><?php echo $os->name ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            Os system is required
                        </div>
                    </div>
                    <!--<div class="form-group">
                        <label for="default_password">Default Password <a href="javascript:void(0)" onclick="document.getElementById('default_password').value=Cloud.passwordGenerator(12)">Generate Random</a></label>
                        <input type="text" class="form-control" id="default_password" name="default_password" placeholder="default password eg. serv"
                               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                        <div class="invalid-feedback">
                            Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters
                        </div>
                    </div>-->
                    <div class="form-group">
                        <label for="total_server">Total</label>
                        <input type="number" min="1"  class="form-control" id="total_server" name="total_server" value="1" placeholder="total servers eg. 1">

                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="ipv6enabled" name="ipv6enabled" value="">
                        <label class="form-check-label" for="ipv6enabled">Enable ipv6</label>
                    </div>

                    <input type="hidden" name="maxinstances" value="" id="maxinstances">

            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close <i class="fa fa-ban"></i></button>
                    <button type="button" class="btn btn-primary" id="deployServer">Deploy <i class="fa fa-magic"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>



<script type="application/javascript">

var types = <?php echo json_encode($types)?>;

</script>
<?php include_once WEB_PATH.DS."assets/js.inc"; ?>
<?php include_once "includes/js.inc"; ?>





</html>