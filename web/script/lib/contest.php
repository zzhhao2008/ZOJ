<?php
class contest
{
    public static function get_config()
    {
        return DB::getdata("contest/config");
    }
    public static function put_config($new)
    {
        return DB::putdata("contest/config", $new);
    }
    public static function configadd()
    {
        $cfg = self::get_config();
        $cfg['contests']++;
        self::put_config($cfg);
        return $cfg['contests'];
    }
    public static function emptycontestconfig()
    {
        return array(
            "title" => "比赛标题",
            "desc" => "比赛描述",
            "starttime" => time(),
            "endtime" => time() + 3600 * 24,
            "problemlist" => [],
            "powerneed" => 0,
            "type"=>"OPEN",
            "joinedusers"=>[],
            "tag"=>[]
        );
    }
    public static function put($id, $configs)
    {
        return DB::putdata("contest/cfgs/" . $id, $configs);
    }
    public static function create()
    {
        $cfg = self::emptycontestconfig();
        $id = self::configadd();
        $cfg['id'] = $id;
        self::put($id, $cfg);
        return $id;
    }
    public static function query($id)
    {
        return DB::getdata("contest/cfgs/" . $id);
    }
    public static function get_list($page = 1, $limit = 100)
    {
        return DB::scanData("contest/cfgs", $page, $limit);
    }
    public static function ContestConfig_Default($type)
    {
        $def = array(
            "OI" => array(
                "showtruescore"=>0,
                "showturchart"=>0,
                "joinable"=>0 //可以中途加入
            ),
            "ACM" => array(
                "showtruescore"=>1,
                "showturchart"=>0,
                "joinable"=>0
            ),
            "IOI"=>array(
                "showtruescore"=>1,
                "showturchart"=>1,
                "joinable"=>0
            ),
            "OPEN"=>array(
                "showtruescore"=>1,
                "showturchart"=>1,
                "joinable"=>1
            )
        );
        return $def[$type];
    }
    public static function joinable($contestcfg,$uid=""){
        $upower=user::queryUser($uid)['power'];
        if($contestcfg['powerneed']>$upower) return 0;
        if($contestcfg['endtime']<time()){ //比赛已结束
            return 0;
        }
        if(self::ContestConfig_Default($contestcfg['type'])['joinable']===1){ //比赛没有结束并且可以中途加入
            return 1;
        }
        if($contestcfg['starttime']>time()){
            return 1;
        }
        return 0;
    }
    public static function  visiable($contestcfg,$uid=""){
        if(user::is_superuserO($uid)){ //超级用户
            return true;
        }
        if(self::joinable($contestcfg,$uid)){ //可以报名
            return 1;
        }
        $upower=user::queryUser($uid)['power'];
        if(in_array($uid, $contestcfg['joinedusers'])){  //已经报名
            return 1;
        }
        if($contestcfg['powerneed']>$upower||
        self::going($contestcfg)) return 0; //权限不足或者比赛正在进行
        else return 1; //可以访问
        return 0;
    }
    public static function joined($users){
        if(user::is_superuser()) return true;
        return in_array(user::read()['name'], $users);
    }
    public static function going($contestcfg){
        return $contestcfg['starttime']<time()&&$contestcfg['endtime']>time()&&!$contestcfg['paused'];
    }
    public static function end($contestcfg){
        return $contestcfg['endtime']<time();
    }
}
