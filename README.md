# 云签到

> cas(cloud add sign)
> 
> emmmm.......

主要是利用php来设置一个定时任务,实现每日签到

然后就是签到的封包了

不过php是单线程的,效率挺低,后面可以改成多进程的模式提高效率

监控文件:app\admin\ctrl\monitor.php

现在只能通过浏览器访问来开启监控....

http://url/index.php?c=monitor&a=start&m=admin

总之现在是一个很不完善的玩意=_=