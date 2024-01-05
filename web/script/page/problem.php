<?php view::header("题目") ?>
<?php
$vis = 0;
if ($_GET['cid']) {
    $ccfg = contest::query($_GET['cid']);
    $cid = $_GET['cid'];
    if (!(contest::visiable($ccfg, user::read()['name']) && contest::joined($ccfg['joinedusers'], user::read()['name']))) {
        $vis = 0;
        die("无权限查看");
    }
    $trid=intval($_GET['trueid']);
    $tid = $ccfg['problemlist'][intval($_GET['trueid'])];
    $thisp = problems::queryproBlemConfig($tid);
    unset($thisp['chance']);
    $thisp['tag'] = [];
    $thisp['systag'] = [];
    $vis = 1;
    $contesting = 1;
    if (contest::going($ccfg)) {
        $stau = "正在进行";
        $vis = 1;
    } elseif (contest::end($ccfg)) {
        view::alert("比赛已结束！");
        $vis = 0;
        $stau = "已结束";
        if(problems::visable($thisp)){
            $vis=1;
        }
    } else {
        view::alert("比赛未开始！");
        $vis = 0;
        $stau = "未开始";
    }
} else if ($_GET['id']) {
    $thisp = problems::queryProblem($_GET['id']);
    if (empty($thisp)) {
        if (practice::get_common($_GET['id'])) {
            jsjump("practiceshow?id=" . $_GET['id']);
        }
        echo "<h2>抱歉，该题目不存在或被禁止访问！</h2>";
        $vis = 0;
        $thisp = problems::get404cfg(0);
    }
    $vis = 1;
    $tid = $_GET['id'];
    $mytry = user::read()['profile']['try'][$_GET['id']];
    $mytry_show = isset(user::read()['profile']['try'][$_GET['id']]) ? $mytry : "--";
}
if ($_POST['answer']) {
    if (!empty($cid)) {
        contest_submission::submit($cid, $trid, $_POST['answer']);
        view::alert("提交成功！");

    } else {
        $thisp['id'] = $tid;
        $res = submit::saveSubmission($_POST['answer'], $thisp);
        if (!is_array($res)) {
            alert($res);
        } else {
            jsjump("/submission?sid=" . $res['sid']);
        }
    }
}
if ($vis === 0) { //默认显示题目列表
    if (user::is_superuser()) {
?>
        <div class="dropdown">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                创建题目
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/problem_cr_rm?crtype=C">选择题</a></li>
                <li><a class="dropdown-item" href="/problem_cr_rm?crtype=P">编程题</a></li>
                <li><a class="dropdown-item" href="/problem_cr_rm?crtype=S">简答题</a></li>
            </ul>
        </div>
    <?php     }
    $problems = array_reverse(DB::scanData("problems/config"));
    ?>
    <table class="table table-hover">
        <thead>
            <tr class="table-info">
                <th class="thint">状态</th>
                <th class="mthint">ID</th>
                <th>题目名称</th>
                <th>题目标签</th>
                <th>难度</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($problems as $k => $v) {
                $k = $v['id'];
                if (!problems::visable($v)) continue;
                $thistry = 0;
                $thistry = user::read()['profile']['try'][$k];
                if (isset(user::read()['profile']['try'][$k])) {
                    $thissco = $thistry;
                    $c = problems::colorsolve($thistry + 0.1);
                } else {
                    $thissco = "--";
                    $c = "";
                };
                if ($thissco === 100) $thissco = "√";

            ?>
                <tr onclick="location.href='?id=<?= $k ?>'">
                    <td class="text-<?= $c ?> table-light"><?= $thissco ?></td>
                    <td class="mthint"><?= $k ?></td>
                    <td><?= $v['title'] ?></td>
                    <td>
                        <code class="badge rounded-pill bg-primary">
                            <?= view::icon("tag") ?>
                            <?= implode("</code> <code class=\"badge rounded-pill bg-primary\">" . view::icon("tag"), $v['tag']) ?>
                        </code>
                    </td>
                    <td class="table-<?= problems::colorsolve($v['difficulty'] * 33) ?>"><?= $commonProblemCfg['difficulty'][$v['difficulty']] ?></td>
                </tr>
            <?php             }
            ?>
        </tbody>
    </table>
<?php } else { //题目查看 
?>
    <main class="row">
        <div class="col-md-8 problembox">
            <!--题面显示-->
            <div>
                <h3><?= $thisp['title'] ?></h3>
                <?php if ($thisp['type'] === 'P') : ?>
                    <div><?= view::icon("stopwatch") ?>时间限制：<?= $thisp['timelimit'] ?>ms
                        <?= view::icon("memory") ?>内存限制：<?= $thisp['memlimit'] ?>MB</div>
                <?php endif; ?>
                <hr>
                <div>
                    <?php if ($thisp['chance'] >= 1) {
                        echo "<span class=\"badge rounded-pill bg-warning\">本题目限制作答1次</span>";
                    }
                    if ($mytry_show !== '--' && $thisp['chance'] >= 1) {
                        $disable = 1;
                        echo "<span class=\"badge rounded-pill bg-danger\">你已经没有作答机会！</span>";
                    }
                    ?>
                </div>
                <p class="problemFace" id="pFace">
                    <?= $thisp['face'] ?>
                </p>
                <div>
                    <?php if (contest::going($ccfg)) { ?>
                        <?php if ($thisp['type'] === 'C') problems::choose($thisp['cs'], array("disable" => $disable)); ?>
                        <?php if ($thisp['type'] === 'S') : ?>
                            <form method="post">
                                <div class="mb-3 mt-3">
                                    <label for="comment">请输入你的答案：</label>
                                    <textarea class="form-control" rows="5" id="comment" name="answer"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">提交</button>
                            </form>
                        <?php endif; ?>
                        <?php if ($thisp['type'] === 'P') : ?>
                            <button class="btn btn-primary" id='submitbtn' onclick="
                        document.getElementById('submitarea').style.display='block';
                        document.getElementById('submitbtn').style.display='none';
                        ">提交答案</button>
                            <form method="post" style="display:none" id="submitarea">
                                <? view::aceeditor("", "c_cpp", 0, "answer") ?>
                                <button type="submit" class="btn btn-primary">提交</button>
                            </form>
                    <?php endif;
                    } ?>
                </div>
            </div>
        </div>
        <!--辅助侧边栏-右-->
        <div class="col-md-4 problemsubbox">
            <?php
            if ($contesting) {
            ?>
                <div>
                    <h4><?= view::icon("stopwatch") ?>比赛时间</h4>
                    <div class="timer" id='lasttime'><?= $stau ?></div>
                    <code><?php echo getDate_full($ccfg['starttime']); ?></code>~<code><?php echo getDate_full($ccfg['endtime']); ?></code>
                </div>
                <div>
                    <a href="/contestshowing?id=<?=$cid?>">返回比赛</a>
                </div>
            <?php
            } else {
            ?>
                <div>
                    <h4><?= view::icon("person-vcard") ?>创作者</h4>
                    来自ZSV官方团队
                    <p class="text-<?= problems::colorsolve($thisp['difficulty'] * 33) ?>"></p>
                </div>
                <div>
                    <h4><?= view::icon("tags") ?>标签</h4>
                    <code class="badge rounded-pill bg-primary"><?= view::icon("tag") ?><?= implode("</code> <code class=\"badge rounded-pill bg-primary\">" . view::icon("tag"), $thisp['tag']) ?></code>
                    <code class="badge rounded-pill bg-success"><?= view::icon("justify-left") ?><?= implode("</code> <code class=\"badge rounded-pill bg-success\">" . view::icon("tag"), $thisp['systag']) ?></code>
                </div>
                <div>
                    <h4><?= view::icon("chat-left-dots") ?>详情</h4>
                    <span class="text-<?= problems::colorsolve($mytry) ?>"><?= view::icon("bookmark") ?><?= $mytry_show ?></span>
                    <a href="/contanct?pid=<?= $tid ?>" class="text-primary"><?= view::icon("chat") ?>讨论</a>
                    <a href="/submissions?pid=<?= $tid ?>" class="text-success"><?= view::icon("upload") ?>提交记录</a>
                    <?php if (user::is_superuser()) : ?>
                        <a href="/problem_edit?pid=<?= $tid ?>" class="text-danger"><?= view::icon("pencil-square") ?>编辑</a>
                    <?php endif; ?>
                </div>
            <?php } ?>
        </div>
    </main>

    <?php view::jsMdLt("pFace", $thisp['face']) ?>
<?php }
view::foot();
if (contest::going($ccfg)) { ?>
    <script>
        timerbox = document.getElementById("lasttime");
        setInterval(function() {
            var now = new Date();
            var end = new Date(<?= $ccfg['endtime'] ?> * 1000);
            var left = end.getTime() - now.getTime();
            if (left <= 0) {
                left = 0;
                timerbox.innerHTML = "比赛已结束";
                location.reload();
            }
            var h = Math.floor(left / (1000 * 60 * 60));
            var m = Math.floor(left / (1000 * 60) % 60);
            var s = Math.floor(left / 1000) % 60;
            //给不足两位的数字补零
            if (h < 10) h = "0" + h;
            if (m < 10) m = "0" + m;
            if (s < 10) s = "0" + s
            timerbox.innerHTML = "" + h + ":" + m + ":" + s;

        }, 1000);
    </script>
<?php } ?>
<style>
    .timer {
        width: 100%;
        text-align: center;
        font-size: 50px;
        font-weight: 200;
        background: rgb(255, 255, 255, 0.2);
        border-radius: 15px;
    }
</style>