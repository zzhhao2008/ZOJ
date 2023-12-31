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
            "type" => "OPEN",
            "joinedusers" => [],
            "tag" => []
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
        contest_submission::createbase($id);
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
                "showtruescore" => 0,
                "showturchart" => 0,
                "joinable" => 0 //可以中途加入
            ),
            "ACM" => array(
                "showtruescore" => 1,
                "showturchart" => 0,
                "joinable" => 0
            ),
            "IOI" => array(
                "showtruescore" => 1,
                "showturchart" => 1,
                "joinable" => 0
            ),
            "OPEN" => array(
                "showtruescore" => 1,
                "showturchart" => 1,
                "joinable" => 1
            ),
            "OOI"=>array(
                "showtruescore" => 0,
                "showturchart" => 1,
                "joinable" => 1
            )
        );
        return $def[$type];
    }
    public static function joinable($contestcfg, $uid = "")
    {
        $upower = user::queryUser($uid)['power'];
        if ($contestcfg['powerneed'] > $upower) return 0;
        if ($contestcfg['endtime'] < time()) { //比赛已结束
            return 0;
        }
        if (self::ContestConfig_Default($contestcfg['type'])['joinable'] === 1) { //比赛没有结束并且可以中途加入
            return 1;
        }
        if ($contestcfg['starttime'] > time()) {
            return 1;
        }
        return 0;
    }
    public static function  visiable($contestcfg, $uid = "")
    {
        if (user::is_superuserO($uid)) { //超级用户
            return true;
        }
        if (self::joinable($contestcfg, $uid)) { //可以报名
            return 1;
        }
        $upower = user::queryUser($uid)['power'];
        if (in_array($uid, $contestcfg['joinedusers'])) {  //已经报名
            return 1;
        }
        if (
            $contestcfg['powerneed'] > $upower ||
            self::going($contestcfg)
        ) return 0; //权限不足或者比赛正在进行
        else return 1; //可以访问
        return 0;
    }
    public static function joined($users)
    {
        if (user::is_superuser()) return true;
        return in_array(user::read()['name'], $users);
    }
    public static function going($contestcfg)
    {
        return $contestcfg['starttime'] < time() && $contestcfg['endtime'] > time() && !$contestcfg['paused'];
    }
    public static function end($contestcfg)
    {
        return $contestcfg['endtime'] < time();
    }
    public static function join($contestid)
    {
        $contestcfg = self::query($contestid);
        
        $contestcfg['joinedusers'][] = user::read()['name'];
        self::put($contestid, $contestcfg);
        return 1;
    }
}
class contest_submission
{
    public static function query($cid, $sid)
    {
        return DB::getdata("/contest/submissions/$cid/$sid");
    }
    public static function put($cid, $sid, $sdata)
    {
        return DB::putdata("/contest/submissions/$cid/$sid", $sdata);
    }
    public static function get_all($cid, $sx = [])
    {
        $data = DB::scanData("/contest/submissions/$cid");
        //开始筛选数据,按照$sx，如$sx['submitor']="abc"代表
        //筛选出所有提交者是abc的提交
        if (empty($sx)) return $data; //没有筛选条件
        $result = [];
        foreach ($data as $k => $v) {
            foreach ($sx as $k1 => $v1) {
                if ($v[$k1] != $v1) break 2;
            }
            $result[$k] = $v;
        }
        return $result;
    }
    public static function createbase($cid)
    {
        return DB::createbase("/contest/submissions/", $cid);
    }
    public static function submit($cid,$pid,$ans)
    {
        $submitor=user::read()['name'];
        $ccfg=contest::query($cid);
        if(!contest::joined($ccfg['joinedusers'])) return 0; //没有参加比赛
        $trueid=$ccfg['problemlist'][$pid];
        if(empty($trueid)) return 0; //题目不存在
        $subdata = array(
            "answer"=>$ans, 
            'problemid' => $pid,
            'submitor' => user::read()['name'],
            'score' => 0,
            'trueid' => $trueid,
        );
        $subid=count(DB::scanName("/contest/submissions/$cid"))+1;
        $subid="$cid-$submitor-$subid";
        return self::put($cid,$subid,$subdata);
    }
}
class contest_chart
{
    public $chartdata = [];
    public $contestid = "";
    public $showtruth;
    public $cfg = [];
    public function  init()
    {
        $contestcfg = contest::query($this->contestid);
        if (empty($contestcfg)) {
            return false;
        }
        $this->cfg = $contestcfg;
        $submissiondata = contest_submission::get_all($this->contestid);
        $cnt = [];

        $this->showtruth = $this->showtruth ?? contest::ContestConfig_Default($contestcfg['type'])['showturchart'];
        foreach ($submissiondata as $v) {
            $submitor = $v['submitor'];
            $tid = $v['problemid'];
            if ($v['trueid'] !== $contestcfg["problemlist"][$tid]) {
                continue;
            }
            if(!isset($cnt[$submitor]['scoreof'][$tid])){
                $cnt[$submitor]['scoreof'][$tid] = $v['score'];
            }
            else $cnt[$submitor]['scoreof'][$tid] = max($cnt[$submitor]['scoreof'][$tid], $v['score']);
            if ($this->showtruth == 0) {
                $cnt[$submitor]['scoreof'][$tid] = 100;
            }
        }
        $sortingdata = [];
        foreach ($cnt as $k => $v) {
            if (in_array($k, $contestcfg['joinedusers']) || user::is_superuserO($k)) {
                $sortingdata[$k] = array(
                    "scoreof" => $v['scoreof'],
                    "totalscore" => array_sum($v['scoreof']),
                    "uid" => $k
                );
            }
        }
        //按totalscore降序排列
        uksort($sortingdata, function ($a, $b) {
            if ($a['totalscore'] == $b['totalscore']) {
                return 0;
            }
            return ($a['totalscore'] > $b['totalscore']) ? -1 : 1;
        });
        //按uid升序排列
        $this->chartdata = $sortingdata;
        return true;
    }
    function show()
    {
        echo "<div id='chart'><table class='table table-hover' >";
        echo "<tr class='table-success'><th>提交者</th>";
        foreach ($this->cfg['problemlist'] as $k => $v) {
            echo "<th>" . problems::numerToWord($k + 1) . "</th>";
        }
        echo "<th>总分</th></tr>";
        foreach ($this->chartdata as $v) {
            echo "<tr>";
            echo "<td>" . user::queryUserNick($v['uid'], 1, 1) . "</td>";
            foreach ($this->cfg['problemlist'] as $k => $sco) {
                $sco = $v["scoreof"][$k];
                if (!isset($v["scoreof"][$k])) echo "<td>--";
                else if ($sco === 0) echo "<td class='table-danger'>$sco";
                else if ($sco > 0 && $sco < 100) echo "<td class='table-warning'>$sco";
                else echo "<td class='table-success'>$sco";
                echo "</td>";
            }
            echo "<td>{$v['totalscore']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    }
}
