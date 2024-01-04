<?php
if($_GET['cid']){ //||judger::auth_key($_GET['pass'])
    if($_GET['get']){
        echo json_encode(contest_submission::get_all($_GET['cid']));
    }
    if($_GET['score']&&$_GET['sid']){
        $submission=contest_submission::query($_GET['cid'],$_GET['sid']);
        $submission['score']=intval($_GET['score']);
        contest_submission::put($_GET['cid'],$_GET['sid'],$submission);
    }
}