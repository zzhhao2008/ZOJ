<?php
$GLOBALS['username'] = $_COOKIE['login_name_code'];
$GLOBALS['userprofile'] = array();
$emptycfg = array(
    'nick' => '',
    'password' => '',
    'llt' => time(),
    'power' => 1,
    'rating' => 1500,
    'try' =>
    array(),
    'contest' =>
    array(),
    'practice' =>
    array(),
    'email' => '',
    'about' => '',
    'dt'=>array()
);
class user
{
    public static function read()
    {
        return array("name" => $GLOBALS['username'], "profile" => $GLOBALS['userprofile']);
    }
    public static function queryUser($username)
    {
        $uprofile = DB::getdata("user/$username");
        if (empty($uprofile)||$uprofile['unlink']===1||$uprofile['ban']===1) {
            return 0;
        }
        return $uprofile;
    }
    public static function queryUserAdmin($username)
    {
        $uprofile = DB::getdata("user/$username");
        return $uprofile;
    }
    public static function LoginCheck($init = 0)
    {
        $name = user::read()['name'];
        $thisup = user::queryUser($name);
        if ($_COOKIE['login_pas_code'] === md5($thisup['llt'] . $thisup['password'])) {
            if ($init === 1) $GLOBALS['userprofile'] = $thisup;
            return $thisup;
        } else {
            return 0;
        }
    }
    public static function saveuserchange($logining = 0)
    {
        if (user::LoginCheck() || $logining) {
            DB::putdata("user/" . user::read()['name'], user::read()['profile']);
            return 1;
        }
        return 0;
    }
    public static function login($name, $pas)
    {
        $cfg = user::queryUser($name);
        if ($cfg === 0) {
            return 0;
        }
        if ($cfg['password'] === md5($pas)) {
            $GLOBALS['userprofile'] = $cfg;
            $GLOBALS['userprofile']['llt'] = time();
            $pass = md5($GLOBALS['userprofile']['llt'] . $GLOBALS['userprofile']['password']);
            setcookie("login_name_code", $name, time() + 3600 * 48, "/");
            setcookie("login_pas_code", $pass, time() + 3600 * 48);
            $GLOBALS['username'] = $name;
            return user::saveuserchange(1);
        } else {
            return 0;
        }
    }
    public static function change($key, $value, $savenow = 0): bool
    {
        $GLOBALS['userprofile'][$key] = $value;
        return $savenow ? user::saveuserchange() : 1;
    }
    public static function change_Add($key, $value, $savenow = 0): bool
    {
        $GLOBALS['userprofile'][$key][] = $value;
        return $savenow ? user::saveuserchange() : 1;
    }
    public static function getMy($key)
    {
        $res = $GLOBALS['userprofile'][$key];
        return $res;
    }
    public static function saveuserprofie($userid,$cfg){
        $y=DB::getdata("user/$userid");
        if(empty($y)||!is_array($cfg)) return 0;
        else return DB::putdata("user/$userid",$cfg);
    }
    public static function is_superuser(){
        return user::read()['profile']['power']>1;
    }
}
