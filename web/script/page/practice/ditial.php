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
    $mytry = user::read()['profile']['practice'][$_GET['id']];
    $mytry_show = isset(user::read()['profile']['practice'][$_GET['id']]) ? $mytry : "--";
} else {
    view::header();
    echo "<h2>抱歉，请提供ID！</h2>";
    view::foot();
    return;
}

$pcnt = 0;
$compedface = "";
$newface_lined = explode("\r\n", $thisp['face']);
foreach ($newface_lined as $line => $txt) {
    $line++;
    if ($txt[0] === '^') {
        $pcnt++;
        $cfg_line = explode(' ', $txt);
        switch ($cfg_line[0]) {
            case '^problem':
                $pcfg = problems::queryProblem($cfg_line[1]);
                switch ($cfg_line[2]) {
                    case "Face":
                        $compedface .= ($cfg_line[3] ?? ($pcnt)) . $pcfg['face'] . "\r\n";
                        break;
                    case "ChooseOnly":
                        $compedface .= ($cfg_line[3] ?? ($pcnt)) . problems::viewchoose($pcfg, $pcnt);
                        break;
                    case "FC":
                        $compedface .= ($cfg_line[3] ?? ($pcnt)) . $pcfg['face'] . "\r\n";
                        $compedface .= problems::viewchoose($pcfg, $pcnt);
                        break;
                    default:
                        $compedface .= $pcfg[$cfg_line[1]] . "\r\n";
                        break;
                }
                break;
            default:
                view::alert("题面解析器类型错误，请练习Admin，在行$line", "danger");
                break;
        }
    } else {
        $compedface .= $txt . "\r\n";
    }
}
$thisp['cmped']['num'] = $pcnt;

view::header("查看练习-" . $thisp['title']);
?>
<main class="row">
    <div class="col-sm-8 problembox">
        <!--题面显示-->
        <div>
            <h3><?= $thisp['title'] ?></h3>
            <hr>
            <div id="pFace">

            </div>
            <?php
            view::jsMdLt("pFace", $compedface);
            ?>
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
        </div>
        <div>
            <h4><?= view::icon("chat-left-dots") ?>详情</h4>
            <span class="text-<?= problems::colorsolve($mytry) ?>"><?= view::icon("bookmark") ?><?= $mytry_show ?></span>
            <?php if (user::is_superuser()) : ?>
                <a href="/problem_edit?pid=<?= $tid ?>" class="text-danger"><?= view::icon("pencil-square") ?>编辑</a>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
view::foot();
?>