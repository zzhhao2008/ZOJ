<?php
class work
{
    static public function getWork($tid)
    {
        return DB::getdata("team/teamwork/$tid");
    }
    static public function putWork($tid, $data)
    {
        return DB::putdata("team/teamwork/$tid", $data);
    }
    static public function putMember($data, $uid = 0)
    {
        if ($uid === 0) {
            $uid = user::read()['name'];
        }
        return DB::putdata("team/workstatus/{$uid}", $data);
    }
    static public function getMember($uid = 0)
    {
        if ($uid === 0) {
            $uid = user::read()['name'];
        }
        return DB::getdata("team/workstatus/{$uid}");
    }
    static public function activitior()
    {
        return "data-bs-toggle=\"offcanvas\" data-bs-target=\"#worklist\" aria-controls=\"offcanvasLeft\"";
    }
}
class work_member
{
    public $myuid;
    public $worklist = array();
    public $doneall = 0;
    function init()
    {
        $this->myuid = user::read()['name'];
        $worklist = [];
        $mystatus = work::getMember($this->myuid);
        $teams = team::init()['joined'];
        foreach ($teams as $tid) {
            if (!team::joined($tid) || team::baned($tid, $this->myuid)) continue;
            $temp = work::getWork($tid)['list'];
            $worklist[$tid] = [];
            if (empty($temp)) continue;
            foreach ($temp as $k => $work) {
                if ($work['del']) continue;
                $worklist[$tid][$k] = $work;
                if (isset($mystatus[$tid][$work['id']]) && $mystatus[$tid][$work['id']]['status'] >= 1) {
                    $worklist[$tid][$k]['status'] = $mystatus[$tid][$work['id']]['status'];
                    $this->doneall = 1;
                } else {
                    $this->doneall = 0;
                }
            }
        }
        $this->worklist = $worklist;
        return $worklist;
    }
    function showmess()
    {
        if (!$this->doneall) {
            view::message("还有未完成的任务,<span " . work::activitior() . ">点我查看</span>", "团队-任务");
        }
    }
    function view()
    {
        $htmllast = "";
        foreach ($this->worklist as $tid => $works) {
            $htmllast .= "<div><h5>团队:" . team::get($tid)['name'] . "</h5>";
            foreach ($works as $work) {
                $htmllast .= "<li>".$work['desc']."</li>";
            }
            $htmllast .= "</div>";
        }
        echo <<<HTML
        <div class="offcanvas offcanvas-start navmainc" tabindex="-1" id="worklist" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">任务列表</h5>
        <button type="button" class="btn-close text-reset" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar"></button>
    </div>
    <div class="offcanvas-body problemsubbox">
        $htmllast
    </div>
    </div>
</div>
HTML;
        //$this->showmess();
    }
}
