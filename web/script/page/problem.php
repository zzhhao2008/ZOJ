<?php view::header("题目") ?>
<?php
$vis = 0;
if ($_GET['id']) {
    $thisp = problems::queryProblem($_GET['id']);
    if (empty($thisp)) {
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
    $thisp['id'] = $tid;
    $res = submit::saveSubmission($_POST['answer'], $thisp);
    if (!is_array($res)) {
        alert($res);
    } else {
        jsjump("/submission?sid=" . $res['sid']);
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
    $problems = DB::scanData("problems/config");
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
        <div class="col-sm-8 problembox">
            <!--题面显示-->
            <div>
                <h3><?= $thisp['title'] ?></h3>
                <?php if ($thisp['type'] === 'P') : ?>
                    <div><?= view::icon("stopwatch") ?>时间限制：<?= $thisp['timelimit'] ?>ms
                        <?= view::icon("memory") ?>内存限制：<?= $thisp['memlimit'] ?>MB</div>
                <?php endif; ?>
                <hr>
                <div>
                    <?php                     if ($thisp['chance'] >= 1) {
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
                            <div class="mb-3 mt-3">
                                <textarea class="form-control" rows="5" id="comment" name="answer"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">提交</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!--辅助侧边栏-右-->
        <div class="col-sm-4 problemsubbox">
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
        </div>
    </main>

    <?php view::jsMdLt("pFace", $thisp['face']) ?>
<?php }
view::foot();
