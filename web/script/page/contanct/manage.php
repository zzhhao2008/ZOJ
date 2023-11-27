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
if ($contanctSelf['creator'] === user::read()['name'] || user::is_superuser()) {
} else {
    view::alert("您没有权限！", "danger");
    view::B404();
}
if($_POST){
    if($_POST['title']){
        $contanctSelf['title']=$_POST['title'];
        $contanctSelf['desc']=$_POST['desc'];
        if ($cid[0] === "P") {
            contanct::putPContanct($cid,$contanctSelf);;
        } else {
            contanct::putContanct($cid,$contanctSelf);
        }
        view::message("保存成功！","System Message");
    }
    else{
        view::alert("标题不能为空！");
    }
}
$thisproblemid = $contanctSelf['for'];
$thisproblem = problems::queryProblem($contanctSelf['for']);
view::header("交流版-编辑-" . $contanctSelf['title']);
?>
<form method="post" id="tableA">
    <div class="row">
        <div class="col-lg-8">
            <div class="abox mb-1">
                <input type="text" class="form-control" id="title" placeholder="Title" name="title" required value="<?= $contanctSelf['title'] ?>">
                <p><code><?= date("Y-m-d H:i", $contanctSelf['createTime']) ?></code>由<code><?= $contanctSelf['creator'] ?></code>创建</p>
                <hr>
                <div id="desc" class="p-2"></div>
                <?php view::aceeditor(htmlspecialchars_decode($contanctSelf['desc']), "markdown", 0, "desc") ?>
            </div>
        </div>
        <div class="col-lg-4 problemsubbox">
            <div class="p-3">
                <h5><?= view::icon("person") ?><?= $contanctSelf['creator'] ?></h5>
                日期：<code><?= date("Y-m-d H:i", $contanctSelf['createTime']) ?></code>
                <p>题目：<a class="text-info" href="/problem?pid=<?= $thisproblemid ?>"><?= $thisproblem['title'] ?></a></p>
            </div>
            <div>
                <a href="/contancting?cid=<?= $cid ?>" class="btn btn-danger">取消编辑</a>
                <input class="btn btn-primary" type="submit" value="保存">
            </div>
        </div>

    </div>
</form>
<script>
    document.addEventListener("keydown", function(e) {
        //可以判断是不是mac，如果是mac,ctrl变为花键
        //event.preventDefault() 方法阻止元素发生默认的行为。
        if (e.keyCode == 83 && (navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey)) {
            e.preventDefault();
            //document.getElementById("alertbox").innerHTML = "Ctrl+S保存成功！";
            document.getElementById("tableA").submit();
        }
    }, false);
</script>
<?php view::foot(); ?>