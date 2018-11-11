<?php
/*
	示例说明：
		$names		数据结构说明数组，用户可根据此数组编写变量模版（通过js的一些设置，可实现可视化的变量选用）
		$data		实际数据，例如下单数据等
		$template	根据$names数组编写的模版字符串
		$res_string	根据以上三者生成的最终字符串（可用于发送消息、短信等）
*/
$names=array(
	'store_name'=>array('title'=>'门店名称'),
	'store_address'=>array('title'=>'门店地址'),
	'store_mobile'=>array('title'=>'门店电话'),
	'goods_list'=>array('title'=>'菜品','is_array'=>true,'child'=>array(
		'name'=>array('title'=>'名称'),
		'price'=>array('title'=>'单价'),
		'num'=>array('title'=>'数量'),
	),),
	'user'=>array('title'=>'用户信息','child'=>array(
		'name'=>array('title'=>'姓名'),
		'mobile'=>array('title'=>'电话'),
		'address'=>array('title'=>'地址'),
	)),
);
$data=array(
	'store_name'=>'黄焖鸡',
	'store_address'=>'民治展涛楼下',
	'store_mobile'=>'13588888888',
	'goods_list'=>array(
		array('name'=>'黄焖鸭','price'=>'14','num'=>1),
		array('name'=>'黄焖鸡','price'=>'13','num'=>2),
		array('name'=>'黄焖猪脚','price'=>'15','num'=>3),
		array('name'=>'黄焖大虾','price'=>'15','num'=>5),
	),
	'user'=>array(
		'name'=>'吴爷',
		'mobile'=>'13688888888',
		'address'=>'展涛A座1306',
	),
);
$template=<<<TPL
外卖订单
门店名称：【门店名称】
门店地址：【门店地址】
门店电话：【门店电话】
菜品列表
名称		单价	数量
【遍历【菜品】】
16|10|6
【菜品.名称】|【菜品.单价】|【菜品.数量】
【结束【菜品】】
收货人名称：【用户信息.姓名】
收货人电话：【用户信息.电话】
收货人地址：【用户信息.地址】
TPL;
require 'template_replace.php';
$replace=new template_replace();
$res_string=$replace->replace($names,$data,$template);
echo $res_string;
exit;
