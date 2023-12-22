<?php
class friends{
    public static function getFriends($userid){
        return DB::getdata("chat/conifg/frineds/$userid");
    }
    public static function putFriends($userid,$new){
        return DB::putdata("chat/conifg/frineds/$userid",$new);
    }
    public static function emptyFriendListConfig(){
        return array(
            "friends"=>array(),
            "request"=>array()
        );
    }
    public static function  emptyFriendConfig(){
        return array(
            "id"=>""
        );
    }
    static public function addFriend($user_id, $friend_id){

    }
}