<?php
/*
 * 9gan.com 出品 9GAN PHP探针
 * ==========================
 * version: 2014-04-10
 * website: http://www.9gan.com
 * website: http://www.73yi.net
 * ==========================
 * 目前功能最强大，界面最清爽的PHP探针
 * ==========================
 * 2014-04-10 增加以下功能：
 * 1. 增加显示Memcache和Zend OPcache
 * 2. 增加PostgreSQL测试
 * 3. 删除多年不更新的第三方PHP组件：eAccelerator
 * ==========================
 * ==========================
 * 2012-04-13 增加以下功能：
 * 1. 增加显示MySQLite3版本查询
 * 2. 增加网速测试
 * 3. 增加实时更新显示系统负载和内存硬盘使用状态
 * ==========================
 * 2010-09-23 修正问题：
 * 1. 在Win下操作系统名称乱码问题
 * 2. 在Win下的内存和交换区为0显示100%问题
 * 3. 添加几个主流VPS测试数据
 * ==========================
 * 2010-04-10 增加以下功能：
 * 1. 网络流量使用状态
 * 2. 内存和SWAP更详细状态
 * 3. CPU信息更详细
 * 4. MySQL版本查询
 * 5. PHP扩展查询
 * 6. GD库版本查询
 * 7. 增加脚本执行时间和内存使用统计
 * 8. 增加参数安全过滤
 * 9. 修正样式，完美兼容所有浏览器
 */

error_reporting(0); //抑制所有错误信息
phpversion() >= '5.1.0' && date_default_timezone_set('UTC');
@header("content-Type: text/html; charset=utf-8"); //语言强制
ob_start();

$title = "9gan.com PHP探针";
$version = "v2014.04.10"; //版本号

define('HTTP_HOST', preg_replace('~^www\.~i', '', $_SERVER['HTTP_HOST']));

$time_start = microtime_float();

function memory_usage() {
    $memory = (!function_exists('memory_get_usage')) ? '0' : round(memory_get_usage
        () / 1024 / 1024, 2) . 'MB';
    return $memory;
}

// 计时
function microtime_float() {
    $mtime = microtime();
    $mtime = explode(' ', $mtime);
    return $mtime[1] + $mtime[0];
}

//单位转换
function formatsize($size) {
    $danwei = array(' B ', ' K ', ' M ', ' G ', ' T ');
    $allsize = array();
    $i = 0;

    for ($i = 0; $i < 4; $i++) {
        if (floor($size / pow(1024, $i)) == 0) {
            break;
        }
    }

    for ($l = $i - 1; $l >= 0; $l--) {
        $allsize1[$l] = floor($size / pow(1024, $l));
        $allsize[$l] = $allsize1[$l] - $allsize1[$l + 1] * 1024;
    }

    $len = count($allsize);

    for ($j = $len - 1; $j >= 0; $j--) {
        $strlen = 4 - strlen($allsize[$j]);
        if ($strlen == 1)
            $allsize[$j] = "<font color='#FFFFFF'>0</font>" . $allsize[$j];
        elseif ($strlen == 2)
            $allsize[$j] = "<font color='#FFFFFF'>00</font>" . $allsize[$j];
        elseif ($strlen == 3)
            $allsize[$j] = "<font color='#FFFFFF'>000</font>" . $allsize[$j];

        $fsize = $fsize . $allsize[$j] . $danwei[$j];
    }
    return $fsize;
}

function valid_email($str) {
    return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix",
        $str)) ? false : true;
}

//检测PHP设置参数
function show($varName) {
    switch ($result = get_cfg_var($varName)) {
        case 0:
            return '<em>×</em>';
            break;

        case 1:
            return '<b>√</b>';
            break;

        default:
            return $result;
            break;
    }
}

//保留服务器性能测试结果
$valInt = isset($_POST['pInt']) ? $_POST['pInt'] : "未测试";
$valFloat = isset($_POST['pFloat']) ? $_POST['pFloat'] : "未测试";
$valIo = isset($_POST['pIo']) ? $_POST['pIo'] : "未测试";

if ($_GET['act'] == "phpinfo") {
    phpinfo();
    exit();
} elseif ($_POST['act'] == "整型测试") {
    $valInt = test_int();
} elseif ($_POST['act'] == "浮点测试") {
    $valFloat = test_float();
} elseif ($_POST['act'] == "IO测试") {
    $valIo = test_io();
}
//网速测试-开始
elseif ($_POST['act'] == "开始测试") {

?>
	<script language="javascript" type="text/javascript">
		var acd1;
		acd1 = new Date();
		acd1ok=acd1.getTime();
	</script>
	<?php

    for ($i = 1; $i <= 100000; $i++) {
        echo "<!--567890#########0#########0#########0#########0#########0#########0#########0#########012345-->";
    }

?>
	<script language="javascript" type="text/javascript">
		var acd2;
		acd2 = new Date();
		acd2ok=acd2.getTime();
		window.location = '?speed=' +(acd2ok-acd1ok);
	</script>
<?php

}
//网速测试-结束
elseif ($_GET['act'] == "Function") {
    $arr = get_defined_functions();
    function php() {
    }
    echo "<pre>";
    echo "这里显示系统所支持的所有函数,和自定义函数\n";
    print_r($arr);
    echo "</pre>";
    exit();
} elseif ($_GET['act'] == "disable_functions") {
    $disFuns = get_cfg_var("disable_functions");
    if (empty($disFuns)) {
        $arr = '<em>×</em>';
    } else {
        $arr = $disFuns;
    }
    function php() {
    }
    echo "<pre>";
    echo "这里显示系统被禁用的函数\n";
    print_r($arr);
    echo "</pre>";
    exit();
}

//MySQL检测
if ($_POST['act'] == 'MySQL检测') {
    $host = isset($_POST['host']) ? trim($_POST['host']) : '';
    $port = isset($_POST['port']) ? (int)$_POST['port'] : '';
    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $host = preg_match('~[^a-z0-9\-\.]+~i', $host) ? '' : $host;
    $port = intval($port) ? intval($port) : '';
    $login = preg_match('~[^a-z0-9\_\-]+~i', $login) ? '' : htmlspecialchars($login);
    $password = is_string($password) ? htmlspecialchars($password) : '';
} elseif ($_POST['act'] == 'PostgreSQL检测') {
    $host = isset($_POST['host']) ? trim($_POST['host']) : '';
    $port = isset($_POST['port']) ? (int)$_POST['port'] : '';
    $dbname = isset($_POST['dbname']) ? trim($_POST['dbname']) : '';
    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $host = preg_match('~[^a-z0-9\-\.]+~i', $host) ? '' : $host;
    $port = intval($port) ? intval($port) : '';
    $dbname = preg_match('~[^a-z0-9\-\.]+~i', $dbname) ? '' : $dbname;
    $login = preg_match('~[^a-z0-9\_\-]+~i', $login) ? '' : htmlspecialchars($login);
    $password = is_string($password) ? htmlspecialchars($password) : '';
} elseif ($_POST['act'] == '函数检测') {
    $funRe = "函数" . $_POST['funName'] . "支持状况检测结果：" . isfun1($_POST['funName']);
} elseif ($_POST['act'] == '邮件检测') {
    $mailRe = "邮件发送检测结果：发送";
    $mailRe .= (false !== @mail($_POST["mailAdd"], "http://" . $_SERVER['SERVER_NAME'] .
        ($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']),
        "This is a test mail!")) ? "完成" : "失败";
}

//网络速度测试
if (isset($_POST['speed'])) {
    $speed = round(100 / ($_POST['speed'] / 1000), 2);
} elseif ($_GET['speed'] == "0") {
    $speed = 6666.67;
} elseif (isset($_GET['speed']) and $_GET['speed'] > 0) {
    $speed = round(100 / ($_GET['speed'] / 1000), 2); //下载速度：$speed kb/s
} else {
    $speed = "<font color=\"red\">&nbsp;未探测&nbsp;</font>";
}


// 检测函数支持
function isfun($funName = '') {
    if (!$funName || trim($funName) == '' || preg_match('~[^a-z0-9\_]+~i', $funName,
        $tmp))
        return '错误';
    return (false !== function_exists($funName)) ? '<b>√</b>' : '<em>×</em>';
}
function isfun1($funName = '') {
    if (!$funName || trim($funName) == '' || preg_match('~[^a-z0-9\_]+~i', $funName,
        $tmp))
        return '错误';
    return (false !== function_exists($funName)) ? '√' : '×';
}

//整数运算能力测试
function test_int() {
    $timeStart = gettimeofday();
    for ($i = 0; $i < 3000000; $i++) {
        $t = 1 + 1;
    }
    $timeEnd = gettimeofday();
    $time = ($timeEnd["usec"] - $timeStart["usec"]) / 1000000 + $timeEnd["sec"] - $timeStart["sec"];
    $time = round($time, 3) . "秒";
    return $time;
}

//浮点运算能力测试
function test_float() {
    //得到圆周率值
    $t = pi();
    $timeStart = gettimeofday();

    for ($i = 0; $i < 3000000; $i++) {
        //开平方
        sqrt($t);
    }

    $timeEnd = gettimeofday();
    $time = ($timeEnd["usec"] - $timeStart["usec"]) / 1000000 + $timeEnd["sec"] - $timeStart["sec"];
    $time = round($time, 3) . "秒";
    return $time;
}

//IO能力测试
function test_io() {
    $fp = @fopen(PHPSELF, "r");
    $timeStart = gettimeofday();
    for ($i = 0; $i < 10000; $i++) {
        @fread($fp, 10240);
        @rewind($fp);
    }
    $timeEnd = gettimeofday();
    @fclose($fp);
    $time = ($timeEnd["usec"] - $timeStart["usec"]) / 1000000 + $timeEnd["sec"] - $timeStart["sec"];
    $time = round($time, 3) . "秒";
    return ($time);
}

// 根据不同系统取得CPU相关信息
switch (PHP_OS) {
    case "Linux":
        $sysReShow = (false !== ($sysInfo = sys_linux())) ? "show":
        "none";
        break;

    case "FreeBSD":
        $sysReShow = (false !== ($sysInfo = sys_freebsd())) ? "show":
        "none";
        break;

        /*
        case "WINNT":
        $sysReShow = (false !== ($sysInfo = sys_windows()))?"show":"none";
        break;
        */

    default:
        break;
}

//linux系统探测
function sys_linux() {
    // CPU
    if (false === ($str = @file("/proc/cpuinfo")))
        return false;
    $str = implode("", $str);
    @preg_match_all("/model\s+name\s{0,}\:+\s{0,}([\w\s\)\(\@.-]+)([\r\n]+)/s", $str,
        $model);
    @preg_match_all("/cpu\s+MHz\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $mhz);
    @preg_match_all("/cache\s+size\s{0,}\:+\s{0,}([\d\.]+\s{0,}[A-Z]+[\r\n]+)/", $str,
        $cache);
    @preg_match_all("/bogomips\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $bogomips);
    if (false !== is_array($model[1])) {
        $res['cpu']['num'] = sizeof($model[1]);
        /*
        for($i = 0; $i < $res['cpu']['num']; $i++)
        {
        $res['cpu']['model'][] = $model[1][$i].'&nbsp;('.$mhz[1][$i].')';
        $res['cpu']['mhz'][] = $mhz[1][$i];
        $res['cpu']['cache'][] = $cache[1][$i];
        $res['cpu']['bogomips'][] = $bogomips[1][$i];
        }*/
        if ($res['cpu']['num'] == 1)
            $x1 = '';
        else
            $x1 = ' ×' . $res['cpu']['num'];
        $mhz[1][0] = ' | 频率:' . $mhz[1][0];
        $cache[1][0] = ' | 二级缓存:' . $cache[1][0];
        $bogomips[1][0] = ' | Bogomips:' . $bogomips[1][0];
        $res['cpu']['model'][] = $model[1][0] . $mhz[1][0] . $cache[1][0] . $bogomips[1][0] .
            $x1;
        if (false !== is_array($res['cpu']['model']))
            $res['cpu']['model'] = implode("<br />", $res['cpu']['model']);
        if (false !== is_array($res['cpu']['mhz']))
            $res['cpu']['mhz'] = implode("<br />", $res['cpu']['mhz']);
        if (false !== is_array($res['cpu']['cache']))
            $res['cpu']['cache'] = implode("<br />", $res['cpu']['cache']);
        if (false !== is_array($res['cpu']['bogomips']))
            $res['cpu']['bogomips'] = implode("<br />", $res['cpu']['bogomips']);
    }

    // NETWORK

    // UPTIME
    if (false === ($str = @file("/proc/uptime")))
        return false;
    $str = explode(" ", implode("", $str));
    $str = trim($str[0]);
    $min = $str / 60;
    $hours = $min / 60;
    $days = floor($hours / 24);
    $hours = floor($hours - ($days * 24));
    $min = floor($min - ($days * 60 * 24) - ($hours * 60));
    if ($days !== 0)
        $res['uptime'] = $days . "天";
    if ($hours !== 0)
        $res['uptime'] .= $hours . "小时";
    $res['uptime'] .= $min . "分钟";

    // MEMORY
    if (false === ($str = @file("/proc/meminfo")))
        return false;
    $str = implode("", $str);
    preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?Cached\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s",
        $str, $buf);
    preg_match_all("/Buffers\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buffers);

    $res['memTotal'] = round($buf[1][0] / 1024, 2);
    $res['memFree'] = round($buf[2][0] / 1024, 2);
    $res['memBuffers'] = round($buffers[1][0] / 1024, 2);
    $res['memCached'] = round($buf[3][0] / 1024, 2);
    $res['memUsed'] = $res['memTotal'] - $res['memFree'];
    $res['memPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memUsed'] /
        $res['memTotal'] * 100, 2) : 0;

    $res['memRealUsed'] = $res['memTotal'] - $res['memFree'] - $res['memCached'] - $res['memBuffers']; //真实内存使用
    $res['memRealFree'] = $res['memTotal'] - $res['memRealUsed']; //真实空闲
    $res['memRealPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memRealUsed'] /
        $res['memTotal'] * 100, 2) : 0; //真实内存使用率

    $res['memCachedPercent'] = (floatval($res['memCached']) != 0) ? round($res['memCached'] /
        $res['memTotal'] * 100, 2) : 0; //Cached内存使用率

    $res['swapTotal'] = round($buf[4][0] / 1024, 2);
    $res['swapFree'] = round($buf[5][0] / 1024, 2);
    $res['swapUsed'] = round($res['swapTotal'] - $res['swapFree'], 2);
    $res['swapPercent'] = (floatval($res['swapTotal']) != 0) ? round($res['swapUsed'] /
        $res['swapTotal'] * 100, 2) : 0;

    // LOAD AVG
    if (false === ($str = @file("/proc/loadavg")))
        return false;
    $str = explode(" ", implode("", $str));
    $str = array_chunk($str, 4);
    $res['loadAvg'] = implode(" ", $str[0]);

    return $res;
}

//FreeBSD系统探测
function sys_freebsd() {
    //CPU
    if (false === ($res['cpu']['num'] = get_key("hw.ncpu")))
        return false;
    $res['cpu']['model'] = get_key("hw.model");
    //LOAD AVG
    if (false === ($res['loadAvg'] = get_key("vm.loadavg")))
        return false;
    //UPTIME
    if (false === ($buf = get_key("kern.boottime")))
        return false;
    $buf = explode(' ', $buf);
    $sys_ticks = time() - intval($buf[3]);
    $min = $sys_ticks / 60;
    $hours = $min / 60;
    $days = floor($hours / 24);
    $hours = floor($hours - ($days * 24));
    $min = floor($min - ($days * 60 * 24) - ($hours * 60));
    if ($days !== 0)
        $res['uptime'] = $days . "天";
    if ($hours !== 0)
        $res['uptime'] .= $hours . "小时";
    $res['uptime'] .= $min . "分钟";
    //MEMORY
    if (false === ($buf = get_key("hw.physmem")))
        return false;
    $res['memTotal'] = round($buf / 1024 / 1024, 2);

    $str = get_key("vm.vmtotal");
    preg_match_all("/\nVirtual Memory[\:\s]*\(Total[\:\s]*([\d]+)K[\,\s]*Active[\:\s]*([\d]+)K\)\n/i",
        $str, $buff, PREG_SET_ORDER);
    preg_match_all("/\nReal Memory[\:\s]*\(Total[\:\s]*([\d]+)K[\,\s]*Active[\:\s]*([\d]+)K\)\n/i",
        $str, $buf, PREG_SET_ORDER);

    $res['memRealUsed'] = round($buf[0][2] / 1024, 2);
    $res['memCached'] = round($buff[0][2] / 1024, 2);
    $res['memUsed'] = round($buf[0][1] / 1024, 2) + $res['memCached'];
    $res['memFree'] = $res['memTotal'] - $res['memUsed'];
    $res['memPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memUsed'] /
        $res['memTotal'] * 100, 2) : 0;

    $res['memRealPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memRealUsed'] /
        $res['memTotal'] * 100, 2) : 0;

    return $res;
}

//取得参数值 FreeBSD
function get_key($keyName) {
    return do_command('sysctl', "-n $keyName");
}

//确定执行文件位置 FreeBSD
function find_command($commandName) {
    $path = array('/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin',
        '/usr/local/sbin');
    foreach ($path as $p) {
        if (@is_executable("$p/$commandName"))
            return "$p/$commandName";
    }
    return false;
}

//执行系统命令 FreeBSD
function do_command($commandName, $args) {
    $buffer = "";
    if (false === ($command = find_command($commandName)))
        return false;
    if ($fp = @popen("$command $args", 'r')) {
        while (!@feof($fp)) {
            $buffer .= @fgets($fp, 4096);
        }
        return trim($buffer);
    }
    return false;
}

//windows系统探测
function sys_windows() {
    if (PHP_VERSION >= 5) {
        $objLocator = new COM("WbemScripting.SWbemLocator");
        $wmi = $objLocator->ConnectServer();
        $prop = $wmi->get("Win32_PnPEntity");
    } else {
        return false;
    }

    //CPU
    $cpuinfo = GetWMI($wmi, "Win32_Processor", array("Name", "L2CacheSize",
        "NumberOfCores"));
    $res['cpu']['num'] = $cpuinfo[0]['NumberOfCores'];
    if (null == $res['cpu']['num']) {
        $res['cpu']['num'] = 1;
    }
    /*
    for ($i=0;$i<$res['cpu']['num'];$i++)
    {
    $res['cpu']['model'] .= $cpuinfo[0]['Name']."<br />";
    $res['cpu']['cache'] .= $cpuinfo[0]['L2CacheSize']."<br />";
    }*/
    $cpuinfo[0]['L2CacheSize'] = ' (' . $cpuinfo[0]['L2CacheSize'] . ')';
    if ($res['cpu']['num'] == 1)
        $x1 = '';
    else
        $x1 = ' ×' . $res['cpu']['num'];
    $res['cpu']['model'] = $cpuinfo[0]['Name'] . $cpuinfo[0]['L2CacheSize'] . $x1;
    // SYSINFO
    $sysinfo = GetWMI($wmi, "Win32_OperatingSystem", array('LastBootUpTime',
        'TotalVisibleMemorySize', 'FreePhysicalMemory', 'Caption', 'CSDVersion',
        'SerialNumber', 'InstallDate'));
    $sysinfo[0]['Caption'] = iconv('GBK', 'UTF-8', $sysinfo[0]['Caption']);
    $sysinfo[0]['CSDVersion'] = iconv('GBK', 'UTF-8', $sysinfo[0]['CSDVersion']);
    $res['win_n'] = $sysinfo[0]['Caption'] . " " . $sysinfo[0]['CSDVersion'] .
        " 序列号:{$sysinfo[0]['SerialNumber']} 于" . date('Y年m月d日H:i:s', strtotime(substr($sysinfo[0]['InstallDate'],
        0, 14))) . "安装";
    //UPTIME
    $res['uptime'] = $sysinfo[0]['LastBootUpTime'];

    $sys_ticks = 3600 * 8 + time() - strtotime(substr($res['uptime'], 0, 14));
    $min = $sys_ticks / 60;
    $hours = $min / 60;
    $days = floor($hours / 24);
    $hours = floor($hours - ($days * 24));
    $min = floor($min - ($days * 60 * 24) - ($hours * 60));
    if ($days !== 0)
        $res['uptime'] = $days . "天";
    if ($hours !== 0)
        $res['uptime'] .= $hours . "小时";
    $res['uptime'] .= $min . "分钟";

    //MEMORY
    $res['memTotal'] = round($sysinfo[0]['TotalVisibleMemorySize'] / 1024, 2);
    $res['memFree'] = round($sysinfo[0]['FreePhysicalMemory'] / 1024, 2);
    $res['memUsed'] = $res['memTotal'] - $res['memFree']; //上面两行已经除以1024,这行不用再除了
    $res['memPercent'] = round($res['memUsed'] / $res['memTotal'] * 100, 2);

    $swapinfo = GetWMI($wmi, "Win32_PageFileUsage", array('AllocatedBaseSize',
        'CurrentUsage'));

    // LoadPercentage
    $loadinfo = GetWMI($wmi, "Win32_Processor", array("LoadPercentage"));
    $res['loadAvg'] = $loadinfo[0]['LoadPercentage'];

    return $res;
}

function GetWMI($wmi, $strClass, $strValue = array()) {
    $arrData = array();

    $objWEBM = $wmi->Get($strClass);
    $arrProp = $objWEBM->Properties_;
    $arrWEBMCol = $objWEBM->Instances_();
    foreach ($arrWEBMCol as $objItem) {
        @reset($arrProp);
        $arrInstance = array();
        foreach ($arrProp as $propItem) {
            eval("\$value = \$objItem->" . $propItem->Name . ";");
            if (empty($strValue)) {
                $arrInstance[$propItem->Name] = trim($value);
            } else {
                if (in_array($propItem->Name, $strValue)) {
                    $arrInstance[$propItem->Name] = trim($value);
                }
            }
        }
        $arrData[] = $arrInstance;
    }
    return $arrData;
}

//比例条
function bar($percent) {
	return '<div class="bar"><div class="barli" style="width:'.$percent.'%">&nbsp;</div></div>';
}

$uptime = $sysInfo['uptime'];
$stime = date("Y-n-j H:i:s");

$hdd_total = round(@disk_total_space(".") / (1024 * 1024 * 1024), 3);
$hdd_free = round(@disk_free_space(".") / (1024 * 1024 * 1024), 3);
$hdd_usage = $hdd_total - $hdd_free;
$hdd_percent = round($hdd_usage / $hdd_total * 100, 2);

$load = $sysInfo['loadAvg']; //系统负载


//判断内存如果小于1G，就显示M，否则显示G单位
if ($sysInfo['memTotal'] < 1024) {
    $memTotal = $sysInfo['memTotal'] . " M";
    $mt = $sysInfo['memTotal'] . " M";
    $mu = $sysInfo['memUsed'] . " M";
    $mf = $sysInfo['memFree'] . " M";
    $mc = $sysInfo['memCached'] . " M"; //cache化内存
    $mb = $sysInfo['memBuffers'] . " M"; //缓冲
    $st = $sysInfo['swapTotal'] . " M";
    $su = $sysInfo['swapUsed'] . " M";
    $sf = $sysInfo['swapFree'] . " M";
    $swapPercent = $sysInfo['swapPercent'];
    $memRealUsed = $sysInfo['memRealUsed'] . " M"; //真实内存使用
    $memRealFree = $sysInfo['memRealFree'] . " M"; //真实内存空闲
    $memRealPercent = $sysInfo['memRealPercent']; //真实内存使用比率
    $memPercent = $sysInfo['memPercent']; //内存总使用率
    $memCachedPercent = $sysInfo['memCachedPercent']; //cache内存使用率
} else {
    $memTotal = round($sysInfo['memTotal'] / 1024, 3) . " G";
    $mt = round($sysInfo['memTotal'] / 1024, 3) . " G";
    $mu = round($sysInfo['memUsed'] / 1024, 3) . " G";
    $mf = round($sysInfo['memFree'] / 1024, 3) . " G";
    $mc = round($sysInfo['memCached'] / 1024, 3) . " G";
    $mb = round($sysInfo['memBuffers'] / 1024, 3) . " G";
    $st = round($sysInfo['swapTotal'] / 1024, 3) . " G";
    $su = round($sysInfo['swapUsed'] / 1024, 3) . " G";
    $sf = round($sysInfo['swapFree'] / 1024, 3) . " G";
    $swapPercent = $sysInfo['swapPercent'];
    $memRealUsed = round($sysInfo['memRealUsed'] / 1024, 3) . " G"; //真实内存使用
    $memRealFree = round($sysInfo['memRealFree'] / 1024, 3) . " G"; //真实内存空闲
    $memRealPercent = $sysInfo['memRealPercent']; //真实内存使用比率
    $memPercent = $sysInfo['memPercent']; //内存总使用率
    $memCachedPercent = $sysInfo['memCachedPercent']; //cache内存使用率
}

//网卡流量
$strs = @file("/proc/net/dev");

for ($i = 2; $i < count($strs); $i++) {
    preg_match_all("/([^\s]+):[\s]{0,}(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/",
        $strs[$i], $info);
    $NetInput[$i] = formatsize($info[2][0]);
    $NetOut[$i] = formatsize($info[10][0]);
    /*
    $tmo = round($info[2][0]/1024/1024, 5);
    $tmo2 = round($tmo / 1024, 5);
    $NetInput[$i] = $tmo2;
    $tmp = round($info[10][0]/1024/1024, 5);
    $tmp2 = round($tmp / 1024, 5);
    $NetOut[$i] = $tmp2;
    */
}

//ajax调用实时刷新
if ($_GET['act'] == "rt") {
    $arr = array('freeSpace' => "$hdd_free", 'TotalMemory' => "$mt", 'UsedMemory' => "$mu",
        'FreeMemory' => "$mf", 'CachedMemory' => "$mc", 'Buffers' => "$mb", 'TotalSwap' =>
        "$st", 'swapUsed' => "$su", 'swapFree' => "$sf", 'loadAvg' => "$load", 'uptime' =>
        "$uptime", 'freetime' => "$freetime", 'bjtime' => "$bjtime", 'stime' => "$stime",
        'memRealPercent' => "$memRealPercent", 'memRealUsed' => "$memRealUsed",
        'memRealFree' => "$memRealFree", 'memPercent' => "$memPercent%",
        'memCachedPercent' => "$memCachedPercent", 'barmemCachedPercent' => "$memCachedPercent%",
        'swapPercent' => "$swapPercent", 'barmemRealPercent' => "$memRealPercent%",
        'barswapPercent' => "$swapPercent%", 'NetOut2' => "$NetOut[2]", 'NetOut3' => "$NetOut[3]",
        'NetOut4' => "$NetOut[4]", 'NetOut5' => "$NetOut[5]", 'NetOut6' => "$NetOut[6]",
        'NetOut7' => "$NetOut[7]", 'NetOut8' => "$NetOut[8]", 'NetOut9' => "$NetOut[9]",
        'NetOut10' => "$NetOut[10]", 'NetInput2' => "$NetInput[2]", 'NetInput3' => "$NetInput[3]",
        'NetInput4' => "$NetInput[4]", 'NetInput5' => "$NetInput[5]", 'NetInput6' => "$NetInput[6]",
        'NetInput7' => "$NetInput[7]", 'NetInput8' => "$NetInput[8]", 'NetInput9' => "$NetInput[9]",
        'NetInput10' => "$NetInput[10]");
    $jarr = json_encode($arr);
    echo $_GET['callback'], '(', $jarr, ')';
    exit;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo HTTP_HOST;?></title>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- Powered by: Yahei.Net -->
<style type="text/css">
<!--
* {font-family: Tahoma, "Microsoft Yahei", Arial; }
body{text-align: center; margin: 0 auto; padding: 0; background-color:#FFFFFF;font-size:14px;font-family:Tahoma, Arial}
h1 {font-size: 25px; font-weight: normal; padding: 0; margin: 0;}
h1 a {color: #00BCFF; }
h1 small {font-size: 13px; font-family: Tahoma; font-weight: bold; color: #999;}
a{color: #00BCFF; text-decoration:none;}
a.black{color: #000; text-decoration:none;}
b{color: #00CF91;}
em{font-weight:bold;font-style:normal;color:#F30;}
table{clear:both;padding: 0; margin: 0 0 10px;border-collapse:collapse; border-spacing: 0;}
th{padding: 3px 6px; font-weight:bold;background:#D7E9FB;color:#000;}
tr{padding: 0; background:#FFF;}
td{padding: 3px 6px; border:1px solid #D7E9FB;}
input{padding: 2px; background: #FFF; border-top:1px solid #666; border-left:1px solid #666; border-right:1px solid #CCC; border-bottom:1px solid #CCC; font-size:12px}
input.btn{font-weight: bold; height: 20px; line-height: 20px; padding: 0 6px; color:#FFF; background: #10AF7B; border-top:1px solid #65DAB4; border-left:1px solid #65DAB4; border-right:1px solid #1A664E; border-bottom:1px solid #1A664E; font-size:12px}.bar {border:1px solid #000; height:5px; font-size:2px; width:60%; margin:2px 0 5px 0;}
.bar {border:1px solid #000; height:5px; font-size:2px; width:60%; margin:2px 0 5px 0;}
.barli{background:#0EEF96; height:5px; margin:0; padding:0;}
#page {width: 920px; padding: 0 20px; margin: 0 auto; text-align: left;}
#header{position: relative; padding: 10px;}
#footer {padding: 15px 0; text-align: center; font-size: 12px; font-family: Arial, Tahoma, Verdana;}
#download {position: absolute; top: 20px; right: 10px; text-align: right; font-weight: bold; color: #06C;}
#download a {color: #00BCFF; text-decoration: underline;}

.w_small{font-family: Tahoma, "Microsoft Yahei", Arial; }
.w_number{color: #f800fe;}
.sudu {padding: 0; background:#5dafd1; }
.suduk { margin:0px; padding:0;}
.resYes{}
.resNo{color: #FF0000;}
.word{word-break:break-all;}
-->
</style>
<script language="JavaScript" type="text/javascript" src="http://lib.sinaapp.com/js/jquery/2.0.2/jquery-2.0.2.min.js"></script>
<script type="text/javascript">
<!--
$(document).ready(function(){getJSONData();});
function getJSONData()
{
	setTimeout("getJSONData()", 1000);
	$.getJSON('?act=rt&callback=?', displayData);
}
function displayData(dataJSON)
{
	$("#freeSpace").html(dataJSON.freeSpace);
	$("#TotalMemory").html(dataJSON.TotalMemory);
	$("#UsedMemory").html(dataJSON.UsedMemory);
	$("#FreeMemory").html(dataJSON.FreeMemory);
	$("#CachedMemory").html(dataJSON.CachedMemory);
	$("#Buffers").html(dataJSON.Buffers);
	$("#TotalSwap").html(dataJSON.TotalSwap);
	$("#swapUsed").html(dataJSON.swapUsed);
	$("#swapFree").html(dataJSON.swapFree);
	$("#swapPercent").html(dataJSON.swapPercent);
	$("#loadAvg").html(dataJSON.loadAvg);
	$("#uptime").html(dataJSON.uptime);
	$("#freetime").html(dataJSON.freetime);
	$("#stime").html(dataJSON.stime);
	$("#bjtime").html(dataJSON.bjtime);
	$("#memRealUsed").html(dataJSON.memRealUsed);
	$("#memRealFree").html(dataJSON.memRealFree);
	$("#memRealPercent").html(dataJSON.memRealPercent);
	$("#memPercent").html(dataJSON.memPercent);
	$("#barmemPercent").width(dataJSON.memPercent);
	$("#barmemRealPercent").width(dataJSON.barmemRealPercent);
	$("#memCachedPercent").html(dataJSON.memCachedPercent);
	$("#barmemCachedPercent").width(dataJSON.barmemCachedPercent);
	$("#barswapPercent").width(dataJSON.barswapPercent);
	$("#NetOut2").html(dataJSON.NetOut2);
	$("#NetOut3").html(dataJSON.NetOut3);
	$("#NetOut4").html(dataJSON.NetOut4);
	$("#NetOut5").html(dataJSON.NetOut5);
	$("#NetOut6").html(dataJSON.NetOut6);
	$("#NetOut7").html(dataJSON.NetOut7);
	$("#NetOut8").html(dataJSON.NetOut8);
	$("#NetOut9").html(dataJSON.NetOut9);
	$("#NetOut10").html(dataJSON.NetOut10);
	$("#NetInput2").html(dataJSON.NetInput2);
	$("#NetInput3").html(dataJSON.NetInput3);
	$("#NetInput4").html(dataJSON.NetInput4);
	$("#NetInput5").html(dataJSON.NetInput5);
	$("#NetInput6").html(dataJSON.NetInput6);
	$("#NetInput7").html(dataJSON.NetInput7);
	$("#NetInput8").html(dataJSON.NetInput8);
	$("#NetInput9").html(dataJSON.NetInput9);
	$("#NetInput10").html(dataJSON.NetInput10);
}
-->
</script>
</head>
<body>

<div id="page">
    <div id="header">
        <h1><a href="http://<?php echo HTTP_HOST;?>"><?php echo HTTP_HOST;?></a></h1>
        <div id="download"><a href="http://www.9gan.com/9ganphp.zip">点此下载最新版 <?php echo $version; ?> (支持Linux,BSD,Windows)</a></div>
    </div>

<!--服务器相关参数-->
<table width="100%" cellpadding="3" cellspacing="0">
  <tr><th colspan="4">服务器参数</th></tr>
  <tr>
    <td>服务器域名/IP地址</td>
    <td colspan="3"><?php echo @get_current_user();?> - <?php echo $_SERVER['SERVER_NAME'];?>(<?php if('/'==DIRECTORY_SEPARATOR){echo $_SERVER['SERVER_ADDR'];}else{echo @gethostbyname($_SERVER['SERVER_NAME']);} ?>)&nbsp;&nbsp;你的IP地址是：<?php echo @$_SERVER['REMOTE_ADDR'];?></td>
  </tr>
  <tr>
    <td>服务器标识</td>
    <td colspan="3"><?php if($sysInfo['win_n'] != ''){echo $sysInfo['win_n'];}else{echo @php_uname();};?></td>
  </tr>
  <tr>
    <td width="13%">服务器操作系统</td>
    <td width="37%"><?php $os = explode(" ", php_uname()); echo $os[0];?> &nbsp;内核版本：<?php if('/'==DIRECTORY_SEPARATOR){echo $os[2];}else{echo $os[1];} ?></td>
    <td width="13%">服务器解译引擎</td>
    <td width="37%"><?php echo $_SERVER['SERVER_SOFTWARE'];?></td>
  </tr>
  <tr>
    <td>服务器语言</td>
    <td><?php echo getenv("HTTP_ACCEPT_LANGUAGE");?></td>
    <td>绝对路径</td>
    <td><?php echo $_SERVER['DOCUMENT_ROOT']?str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']):str_replace('\\','/',dirname(__FILE__));?></td>
  </tr>
  <tr>
    <td>服务器端口</td>
    <td><?php echo $_SERVER['SERVER_PORT'];?></td>
    <td>系统平均负载</td>
    <td><em><span id="loadAvg"><?php echo $load;?></span></em></td>
  </tr>
</table>

<?if("show"==$sysReShow){?>
<table width="100%" cellpadding="3" cellspacing="0" align="center">
  <tr><th colspan="6">服务器实时数据</th></tr>
  <tr>
    <td width="13%" >服务器当前时间</td>
    <td width="37%" ><span id="stime"><?php echo $stime;?></span></td>
    <td width="13%" >服务器已运行时间</td>
    <td width="37%" colspan="3"><span id="uptime"><?php echo $uptime;?></span></td>
  </tr>
  <tr>
    <td>硬盘使用</td>
    <td colspan="5">
    硬盘容量共 <b><?php echo $hdd_total;?> G</b>， 空闲 <b><span id="freeSpace"><?php echo $hdd_free;?></span> G</b>，使用率 <b><?=$hdd_percent?>%</b>
    <?php echo bar($hdd_percent);?>
    </td>
  </tr>
  <tr>
    <td width="13%">CPU型号 [<?php echo $sysInfo['cpu']['num'];?>核]</td>
    <td width="87%" colspan="5"><?php echo $sysInfo['cpu']['model'];?></td>
  </tr>
	  <tr>
		<td>内存使用状况</td>
		<td colspan="5">
<?php
$tmp = array(
    'memTotal', 'memUsed', 'memFree', 'memPercent',
    'memCached', 'memRealPercent',
    'swapTotal', 'swapUsed', 'swapFree', 'swapPercent'
);
foreach ($tmp AS $v) {
    $sysInfo[$v] = $sysInfo[$v] ? $sysInfo[$v] : 0;
}
?>
          物理内存：共
          <b><?php echo $memTotal;?> </b>
           , 已用
          <b><span id="UsedMemory"><?php echo $mu;?></span></b>
          , 空闲
          <b><span id="FreeMemory"><?php echo $mf;?></span></b>
          , 使用率
		  <span id="memPercent"><?php echo $memPercent;?></span>
          <div class="bar"><div id="barmemPercent" class="barli" >&nbsp;</div></div>
<?php
//判断如果cache为0，不显示
if($sysInfo['memCached']>0)
{
?>
		  Cache化内存为 <span id="CachedMemory"><?php echo $mc;?></span>
		  , 使用率
          <span id="memCachedPercent"><?php echo $memCachedPercent;?></span>
		  %	| Buffers缓冲为  <span id="Buffers"><?php echo $mb;?></span>
          <div class="bar"><div id="barmemCachedPercent" class="barli" >&nbsp;</div></div>

          真实内存使用
          <span id="memRealUsed"><?php echo $memRealUsed;?></span>
		  , 真实内存空闲
          <span id="memRealFree"><?php echo $memRealFree;?></span>
		  , 使用率
          <span id="memRealPercent"><?php echo $memRealPercent;?></span>
          %
          <div class="bar"><div id="barmemRealPercent" class="barli" >&nbsp;</div></div>
<?php
}
//判断如果SWAP区为0，不显示
if($sysInfo['swapTotal']>0)
{
?>
          SWAP区：共
          <?php echo $st;?>
          , 已使用
          <span id="swapUsed"><?php echo $su;?></span>
          , 空闲
          <span id="swapFree"><?php echo $sf;?></span>
          , 使用率
          <span id="swapPercent"><?php echo $swapPercent;?></span>
          %
          <div class="bar"><div id="barswapPercent" class="barli" >&nbsp;</div></div>

<?php
}
?>
	  </td>
	</tr>
</table>
<?}?>

<?php if (false !== ($strs = @file("/proc/net/dev"))) : ?>
<table width="100%" cellpadding="3" cellspacing="0" align="center">
    <tr><th colspan="3">网络使用状况</th></tr>
<?php for ($i = 2; $i < count($strs); $i++ ) : ?>
<?php preg_match_all( "/([^\s]+):[\s]{0,}(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/", $strs[$i], $info );?>
     <tr>
        <td width="13%"><?php echo $info[1][0]?> : </td>
        <td width="43%">已接收 : <b><span id="NetInput<?php echo $i?>"><?php echo $NetInput[$i]?></span></b></td>
        <td width="43%">已发送 : <em><span id="NetOut<?php echo $i?>"><?php echo $NetOut[$i]?></span></em></td>
    </tr>
<?php endfor; ?>
</table>
<?php endif; ?>


<!--网络速度测试-->
<form action="<?php echo $_SERVER[PHP_SELF];?>" method="post">
<table width="100%" cellpadding="3" cellspacing="0" align="center">
	<tr><th colspan="3" class="th_3">网络速度测试</th></tr>
  <tr>
    <td width="19%" align="center"><input name="act" type="submit" class="btn" value="开始测试" />
        <br />
	向客户端传送1000k字节数据<br />
	带宽比例按理想值计算
	</td>
    <td width="81%" align="center" >

  <table align="center" width="550" border="0" cellspacing="0" cellpadding="0" >
    <tr >
    <td height="15" width="50">带宽</td>
	<td height="15" width="50">1M</td>
    <td height="15" width="50">2M</td>
    <td height="15" width="50">3M</td>
    <td height="15" width="50">4M</td>
    <td height="15" width="50">5M</td>
    <td height="15" width="50">6M</td>
    <td height="15" width="50">7M</td>
    <td height="15" width="50">8M</td>
    <td height="15" width="50">9M</td>
    <td height="15" width="50">10M</td>
    </tr>
   <tr>
    <td colspan="11" class="suduk" ><table align="center" width="550" border="0" cellspacing="0" cellpadding="0" height="8" class="suduk">
    <tr>
      <td class="sudu"  width="<?php
	if(preg_match("/[^\d-., ]/",$speed))
		{
			echo "0";
		}
	else{
			echo 550*($speed/11000);
		}
		?>"></td>
      <td class="suduk" width="<?php
	if(preg_match("/[^\d-., ]/",$speed))
		{
			echo "550";
		}
	else{
			echo 550-550*($speed/11000);
		}
		?>"></td>
    </tr>
    </table>
   </td>
  </tr>
  </table>
  <?php echo (isset($_GET['speed']))?"下载 1000 KB 数据用时 <b>".$_GET['speed']."</b> 毫秒，下载速度：<b>".$speed." KB/s</b>，需测试多次取平均值，超过10M直接看下载速度": "<em> 未探测 </em>" ?>
    </td>
  </tr>
</table>
</form>


<!--PHP相关-->
<table width="100%" cellpadding="3" cellspacing="0" align="center">
  <tr>
    <th colspan="4" class="th_1">PHP已编译模块检测</th>
  </tr>
  <tr>
    <td colspan="4"><span class="w_small">
<?php
$able=get_loaded_extensions();
foreach ($able as $key=>$value) {
	if ($key!=0 && $key%13==0) {
		echo '<br />';
	}
	echo "$value&nbsp;&nbsp;";
}
?></span>
    </td>
  </tr>
</table>
<table width="100%" cellpadding="3" cellspacing="0" align="center">
  <tr><th colspan="4" class="th_1">PHP相关参数</th></tr>
  <tr>
    <td width="32%">PHP信息（phpinfo）：</td>
    <td width="18%">
		<?php
		$phpSelf = $_SERVER[PHP_SELF] ? $_SERVER[PHP_SELF] : $_SERVER[SCRIPT_NAME];
		$disFuns=get_cfg_var("disable_functions");
		?>
    <?php echo (false!==eregi("phpinfo",$disFuns))? '<em>×</em>' :"<a href='$phpSelf?act=phpinfo' target='_blank'>PHPINFO</a>";?>
    </td>
    <td width="32%">PHP版本（php_version）：</td>
    <td width="18%"><b><?php echo PHP_VERSION;?></b></td>
  </tr>
  <tr>
    <td>PHP运行方式：</td>
    <td><?php echo strtoupper(php_sapi_name());?></td>
    <td>脚本占用最大内存（memory_limit）：</td>
    <td><?php echo show("memory_limit");?></td>
  </tr>
  <tr>
    <td>PHP安全模式（safe_mode）：</td>
    <td><?php echo show("safe_mode");?></td>
    <td>POST方法提交最大限制（post_max_size）：</td>
    <td><?php echo show("post_max_size");?></td>
  </tr>
  <tr>
    <td>上传文件最大限制（upload_max_filesize）：</td>
    <td><?php echo show("upload_max_filesize");?></td>
    <td>浮点型数据显示的有效位数（precision）：</td>
    <td><?php echo show("precision");?></td>
  </tr>
  <tr>
    <td>脚本超时时间（max_execution_time）：</td>
    <td><?php echo show("max_execution_time");?>秒</td>
    <td>socket超时时间（default_socket_timeout）：</td>
    <td><?php echo show("default_socket_timeout");?>秒</td>
  </tr>
  <tr>
    <td>PHP页面根目录（doc_root）：</td>
    <td><?php echo show("doc_root");?></td>
    <td>用户根目录（user_dir）：</td>
    <td><?php echo show("user_dir");?></td>
  </tr>
  <tr>
    <td>dl()函数（enable_dl）：</td>
    <td><?php echo show("enable_dl");?></td>
    <td>指定包含文件目录（include_path）：</td>
    <td><?php echo show("include_path");?></td>
  </tr>
  <tr>
    <td>显示错误信息（display_errors）：</td>
    <td><?php echo show("display_errors");?></td>
    <td>自定义全局变量（register_globals）：</td>
    <td><?php echo show("register_globals");?></td>
  </tr>
  <tr>
    <td>数据反斜杠转义（magic_quotes_gpc）：</td>
    <td><?php echo show("magic_quotes_gpc");?></td>
    <td>"&lt;?...?&gt;"短标签（short_open_tag）：</td>
    <td><?php echo show("short_open_tag");?></td>
  </tr>
  <tr>
    <td>"&lt;% %&gt;"ASP风格标记（asp_tags）：</td>
    <td><?php echo show("asp_tags");?></td>
    <td>忽略重复错误信息（ignore_repeated_errors）：</td>
    <td><?php echo show("ignore_repeated_errors");?></td>
  </tr>
  <tr>
    <td>忽略重复的错误源（ignore_repeated_source）：</td>
    <td><?php echo show("ignore_repeated_source");?></td>
    <td>报告内存泄漏（report_memleaks）：</td>
    <td><?php echo show("report_memleaks");?></td>
  </tr>
  <tr>
    <td>自动字符串转义（magic_quotes_gpc）：</td>
    <td><?php echo show("magic_quotes_gpc");?></td>
    <td>外部字符串自动转义（magic_quotes_runtime）：</td>
    <td><?php echo show("magic_quotes_runtime");?></td>
  </tr>
  <tr>
    <td>打开远程文件（allow_url_fopen）：</td>
    <td><?php echo show("allow_url_fopen");?></td>
    <td>声明argv和argc变量（register_argc_argv）：</td>
    <td><?php echo show("register_argc_argv");?></td>
  </tr>
  <tr>
    <td>Cookie 支持：</td>
    <td><?php echo isset($_COOKIE)?'<b>√</b>' : '<em>×</em>';?></td>
    <td>拼写检查（ASpell Library）：</td>
    <td><?php echo isfun("aspell_check_raw");?></td>
  </tr>
   <tr>
    <td>高精度数学运算（BCMath）：</td>
    <td><?php echo isfun("bcadd");?></td>
    <td>PREL相容语法（PCRE）：</td>
    <td><?php echo isfun("preg_match");?></td>
   <tr>
    <td>PDF文档支持：</td>
    <td><?php echo isfun("pdf_close");?></td>
    <td>SNMP网络管理协议：</td>
    <td><?php echo isfun("snmpget");?></td>
  </tr>
   <tr>
    <td>VMailMgr邮件处理：</td>
    <td><?php echo isfun("vm_adduser");?></td>
    <td>Curl支持：</td>
    <td><?php echo isfun("curl_init");?></td>
  </tr>
   <tr>
    <td>SMTP支持：</td>
    <td><?php echo get_cfg_var("SMTP")?'<b>√</b>' : '<em>×</em>';?></td>
    <td>SMTP地址：</td>
    <td><?php echo get_cfg_var("SMTP")?get_cfg_var("SMTP"):'<em>×</em>';?></td>
  </tr>
	<tr>
		<td>默认支持函数（enable_functions）：</td>
		<td colspan="3"><a href='<?php echo $phpSelf;?>?act=Function' target='_blank' class='static'>请点这里查看详细！</a></td>
	</tr>
	<tr>
		<td>被禁用的函数（disable_functions）：</td>
		<td colspan="3" class="word">
<?php
$disFuns=get_cfg_var("disable_functions");
if(empty($disFuns))
{
	echo '<em>×</em>';
}
else
{
	//echo $disFuns;
	$disFuns_array =  explode(',',$disFuns);
	foreach ($disFuns_array as $key=>$value)
	{
		if ($key!=0 && $key%5==0) {
			echo '<br />';
	}
	echo "$value&nbsp;&nbsp;";
}
}

?>
		</td>
	</tr>
</table>
<!--组件信息-->
<table width="100%" cellpadding="3" cellspacing="0" align="center">
  <tr><th colspan="4" class="th_1">组件支持</th></tr>
  <tr>
    <td width="32%">FTP支持：</td>
    <td width="18%"><?php echo isfun("ftp_login");?></td>
    <td width="32%">XML解析支持：</td>
    <td width="18%"><?php echo isfun("xml_set_object");?></td>
  </tr>
  <tr>
    <td>Session支持：</td>
    <td><?php echo isfun("session_start");?></td>
    <td>Socket支持：</td>
    <td><?php echo isfun("socket_accept");?></td>
  </tr>
  <tr>
    <td>Calendar支持</td>
    <td><?php echo isfun('cal_days_in_month');?>
	</td>
    <td>允许URL打开文件：</td>
    <td><?php echo show("allow_url_fopen");?></td>
  </tr>
  <tr>
    <td>GD库支持：</td>
    <td>
    <?php
        if(function_exists(gd_info)) {
            $gd_info = @gd_info();
	        echo $gd_info["GD Version"];
	    }else{echo '<em>×</em>';}
	?></td>
    <td>压缩文件支持(Zlib)：</td>
    <td><?php echo isfun("gzclose");?></td>
  </tr>
  <tr>
    <td>IMAP电子邮件系统函数库：</td>
    <td><?php echo isfun("imap_close");?></td>
    <td>历法运算函数库：</td>
    <td><?php echo isfun("JDToGregorian");?></td>
  </tr>
  <tr>
    <td>正则表达式函数库：</td>
    <td><?php echo isfun("preg_match");?></td>
    <td>WDDX支持：</td>
    <td><?php echo isfun("wddx_add_vars");?></td>
  </tr>
  <tr>
    <td>Iconv编码转换：</td>
    <td><?php echo isfun("iconv");?></td>
    <td>mbstring：</td>
    <td><?php echo isfun("mb_eregi");?></td>
  </tr>
  <tr>
    <td>高精度数学运算：</td>
    <td><?php echo isfun("bcadd");?></td>
    <td>LDAP目录协议：</td>
    <td><?php echo isfun("ldap_close");?></td>
  </tr>
  <tr>
    <td>MCrypt加密处理：</td>
    <td><?php echo isfun("mcrypt_cbc");?></td>
    <td>哈稀计算：</td>
    <td><?php echo isfun("mhash_count");?></td>
  </tr>
</table>

<!--第三方组件信息-->
<table width="100%" cellpadding="3" cellspacing="0" align="center">
  <tr><th colspan="4" class="th_1">第三方组件</th></tr>
  <tr>
    <td width="32%">Zend版本</td>
    <td width="18%"><?php $zend_version = zend_version();if(empty($zend_version)){echo '<em>×</em>';}else{echo $zend_version;}?></td>
    <td width="32%">
<?php
$PHP_VERSION = PHP_VERSION;
$PHP_VERSION = substr($PHP_VERSION,2,1);
if($PHP_VERSION > 2)
{
	echo "ZendGuardLoader[启用]";
}
else
{
	echo "Zend Optimizer";
}
?>
	</td>
    <td width="18%"><?php if($PHP_VERSION > 2){echo (get_cfg_var("zend_loader.enable"))?'<b>√</b>':'<em>×</em>';} else{if(function_exists('zend_optimizer_version')){	echo zend_optimizer_version();}else{	echo (get_cfg_var("zend_optimizer.optimization_level")||get_cfg_var("zend_extension_manager.optimizer_ts")||get_cfg_var("zend.ze1_compatibility_mode")||get_cfg_var("zend_extension_ts"))?'<b>√</b>':'<em>×</em>';}}?></td>
  </tr>
  <tr>
    <td>Memcached</td>
    <td><?php if((phpversion('Memcached'))!=''){echo phpversion('Memcached');}else{ echo "<em>×</em>";} ?></td>
    <td>ioncube</td>
    <td><?php if(extension_loaded('ionCube Loader')){   $ys = ioncube_loader_iversion();   $gm = ".".(int)substr($ys,3,2);   echo ionCube_Loader_version().$gm;}else{echo "<em>×</em>";}?></td>
  </tr>
  <tr>
    <td>Memcache[new Memcached()]</td>
    <td><?php if((phpversion('Memcache'))!=''){echo phpversion('Memcache');}else{ echo "<em>×</em>";} ?></td>
    <td>XCache</td>
    <td><?php if((phpversion('XCache'))!=''){echo phpversion('XCache');}else{ echo "<em>×</em>";} ?></td>
  </tr>
  <tr>
    <td>Zend OPcache</td>
    <td><?php if((phpversion('Zend OPcache'))!=''){echo phpversion('Zend OPcache');}else{ echo "<em>×</em>";} ?></td>
    <td>APC</td>
    <td><?php if((phpversion('APC'))!=''){echo phpversion('APC');}else{ echo "<em>×</em>";} ?></td>
  </tr>
</table>

<!--数据库支持-->
<table width="100%" cellpadding="3" cellspacing="0" align="center">
  <tr><th colspan="4" class="th_2">数据库支持</th></tr>
  <tr>
    <td width="32%">MySQL 数据库：</td>
    <td width="18%"><?php echo isfun("mysql_close");?>
    <?php
    if(function_exists("mysql_get_server_info")) {
        $s = @mysql_get_server_info();
        $s = $s ? '&nbsp; mysql_server 版本：'.$s : '';
	    $c = '&nbsp; mysql_client 版本：'.@mysql_get_client_info();
        echo $s ? $s : $c;
    }
    ?>
	</td>
    <td width="32%">ODBC 数据库：</td>
    <td width="18%"><?php echo isfun("odbc_close");?></td>
  </tr>
  <tr>
    <td>Oracle 数据库：</td>
    <td><?php echo isfun("ora_close");?></td>
    <td>SQL Server 数据库：</td>
    <td><?php echo isfun("mssql_close");?></td>
  </tr>
  <tr>
    <td>dBASE 数据库：</td>
    <td><?php echo isfun("dbase_close");?></td>
    <td>mSQL 数据库：</td>
    <td><?php echo isfun("msql_close");?></td>
  </tr>
  <tr>
    <td>SQLite 数据库：</td>
    <td><?php if(extension_loaded('sqlite3')) {$sqliteVer = SQLite3::version();echo '<font color=green><b>√</b></font>　';echo "SQLite3　Ver ";echo $sqliteVer[versionString];}else {echo isfun("sqlite_close");if(isfun("sqlite_close") == '<font color="green">√</font>') {echo "&nbsp; 版本： ".@sqlite_libversion();}}?></td>
    <td>Hyperwave 数据库：</td>
    <td><?php echo isfun("hw_close");?></td>
  </tr>
  <tr>
    <td>Postgre SQL 数据库：</td>
    <td><?php echo isfun("pg_close"); ?></td>
    <td>Informix 数据库：</td>
    <td><?php echo isfun("ifx_close");?></td>
  </tr>
  <tr>
    <td>DBA 数据库：</td>
    <td><?php echo isfun("dba_close");?></td>
    <td>DBM 数据库：</td>
    <td><?php echo isfun("dbmclose");?></td>
  </tr>
  <tr>
    <td>FilePro 数据库：</td>
    <td><?php echo isfun("filepro_fieldcount");?></td>
    <td>SyBase 数据库：</td>
    <td><?php echo isfun("sybase_close");?></td>
  </tr>
</table>

<form action="<?php echo $_SERVER[PHP_SELF]."#bottom";?>" method="post">
<!--服务器性能检测-->
<table width="100%" cellpadding="3" cellspacing="0" align="center">
  <tr><th colspan="5" class="th_6">服务器性能检测</th></tr>
  <tr align="center">
    <td width="19%">参照对象</td>
    <td width="17%">整数运算能力检测<br />(1+1运算300万次)</td>
    <td width="17%">浮点运算能力检测<br />(圆周率开平方300万次)</td>
    <td width="17%">数据I/O能力检测<br />(读取10K文件1万次)</td>
    <td width="30%">CPU信息</td>
  </tr>
  <tr align="center">
    <td align="left">美国 LinodeVPS</td>
    <td>0.357秒</td>
    <td>0.802秒</td>
    <td>0.023秒</td>
    <td align="left">4 x Xeon L5520 @ 2.27GHz</td>
  </tr>
  <tr align="center">
    <td align="left">美国 PhotonVPS.com</td>
    <td>0.431秒</td>
    <td>1.024秒</td>
    <td>0.034秒</td>
    <td align="left">8 x Xeon E5520 @ 2.27GHz</td>
  </tr>
  <tr align="center">
    <td align="left">美国 DirectSpace.net</td>
    <td>0.208秒</td>
    <td>0.442秒</td>
    <td>0.014秒</td>
    <td align="left">4 x Core i5 2500 @ 3.30GHz</td>
  </tr>
  <tr align="center">
    <td align="left">美国 RiZie.com</td>
    <td>0.521秒</td>
    <td>1.559秒</td>
    <td>0.054秒</td>
    <td align="left">2 x Pentium4 3.00GHz</td>
  </tr>
  <tr align="center">
    <td align="left">美国 BuyVM.com</td>
    <td>0.576秒</td>
    <td>1.565秒</td>
    <td>0.053秒</td>
    <td align="left">2 x Xeon L5520 @ 2.27GHz</td>
  </tr>
  <tr align="center">
    <td align="left">埃及 CitynetHost.com</a></td>
    <td>0.343秒</td>
    <td>0.761秒</td>
    <td>0.023秒</td>
    <td align="left">2 x Core2Duo E4600 @ 2.40GHz</td>
  </tr>
  <tr align="center">
    <td align="left">美国 IXwebhosting.com</td>
    <td>0.535秒</td>
    <td>1.607秒</td>
    <td>0.058秒</td>
    <td align="left">4 x Xeon E5530 @ 2.40GHz</td>
  </tr>
  <tr align="center">
    <td>本台服务器</td>
    <td><b><?php echo $valInt;?></b><br /><input class="btn" name="act" type="submit" value="整型测试" /></td>
    <td><b><?php echo $valFloat;?></b><br /><input class="btn" name="act" type="submit" value="浮点测试" /></td>
    <td><b><?php echo $valIo;?></b><br /><input class="btn" name="act" type="submit" value="IO测试" /></td>
    <td></td>
  </tr>
</table>
<input type="hidden" name="pInt" value="<?php echo $valInt;?>" />
<input type="hidden" name="pFloat" value="<?php echo $valFloat;?>" />
<input type="hidden" name="pIo" value="<?php echo $valIo;?>" />

<!--MySQL数据库连接检测-->
<table width="100%" cellpadding="3" cellspacing="0" align="center">
	<tr><th colspan="3" class="th_2">MySQL数据库连接检测</th></tr>
  <tr>
    <td width="5%"></td>
    <td width="80%">
      地址：<input type="text" name="host" value="localhost" size="10" />
      端口：<input type="text" name="port" value="3306" size="10" />
      用户名：<input type="text" name="login" size="10" />
      密码：<input type="password" name="password" size="10" />
    </td>
    <td width="15%">
      <input class="btn" type="submit" name="act" value="MySQL检测" />
    </td>
  </tr>
</table>
  <?php
  if ($_POST['act'] == 'MySQL检测') {
  	if(function_exists("mysql_close")==1) {
  		$link = @mysql_connect($host.":".$port,$login,$password);
  		if ($link){
  			echo "<script>alert('连接到MySql数据库正常')</script>";
  		} else {
  			echo "<script>alert('无法连接到MySql数据库！')</script>";
  		}
  	} else {
  		echo "<script>alert('服务器不支持MySQL数据库！')</script>";
  	}
  }
	?>
<!--PostgreSQL数据库连接检测-->
<table width="100%" cellpadding="3" cellspacing="0" align="center">
	<tr><th colspan="3" class="th_2">PostgreSQL数据库连接检测</th></tr>
  <tr>
    <td width="5%"></td>
    <td width="80%">
      地址：<input type="text" name="host" value="localhost" size="10" />
      端口：<input type="text" name="port" value="5432" size="10" />
      用户名：<input type="text" name="login" size="10" />
      密码：<input type="password" name="password" size="10" />
      数据库名：<input type="text" name="dbname" size="10" />
    </td>
    <td width="15%">
      <input class="btn" type="submit" name="act" value="PostgreSQL检测" />
    </td>
  </tr>
</table>
  <?php
  if ($_POST['act'] == 'PostgreSQL检测') {
  	if(function_exists("pg_connect")==1) {
  		$link = @pg_connect($host.":".$port,$dbname,$login,$password);
  		if ($link){
  			echo "<script>alert('连接到PostgreSql数据库正常')</script>";
  		} else {
  			echo "<script>alert('无法连接到PostgreSql数据库！')</script>";
  		}
  	} else {
  		echo "<script>alert('服务器不支持PostgreSQL数据库！')</script>";
  	}
  }
	?>
<!--函数检测-->
<table width="100%" cellpadding="3" cellspacing="0" align="center">
	<tr><th colspan="3" class="th_4">函数检测</th></tr>
  <tr>
    <td width="5%"></td>
    <td width="80%">
      请输入您要检测的函数：
      <input type="text" name="funName" size="50" />
    </td>
    <td width="15%">
      <input class="btn" type="submit" name="act" align="right" value="函数检测" />
    </td>
  </tr>
  <?php
  if ($_POST['act'] == '函数检测') {
  	echo "<script>alert('$funRe')</script>";
  }
  ?>
</table>
<!--邮件发送检测-->
<table width="100%" cellpadding="3" cellspacing="0" align="center">
  <tr><th colspan="3" class="th_5">邮件发送检测</th></tr>
  <tr>
    <td width="5%"></td>
    <td width="80%">
      请输入您要检测的邮件地址：
      <input type="text" name="mailAdd" size="50" />
    </td>
    <td width="15%">
    <input class="btn" type="submit" name="act" value="邮件检测" />
    </td>
  </tr>
  <?php
  if ($_POST['act'] == '邮件检测') {
  	echo "<script>alert('$mailRe')</script>";
  }
  ?>
</table>
</form>
<a id="bottom"></a>

<div id="footer">
&copy; <?php echo date("Y",time());?> <a href="http://www.9gan.com/">9gan.com</a> PHP探针 <?php echo $version; ?> All Rights Reserved.<br />
<?php $run_time = sprintf('%0.4f', microtime_float() - $time_start);?>
Processed in <?=$run_time?> seconds. <?=memory_usage();?> memory usage.
</div>

</div>
</body>
</html>