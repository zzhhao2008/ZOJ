<?php
$myteams = team::init();
if ($_GET['no']) {
    if(!team::get($_GET['catch'])) echo 0;
    else if(!team::joined($_GET['catch'])) echo 0;
    else echo json_encode(team::get($_GET['catch']));
    exit;
}
if ($_GET['goout'] && isset($_GET['id'])) {
    if(team::goout($_GET['id'])){
        jsjump("/teamwork");
        exit;
    }
    else{
        view::alert("失败","danger");
    }
}
if ($_GET['join'] && isset($_GET['id'])) {
    if (team::joinable($_GET['id'])) {
        team::join($_GET['id']);
        jsjump("/teamwork");
    }
    else {
        view::alert("失败，团队未公开");
    }
    exit;
}
view::header("团队控制台"); ?>
<div class="row">
    <div class="col-sm-12 problemsubbox">
        <div style="text-align: center;">
            <h3>团队控制台</h3>
        </div>
        <hr>
    </div>
    <div class="col-sm-8 problembox">
        <div id="defaultpage">
            <h3>我的团队</h3>
            <div class="row">
                <?php
                foreach ($myteams['joined'] as $v) {
                    if (!team::visiable($v)) {
                        continue;
                    }
                    $tcfg = team::get($v);
                ?>
                    <div class="col-sm-6 s-table-item">
                        <div>
                            <h5 style="margin: 0;" onclick="gototeam(<?= $v ?>)"><?= $tcfg['name'] ?></h5>
                            <?php
                            if (team::is_leader($v)) echo '<span class="badge rounded-pill bg-danger">
                            <a class="text-light" href="team/manage?id=' . $v . '">管理</a></span>';
                            if (team::joinable($v)) echo '<span class="badge rounded-pill bg-success">公开</span>';
                            ?>
                            <span class="badge rounded-pill bg-primary">成员:<?= count($tcfg['members']) ?></span>
                            <span class="badge rounded-pill bg-info"><?= ($tcfg['type']) ?></span>
                            <hr style="margin: 1px;">
                            <?= $tcfg['description'] ?>
                        </div>

                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <div id="teamviewer" style="display: none;">

        </div>
    </div>
    <div class="col-sm-4 problemsubbox">
        <div>
            <h4>管理</h4>
            <div id="c-default">
                <a href="/team/manage?id=create" class="btn btn-info">创建</a>
                <form>
                    <input name="id" class="form-control" placeholder="团队ID" type="number">
                    <input name="join" value="1" type="hidden">
                    <input class="btn btn-primary" type="submit" value="加入">
                </form>
            </div>
            <div id="subviewer" style="display: none;"></div>
        </div>
    </div>
</div>
<?php view::foot(); ?>
<script>
    var userid = `<?= user::read()['name'] ?>`;
    var viewer = document.getElementById("teamviewer");
    var subviewer = document.getElementById("subviewer");
    var defaultsub = document.getElementById("c-default");
    var defaultpage = document.getElementById("defaultpage");

    function gototeam(id) {
        defaultpage.style.display = "none";
        defaultsub.style.display = "none";
        viewer.style.display = "block";
        subviewer.style.display = "block";
        viewer.innerHTML = "";
        subviewer.innerHTML = "";
        fetch("?catch=" + id + "&no=NO" + id)
            .then(response => response.json())
            .then(data => gototeamview(data))
    }

    function gototeamview(data) {
        console.log(data)
        if(data==0){
            backtodefault();
            return;
        }
        var op = 0;

        backbutton = document.createElement("p");
        backbutton.innerHTML = '<返回';
        backbutton.onclick = function() {
            backtodefault()
        }
        viewer.appendChild(backbutton);

        titlediv = document.createElement("h4");
        titlediv.innerHTML = data.name;
        viewer.appendChild(titlediv);
        viewer.appendChild(document.createElement("hr"));


        titlediv = document.createElement("h5");
        titlediv.innerHTML = "描述";
        viewer.appendChild(titlediv);
        viewer.appendChild(document.createElement("hr"));
        descdiv = document.createElement("p");
        descdiv.style.wordBreak = 'break-all';
        descdiv.innerHTML = data.description;
        viewer.appendChild(descdiv);


        titlediv = document.createElement("h5");
        titlediv.innerHTML = "成员";
        viewer.appendChild(titlediv);
        viewer.appendChild(document.createElement("hr"));

        memberdiv = document.createElement("ul");
        for (i in data.members) {
            child = document.createElement("li");
            child.innerHTML = "<a target='_blank' class='text-primary' href='/visituser?uid=" + data.members[i] + "'>" +
                data.members[i] + "</a>";
            memberdiv.appendChild(child);
        }
        viewer.appendChild(memberdiv);

        titlediv = document.createElement("h5");
        titlediv.innerHTML = "管理员";
        viewer.appendChild(titlediv);
        viewer.appendChild(document.createElement("hr"));

        memberdiv = document.createElement("ul");
        for (i in data.leaders) {
            child = document.createElement("li");
            child.innerHTML = "<a target='_blank' class='text-danger' href='/visituser?uid=" + data.leaders[i] + "'>" +
                data.leaders[i] + "</a>";
            memberdiv.appendChild(child);
            if (data.leaders[i] == userid) op = 1
        }
        viewer.appendChild(memberdiv);

        if (op == 1) {
            opb = document.createElement("a");
            opb.innerHTML = "管理";
            opb.classList.add("btn");
            opb.classList.add("btn-primary");
            opb.href = "/team/manage?id=" + data.id;
            subviewer.appendChild(opb);
        }

        opb = document.createElement("a");
        opb.innerHTML = "退出";
        opb.classList.add("btn");
        opb.classList.add("btn-danger");
        opb.href = "?goout=1&id=" + data.id;
        subviewer.appendChild(opb);
    }

    function backtodefault(flush = 0) {
        if (flush) document.reload();
        defaultpage.style.display = "block";
        defaultsub.style.display = "block";
        viewer.style.display = "none";
        subviewer.style.display = "none";
    }
</script>