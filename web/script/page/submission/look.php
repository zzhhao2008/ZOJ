<?php $sid = $_GET['sid'];
$Submissiondata = submit::get_submission($sid);
if (empty($sid)) {
    view::B404();
    exit;
}
$problemid = $Submissiondata['problemid'];
if(stripos(" ".$problemid,"Practice")>=1){
    $problemid = str_replace("Practice","",$problemid);
    $thisproblem = practice::get_common($problemid);
}
else $thisproblem = problems::queryProblem($problemid);
$problemconfig = $thisproblem;
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
        ($Submissiondata['submitor'] !== user::read()['name'] && !user::is_superuser())//不是管理员和提交者
    )
    ||$problemconfig['opencode']===1
) :
?>
    <div>
        <h3>用户答案</h3>
        <?view::aceeditor($Submissiondata['answer'],"c_cpp",1)?>
    </div>
    <div>
        <h3>评测详情</h3>
        <div class="bg-light">
            <?
            if ($Submissiondata['dataid']) {
                echo "# " . $Submissiondata['dataid'] . ":<br>";
            }
            if ($Submissiondata['err']) {
                view::aceeditor($Submissiondata['err'],"text",1) ;
            }
            if ($Submissiondata['reply']) {
                view::aceeditor($Submissiondata['reply'],"text",1) ;
            }
            ?>
        </div>
    </div>

<?php endif; ?>


<?php view::foot() ?>