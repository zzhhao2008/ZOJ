<?php
class contest{
    public static function get_config(){
        return DB::getdata("contest/config");
    }
    public static function put_config($new){
        return DB::putdata("contest/config",$new);
    }
    public static function configadd(){
        $cfg=self::get_config();
        $cfg['contests']++;
        self::put_config($cfg);
        return $cfg['contests'];
    }
    public static function emptycontestconfig(){
        return array(
            "title"=>"比赛标题",
            "desc"=>"比赛描述",
            "starttime"=>time(),
            "endtime"=>time()+3600*24,
            "problemlist"=>[],
            "powerneed"=>0,
            
        );
    }
    public static function put($id,$configs){
        return DB::putdata("contest/cfgs/".$id,$configs);
    }
    public static function create(){
        $cfg=self::emptycontestconfig();
        $id=self::configadd();
        $cfg['id']=$id;
        self::put($id,$cfg);
        return $id;
    }
    public static function query($id){
        return DB::getdata("contest/cfgs/".$id);
    }

}