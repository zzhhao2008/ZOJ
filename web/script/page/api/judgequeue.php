<?php
if(judger::auth_key($_GET['pass']))
echo judger::get_judgequeue();
else{
    //echo md5(round(time()/10) ."zsv");
}