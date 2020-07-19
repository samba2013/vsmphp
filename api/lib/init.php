<?php
namespace vsm\api\lib;

require_once "webconfig.php";

use http\Header;
use vsm\api\lib\Database;
use vsm\bl\SessionHandler\SessionHandler;


if(!isset($_SESSION['user']))
    Header("Location: ".PAGES_PATH.DS."authentication".DS."login.php");


$DB=null;
try
{
    $DB=Database::get()->connect();
}
catch (\PDOException $e){
    die( "Database failed to connect : ".$e->getMessage());
}


