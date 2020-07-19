<?php
define("APP_NAME","vsm");
define("DS",DIRECTORY_SEPARATOR);
define("APP_HOST","http://".$_SERVER['HTTP_HOST']);
define("ROOT_PATH",DS."var".DS.APP_NAME);
define("BL_PATH",ROOT_PATH.DS."bl");
define("DAL_PATH",ROOT_PATH.DS."dal");
define("WEB_PATH",ROOT_PATH.DS."api");
define("PAGES_PATH",APP_HOST.DS."pages");
define("DATE_TIMEZONE",'America/New_York');
const USER_TYPES = array(
    1=>'ADMIN',
    2=>'MAILER'
);
const APP_ROLES = [
  'account'=>1,
  'user'=>1,
  'cloud'=>2

];



/**
 * AutoLoader
 */

spl_autoload_register(function ($class) {
    $file = "/var/".str_replace('\\', DS, $class).'.php';

    if (file_exists($file)) {
        require $file;
        return true;
    }
    return false;
});


if(!isset($_SESSION['VSM'])){
    session_start([
        "name"=>"VSM",
        "cookie_lifetime"=>86400,
        "read_and_close"=>true

    ]);
}


date_default_timezone_set(DATE_TIMEZONE);
?>