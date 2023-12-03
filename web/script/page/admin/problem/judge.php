<?php
view::header("手动评分");
$submissionid = $_GET['sid'];
//查询submission
$sdata = submit::get_submission($submissionid);
$sdata['problem'] = problems::queryproBlemConfig($sdata['problemid']);
$sdata['judgement'] = problems::queryproBlemJudement($sdata['problemid']);
$pid = $sdata['problemid'];
if ($sdata['status'] == 'waiting') {
    //var_dump($sdata);
} else {
    view::alert("该记录已经评测过！");
    view::foot();
    exit;
}
if ($_POST['score']) {
    $data = $_POST;
    if (judger::save_judegres($submissionid, $data)) {
        jsjump("/submissions");
    } else {
        view::alert("保存失败！");
    }
}
?>
<div class="row">
    <div class="problemsubbox col-md-8">
        <div>
            <h5>题目：<a href="/problem?id=<?= $pid ?>"><?= $sdata['problem']['title'] ?></a></h5>
            <h5>提交者:<?= user::queryUserNick($sdata['submitor'], 1, 1) ?></h5>
            <h5>提交时间：<?= date("Y-m-d H:i:s", $sdata['time']) ?></h5>
        </div>
        <div>
            <h5>该题目参考答案:</h5>
            <?php view::aceeditor($sdata['problem']['ans'], 'code', 1) ?>
        </div>
        <div>
            <h5>用户答案:</h5>
            <?php view::aceeditor($sdata['answer'], 'code', 1) ?>
        </div>
    </div>
    <div class="problemsubbox col-md-4">
        <div>
            <form method="post">
                <h5>结果:</h5>
                <label for="comment">请输入评价：</label>
                <textarea class="form-control" rows="5" id="comment" name="err"></textarea>
                <label for="pwd" class="form-label">分数（满分100）:</label>
                <input type="number" min=0 max=100 class="form-control" id="pwd" placeholder="Enter" name="score" oninput="if(value>100)value=100;if(value<0)value=0">
                <input type="submit" value="确定" class="btn btn-danger">
            </form>
        </div>
    </div>
</div>


<?php view::foot(); ?>