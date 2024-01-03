<?php
class team{
    static function get($teamid){
        return DB::getdata("team/teamconfig/$teamid");
    }
    static function put($teamid,$cfg){
        return DB::putdata("team/teamconfig/$teamid",$cfg);
    }
    static function user(){
        return DB::getdata("team/user/".user::read()['name']);
    }
}