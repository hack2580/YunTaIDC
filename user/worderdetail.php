<?php
include("../includes/common.php");
if(empty($_SESSION['ytidc_user']) && empty($_SESSION['ytidc_pass'])){
  	@header("Location: ./login.php");
     exit;
}else{
  	$username = daddslashes($_SESSION['ytidc_user']);
  	$userkey = daddslashes($_SESSION['ytidc_adminkey']);
  	$user = $DB->query("SELECT * FROM `ytidc_user` WHERE `username`='{$username}'");
  	if($user->num_rows != 1){
      	@header("Location: ./login.php");
      	exit;
    }else{
    	$user = $user->fetch_assoc();
      	$userkey1 = md5($_SERVER['HTTP_HOST'].$user['password']);
      	if($userkey != $userkey1){
      		@header("Location: ./login.php");
      		exit;
      	}
    }
}
$id = daddslashes($_GET['id']);
$result = $DB->query("SELECT * FROM `ytidc_worder` WHERE `id`='{$id}' AND `user`='{$user['id']}'");
if($result->num_rows != 1){
	@header("Location: ./worder.php");
	exit;
}else{
	$row = $result->fetch_assoc();
}
$template = file_get_contents("../templates/".$conf['template']."/user_header.template").file_get_contents("../templates/".$conf['template']."/user_worder_detail.template").file_get_contents("../templates/".$conf['template']."/user_footer.template");
$template_code = array(
	'site' => $site,
	'config' => $conf,
	'template_file_path' => '../templates/'.$conf['template'],
	'user' => $user,
	'worder' => $row,
);
$template = template_code_replace($template, $template_code);
echo $template;