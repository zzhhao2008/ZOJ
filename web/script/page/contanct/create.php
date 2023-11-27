<?php
if($_POST['title']||$_POST['name']){
    var_dump($_POST);
}
?>
<?php view::header("开始交流-创建"); ?>
<form class="needs-validation" method="POST">
    <div class="form-group form-floating m-1">
        <input type="text" class="form-control" id="for" placeholder="PID" name="for" value="<?= $_GET['pid'] ?>" required>
        <label for="title">题目</label>
        <div class="valid-feedback">OK！</div>
        <div class="invalid-feedback">请输入题目！</div>
    </div>
    <div class="form-group form-floating m-1">
        <input type="text" class="form-control" id="title" placeholder="Title" name="title" required>
        <label for="title">标题</label>
        <div class="valid-feedback">OK！</div>
        <div class="invalid-feedback">请输入标题！</div>
    </div>
    <?view::aceeditor("","markdown",0)?>
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