<?php //使用一个列表展示所有的数据
//获取数据
$submissions = DB::scanName("submission/completed");
//逆序$submissions
$submissions = array_reverse($submissions);
$showingsubmission = [];
//遍历所有的提交记录
$cnt=0;
foreach ($submissions as $submissionid) {
    $submission = submit::get_submission($submissionid);
    //如果提交记录为空，则跳过
    //获取提交记录的problem_id
    $thisproblemid = $submission['problemid'];
    //获取提交记录的submitor
    $thisuserid = $submission['submitor'];
    //获取problem_id对应的problem信息
    $thisproblem = problems::queryProblem($thisproblemid);
    //如果problem信息为空，则跳过
    if (empty($thisproblem)||$thisproblem['hiddensubmission']===1) continue;
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
    if($cnt>=100) break;
}
//使用usort(),以['time']为依据对$showingsubmission进行排序
usort($showingsubmission, function ($a, $b) {
    return $a['time'] < $b['time'];
});
//显示标题
view::header("提交记录");
//使用HTML-table展示数据（$showingsubmission）
echo "<table class='table table-hover' >";
echo "<tr class='table-secondary'><th>题目</th><th>提交者</th><th>提交时间</th><th>分数</th></tr>";
foreach ($showingsubmission as $submission) {
    $sdate = date("Y-m-d H:i:s", $submission['time']);
    $color=problems::colorsolve($submission['score']);
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
<?php //显示页脚
view::foot();
