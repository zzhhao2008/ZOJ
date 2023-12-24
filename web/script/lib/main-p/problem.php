<?php
$commonProblemCfg = array(
    "fullcsore" => 100,
    "emptyscore" => 0,
    "difficulty" => array(
        "J入门", "C一般", "M中等", "H较难", "V困难", "Dinner", "暂无评定"
    ),
);
class problems
{
    public static function get404cfg()
    {
        return array(
            'id' => '',
            'ctime' => 1699192049,
            'type' => 'G',
            'face' => '',
            'title' => '404 Notfound',
            'pn' => 0,
            'ratingp' => 0,
            'chance' => 0,
            'tag' =>
            array(
                ''
            ),
            'systag' =>
            array(
                0 => '',
            ),
            'difficulty' => 0,
            'ans' => '',
            'cs' =>
            array(),
            'judge' =>
            array(),
            'pr' => 0,
        );
    }
    public static function colorsolve($sco)
    {
        // 判断sco的值，返回不同的颜色
        if ($sco === 0) return "";
        if ($sco <= 33) return "danger";
        elseif ($sco <= 66) return "warning";
        elseif ($sco <= 99.11) return "primary";
        elseif ($sco <= 132) return "success";
        elseif ($sco <= 165) return "info";
        return "secondary";
    }
    /**
     * C:选择
     * P:编程
     * S:填空
     */
    public static function checktype($type)
    {
        $alt = ['C', 'P', 'S'];
        if (!in_array($type, $alt)) {
            return 0;
        }
        return 1;
    }
    public static function common($type, $movement)
    {
        $common = DB::getdata("problems/common");
        $common[$type . "cnt"] += $movement;
        $common['totalcnt'] += $movement;
        var_dump($common);
        DB::putdata("problems/common", $common);
        return $common[$type . "cnt"];
    }
    public static function creatProblem($type)
    {
        if (problems::checktype($type)) {
            $id = $type . problems::common($type, 1);
            $problemcfg = array(
                "id" => $id,
                "ctime" => time(), //创建时间
                "type" => $type, //类型
                "face" => "题目",
                "title" => "新题目$id",
                "pn" => 0, //需要的等级
                "ratingp" => 1, //Rating加减
                "chance" => 0, //机会限制
                "tag" => array(), //标签
                "systag" => array(), //系统标签
                "difficulty" => 6
            );
            if ($type === 'C') {
                $problemcfg['ans'] = "E";
                $problemcfg['cs'] = array("A" => "A", "B" => "B", "C" => "C", "D" => "D");
                $problemcfg['chance'] = 1;
            }
            if ($type === 'P') {
                $problemcfg['timelimit'] = 1000;
                $problemcfg['memlimit'] = 128;
                $problemcfg['outputlimit'] = 128;
                $judge = array(
                    'method' => 'ncmp',
                    'time' => 1000,
                    'mem' => 128,
                    'out' => 64,
                    'data' =>
                    array(
                
                        array(
                            'method' => 'ncmp',
                            'in' => '',
                            'out' => '',
                        ),
                    )
                );
                DB::putdata("problems/judge/$id", $judge);
            }
            if ($type === 'S') {
                $problemcfg['outputlimit'] = 2048;
            }
            DB::putdata("problems/config/$id", $problemcfg);
            return $id;
        }
        return 0;
    }
    public static function visable($cfg)
    {
        global $mypower;
        if (isset($cfg['unlink']) && $cfg['unlink'] >= 1) return 0;
        if (user::is_superuser()) return 1;
        if (isset($cfg['contestneed']) && $cfg['contestneed']) return 0;
        if ($cfg['pn'] <= $mypower) return 1;
    }
    public static function queryproBlemConfig($id)
    {
        return DB::getdata("problems/config/$id");
    }
    public static function queryproBlemJudement($id)
    {
        return DB::getdata("problems/judge/$id");
    }
    /** 
     * 获取题目信息
     */
    public static function queryProblem($id,$author = false)
    {
        $cf = problems::queryproBlemConfig($id);
        $ju = problems::queryproBlemJudement($id);
        if (!$cf || (!problems::visable($cf)&&!$author)) return array();
        else {
            $cf['judge'] = $ju;
        }
        return $cf;
    }
    public static function numerToWord($n)
    {
        $array = ['Z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $ans = '';
        while ($n > 26) {
            $ans = $array[$n % 26] . $ans;
            $n = floor($n / 26);
        }
        $ans = $array[$n] . $ans;
        return $ans;
    }
    /**
     * 显示一个单独选择题的提交表单
     */
    public static function choose($choices, $cfg = array())
    {
        echo "<form method='post'>";
        $n = count($choices);
        for ($i = 1; $i <= $n; $i++) {
            $now = problems::numerToWord($i);
            $nc = $choices[$now];
            echo "<div class='form-check'>
                <input type='radio' class='form-check-input' id='radio$i' name='answer' value='$now'>
                <label class='form-check-label' for='radio$i' id='labal$now'>$now.$nc</label>
            </div>";
            view::jsMdLt("labal$now",$now.".".$nc);
        }
        if (!$cfg['disable']) echo "<button type='submit' class='btn btn-primary mt-3'>提交</button>";
        echo "</form>";
    }
    /**
     * 多选择题选项显示器
     */
    public static function viewchoose($problem, $idnow,$tid=0)
    {
        $choices = $problem["cs"];
        $n = count($choices);
        $out='';
        for ($i = 1; $i <= $n; $i++) {
            $now = problems::numerToWord($i);
            $nc = $choices[$now];
            $out.= "<div class='form-check'>
                <input type='radio' class='form-check-input' id='radio$idnow-$i' name='answer[$idnow]' value='$now'>
                <label class='form-check-label' for='radio$idnow-$i' id='labal-$idnow-$now'>$now.$nc</label>
            </div>".
            view::jsMdLt_GetOnly("labal-$idnow-$now",1);
        }
        echo "<input type='hidden' name='tid[$idnow]' value='$tid'>";
        return $out;
    }
    static function save($id, $data)
    {
        if ($data['judge']) {
            DB::putdata("problems/judge/" . $id, $data['judge']);
            unset($data['judge']);
        }

        DB::putdata("problems/config/" . $id, $data);
    }
    public static function viewchooseeditor($problem)
    {
        $choices = $problem["cs"];
        $n = count($choices);
        for ($i = 1; $i <= $n; $i++) {
            $now = problems::numerToWord($i);
            $nc = $choices[$now];
            echo "<div class='input-group mb-2'>
                <span class='input-group-text'>$now</span>
                <input type='text' class='form-control' placeholder='$now' value='$nc' name='choose[$now]'>
            </div>";
        }
    }
}
