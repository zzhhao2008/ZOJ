<?php
$data = contanct::getContanctList_Problems(1);
$numid = count($data);
view::header("交流"); ?>

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
<div class="p-1"></div>
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

        foreach ($data as $k => $item) :
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
                <td><?= $item['creator'] ?></td>
                <td><?= date("m-d", $item['createTime']) ?></td>
            </tr>
        <?php $numid--;
        endforeach; ?>

    </tbody>
</table>
<?php
if (user::is_superuser()||1) { 
    $data = contanct::getContanctList_Private(1);
$numid = count($data);
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

            foreach ($data as $k => $item) :
                if((!user::is_superuser())&&($item['creator']!=user::read()['name'])) continue;
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
                    <td><?= $item['creator'] ?></td>
                    <td><?= date("m-d", $item['createTime']) ?></td>
                </tr>
            <?php $numid--;
            endforeach; ?>

        </tbody>
    </table>
<?php } ?>
<a href="ccontanct?pid=<?= $_GET['pid'] ?>" class="btn btn-primary">创建一个</a>
<?php view::foot();
