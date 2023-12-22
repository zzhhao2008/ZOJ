<?php
function solve($submissions)
{
    $cnt = 0;
    if($submissions == null){
        return [];
    }
    foreach ($submissions as $submissionid) {
        $submission = submit::get_submission($submissionid);
        //如果提交记录为空，则跳过
        
        //获取提交记录的problem_id
        $thisproblemid = $submission['problemid'];
        //获取提交记录的submitor
        $thisuserid = $submission['submitor'];
        //获取problem_id对应的problem信息 
        if(stripos(" ".$thisproblemid,"Practice")>=1){
            $thisproblemid = str_replace("Practice","",$thisproblemid);
            $thisproblem = practice::get_common($thisproblemid);
        }
        else $thisproblem = problems::queryProblem($thisproblemid);
        //如果problem信息为空,或没有查看权限，则跳过
        if (empty($thisproblem) || ($thisproblem['hiddensubmission'] === 1  && !user::is_superuser())) continue;
        if ($_GET['pid']) {
            if ($thisproblemid != $_GET['pid']) {
                continue;
            }
        }
        if ($_GET['creator']) {
            if ($thisuserid != $_GET['creator']) {
                continue;
            }
        }
        if ($_GET['time']) {
            if (date("m-d", $submission['time']) != $_GET['time']) {
                continue;
            }
        }
        //将提交记录的problem_id，problem_title，submitor，time存入$showingsubmission
        $showingsubmission[] = array(
            "submissionid" => $submissionid,
            "problemid" => $thisproblemid,
            "problemtitle" => $thisproblem['title'],
            "submitor" => $thisuserid,
            "time" => $submission['time'],
            "score" => $submission['score']
        );
        $cnt++;
        if ($cnt >= 100) break;
    }
    if(empty($showingsubmission)) return [];
    //使用usort(),以['time']为依据对$showingsubmission进行排序
    usort($showingsubmission, function ($a, $b) {
        return $a['time'] < $b['time'];
    });
    return $showingsubmission;
}
//使用一个列表展示所有的数据
//获取数据
$submissions = DB::scanName("submission/completed");
//逆序$submissions
$showingsubmission = solve($submissions);
//遍历所有的提交记录
if (user::is_superuser()) {
    $waiting = solve(DB::scanName("submission/judgequeue"));
}
//显示标题
view::header("提交记录");
?>
<div class="abox">
    <h5>Fifter/筛选</h5>
    <form>
        <div class="row">
            <div class="col-sm-6">
                <input type="text" class="form-control flex" name="pid" placeholder="题目ID:" value="<?= $_GET['pid'] ?>">
            </div>
            <div class="col-sm-6">
                <input type="text" class="form-control flex" name="creator" placeholder="创建者" value="<?= $_GET['creator'] ?>">
            </div>
            <div class="col-sm-6">
                <input type="text" class="form-control flex" name="time" placeholder="时间(m-d)" value="<?= $_GET['time'] ?>">
            </div>
            <div class="col-sm-4">
                <input type="submit" value="筛选" class="btn btn-primary">
            </div>
        </div>
    </form>
</div>
<hr>
<div class="abox">
    <?php

    //使用HTML-table展示数据（$showingsubmission）
    echo "<table class='table table-hover' >";
    echo "<tr class='table-secondary'><th>题目</th><th>提交者</th><th>提交时间</th><th>分数</th></tr>";
    foreach ($showingsubmission as $submission) {
        $sdate = date("Y-m-d H:i:s", $submission['time']);
        $color = problems::colorsolve($submission['score']);
        echo "<tr onclick='location.href=\"/submission?sid={$submission['submissionid']}\"' class=\"table-$color\">
    <td>{$submission['problemtitle']}</td>
    <td>{$submission['submitor']}</td>
    <td>{$sdate}</td>
    <td>{$submission['score']}</td>
    </tr>";
    }
    echo "</table>";
    ?>
    <div class="text-warning">
        仅显示最近100条提交记录，等待评测以及不允许展示的提交记录除外
    </div>
    <h5>等待评测：</h5>
    <?php
    if ($waiting) {
        //使用HTML-table展示数据（$showingsubmission）
        echo "<table class='table table-hover' >";
        echo "<tr class='table-info'><th>题目</th><th>提交者</th><th>提交时间</th><th>分数</th></tr>";
        foreach ($waiting as $submission) {
            $sdate = date("Y-m-d H:i:s", $submission['time']);
            $color = problems::colorsolve($submission['score']);
            echo "<tr class=\"table-$color\">
    <td>{$submission['problemtitle']}</td>
    <td>{$submission['submitor']}</td>
    <td>{$sdate}</td>
    <td><a href='/submission?sid={$submission['submissionid']}'>查看详情<a>
    <a href='/problem_judge?sid={$submission['submissionid']}'>手动评测<a></td>
    </tr>";
        }
        echo "</table>";
    }
    ?>
</div>
<?php //显示页脚
view::foot();
