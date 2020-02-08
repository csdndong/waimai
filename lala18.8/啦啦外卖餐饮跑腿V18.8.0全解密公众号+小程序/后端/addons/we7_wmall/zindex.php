<?php
define("IN_MOBILE", true);
require "../framework/bootstrap.inc.php";
load()->app("common");
load()->app("template");
load()->model("app");
$acl = array("home" => array("default" => "home"), "mc" => array("default" => "home"));
$controllers = array();
$handle = opendir(IA_ROOT . "/app/source/");
if (!empty($handle)) {
    while ($dir = readdir($handle)) {
        if ($dir != "." && $dir != "..") {
            $controllers[] = $dir;
        }
    }
}
if (!in_array($controller, $controllers)) {
    $controller = "home";
}
$init = IA_ROOT . "/app/source/" . $controller . "/__init.php";
if (is_file($init)) {
    require $init;
}
$actions = array();
$handle = opendir(IA_ROOT . "/app/source/" . $controller);
if (!empty($handle)) {
    while ($dir = readdir($handle)) {
        if ($dir != "." && $dir != ".." && strexists($dir, ".ctrl.php")) {
            $dir = str_replace(".ctrl.php", "", $dir);
            $actions[] = $dir;
        }
    }
}
if (!in_array($action, $actions)) {
    $action = $acl[$controller]["default"];
}
if (!in_array($action, $actions)) {
    $action = $actions[0];
}
require _forward($controller, $action);
function _forward($c, $a)
{
    $file = IA_ROOT . "/app/source/" . $c . "/" . $a . ".ctrl.php";
    return $file;
}

?>
