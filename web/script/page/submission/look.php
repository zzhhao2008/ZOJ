<?php $sid = $_GET['sid'];
$Submissiondata = submit::get_submission($sid);
if (empty($sid)) {
    view::B404();
    exit;
}
$problemid = $Submissiondata['problemid'];
$problemconfig = problems::queryProblem($problemid);
if (empty($problemconfig)) {
    view::B404();
    exit;
}
view::header("提交记录-$sid");
?>
<h1 class="text-<?= $Submissiondata['score'] >= 100 ? problems::colorsolve(100) : "danger"; ?>"><?= $Submissiondata['status'] ?></h1>
<table class="table table-hover">
    <thead>
        <tr>
            <th class="w-25">项目</th>
            <th>值</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>题目</td>
            <td><a href="/problem?id=<?= $problemid ?>"><?= $problemconfig['title'] ?></a></td>
        </tr>
        <tr>
            <td>提交时间</td>
            <td><?= date("Y-m-d H:i:s", $Submissiondata['time']) ?></td>
        </tr>
        <tr>
            <td>用户ID</td>
            <td><?= $Submissiondata['submitor'] ?></td>
        </tr>
        <tr>
            <td>状态</td>
            <td><?= $Submissiondata['status'] ?></td>
        </tr>
        <tr>
            <td>分数</td>
            <td class="table-<?= problems::colorsolve($Submissiondata['score'] + 0.1) ?>"><?= $Submissiondata['score'] ?></td>
        </tr>
    </tbody>
</table>
<?php
//如果是选择题
if (
    !(
        ($problemconfig['type'] === 'C' || $problemconfig['hiddsubmit'] === 1)
        &&
        ($Submissiondata['submitor'] !== user::read()['name'] && !user::is_superuser())
    )
) :
?>
    <div>
        <h3>用户答案</h3>
        <pre class="bg-light"><?= htmlspecialchars($Submissiondata['answer'])  ?></pre>
    </div>
    <div>
        <h3>评测详情</h3>
        <div class="bg-light">
            <?
            if ($Submissiondata['dataid']) {
                echo "# " . $Submissiondata['dataid'] . ":<br>";
            }
            if ($Submissiondata['err']) {
                echo str_replace("\n","<br>",htmlspecialchars($Submissiondata['err']));
            }
            ?>
        </div>
    </div>

<?php endif; ?>


<?php view::foot() ?>