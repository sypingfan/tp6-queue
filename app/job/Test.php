<?php
/**
 * Created by PhpStorm.
 * User: 平凡
 * Date: 2023/10/4
 * Time: 17:08
 */

namespace app\job;

use think\Exception;
use think\facade\Db;
use think\facade\Log;
use think\queue\Job;

class Test
{
    public function fire(Job $job,$data)
    {
        try {
            //TODO
            Log::info("开始发送消息:".json_encode($data));
            //1.给用户发送消息
            $flag = $this->insertMsg($data);

            if($flag){
                //2.发送完成后 删除job
                Log::info("发送成功");
                $job->delete();
            }else{
                Log::error("发送失败");
                //任务轮询4此后删除
                if($job->attempts()>3){
                    //第1种处理方式：重新发布任务，该任务延迟10秒后再执行
//                    $job->release(10);
                    //第2种处理方式：原任务的基础上1分钟在执行一次并增加尝试次数
//                    $job->failed();
                    //第3中处理方式：删除任务
                    $job->delete();
                }
            }
        }catch (\Exception $e){
            //队列执行失败
            Log::error("消息队列达到最大重复次数执行后失败：".$e->getMessage().json_encode($data));
        }
    }
    public function insertMsg($data)
    {
        $result = Db::name("crawling")->insert([
            "uniqid"=>$data['uniqid'],
            "time"=>$data['time'],
            "msectime"=>$data['msectime'],
            "title"=>$data['title'],
            "url"=>$data['url']
        ]);
        Log::info("数据库添加：".$result.json_encode($data));
        return $result==1;
    }

}
