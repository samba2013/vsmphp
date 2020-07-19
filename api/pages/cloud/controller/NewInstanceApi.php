<?php

namespace vsm\api\pages\cloud\controller;
require_once "../../../lib/init.php";

use vsm\api\lib\Helpers\VsmAssistance;
use vsm\bl\Vultr\Script;
use vsm\bl\Vultr\Server;
use vsm\dal\AccountRepository;
use vsm\dal\CloudRepository;


$AppConfig=parse_ini_file(WEB_PATH.DS.'lib'.DS.'app.ini');

if(!empty($_POST)
    && isset($_POST['region_id']) && is_numeric($_POST['region_id'])
    && isset($_POST['account_id']) && is_numeric($_POST['account_id'])
){
    $output=(object)[
        "data"=>[],
        "error"=>null,
        "success"=>false
    ];

    $action = isset($_POST['action'])?$_POST['action']:"get_plans";
    $regionId=intval($_POST['region_id']);
    $accountId=intval($_POST['account_id']);

    $repo = CloudRepository::getInstance();
    $accountRepo = AccountRepository::getInstance();

    $account = $accountRepo->Find($accountId);
    if(!$account){
        $output->error="Account not found";
        die(json_encode($output));
    }

    $serverRepo=$repo->getServer($account->token,$regionId);
    $scriptObj=$repo->getScript($account->token);
    $userdata="#!/bin/sh\nmkdir -p /root/.ssh\nchmod 600 /root/.ssh";
    $userdata.="\nsed -i 's/PasswordAuthentication no/PasswordAuthentication yes/' /etc/ssh/sshd_config;";
    $userdata.="\nsed -i 's/PermitRootLogin without-password/#PermitRootLogin without-password/' /etc/ssh/sshd_config;";
    $userdata.="\necho \"[PASSWORD]\" | passwd root --stdin > /dev/null";
    $userdata.="\nsudo service sshd restart";



    switch ($action){
        case 'get_plans':
            $plans = $repo->getPlan()->getList();
            $availablePlansInRegions=$repo->getRegion()->getAvailable($regionId);

            $output->data=['plans'=>$plans,'available'=>$availablePlansInRegions];
            $output->success=true;
            break;
        case 'deploy':
            $tmpRepo=new Server($account->token,-1);
            $existClouds=(array)$tmpRepo->getList();
            $maxinstances=intval($_POST['maxinstances']);
            $total=intval($_POST['total']);
            if(($total+count($existClouds)) > $maxinstances){
                $output->error="You reached maximum instances : ".$maxinstances;
                die(json_encode($output));
            }
            $name=addslashes($_POST['name']);
            $hostname=$_POST['domain'];
            $defaultPassword=$AppConfig['cloud_default_password'];
            $startWith=addslashes($_POST['start_with']);
            $osId=intval($_POST['os_id']);
            $planId=intval($_POST['machine_id']);
            $osId=intval($_POST['os_id']);
            $ipv6enabled=intval($_POST['ipv6enabled'])==1?'yes':'no';


            exec('echo "'.$defaultPassword.'" | cracklib-check',$out,$err);

            if(!$err && count($out)){
                $result=current($out);
                if(!preg_match("#\:\sOK$#i",$result)){

                    $output->error="Password not strength enough";
                    die(json_encode($output));
                }
            }else{
                $output->error="System error";
                die(json_encode($output));
            }

            //* script *//

            $script=$scriptObj->GetScriptByName("VSMSCRIPT");
            $userdata=str_replace("[PASSWORD]",$defaultPassword,$userdata);
            $scriptId=0;
            if($script==null){
                $scriptId=$scriptObj->CreateScript("VSMSCRIPT",$userdata);
            }else{
                $scriptId=$script->SCRIPTID;
                $scriptObj->Update($scriptId,"VSMSCRIPT",$userdata);
            }
            $tag=$hostname;
            $label=  $name.$startWith;
            for ($i=1;$i<=$total;$i++){

                $subid=$serverRepo->Deploy($osId,$planId,$regionId,$hostname,$label,$scriptId,$ipv6enabled,$tag);
                if(!is_numeric($subid)){
                    //VsmAssistance::WrapObject($serverRepo->getLastError(),true,true);
                    $output->error="Error creating servers ".(is_string($serverRepo->getLastError())?$serverRepo->getLastError():"");
                    die(json_encode($output));
                }
                $label=  $name.str_pad(intval($startWith) + $i, strlen($startWith), "0", STR_PAD_LEFT);
            }

            $output->success=true;
            break;

        default:
            break;
    }


    die(json_encode($output));

}

?>