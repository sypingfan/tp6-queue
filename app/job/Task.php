<?php
/**
 * Created by PhpStorm.
 * User: 平凡
 * Date: 2023/10/3
 * Time: 21:57
 */

namespace app\job;

use think\facade\Log;
use think\queue\Job;

class Task
{
    public function fire(Job  $job,$data)
    {
        Log::info("测试:".json_encode($data));
        $rt = $this->doJob($data);
        if($rt){
            $job->delete();
            return true;
        }
        //重试第三次失败todo...
        if($job->attempts()==3){
            $job->delete();
            return false;
        }
        //执行失败10S后重试
        $job->release();
    }
    public function doJob($data){
        echo date("Y-m-d H:i:s")."\n";
        return false;
    }
}