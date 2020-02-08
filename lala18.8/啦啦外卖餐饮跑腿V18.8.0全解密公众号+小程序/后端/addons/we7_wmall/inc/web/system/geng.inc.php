<?php
defined('IN_IA') or exit('Access Denied');

function cloud_w_request($url, $post = '', $extra = array(), $timeout = 60) {
	load()->func('communication');
	$response = ihttp_request($url, $post, $extra, $timeout);
	if(is_error($response)) {
		return error(-1, "错误: {$response['message']}");
	}
	return $response['content'];
}

function cloud_w_plugins() {
	global $_W;
	$plugins = pdo_getall('tiny_wmall_plugin', array(), array('name'), 'name');
	$plugins = array_keys($plugins);
	if(is_file(MODULE_ROOT . '/template/vue/index.html')) {
		$plugins[] = 'vue';
	}
	/*	if(!in_array('plateformApp', $plugins) && (is_file(MODULE_ROOT . '/template/plateform/index.html') || is_file(MODULE_ROOT . '/inc/wxapp/plateform/order/takeout.inc.php'))) {
			$plugins[] = 'plateformApp';
			$plugins['plateformApp_from'] = 'file';
		}*/
	return $plugins;
}

function cloud_w_query_auth($code, $module) {
	global $_W;
	$plugins = cloud_w_plugins();
	$params = array(
		'url' => rtrim($_W['siteroot'], "/"),
		'host' => $_SERVER['HTTP_HOST'],
		'code' => $code,
		'site_id' => $_W['setting']['site']['key'],
		'module' => $module,
		'method' => 'query_auth',
		'uniacid' => $_W['uniacid'],
		'plugins' => $plugins
	);
	$content = cloud_w_request('http://up.hao071.com/app/index.php?i=1&c=entry&do=auth&v=v2&m=tiny_manage', $params);
	if(is_error($content)) {
		return $content;
	}
	$result = @json_decode($content, true);
	if(empty($result['message'])) {
		return error(-1, "未知错误");
	}
	return $result['message'];
}

function cloud_w_client_define() {
	return array(
		'/model/cloud.mod.php',
	);
}

function cloud_w_build_params() {
	global $_W;
	$cache = cache_read('we7_wmall');
	if(empty($cache)) {
		$cache = get_global_config('auth');
	}
	$pars = array();
	$pars['module'] = 'we7_wmall';
	$pars['url'] = $_W['siteroot'];
	$pars['ip'] = CLIENT_IP;
	$pars['family'] = MODULE_FAMILY;
	$pars['version'] = MODULE_VERSION;
	$pars['release'] = MODULE_RELEASE_DATE;
	$pars['code'] = $cache['code'];
	$pars['cloud_id'] = $cache['cloud_id'];
	$pars['plugins'] = cloud_w_plugins();
	$pars['ionCube'] = extension_loaded('ionCube Loader') ? 1 : 0;
	$pars['mcrypt'] = extension_loaded('mcrypt') ? 1 : 0;
	$pars['loader'] = file_exists(IA_ROOT . '/web/loader.php') ? 1 : 0;
	$pars['php_version'] = PHP_VERSION;
	$pars['ifrom'] = 'we7';
	$pars['itouch'] = file_exists(IA_ROOT . '/app/zindex.php') ? 'zindex' : 'index';
	$clients = cloud_w_client_define();
	$string = '';
	foreach($clients as $cli) {
		$string .= md5_file(MODULE_ROOT . $cli);
	}
	$pars['client'] = md5($string);
	return $pars;
}

function cloud_w_shipping_parse($dat, $file) {
	if(is_error($dat)) {
		return error(-1, '网络传输错误, 请检查您的cURL是否可用, 或者服务器网络是否正常. ' . $dat['message']);
	}
	$dat_bak = $dat;
	$dat = @json_decode($dat, true);
	if(!is_array($dat)) {
		return error(-1, $dat_bak);
	}
	$dat = $dat['message'];
	if(is_error($dat)) {
		return $dat;
	}
	if (strlen($dat['message']) != 32) {
		return error(-1, '获取更新文件失败-1');
	}
	$data = @file_get_contents($file);
	if (empty($data)) {
		return error(-1, '获取更新文件失败-2.');
	}
	@unlink($file);
	$ret = @iunserializer($data);
	if (empty($data) || empty($ret) || $dat['message'] != $ret['secret']) {
		return error(-1, '云服务平台向您的服务器传输的数据校验失败, 可能是因为您的网络不稳定, 或网络不安全, 请稍后重试.');
	}
	$ret = iunserializer($ret['data']);
	return $ret;
}

function cloud_w_upgrade_version($family, $version, $release = 0) {
	$verfile = MODULE_ROOT . '/version.php';
	$verdat = <<<VER
<?php

defined('IN_IA') or exit('Access Denied');
define('MODULE_FAMILY', '{$family}');
define('MODULE_VERSION', '{$version}');
define('MODULE_RELEASE_DATE', '{$release}');
VER;
	file_put_contents($verfile, trim($verdat));
}

function cloud_w_i_build_base() {
	global $_W;
	$_W['ifrom'] = 'we7';
	$pars = cloud_w_build_params();
	$pars['method'] = 'upgrade';
	$dat = cloud_w_request('http:///app/index.php?i=0&c=entry&do=upgrade&op=build&v=v2&m=tiny_manage', $pars);
	$file = IA_ROOT . '/data/we7_wmall.build';
	$ret = cloud_w_shipping_parse($dat, $file);
	if(!is_error($ret)) {
		$cache = cache_read('we7_wmall');
		if(empty($cache)) {
			$cache = get_global_config('auth');
		}
		if(empty($cache['code'])) {
			$data = array(
				'cloud_id' => $ret['id'],
				'code' => $ret['code'],
				'gengxin' => $ret['update_type'],
				'addtime' => $ret['addtime'],
				'endtime' => $ret['endtime'],
				'code_status' => 1,
			);
			cache_write('we7_wmall', $data);
			set_global_config('auth', $data);
		}
		if($ret['family'] != MODULE_FAMILY) {
			if($ret['family'] == 'basic') {
				cloud_w_upgrade_version($ret['family'], '2.0.0', '1000');
			} elseif($ret['family'] == 'errander_deliveryerApp') {
				cloud_w_upgrade_version($ret['family'], '5.0.0', '1000');
			} elseif($ret['family'] == 'wxapp') {
				cloud_w_upgrade_version($ret['family'], '2.0.0', '1000');
			}
		}
		$files = array();
		if(!empty($ret['files'])) {
			foreach($ret['files'] as $file) {
				$entry = MODULE_ROOT . $file['path'];
				if(!is_file($entry) || md5_file($entry) != $file['checksum']) {
					$files[] = $file['path'];
				}
			}
		}
		$ret['files'] = $files;

		$schemas = array();
		if(!empty($ret['schemas'])) {
			load()->func('db');
			foreach($ret['schemas'] as $remote) {
				$name = substr($remote['tablename'], 4);
				$local = cloud_w_db_table_schema(pdo(), $name);
				unset($remote['increment']);
				unset($local['increment']);
				if(empty($local)) {
					$schemas[] = $remote;
				} else {
					$sqls = db_table_fix_sql($local, $remote);
					if(!empty($sqls)) {
						$schemas[] = $remote;
					}
				}
			}
		}
		$ret['schemas'] = $schemas;
		if(!empty($ret['schemas'])) {
			$ret['database'] = array();
			foreach($ret['schemas'] as $remote) {
				$row = array();
				$row['tablename'] = $remote['tablename'];
				$name = substr($remote['tablename'], 4);
				$local = cloud_w_db_table_schema(pdo(), $name);
				unset($remote['increment']);
				unset($local['increment']);
				if(empty($local)) {
					$row['new'] = true;
				} else {
					$row['new'] = false;
					$row['fields'] = array();
					$row['indexes'] = array();
					$diffs = db_schema_compare($local, $remote);
					if(!empty($diffs['fields']['less'])) {
						$row['fields'] = array_merge($row['fields'], $diffs['fields']['less']);
					}
					if(!empty($diffs['fields']['diff'])) {
						$row['fields'] = array_merge($row['fields'], $diffs['fields']['diff']);
					}
					if(!empty($diffs['indexes']['less'])) {
						$row['indexes'] = array_merge($row['indexes'], $diffs['indexes']['less']);
					}
					if(!empty($diffs['indexes']['diff'])) {
						$row['indexes'] = array_merge($row['indexes'], $diffs['indexes']['diff']);
					}
					$row['fields'] = implode($row['fields'], ' ');
					$row['indexes'] = implode($row['indexes'], ' ');
				}
				$ret['database'][] = $row;
			}
		}

		$ret['upgrade'] = false;
		if(!empty($ret['files']) || !empty($ret['schemas']) || !empty($ret['scripts'])) {
			$ret['upgrade'] = true;
		}
		$upgrade = array();
		$upgrade['upgrade'] = $ret['upgrade'];
		$upgrade['lastupdate'] = TIMESTAMP;
		cache_write('we7_wmall_upgrade', $upgrade);
	}
	return $ret;
}

function cloud_w_build_script($packet) {
	$scripts = array();
	$updatefiles = array();

	if (!empty($packet['scripts'])) {
		$updatedir = MODULE_ROOT . '/resource/update/';
		load()->func('file');
		rmdirs($updatedir, true);
		mkdirs($updatedir);

		$cfamily = MODULE_FAMILY;
		$cversion = MODULE_VERSION;
		$crelease = MODULE_RELEASE_DATE;
		$crelease_temp = intval($crelease);
		foreach($packet['scripts'] as $script) {
			if(($script['version'] < $cversion && $script['release'] <= $crelease) || ($crelease_temp > 0 && $script['release'] <= $crelease)) {
				continue;
			}
			$fname = "update({$cversion}-{$script['version']}_{$script['release']}).php";
			$script['script'] = @base64_decode($script['script']);
			if(empty($script['script'])) {
				continue;
			}
			$updatefile = $updatedir . $fname;
			file_put_contents($updatefile, $script['script']);
			$updatefiles[] = $updatefile;
			$s = array_elements(array('message', 'family', 'version', 'release'), $script);
			$s['fname'] = $fname;
			$scripts[] = $s;
		}
	}

	return $scripts;
}

function cloud_w_download($path) {
	$pars = cloud_w_build_params();
	$pars['method'] = 'download';
	$pars['path'] = $path;
	$pars['gz'] = function_exists('gzcompress') && function_exists('gzuncompress') ? 'true' : 'false';
	$headers = array('content-type' => 'application/x-www-form-urlencoded');
	$dat = cloud_w_request('http://up.hao071.com/app/index.php?i=0&c=entry&do=upgrade&op=download&v=v2&m=tiny_manage', $pars, $headers, 300);
	if(is_error($dat)) {
		return error(-1, '网络存在错误， 请稍后重试。' . $dat['message']);
	}
	$ret = @json_decode($dat, true);
	if(is_error($ret['message'])) {
		return $ret['message'];
	} else {
		return error(0, 'success');
	}
}

function cloud_w_parse_build($post) {
	global $_W;
	$dat = __secure_decode($post);
	if(!empty($dat)) {
		$secret = random(32);
		$ret = array();
		$ret['data'] = $dat;
		$ret['secret'] = $secret;
		file_put_contents(IA_ROOT . '/data/we7_wmall.build', iserializer($ret));
		return error(0, $secret);
	}
	return error(-1, '文件传输失败dd');
}

function cloud_w_parse_schema($post) {
	$dat = __secure_decode($post);
	if(!empty($dat)) {
		$secret = random(32);
		$ret = array();
		$ret['data'] = $dat;
		$ret['secret'] = $secret;
		file_put_contents(IA_ROOT . '/data/application.schema', iserializer($ret));
		exit($secret);
	}
}

function cloud_w_parse_download($post) {
	$data = base64_decode($post);
	if(base64_encode($data) != $post) {
		$data = $post;
	}
	$ret = iunserializer($data);
	$gz = function_exists('gzcompress') && function_exists('gzuncompress');
	$file = base64_decode($ret['file']);
	if($gz) {
		$file = gzuncompress($file);
	}
	$cache = cache_read('we7_wmall');
	if(empty($cache)) {
		$cache = get_global_config('auth');
	}
	$string = md5($file) . $ret['path'] . $cache['code'];
	if(md5($string) == $ret['sign']) {
		$path = IA_ROOT . $ret['path'];
		load()->func('file');
		@mkdirs(dirname($path));
		file_put_contents($path, $file);
		$sign = md5(md5_file($path) . $ret['path'] . $cache['code']);
		if($ret['sign'] == $sign) {
			return error(0, 'success');
		}
	}
	return error(-1, '文件校验失败');
}

function cloud_w_run_download($path) {
	global $_GPC;
	$ret = cloud_w_download($path);
	if(!is_error($ret)) {
		return error(0, 'success');
	}
	return $ret;
}

function cloud_w_run_script($fname) {
	global $_GPC;
	$entry = MODULE_ROOT . '/resource/update/' . $fname;
	if (is_file($entry) && preg_match('/^update\(\d{1,2}\.\d{1,2}\.\d{1,2}\-\d{1,2}\.\d{1}\.\d{1}\_\d{14}\)\.php$/', $fname)) {
		$evalret = include $entry;
		if(!empty($evalret)) {
			@unlink($entry);
			return error(0, 'success');
		}
	}
	return error(-1, 'failed');
}

function cloud_w_run_schemas($packet, $tablename) {
	global $_GPC;
	foreach($packet['schemas'] as $schema) {
		if (substr($schema['tablename'], 4) == $tablename) {
			$remote = $schema;
			break;
		}
	}
	if(!empty($remote)) {
		load()->func('db');
		$local = cloud_w_db_table_schema(pdo(), $tablename);
		$sqls = db_table_fix_sql($local, $remote);
		$error = false;
		foreach($sqls as $sql) {
			if (pdo_query($sql) === false) {
				$error = true;
				break;
			}
		}
		if (!$error) {
			return error(0, 'success');
		}
	}
	return error(-1, 'failed');
}

function __secure_decode($post) {
	global $_W;
	$data = base64_decode($post);
	if (base64_encode($data) != $post) {
		$data = $post;
	}
	$ret = iunserializer($data);
	$cache = cache_read('we7_wmall');
	if(empty($cache)) {
		$cache = get_global_config('auth');
	}
	if($_W['ifrom'] == 'we7') {
		return $ret['data'];
	}
	$string = $ret['data'] . $cache['code'];
	if(md5($string) == $ret['sign']) {
		return $ret['data'];
	}
	return false;
}

function cloud_w_db_table_schema($db, $tablename = '') {
	$result = $db->fetch("SHOW TABLE STATUS LIKE '" . trim($db->tablename($tablename), '`') . "'");
	if(empty($result)) {
		return array();
	}
	$ret['tablename'] = $result['Name'];
	$ret['charset'] = $result['Collation'];
	$ret['engine'] = $result['Engine'];
	$ret['increment'] = $result['Auto_increment'];
	$result = $db->fetchall("SHOW FULL COLUMNS FROM " . $db->tablename($tablename));
	foreach($result as $value) {
		$temp = array();
		$type = explode(" ", $value['Type'], 2);
		$temp['name'] = $value['Field'];
		$pieces = explode('(', $type[0], 2);
		$temp['type'] = $pieces[0];
		$temp['length'] = rtrim($pieces[1], ')');
		$temp['null'] = $value['Null'] != 'NO';
		if(isset($value['Default'])) {
			$temp['default'] = $value['Default'];
		}
		$temp['signed'] = empty($type[1]);
		$temp['increment'] = $value['Extra'] == 'auto_increment';
		$ret['fields'][$value['Field']] = $temp;
	}
	$result = $db->fetchall("SHOW INDEX FROM " . $db->tablename($tablename));
	foreach($result as $value) {
		$ret['indexes'][$value['Key_name']]['name'] = $value['Key_name'];
		$ret['indexes'][$value['Key_name']]['type'] = ($value['Key_name'] == 'PRIMARY') ? 'primary' : ($value['Non_unique'] == 0 ? 'unique' : 'index');
		$ret['indexes'][$value['Key_name']]['fields'][] = $value['Column_name'];
	}
	return $ret;
}

function databaseEngine_transfer() {
	global $_W;
	return true;
	$sql = "SHOW TABLE STATUS WHERE name LIKE '" . $_W['config']['db']['master']['tablepre']."tiny_wmall_%'";
	$tables = pdo_fetchall($sql);
	$InnoDBs = array();
	foreach($tables as $table) {
		$result = pdo_fetch("SHOW TABLE STATUS LIKE '" . trim($table['Name'], '`') . "'");
		if($result['Engine'] == 'InnoDB') {
			$InnoDBs[] = $table['Name'];
			$sql = "ALTER TABLE `{$table['Name']}` ENGINE = MyISAM;";
			pdo_query($sql);
		}
	}
	return true;
}

function iscript($msg) {
	$str = "<script>alert('".$msg."');history.go(-1);</script>";
	echo $str;
	die();
}

function set_global_config($key, $value) {
	global $_W;
	$_W['uniacid'] = 0;
	$status = set_system_config($key, $value);
	return $status;
}

function get_global_config($key = '') {
	$result = get_system_config($key, 0);
	return $result;
}

function set_system_config($key, $value) {
	global $_W;
	$sysset = get_system_config();
	$keys = explode('.', $key);
	$counts = count($keys);
	if($counts == 1) {
		$sysset[$keys[0]] = $value;
	} elseif($counts == 2) {
		if(!is_array($sysset[$keys[0]])) {
			$sysset[$keys[0]] = array();
		}
		$sysset[$keys[0]][$keys[1]] = $value;
	} elseif($counts == 3) {
		if(!is_array($sysset[$keys[0]])) {
			$sysset[$keys[0]] = array();
		} elseif(!is_array($sysset[$keys[0]][$keys[1]])) {
			$sysset[$keys[0]][$keys[1]] = array();
		}
		$sysset[$keys[0]][$keys[1]][$keys[2]] = $value;
	}
	pdo_update('tiny_wmall_config', array('sysset' => iserializer($sysset)), array('uniacid' => $_W['uniacid']));
	return true;
}

if(!function_exists('get_system_config')) {
	function get_system_config($key = '', $uniacid = -1) {
		global $_W;
		if($uniacid == -1) {
			$uniacid = intval($_W['uniacid']);
		}
		$config = pdo_get('tiny_wmall_config', array('uniacid' => $uniacid), array('sysset', 'pluginset', 'id'));
		if(empty($config['id'])) {
			$init_config = array(
				'uniacid' => $uniacid
			);
			pdo_insert('tiny_wmall_config', $init_config);
			return array();
		}
		if(defined('IN_WXAPP') && $key == 'payment') {
			$pluginset = iunserializer($config['pluginset']);
			$config_wxapp = $pluginset['wxapp'];
			return $config_wxapp['payment'];
		}
		$sysset = iunserializer($config['sysset']);
		if(!is_array($sysset)) {
			$sysset = array();
		}
		$pluginset = iunserializer($config['pluginset']);
		if(!is_array($pluginset)) {
			$pluginset = array();
		}
		$sysset['wxapp'] = $pluginset['wxapp'];
		unset($sysset['wxapp']['menu'], $sysset['wxapp']['extPages']);
		$_W['is_agentconfig'] = 0;
		if($_W['agentid'] > 0) {
			$sysset['manager_plateform'] = $sysset['manager'];
			$sysset_agent = get_agent_system_config();
			if(!empty($sysset_agent)) {
				$sysset = multimerge($sysset, $sysset_agent);
			}
			$_W['is_agentconfig'] = $_W['agentid'];
		}
		if(empty($sysset['takeout']) || empty($sysset['takeout']['range']['map']['location_x'])) {
			$sysset['takeout']['range']['map'] = array(
				'location_x' => '39.908743',
				'location_y' => '116.397573',
			);
		}
		if(empty($sysset['sms']['verify'])) {
			$sysset['sms']['verify'] = array(
				'clerk_register' => 1,
				'consumer_register' => 1
			);
		}
		if(!empty($sysset['mall']['logo'])) {
			$sysset['mall']['logo'] = tomedia($sysset['mall']['logo']);
		}
		if(empty($key)) {
			return $sysset;
		}
		$keys = explode('.', $key);
		$counts = count($keys);
		if($counts == 1) {
			return $sysset[$key];
		} elseif($counts == 2) {
			return $sysset[$keys[0]][$keys[1]];
		} elseif($counts == 3) {
			return $sysset[$keys[0]][$keys[1]][$keys[2]];
		}
	}
}
function run_install_data(){}
if(!function_exists('p')) {
	function p($data){
		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}
}

?>