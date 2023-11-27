<?php
if ($_GET['logout']) {
    setcookie("login_name_code", "", time() - 3600 * 48, "/");
    jsjump("?");
}
view::header("我的账户");
$userconfig = user::read()['profile'];
?>
<p>
<h2>Hi,<?= $userconfig['nick'] ?>!</h2>
</p>
<a href="?logout=1" class="btn btn-danger"><?= view::icon('box-arrow-left') ?>退出登录</a>
<a href="/change" class="btn btn-primary"><?= view::icon('pencil-square') ?>个人设置</a>
<ul class="list-group">
    <li class="list-group-item list-group-item-info">Rating:<?= $userconfig['rating'] ?></li>
    <li class="list-group-item list-group-item-secondary">Email:<?= $userconfig['email'] ?></li>
    <li class="list-group-item list-group-item-dark">
        <h5>个人介绍</h5><?= $userconfig['about'] ?>
    </li>
    <li class="list-group-item list-group-item-warning">
        <h5>尝试过的题目</h5>
        <ul class="list-group list-group-horizontal">
            <?php
            $userconfig['ac'] = [];
            foreach ($userconfig['try'] as $k => $v) {
                if ($v >= 100) {
                    $userconfig['ac'][] = $k;
                    continue;
                }
            ?>
                <a href="/problem?id=<?= $k ?>" class="list-group-item"><?= $k, " <code>", $v, "</code>" ?>
                </a>
            <?php } ?>
        </ul>
    </li>
    <li class="list-group-item list-group-item-success">
        <h5>AC的题目</h5>
        <ul class="list-group list-group-horizontal">
            <?php foreach ($userconfig['ac'] as $k => $v) {
            ?>
                <a href="/problem?id=<?= $v ?>" class="list-group-item list-group-item-info"><?= $v ?>
                </a>
            <?php } ?>
        </ul>
    </li>
    <li class="list-group-item list-group-item-primary">
        <h5>比赛与练习</h5>
        <ul class="list-group list-group-horizontal">
            <?php foreach ($userconfig['contest'] as $v) { ?>
                <a href="/contest?id=<?= $v ?>" class="list-group-item"><?= $v ?>
                </a>
            <?php } ?>
        </ul>
        <ul class="list-group list-group-horizontal">
            <?php foreach ($userconfig['practice'] as $v) { ?>
                <a href="/practice?id=<?= $v ?>" class="list-group-item"><?= $v ?>
                </a>
            <?php } ?>
        </ul>
    </li>
    <li class="list-group-item list-group-item-light">
        <h5>动态</h5>
        <div>
            <?php 
            usort($userconfig['dt'],function($a,$b){
                //按时间从大到小
                return $b['time']-$a['time'];
            });
            foreach ($userconfig['dt'] as $v) { 
                $date=date("Y-m-d H:i:s",$v['time']);
                echo <<<HTML
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{$date}</h5>
        <p class="card-text">{$v['art']}</p>
    </div>
</div>
HTML;
            } ?>
        </div>
    </li>
</ul>
<?php view::foot(); ?>