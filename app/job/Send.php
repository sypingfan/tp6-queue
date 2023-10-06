<?php
/**
 * Created by PhpStorm.
 * User: 平凡
 * Date: 2023/10/3
 * Time: 21:52
 */

namespace app\job;

use think\facade\Queue;

class Send
{

    public function job()
    {

        $jobHandleClassName = "app\job\Task";
        $jobQueueName =  'task';
        $orderData = ['order_sn'=>uniqid()];
//        $isPushed =  Queue::later();//立即执行
        $isPushed = Queue::later(10,$jobHandleClassName,$orderData,$jobQueueName);////这儿的10是指10秒后执行队列任务
        if($isPushed){
            echo "队列添加成功";
        }else{
            echo "队列插入失败";
        }
    }
}