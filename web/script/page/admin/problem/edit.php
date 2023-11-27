<?php
if ($_GET['pid']) {
    $thisp = problems::queryProblem($_GET['pid']);
    $tid = $_GET['pid']; //题目ID
    if ($thisp['type'] === "C") {
        jsjump("problem_edit_c?pid=$tid");
    }
}
/**
 * Array
(
    [title] => A+B Problem
    [face] => #### 题目描述
    [outputlimit] => 128
    [timelimit] => 1000
    [memlimit] => 128
    [pn] => 0
    [tag] => C++,模拟
    [systag] => C++,模拟
    [ratingp] => 1
    [difficulty] => 0
)
 */
if ($_POST) {
    $thisp['title'] = $_POST['title'];
    $thisp['face'] = $_POST['face'];
    $thisp['pr'] = intval($_POST['pr']);
    $thisp['chance'] = intval($_POST['chance']);
    $thisp['outputlimit'] = intval($_POST['outputlimit']);
    $thisp['timelimit'] = intval($thisp['timelimit']);
    $thisp['memlimit'] = intval($thisp['memlimit']);
    $thisp['pn'] = intval($_POST['pn']);
    $thisp['tag'] = explode(",", $_POST['tag']);
    $thisp['systag'] = explode(",", $_POST['systag']);
    $thisp['ratingp'] = intval($_POST['ratingp']);
    $thisp['difficulty'] = intval($_POST['difficulty']);
    $thisp['hiddensubmission'] = intval($_POST['hiddensubmission']);
    $thisp['opencode'] = intval($_POST['opencode']);
    //var_dump($thisp);
    problems::save($tid, $thisp);
}
view::header("编辑题目-" . $tid);
if (empty($thisp)) :
    echo "<h2>抱歉，该题目不存在或被禁止访问！</h2>";
else :
?>

    <form method="post" class="row" id="tableA">
        <div class="col-sm-8 problembox">
            <!--题面编辑-->
            <div>
                <div class="w-100">
                    <label for="tit" class="form-label">题目 <?= $thisp['type'] ?></label>
                    <input type="text" class="form-control" id="tit" name="title" value="<?= $thisp['title'] ?>">
                </div>
                <textarea id="pFace" class="form-control" rows="15" name="face"><?= $thisp['face'] ?></textarea>
                <div class="w-50">
                    <label for="ol" class="form-label">答案长度限制(P:MB S:字符)</label>
                    <input type="text" class="form-control" id="ol" name="outputlimit" value="<?= $thisp['outputlimit'] ?>">
                </div>
                <?php if ($thisp['type'] === 'P') : ?>
                    <div class="w-50 col-6">
                        <label for="ol" class="form-label">时间限制(ms)</label>
                        <input type="number" class="form-control" id="ol" name="timelimit" value="<?= $thisp['timelimit'] ?>">
                    </div>
                    <div class="w-50 col-6">
                        <label for="ol" class="form-label">内存限制(MB)</label>
                        <input type="number" class="form-control" id="ol" name="memlimit" value="<?= $thisp['memlimit'] ?>">
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-sm-4  problemsubbox">
            <div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="ChanceSwitch" name="chance" value=1 <?= $thisp['chance'] ? "checked" : "" ?>>
                    <label class="form-check-label" for="ChanceSwitch">限制作答机会</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="ChanceSwitch" name="pr" value=1 <?= $thisp['pr'] ? "checked" : "" ?>>
                    <label class="form-check-label" for="ChanceSwitch">手工评分</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="ChanceSwitch" name="hiddensubmission" value=1 <?= $thisp['hiddensubmission'] ? "checked" : "" ?>>
                    <label class="form-check-label" for="ChanceSwitch">隐藏提交记录</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="ChanceSwitch" name="opencode" value=1 <?= $thisp['opencode'] ? "checked" : "" ?>>
                    <label class="form-check-label" for="ChanceSwitch">开放答案</label>
                </div>
                <div class="">
                    <label for="pn" class="form-label">等级限制</label>
                    <input type="number" class="form-control" id="pn" name="pn" value="<?= $thisp['pn'] ?>">
                </div>
                <div class="mb-3">
                    <label for="tag" class="form-label">标签</label>
                    <input type="text" class="form-control" id="tag" name="tag" value="<?= implode(",", $thisp['tag']) ?>">
                </div>
                <div class="mb-3">
                    <label for="systag" class="form-label">系统标签（副标签）</label>
                    <input type="text" class="form-control" id="systag" name="systag" value="<?= implode(",", $thisp['systag']) ?>">
                </div>
                <div class="mb-3">
                    <label for="rp" class="form-label">Rating变化</label>
                    <input type="number" class="form-control" id="rp" name="ratingp" value="<?= $thisp['ratingp'] ?>">
                </div>
                <label for="dft" class="form-label">难度</label>
                <select class="form-select" id="dft" name="difficulty">
                    <?php for ($i = 0; $i < 7; $i++) {
                        echo "<option value='$i' class='text-" .
                            problems::colorsolve($i * 33)
                            . "' "
                            . ($i === $thisp['difficulty'] ? "selected" : "")
                            . ">"
                            . $commonProblemCfg['difficulty'][$i] . "</option>";
                    } ?>
                </select>
            </div>
            <div>
                <input class="btn btn-primary" type="submit" value="保存">
                <button class="btn btn-danger" type="button" onclick="reflush()">重置</button>
                <a class="btn btn-info" href='problem_edit_d?pid=<?=$tid?>'>修改评测设置</a>
            </div>
        </div>
    </form>

<?php endif;
view::foot(); ?>
<style>
    .problemFace {
        min-width: 100%;
        border: none;
        min-height: 500px;
        max-width: 100%;
    }
</style>
<script>
    function reflush() {
        res = prompt("确定重置更改请输入：yes")
        if (res == "yes") {
            document.getElementById('tableA').reset();
        }
    }
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