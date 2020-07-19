<?php

namespace vsm\api\pages\cloud\controller;
require_once "../../../lib/init.php";

use vsm\api\lib\Helpers\VsmAssistance;
use vsm\dal\AccountRepository;
use vsm\dal\CloudRepository;




$repo = CloudRepository::getInstance();
$accountRepo = AccountRepository::getInstance();

$allaccounts=$accountRepo->OnlyActive();
$allregions = $repo->getRegion()->getList();
$regions=[];
$accounts=[];
foreach ($allregions as $region){
    $regions[$region->DCID]=$region;
}

foreach ($allaccounts as $account){
    $accounts[$account->id]=$account;
}


if(!empty($_POST)
    && isset($_POST['account_id'])
    && is_numeric($_POST['account_id'])
    && isset($_POST['region_id'])
    && is_numeric($_POST['region_id'])
){


    $accountId = intval($_POST['account_id']);
    $regionId = (isset($_POST['region_id']) && !empty($_POST['region_id']))?intval($_POST['region_id']):-1;

    $accountObj = isset($accounts[$accountId])?$accounts[$accountId]:[];
    $rows = [];
    $serverRepo = null;
    if(count($accountObj)){
        $serverRepo=$repo->getServer($accountObj->token,$regionId);
    }


    if(isset($_POST['draw'])){
        $rows = $serverRepo->getList();
        $recordsTotal = count($rows);
        $data = [];
        foreach ($rows as $row) {

            $ips = $serverRepo->getIps($row->SUBID);

            $rdns = "N/A";
            foreach ($ips as $ip) {
                if ($ip->ip == $row->main_ip) {
                    $rdns = $ip->reverse;
                }
            }

            $canStart = ($row->power_status == 'running')?"disabled":"";
            $canStop = ($row->power_status == 'stopped')?"disabled":"";


            $serverRegion = isset($regions[$row->DCID]) ? ($regions[$row->DCID]->country . " " . $regions[$row->DCID]->name) : $row->location;
            $actions =<<<EOL
<div class="dropdown dropleft">
  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fa fa-cog"></i> Settings
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item _Stop {$canStop}" href="javascript:void(0)" data-id="{$row->SUBID}">Stop <i class="fa fa-stop-circle"></i></a>
    <a class="dropdown-item _Start {$canStart}" href="javascript:void(0)" data-id="{$row->SUBID}">Start <i class="fa fa-play-circle"></i></a>
    <a class="dropdown-item _Reboot" href="javascript:void(0)" data-id="{$row->SUBID}">Reboot <i class="fa fa-retweet"></i></a>
    <a class="dropdown-item _Destroy" href="javascript:void(0)" data-id="{$row->SUBID}">Destroy <i class="fa fa-trash"></i></a>
  </div>
</div>
EOL;

            $data[]=[
                'check'=>'',
                'id'=>$row->SUBID,
                'label'=>$row->label,
                'region'=>$serverRegion,
                'ipv4'=>(isset($row->main_ip) && strlen($row->main_ip))?$row->main_ip:"N/A",
                'domain'=>(isset($row->tag) && strlen($row->tag))?$row->tag:"NONE",
                'ipv6'=>(isset($row->v6_main_ip) && strlen($row->v6_main_ip))?$row->v6_main_ip:"N/A",
                'rdns'=>$rdns,
                'power_status'=>$row->power_status,
                'server_state'=>$row->server_state,
                'actions'=>$actions
            ];
        }



        $output = [
            'draw'=>intval($_POST['draw']),
            'recordsTotal'=>$recordsTotal,
            'recordsFiltered'=>count($data),
            'data'=>$data
        ];

        die(json_encode($output));
    }elseif (isset($_POST['SUBID']) && $_POST['actionSoloServer']){
        $action = trim($_POST['actionSoloServer']);
        switch ($action){
            case 'stop':
                $serverRepo->StopServer(intval($_POST['SUBID']));
                break;
            case 'reboot':
                $serverRepo->RebootServer(intval($_POST['SUBID']));
                break;
            case 'destroy':
                $serverRepo->Destroy(intval($_POST['SUBID']));
                break;
            case 'start':
                $serverRepo->StartServer(intval($_POST['SUBID']));
                break;
            default:
                break;
        }
        echo 1;

    }elseif (isset($_POST['subids']) && $_POST['actionMultiServer']){
            $subids = explode(",",$_POST['subids']);
            $serverAction = $_POST['actionMultiServer'];

            foreach ($subids as $subid){
                switch ($serverAction){
                    case 'stop':
                        $serverRepo->StopServer(intval($subid));
                        break;
                    case 'reboot':
                        $serverRepo->RebootServer(intval($subid));
                        break;
                    case 'destroy':
                        $serverRepo->Destroy(intval($subid));
                        break;
                    case 'start':
                        $serverRepo->StartServer(intval($subid));
                        break;
                    default:
                        break;
                }
                sleep(0.5);
            }
        echo 1;
    }



}else{
    $output = [
        'draw'=>1,
        'data'=>[]
    ];
    echo json_encode($output);
}
?>