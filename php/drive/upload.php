<?php
/**
 * 调用上传接口
 * $link->平台后端统一上传接口
 */
$link = '/admin/adminAccount/move_upload.html'; //本地的上传接口
$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
$uploadUrl = $http_type . $_SERVER['HTTP_HOST'] . $link; //构建远程上传接口
//有上传文件时
if (empty($_FILES) === false) {
    //原文件名
    $file_name = $_FILES['imgFile']['name'];
    //服务器上临时文件名
    $tmp_name = $_FILES['imgFile']['tmp_name'];
    //文件类型
    $file_type = $_FILES['imgFile']['type'];

    //表单请求参数
    $postData = [
        'path' => 'editor',
        'file' => new \CURLFile(realpath($tmp_name), $file_type, $file_name),
    ];

    $res = curlUploadFile($uploadUrl, $postData);
    if ($res['body'] && !empty($res['body'])) {
        $arr = json_decode($res['body'], true);
        if (isset($arr['code']) && $arr['code'] == 0) {
            $file_url = $arr['data']['signLink'];
            header('Content-type: text/html; charset=UTF-8');
            $json = new Services_JSON();
            echo $json->encode(array('error' => 0, 'url' => $file_url));
            exit;
        } else {
            $msg = isset($arr['msg']) && !empty($arr['msg']) ? $arr['msg'] : "服务器无响应，刷新后再试！";
            alert($msg);
        }

    } else {
        alert("服务器无响应，刷新后再试");
    }
}

/**
 * CURL文件上传
 * @param $url
 * @param $data
 * @return array
 */
function curlUploadFile($url, $data)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-type' => 'multipart/form-data;charset=UTF-8']);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_COOKIE, fmtCookie($_COOKIE));
    $res = curl_exec($curl);
    $info = curl_getinfo($curl);
    $error = curl_error($curl);
    curl_close($curl);

    return [
        'body' => $res,
        'info' => $info,
        'error' => $error,
    ];
}

/**
 * 格式成html页面cookie结构
 * @param array $cookie
 * @return string
 */
function fmtCookie(array $cookie)
{
    $str = '';
    foreach ($cookie as $key => $vo) {
        if ($str) {
            $str .= ';' . $key . '=' . $vo;
        } else {
            $str .= $key . '=' . $vo;
        }
    }
    return $str;
}
