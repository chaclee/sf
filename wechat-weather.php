<?php
/**
 * wechat php test
 */
// define your token
define("TOKEN", "rainwsy");
$wechatObj = new wechatCallbackapiTest();
if (! empty($_GET['echostr'])) {
    $wechatObj->valid();
}
$wechatObj->responseMsg();

class wechatCallbackapiTest
{

    public function valid()
    {
        $echoStr = $_GET["echostr"];
        
        // valid signature , option
        if ($this->checkSignature()) {
            echo $echoStr;
            exit();
        }
    }

    public function responseMsg()
    {
        // get post data, May be due to the different environments
        $postStr = file_get_contents("php://input");
        
        // extract post data
        if (! empty($postStr)) {
            /*
             * libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
             * the best way is to check the validity of xml by yourself
             */
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>0</FuncFlag>
                            </xml>";
            if (! empty($keyword)) {
                $msgType = "text";
                
                $url = 'http://api.map.baidu.com/telematics/v3/weather?location=' . urlencode($keyword) . '&output=json&ak=D6aVcxzKNkr3KnZNfyEdfcwh';
                
                $json = file_get_contents($url);
                
                $obj = json_decode($json);
                
                if ($obj->error !== 0) {
                    
                    echo sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, '输入有误,不存在的城市');
                    
                    exit();
                }
                
                $text = '';
                foreach ($obj->results[0]->weather_data as $row) {
                    $text .= $row->date . "-" . $row->weather . "-" . $row->wind . "-" . $row->temperature . "\n\n";
                }
                
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, trim($text));
                echo $resultStr;
            } else {
                echo "Input something...";
            }
        } else {
            echo "";
            exit();
        }
    }

    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (! defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        
        $token = TOKEN;
        $tmpArr = array(
            $token,
            $timestamp,
            $nonce
        );
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    private function express($com, $no)
    {
        $url = 'http://www.kuaidi100.com/query?type=' . $com . '&postid=' . $no;
        $json = file_get_contents($url);
        $json = json_decode($json);
        $content = '';
        if ($json->status == 200) {
            $content .= sprintf('当前查询的是:%s %s %s', $json->com, $json->nu, "\n");
            foreach ($json->data as $row) {
                $content .= sprintf('%s  %s%s', $row->time, $row->context, "\n");
            }
        } else {
            $content = $json->message;
        }
        return $content;
    }
}
