<?php view::header("交流"); ?>

<table class="table table-hover">
    <thead>
        <tr class="table-danger">
            <th class="thint">#</th>
            <th class="mthint">题目ID</th>
            <th>标题</th>
            <th>内容</th>
            <th>创建者</th>
            <th class="mthint">时间</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $data=contanct::getContanctList_Problems(1);
        $numid=count($data);
        foreach ($data as $k=>$item): 
            if($_GET['pid']){
                if($item['for']!=$_GET['pid']){
                    continue;
                }
            }
            ?>
        <tr onclick="window.location='/contancting?cid=<?=$item['id']?>'">
            <td class="thint"><?= $numid ?></td>
            <td class="mthint"><?= $item['for'] ?></td>
            <td><?= $item['title'] ?></td>
            <td><?= $item['desc'] ?></td>
            <td><?= $item['creator'] ?></td>
            <td><?= date("m-d",$item['createTime']) ?></td>
        </tr>
        <?php $numid--;
        endforeach; ?>
        
    </tbody>
</table>
<?if($numid===count($data))echo "It Seems Like That There Is Nothing."?>
<a href="ccontanct" class="btn btn-primary">创建一个</a>
<?php view::foot();
