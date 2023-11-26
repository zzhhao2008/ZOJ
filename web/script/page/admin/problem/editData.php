<?
view::header("题目数据管理");
if ($_GET['pid']) {
    $thisp = problems::queryProblem($_GET['pid']);
    $tid = $_GET['pid']; //题目ID
    if ($thisp['type'] !== "P") {
        view::alert("题目类型错误,暂不支持该类型自定义评测", "danger", 9999999);
        view::foot();
        exit;
    }
    $tjm = $thisp['judge'];
} else {
    view::alert("题目ID为空！", "danger", 9999999);
    view::foot();
    exit;
}
if ($_POST) {
    $tjm = $_POST;
    $tjm['mem']=intval($tjm['mem']);
    $tjm['time']=intval($tjm['time']);
    DB::putdata("problems/judge/$tid", $tjm);
}
?>
<form method="post">
    <div class="row">
        <div class="col-8">
            <div class="form-group">
                <label for="name">评测方式</label>
                <input type="text" class="form-control" value="<?= $tjm['method'] ?>" name="method" placeholder="">
            </div>
            <div class="form-group">
                <label for="name">时间限制(ms)</label>
                <input type="number" class="form-control" value="<?= $tjm['time'] ?>" name="time" placeholder="">
            </div>
            <div class="form-group">
                <label for="name">内存限制(MB)</label>
                <input type="number" class="form-control" value="<?= $tjm['mem'] ?>" name="mem" placeholder="">
            </div>
            <div class="form-group">
                <label for="name">输出限制(MB)</label>
                <input type="number" class="form-control" value="<?= $tjm['out'] ?>" name="out" placeholder="">
            </div>
            <div id="datas">
                <h4>数据配置<a href="javascript:add()" class="btn btn-info"><?= view::icon("plus") ?></a></h4>
                <? foreach ($tjm['data'] as $k => $v) {
                    $me = $v['method'];
                    $in = $v['in'];
                    $out = $v['out'];
                    echo "<div id='data-$k'>";
                    echo "<h5># $k</h5>";
                    echo "<div class='form-group'>";
                    echo "
                    <input type='text' class='form-control' value='$me' name='data[$k][method]'>
                    ";
                    echo '<div class="form-group">
                    <label for="name">输入</label>
                    <textarea class="form-control" rows="3" name="data[' . $k . '][in]">' . $in . '</textarea>
                    </div>';
                    echo '<div class="form-group">
                    <label for="name">输出</label>
                    <textarea class="form-control" rows="3" name="data[' . $k . '][out]">' . $out . '</textarea>
                    </div>';
                    echo "</div>";
                    echo "</div>";
                }
                ?>
            </div>

        </div>
        <div class="col-sm-4  problemsubbox">
            <div>
                <input type="submit" value="保存" class="btn btn-primary">
            </div>

        </div>
    </div>
</form>
<?php view::foot() ?>
<script>
    var nowid = <?= count($tjm['data']) ?>

    function add() {
        newchild = document.createElement('div');
        newchild.id = 'data-' + nowid
        
        newchild.innerHTML = `
        <h5># `+nowid+`</h5>
        <div class="form-group">
            <input type="text" class="form-control" value="<?=$tjm['method']?>" name="data[`+nowid+`][method]">
            <div class="form-group">
                <label for="name">输入</label>
                <textarea class="form-control" rows="3" name="data[`+nowid+`][in]"></textarea>
            </div>
            <div class="form-group">
                <label for="name">输出</label>
                <textarea class="form-control" rows="3" name="data[`+nowid+`][out]"></textarea>
            </div>
            </div>
        </div>
        `;
        nowid++;
        document.getElementById('datas').appendChild(newchild);
    }
</script>