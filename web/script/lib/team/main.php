<?php
class team
{
    static function get($teamid)
    {
        return DB::getdata("team/teamconfig/$teamid");
    }
    static function put($teamid, $cfg)
    {
        return DB::putdata("team/teamconfig/$teamid", $cfg);
    }
    static function user($uid="\$\$myself")
    {
        if($uid == "\$\$myself") $uid =  user::read()['name'];
        return DB::getdata("team/user/" .$uid);
    }
    static function create()
    {
        /**
         * About Type
         * Basic : 基础团队 最多50人 无特权
         * junior : 初级团队 最多200人 基本特权
         * senior : 高级团队 由初级团队升级（评分高于2500且时间超过1个月） 大部分特权
         */
        $empty = [
            "creator" => user::read()['name'],
            "time" => time(),
            "leaders" => [user::read()['name']],
            "members" => [],
            "name" => "新团队 - ",
            "description" => "",
            "type" => "basic",
            "rating" => 1000,
            "cfgto" => [],
            "joinable" => 1
        ];
        $work = [
            "list"=>[
                ['id'=>1,"desc"=>"默认任务"]
            ],
            'cfg'=>[],
        ];
        $id = count(DB::scanName("team/teamconfig"));
        $empty['id'] = $id;
        $empty['name'] .= $id;
        DB::putdata("team/teamconfig/$id", $empty);
        DB::putdata("team/teamwork/$id", $work);
        self::join($id);
        return $id;
    }
    static function joinable($tid)
    {
        global $mypower;
        $myid = user::read()['name'];
        $teamcfg = self::get($tid);
        if(empty($teamcfg)) return 0;
        return ($teamcfg['joinable'] <= $mypower) && ($teamcfg['cfgto'][$myid]['ban'] !== 1);
    }
    static function join($tid)
    {   
        $teamcfg = self::get($tid);
        if(empty($teamcfg)) return 0;
        $mycfg = self::user();
        if (!in_array($tid, $mycfg['joined'])) {
            $mycfg['joined'][] = $tid;
            DB::putdata("team/user/" . user::read()['name'], $mycfg);
        }
        if (!in_array(user::read()['name'], $teamcfg['members']) && self::joinable($tid)) {
            $teamcfg['members'][] = user::read()['name'];
            DB::putdata("team/teamconfig/$tid", $teamcfg);
        }
    }
    static function init()
    {
        global $mypower;
        if ($mypower <= 0) return [];
        $mycfg = self::user();
        if (empty($mycfg)) {
            $mycfg = array(
                "joined" => [],
            );
            DB::putdata("team/user/" . user::read()['name'], $mycfg);
        }
        return $mycfg;
    }
    static function joined($tid, $uid = "\$\$myself\#\#")
    {
        if ($uid == "\$\$myself\#\#") $uid = user::read()['name'];
        $teamcfg = self::get($tid);
        if(empty($teamcfg)) return 0;
        return in_array($uid, $teamcfg['members']);
    }
    static function is_leader($tid, $uid = "\$\$myself\#\#")
    {
        if(user::is_superuserO($uid)) return 1;
        if ($uid == "\$\$myself\#\#") $uid = user::read()['name'];
        $teamcfg = self::get($tid);
        if(empty($teamcfg)) return 0;
        return in_array($uid, $teamcfg['leaders']) || user::is_superuserO($uid);
    }
    static function visiable($tid)
    {
        if (team::joinable($tid)) return 1;
        if (user::is_superuser()) return 1;
        if (self::joined($tid)) return 1;
        return 0;
    }
    static function goout($tid)
    {
        $teamconifg = team::get($tid);
        if(empty($teamconfig)) return 0;
        $uid = user::read()['name'];
        $mycfg = self::user();
        //判断是否为唯一的leader
        if (count($teamconifg['leaders']) == 1) {
            if ($uid === $teamconifg['leaders'][0]) {
                return 0;
            }
        }
        if (in_array($tid, $mycfg['joined'])) {
            $mycfg['joined'] = array_diff($mycfg['joined'], [$tid]);
            DB::putdata("team/user/" . user::read()['name'], $mycfg);
        }
        //查找members中值为$uid的元素并删除
        $teamconifg['members'] = array_diff($teamconifg['members'], [$uid]);
        $teamconifg['members'] = array_values($teamconifg['members']);
        //查找leaders中值为$uid的元素并删除
        if (team::is_leader($tid, $uid)) {
            $teamconifg['leaders'] = array_diff($teamconifg['leaders'], [$uid]);
            $teamconifg['leaders'] = array_values($teamconifg['leaders']);
        }
        return team::put($tid, $teamconifg);
        return true;
    }

    static function baned($tid,$uid="\$\$myself\#\#"){
        if ($uid == "\$\$myself\#\#") $uid = user::read()['name'];
        $teamcfg = self::get($tid);
        if(empty($teamcfg)) return 1;
        return $teamcfg['cfgto'][$uid]['ban'];
    }
}
