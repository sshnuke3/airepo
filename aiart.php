<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <title>输入文本并提交</title>

</head>

<body>

    <form action="aiart.php" method="post">

        <label for="text1">正面关键词:</label>

        <input type="text" id="text1" name="text1"><br><br>

        <label for="text2">负面关键词:</label>

        <input type="text" id="text2" name="text2"><br><br>

        <input type="submit" value="提交">

    </form>

<?php
//composer require tencentcloud/tencentcloud-sdk-php
require_once 'vendor/autoload.php';
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Aiart\V20221229\AiartClient;
use TencentCloud\Aiart\V20221229\Models\TextToImageRequest;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $text1 = $_POST['text1']; // 获取名为 "text1" 的文本字段
    $text2 = $_POST['text2']; // 获取名为 "text2" 的文本字段
try {
    // 实例化一个认证对象，入参需要传入腾讯云账户 SecretId 和 SecretKey，此处还需注意密钥对的保密
    // 代码泄露可能会导致 SecretId 和 SecretKey 泄露，并威胁账号下所有资源的安全性。以下代码示例仅供参考，建议采用更安全的方式来使用密钥，请参见：https://cloud.tencent.com/document/product/1278/85305
    // 密钥可前往官网控制台 https://console.cloud.tencent.com/cam/capi 进行获取
    $cred = new Credential("", "");
    // 实例化一个http选项，可选的，没有特殊需求可以跳过
    $httpProfile = new HttpProfile();
    $httpProfile->setEndpoint("aiart.tencentcloudapi.com");

    // 实例化一个client选项，可选的，没有特殊需求可以跳过
    $clientProfile = new ClientProfile();
    $clientProfile->setHttpProfile($httpProfile);
    // 实例化要请求产品的client对象,clientProfile是可选的
    $client = new AiartClient($cred, "ap-shanghai", $clientProfile);

    // 实例化一个请求对象,每个接口都会对应一个request对象
    $req = new TextToImageRequest();

    $params = array(
        "Prompt" => $text1,
        "NegativePrompt" => $text2,
        "Styles" => array( "000" ),
        "ResultConfig" => array(
            "Resolution" => "768:768"
        )
    );
    $req->fromJsonString(json_encode($params));

    // 返回的resp是一个TextToImageResponse的实例，与请求对象对应
    $resp = $client->TextToImage($req);

    // 输出json格式的字符串回包
    $p_d=base64_decode(json_decode($resp->toJsonString(), true)["ResultImage"]);
    file_put_contents("out.jpg", $p_d);
    echo '<a href="out.jpg">点击查看图片</a>';
}
catch(TencentCloudSDKException $e) {
    echo $e;
}
}
?>

</body>

</html>
