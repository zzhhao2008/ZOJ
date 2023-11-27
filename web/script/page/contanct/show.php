<?php
$cid = $_GET['cid'];
if ($cid[0] === "P") {
    $contanctSelf = contanct::queryPContanct($cid);
} else {
    $contanctSelf = contanct::queryContanct($cid);
}
if (empty($contanctSelf)) {
    view::alert("该讨论不存在！");
    view::B404();
}
$thisproblemid = $contanctSelf['for'];
$thisproblem = problems::queryProblem($contanctSelf['for']);
view::header("交流版-" . $contanctSelf['title']);
?>
<div class="row">
    <div class="col-lg-8">
        <div class="abox mb-1">
            <h2><?= $contanctSelf['title'] ?></h2>
            <p><code><?= date("Y-m-d H:i", $contanctSelf['createTime']) ?></code>由<code><?= $contanctSelf['creator'] ?></code>创建</p>
            <hr>
            <div id="desc" class="p-2"></div>
            <?php view::jsMdLt("desc", $contanctSelf['desc']); ?>
        </div>
    </div>
    <div class="col-lg-4 problemsubbox">
        <div class="p-3">
            <h5><?= view::icon("person") ?><?= $contanctSelf['creator'] ?></h5>
            日期：<code><?= date("Y-m-d H:i", $contanctSelf['createTime']) ?></code>
            <p>题目：<a class="text-info" href="/problem?pid=<?= $thisproblemid ?>"><?= $thisproblem['title'] ?></a></p>
        </div>
        <div>
            <a style="color:pink" href="javascript::zan()" class="btn btn-light"><?=view::icon("hand-thumbs-up")?>点赞</a>
            <?php
            if ($contanctSelf['creator'] === user::read()['name'] || user::is_superuser()) : ?>
                <p><a href="/contanctmanage?cid=<?= $cid ?>" class="btn btn-primary">编辑</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php view::foot(); ?>