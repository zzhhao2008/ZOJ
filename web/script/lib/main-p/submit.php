<?php
//保存提交数据
class submit
{
    //保存提交数据
    static public function saveSubmission($answer, $problemconfig)
    {
        //读取用户数据
        $mydata = user::read()['profile'];
        //如果用户数据为空，返回用户认证失败
        if (empty($mydata)) return "请登录";
        //获取提交者
        $submitor = user::read()['name'];
        //获取当前时间
        $time = time();
        //获取提交id
        $newSubmissionId = DB::getdata("submission/config")['total'] + 1;
        //获取问题id
        $problemid = $problemconfig['id'];
        //获取尝试次数
        $tryof = isset(user::read()['profile']['try'][$problemid]);
        //如果尝试次数大于0，且问题类型为S，则返回没有机会
        if ($tryof && $problemconfig['chance'] >= 1) {
            return "你已无作答机会";
        }
        //获取提交数据
        $newSubmissionName = "sids-$newSubmissionId-pids-$problemid-sors-$submitor-endd";
        $Submissiondata = array(
            "problemid" => $problemid,
            "submitor" => $submitor,
            "time" => $time,
            "answer" => $answer,
            "score" => 0,
            "id" => $newSubmissionId,
            "status" => "waiting",
        );

        //如果问题类型为S，则验证答案长度是否超限
        if ($problemconfig['type'] === 'S') {
            //验证答案长度是否超限
            if (strlen($answer) > $problemconfig['outputlimit']) {
                $Submissiondata['status'] = "error";
                $Submissiondata['reply'] = 'OLE';
                $Submissiondata['answer'] = "OLE--No Answer Has Been Saved";
            };
        }
        //如果问题类型为P，则验证答案长度是否超限
        if ($problemconfig['type'] === 'P') {
            //验证答案长度是否超限
            if (strlen($answer) > 32768) {
                $Submissiondata['status'] = "error";
                $Submissiondata['reply'] = 'OLE';
                $Submissiondata['answer'] = "OLE--No Answer Has Been Saved";
            };
        }
        //更新尝试次数
        judger::update_tyring($problemid, 0); //更新尝试次数
        //将提交数据存入数据库
        DB::putdata("submission/judgequeue/$newSubmissionName", $Submissiondata);

        //更新提交数据
        $newConfig = DB::getdata("submission/config");
        $newConfig['total']++;
        DB::putdata("submission/config", $newConfig);

        if($problemconfig['type'] === 'C'){
            judger::judge_choose($newSubmissionName);
        }
        return array("sid" => $newSubmissionName);
    }
    static public function get_submission($sid):array{
        $Submissiondata = DB::getdata("submission/completed/$sid");
        if (!empty($Submissiondata)) { 
            return $Submissiondata;
        }
        $Submissiondata = DB::getdata("submission/judgequeue/$sid");
        if (!empty($Submissiondata)) {
            return $Submissiondata;
        }
        return array();
    }
}