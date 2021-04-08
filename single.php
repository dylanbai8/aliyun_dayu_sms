<?php



// 设置访问密码
$pass_set = "qwer1234";
$pass_get = $_GET["p"];
if ($pass_get != $pass_set) {echo "<p>禁止访问！</p>"; die;}



// 要发送的手机号码
$Phone = $_GET["n"];

// 变量和变量内容
$Code = "mima";
$CodeFor = $_GET["c"];

// 签名和模板ID
$Sign = "金雨";
$Template = "SMS_20858";

// 配置AccessKey
$KeyId = "LTAI5tSrVMJGwu4xqR";
$KeySecret = "1ec2V4TjwpVhk6DjhsQcNeX3";


if (empty($Phone)) {$Phone = $_POST["Phone"];}
if (empty($CodeFor)) {$CodeFor = $_POST["CodeFor"];}

?>
<html>
<head>
<meta charset="utf-8">
<title>会员盒子-售后短信</title>
</head>
<body>

<form action="?p=qwer1234" method="post">
变量: <input type="text" name="CodeFor" value="<?php echo $CodeFor;?>" autocomplete="off">
手机: <input type="text" name="Phone" autocomplete="off">
<input type="submit" value="发送">
</form>

</body>
</html>
<?php
if (empty($CodeFor)) {die;}
if (empty($Phone)) {die;}


class SignatureHelper {

    public function request($accessKeyId, $accessKeySecret, $domain, $params, $security=false, $method='POST') {
        $apiParams = array_merge(array (
            "SignatureMethod" => "HMAC-SHA1",
            "SignatureNonce" => uniqid(mt_rand(0,0xffff), true),
            "SignatureVersion" => "1.0",
            "AccessKeyId" => $accessKeyId,
            "Timestamp" => gmdate("Y-m-d\TH:i:s\Z"),
            "Format" => "JSON",
        ), $params);
        ksort($apiParams);

        $sortedQueryStringTmp = "";
        foreach ($apiParams as $key => $value) {
            $sortedQueryStringTmp .= "&" . $this->encode($key) . "=" . $this->encode($value);
        }

        $stringToSign = "${method}&%2F&" . $this->encode(substr($sortedQueryStringTmp, 1));

        $sign = base64_encode(hash_hmac("sha1", $stringToSign, $accessKeySecret . "&",true));

        $signature = $this->encode($sign);

        $url = ($security ? 'https' : 'http')."://{$domain}/";

        try {
            $content = $this->fetchContent($url, $method, "Signature={$signature}{$sortedQueryStringTmp}");
            return json_decode($content);
        } catch( \Exception $e) {
            return false;
        }
    }

    private function encode($str)
    {
        $res = urlencode($str);
        $res = preg_replace("/\+/", "%20", $res);
        $res = preg_replace("/\*/", "%2A", $res);
        $res = preg_replace("/%7E/", "~", $res);
        return $res;
    }

    private function fetchContent($url, $method, $body) {
        $ch = curl_init();

        if($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        } else {
            $url .= '?'.$body;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "x-sdk-client" => "php/2.0.0"
        ));

        if(substr($url, 0,5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $rtn = curl_exec($ch);

        if($rtn === false) {
            trigger_error("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), E_USER_ERROR);
        }
        curl_close($ch);

        return $rtn;
    }
}


function sendSms() {

    global $KeyId;
    global $KeySecret;
    global $Phone;
    global $Sign;
    global $Template;
    global $Code;
    global $CodeFor;

    $params = array ();
    $security = false; //是否启用https

    $accessKeyId = $KeyId;
    $accessKeySecret = $KeySecret;
    $params["PhoneNumbers"] = $Phone;
    $params["SignName"] = $Sign;
    $params["TemplateCode"] = $Template;
    $params['TemplateParam'] = Array (
        $Code => $CodeFor
    );

    if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
        $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
    }

    $helper = new SignatureHelper();

    $content = $helper->request(
        $accessKeyId,
        $accessKeySecret,
        "dysmsapi.aliyuncs.com",
        array_merge($params, array(
            "RegionId" => "cn-hangzhou",
            "Action" => "SendSms",
            "Version" => "2017-05-25",
        )),
        $security
    );

    return $content;
}


$results = print_r(sendSms(), true);

if(strpos($results,'OK') !==false){
    echo "<p>上次发送: $Phone</p>";
    echo "<p>返回结果: 成功！</p>";
}else{
    echo "<p>上次发送: $Phone</p>";
    echo "<p>返回结果: <b>失败！</b></p>";
}

?>

