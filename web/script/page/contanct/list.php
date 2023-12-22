<?php
$p = intval($_GET['page']) ? intval($_GET['page']) : 1;
$data = contanct::getContanctList_Problems(1, $p, 25);
$numid = $data['end'] + 1;
$pagecnt = $data['allpage'];
view::header("交流");
usort($data['data'], function ($a, $b) {
    return $a['createTime'] < $b['createTime'];
});
?>

<div class="abox">
    <h5>Fifter/筛选</h5>
    <form>
        <div class="row">
            <div class="col-sm-6">
                <input type="text" class="form-control flex" name="pid" placeholder="题目ID:" value="<?= $_GET['pid'] ?>">
            </div>
            <div class="col-sm-6">
                <input type="text" class="form-control flex" name="creator" placeholder="创建者" value="<?= $_GET['creator'] ?>">
            </div>
            <div class="col-sm-6">
                <input type="text" class="form-control flex" name="time" placeholder="时间(m-d)" value="<?= $_GET['time'] ?>">
            </div>
            <div class="col-sm-4">
                <input type="submit" value="筛选" class="btn btn-primary">
            </div>
        </div>
    </form>
</div>
<hr>
<div class="abox">
    <table class="table table-hover">
        <thead>
            <tr class="table-danger">
                <th class="thint">#</th>
                <th class="mthint">题目ID</th>
                <th>标题</th>
                <th>创建者</th>
                <th class="mthint">时间</th>
            </tr>
        </thead>
        <tbody>
            <?php

            foreach ($data['data'] as $k => $item) :
                if ($_GET['pid']) {
                    if ($item['for'] != $_GET['pid']) {
                        continue;
                    }
                }
                if ($_GET['creator']) {
                    if ($item['creator'] != $_GET['creator']) {
                        continue;
                    }
                }
                if ($_GET['time']) {
                    if (date("m-d", $item['createTime']) != $_GET['time']) {
                        continue;
                    }
                }
            ?>
                <tr onclick="window.location='/contancting?cid=<?= $item['id'] ?>'">
                    <td class="thint"><?= $numid ?></td>
                    <td class="mthint"><?= $item['for'] ?></td>
                    <td><?= $item['title'] ?></td>
                    <td><?= user::queryUserNick($item['creator'], 1) ?></td>
                    <td><?= date("m-d", $item['createTime']) ?></td>
                </tr>
            <?php $numid--;
            endforeach; ?>

        </tbody>
    </table>
</div>
<div class="p-1"></div>
<div class="abox">
    <?php
    if (user::is_superuser() || 1) {
        $data = contanct::getContanctList_Private(1, $p, 10);
        $numid = count($data);
        $pagecnt = max($pagecnt, $data['cfg']);
    ?>
        <h4>隐藏</h4>
        <table class="table table-hover">
            <thead>
                <tr class="table-warning">
                    <th class="thint">#</th>
                    <th class="mthint">题目ID</th>
                    <th>标题</th>
                    <th>创建者</th>
                    <th class="mthint">时间</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($data['data'] as $k => $item) :
                    if ((!user::is_superuser()) && ($item['creator'] != user::read()['name'])) continue;
                ?>
                    <tr onclick="window.location='/contancting?cid=<?= $item['id'] ?>'">
                        <td class="thint"><?= $numid ?></td>
                        <td class="mthint"><?= $item['for'] ?></td>
                        <td><?= $item['title'] ?></td>
                        <td><?= $item['creator'] ?></td>
                        <td><?= date("m-d", $item['createTime']) ?></td>
                    </tr>
                <?php $numid--;
                endforeach; ?>

            </tbody>
        </table>
    <?php } ?>
</div>
<div class="">
    <ul class="pagination justify-content-center">
        <?php
        for ($i = 1; $i <= $pagecnt; $i++) {
            echo <<<HTML
                <li class="page-item"><a class="page-link" href="?page=$i">$i</a></li>
HTML;
        }
        ?>

    </ul>
</div>
<a href="ccontanct?pid=<?= $_GET['pid'] ?>" class="btn btn-primary">创建一个</a>
<?php view::foot();
