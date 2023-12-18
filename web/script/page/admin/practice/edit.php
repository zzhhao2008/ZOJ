<?php
if ($_GET['id']) {
    $thisp = practice::get_common($_GET['id']);
    $tid = $_GET['id']; //交流ID
    if (empty($thisp)||$thip===[]) :
        view::header();
        echo "<h2>抱歉，该练习不存在或被禁止访问！</h2>";
        view::foot();
        return;
    endif;
}else{
    view::header();
    echo "<h2>抱歉，请提供ID！</h2>";
    view::foot();
    return;
}
/**
 * Array

    "title" => "标题",
    "face" => "题面",
    "rating" => 100, //全部完成后加分
    "showans" => 0,
    "successshow" => "成功后提示文本",
    "createtime" => time(),
    "creator" => user::read()['name'],"tag"=>[]

 */
if ($_POST) {
    $thisp['title'] = $_POST['title'];
    $newface = $_POST['face'];
    $thisp['face']=$newface;
    $thisp['pn'] = intval($_POST['pn']);
    $thisp['chance'] = intval($_POST['chance']);
    $thisp['tag'] = explode(",", $_POST['tag']);
    $thisp['rating'] = intval($_POST['rating']);
    $thisp['successshow'] = $_POST['successshow'];
    /* 题面解析器，开始 */
    $pcnt=0;
    $newface_lined=explode("\r\n",$newface);
    foreach($newface_lined as $line=>$txt){
        $line++;
        if($txt[0]==='^'){
            $pcnt++;
            $cfg_line=explode(' ',$txt);
            switch ($cfg_line[0]){
                case '^problem':
                    $pcfg=problems::queryProblem($cfg_line[1]);
                    echo "在行$line 以".$cfg_line[2]."方式引入题目".$cfg_line[1].$pcfg['title']."并编号：".($cfg_line[3]??$pcnt)."<br>";
                    break;
                default:
                    view::alert("题面解析器类型错误，请检查题面，在行$line","danger");
                    break;
            }
        }
    }
    $thisp['cmped']['num']=$pcnt;
    view::alert("共引入题目 $pcnt 个<br>");
    /** 结束 */
    practice::put_common($tid, $thisp);
}
view::header("编辑练习-" . $tid);
?>

    <form method="post" class="row" id="tableA">
        <div class="col-sm-8 problembox">
            <!--题面编辑-->
            <div>
                <div class="w-100">
                    <label for="tit" class="form-label">练习</label>
                    <input type="text" class="form-control" id="tit" name="title" value="<?= $thisp['title'] ?>">
                </div>
                <? view::aceeditor($thisp['face'],"markdown",0,"face")?>
            </div>
        </div>
        <div class="col-sm-4  problemsubbox">
            <div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="ChanceSwitch" name="chance" value=1 <?= $thisp['chance'] ? "checked" : "" ?>>
                    <label class="form-check-label" for="ChanceSwitch">限制作答机会</label>
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
                    <label for="rp" class="form-label">Rating变化</label>
                    <input type="number" class="form-control" id="rp" name="rating" value="<?= $thisp['rating'] ?>">
                </div>
            </div>
            <div>
                <input class="btn btn-primary" type="submit" value="保存">
                <button class="btn btn-danger" type="button" onclick="reflush()">重置</button>
            </div>
        </div>
    </form>

<?php
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