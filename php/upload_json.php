<?php
/**
 * 上传配置
 */
require_once 'JSON.php';

//PHP上传失败
if (!empty($_FILES['imgFile']['error'])) {
    switch ($_FILES['imgFile']['error']) {
        case '1':
            $error = '超过php.ini允许的大小。';
            break;
        case '2':
            $error = '超过表单允许的大小。';
            break;
        case '3':
            $error = '图片只有部分被上传。';
            break;
        case '4':
            $error = '请选择图片。';
            break;
        case '6':
            $error = '找不到临时目录。';
            break;
        case '7':
            $error = '写文件到硬盘出错。';
            break;
        case '8':
            $error = 'File upload stopped by extension。';
            break;
        case '999':
        default:
            $error = '未知错误。';
    }
    alert($error);
}


function alert($msg)
{
    header('Content-type: text/html; charset=UTF-8');
    $json = new Services_JSON();
    echo $json->encode(array('error' => 1, 'message' => $msg));
    exit;
}

//引入上传方法文件
require_once("./drive/upload.php");