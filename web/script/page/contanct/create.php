<?php view::header("开始交流-创建"); ?>
<form action="" class="needs-validation" novalidate>
    <div class="form-group form-floating m-1">
        <input type="text" class="form-control" id="title" placeholder="Title" name="title" required>
        <label for="title">标题</label>
        <div class="valid-feedback">OK！</div>
        <div class="invalid-feedback">请输入标题！</div>
    </div>
    <div class="form-group m-1">
        <textarea class="form-control" cols=10 id="desc" name="desc" placeholder="Comment goes here"></textarea>
        <div class="valid-feedback">OK！</div>
        <div class="invalid-feedback">请输入内容！</div>
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