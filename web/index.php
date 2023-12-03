<?php
include "./script/lib/main.php";
Router::loadRouteMap();
$p=user::LoginCheck(1);
$safereq=requestDecode();
$mypower=0;
$mypower=$p['power'];
include includePage(Router::GetScriptPath(Router::getUri(),$mypower));
