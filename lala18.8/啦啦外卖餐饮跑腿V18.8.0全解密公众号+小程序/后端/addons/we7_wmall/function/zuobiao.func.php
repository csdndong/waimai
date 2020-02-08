<?php
/*

 
 
 * 源码仅供研究学习，请勿用于商业用途
 */

defined("IN_IA") or exit("Access Denied");
/**
 三合一外卖系统
 * =========================================================
 * Copy right 2055-2088 。
 * ----------------------------------------------
 * 官方网址：？？？
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : 外卖系统
 * @客服QQ : 
 */
function is_in_two_point($point1, $point2, $point3, $quadrant = false)
{
    $diff_lng = $point1[0] - $point2[0];
    $diff_lat = $point1[1] - $point2[1];
    if ($quadrant && 0 < $diff_lng * ($point3[0] - $point2[0]) && 0 < $diff_lat * ($point3[1] - $point2[1])) {
        return true;
    }
    $lng_in = $lat_in = false;
    if (0 < $diff_lng && $point2[0] <= $point3[0] && $point3[0] <= $point1[0]) {
        $lng_in = true;
    } else {
        if ($diff_lng < 0 && $point1[0] <= $point3[0] && $point3[0] <= $point2[0]) {
            $lng_in = true;
        }
    }
    if (0 < $diff_lat && $point2[1] <= $point3[1] && $point3[1] <= $point1[1]) {
        $lat_in = true;
    } else {
        if ($diff_lat < 0 && $point1[1] <= $point3[1] && $point3[1] <= $point2[1]) {
            $lat_in = true;
        }
    }
    if ($lat_in && $lng_in || $diff_lat == 0 && $lng_in || $diff_lng == 0 && $lat_in) {
        return true;
    }
    return false;
}
function is_points_in_identical_side($point1, $point2, $point3, $point4, $vector = false)
{
    $slope = ($point1[0] - $point2[0]) / ($point1[1] - $point2[1]);
    $same_direction = true;
    if ($vector) {
        $same_direction = 0 <= ($point1[0] - $point2[0]) * ($point3[0] - $point4[0]);
    }
    if (0 < ($slope * $point3[1] - $point3[0]) * ($slope * $point4[1] - $point4[0]) && $same_direction) {
        return true;
    }
    return false;
}
function is_in_identical_direction($reference, $judged)
{
    $in_quadrant_accept = is_in_two_point($reference["destination"], $reference["origin"], $judged["destination"], true);
    $in_quadrant_origin = is_in_two_point($reference["destination"], $reference["origin"], $judged["origin"]);
    if ($in_quadrant_accept && $in_quadrant_origin) {
        $in_identical_direction = is_points_in_identical_side($reference["destination"], $reference["origin"], $judged["destination"], $judged["origin"], true);
        if ($in_identical_direction) {
            return true;
        }
    }
    return false;
}

?>