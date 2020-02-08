<?php


defined("IN_IA") or exit("Access Denied");
function google_batch_calculate_distance($origins, $destination, $key = "AIzaSyABxMCzgtzJxCbJu8Cxwv7BszayIAWN1xw")
{
    global $_W;
    $query = array("key" => $key, "origins" => $origins, "destination" => implode(",", $destination));
    $url = "http://maps.googleapis.com/maps/api/distancematrix/json?";
    $query = http_build_query($query);
    load()->func("communication");
    $result = ihttp_get($url . $query);
    if (is_error($result)) {
        return $result;
    }
    $result = @json_decode($result["content"], true);
}
function google_geocode_geo($address, $key = "AIzaSyABxMCzgtzJxCbJu8Cxwv7BszayIAWN1xw")
{
    global $_W;
    if (empty($address)) {
        return error(-1, "要获取经纬度的地址不存");
    }
    $query = array("key" => $key, "address" => $address);
    $url = "https://maps.googleapis.com/maps/api/geocode/json?";
    $query = http_build_query($query);
    load()->func("communication");
    $result = ihttp_get($url . $query);
    if (is_error($result)) {
        return $result;
    }
    $result = @json_decode($result["content"], true);
    if ($result["status"] != "OK") {
        return error(-1, $result["error_message"]);
    }
    $data = $result["results"][0];
    if (!empty($data) && !empty($data["geometry"]["location"])) {
        $data["location"] = array($result["geometry"]["location"]["lng"], $result["geometry"]["location"]["lat"]);
    }
    return $data;
}

?>
