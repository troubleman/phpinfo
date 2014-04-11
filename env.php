<?
header("content-Type: text/html; charset=utf-8");
$version = "1.3.6a";
/* 
+----------------------------------------------------------------------
|   **如果你看到了这里，说明你的服务器不支持PHP**
+----------------------------------------------------------------------
|   文件名： 废墟のPHP探针
+---------------------------------------------------------------------- 
|   Copyright  2003-2005 WapCity 版权所有并保留所有版权                  
+---------------------------------------------------------------------- 
|   本探针制作时参考了其他一些探针        
|   此向其作者表示感谢                      
+----------------------------------------------------------------------
|   作者： 废墟の复甦 < anerg@183.ha.cn > [ QQ:1616676 ]
+----------------------------------------------------------------------
*/
extract($_GET);extract($_POST);
error_reporting();
$ads = '';//此处放置顶部广告


function getmicrotime()
{ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec); 
} 
$page_time_start=getmicrotime();
//脚本运行时间
function addTime($s=0)
{
	$time_start=getmicrotime();
	for($index=0;$index<=500000;$index++);
	{
		$count=1+1;
	}
	$time_end=getmicrotime();
	$time=$time_end-$time_start;
	$time=round($time*1000);
	$time=$s==0?"<font color=red>$time</font>":$time;
	return($time);	
}//END FUNCTION
function sqrtTime($s=0)
{
	$test=pi();
	$time_start=getmicrotime();
	for($index=0;$index<=500000;$index++);
	{
		sqrt($test);
	}
	$time_end=getmicrotime();
	$time=$time_end-$time_start;
	$time=round($time*1000);
	$time=$s==0?"<font color=red>$time</font>":$time;
	return($time);
}//END FUNCTION

if ("phpinfo" == $testinfo)
{
	phpinfo();
	exit;
}//END IF

function echo_info($str)
{
	echo "<script>alert('$str')</script>";
}

function temp($temp)
{
	if($temp==1)
	{
	$s='<font color=green>支持<b>√</b></font>';
	}
	else
	{
	$s='<font color=red>不支持<b>×</b></font>';
	}
	return $s;
}
//回传数据
$mailcontent	=	$_SERVER[SERVER_NAME].$_SERVER[PHP_SELF];
//服务器时间比较
$mtime[] = array("参照机器","整数运算,50万次“1+1”","浮点运算，50万次平方根");
$mtime[] = array("公司电脑 (P4/1.7G 256M Win2k)","404 毫秒","398 毫秒");
$mtime[] = array("我的电脑 (AMD1800+ 512M WinXP sp2)","285 毫秒","280 毫秒");
$mtime[] = array("<a href='http://www.v8v.net' target='_blank'><font color='#FFFF99'>中国同网</font></a> 全能ASP+PHP (2005-5-30)","138 毫秒","137 毫秒");
$mtime[] = array("恩博在线 商务型(L)-200M (2005-5-30)","383 毫秒","394 毫秒");
$mtime[] = array("Orgfree.com (2005-5-31)","320 毫秒","313 毫秒");
$mtime[] = array("<a href='mailto:r.anerg@gmail.com?subject=服务器参数&body=http://".$mailcontent."' title='请尽量描述清楚,写明是商业空间还是免费空间,谢谢'><font color='#FFFFCC'>点此告诉我其他服务器参数</font></a>",
				"<a href='http://id002.com/env/hostlist.php' target='_blank'><font color='#FFFFCC'>更多服务器参数</font></a>");
//当前主机
$mtime[] = array("<font color=red>当前这台服务器</font>",addTime()." 毫秒",sqrtTime()." 毫秒");

/*获取服务器信息*/
//ZEND op
$url	= "http://".$mailcontent."?testinfo=phpinfo";
@$content = file_get_contents ($url);  
eregi("Optimizer (.*), Copyright", $content, $regs);  
$OptimizerVersion	=	$regs[1]; 
$info[] = array("域名","Domain Name",$_SERVER['SERVER_NAME']."&nbsp;-&nbsp;".getenv(SERVER_ADDR));//主机名
$info[] = array("服务器端口","Server Port",getenv(SERVER_PORT));//端口
$info[] = array("服务器操作系统","Operating System",PHP_OS); //服务器操作系统
$info[] = array("WEB服务器版本","Web Server Version",$_SERVER['SERVER_SOFTWARE']); //web服务器版本
$info[] = array("PHP版本","PHP Version",PHP_VERSION);//php版本
$info[] = array("服务器语种","Server Language",getenv("HTTP_ACCEPT_LANGUAGE")); //服务器语种
$info[] = array("ZEND版本","ZEND Version",zend_version());
if('' != $OptimizerVersion){
	$info[] = array("ZEND Optimizer版本","ZEND Optimizer Version",$OptimizerVersion);
}
$info[] = array("绝对路径","Full path",$_SERVER['DOCUMENT_ROOT']. "<br>".$_SERVER['$PATH_INFO']); //绝对路径
$info[] = array("服务器剩余空间","Disk Free Space",intval(diskfreespace(".") / (1024 * 1024))."M"); //服务器空间大小
$info[] = array("服务器时间","Server Current Time",date("n月j日H点i分s秒")); //服务器时间
//$info[] = array("","",get_current_user()); //用户
//$info[] = array("","",isset($_SERVER["SERVER_ADMIN"])?"<a href=\"mailto:$_SERVER[SERVER_ADMIN]\" title=发送邮件>$_SERVER[SERVER_ADMIN]</a>":"<a href=\"mailto:get_cfg_var(sendmail_from)\" title=发送邮件>get_cfg_var(sendmail_from)</a>"); //管理员邮箱

/*PHP基本特性*/
$dis_func = get_cfg_var("disable_functions");
$php[] = array("PHP信息","PHPINFO",ereg("phpinfo",$dis_func)?"<font color=red>不支持<b>×</b></font>":"<font color=green>支持<b>√</b></font><a href=$PHP_SELF?testinfo=phpinfo>点此查看PHPINFO详细信息</a>");
$php[] = array("自定义全局变量","register_globals",temp(get_cfg_var("register_globals")));
$php[] = array("脚本运行可占最大内存","memory_limit",get_cfg_var("memory_limit")?get_cfg_var("memory_limit"):"无"); //单个脚本运行时可占用的最大内存
$php[] = array("脚本上传文件大小限制","upload_max_filesize",get_cfg_var("upload_max_filesize")?get_cfg_var("upload_max_filesize"):"不允许上传附件");   //用PHP脚本上传文件大小限制
$php[] = array("被屏蔽的函数","disable_functions",get_cfg_var("disable_functions")?get_cfg_var("disable_functions"):"无"); //被屏蔽的函数
$php[] = array("POST方法提交限制","post_max_size",get_cfg_var("post_max_size")); //post方法提交内容限制
$php[] = array("脚本超时时间","max_execution_time",get_cfg_var("max_execution_time")."秒"); //脚本超时时间
$php[] = array("显示错误信息","display_errors",temp(get_cfg_var("display_errors"))); 

/*常见组件*/   
$obj[] = array("SMTP支持","smtp",temp(get_magic_quotes_gpc("smtp")));//SMTP
$obj[] = array("PHP安全模式","Safe_mode",temp(get_cfg_var("safe_mode")));  //PHP安全模式(Safe_mode)
$obj[] = array("XML 解析函数库","XML Support",temp(get_magic_quotes_gpc("XML Support")));//XML 支持      
$obj[] = array("FTP 文件传输函数库","FTP support",temp(get_magic_quotes_gpc("FTP support")));//FTP 支持
$obj[] = array("允许使用URL打开文件","allow_url_fopen",temp(get_cfg_var("allow_url_fopen")));//允许使用URL打开文件
$obj[] = array("动态链接库","enable_dl",temp(get_cfg_var("enable_dl")));//动态链接库

/*其他组件*/
$qobj[] = array("IMAP 电子邮件系统函数库","IMAP, POP3 and NNTP Functions",temp(function_exists("imap_close")));//IMAP电子邮件系统 
$qobj[] = array("历法运算函数库","Calendar Functions",temp(function_exists("JDToGregorian")));//历法
$qobj[] = array("压缩文件函数库(Zlib)","Zlib Compression Functions",temp(function_exists("gzclose"))); //压缩文件支持(Zlib)
$qobj[] = array("Session支持","Session Handling Functions",temp(function_exists("session_start"))); //Session支持
$qobj[] = array("Socket支持","Socket Functions",temp(function_exists("fsockopen"))); //Socket支持
$qobj[] = array("正则表达式函数库","PREL",temp(function_exists("preg_match")));//PREL相容语法 PCRE
@$gdInfo=gd_info();
$qobj[] = array("图像函数库","GD Library",function_exists("imageline")==1?temp(function_exists("imageline")).$gdInfo["GD Version"]:temp(function_exists("imageline")));//图形处理 GD Library
$qobj[] = array("FDF表单资料格式函数库","Forms Data Format Functions",temp(function_exists("FDF_close")));//FDF表单资料格式
$qobj[] = array("Iconv编码转换","iconv Functions",temp(function_exists("iconv")));//ICONV
$qobj[] = array("mbstring","Multi-Byte String Functions",temp(function_exists("mb_eregi")));//mb_string
$qobj[] = array("SNMP网络管理协议","SNMP Functions",temp(function_exists("snmpget")));//SNMP网络管理协议

/*数据库信息*/
$sql[] = array("MySQL 数据库","",temp(function_exists("mysql_close"))); //mysql数据库
$sql[] = array("ODBC 数据库","",temp(function_exists("odbc_close"))); //odbc数据库
$sql[] = array("Oracle 数据库","",temp(function_exists("ora_close"))); //ora数据库
$sql[] = array("Oracle 8 数据库","",temp(function_exists("OCILogOff")));//Oracle 8 数据库
$sql[] = array("SQL Server 数据库","",temp(function_exists("mssql_close")));//SQL Server数据库
$sql[] = array("mSQL 数据库","",temp(function_exists("msql_close")));//msql数据库
$sql[] = array("Hyperwave 数据库","",temp(function_exists("hw_close")));//Hyperwave数据库
$sql[] = array("dBase 数据库","",temp(function_exists("dbase_close")));//dbase数据库
$sql[] = array("PostgreSQL 数据库","",temp(function_exists("pg_connect")));//PostgreSQL数据库
$sql[] = array("firePro 数据库","",temp(function_exists("filepro")));//firePro数据库

function echoInfo($in,$tb=0)
{  
	$tw = $tb != 1 ? array("20%", "30%", "50%") : array("50%", "25%", "25%");
	for ($i = 0; $i < count($in); $i++)
	{
		$tbClass = $i%2 == 0 ? "bTable" : "cTable";
		$rs .= "<tr><td width={$tw[0]} class={$tbClass}>&nbsp; {$in[$i][0]}</td>
				<td width={$tw[1]} class={$tbClass}>{$in[$i][1]}</td>
				<td width={$tw[2]} class={$tbClass}>{$in[$i][2]}</td></tr>";
	}
	return $rs;
}
function echoTable($arr)
{
	for ($i = 0; $i < count($arr); $i++)
	{
		$rs .= '<table width="760" border="0" align="center" cellpadding="2" cellspacing="0" class="aTable">
				  <tr>
					<td colspan="3"><span class="aTitle"> ■'.$arr[$i][0].':::... </span></td>
				  </tr>'.$arr[$i][1].
				'</table>';
	}
	return $rs;
}
$arr[]	= array("服务器相关参数",echoInfo($info));
$arr[]	= array("PHP基本参数",echoInfo($php));
$arr[]	= array("常见组件信息",echoInfo($obj));
$arr[]	= array("其他组件信息",echoInfo($qobj));
$arr[]	= array("数据库支持信息",echoInfo($sql));
$arr[]	= array("服务器性能测试",echoInfo($mtime,1));
$page	= echoTable($arr);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>废墟のPHP探针V<?=$version?></title>
<style type="text/css">
<!--
body {
	table-layout: fixed;
	word-break:break-all;
	background-color: #B1C3D9;
	margin-top: 0px;
	color: #FFFFFF;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.LogoFont {
	font-family: "Arial Black";
	font-size: 18px;
	font-weight: bolder;
}
a:link {
	color: #FFFFFF;
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #FFFFFF;
}
a:hover {
	text-decoration: none;
	color: #CCCCCC;
}
a:active {
	text-decoration: none;
	color: #FFFFFF;
}
.aTable {
	background-color: #60778C;
}
.bTable {
	background-color: #7895AF;
}
.cTable {
	background-color: #97ADC1;
}
.aTitle {font-size: 12px; font-weight: bold; color: #D37A50; }
.input
{
	BORDER: 1px solid #394753;
	FONT-SIZE: 8pt;
	BACKGROUND-color: #B1C3D9;
	color: #435463;
	height: 12px;
	font-family: Tahoma, Vrinda, Arial;
}
.sub
{
	BACKGROUND-COLOR: #2C3741;
	BORDER: medium none;
	COLOR: #ffffff;
	HEIGHT: 14px;
	font-size: 8pt;
	font-family: Tahoma, Vrinda, Arial;
}
-->
</style>
</head>

<body>
<table width="760" align="center" cellpadding="2" cellspacing="0" class="aTable">
  <tr>
    <td width="164"> 废墟のPHP探针<br>
      <span class="LogoFont">PHP ENV</span> v <?=$version?> <br>
Server Environment Probe </td>
    <td width="588" align="right"><?=$ads?></td>
  </tr>
</table>
<table width="760" border="0" align="center" cellpadding="2" cellspacing="0" class="bTable">
  <tr>
    <td width="664"> 出现以下情况即表示您的空间不支持PHP： 1、访问本文件时提示下载。 2、访问本文件时看到类似“&lt;?php?&gt;”的文字。 </td>
    <td width="88" align="right"><a href="#bottom">底部</a>↓</td>
  </tr>
</table>
<?=$page?>
<A name="#function"></A>
<table width="760" border="0" align="center" cellpadding="2" cellspacing="0" class="aTable">
  <tr>
    <td colspan="2"><span class="aTitle"> ■函数支持情况检测:::... </span></td>
  </tr>
  <FORM action=<?=$_SERVER[PHP_SELF]?>#function method=post>
  <tr>
    <td class="bTable" width="80%">
	&nbsp;
	<input class=input type=text value="" name="fname" size=40> 请输入要检测的函数
	<input name="fc" value="check" type="hidden"></td>
	<td class="bTable" width="20%"><INPUT type=submit value="CHECK" class=sub>	</td>
  </tr>
	<?php
	if ("check" == $fc)
	{
		$ss=temp(function_exists($fname));
		echo "
			<tr>
			<td class='cTable' width='80%'>您检测的函数是<b> $fname </b></td>
			<td class='cTable' width='20%'>$ss</td>
			</tr>
			";
	}//END IF 
	?>
  </FORM>
</table>
<A name="#email"></A>
<table width="760" border="0" align="center" cellpadding="2" cellspacing="0" class="aTable">
  <tr>
    <td colspan="2"><span class="aTitle"> ■邮件发送支持情况检测:::... </span></td>
  </tr>
  <FORM action=<?=$_SERVER[PHP_SELF]?>#email method=post>
  <tr>
    <td class="bTable" width="80%">
	&nbsp;
	<input class=input type=text value="" name="toemail" size=40> 请输入框中输入一个邮件地址
	<input name="mt" value="check" type="hidden"></td>
	<td class="bTable" width="20%"><INPUT type=submit value="CHECK" class=sub>	</td>
  </tr>
	<?php
	if ("check" == $mt)
	{
		if (1 == function_exists("mail"))
		{
			if (!eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$",$toemail))
			{
				echo_info("请输入正确的E-mail地址！");
			}//END if
			else
			{
				$message="这是一封测试邮件！由 【废墟のPHP探针 v".$version."】 发出用于测试服务器的mail函数功能。\n欢迎使用废墟のPHP探针，本程序公开源代码，你可以任意复制、传播和使用。\n你可以从我的网站 ( http://wapcity.org.ru/  http://id002.com/), 或其他支持站点下载到本程序。";
				@mail($toemail, "测试邮件", $message);
				echo "
					<tr>
					<td class='cTable' width='80%'>一封测试邮件已经发送到邮箱<b>$toemail</b></td>
					<td class='cTable' width='20%'></td>
					</tr>
					";
			}
		}//END IF;
		else
		{
			echo_info("您的服务器不支持MAIL函数！");
		}
	}//END IF 
	?>
  </FORM>
</table>
<A name="#bottom"></A>
<table width="760" border="0" align="center" cellpadding="2" cellspacing="0" class="aTable">
  <tr>
    <td colspan="2"><span class="aTitle"> ■MySQL数据库连接测试:::... </span></td>
  </tr>
  <FORM action=<?=$_SERVER[PHP_SELF]?>#bottom method=post>
  <tr>
    <td class="bTable" width="80%">
	&nbsp;地址: <input class=input type=text value="localhost" name="sql_host" size=10>
	&nbsp;端口: <input class=input type=text value="3306" name="sql_port" size=10>
	&nbsp;帐号: <input class=input type=text name="sql_login" size=10>
	&nbsp;密码: <input class=input type=text name="sql_password" size=10>
	&nbsp;<input name="conn" value="mysql" type="hidden"></td>
	<td class="bTable" width="20%"><INPUT type=submit value="CHECK" class=sub>	</td>
  </tr>
	<?
	if ("mysql" == $conn)
	{
		if(function_exists("mysql_close")==1)
		{
			$link = @mysql_connect($sql_host.":".$sql_port, $sql_login, $sql_password);
			if ($link) 
			{
				echo_info("帐号 $sql_login 连接到MySql数据库正常");
			} else
			{
				echo_info("无法连接到MySql数据库！");
			}
		}
		else
		{
			echo_info("服务器不支持MySQL数据库！");
		}
	}//END IF
	?>
  </FORM>
</table>
<table width="760" border="0" align="center" cellpadding="2" cellspacing="0" class="aTable">
  <tr>
    <td colspan="2"><span class="aTitle"> ■PostgreSQL数据库连接测试:::... </span></td>
  </tr>
  <FORM action=<?=$_SERVER[PHP_SELF]?>#bottom method=post>
  <tr>
    <td class="bTable" width="80%">
	&nbsp;地址: <input class=input type=text value="localhost" name="sql_host" size=10>
	&nbsp;端口: <input class=input type=text value="5432" name="sql_port" size=10>
	&nbsp;帐号: <input class=input type=text name="sql_login" size=10>
	&nbsp;密码: <input class=input type=text name="sql_password" size=10>
	&nbsp;数据库名:<input class=input type=text name="sql_dbname" size=10>
	<input name="conn" value="psql" type="hidden"></td>
	<td class="bTable" width="20%"><INPUT type=submit value="CHECK" class=sub>	</td>
  </tr>
  <?
	if ("psql" == $conn)
	{
		if(function_exists("pg_connect")==1)
		{
			$conn_string = "host=$sql_host port=$sql_port dbname=$sql_dbname user=$sql_login password=$sql_password";
			$link = @pg_connect($conn_string);
			if ($link) 
			{
				echo_info("帐号 $sql_login 连接到PostgreSQL数据库正常");
			} else
			{
				echo_info("无法连接到PostgreSQL数据库！");
			}
		}
		else
		{
			echo_info("服务器不支持PostgreSQL数据库！");
		}
	}//END IF
  ?>
  </FORM>
</table>
<table width="760" border="0" align="center" cellpadding="2" cellspacing="0" class="aTable">
  <tr>
    <td colspan="3" align="center">
	<?php
	$page_time_end=getmicrotime(); 
	$pageTime = round(($page_time_end-$page_time_start)*1000000)/1000;
	echo "页面执行时间".$pageTime."毫秒";
	?>
	<br>
	欢迎访问 <a href="http://wapcity.org.ru" target="_blank">http://wapcity.org.ru</a> <a href="http://id002.com" target="_blank">http://id002.com</a><br>
	本程序由废墟の复甦 <a href="mailto:r.anerg@gmail.com">r.anerg@gmail.com</a> 编写，转载时请保留这些信息
	</td>
  </tr>
</table>
</body>
</html>
