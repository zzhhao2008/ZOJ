<?php
if(judger::auth_key($_GET['pass'])){
    //读取get中的submissionid
    $submissionid = $_GET['submissionid'];
    //查询submission
    $sdata=submit::get_submission($submissionid);
    $sdata['problem']=problems::queryproBlemConfig($sdata['problemid']);
    $sdata['judgement']=problems::queryproBlemJudement($sdata['problemid']);
    //如果type等于P且status为waiting，输出JSON
    if($sdata['problem']['type']=='P' && $sdata['status']=='waiting'){
        unset($sdata['problem']);
        $sdata['judgement']['ans']=$sdata['answer'];
        echo json_encode($sdata['judgement']);
    }
    //否则输出JSON:SKIP
    else{
        echo json_encode(array('method'=>'SKIP'));
    }
}

