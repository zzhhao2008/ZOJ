<?php
view::header("404 Error");
echo "<h1>I'am Sorry,But The Page ".Router::getUri()." is not Exists!</h1>";
?>
<hr>
<p>抱歉，该页面无法被访问</p>
<p>可能的原因:</p>
<p>1.未登录，该页面仅登录后可用</p>
<p>2.该页面已被删除</p>
<?php view::foot();