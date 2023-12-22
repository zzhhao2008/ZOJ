<?php
class judger
{
    //更新尝试次数
    static public function update_tyring($id, $sco)
    {
        //echo $id, " ", $sco;
        //alert($sco);
        $alltry = user::read()['profile']['try'];
        $alltry[$id] = max($sco, $alltry[$id]);
        var_dump($alltry);
        user::change("try", $alltry);
        user::saveuserchange();
    }
    //更新尝试次数-系统
    static public function update_tyring_system($uid, $id, $sco)
    {
        $alltry = user::queryUser($uid);
        $alltry['try'][$id] = max($sco, $alltry['try'][$id]);
        DB::putdata("user/$uid", $alltry);
    }
    static public function update_practice_system($uid, $id, $sco)
    {
        $alltry = user::queryUser($uid);
        $alltry['practice'][$id] = max(intval($sco), $alltry['practice'][$id]);
        DB::putdata("user/$uid", $alltry);
    }
    //判断提交是否正确
    public static function judge_choose($sid)
    {
        $Submissiondata = DB::getdata("submission/judgequeue/$sid");
        if (empty($Submissiondata)) {
            return false;
        }
        $tid = $Submissiondata["problemid"];
        $answer = $Submissiondata["answer"];
        $problemconfig = DB::getdata("problems/config/$tid");
        $problemid = $tid;
        //验证答案是否与正确答案一致
        //$Submissiondata['ta']=$answer.$problemconfig['ans'];
        if ($answer === $problemconfig['ans']) {
            $Submissiondata['status'] = "success";
            $Submissiondata['reply'] = "AC";
            $Submissiondata['score'] = 100;
        } else {
            $Submissiondata['status'] = "error";
            $Submissiondata['reply'] = "WA";
            $Submissiondata['score'] = 0;
        }
        DB::putdata("submission/completed/$sid", $Submissiondata);
        DB::rmdata("submission/judgequeue/$sid");
        //更新尝试次数
        judger::update_tyring($problemid, $Submissiondata['score']);
        return true;
    }
    static public function save_judegres($sid, $data, $actually = 0)
    {
        if (!$actually) $Submissiondata = DB::getdata("submission/judgequeue/$sid");
        if (empty($Submissiondata) && ($actually === 0)) {
            return false;
        }
        $tid = $Submissiondata["problemid"];
        foreach ($data as $key => $value) :
            //echo $key.":".$value."\n";
            //赋值给$Submissiondata
            if ($key === "score") $value = floatval($value);
            $Submissiondata[$key] = $value;
        endforeach;

        DB::putdata("submission/completed/$sid", $Submissiondata);
        DB::rmdata("submission/judgequeue/$sid");
        //更新尝试次数
        judger::update_tyring_system($Submissiondata['submitor'], $tid, $Submissiondata['score']);
        return true;
    }
    static public function get_judgequeue(): string
    {
        //从 judger队列中获取所有要评测的题目，以JSON字符串返回ID
        $judgerqueue = DB::scanName("submission/judgequeue");
        foreach ($judgerqueue as $k => $v) {
            if (stripos($v, "pids-P") <= 1) {
                unset($judgerqueue[$k]);
            }
        }
        $jsons = json_encode($judgerqueue);
        return $jsons;
    }
    static public function auth_key($key)
    {
        return $key === md5(round(time() / 10) . "zsv");
    }
    
    static public function updatePractice($submissiondata, $sid = "114514")
    {
        if ($sid = "114514") {
            $sid = DB::getdata("submission/config")['total'] + 1;
            $newConfig = DB::getdata("submission/config");
            $newConfig['total']++;
            DB::putdata("submission/config", $newConfig);
            $submitor = user::read()['name'];
            $problemid=$submissiondata['problemid'];
            $sid = "sids-$sid-pid-$problemid-sors-$submitor-endd";
            $submissiondata['id'] = $sid;
            $submissiondata['submitor'] = $submitor;
            $submissiondata['time']=time();
        }
        DB::putdata("submission/completed/$sid", $submissiondata);
        judger::update_practice_system($submitor, str_replace("Practice","",$problemid),$submissiondata['score']);
        return $sid;
    }
}
