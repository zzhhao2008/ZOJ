<?php
class team_Manage
{
    public static function kickoff($tid, $uid)
    {
        if ($uid === user::read()['name']) return 0;
        $teamconifg = team::get($tid);
        if (!team::is_leader($tid) || !(team::joined($tid, $uid)||team::is_leader($tid,$uid))) return 0; //不是管理员或者没有加入
        //查找members中值为$uid的元素并删除
        $teamconifg['members'] = array_diff($teamconifg['members'], [$uid]);
        $teamconifg['members'] = array_values($teamconifg['members']);
        //查找leaders中值为$uid的元素并删除
        if (team::is_leader($tid, $uid)) {
            $teamconifg['leaders'] = array_diff($teamconifg['leaders'], [$uid]);
            $teamconifg['leaders'] = array_values($teamconifg['leaders']);
        }
        return team::put($tid, $teamconifg);
    }
    public static function ban($tid, $uid)
    {
        if ($uid === user::read()['name']) return 0;
        $teamconifg = team::get($tid);
        if (!team::is_leader($tid)) return 0; //不是管理员
        $teamconifg['cfgto'][$uid]['ban'] = 1;
        return team::put($tid, $teamconifg);
    }
    public static function unban($tid, $uid)
    {
        if ($uid === user::read()['name']) return 0;
        $teamconifg = team::get($tid);
        if (!team::is_leader($tid)) return 0; //不是管理员
        $teamconifg['cfgto'][$uid]['ban'] = 0;
        return team::put($tid, $teamconifg);
    }
    public static function change($tid,$new){
        
    }
}
