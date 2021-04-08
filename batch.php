<?php

// 设置访问密码
$pass_set = "qwer1234";
$pass_get = $_GET["p"];
if ($pass_get != $pass_set) {echo "<p>禁止访问！</p>"; die;}

session_start();
$CodeFor = $_POST["CodeFor"];
$Phone = $_POST["Phone"];

?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>会员盒子-售后短信</title>
<style type="text/css">
textarea,input{
    outline-style: none;
    border: 1px solid #ccc; 
    border-radius: 3px;
    padding: 13px 14px;
    width: 260px;
    font-size: 14px;
    font-weight: 700;
    font-family: "Microsoft soft";
}
input:focus{
    border-color: #66afe9;
    outline: 0;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(102,175,233,.6);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(102,175,233,.6);
}
div{
    width: 260px;
    margin: 0 auto;
}
</style>
</head>
<body>
<div>
<form action="?p=qwer1234" method="post">
<p>变量:</p><p><input type="text" name="CodeFor" value="<?php echo $CodeFor; ?>" autocomplete="off"></p>
<p>手机:</p><p><textarea rows="13" name="Phone"></textarea></p>
<p><input type="submit" value="发送"></p>
</form>

<?php
if ($_SESSION['SUB'] == $_POST["Phone"]) {
    echo "<p><b>请勿重复提交！</b></p>";
	die;
} else {
    $_SESSION['SUB'] = $_POST["Phone"];
}
if (empty($CodeFor)) {die;}
if (empty($Phone)) {die;}


$array = explode("\r\n", $Phone);
$get_num = count($array);
echo "<p><b>本次发送合计: ".$get_num."条</b></p>";

foreach ($array as $value) {

	$url = "https://xxxxx.com/api/send_sms_api.php?p=qwer1234&c=".$_POST["CodeFor"]."&n=".$value;
    // echo $url."<br><br>";
    $results = file_get_contents($url);
    // echo $value."<br><br>";
    // echo $results."<br><br>";
    if (strpos($results,'OK') !== false) {
        echo "<p>上次发送: $value</p>";
        echo "<p>返回结果: 成功！</p>";
    } else {
        echo "<p>上次发送: $value</p>";
        echo "<p>返回结果: <b>失败！</b></p>";
    }
}

?>

</div>
</body>
</html>

