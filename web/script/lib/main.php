<?php
function includeLib($libname)
{
    return "./script/lib/$libname.php";
}
function includePage($pagename)
{
    return "./script/page/$pagename.php";
}
function includeViewer($name)
{
    return "./script/view/$name.php";
}
function includeC($name)
{
    return "./script/$name.php";
}
include includeLib("data");
include includeLib("router");
include includeLib("view");
include includeLib("user");
include includeLib("problem");
include includeLib("judge");
include includeLib("submit");
include includeLib("contanct");
function arrayDecode($array)
{
    $req = [];
    foreach ($array as $k => $v) {
        if (is_array($v)) {
            $req[$k] = arrayDecode($v);
        } else {
            $safeneedle = [];
            $v = htmlspecialchars($v);
            if (!in_array($k, $safeneedle)) $req[$k] = addslashes($v);
            else $req[$k] = $array[$k];
        }
    }
    return $req;
}
function requestDecode()
{
    $safeneedle = ["answer", "face"];
    $req_all = [];
    foreach ($_GET as $k => $v) {
        if (is_array($v)) {
            $_GET[$k]=arrayDecode($v);
        } else {
            $v = htmlspecialchars($v);
            if (!in_array($k, $safeneedle)) $_GET[$k] = ($v);
            $req_all[$k] = $_GET[$k];
        }
    }
    foreach ($_POST as $k => $v) {
        if (is_array($v)) {
            $_POST[$k]=arrayDecode($v);
        } else {
            $v = htmlspecialchars($v);
            if (!in_array($k, $safeneedle)) $_POST[$k] = ($v);
            $req_all[$k] = $_POST[$k];
        }
    }
    foreach ($_COOKIE as $k => $v) {
        $v = htmlspecialchars($v);
        $_COOKIE[$k] = addslashes($v);
    }
    return $req_all;
}
function alert($msg)
{
    echo "<script>alert('$msg')</script>";
}
function jsjump($u)
{
    echo "<script>window.location='$u'</script>";
}
function jsreload()
{
    echo "<script>window.location.reload()</script>";
}

function getstatic($name)
{
    return file_get_contents("static/$name");
}
