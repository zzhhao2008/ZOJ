<?php
if (judger::auth_key($_GET['pass'])|1) {
    $sid = $_GET['sid'];
    $data=$_GET;
    unset($data['pass']);
    unset($data['sid']);
    if (judger::save_judegres($sid, $data)) {
        echo "OK";
    } else {
        echo "Fail";
    }
}
