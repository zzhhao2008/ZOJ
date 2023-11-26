<?php
function emptycheck(){
    return empty($_POST['password'])||empty($_POST['nickname'])||empty($_POST['email']);
}
$alert="";
if ($_POST['username']) {
    $id = $_POST['username'];
    if (user::queryUser($id)) {
        $alert="该用户名已被使用";
    }elseif(emptycheck()){
        $alert="请不要空项";
    } 
    else {
        $thiscfg = $emptycfg;
        $thiscfg['nick'] = $_POST['nickname'];
        $thiscfg['email'] = $_POST['email'];
        $thiscfg['password'] = md5($_POST['password']);
        DB::putdata("user/$id", $thiscfg);
        user::login($id,md5($_POST['password']));
        jsjump("/profile");
    }
}
?>
<?php view::header("注册"); ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <img src="/icon.jpg" style="height: 100px;border-radius:5px">
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">用户名</label>
                        <input type="text" class="form-control" id="username" name="username" aria-describedby="usernameHelp">
                        <div id="usernameHelp" class="form-text">请输入用户名</div>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">昵称</label>
                        <input type="text" class="form-control" id="nickname" name="nickname" aria-describedby="usernameHelp">
                        <div id="usernameHelp" class="form-text">请输入昵称</div>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">邮箱</label>
                        <input type="email" class="form-control" id="email" name="email" aria-describedby="usernameHelp">
                        <div id="usernameHelp" class="form-text">请输入邮箱</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">密码</label>
                        <input type="password" class="form-control" id="password" name="password" aria-describedby="passwordHelp">
                        <div id="passwordHelp" class="form-text">请输入密码</div>
                    </div>
                    <div class="mb-3">
                        <?=$alert?>
                    </div>
                    <button type="submit" class="btn btn-primary">注册</button>
                    <a type="button" href="/profile" class="btn btn-default">登录</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php view::foot(); ?>