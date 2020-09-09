筛选器
======

所有的筛选器都在 `raddy\modules\admin\filter` 命名空间下

内置筛选器
----------

### NumberFilter

```
$field->setFilter(new NumberFilter());
```

使用的效果为改成范围筛选

### MoneyFilter

```
$field->setFilter(new MoneyFilter('cent'));
```

MoneyFilter 继承自 NumberFilter，区别为构造函数带一个可选参数。

此参数为金额数据在库里的单位，默认为 `cent` 即分。筛选器会根据金额单位，可能会将用户的输入做一次转化后再筛选

### DateTimeFilter

```
$field->setFilter(new DateTimeFilter());
```

直接用就行，无其他任何选项

### OptionsFilter

```
$field->setFilter(new OptionsFilter([0 => 'A', 1 => 'B', 2 => 'C']));
```

构造函数接受一个参数 `$options`。此参数可省略不写，默认为 `null`。当为 `null` 时，可以让此筛选器尝试自动获取下拉选项数据。

自动获取下拉选项数据的规则为：获取当前字段在表里出现过的所有值。

### StringFilter

```
$field->setFilter(new StringField(true));
```

构造函数接受一个参数 `$isFuzzyFindEnabled`。此参数的含义为，是否允许字符串模糊匹配。默认为 `false`

自定义筛选器
------------

自定义一个筛选器，需要实现 `raddy\modules\admin\filter\FilterInterface` 接口，包含以下两个方法：

```
createSearchCondition(string $fieldName, $value): array
```

此方法用于返回搜索条件，形式跟 `yii\db\Query` 所支持的筛选条件一致

```
getDataColumnConfig(Filter $model, string $name, ResourceInterface $resource)
```

此方法用于返回 Yii2 DataColumn 类所支持的 filter 选项。
