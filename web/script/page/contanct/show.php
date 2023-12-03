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
if ($_GET['zan']) {
    contanct_zan::add($cid);
    echo json_encode(contanct_zan::query($cid));
    exit;
}
if ($_POST['reply']) {
    contanct_reply::add($cid, $_POST['reply']);
    view::message("发送成功,<b>请不要再刷新页面</b>");
    jsjump("contancting?cid=$cid");
}
if(isset($_GET['del'])){
    if(user::read()['name'] === $contanctSelf['creator']) $su=1;
    else $su=0;
    $res=contanct_reply::rmd($cid,$_GET['del'],$su);
    if($res){
        view::message("删除成功");
    }else{
        view::message("失败！");
    }
    jsjump("contancting?cid=$cid");
}
$thisproblemid = $contanctSelf['for'];
$thisproblem = problems::queryProblem($contanctSelf['for']);
view::header("交流版-" . $contanctSelf['title']);
$zan = contanct_zan::query($cid);
$reply = contanct_reply::getData($cid);
?>
<div class="row">
    <div class="col-lg-8">
        <div class="abox mb-1 master-b-m">
            <h2><?= $contanctSelf['title'] ?></h2>
            <p><code><?= date("Y-m-d H:i", $contanctSelf['createTime']) ?></code>由<code><?= $contanctSelf['creator'] ?></code>创建</p>
            <hr>
            <div id="desc" class="p-2"></div>
            <?php view::jsMdLt("desc", $contanctSelf['desc']); ?>
        </div>
    </div>
    <div class="col-lg-4 problemsubbox">
        <div class="p-3">
            <h5><?= view::icon("person") ?><?= user::queryUserNick($contanctSelf['creator'],1,1) ?></h5>
            发布于<code><?= date("Y-m-d H:i", $contanctSelf['createTime']) ?></code>
            <p>题目：<a class="text-info" href="/problem?pid=<?= $thisproblemid ?>"><?= $thisproblem['title'] ?></a></p>
        </div>
        <div>
            <a href="javascript:zan()" class="btn">
                <span id="zan-e" <?= $zan['my'] ? "style='display:none'" : "" ?>><?= view::icon("hand-thumbs-up") ?> </span>
                <span class="pink" id="zan-f" <?= $zan['my'] ? "" : "style='display:none'" ?>><?= view::icon("hand-thumbs-up-fill") ?> </span>
                <span id="zan"><?= $zan['cnt'] ?></span></a>
            <a href="javascript:addreply()" class="btn btn-info"><?= view::icon("message") ?>写回复</a>
            <?php
            if ($contanctSelf['creator'] === user::read()['name'] || user::is_superuser()) : ?>
                <a href="/contanctmanage?cid=<?= $cid ?>" class="btn btn-primary">编辑</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-8">
        <div class="abox my-b-m p-2" style="<?= count($reply) === 0 ? "" : "display:none" ?>" id="wrt">
            <h5>写回复</h5>
            <hr>
            <form method="post">
                <? view::aceeditor("", "markdown", 0, "reply") ?>
                <input type="submit" value="发送" class="btn btn-primary">
            </form>
        </div>
    </div>
    <?php
    usort($reply, function ($a, $b) {
        //按时间(time)降序排序
        return $b['time'] - $a['time'];
    });
    foreach ($reply as $k => $rep) :
        if(!user::is_superuser() &&$rep['del']===1) continue;
    ?>
        <p></p>
        <div class="col-sm-8">
            <div class="abox <?= $rep['submitor'] === user::read()['name'] ? "my" : ($contanctSelf['creator'] === $rep['submitor'] ?"master":"visitor") ?>-b-m p-2">
                <b style="font-size: larger;"><?= view::icon("people") ?><?= $rep['submitor'] ?></b><br>
                <code><?= date("Y-m-d H:i:s", $rep['time']) ?></code>
                <code style="float:right"># <?= $rep['floor'] ?></code>
                <?php if (($rep['submitor'] === user::read()['name'] || user::is_superuser() || user::read()['name'] === $contanctSelf['creator'])&&$rep['del']!=1) : ?>
                    <a href="?del=<?= $rep['floor']-1 ?>&cid=<?= $_GET['cid'] ?>">删除</a>
                <?php endif; ?>
                <?php
                if($rep['del']===1) echo "(已删除)";
                ?>
                <hr>
                <div id="tex<?= $k ?>"></div>
                <?= view::jsMdLt("tex$k", $rep['content']) ?>
            </div>
        </div>
    <?php endforeach; ?>

</div>
<?php view::foot(); ?>
<script>
    function zan() {
        fetch("?zan=1&cid=<?= $cid ?>")
            .then((response) => response.json())
            .then((data) => solvezan(data));
    }

    function solvezan(data) {
        document.getElementById("zan").innerHTML = data.cnt
        if (data.my) {
            document.getElementById("zan-e").style.display = "none";
            document.getElementById("zan-f").style.display = "inline";
        } else {
            document.getElementById("zan-e").style.display = "inline";
            document.getElementById("zan-f").style.display = "none";
        }
    }

    function addreply() {
        document.getElementById("wrt").style.display = "block";
    }
</script>