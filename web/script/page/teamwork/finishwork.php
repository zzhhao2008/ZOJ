<?php
$errorjson=json_encode(["status"=>0]);
if(isset($_GET['tid'])&&isset($_GET['workid'])){
    $wc=new work_member();
    $wc->init();
    if($wc->checkFinish($_GET['tid'],$_GET['workid'])){
        $wc->init();
        echo json_encode(['status'=>1,"html"=>$wc->view(1)]);
    }else{
        echo $errorjson;
    }
}else{
    echo $errorjson;
}