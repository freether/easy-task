<?php
namespace EasyTask\plugin;

use \Thread as Thread;

/**
 * 多线程基类
 */
class TaskThread extends Thread
{
    /**
     * 线程执行的任务
     * @var $item
     */
    public $item;

    /**
     * 当前进程Id
     * @var int
     */
    private $creatorId;

    /**
     * 构造函数
     * @var $item
     */
    public function __construct($item)
    {
        $this->item = $item;
    }

    /**
     * 获取当前线程Id
     * @return int
     */
    public function getThreadId()
    {
        return parent::getThreadId();
    }

    /**
     * 获取执行当前线程的线程Id
     * @return int
     */
    public function getCurrentId()
    {
        return parent::getCurrentThreadId();
    }

    /**
     * 执行任务体
     */
    private function callTask()
    {
        $item = $this->item;
        if ($item['type'] == 0)
        {
            $func = $item['func'];
            $func();
        }
        elseif ($item['type'] == 1)
        {
            call_user_func([$item['class'], $item['func']]);
        }
        else
        {
            $object = new $item['class']();
            call_user_func([$object, $item['func']]);
        }
    }

    /**
     * 单线程执行的任务
     */
    public function run()
    {
        //记录线程ID
        $this->creatorId = Thread::getCurrentThreadId();

        //修复线程中时间问题
        date_default_timezone_set('Asia/Shanghai');

        //循环执行任务
        while (true)
        {
            //执行任务
            $this->callTask();

            //Cpu休息
            sleep($this->item['time']);
        }
    }
}