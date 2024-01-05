<?php
function AuthFailed($mess = "身份验证失败")
{
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


if($_POST){
    //将POST中的name desc joinable赋值给$teamcfg
    $teamcfg['name'] = $_POST['name'];
    $teamcfg['description'] = $_POST['desc'];
    $teamcfg['joinable'] = intval($_POST['joinable']);
    if(team::put($tid,$teamcfg)){
        jsjump("?id=$tid");
        exit;
    }
    else{
        view::alert("保存失败！");
    }
}

view::header("团队管理-" . $teamcfg['name']);
if (!$teamcfg || !team::is_leader($tid)) {
    AuthFailed("你不是该团队的管理员！");
}
?>
<div class="row">
    <div class="col-sm-2 problemsubbox">
        <div>
            <h5 id="infobtn">基本信息管理</h5>
            <h5 id="memberbtn">成员管理</h5>
            <h5 id="taskbtn">团队任务管理</h5>
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
                    <input class="form-check-input" type="checkbox" id="mySwitch" name="joinable" value="1" <?=$teamcfg['joinable']===1?"checked":""?>>
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
    }
    document.getElementById("taskbtn").onclick = function() {
        tabto("task");
    }
    tabto("info");
</script>
<?php view::foot() ?>