<?php view::header("练习列表");
if (user::is_superuser()) {
?>
    <div class="dropdown">
        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
            创建
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/practice_create_common">普通</a></li>
        </ul>
    </div>
<?php     }
$p=intval($_GET['page'])?intval($_GET['page']):1;
$practices = practice::get_all_common($p);
?>
<table class="table table-hover">
    <thead>
        <tr class="table-info">
            <th class="thint">状态</th>
            <th class="mthint">题目数量</th>
            <th class="mthint">ID</th>
            <th>标题</th>
            <th>标签</th>
            <th>创建时间</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($practices as $k => $v) {
            $k = $v['id'];
            if (!practice::visable_common($v)) continue;
            $thistry = 0;
            $thistry = user::read()['profile']['practice'][$k];
            if (isset(user::read()['profile']['practice'][$k])) {
                $thissco = $thistry;
                $c = problems::colorsolve($thistry + 0.1);
            } else {
                $thissco = "--";
                $c = "";
            };
            if ($thissco >= 100) $thissco = "√";

        ?>
            <tr onclick="location.href='practiceshow?id=<?= $k ?>'">
                <td class="text-<?= $c ?> table-light"><?= $thissco ?></td>
                <td class="mthint"><?= $v['cmped']['num']??"Unk" ?></td>
                <td class="mthint"><?= $v['id'] ?></td>
                <td><?= $v['title'] ?></td>
                <td>
                    <code class="badge rounded-pill bg-primary">
                        <?= view::icon("tag") ?>
                        <?= implode("</code> <code class=\"badge rounded-pill bg-primary\">" . view::icon("tag"), $v['tag']) ?>
                    </code>
                </td>
                <td><?=date($v['createtime'])?></td>
            </tr>
        <?php             }
        ?>
    </tbody>
</table>
<?php
view::foot();
