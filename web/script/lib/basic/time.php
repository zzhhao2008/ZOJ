<?php
function getDate_full($stamp)
{
    return date("Y-m-d H:i:s", $stamp);
}
/**
 * 自动返回传入值到现在相距的时间，如果超过1天，就返回日期
 * @return string
 */
function getDate_ToNow($stamp)
{
    //自动返回传入值到现在相距的时间，如果超过1天，就返回日期

    $now = time();
    $diff = $now - $stamp;
    $day=3600*24;
    if ($diff >$day*7) {
        return date("Y-m-d H:i:s", $stamp);
    } elseif($diff > $day) {
        return $diff%$day . "天前";
    }
    elseif($diff > 3600) {
        return $diff%3600 . "小时前";
    }
    elseif($diff > 60) {
        return $diff%60 . "分钟前";
    }
    elseif($diff > 0) {
        return $diff . "秒前";
    }
    else {
        return "刚刚";
    }
}