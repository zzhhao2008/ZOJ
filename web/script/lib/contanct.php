<?php
class contanct
{
    public static function getContanctList_Problems($returnRaw = 0)
    {
        if ($returnRaw == 1) {
            return DB::scanData("contanct/problem");
        } else {
            return DB::scanName("contanct/problem");
        }
    }
    public static function getContanctList_Private($returnRaw = 0)
    {
        if ($returnRaw == 1) {
            return DB::scanData("contanct/private");
        } else {
            return DB::scanName("contanct/private");
        }
    }
    public static function queryContanct($cid)
    {
        return DB::getData("contanct/problem/$cid");
    }
    public static function putContanct($cid,$data)
    {
        return DB::putData("contanct/problem/$cid",$data);
    }
    public static function putPContanct($cid,$data)
    {
        return DB::putData("contanct/private/$cid",$data);
    }
    public static function queryPContanct($cid)
    {
        return DB::getData("contanct/private/$cid");
    }
    public static function getConfig() {
        return DB::getData("contanct/config");
    }
    public static function putConfig($new) {
        return DB::putData("contanct/config",$new);
    }
    public static function getContanctCount($id){
        $config=contanct::getConfig();
        if($config[$id]){
            return $config[$id];
        }
        return 0;
    }
    /**
     * ErrorCode:
     * 0: 成功
     * 1: 失败(Auth)
     * 2: 失败(ForEmpty)
     * 
     */
    public static function createContanct($for,$title,$desc){
        global $mypower;
        if($mypower<=0){
            return 1;
        }
        if(problems::queryProblem($for)===array()){
            return 2;
        }
        $config=contanct::getConfig();
        $config[$for]=contanct::getContanctCount($for)+1;
        contanct::putConfig($config);
        $username=user::read()['name'];
        $id=contanct::getContanctCount($for)."-$for-".$username;
        $newContanct=array(
            "id"=>$id,
            "for"=>$for,
            "creator"=>$username,
            "createTime"=>time(),
            "title"=>$title,
            "desc"=>$desc
        );
        contanct::putContanct($id,$newContanct);
        return $id;
    }
    public static function createContanctPrivate($for,$title,$desc){
        global $mypower;
        if($mypower<=0){
            return 1;
        }
        if(problems::queryProblem($for)===array()){
            return 2;
        }
        $config=contanct::getConfig();
        $config["P".$for]=contanct::getContanctCount("P".$for)+1;
        contanct::putConfig($config);
        $username=user::read()['name'];
        $id="P-".contanct::getContanctCount("P".$for)."-$for-".$username;
        $newContanct=array(
            "id"=>$id,
            "for"=>$for,
            "creator"=>$username,
            "createTime"=>time(),
            "title"=>$title,
            "desc"=>$desc
        );
        contanct::putPContanct($id,$newContanct);
        return $id;
    }
}
