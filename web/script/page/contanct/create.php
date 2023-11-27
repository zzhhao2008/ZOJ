<?php
function successDo($id)
{
    if($_POST['dton']){
        user::change_Add("dt",array("time"=>time(),"art"=>"创建了题目 #".$_POST['for']."的讨论版（".$id."）"),1);
    }
    jsjump("/contancting?cid=" . $id);
}
function decodeRes($res)
{
    switch ($res) {
        case 1:
            view::alert("用户验证失败！", "warning");
            break;
        case 2:
            view::alert("创建失败，因为题号错误或没有权限！");
            break;
        default:
            successDo($res);
            break;
    }
}
if ($_POST['title'] && $_POST['for']) {
    if (!$_POST['private']) {
        $res = contanct::createContanct($_POST['for'], $_POST['title'], $_POST['content']);
        decodeRes($res);
    } else {
        $res = contanct::createContanctPrivate($_POST['for'], $_POST['title'], $_POST['content']);
        decodeRes($res);
    }
}
?>
<?php view::header("开始交流-创建"); ?>
<form class="needs-validation" method="POST">
    <div class="form-group form-floating m-1">
        <input type="text" class="form-control" id="for" placeholder="PID" name="for" value="<?= $_GET['pid'] ? $_GET['pid'] : $_POST['for'] ?>" required>
        <label for="title">题目</label>
        <div class="valid-feedback">OK！</div>
        <div class="invalid-feedback">请输入题目！</div>
    </div>
    <div class="form-group form-floating m-1">
        <input type="text" class="form-control" id="title" placeholder="Title" name="title" required value="<?= $_POST['title'] ?>">
        <label for="title">标题</label>
        <div class="valid-feedback">OK！</div>
        <div class="invalid-feedback">请输入标题！</div>
    </div>
    <? view::aceeditor($_POST['content'], "markdown", 0, "content") ?>
    <div style="border: 1px solid;" class="p-2 mb-1">
        <h4>自定义设置</h4>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="mySwitch" name="private" value=1 checked>
            <label class="form-check-label" for="mySwitch">不公开/私有</label>
        </div>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="mySwitch" name="dton" value=1 checked>
            <label class="form-check-label" for="mySwitch">同步到我的动态</label>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">提交</button>
</form>

<script>
    // 如果验证不通过禁止提交表单
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            // 获取表单验证样式
            var forms = document.getElementsByClassName('needs-validation');
            // 循环并禁止提交
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>


<?php view::foot(); ?>