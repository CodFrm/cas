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

修改更目录下的```.env```文件,修改数据库配置部分

config为读取数据库cas_config中的pwd_encode_salt字段

### 启动
使用php运行目录下的start.php文件,即可启动监控

```base
php start.php
//注意后台运行
nohup php start.php > /dev/null 2>&1 &
```

在```app/cache/log/year/month/```目录下,可以看到运行日志

在表```cas_log```中可以看到详细的数据

如果启动不成功,请在数据库中修```改cas_config```表的```monitor_status```,值修改为0,重新启动

0为未启动,1为启动

## 添加平台
继承```app\common\BasePlatform```抽象类,实现里面的方法

在数据库cas_platform表中添加平台的信息,在cas_action中添加操作的信息

实现的类要放在app/common/api目录下

## 支持平台
> 太久没更新有些失效了...

- [x] 百度贴吧
- [x] b站直播
- [ ] 联通客户端
**......**

## TODO
> 先不挖太多坑了

- [x] 优化操作流程
- [x] CLI模式启动
- [ ] 平台账号登录,更方便的添加账号

