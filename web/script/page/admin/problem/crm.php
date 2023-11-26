<?php view::header("创建或删除题目"); ?>
<?php
if (problems::checktype($_GET['crtype'])) {
    //echo problems::creatProblem($_GET['crtype']);
    jsjump("/problem_edit?pid=" . problems::creatProblem($_GET['crtype']));
}
?>
<?php view::foot(); ?>
