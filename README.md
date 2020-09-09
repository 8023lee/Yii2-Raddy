Raddy - Yii2 巡航速度开发
=========================

快速不如巡航速度省油。

设计思路
--------

- 每张数据库表，映射一个业务模型，如订单
- 业务模型上的属性，都对应到确定的属性类型上，如，订单金额是资金属性
- 属性类型有确定的存储和展示格式化规则，如年化收益百分比率可存储为 `0.060000`，展示为 `6%`
- 业务模型上定义至少一个标识属性，如ID
- 业务模型上可定义一个以上关键属性
- 列表视图的列可包含关联模型的关键属性
- 列表视图的所有列，都可以支持检索、排序
- 导出数据时，默认导出关联模型的关键属性，可选导出关联模型的全部属性

文档
----

见 [docs](docs/index.md)。

文档支持 mkdocs 生成在线文档，可通过 mkdocs 工具生成

```
mkdocs build
```

或直接本地浏览器打开

```
mkdocs serve
```