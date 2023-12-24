<?php view::header("比赛列表");
$contestlist = contest::get_list(0);
?>
<table class="table table-hover">
    <thead>
        <tr class="table-info">
            <th class="mthint">状态</th>
            <th class="thint">题目数量</th>
            <th class="thint">报名人数</th>
            <th>标题</th>
            <th class="mthint">标签</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($contestlist as $k => $v) {
            if($v['starttime']>time()){
                $status = "<span class='text-warning'>未开始</span>";
            }
            elseif($v['endtime']<time()){
                $status = "<span class='text-success'>已结束</span>";
            }
            else{
                $status = "<span class='text-danger'>进行中</span>";
                if(contest::joined($v['joinedusers'])){
                    $status .= "<span class='text-success'>√</span>";
                }
                elseif(contest::joinable($v,user::read()['name'])){
                    $status .= "<span class='text-info'>√</span>";
                }
            }
        ?>
            <tr onclick="location.href='contestshowing?id=<?= $v['id'] ?>'">
                <td><?= $status ?></td>
                <td><?= count($v['problemlist']) ?></td>
                <td><?= count($v['joinedusers']) ?></td>
                <td><?= $v['title'] ?></td>
                <td>
                    <code class="badge rounded-pill bg-info">
                        <?= view::icon("tag") ?>
                        <?= implode("</code> <code class=\"badge rounded-pill bg-info\">" . view::icon("tag"), $v['tag']) ?>
                    </code>
                </td>
                
            </tr>
        <?php             }
        ?>
    </tbody>
</table>
<?php view::foot(); ?>