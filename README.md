﻿<p align=""><h4>EasyTask简单易用的PHP常驻内存定时任务包</h4></p>
<p align="">
<a href="" rel="noopener noreferrer" target="_blank" rel="noopener noreferrer">
<img src="https://www.gaojiufeng.cn/static/images/stable_version.svg" style="max-width:100%;">
<img src="https://www.gaojiufeng.cn/static/images/php_version.svg" style="max-width:100%;">
<img src="https://www.gaojiufeng.cn/static/images/license.svg" style="max-width:100%;">
</a>
</p>

## <h4 style="text-align:left">  项目介绍 </h4>
<p>&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;EasyTask是PHP常驻内存定时任务Composer包，通过其简洁的Api，您可以用它来完成需要长期反复运行的任务(如订单超时自动取消,短信邮件异步推送,后台报表数据异步生成,队列/消费者/频道订阅者)，任何在FPM下比较耗时的功能您都可以交给它来完成。我们还支持任务异常退出自动恢复任务，您还能自定义配置异常信息(例如接收异常通知到您的邮件或短信)。工具包同时支持windows、linux、mac环境运行。
</p>

## <h4>   运行环境 </h4>

<ul>
    <li>windows：PHP>=5.4 (依赖com_dotnet+wpc扩展,<a href="https://www.gaojiufeng.cn/static/exe/Wpc_install.zip" target="_blank">wpc扩展一键安装包</a>）</li>  
    <li>linux|mac：PHP>=5.4 (依赖pcntl+posix扩展,一般默认已装）</li>  
</ul>  

## <h4>  Composer安装 </h4>

~~~
  composer require easy-task/easy-task
~~~

## <h5>【一】. 快速入门->创建任务 </h5>

~~~
//初始化
$task = new Task();

// 设置常驻内存
$task->setDaemon(true);

// 设置项目名称
$task->setPrefix('EasyTask');

// 设置记录运行时目录(日志或缓存目录)
$task->setRunTimePath('./Application/Runtime/');

// 1.添加闭包函数类型定时任务(开启2个进程,每隔10秒执行1次)
$task->addFunc(function () {
    $url = 'https://www.gaojiufeng.cn/?id=243';
    @file_get_contents($url);
}, 'request', 10, 2);

// 2.添加类的方法类型定时任务(同时支持静态方法)(开启1个进程,每隔20秒执行1次)
$task->addClass(Sms::class, 'send', 'sendsms', 20, 1);

// 3.添加指令类型的定时任务(开启1个进程,每隔10秒执行1次)
$command = 'php /www/web/orderAutoCancel.php';
$task->addCommand($command,'orderCancel',10,1);

// 启动任务
$task->start();
~~~

## <h5>【二】. 快速入门->连贯操作 </h5>

~~~
$task = new Task();

// 设置常驻内存
$task->setDaemon(true)   

// 设置项目名称
->setPrefix('ThinkTask')   

// 设置系统时区
->setTimeZone('Asia/Shanghai')  

// 设置子进程挂掉自动重启
->setAutoRecover(true)  

// 设置PHP运行路径,一般Window系统才需要设置,当系统无法找到才需要您手动设置
->setPhpPath('C:/phpEnv/php/php-7.0/php.exe')

/**
 * 设置记录运行时目录(日志或缓存目录)
 * 不设置的话Linux默认/tmp/目录
 * 不设置的话Window默认C:\Windows\Temp目录
 */
->setRunTimePath('./Application/Runtime/')

/**
 * 关闭EasyTask的异常注册
 * EasyTask将不再监听set_error_handler/set_exception_handler/register_shutdown_function事件
 */
->setCloseErrorRegister(true)

/**
 * 设置接收运行中的错误或者异常(方式1)
 * 您可以自定义处理异常信息,例如将它们发送到您的邮件中,短信中,作为预警处理
 * (不推荐的写法,除非您的代码健壮)
 */
->setErrorRegisterNotify(function ($ex) {
    //获取错误信息|错误行|错误文件
    $message = $ex->getMessage();
    $file = $ex->getFile();
    $line = $ex->getLine();
})

/**
 * 设置接收运行中的错误或者异常的Http地址(方式2)
 * Easy_Task会POST通知这个url并传递以下参数:
 * errStr:错误信息
 * errFile:错误文件
 * errLine:错误行
 * 您的Url收到POST请求可以编写代码发送邮件或短信通知您
 * (推荐的写法)
 */
->setErrorRegisterNotify('https://www.gaojiufeng.cn/rev.php')

// 添加任务定时执行闭包函数
->addFunc(function () {
    echo 'Success3' . PHP_EOL;
}, 'fucn', 20, 1)   

// 添加任务定时执行类的方法
->addClass(Sms::class, 'send', 'sendsms1', 20, 1)   

// 添加任务定时执行命令
->addCommand('php /www/wwwroot/learn/curl.php','cmd',6,1)

// 启动任务
->start();
~~~

## <h5>【三】. 快速入门->命令整合 </h5>

~~~
// 获取命令
$force = empty($_SERVER['argv']['2']) ? '' : $_SERVER['argv']['2'];
$command = empty($_SERVER['argv']['1']) ? '' : $_SERVER['argv']['1'];

// 配置任务
$task = new Task();
$task->setRunTimePath('./Application/Runtime/')
$task->addFunc(function () {
        $url = 'https://www.gaojiufeng.cn/?id=271';
        @file_get_contents($url);
    }, 'request', 10, 2);;

// 根据命令执行
if ($command == 'start')
{
    $task->start();
}
elseif ($command == 'status')
{
    $task->status();
}
elseif ($command == 'stop')
{
    $force = ($force == 'force'); //是否强制停止
    $task->stop($force);
}
else
{
    exit('Command is not exist');
}

启动任务: php console.php start
查询任务: php console.php status
普通关闭: php console.php stop
强制关闭: php console.php stop force
~~~

## <h5>【四】. 快速入门->认识输出信息 </h5>

~~~
┌─────┬──────────────┬─────────────────────┬───────┬────────┬──────┐
│ pid │ name         │ started             │ timer │ status │ ppid │
├─────┼──────────────┼─────────────────────┼───────┼────────┼──────┤
│ 32  │ Task_request │ 2020-01-10 15:55:44 │ 10    │ active │ 31   │
│ 33  │ Task_request │ 2020-01-10 15:55:44 │ 10    │ active │ 31   │
└─────┴──────────────┴─────────────────────┴───────┴────────┴──────┘
参数:
pid:当前定时任务的进程id
name:您为您的定时任务起的别名
started:定时任务启动时间
timer:定时任务执行间隔时间
status:定时任务状态
ppid:管理当前定时任务的守护进程id
~~~

## <h5>【五】. 进阶了解->建议阅读 </h5>

~~~
(1). 建议您使用绝对路径进行开发,是标准更是规范
(2). 禁止在任务中使用exit/die语法,否则导致整个进程退出
(3). Windows建议开启popen,pclose方法,会自动尝试帮您解决CMD输出中文乱码问题
(4). 日志文件在运行时目录的Log目录下,标出输入输出异常文件在运行时目录Std目录下
(5). 普通停止任务,任务会在执行成功后开始安全退出,强制停止任务直接退出任务,可能正在执行就强制退出
(6). 开发遵守先同步启动测试正常运行无任何报错再设置异步运行,有问题查看日志文件或者标准输入输出异常文件,或者上QQ群反馈
~~~

## <h5>【六】. 进阶了解->框架集成教程 </h5>

&ensp;&ensp;[<font size=2>-> thinkphp3.2.x教程</font>](https://www.gaojiufeng.cn/?id=293). 

&ensp;&ensp;[<font size=2>-> thinkPhp5.x.x教程</font>](https://www.gaojiufeng.cn/?id=294).

&ensp;&ensp;[<font size=2>-> laravelPhp6.x.x教程</font>](https://www.gaojiufeng.cn/?id=295).

## <h5>【七】. 进阶了解->推荐操作 </h5>

~~~
(1).推荐使用7.1以上版本的PHP,支持异步信号,不依赖ticks
(2).推荐安装php_event扩展基于事件轮询的毫秒级定时支持
~~~

## <h5>【八】. 进阶了解->时间参数支持crontab命令 </h5>

~~~
 (1).特殊表达式:
    @yearly                    每年运行一次 等同于(0 0 1 1 *) 
    @annually                  每年运行一次 等同于(0 0 1 1 *)
    @monthly                   每月运行一次 等同于(0 0 1 * *) 
    @weekly                    每周运行一次 等同于(0 0 * * 0) 
    @daily                     每日运行一次 等同于(0 0 * * *) 
    @hourly                    每小时运行一次 等同于(0 * * * *)
 (2).标准表达式:
    '30 21 * * *'              每天晚上21:30执行一次
    '0 23 * * 6'               每周星期六的晚上23:00执行一次
    '3,15 * * * *'             每小时的第3分钟和第15分钟执行一次
    '45 4 1,10,22 * *'         每月的1/10/22日的04:45执行一次
    '3,15 8-11 * * *'          每天上午8点到11点的第3分钟和第15分钟执行一次
    其他指令请自己测试
   使用example/build_cron_date.php生成执行时间列表来检查自己的命令是否符合预期
~~~

## <h5>【九】. 特别感谢 </h5>
~~~
(1).ThinkPHP(命令行输出组件基于Tp_Table组件),官方地址:http://www.thinkphp.cn/
(2).Cron-expression(Crontab命令解析和版本兼容基于Cron-expression),官方地址:https://github.com/dragonmantank/cron-expression
~~~
## <h5>【十】. Bug反馈 </h5>
~~~
请反馈至QQ群60973229,感谢持续反馈的用户,是您的反馈让EasyTask越来越稳定!
~~~