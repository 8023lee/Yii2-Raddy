字段
====

概念
----

在[资源](resources.md)章节已经对字段的概念有了简单的介绍，即字段用于定义数据模型上的某个字段应该如何处理，而『处理』指列表中的显示，表单中的显示，列表中的筛选，表单中的输入框如何显示。

所有的内置字段类都在 `\raddy\modules\admin\field` 命名空间下。

目前 raddy 通过以下规则，来根据数据库表字段类型自动设置字段类型：

| 数据库字段类型 | raddy 字段类型 |
|---|---|
| datetime | DateTimeField |
| timestamp | |
| tinyint(1) | BooleanField |
| 其他 | StringField |

内置的字段
----------

### 布尔 BooleanField

```
public function getFields(): array
{
    return [
        'isActive' => new BooleanField(),
    ]
}
```

布尔字段的数据会显示成『是』或者『否』

### 时间日期 DateTimeField

```
public function getFields(): array
{
    return [
        'createTime' => new DateTimeField('php:Y-m-d H:i:s'),
    ];
}
```

DateTimeField 的构造函数接受一个参数 `$format`，即时间格式，内容为 Yii2 DataColumn 支持的时间格式类型，允许的格式有 `short`, `medium`, `long` 或 `full`，以及所有 php 函数 `date` 所支持的写法，但要加 `php:` 前缀，比如 `php:Y-m-d H:i:s`；可省略不写，默认为 `short`。

提示：如果指定的格式为非 `date` 函数的写法，安装 php 的 intl 扩展可让时间的显示更符合指定地区的阅读习惯。

### 枚举 EnumField

```
public function getFields(): array
{
    return [
        'chargeType' => new EnumField([1 => '快捷', 2 => '网银', 3 => '转账']),
    ];
}
```

EnumField 的构造函数接受一个数组参数，key 是数据库里存储的数据， value 是对应要显示的内容。此参数也可以忽略，默认为 null，这种情况只对筛选有影响，具体的作用在[筛选](filters.md)一章再说。

### 金额 MoneyField

```
public function getFields(): array
{
    return [
        'balance' => new MoneyField('cent'),
    ];
}
```

MoneyField 的构造函数接受一个字符串参数，来表示数据库里的数据的单位，可以不填，默认为 `cent`，即以分为单位的整型。另外也接受 `yuan`，则数据库应该为两位小数的浮点类型。

### 字符串 StringField

```
public function getFields(): array
{
    return [
        'name' => new StringField(),
    ];
}
```

StringField 是最简单的字段类型，对原数据直接显示

配置字段筛选器
--------------

所有的字段都支持配置字段筛选器：

```
use raddy/modules/admin/filter/StringFilter;

$field = new StringField();
$field->setFilter(new StringFilter());
```

Raddy 的字段类型都会自己设置默认的筛选器：

| 字段类型 | 筛选器 | 样式 |
|---|---|---|
| BooleanField | Yii2 内置 | 下拉菜单式，有是否两个选项 |
| DateTimeField | DateTimeFilter | 带日期选择的控件 |
| EnumField | OptionsFilter | 下拉多选菜单式 |
| 其他 | StringFilter | 文本框 |

Raddy 内置的筛选器及其用法，见[筛选器](filters.md)章节。

可以通过设置筛选器为 null 的方式去掉某个字段的筛选：

```
$field->setFilter(null);
```

配置表单输入框
--------------

默认情况下，所有的字段都是不可编辑的，如果想在编辑表单设置某个字段为可编辑状态，可以通过下面方式完成：

```
use raddy/modules/admin/input/TextType;

$field = new StringField();
$field->setInputType(new TextType());
```

你也可以只用告诉 raddy 想设置某个字段可编辑，让字段类自动帮你设置合适的输入类型：

```
$field->setInputable(true);
```

Raddy 设置的默认表单输入类型与字段类型的对应关系如下：

| 字段类型 | 输入类型 | 注释 |
|---|---|---|
| BooleanField | SelectType | 下拉菜单样式，可选是和否 |
| EnumField | | 根据构造函数传入的 `$values` 参数生成的下拉菜单 |
| DateTimeField | TextType | 文本框 |
| MoneyField | | |
| StringField | | |

Raddy 内置的输入类型及其用法，可见[输入类型](inputs.md)。

配置所有表格显示的内容
----------------------

除了比如 BooleanField 这种 getFormat 方法有返回的字段，默认都显示数据库原始内容（或者显示带链接的数据库原始内容，在 resources 一章已经聊过）。如果默认的行为不满足需求，可以通过 `setContentGenerator` 方法来设置如何显示，比如使用 `number_format` 来格式化浮点数的显示：

```
use yii\base\Model;

$field->setContentGenerator(function (string $name, Model $model) {
    return number_format($model->$name, 2);
});
```

创建自定义字段类型
------------------

如果内置的字段都不满足需求，可以通过创建自定义的字段类来满足需求。

字段类需要实现 `raddy\modules\admin\field\FieldInterface` 接口，包含以下方法：

### `getFormat(): string` 或者 `getContentGenerator(): ?Closure`

这两个方法都跟显示有关系，比如设置某个字段类的 `getFormat` 返回的值是 `boolean`，则只要数据的值等于 `true`，就会显示『是』。

或者通过 `getContentGenerator` 方法来返回一个匿名函数，此函数接受两个参数 `$name` 和 `$model`，并返回要显示的 HTML 代码，比如 EnumField 就是通过此方法来将值转化成要显示的文字。

但一般来说 `getContentGenerator` 并不需要有单独的实现（直接用 `AbstractField` 默认提供的方法就好），它只是配合上一节提到的 `setContentGenerator` 方法一起使用。

`getFormat` 的优先级高于 `getContentGenerator`，即如果 `getFormat` 有返回，则 `getContentGenerator` 不起作用。

`getFormat` 比 `getContentGenerator` 要多一点作用，`getFormat` 也可能影响到筛选的样式，比如 `getFormat` 返回 `boolean`，筛选框会变成有『是』或者『否』的下拉菜单。

`getFormat` 可返回任何 Yii2 DataColumn 支持的 format 选项。

### `preDataValidation(string $name, Model $model): void`

数据模型在校验之前，如果要对某个字段数据做处理，可以通过实现此方法来满足需求。

### `handlePostData(string $name, Model $model): void`

当表单成功校验之后，数据模型保存之前，你需要对某个字段做一些处理，则可以通过实现此方法来满足需求。

### `wrapInputHtml(string $html, string $name, Model $model): string`

如果在表单输入框（或者数据，如果此字段不能被编辑的话）的 HTML 生成完毕后，你打算在 HTML 前后添加更多的 HTML 代码，可以通过实现此方法来满足需求。

### `setInputable(bool $isInputable): FieldInterface`

可以通过此方法，根据参数来自动设置输入类型。
