<?php
/**
 * Created by PhpStorm.
 * User: 平凡
 * Date: 2023/10/4
 * Time: 17:07
 */

namespace app\controller;

use app\job\Test;

class Queue
{
    public function sendMsg()
    {
        $url ='http://www.cssmoban.com/cssthemes/27514.shtml';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);//URL地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//获取页面内容或提交数据，有时候希望返回的内容作为变量储存，而不是直接输出。这个时候就必需设置curl的CURLOPT_RETURNTRANSFER选项为1或true。
        curl_setopt($ch, CURLOPT_COOKIE,"name=value; name2=value2");//通过手动设置CURLOPT_COOKIE参数来设置Cookie，以保持用户状态。
        curl_setopt($ch, CURLOPT_PROXY,":8080");//设置CURLOPT_PROXY参数来使用代理服务器，以隐藏自己的真实IP地址。
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);//自动处理页面重定向
        //头部信息
        $user_agents = array("Mozilla/5.0(Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3",    "Mozilla/5.0(Windows NT 6.1; WOW64; rv:54.0) Gecko/20100101 Firefox/54.0",    "Mozilla/5.0(Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36",);
        curl_setopt($ch, CURLOPT_USERAGENT,$user_agents[array_rand($user_agents)]);//随机User-Agent来模拟不同的浏览器访问，以绕过反爬虫机制。

        $output = curl_exec($ch);
        //检查是否发送了错误
        if(curl_errno($ch)){
            //获取错误码
           echo json_encode(curl_error($ch));

        }
        curl_close($ch);
        //获取到目标页面的HTML源代码，并通过DOMDocument类和DOMXPath类对其进行解析和查询，最终输出了所有target为“_blank”的a标签的href属性值。
        $html = mb_convert_encoding($output,"UTF-8","GBK");//处理编码
        $doc = new \DOMDocument();
        @$doc->loadHTML($output);
        $xpath = new \DOMXPath($doc);
        $elements =$xpath->query('//a[@target="_blank"]');//查找

        foreach ($elements as $element) {
            $href =  $element->getAttribute("href");
            $alt =  $element->getAttribute("alt");
            $msgData = array(
                "uniqid"=>uniqid(),
                "time"=>date("Y-m-d H:i:s",time()),
                "msectime"=>$this->get_msectime(),
                "title"=>$alt,
                "url"=>$href
            );
            $queueName = "task";
            \think\facade\Queue::push(Test::class,$msgData,$queueName);//立即执行
//            think\facade\Queue::later($delay, $job, $data = '', $queue = null)//延时$delay秒后执行

        }







    }

    //返回当前的毫秒时间戳
    function get_msectime()
    {

        list($msec, $sec) = explode(' ', microtime());

        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);

        return $msectime;
    }

    public function register()
    {
        //1.用户注册成功
//        User::register();//模拟用户注册成功
        //2.给用户发送消息
        $this->sendMsg();
        echo "success";
    }


}