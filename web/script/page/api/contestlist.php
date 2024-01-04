<?php
$list=[];
$contests=contest::get_list(0);
foreach($contests as $contest){
    if(contest::end($contest)||contest::going($contest)){
        $list[]=$contest['id'];
    }
}
echo json_encode($list);