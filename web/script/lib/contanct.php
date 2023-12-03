<?php
class contanct
{
    public static function getContanctList_Problems($returnRaw = 0, $page = 0, $limit = 50)
    {

        if ($returnRaw === 1) {
            return DB::scanData("contanct/problem", $page, $limit, 0);
        } else if ($returnRaw === 2) {
            return DB::scanData("contanct/problem", $page, $limit, 1);
        } else {
            return DB::scanName("contanct/problem");
        }
    }
    public static function getContanctList_Private($returnRaw = 0, $page = 0, $limit = 50)
    {
        if ($returnRaw == 1) {
            return DB::scanData("contanct/private", $page, $limit,0);
        } else if ($returnRaw == 2) {
            return DB::scanData("contanct/private", $page, $limit,1);
        } else {
            return DB::scanName("contanct/private");
        }
    }
    public static function queryContanct($cid)
    {
        return DB::getData("contanct/problem/$cid");
    }
    public static function putContanct($cid, $data)
    {
        return DB::putData("contanct/problem/$cid", $data);
    }
    public static function putPContanct($cid, $data)
    {
        return DB::putData("contanct/private/$cid", $data);
    }
    public static function queryPContanct($cid)
    {
        return DB::getData("contanct/private/$cid");
    }
    public static function getConfig()
    {
        return DB::getData("contanct/config");
    }
    public static function putConfig($new)
    {
        return DB::putData("contanct/config", $new);
    }
    public static function getContanctCount($id)
    {
        $config = contanct::getConfig();
        if ($config[$id]) {
            return $config[$id];
        }
        return 0;
    }
    /**
     * ErrorCode:
     * 0: 成功
     * 1: 失败(Auth)
     * 2: 失败(ForEmpty)
     * 
     */
    public static function createContanct($for, $title, $desc)
    {
        global $mypower;
        if ($mypower <= 0) {
            return 1;
        }
        if (problems::queryProblem($for) === array()) {
            return 2;
        }
        $config = contanct::getConfig();
        $config[$for] = contanct::getContanctCount($for) + 1;
        contanct::putConfig($config);
        $username = user::read()['name'];
        $id = contanct::getContanctCount($for) . "-$for-" . $username;
        $newContanct = array(
            "id" => $id,
            "for" => $for,
            "creator" => $username,
            "createTime" => time(),
            "title" => $title,
            "desc" => $desc
        );
        contanct::putContanct($id, $newContanct);
        return $id;
    }
    public static function createContanctPrivate($for, $title, $desc)
    {
        global $mypower;
        if ($mypower <= 0) {
            return 1;
        }
        if (problems::queryProblem($for) === array()) {
            return 2;
        }
        $config = contanct::getConfig();
        $config["P" . $for] = contanct::getContanctCount("P" . $for) + 1;
        contanct::putConfig($config);
        $username = user::read()['name'];
        $id = "P-" . contanct::getContanctCount("P" . $for) . "-$for-" . $username;
        $newContanct = array(
            "id" => $id,
            "for" => $for,
            "creator" => $username,
            "createTime" => time(),
            "title" => $title,
            "desc" => $desc
        );
        contanct::putPContanct($id, $newContanct);
        return $id;
    }
}
class contanct_zan
{
    public static function getData($cid)
    {
        return DB::getdata("contanct/zan/$cid");
    }
    public static function putZan($cid, $cfg)
    {
        return DB::putdata("contanct/zan/$cid", $cfg);
    }
    public static function query($cid)
    {
        $cfg = contanct_zan::getData($cid);
        if (!$cfg['cnt']) $cfg['cnt'] = 0;
        $res = ["my" => 0, "cnt" => $cfg['cnt']];
        if ($cfg === array()) {
            return $res;
        }
        $myid = user::read()['name'];
        if ($cfg['list'][$myid] >= 0) {
            $res['my'] = 1;
        }
        return $res;
    }
    public static function add($cid)
    {
        $cfg = contanct_zan::getData($cid);
        $myid = user::read()['name'];
        if ($cfg['list'][$myid] >= 0) {
            $cfg['list'][$myid] = -1 * time();
            $cfg['cnt']--;
        } else {
            $cfg['list'][$myid] = time();
            $cfg['cnt']++;
        }
        return contanct_zan::putZan($cid, $cfg);
    }
}
class contanct_reply
{
    public static function getData($cid)
    {
        return DB::getdata("contanct/reply/$cid");
    }
    public static function putData($cid, $cfg)
    {
        return DB::putdata("contanct/reply/$cid", $cfg);
    }
    public static function query($cid)
    {
    }
    public static function add($cid, $c)
    {
        $raw = contanct_reply::getData($cid);
        $myid = user::read()['name'];
        $raw[] = array(
            "submitor" => $myid,
            "time" => time(),
            "content" => $c,
            "floor" => count($raw) + 1
        );
        return contanct_reply::putData($cid, $raw);
    }
    public static function rmd($cid, $rid,$sudo=0)
    {
        $raw = contanct_reply::getData($cid);
        $myid = user::read()['name'];
        if ($raw[$rid]['submitor'] == $myid || $sudo == 1 ||user::is_superuser()) {
            if ($raw[$rid]['del'] == 1) return 1;
            $raw[$rid]['del'] = 1;
            return contanct_reply::putData($cid, $raw);
        } else {
            return 0;
        }
    }
}
