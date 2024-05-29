<?
header("Content-Type:text/html;charset=utf8");
date_default_timezone_set('asia/shanghai');//把时区调整为中国（上海）时区

$con=mysqli_connect("127.0.0.1","root","2eec8a8f0f4129a6","weixin");
//mysqli_select_db($db, 'order');  //假设建立连接时未指定数据库则选择使用的数据库，切换使用的数据库
if (mysqli_connect_errno($con))
{
    echo "连接 MySQL 失败: " . mysqli_connect_error();
}
mysqli_query($con,"SET NAMES UTF8MB4");

$sql = 'UPDATE `weixin`.`11_jingcai_lk` SET `openid` = "1213" WHERE `11_jingcai_lk`.`id` ="4"';
mysqli_query($con,$sql);

echo 'sdfsdffsd';

?>