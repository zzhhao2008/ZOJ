<?php
if ($_GET['cid'] && $_GET['pid']) {
    $cfg = contest::query($_GET['cid']);
    if (!empty($cfg)) {
        if (contest::visiable($cfg, user::read()['name'])) {
            $pid = $_GET['pid'];
            if (in_array($pid, $cfg['problemlist'])) {
                $data=problems::queryproBlemConfig($pid,array("ans"));
                unset($data['ans']);
                echo json_encode($data,1);
                return;
            }
        }
    }
}
echo json_encode(False, 1);
