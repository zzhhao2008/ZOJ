<?php
function AuthFailed($mess = "身份验证失败")
{
    view::header("Error-403");
    view::alert($mess, "danger");
    view::foot();
    exit;
}
$tid = $_GET['id'];
if ($tid === "create") {
    $tid = team::create();
    jsjump("?id=$tid");
    exit;
}

$teamcfg = team::get($tid);

if (!$teamcfg || !team::is_leader($tid)) {
    AuthFailed("你不是该团队的管理员！");
}

if ($_GET['quest']) {
    switch ($_GET['quest']) {
        case "getmembers":
            $mem = [];
            foreach ($teamcfg['members'] as $uid) {
                $new['nicklink'] = user::queryUserNick($uid, 1, 1);
                $new['nick'] = user::queryUserNick($uid, 0, 0);
                if (team::is_leader($tid, $uid)) {
                    $new['leader'] = 1;
                } else $new['leader'] = 0;
                if (team::baned($tid, $uid)) {
                    $new['ban'] = 1;
                } else $new['ban'] = 0;
                $new['id'] = $uid;
                $mem[] = $new;
            }
            echo json_encode($mem);
            exit;
            break;
        case "ban":
            if(team_Manage::ban($tid, $_GET['uid'])){
                echo "ok";
            }else{
                echo "fail";
            }
            exit;
            break;
        case "unban":
            if(team_Manage::unban($tid, $_GET['uid'])){
                echo "ok";
            }
            exit;
            break;
        case "kickoff":
            if(team_Manage::kickoff($tid, $_GET['uid'])){
                echo "ok";
            }
            exit;
            break;
        case "setleader":
            if(team_Manage::setleader($tid, $_GET['uid'])){
                echo "ok";
        }
            exit;
            break;
        case "unsetleader":
            if(team_Manage::unsetleader($tid, $_GET['uid'])){
                echo "ok";
            }
            exit;
            break;


    }
}


if ($_POST) {
    //将POST中的name desc joinable赋值给$teamcfg
    $teamcfg['name'] = $_POST['name'];
    $teamcfg['description'] = $_POST['desc'];
    $teamcfg['joinable'] = intval($_POST['joinable']);
    if (team::put($tid, $teamcfg)) {
        jsjump("?id=$tid");
        exit;
    } else {
        view::alert("保存失败！");
    }
}

view::header("团队管理-" . $teamcfg['name']);

?>
<div class="row">
    <div class="col-sm-2 problemsubbox">
        <div class="">
            <h5 id="infobtn">基本信息管理</h5>
            <h5 id="memberbtn">成员管理</h5>
            <h5 id="taskbtn">团队任务管理</h5>
            <h5 onclick="window.location='/teamwork'">返回</h5>
        </div>
    </div>
    <div class="col-sm-10 problembox hiddenchild">
        <div id="infobox">
            <form onclick="changed=1" method="post">
                <div>
                    <label for="name" class="form-label">团队名称：</label>
                    <input class="form-control" id="name" maxlength="25" name="name" value="<?= $teamcfg['name'] ?>" placeholder="Enter name">
                </div>
                <div>
                    <label for="desc" class="form-label">团队简介：</label>
                    <input class="form-control" id="desc" maxlength="150" name="desc" value="<?= $teamcfg["description"] ?>" placeholder="Enter description">
                </div>
                <div class="form-check form-switch m-3">
                    <input class="form-check-input" type="checkbox" id="mySwitch" name="joinable" value="1" <?= $teamcfg['joinable'] === 1 ? "checked" : "" ?>>
                    <label class="form-check-label" for="mySwitch">可以自由加入</label>
                </div>
                <input type="submit" value="保存" class="btn btn-danger">
            </form>
        </div>
        <div id="memberbox">

        </div>
        <div id="taskbox">

        </div>
    </div>
</div>
<style>
    .hiddenchild>* {
        display: none;
    }
</style>
<script>
    var changed = 0;
    var now = "";

    function tabto(id) {
        if (changed != 0 && now != id) {
            ShowMessage("Task", "请及时保存修改以免丢失", 0);
            changed = 0;
        }
        document.getElementById("infobox").style.display = "none";
        document.getElementById("memberbox").style.display = "none";
        document.getElementById("taskbox").style.display = "none";
        document.getElementById(id + "box").style.display = "block";
        document.getElementById("infobtn").className = "";
        document.getElementById("memberbtn").className = "";
        document.getElementById("taskbtn").className = "";
        document.getElementById(id + "btn").className = "active-item";
        now = id;
    }
    document.getElementById("infobtn").onclick = function() {
        tabto("info");
    }
    document.getElementById("memberbtn").onclick = function() {
        tabto("member");
        //loadmember();
        var xmlhttp;
    }
    document.getElementById("taskbtn").onclick = function() {
        tabto("task");
    }
    tabto("info");
    loadmember();

    function loadmember() {
        var xmlhttp;
        if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else { // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                data = JSON.parse(xmlhttp.responseText);
                memberox = document.getElementById("memberbox");
                memberox.innerHTML = "";
                memberox_row = document.createElement("div");
                memberox_row.className = "row";
                //渲染成员管理组件
                for (i in data) {
                    thisone = data[i];
                    thiscol = document.createElement("div");
                    thiscol.className = "col-sm-6  s-table-item";
                    contdiv = document.createElement("div");

                    nickline = document.createElement("h5"); //NICK
                    nickline.innerHTML = thisone.nicklink+"<small>("+thisone.id+")</small>";
                    contdiv.appendChild(nickline);

                    hr = document.createElement("hr");
                    contdiv.appendChild(hr);



                    if (thisone.id != `<?= addslashes(user::read()['name']) ?>`) {
                        if (thisone.leader == 1) {
                            leaderline = document.createElement("span"); //LEADER
                            leaderline.innerHTML = `<span class="badge rounded-pill bg-info">管理员</span>`;
                            contdiv.appendChild(leaderline);
                            leaderline = document.createElement("span"); //LEADER
                            leaderline.innerHTML = `<span class="badge rounded-pill bg-warning">取消管理员</span>`;
                            leaderline.onclick = function() {
                                cancelleader(thisone.id);
                            }
                            contdiv.appendChild(leaderline);
                        } else {
                            leaderline = document.createElement("span"); //SETLEADER
                            leaderline.innerHTML = `<span class="badge rounded-pill bg-success">设为管理员</span>`;
                            leaderline.onclick = function() {
                                setleader(thisone.id);
                            }
                            contdiv.appendChild(leaderline);
                        }

                        if (thisone.ban) {
                            leaderline = document.createElement("span"); //BAN
                            leaderline.innerHTML = `<span class="badge rounded-pill bg-danger">解除禁言</span>`;
                            leaderline.onclick = function() {
                                unban(thisone.id);
                            }
                            contdiv.appendChild(leaderline);
                        } else {
                            leaderline = document.createElement("span"); //BAN
                            leaderline.innerHTML = `<span class="badge rounded-pill bg-danger">禁言</span>`;
                            leaderline.onclick = function() {
                                ban(thisone.id);
                            }
                            contdiv.appendChild(leaderline);
                        }
                        leaderline = document.createElement("span"); //KickOFF
                        leaderline.innerHTML = `<span class="badge rounded-pill bg-danger">踢出团队</span>`;
                        leaderline.onclick = function() {
                            kickoff(thisone.id);
                        }
                        contdiv.appendChild(leaderline);
                    }else{
                        leaderline = document.createElement("span"); //KickOFF
                        leaderline.innerHTML = `<span class="badge rounded-pill bg-dark">我自己</span>`;
                        contdiv.appendChild(leaderline);
                    }
                    thiscol.appendChild(contdiv);
                    memberox_row.appendChild(thiscol);
                }
                memberox.appendChild(memberox_row);
            }
        }
        xmlhttp.open("GET", "?quest=getmembers&id=<?= $tid ?>", true);
        xmlhttp.send();
    }

    function kickoff(id) {
        if (confirm("确定踢出该成员？")) {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if (this.responseText == "ok") {
                        ShowMessage("SYS", "已成功将"+id+"踢出团队", "Now");
                        loadmember();
                    } else {
                        ShowMessage("SYS", "踢出成员"+id+"失败", "Now");
                    }
                }
            }
            xmlhttp.open("GET", "?quest=kickoff&id=<?= $tid ?>&uid=" + id, true);
            xmlhttp.send();
        }
    }

    function setleader(id) {
        xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText == "ok") {
                    ShowMessage("SYS", "已成功设置"+id+"为团队负责人", "Now");
                    loadmember();
                } else {
                    ShowMessage("SYS", "设置团队负责人"+id+"失败", "Now");
                }
            }
        }
        xmlhttp.open("GET", "?quest=setleader&id=<?= $tid ?>&uid=" + id, true);
        xmlhttp.send();
    }

    function cancelleader(id) {
        xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText == "ok") {
                    ShowMessage("SYS", "已成功取消"+id+"的团队负责人身份", "Now");
                    loadmember();
                } else {
                    ShowMessage("SYS", "取消"+id+"团队负责人身份失败", "Now");
                }
            }
        }
        xmlhttp.open("GET", "?quest=unsetleader&id=<?= $tid ?>&uid=" + id, true);
        xmlhttp.send();
    }

    function ban(id) {
        xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText == "ok") {
                    ShowMessage("SYS", "已成功封禁"+id+"", "Now");
                    loadmember();
                } else {
                    ShowMessage("SYS", "封禁"+id+"失败", "Now");
                }
            }
        }
        xmlhttp.open("GET", "?quest=ban&id=<?= $tid ?>&uid=" + id, true);
        xmlhttp.send();
    }

    function unban(id) {
        xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText == "ok") {
                    ShowMessage("SYS", "已成功解封"+id+"", "Now");
                    loadmember();
                } else {
                    ShowMessage("SYS", "解封成员"+id+"失败", "Now");
                }
            }
        }
        xmlhttp.open("GET", "?quest=unban&id=<?= $tid ?>&uid=" + id, true);
        xmlhttp.send();
    }
</script>
<?php view::foot() ?>