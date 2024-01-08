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
        $mycfg = team::user($uid);
        //判断是否为唯一的leader
        if (count($teamconifg['leaders']) == 1) {
            if ($uid === $teamconifg['leaders'][0]) {
                return 0;
            }
        }
        if (in_array($tid, $mycfg['joined'])) {
            $mycfg['joined'] = array_diff($mycfg['joined'], [$tid]);
            DB::putdata("team/user/$uid", $mycfg);
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
    public static function setleader($tid, $uid){
        $teamconifg = team::get($tid);
        if (!team::is_leader($tid)) return 0; //不是管理员
        if (!in_array($uid, $teamconifg['leaders'])) {
            $teamconifg['leaders'][] = $uid;
        }
        return team::put($tid, $teamconifg);
    }
    static function unsetleader($tid, $uid){
        $teamconifg = team::get($tid);
        if (!team::is_leader($tid)) return 0; //不是管理员
        $teamconifg['leaders'] = array_diff($teamconifg['leaders'], [$uid]);
        return team::put($tid, $teamconifg);
    }
}
