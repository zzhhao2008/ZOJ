<?php
if ($_GET['id']) {
    $thisp = practice::get_common($_GET['id']);
    $tid = $_GET['id']; //交流ID
    if (empty($thisp) || $thip === [] || !practice::visable_common($thisp)) :
        view::header();
        echo "<h2>抱歉，该练习不存在或被禁止访问！</h2>";
        view::foot();
        return;
    endif;
    if (isset(user::read()['profile']['practice'][$_GET['id']]) && $thisp['chance'] && !user::is_superuser()) {
        view::header();
        view::alert("您已经没有作答机会");
        view::foot();
        return;
    }
    $mytry = user::read()['profile']['practice'][$_GET['id']];
    $mytry_show = isset(user::read()['profile']['practice'][$_GET['id']]) ? $mytry : "--";
} else {
    view::header();
    echo "<h2>抱歉，请提供ID！</h2>";
    view::foot();
    return;
}
if ($_POST) {
    $score = 0;
    $scoreOfOne = 100 / $thisp['cmped']['num'];
    $log = "";
    $answer = "";
    foreach ($_POST['answer'] as $k => $v) {
        $thistid = $_POST['tid'][$k];
        $thisanswer = $_POST['answer'][$k];
        $thisanswer = str_replace(' ', '', $thisanswer);
        $thisanswer = str_replace('\n', '', $thisanswer);
        $thisanswer = str_replace('\r', '', $thisanswer);
        $thistcfg = problems::queryproBlemConfig($thistid);
        $rightanswer = $thistcfg['ans'];
        if ($rightanswer === $thisanswer) {
            $score += $scoreOfOne;
            $log .= "Problem Number:" . $k . " ProblemCommonId $thistid Status:AC \r\n";
        } else {
            $score += 0;
            $log .= "Problem Number:" . $k . " ProblemCommonId $thistid Status:WA \r\n";
        }
        $answer .= $thisanswer;
    }
    $Submissiondata = array(
        "problemid" => "Practice" . $tid,
        "answer" => $answer,
        "score" => $score,
        "status" => $score >= 100 ? "AC" : "WA",
        "reply" => $log.($score >= 100 ? $thisp["successshow"] : "")
    );
    if($score >= 100) user::change_intval("rating",$thisp['rating'],1);
    $newid = judger::updatePractice($Submissiondata);
    jsjump("/submission?sid=" . $newid);
}
/*
$pcnt = 0;
$compedface = "\r\n";
$newface_lined = explode("\r\n", $thisp['face']);
foreach ($newface_lined as $line => $txt) {
    $line++;
    if ($txt[0] === '^') {
        $pcnt++;
        $cfg_line = explode(' ', $txt);
        switch ($cfg_line[0]) {
            case '^problem':
                $pcfg = problems::queryProblem($cfg_line[1]);
                if ($pcfg['type'] === "C") {
                    switch ($cfg_line[2]) {
                        case "Face":
                            echo  ($cfg_line[3] ?? ($pcnt)) . "." . $pcfg['face'] . "\r\n";
                            break;
                        case "ChooseOnly":
                            echo  ($cfg_line[3] ?? ($pcnt)) . "." . problems::viewchoose($pcfg, $pcnt);
                            break;
                        case "FC":
                            echo  ($cfg_line[3] ?? ($pcnt)) . "." . $pcfg['face'] . "\r\n";
                            echo  problems::viewchoose($pcfg, $pcnt);
                            break;
                        default:
                            echo  ($cfg_line[3] ?? ($pcnt)) . "." . $pcfg['face'] . "\r\n";
                            echo  problems::viewchoose($pcfg, $pcnt);
                            break;
                    }
                }else{
                    echo  ($cfg_line[3] ?? ($pcnt)) . ".". $pcfg['face'] . "\r\n";
                }
                break;
            default:
                view::alert("题面解析器类型错误，请练习Admin，在行$line", "danger");
                break;
        }
    } else {
        echo  $txt . "\r\n";
    }
}
*/
view::header("查看练习-" . $thisp['title']);
?>
<main class="row">
    <div class="col-sm-8 problembox">
        <!--题面显示-->
        <div>
            <h3><?= $thisp['title'] ?></h3>
            <hr>
            <form method="post">
                <div id="pFace">
                    <?php
                    $pcnt = 0;
                    $compedface = "\r\n";
                    $newface_lined = explode("\r\n", $thisp['face']);
                    foreach ($newface_lined as $line => $txt) {
                        $line++;
                        if ($txt[0] === '^') {
                            $pcnt++;
                            $cfg_line = explode(' ', $txt);
                            switch ($cfg_line[0]) {
                                case '^problem':
                                    $pcfg = problems::queryProblem($cfg_line[1],1);
                                    $thistid = ($cfg_line[3] ?? ($pcnt));
                                    $facetxt = "<div id='faceof$thistid'>" . $thistid . "." . $pcfg['face'] . "</div>\r\n" . view::jsMdLt_GetOnly("faceof$thistid", 1);
                                    if ($pcfg['type'] === "C") {
                                        switch ($cfg_line[2]) {
                                            case "Face":
                                                echo  $facetxt;
                                                break;
                                            case "ChooseOnly":
                                                echo $thistid . "." . problems::viewchoose($pcfg, $pcnt, $cfg_line[1]);
                                                break;
                                            case "FC":
                                                echo  $facetxt;
                                                echo  problems::viewchoose($pcfg, $pcnt, $cfg_line[1]);
                                                break;
                                            default:
                                                echo  $facetxt;
                                                echo  problems::viewchoose($pcfg, $pcnt, $cfg_line[1]);
                                                break;
                                        }
                                    } else {
                                        echo $facetxt;
                                        echo <<<HTML
<a href="problem?id={$cfg_line[1]}" class="btn btn-info">查看此题目详情并提交</a>
HTML;
                                    }
                                    break;
                                default:
                                    view::alert("题面解析器类型错误，请联系Admin，在行$line （Error Function Type）", "danger");
                                    break;
                            }
                        } else {
                            echo "<div id='txteof$line'>$txt</div>\r\n" . view::jsMdLt_GetOnly("txteof$line", 1);
                            //echo  $txt . "\r\n";
                        }
                    } ?>
                    <input type="submit" class="btn btn-primary" value="一键提交所有选择题">
                </div>
            </form>
        </div>
    </div>
    <!--辅助侧边栏-右-->
    <div class="col-sm-4 problemsubbox">
        <div>
            <h4><?= view::icon("person-vcard") ?>创作者</h4>
            来自<?= user::queryUserNick($thisp['creator'], 1, 1) ?>
        </div>
        <div>
            <h4><?= view::icon("tags") ?>标签</h4>
            <code class="badge rounded-pill bg-primary"><?= view::icon("tag") ?><?= implode("</code> <code class=\"badge rounded-pill bg-primary\">" . view::icon("tag"), $thisp['tag']) ?></code>
            <?php if ($thisp['chance']) { ?>
                <code class="badge rounded-pill bg-danger">有次数限制</code>
            <? } ?>
        </div>
        <div>
            <h4><?= view::icon("chat-left-dots") ?>详情</h4>
            <span class="text-<?= problems::colorsolve($mytry) ?>"><?= view::icon("bookmark") ?><?= $mytry_show ?></span>
            <?php if (user::is_superuser()) : ?>
                <a href="/practice_editor?id=<?= $tid ?>" class="text-warning"><?= view::icon("pencil-square") ?>编辑</a>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
view::foot();
?>