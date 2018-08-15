# 云签到

> cas(cloud add sign)
> 
> emmmm.......

主要是利用php来设置一个定时任务,实现每日签到

然后就是签到的封包了

不过php是单线程的,效率挺低,后面可以改成多进程的模式提高效率

监控文件:app\admin\ctrl\monitor.php

使用cli模式运行

总之现在是一个很不完善的玩意=_=

## 安装
将源码克隆至自己的服务器,根目录下的db.sql为数据库结构文件

利用phpmyadmin或其他工具将db.sql导入到数据库

打开icf/config.php文件,修改数据库的配置

```php
<?php
'db' => [
        'type' => 'mysql',
        'server' => 'localhost',
        'port' => 3306,
        'db' => 'cas',//数据库名
        'user' => 'root',//数据库用户
        'pwd' => '',//数据库用户密码
        'prefix' => 'cas_'//表前缀
]
```
因为还没有写注册功能,所以需要自己去数据库中添加账号....(~_~)

密码加密规则:hash('sha256', $uid . $pwd . config('pwd_encode_salt'));

config为读取数据库cas_config中的pwd_encode_salt字段

### 启动
使用php运行目录下的start.php文件,即可启动监控

```base
php start.php
```

在```app/cache/log/year/month/```目录下,可以看到运行日志

在表```cas_log```中可以看到详细的数据

如果启动不成功,请在数据库中修```改cas_config```表的```monitor_status```,值修改为0,重新启动

0为未启动,1为启动

## 添加平台
继承 app\common\BasePlatform 抽象类,实现里面的方法

在数据库cas_platform表中添加平台的信息,在cas_action中添加操作的信息

实现的类要放在app/common/api目录下

## 支持平台
- [x] 百度贴吧
- [x] b站直播
- [x] 网易云
- [x] V2EX
- [ ] 联通客户端
- [ ] 网易云手机客户端
**......**

## TODO
- [x] 优化操作流程
- [x] CLI模式启动
- [ ] 平台账号登录,更方便的添加账号
- [ ] 多进程
- [ ] 更多的平台(大家可以来推荐哦)
- [ ] 注册(先自己玩着)
- [ ] 漂亮的页面(至少能看吧)

