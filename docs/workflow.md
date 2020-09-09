运行流程
========

当访问一个资源的列表时，`benbanfa\raddy\controllers\ResourceController` 会被创建，并开始执行以下任务

1. 通过 URL 里指定的 resourceId，得到对应的 resource 对象，resource 对象是总配置，它告诉了 raddy 具体如何展现它代表的资源
2. 先看 resource 对象上是否配置了针对列表的 `onQuerying` 事件处理，如果有就执行它，并且如果其返回 response 对象，就返回此 response 对象并停止往下执行代码
3. 通过 resource 对像的 `getModelClass` 方法可获得 resource 所代表的对象类，并通过它创建 ActiveQuery
4. `benbanfa\raddy\services\ResourceFieldsReader` 通过 resource 对象的 `getFields` 方法返回的信息，以及结合上一步获取的数据类，分析或者读取出数据有什么字段，每个字段如何显示、筛选等信息
5. `benbanfa\raddy\services\JoinQueryHandler` 通过上面步骤得到的 ActiveQuery 对象以及字段信息，分析并自动处理好要联查的表
6. 通过 resource 对 `onListFiltering` 事件的处理，对筛选项做一些指定的处理，以及对 ActiveQuery 做一些指定的预处理，比如过滤掉当前用户无法查看的数据
7. `benbanfa\raddy\Filter` 通过生成的字段信息以及 ActiveQuery 对象，执行筛选操作（即给 ActiveQuery 添加更多的查询条件）
8. 创建 ActiveDataProvider 对象，并把处理好的 ActiveQuery 对象赋值给它
9. `benbanfa\raddy\services\GridColumnsOptionMaker` 通过字段信息和 resource 对象上的配置，生成 Yii2 GridView 对象支持的列选项
10. GridView 结合列选项渲染出最终的列表页面

而访问单个的资源时，上述步骤的 1 ~ 5 其实都一样，后面的步骤换成了：

1. 通过 URL 里面的参数，结合 ActiveQuery，找到一条记录
2. 通过 resource 对象对 `onFormRendering` 事件的处理，对表单相关选项做一些修改，比如设置某些字段只读，或者把单行文本框改成多行文本框等
3. 根据是否是 POST 访问，来判断是处理表单提交，还是处理表单显示，如果是提交，则尝试将提交参数绑定数据对象并保存
4. 如果是显示表单，则依然通过 `benbanfa\raddy\services\GridColumnsOptionMaker` 创建用于 Yii2 DetailView 的选项
5. DetailView 结合选项显示出最终的表单页面
