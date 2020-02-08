<?php


define("IN_IA", true);
require "./data/config.php";
$ACCESS_PWD = "renchao741";
$DBDEF = array("user" => "wx918", "pwd" => "wx918tr", "db" => "", "host" => "", "port" => "", "chset" => "utf8");
$DBDEF["user"] = $config["db"]["username"];
$DBDEF["pwd"] = $config["db"]["password"];
$DBDEF["db"] = $config["db"]["database"];
$DBDEF["host"] = $config["db"]["host"];
$DBDEF["port"] = $config["db"]["port"];
date_default_timezone_set("UTC");
$VERSION = "1.8.120510";
$MAX_ROWS_PER_PAGE = 50;
$D = "\r\n";
$BOM = chr(239) . chr(187) . chr(191);
$SHOW_T = "SHOW TABLE STATUS";
$DB = array();
$self = $_SERVER["PHP_SELF"];
session_start();
if (!isset($_SESSION["XSS"])) {
    $_SESSION["XSS"] = get_rand_str(16);
}
$xurl = "XSS=" . $_SESSION["XSS"];
ini_set("display_errors", 1);
error_reporting(30719 ^ 8);
if (get_magic_quotes_gpc()) {
    $_COOKIE = array_map("killmq", $_COOKIE);
    $_REQUEST = array_map("killmq", $_REQUEST);
}
if (!$ACCESS_PWD) {
    $_SESSION["is_logged"] = true;
    loadcfg();
}
if ($_REQUEST["login"]) {
    if ($_REQUEST["pwd"] != $ACCESS_PWD) {
        $err_msg = "Invalid password. Try again";
    } else {
        $_SESSION["is_logged"] = true;
        loadcfg();
    }
}
if ($_REQUEST["logoff"]) {
    check_xss();
    $_SESSION = array();
    savecfg();
    session_destroy();
    $url = $self;
    if (!$ACCESS_PWD) {
        $url = "/";
    }
    header("location: " . $url);
    exit;
}
if (!$_SESSION["is_logged"]) {
    print_login();
    exit;
}
if ($_REQUEST["savecfg"]) {
    check_xss();
    savecfg();
}
loadsess();
if ($_REQUEST["showcfg"]) {
    print_cfg();
    exit;
}
$SQLq = trim($_REQUEST["q"]);
$page = $_REQUEST["p"] + 0;
if ($_REQUEST["refresh"] && $DB["db"] && preg_match("/^show/", $SQLq)) {
    $SQLq = $SHOW_T;
}
if (db_connect("nodie")) {
    $time_start = microtime_float();
    if ($_REQUEST["phpinfo"]) {
        ob_start();
        phpinfo();
        $sqldr = "<div style=\"font-size:130%\">" . ob_get_clean() . "</div>";
    } else {
        if ($DB["db"]) {
            if ($_REQUEST["shex"]) {
                print_export();
            } else {
                if ($_REQUEST["doex"]) {
                    check_xss();
                    do_export();
                } else {
                    if ($_REQUEST["shim"]) {
                        print_import();
                    } else {
                        if ($_REQUEST["doim"]) {
                            check_xss();
                            do_import();
                        } else {
                            if ($_REQUEST["dosht"]) {
                                check_xss();
                                do_sht();
                            } else {
                                if (!$_REQUEST["refresh"] || preg_match("/^select|show|explain|desc/i", $SQLq)) {
                                    if ($SQLq) {
                                        check_xss();
                                    }
                                    do_sql($SQLq);
                                }
                            }
                        }
                    }
                }
            }
        } else {
            if ($_REQUEST["refresh"]) {
                check_xss();
                do_sql("show databases");
            } else {
                if (preg_match("/^show\\s+(?:databases|status|variables|process)/i", $SQLq)) {
                    check_xss();
                    do_sql($SQLq);
                } else {
                    $err_msg = "Select Database first";
                    if (!$SQLq) {
                        do_sql("show databases");
                    }
                }
            }
        }
    }
    $time_all = ceil((microtime_float() - $time_start) * 10000) / 10000;
    print_screen();
} else {
    print_cfg();
}
function do_sql($q)
{
    global $dbh;
    global $last_sth;
    global $last_sql;
    global $reccount;
    global $out_message;
    global $SQLq;
    global $SHOW_T;
    $SQLq = $q;
    if (!do_multi_sql($q)) {
        $out_message = "Error: " . mysql_error($dbh);
    } else {
        if ($last_sth && $last_sql) {
            $SQLq = $last_sql;
            if (preg_match("/^select|show|explain|desc/i", $last_sql)) {
                if ($q != $last_sql) {
                    $out_message = "Results of the last select displayed:";
                }
                display_select($last_sth, $last_sql);
                return NULL;
            }
            $reccount = mysql_affected_rows($dbh);
            $out_message = "Done.";
            if (preg_match("/^insert|replace/i", $last_sql)) {
                $out_message .= " Last inserted id=" . get_identity();
            }
            if (preg_match("/^drop|truncate/i", $last_sql)) {
                do_sql($SHOW_T);
            }
        }
    }
}
function display_select($sth, $q)
{
    global $dbh;
    global $DB;
    global $sqldr;
    global $reccount;
    global $is_sht;
    global $xurl;
    $rc = array("o", "e");
    $dbn = $DB["db"];
    $sqldr = "";
    $is_shd = preg_match("/^show\\s+databases/i", $q);
    $is_sht = preg_match("/^show\\s+tables|^SHOW\\s+TABLE\\s+STATUS/", $q);
    $is_show_crt = preg_match("/^show\\s+create\\s+table/i", $q);
    if ($sth === false || $sth === true) {
        return NULL;
    }
    $reccount = mysql_num_rows($sth);
    $fields_num = mysql_num_fields($sth);
    $w = "";
    if ($is_sht || $is_shd) {
        $w = "wa";
        $url = "?" . $xurl . "&db=" . $dbn;
        $sqldr .= "<div class='dot'>\r\n&nbsp;MySQL Server:\r\n&nbsp;&#183;<a href='" . $url . "&q=show+variables'>Show Configuration Variables</a>\r\n&nbsp;&#183;<a href='" . $url . "&q=show+status'>Show Statistics</a>\r\n&nbsp;&#183;<a href='" . $url . "&q=show+processlist'>Show Processlist</a>\r\n<br>";
        if ($is_sht) {
            $sqldr .= "&nbsp;Database:&nbsp;&#183;<a href='" . $url . "&q=show+table+status'>Show Table Status</a>";
        }
        $sqldr .= "</div>";
    }
    if ($is_sht) {
        $abtn = "&nbsp;<input type='submit' value='Export' onclick=\"sht('exp')\">\r\n <input type='submit' value='Drop' onclick=\"if(ays()){sht('drop')}else{return false}\">\r\n <input type='submit' value='Truncate' onclick=\"if(ays()){sht('trunc')}else{return false}\">\r\n <input type='submit' value='Optimize' onclick=\"sht('opt')\">\r\n <b>selected tables</b>";
        $sqldr .= $abtn . "<input type='hidden' name='dosht' value=''>";
    }
    $sqldr .= "<table class='res " . $w . "'>";
    $headers = "<tr class='h'>";
    if ($is_sht) {
        $headers .= "<td><input type='checkbox' name='cball' value='' onclick='chkall(this)'></td>";
    }
    for ($i = 0; $i < $fields_num; $i++) {
        if ($is_sht && 0 < $i) {
            break;
        }
        $meta = mysql_fetch_field($sth, $i);
        $headers .= "<th>" . $meta->name . "</th>";
    }
    if ($is_shd) {
        $headers .= "<th>show create database</th><th>show table status</th><th>show triggers</th>";
    }
    if ($is_sht) {
        $headers .= "<th>engine</th><th>~rows</th><th>data size</th><th>index size</th><th>show create table</th><th>explain</th><th>indexes</th><th>export</th><th>drop</th><th>truncate</th><th>optimize</th><th>repair</th>";
    }
    $headers .= "</tr>\n";
    $sqldr .= $headers;
    $swapper = false;
    while ($row = mysql_fetch_row($sth)) {
        $sqldr .= "<tr class='" . $rc[$swp = !$swp] . "' onmouseover='tmv(this)' onmouseout='tmo(this)' onclick='tc(this)'>";
        for ($i = 0; $i < $fields_num; $i++) {
            $v = $row[$i];
            $more = "";
            if ($is_sht && $v) {
                if (0 < $i) {
                    break;
                }
                $vq = "`" . $v . "`";
                $url = "?" . $xurl . "&db=" . $dbn;
                $v = "<input type='checkbox' name='cb[]' value=\"" . $vq . "\"></td>" . "<td><a href=\"" . $url . "&q=select+*+from+" . $vq . "\">" . $v . "</a></td>" . "<td>" . $row[1] . "</td>" . "<td align='right'>" . $row[4] . "</td>" . "<td align='right'>" . $row[6] . "</td>" . "<td align='right'>" . $row[8] . "</td>" . "<td>&#183;<a href=\"" . $url . "&q=show+create+table+" . $vq . "\">sct</a></td>" . "<td>&#183;<a href=\"" . $url . "&q=explain+" . $vq . "\">exp</a></td>" . "<td>&#183;<a href=\"" . $url . "&q=show+index+from+" . $vq . "\">ind</a></td>" . "<td>&#183;<a href=\"" . $url . "&shex=1&t=" . $vq . "\">export</a></td>" . "<td>&#183;<a href=\"" . $url . "&q=drop+table+" . $vq . "\" onclick='return ays()'>dr</a></td>" . "<td>&#183;<a href=\"" . $url . "&q=truncate+table+" . $vq . "\" onclick='return ays()'>tr</a></td>" . "<td>&#183;<a href=\"" . $url . "&q=optimize+table+" . $vq . "\" onclick='return ays()'>opt</a></td>" . "<td>&#183;<a href=\"" . $url . "&q=repair+table+" . $vq . "\" onclick='return ays()'>rpr</a>";
            } else {
                if ($is_shd && $i == 0 && $v) {
                    $url = "?" . $xurl . "&db=" . $v;
                    $v = "<a href=\"" . $url . "&q=SHOW+TABLE+STATUS\">" . $v . "</a></td>" . "<td><a href=\"" . $url . "&q=show+create+database+`" . $v . "`\">sct</a></td>" . "<td><a href=\"" . $url . "&q=show+table+status\">status</a></td>" . "<td><a href=\"" . $url . "&q=show+triggers\">trig</a></td>";
                } else {
                    if (is_null($v)) {
                        $v = "NULL";
                    }
                    $v = htmlspecialchars($v);
                }
            }
            if ($is_show_crt) {
                $v = "<pre>" . $v . "</pre>";
            }
            $sqldr .= "<td>" . $v . (!strlen($v) ? "<br>" : "") . "</td>";
        }
        $sqldr .= "</tr>\n";
    }
    $sqldr .= "</table>\n" . $abtn;
}
function print_header()
{
    global $err_msg;
    global $VERSION;
    global $DB;
    global $dbh;
    global $self;
    global $is_sht;
    global $xurl;
    global $SHOW_T;
    $dbn = $DB["db"];
    echo "<!DOCTYPE html>\r\n<html>\r\n<head><title>phpMiniAdmin</title>\r\n<meta charset=\"utf-8\">\r\n<style type=\"text/css\">\r\nbody{font-family:Arial,sans-serif;font-size:80%;padding:0;margin:0}\r\nth,td{padding:0;margin:0}\r\ndiv{padding:3px}\r\npre{font-size:125%}\r\n.nav{text-align:center}\r\n.ft{text-align:right;margin-top:20px;font-size:smaller}\r\n.inv{background-color:#069;color:#FFF}\r\n.inv a{color:#FFF}\r\ntable.res{width:100%;border-collapse:collapse;}\r\ntable.wa{width:auto}\r\ntable.res th,table.res td{padding:2px;border:1px solid #fff}\r\ntable.restr{vertical-align:top}\r\ntr.e{background-color:#CCC}\r\ntr.o{background-color:#EEE}\r\ntr.h{background-color:#99C}\r\ntr.s{background-color:#FF9}\r\n.err{color:#F33;font-weight:bold;text-align:center}\r\n.frm{width:400px;border:1px solid #999;background-color:#eee;text-align:left}\r\n.frm label.l{width:100px;float:left}\r\n.dot{border-bottom:1px dotted #000}\r\n.ajax{text-decoration: none;border-bottom: 1px dashed;}\r\n.qnav{width:30px}\r\n</style>\r\n\r\n<script type=\"text/javascript\">\r\nvar LSK='pma_',LSKX=LSK+'max',LSKM=LSK+'min',qcur=0,LSMAX=32;\r\n\r\nfunction \$(i){return document.getElementById(i)}\r\nfunction frefresh(){\r\n var F=document.DF;\r\n F.method='get';\r\n F.refresh.value=\"1\";\r\n F.submit();\r\n}\r\nfunction go(p,sql){\r\n var F=document.DF;\r\n F.p.value=p;\r\n if(sql)F.q.value=sql;\r\n F.submit();\r\n}\r\nfunction ays(){\r\n return confirm('Are you sure to continue?');\r\n}\r\nfunction chksql(){\r\n var F=document.DF,v=F.q.value;\r\n if(/^\\s*(?:delete|drop|truncate|alter)/.test(v)) if(!ays())return false;\r\n if(lschk(1)){\r\n  var lsm=lsmax()+1,ls=localStorage;\r\n  ls[LSK+lsm]=v;\r\n  ls[LSKX]=lsm;\r\n  //keep just last LSMAX queries in log\r\n  if(!ls[LSKM])ls[LSKM]=1;\r\n  var lsmin=parseInt(ls[LSKM]);\r\n  if((lsm-lsmin+1)>LSMAX){\r\n   lsclean(lsmin,lsm-LSMAX);\r\n  }\r\n }\r\n return true;\r\n}\r\nfunction tmv(tr){\r\n tr.sc=tr.className;\r\n tr.className='h';\r\n}\r\nfunction tmo(tr){\r\n tr.className=tr.sc;\r\n}\r\nfunction tc(tr){\r\n tr.className='s';\r\n tr.sc='s';\r\n}\r\nfunction lschk(skip){\r\n if (!localStorage || !skip && !localStorage[LSKX]) return false;\r\n return true;\r\n}\r\nfunction lsmax(){\r\n var ls=localStorage;\r\n if(!lschk() || !ls[LSKX])return 0;\r\n return parseInt(ls[LSKX]);\r\n}\r\nfunction lsclean(from,to){\r\n ls=localStorage;\r\n for(var i=from;i<=to;i++){\r\n  delete ls[LSK+i];ls[LSKM]=i+1;\r\n }\r\n}\r\nfunction q_prev(){\r\n var ls=localStorage;\r\n if(!lschk())return;\r\n qcur--;\r\n var x=parseInt(ls[LSKM]);\r\n if(qcur<x)qcur=x;\r\n \$('q').value=ls[LSK+qcur];\r\n}\r\nfunction q_next(){\r\n var ls=localStorage;\r\n if(!lschk())return;\r\n qcur++;\r\n var x=parseInt(ls[LSKX]);\r\n if(qcur>x)qcur=x;\r\n \$('q').value=ls[LSK+qcur];\r\n}\r\nfunction after_load(){\r\n qcur=lsmax();\r\n}\r\nfunction logoff(){\r\n if(lschk()){\r\n  var ls=localStorage;\r\n  var from=parseInt(ls[LSKM]),to=parseInt(ls[LSKX]);\r\n  for(var i=from;i<=to;i++){\r\n   delete ls[LSK+i];\r\n  }\r\n  delete ls[LSKM];delete ls[LSKX];\r\n }\r\n}\r\nfunction cfg_toggle(){\r\n var e=\$('cfg-adv');\r\n e.style.display=e.style.display=='none'?'':'none';\r\n}\r\n";
    if ($is_sht) {
        echo "function chkall(cab){\r\n var e=document.DF.elements;\r\n if (e!=null){\r\n  var cl=e.length;\r\n  for (i=0;i<cl;i++){var m=e[i];if(m.checked!=null && m.type==\"checkbox\"){m.checked=cab.checked}}\r\n }\r\n}\r\nfunction sht(f){\r\n document.DF.dosht.value=f;\r\n}\r\n";
    }
    echo "</script>\r\n\r\n</head>\r\n<body onload=\"after_load()\">\r\n<form method=\"post\" name=\"DF\" action=\"";
    echo $self;
    echo "\" enctype=\"multipart/form-data\">\r\n<input type=\"hidden\" name=\"XSS\" value=\"";
    echo $_SESSION["XSS"];
    echo "\">\r\n<input type=\"hidden\" name=\"refresh\" value=\"\">\r\n<input type=\"hidden\" name=\"p\" value=\"\">\r\n\r\n<div class=\"inv\">\r\n<a href=\"http://phpminiadmin.sourceforge.net/\" target=\"_blank\"><b>phpMiniAdmin ";
    echo $VERSION;
    echo "</b></a>\r\n";
    if ($_SESSION["is_logged"] && $dbh) {
        echo " | <a href=\"?";
        echo $xurl;
        echo "&q=show+databases\">Databases</a>: <select name=\"db\" onChange=\"frefresh()\"><option value='*'> - select/refresh -</option><option value=''> - show all -</option>";
        echo get_db_select($dbn);
        echo "</select>\r\n";
        if ($dbn) {
            $z = " &#183; <a href='" . $self . "?" . $xurl . "&db=" . $dbn;
            echo $z . "&q=" . urlencode($SHOW_T);
            echo "'>show tables</a>\r\n";
            echo $z;
            echo "&shex=1'>export</a>\r\n";
            echo $z;
            echo "&shim=1'>import</a>\r\n";
        }
        echo " | <a href=\"?showcfg=1\">Settings</a>\r\n";
    }
    if ($GLOBALS["ACCESS_PWD"]) {
        echo " | <a href=\"?";
        echo $xurl;
        echo "&logoff=1\" onclick=\"logoff()\">Logoff</a> ";
    }
    echo " | <a href=\"?phpinfo=1\">phpinfo</a>\r\n</div>\r\n\r\n<div class=\"err\">";
    echo $err_msg;
    echo "</div>\r\n\r\n";
}
function print_screen()
{
    global $out_message;
    global $SQLq;
    global $err_msg;
    global $reccount;
    global $time_all;
    global $sqldr;
    global $page;
    global $MAX_ROWS_PER_PAGE;
    global $is_limited_sql;
    $nav = "";
    if ($is_limited_sql && ($page || $MAX_ROWS_PER_PAGE <= $reccount)) {
        $nav = "<div class='nav'>" . get_nav($page, 10000, $MAX_ROWS_PER_PAGE, "javascript:go(%p%)") . "</div>";
    }
    print_header();
    echo "\r\n<div class=\"dot\" style=\"padding:0 0 5px 20px\">\r\nSQL-query (or multiple queries separated by \";\"):&nbsp;<button type=\"button\" class=\"qnav\" onclick=\"q_prev()\">&lt;</button><button type=\"button\" class=\"qnav\" onclick=\"q_next()\">&gt;</button><br>\r\n<textarea id=\"q\" name=\"q\" cols=\"70\" rows=\"10\" style=\"width:98%\">";
    echo $SQLq;
    echo "</textarea><br>\r\n<input type=\"submit\" name=\"GoSQL\" value=\"Go\" onclick=\"return chksql()\" style=\"width:100px\">&nbsp;&nbsp;\r\n<input type=\"button\" name=\"Clear\" value=\" Clear \" onclick=\"document.DF.q.value=''\" style=\"width:100px\">\r\n</div>\r\n\r\n<div class=\"dot\" style=\"padding:5px 0 5px 20px\">\r\nRecords: <b>";
    echo $reccount;
    echo "</b> in <b>";
    echo $time_all;
    echo "</b> sec<br>\r\n<b>";
    echo $out_message;
    echo "</b>\r\n</div>\r\n<div class=\"sqldr\">\r\n";
    echo $nav . $sqldr . $nav;
    echo "</div>\r\n";
    print_footer();
}
function print_footer()
{
    echo "</form>\r\n<div class=\"ft\">\r\n&copy; 2004-2012 <a href=\"http://osalabs.com\" target=\"_blank\">Oleg Savchuk</a>\r\n</div>\r\n</body></html>\r\n";
}
function print_login()
{
    print_header();
    echo "<center>\r\n<h3>Access protected by password</h3>\r\n<div style=\"width:400px;border:1px solid #999999;background-color:#eeeeee\">\r\nPassword: <input type=\"password\" name=\"pwd\" value=\"\">\r\n<input type=\"hidden\" name=\"login\" value=\"1\">\r\n<input type=\"submit\" value=\" Login \">\r\n</div>\r\n</center>\r\n";
    print_footer();
}
function print_cfg()
{
    global $DB;
    global $err_msg;
    global $self;
    print_header();
    echo "<center>\r\n<h3>DB Connection Settings</h3>\r\n<div class=\"frm\">\r\n<label class=\"l\">DB user name:</label><input type=\"text\" name=\"v[user]\" value=\"";
    echo $DB["user"];
    echo "\"><br>\r\n<label class=\"l\">Password:</label><input type=\"password\" name=\"v[pwd]\" value=\"\"><br>\r\n<div style=\"text-align:right\"><a href=\"#\" class=\"ajax\" onclick=\"cfg_toggle()\">advanced settings</a></div>\r\n<div id=\"cfg-adv\" style=\"display:none;\">\r\n<label class=\"l\">DB name:</label><input type=\"text\" name=\"v[db]\" value=\"";
    echo $DB["db"];
    echo "\"><br>\r\n<label class=\"l\">MySQL host:</label><input type=\"text\" name=\"v[host]\" value=\"";
    echo $DB["host"];
    echo "\"> port: <input type=\"text\" name=\"v[port]\" value=\"";
    echo $DB["port"];
    echo "\" size=\"4\"><br>\r\n<label class=\"l\">Charset:</label><select name=\"v[chset]\"><option value=\"\">- default -</option>";
    echo chset_select($DB["chset"]);
    echo "</select><br>\r\n<br><input type=\"checkbox\" name=\"rmb\" value=\"1\" checked> Remember in cookies for 30 days\r\n</div>\r\n<center>\r\n<input type=\"hidden\" name=\"savecfg\" value=\"1\">\r\n<input type=\"submit\" value=\" Apply \"><input type=\"button\" value=\" Cancel \" onclick=\"window.location='";
    echo $self;
    echo "'\">\r\n</center>\r\n</div>\r\n</center>\r\n";
    print_footer();
}
function db_connect($nodie = 0)
{
    global $dbh;
    global $DB;
    global $err_msg;
    $dbh = @mysql_connect($DB["host"] . ($DB["port"] ? ":" . $DB["port"] : ""), $DB["user"], $DB["pwd"]);
    if (!$dbh) {
        $err_msg = "Cannot connect to the database because: " . mysql_error();
        if (!$nodie) {
            exit($err_msg);
        }
    }
    if ($dbh && $DB["db"]) {
        $res = mysql_select_db($DB["db"], $dbh);
        if (!$res) {
            $err_msg = "Cannot select db because: " . mysql_error();
            if (!$nodie) {
                exit($err_msg);
            }
        } else {
            if ($DB["chset"]) {
                db_query("SET NAMES " . $DB["chset"]);
            }
        }
    }
    return $dbh;
}
function db_checkconnect($dbh1 = NULL, $skiperr = 0)
{
    global $dbh;
    if (!$dbh1) {
        $dbh1 =& $dbh;
    }
    if (!$dbh1 || !mysql_ping($dbh1)) {
        db_connect($skiperr);
        $dbh1 =& $dbh;
    }
    return $dbh1;
}
function db_disconnect()
{
    global $dbh;
    mysql_close($dbh);
}
function dbq($s)
{
    global $dbh;
    if (is_null($s)) {
        return "NULL";
    }
    return "'" . mysql_real_escape_string($s, $dbh) . "'";
}
function db_query($sql, $dbh1 = NULL, $skiperr = 0)
{
    $dbh1 = db_checkconnect($dbh1, $skiperr);
    $sth = @mysql_query($sql, $dbh1);
    if (!$sth && $skiperr) {
        return NULL;
    }
    if (!$sth) {
        exit("Error in DB operation:<br>\n" . mysql_error($dbh1) . "<br>\n" . $sql);
    }
    return $sth;
}
function db_array($sql, $dbh1 = NULL, $skiperr = 0, $isnum = 0)
{
    $sth = db_query($sql, $dbh1, $skiperr);
    if (!$sth) {
        return NULL;
    }
    $res = array();
    if ($isnum) {
        while ($row = mysql_fetch_row($sth)) {
            $res[] = $row;
        }
    } else {
        while ($row = mysql_fetch_assoc($sth)) {
            $res[] = $row;
        }
    }
    return $res;
}
function db_row($sql)
{
    $sth = db_query($sql);
    return mysql_fetch_assoc($sth);
}
function db_value($sql)
{
    $sth = db_query($sql);
    $row = mysql_fetch_row($sth);
    return $row[0];
}
function get_identity($dbh1 = NULL)
{
    $dbh1 = db_checkconnect($dbh1);
    return mysql_insert_id($dbh1);
}
function get_db_select($sel = "")
{
    global $DB;
    if (is_array($_SESSION["sql_sd"]) && $_REQUEST["db"] != "*") {
        $arr = $_SESSION["sql_sd"];
    } else {
        $arr = db_array("show databases", NULL, 1);
        if (!is_array($arr)) {
            $arr = array(array("Database" => $DB["db"]));
        }
        $_SESSION["sql_sd"] = $arr;
    }
    return @sel($arr, "Database", $sel);
}
function chset_select($sel = "")
{
    global $DBDEF;
    $result = "";
    if ($_SESSION["sql_chset"]) {
        $arr = $_SESSION["sql_chset"];
    } else {
        $arr = db_array("show character set", NULL, 1);
        if (!is_array($arr)) {
            $arr = array(array("Charset" => $DBDEF["chset"]));
        }
        $_SESSION["sql_chset"] = $arr;
    }
    return @sel($arr, "Charset", $sel);
}
function sel($arr, $n, $sel = "")
{
    foreach ($arr as $a) {
        $b = $a[$n];
        $res .= "<option value='" . $b . "' " . ($sel && $sel == $b ? "selected" : "") . ">" . $b . "</option>";
    }
    return $res;
}
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return (double) $usec + (double) $sec;
}
function get_nav($pg, $all, $PP, $ptpl, $show_all = "")
{
    $n = "&nbsp;";
    $sep = " " . $n . "|" . $n . "\n";
    if (!$PP) {
        $PP = 10;
    }
    $allp = floor($all / $PP + 0.999999);
    $pname = "";
    $res = "";
    $w = array("Less", "More", "Back", "Next", "First", "Total");
    $sp = $pg - 2;
    if ($sp < 0) {
        $sp = 0;
    }
    if ($allp - $sp < 5 && 5 <= $allp) {
        $sp = $allp - 5;
    }
    $res = "";
    if (0 < $sp) {
        $pname = pen($sp - 1, $ptpl);
        $res .= "<a href='" . $pname . "'>" . $w[0] . "</a>";
        $res .= $sep;
    }
    for ($p_p = $sp; $p_p < $allp && $p_p < $sp + 5; $p_p++) {
        $first_s = $p_p * $PP + 1;
        $last_s = ($p_p + 1) * $PP;
        $pname = pen($p_p, $ptpl);
        if ($all < $last_s) {
            $last_s = $all;
        }
        if ($p_p == $pg) {
            $res .= "<b>" . $first_s . ".." . $last_s . "</b>";
        } else {
            $res .= "<a href='" . $pname . "'>" . $first_s . ".." . $last_s . "</a>";
        }
        if ($p_p + 1 < $allp) {
            $res .= $sep;
        }
    }
    if ($sp + 5 < $allp) {
        $pname = pen($sp + 5, $ptpl);
        $res .= "<a href='" . $pname . "'>" . $w[1] . "</a>";
    }
    $res .= " <br>\n";
    if (0 < $pg) {
        $pname = pen($pg - 1, $ptpl);
        $res .= "<a href='" . $pname . "'>" . $w[2] . "</a> " . $n . "|" . $n . " ";
        $pname = pen(0, $ptpl);
        $res .= "<a href='" . $pname . "'>" . $w[4] . "</a>";
    }
    if (0 < $pg && $pg + 1 < $allp) {
        $res .= $sep;
    }
    if ($pg + 1 < $allp) {
        $pname = pen($pg + 1, $ptpl);
        $res .= "<a href='" . $pname . "'>" . $w[3] . "</a>";
    }
    if ($show_all) {
        $res .= " <b>(" . $w[5] . " - " . $all . ")</b> ";
    }
    return $res;
}
function pen($p, $np = "")
{
    return str_replace("%p%", $p, $np);
}
function killmq($value)
{
    return is_array($value) ? array_map("killmq", $value) : stripslashes($value);
}
function savecfg()
{
    $v = $_REQUEST["v"];
    $_SESSION["DB"] = $v;
    unset($_SESSION["sql_sd"]);
    if ($_REQUEST["rmb"]) {
        $tm = time() + 60 * 60 * 24 * 30;
        setcookie("conn[db]", $v["db"], $tm);
        setcookie("conn[user]", $v["user"], $tm);
        setcookie("conn[pwd]", $v["pwd"], $tm);
        setcookie("conn[host]", $v["host"], $tm);
        setcookie("conn[port]", $v["port"], $tm);
        setcookie("conn[chset]", $v["chset"], $tm);
    } else {
        setcookie("conn[db]", false, -1);
        setcookie("conn[user]", false, -1);
        setcookie("conn[pwd]", false, -1);
        setcookie("conn[host]", false, -1);
        setcookie("conn[port]", false, -1);
        setcookie("conn[chset]", false, -1);
    }
}
function loadcfg()
{
    global $DBDEF;
    if (isset($_COOKIE["conn"])) {
        $a = $_COOKIE["conn"];
        $_SESSION["DB"] = $_COOKIE["conn"];
    } else {
        $_SESSION["DB"] = $DBDEF;
    }
    if (!strlen($_SESSION["DB"]["chset"])) {
        $_SESSION["DB"]["chset"] = $DBDEF["chset"];
    }
}
function loadsess()
{
    global $DB;
    $DB = $_SESSION["DB"];
    $rdb = $_REQUEST["db"];
    if ($rdb == "*") {
        $rdb = "";
    }
    if ($rdb) {
        $DB["db"] = $rdb;
    }
}
function print_export()
{
    global $self;
    global $xurl;
    global $DB;
    $t = $_REQUEST["t"];
    $l = $t ? "Table " . $t : "whole DB";
    print_header();
    echo "<center>\r\n<h3>Export ";
    echo $l;
    echo "</h3>\r\n<div class=\"frm\">\r\n<input type=\"checkbox\" name=\"s\" value=\"1\" checked> Structure<br>\r\n<input type=\"checkbox\" name=\"d\" value=\"1\" checked> Data<br><br>\r\n<div><label><input type=\"radio\" name=\"et\" value=\"\" checked> .sql</label>&nbsp;</div>\r\n<div>\r\n";
    if ($t && !strpos($t, ",")) {
        echo " <label><input type=\"radio\" name=\"et\" value=\"csv\"> .csv (Excel style, data only and for one table only)</label>\r\n";
    } else {
        echo "<label>&nbsp;( ) .csv</label> <small>(to export as csv - go to 'show tables' and export just ONE table)</small>\r\n";
    }
    echo "</div>\r\n<br>\r\n<div><label><input type=\"checkbox\" name=\"gz\" value=\"1\"> compress as .gz</label></div>\r\n<br>\r\n<input type=\"hidden\" name=\"doex\" value=\"1\">\r\n<input type=\"hidden\" name=\"t\" value=\"";
    echo $t;
    echo "\">\r\n<input type=\"submit\" value=\" Download \"><input type=\"button\" value=\" Cancel \" onclick=\"window.location='";
    echo $self . "?" . $xurl . "&db=" . $DB["db"];
    echo "'\">\r\n</div>\r\n</center>\r\n";
    print_footer();
    exit;
}
function do_export()
{
    global $DB;
    global $VERSION;
    global $D;
    global $BOM;
    global $ex_isgz;
    $rt = str_replace("`", "", $_REQUEST["t"]);
    $t = explode(",", $rt);
    $th = array_flip($t);
    $ct = count($t);
    $z = db_row("show variables like 'max_allowed_packet'");
    $MAXI = floor($z["Value"] * 0.8);
    if (!$MAXI) {
        $MAXI = 838860;
    }
    $aext = "";
    $ctp = "";
    $ex_isgz = $_REQUEST["gz"] ? 1 : 0;
    if ($ex_isgz) {
        $aext = ".gz";
        $ctp = "application/x-gzip";
    }
    ex_start();
    if ($ct == 1 && $_REQUEST["et"] == "csv") {
        ex_hdr($ctp ? $ctp : "text/csv", (string) $t[0] . ".csv" . $aext);
        if ($DB["chset"] == "utf8") {
            ex_end($BOM);
        }
        $sth = db_query("select * from `" . $t[0] . "`");
        $fn = mysql_num_fields($sth);
        for ($i = 0; $i < $fn; $i++) {
            $m = mysql_fetch_field($sth, $i);
            ex_w(qstr($m->name) . ($i < $fn - 1 ? "," : ""));
        }
        ex_w($D);
        while ($row = mysql_fetch_row($sth)) {
            ex_w(to_csv_row($row));
        }
        ex_end();
        exit;
    }
    ex_hdr($ctp ? $ctp : "text/plain", (string) $DB["db"] . ($ct == 1 && $t[0] ? "." . $t[0] : (1 < $ct ? "." . $ct . "tables" : "")) . ".sql" . $aext);
    ex_w("-- phpMiniAdmin dump " . $VERSION . $D . "-- Datetime: " . date("Y-m-d H:i:s") . (string) $D . "-- Host: " . $DB["host"] . $D . "-- Database: " . $DB["db"] . $D . $D);
    ex_w("/*!40030 SET NAMES " . $DB["chset"] . " */;" . $D . "/*!40030 SET GLOBAL max_allowed_packet=16777216 */;" . $D . $D);
    $sth = db_query("show tables from `" . $DB["db"] . "`");
    while ($row = mysql_fetch_row($sth)) {
        if (!$rt || array_key_exists($row[0], $th)) {
            do_export_table($row[0], 1, $MAXI);
        }
    }
    ex_w((string) $D . "-- phpMiniAdmin dump end" . $D);
    ex_end();
    exit;
}
function do_export_table($t = "", $isvar = 0, $MAXI = 838860)
{
    global $D;
    set_time_limit(600);
    if ($_REQUEST["s"]) {
        $sth = db_query("show create table `" . $t . "`");
        $row = mysql_fetch_row($sth);
        $ct = preg_replace("/\n\r|\r\n|\n|\r/", $D, $row[1]);
        ex_w("DROP TABLE IF EXISTS `" . $t . "`;" . $D . $ct . ";" . $D . $D);
    }
    if ($_REQUEST["d"]) {
        $exsql = "";
        ex_w("/*!40000 ALTER TABLE `" . $t . "` DISABLE KEYS */;" . $D);
        $sth = db_query("select * from `" . $t . "`");
        while ($row = mysql_fetch_row($sth)) {
            $values = "";
            foreach ($row as $v) {
                $values .= ($values ? "," : "") . dbq($v);
            }
            $exsql .= ($exsql ? "," : "") . "(" . $values . ")";
            if ($MAXI < strlen($exsql)) {
                ex_w("INSERT INTO `" . $t . "` VALUES " . $exsql . ";" . $D);
                $exsql = "";
            }
        }
        if ($exsql) {
            ex_w("INSERT INTO `" . $t . "` VALUES " . $exsql . ";" . $D);
        }
        ex_w("/*!40000 ALTER TABLE `" . $t . "` ENABLE KEYS */;" . $D . $D);
    }
    flush();
}
function ex_hdr($ct, $fn)
{
    header("Content-type: " . $ct);
    header("Content-Disposition: attachment; filename=\"" . $fn . "\"");
}
function ex_start()
{
    global $ex_isgz;
    global $ex_gz;
    global $ex_tmpf;
    if ($ex_isgz) {
        $ex_tmpf = tempnam(sys_get_temp_dir(), "pma") . ".gz";
        if (!($ex_gz = gzopen($ex_tmpf, "wb9"))) {
            exit("Error trying to create gz tmp file");
        }
    }
}
function ex_w($s)
{
    global $ex_isgz;
    global $ex_gz;
    if ($ex_isgz) {
        gzwrite($ex_gz, $s, strlen($s));
    } else {
        echo $s;
    }
}
function ex_end()
{
    global $ex_isgz;
    global $ex_gz;
    global $ex_tmpf;
    if ($ex_isgz) {
        gzclose($ex_gz);
        readfile($ex_tmpf);
    }
}
function print_import()
{
    global $self;
    global $xurl;
    global $DB;
    print_header();
    echo "<center>\r\n<h3>Import DB</h3>\r\n<div class=\"frm\">\r\n<b>.sql</b> or <b>.gz</b> file: <input type=\"file\" name=\"file1\" value=\"\" size=40><br>\r\n<input type=\"hidden\" name=\"doim\" value=\"1\">\r\n<input type=\"submit\" value=\" Upload and Import \" onclick=\"return ays()\"><input type=\"button\" value=\" Cancel \" onclick=\"window.location='";
    echo $self . "?" . $xurl . "&db=" . $DB["db"];
    echo "'\">\r\n</div>\r\n<br><br><br>\r\n<!--\r\n<h3>Import one Table from CSV</h3>\r\n<div class=\"frm\">\r\n.csv file (Excel style): <input type=\"file\" name=\"file2\" value=\"\" size=40><br>\r\n<input type=\"checkbox\" name=\"r1\" value=\"1\" checked> first row contain field names<br>\r\n<small>(note: for success, field names should be exactly the same as in DB)</small><br>\r\nCharacter set of the file: <select name=\"chset\">";
    echo chset_select("utf8");
    echo "</select>\r\n<br><br>\r\nImport into:<br>\r\n<input type=\"radio\" name=\"tt\" value=\"1\" checked=\"checked\"> existing table:\r\n <select name=\"t\">\r\n <option value=''>- select -</option>\r\n ";
    echo sel(db_array("show tables", NULL, 0, 1), 0, "");
    echo "</select>\r\n<div style=\"margin-left:20px\">\r\n <input type=\"checkbox\" name=\"ttr\" value=\"1\"> replace existing DB data<br>\r\n <input type=\"checkbox\" name=\"tti\" value=\"1\"> ignore duplicate rows\r\n</div>\r\n<input type=\"radio\" name=\"tt\" value=\"2\"> create new table with name <input type=\"text\" name=\"tn\" value=\"\" size=\"20\">\r\n<br><br>\r\n<input type=\"hidden\" name=\"doimcsv\" value=\"1\">\r\n<input type=\"submit\" value=\" Upload and Import \" onclick=\"return ays()\"><input type=\"button\" value=\" Cancel \" onclick=\"window.location='";
    echo $self;
    echo "'\">\r\n</div>\r\n-->\r\n</center>\r\n";
    print_footer();
    exit;
}
function do_import()
{
    global $err_msg;
    global $out_message;
    global $dbh;
    global $SHOW_T;
    $err_msg = "";
    $F = $_FILES["file1"];
    if ($F && $F["name"]) {
        $filename = $F["tmp_name"];
        $pi = pathinfo($F["name"]);
        if ($pi["extension"] != "sql") {
            $tmpf = tempnam(sys_get_temp_dir(), "pma");
            if (($gz = gzopen($filename, "rb")) && ($tf = fopen($tmpf, "wb"))) {
                while (!gzeof($gz)) {
                    if (fwrite($tf, gzread($gz, 8192), 8192) === false) {
                        $err_msg = "Error during gz file extraction to tmp file";
                        break;
                    }
                }
                gzclose($gz);
                fclose($tf);
                $filename = $tmpf;
            } else {
                $err_msg = "Error opening gz file";
            }
        }
        if (!$err_msg) {
            if (!do_multi_sql("", $filename)) {
                $err_msg = "Import Error: " . mysql_error($dbh);
            } else {
                $out_message = "Import done successfully";
                do_sql($SHOW_T);
                return NULL;
            }
        }
    } else {
        $err_msg = "Error: Please select file first";
    }
    print_import();
    exit;
}
function do_multi_sql($insql, $fname = "")
{
    set_time_limit(600);
    $sql = "";
    $ochar = "";
    $is_cmt = "";
    $GLOBALS["insql_done"] = 0;
    while ($str = get_next_chunk($insql, $fname)) {
        $opos = 0 - strlen($ochar);
        $cur_pos = 0;
        $i = strlen($str);
        while ($i--) {
            if ($ochar) {
                list($clchar, $clpos) = get_close_char($str, $opos + strlen($ochar), $ochar);
                if ($clchar) {
                    if ($ochar == "--" || $ochar == "#" || $is_cmt) {
                        $sql .= substr($str, $cur_pos, $opos - $cur_pos);
                    } else {
                        $sql .= substr($str, $cur_pos, $clpos + strlen($clchar) - $cur_pos);
                    }
                    $cur_pos = $clpos + strlen($clchar);
                    $ochar = "";
                    $opos = 0;
                } else {
                    $sql .= substr($str, $cur_pos);
                    break;
                }
            } else {
                list($ochar, $opos) = get_open_char($str, $cur_pos);
                if ($ochar == ";") {
                    $sql .= substr($str, $cur_pos, $opos - $cur_pos + 1);
                    if (!do_one_sql($sql)) {
                        return 0;
                    }
                    $sql = "";
                    $cur_pos = $opos + strlen($ochar);
                    $ochar = "";
                    $opos = 0;
                } else {
                    if (!$ochar) {
                        $sql .= substr($str, $cur_pos);
                        break;
                    }
                    $is_cmt = 0;
                    if ($ochar == "/*" && substr($str, $opos, 3) != "/*!") {
                        $is_cmt = 1;
                    }
                }
            }
        }
    }
    if ($sql) {
        if (!do_one_sql($sql)) {
            return 0;
        }
        $sql = "";
    }
    return 1;
}
function get_next_chunk($insql, $fname)
{
    global $LFILE;
    global $insql_done;
    if ($insql) {
        if ($insql_done) {
            return "";
        }
        $insql_done = 1;
        return $insql;
    }
    if (!$fname) {
        return "";
    }
    if (!$LFILE) {
        $LFILE = fopen($fname, "r+b") or exit("Can't open [" . $fname . "] file \$!");
    }
    return fread($LFILE, 64 * 1024);
}
function get_open_char($str, $pos)
{
    if (preg_match("/(\\/\\*|^--|(?<=\\s)--|#|'|\"|;)/", $str, $m, PREG_OFFSET_CAPTURE, $pos)) {
        $ochar = $m[1][0];
        $opos = $m[1][1];
    }
    return array($ochar, $opos);
}
function get_close_char($str, $pos, $ochar)
{
    $aCLOSE = array("'" => "(?<!\\\\)'|(\\\\+)'", "\"" => "(?<!\\\\)\"", "/*" => "\\*\\/", "#" => "[\\r\\n]+", "--" => "[\\r\\n]+");
    if ($aCLOSE[$ochar] && preg_match("/(" . $aCLOSE[$ochar] . ")/", $str, $m, PREG_OFFSET_CAPTURE, $pos)) {
        $clchar = $m[1][0];
        $clpos = $m[1][1];
        $sl = strlen($m[2][0]);
        if ($ochar == "'" && $sl) {
            if ($sl % 2) {
                list($clchar, $clpos) = get_close_char($str, $clpos + strlen($clchar), $ochar);
            } else {
                $clpos += strlen($clchar) - 1;
                $clchar = "'";
            }
        }
    }
    return array($clchar, $clpos);
}
function do_one_sql($sql)
{
    global $last_sth;
    global $last_sql;
    global $MAX_ROWS_PER_PAGE;
    global $page;
    global $is_limited_sql;
    $sql = trim($sql);
    $sql = preg_replace("/;\$/", "", $sql);
    if ($sql) {
        $last_sql = $sql;
        $is_limited_sql = 0;
        if (preg_match("/^select/i", $sql) && !preg_match("/limit +\\d+/i", $sql)) {
            $offset = $page * $MAX_ROWS_PER_PAGE;
            $sql .= " LIMIT " . $offset . "," . $MAX_ROWS_PER_PAGE;
            $is_limited_sql = 1;
        }
        $last_sth = db_query($sql, 0, "noerr");
        return $last_sth;
    }
    return 1;
}
function do_sht()
{
    global $SHOW_T;
    $cb = $_REQUEST["cb"];
    if (!is_array($cb)) {
        $cb = array();
    }
    switch ($_REQUEST["dosht"]) {
        case "exp":
            $_REQUEST["t"] = join(",", $cb);
            print_export();
            exit;
        case "drop":
            $sq = "DROP TABLE";
            break;
        case "trunc":
            $sq = "TRUNCATE TABLE";
            break;
        case "opt":
            $sq = "OPTIMIZE TABLE";
    }
    if ($sq) {
        $sql = "";
        foreach ($cb as $v) {
            $sql .= $sq . " " . $v . ";\n";
        }
        if ($sql) {
            do_sql($sql);
        }
    }
    do_sql($SHOW_T);
}
function to_csv_row($adata)
{
    global $D;
    $r = "";
    foreach ($adata as $a) {
        $r .= ($r ? "," : "") . qstr($a);
    }
    return $r . $D;
}
function qstr($s)
{
    $s = nl2br($s);
    $s = str_replace("\"", "\"\"", $s);
    return "\"" . $s . "\"";
}
function get_rand_str($len)
{
    $result = "";
    $chars = preg_split("//", "ABCDEFabcdef0123456789");
    for ($i = 0; $i < $len; $i++) {
        $result .= $chars[rand(0, count($chars) - 1)];
    }
    return $result;
}
function check_xss()
{
    global $self;
    if ($_SESSION["XSS"] != trim($_REQUEST["XSS"])) {
        unset($_SESSION["XSS"]);
        header("location: " . $self);
        exit;
    }
}
function rw($s)
{
    echo $s . "<br>\n";
}

?>
