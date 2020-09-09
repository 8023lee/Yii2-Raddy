输入类型
========

内置输入类型
------------

### SelectType

```
$field->setInputType(new SelectType([0 => 'A', 1 => 'B', 2 => 'C']));
```

构造函数接受一个参数 `$options`，但可以不用传，默认为 `null`。

如果 `$options` 为 `null`，其作用是让 SelectType 自动获取选项，可使用的情景为：

当字段名为 `xxId` 或者 `xx_id` 时，会自动查找是否存在名为 `xx` 的资源对象，如果有会自动从 xx 对应的表上获取所有的数据，并且以 xx 的 id 为 key，xx 的资源对象 `getNameFields` 返回的数组的第一个元素指定的字段为值，生成下拉菜单。

### TextArea

```
$field->setInputType(new TextAreaType(4));
```

构造函数接受一个参数 `$rows`，即文本框行数，可以忽略，默认为 4。

### TextType

```
$field->setInputType(new TextType());
```

直接用就行无其他选项。
