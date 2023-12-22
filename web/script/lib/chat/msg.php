<?php
class chatmsg{
    static public function getmsgs($msgid){
        return DB::getdata("chat/msg/$msgid");
    }
    static public function savemsg($msgid,$msg){
        return DB::putdata("chat/msg/$msgid",$msg);
    }
    static public function creatmsg($users){
        $newmsgid=count(DB::scanName("chat/msg"))+1;
        $newmsg=array(
            "users"=>$users,
            "serveDate"=>date("Y-m-d"),
            "data"=>[]
        );
        return chatmsg::savemsg($newmsgid,$newmsg);
    }
}