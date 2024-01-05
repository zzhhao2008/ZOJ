<?php
$cid = $_GET['id'];
$ccfg = contest::query($cid);
$myid = user::read()['name'];
if (empty($ccfg)) {
    jsjump("contest");
    exit;
}
if (!contest::joined($ccfg['joinedusers'])) {
    if ($_POST['join'] === "join" && contest::joinable($ccfg, $myid)) {
        contest::join($cid);
        jsjump("contestshowing?id=".$cid);
    }
}
if (!contest::visiable($ccfg, $myid)) {
    view::alert("您暂时无法查看此比赛", "danger", 10000);
    view::B403();
    exit;
}
if (contest::going($ccfg)) {
    $stau = "正在进行";
} elseif (contest::end($ccfg)) {
    $stau = "已结束";
} else {
    $stau = "未开始";
}

view::header("比赛详情-" . $ccfg['title']);
?>
<main class="row">
    <div class="col-md-8 problembox">
        <!--题面显示-->
        <div>
            <?php if (contest::joined($ccfg['joinedusers'])) : ?>
                <h3><?= $ccfg['title'] ?></h3>
                <ul class="nav nav-pills nav-justified">
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:tabto('pFace')">描述</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:tabto('problemlist')">题目列表</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:tabto('chart')">排行榜</a>
                    </li>
                </ul>
                <hr>
                <div id="pFace"></div>
                <?= view::jsMdLt("pFace", $ccfg['desc']); ?>
                <div id="problemlist" style="display: none;">
                    <h4>题目列表</h4>
                    <div class="list-group">
                        <?php if(contest::going($ccfg)||contest::end($ccfg)) foreach ($ccfg['problemlist'] as $k => $v) {
                            $thisp = problems::queryproBlemConfig($v);
                            if (empty($thisp)) continue;
                        ?>
                            <a href="javascript:problem(`<?= $v ?>`,`<?= $k ?>`);tabto('problemview')" class="list-group-item list-group-item-action" style="opacity: 0.5;"><?= problems::numerToWord($k + 1), " : ", $thisp['title'] ?></a>
                        <?php } ?>
                    </div>
                </div>
                <div id="chart" style="display: none;">
                    <h4>排行榜</h4>
                    <?php
                    $chart = new contest_chart;
                    $chart->contestid = $cid;
                    $chart->init();
                    //var_dump($chart->chartdata);
                    $chart->show();

                    ?>
                </div>
                <div id="problemview" style="display: none;">
                    <h4>题目</h4>
                </div>
            <? else : ?>
                <div id="pFace"></div>
                <?= view::jsMdLt("pFace", $ccfg['desc']); ?>
                <form method="post">
                    <input value="join" name="join" type="hidden">
                    <input class="btn btn-danger" value="立即报名" type="submit">
                </form>
            <? endif; ?>
        </div>
    </div>
    <!--辅助侧边栏-右-->
    <div class="col-md-4 problemsubbox">
        <div>
            <h4><?= view::icon("stopwatch") ?>比赛时间</h4>
            <div class="timer" id='lasttime'><?= $stau ?></div>
            <code><?php echo getDate_full($ccfg['starttime']); ?></code>~<code><?php echo getDate_full($ccfg['endtime']); ?></code>
        </div>
        <div>
            <h4><?= view::icon("tags") ?>标签</h4>
            <code class="badge rounded-pill bg-primary"><?= view::icon("tag") ?><?= implode("</code> <code class=\"badge rounded-pill bg-primary\">" . view::icon("tag"), $ccfg['tag']) ?></code>
        </div>
        <div>
            <h4><?= view::icon("code-slash") ?>赛制</h4>
            <code class="badge rounded-pill bg-danger"><?php echo $ccfg['type']; ?></code>
        </div>
        <div>
            <?php if (user::is_superuser()) : ?>
                <a href="/contest_edit?pid=<?= $tid ?>" class="text-danger"><?= view::icon("pencil-square") ?>编辑</a>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php view::foot();
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
<script>
    function tabto(id) {
        document.getElementById("pFace").style.display = "none";
        document.getElementById("problemlist").style.display = "none";
        document.getElementById("chart").style.display = "none";
        document.getElementById("problemview").style.display = "none";
        document.getElementById(id).style.display = "block";
    }

    function problem(pid, tureid) {
        window.location.href = "problem?id=" + pid + "&cid=<?= $cid ?>&trueid=" + tureid;
        fetch("getcontest-p?pid=" + pid + "&cid=<?= $cid ?>")
            .then(response => response.json())
            .then(data => problemdecode(data));
    }

    function problemdecode(data) {
        viewer = document.getElementById("problemview");
        viewer.innerHTML = "";
        titlechild = document.createElement("h2");
        titlechild.innerHTML = data.title;
        viewer.appendChild(titlechild);

        hrc = document.createElement("hr");
        viewer.appendChild(hrc);

        facechild = document.createElement("div");
        facechild.innerHTML = marked.parse(data.face);
        viewer.appendChild(facechild);
        var form = document.createElement("form");
        form.id = "problemform";
        form.method = "POST";
        form.action = "";

        var pidinput = document.createElement("input");
        pidinput.type = "hidden";
        pidinput.name = "pid";
        pidinput.value = data.id;
        form.appendChild(pidinput);
        usingscee = 0
        switch (data.type) {
            case "C":
                //单项选择题
                /**
                 * data.cs:
                 * {"A":"A的内容","B":"B的内容"}
                 * <div class="form-check">
                    <input type="radio" class="form-check-input" id="radio2" name="optradio" value="option2">Option 2
                    <label class="form-check-label" for="radio2"></label>
                    </div>
                 */
                for (i in data.cs) {
                    var div = document.createElement("div");
                    div.className = "form-check";

                    var input = document.createElement("input");
                    input.type = "radio";
                    input.className = "form-check-input";
                    input.name = "option";
                    input.value = i;
                    input.id = i;
                    div.appendChild(input);

                    var label = document.createElement("label");
                    label.className = "form-check-label";
                    label.setAttribute("for", i);
                    label.innerHTML = data.cs[i];
                    div.appendChild(label);
                    form.appendChild(div);
                }

                //form.submit();
                break;
            case "P":
                editor = document.createElement("div");
                editor.innerHTML = `
                <input id="ace-1" name="answer" type="hidden">
                <pre id='codeEditor1' class="ace_editor" style="min-height:320px"><s:textarea class="ace_text-input"   cssStyle="width:97.5%;height:320px;"/></pre>
                `;
                form.appendChild(editor);
                usingscee = "c_cpp";
                break;
            case "S":
                editor = document.createElement("div");
                editor.innerHTML = `
                <input id="ace-1" name="answer" type="hidden">
                <pre id='codeEditor1' class="ace_editor" style="min-height:320px"><s:textarea class="ace_text-input"   cssStyle="width:97.5%;height:320px;"/></pre>
                `;
                form.appendChild(editor);
                usingscee = "text";
                break;
        }

        var submitor = document.createElement("input");
        submitor.type = "submit";
        submitor.value = "提交";
        submitor.className = "btn btn-primary";
        form.appendChild(submitor);

        viewer.appendChild(form);
        if (usingscee != 0) {
            initEditor(1, usingscee, 0);
            editors[1].setTheme("ace/theme/<?= $editorthemeid ?>");
        }
        import('/static/js/mathtex.js')
    }
</script>