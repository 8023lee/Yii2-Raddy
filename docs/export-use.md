数据导出
========

数据导出分为快速开发的数据导出及非快速开发的数据导出,均使用push job的形式监控任务的执行过程,可查看[任务管理](job-use.md) ,数据的统计结果使用文件上传的功能保存到raddy_file的数据表中,文件下载使用文件查看功能，见[文件上传](file-use.md)

快速开发数据导出只适用于有Resource中的资源，目前只实现了excel的文件导出，后续会扩展其他的文件格式。

非快速开发数据导出需要在自己定义的job中统计需求的数据，其余与快速开发逻辑相同。


job的配置及页面跳转
---------
- 在自己定义的job中如果需要查看用户,平台及文件名称,可添加ownerId, appId，name属性，配置如下
```
$id = Yii::$app->queue2->push(new ExportJob([
    'userId' => $adminId,
    'appId' => Yii::$app->id,
    'name' => '票据信息导出'.\date('YmdHis').'.xls',
]));
```
- push job后需要跳转到任务管理页面查看该(使用filer过滤)任务的执行过程，并设置5s/次(可修改refreshSeconds的值)自动刷新，结果出来后停止刷新,跳转链接示例如下：
```
url' => Url::to(['/raddy/resource/index', 'resourceId' => 'jobRun', 'refreshSeconds' => 5, 'Filter' => ['jobEntry.id' => $id]]),
```
- 本示例是在ajax中通过获取url进行的跳转，如果要在php中实现跳转，可如下配置：
```
return $this->redirect($url);
```


