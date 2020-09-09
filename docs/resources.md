资源
====

在 raddy 里，一个 model 可以生成一个带新增或者编辑功能的后台界面，而资源类（resource）则是描述后台功能的类。

创建资源
--------

一般来说，资源的命名为 model 名 + `Resource`，比如[索引页](index.md)看到的 `PlatformResource`。

资源类需要实现 `raddy\modules\admin\resource\ResourceInterface` 接口。

通过实现接口里的 `getModelClass` 方法，可以将此 Resource 绑定某个 model。

资源字段定义
------------

资源可以通过实现其 `getFields` 方法来告诉 raddy 要对 model 的哪一些字段做配置，『配置』包括了显示、筛选、编辑等行为。

Raddy 提供了若干实现了 `raddy\modules\admin\field\FieldInterface` 的字段类，可用来描述 model 某个字段的字段类型，比如对于会员模型的名称（name 字段），可设置为 StringField 类型；充值状态可设置为 EnumField。

Raddy 所有内置的字段类型，可以通过[字段](fields.md)章节查看

[索引页](index.md)的例子并没有显式定义 `getFields` 方法，但也可以工作，那是因为 raddy 具有自动通过 model 的数据库表结构来猜测资源的能力。

但猜测的结果不一定是符合需求的，这种情况，我们就可以通过 `getFields` 方法，对某些字段做类型的定义，跳过 raddy 的自动设置：

```
<?php

namespace backend\resources;

use common\models\account\Account;
use raddy\modules\admin\field\EnumField;
use raddy\modules\admin\field\MoneyField;
use raddy\modules\admin\resource\AbstractResource;

class AccountResource extends AbstractResource
{
    public function getFields(): array
    {
        return [
            'balance' => new MoneyField(),
            'type' => new EnumField(),
        ];
    }

    public function getModelClass(): string
    {
        return Account::class;
    }
}
```

`getFields` 方法里提到的字段，即会按照定义的字段类型处理，而没提到的字段，则依然会通过自动猜测的方式来定义字段类型。

那如果某个 model 里的字段并不想显示怎么办？Raddy 可以通过设置不想显示的字段为 `false` 的方式来处理。

另外，字段并非只能是数据库表里有的字段，而是 model 上所有的字段。比如某些加密字段所对应的实际的字段，在数据库表里没有，我们就可以通过单独指定字段的方式，来让 raddy 知道有这些个字段需要处理。

下面便是一个排除表里的加密字段，而添加 model 上定义的原文字段的例子。

```
<?php

namespace backend\resources;

// use ...
use raddy\modules\admin\field\StringField;

class ChargeResource extends AbstractResource
{
    // public function ...

    public function getFields(): array
    {
        return [
            // ... =>
            'idCertNumberCrypted' => false,
            'accountNumberCrypted' => false,
            'mobileNumberCrypted' => false,
            'legalNameCrypted' => false,
            'legalName' => new StringField(),
            'accountNumber' => new StringField(),
            'idCertNumber' => new StringField(),
            'mobileNumber' => new StringField(),
        ];
    }
}
```

最后，我们会有把比较重要的字段尽量显示在前面的需求。在 `getFields` 方法里出现的字段，都是显示在所有字段最前面的，并且它们是按指定的顺序显示。如果你只是为了调整顺序，而并不想自己指定字段类型，你可以指定字段为 `null` 类型，raddy 依然会自动补充其字段类型。

```
<?php

namespace backend\resources;

// use ...

class PlatformResource extends AbstractResource
{
    // public function ...

    public function getFields(): array
    {
        return [
            'platformName' => null,
            'platformCode' => null,
        ];
    }
}
```

资源标识的定义
--------------

如果有一个用户数据模型，有姓名字段 name，那么我们可以说，name 是用户的标识。注意我们这里说的标识，是一种比 id 更易于识别的标识。

当然，如果用户还有身份证这个字段，身份证也可以作为用户的标识，甚至身份证和名字一起可以作为标识。

Resource 的 `getNameFields` 方法，就是用来定义相关的 model 的『标识』到底是哪些字段：

```
<?php

namespace backend\resources;

// use ...

class PlatformResource extends AbstractResource
{
    // public function ...

    public function getNameFields(): array
    {
        return ['platformName'];
    }
}
```

但定义资源标识有什么用呢？举一个例子，如果用户有关联的 model 叫 Platform，那么在显示 user 表里的 platformId 时，就不是只显示毫无识别性的 platformId，而是还会显示 Platform 的名称，并且名称默认是带该 platform 资源的链接的；另外，如果后台编辑某个用户的 platformId 时，如果设置 platformId 的表单类型是下拉框，则 raddy 可通过 `getNameFields` 方法来设置下拉框里的项应该显示什么。

操作
----

Raddy 可以针对某一条记录做一些操作，最常见的就是删除了。假设 platform 有一个字段表示平台是否关闭，通过定义操作可以方便用户快速切换是否关闭状态：

```
<?php

namespace backend\resources;

// use ...

class PlatformResource extends AbstractResource
{
    // public function ...

    public function getRowActions(): array
    {
        return [
            'toggleDisabled' => [
                'action' => function ($model) {
                    $model->isDisabled = !$model->isDisabled;
                    $model->save();
                },
                'label' => function ($model) {
                    return sprintf('%s交易', $model->isDisabled ? '开启' : '关闭');
                },
                'class' => function ($model) {
                    return $model->isDisabled ? 'primary' : 'danger';
                },
            ],
        ];
    }
}
```

可以看到 `getRowActions` 可以定义一个操作的行为（action），名称（label），以及按钮的类型（class）。

例子中操作的三个属性，都是通过一个匿名函数来定义的。实际上，只要是一个 callable 都是可以的。

另外，如果 label 和 class 是一个固定的值，可直接赋值一个字符串。

除了 `action`，你也可以使用 `url` 属性，用来指定新窗口链接地址，`url` 也即可以是一个字符串，也可以是一个 callback。当 `action` 和 `url` 同时存在时，`action` 的优先级会高于 `url`。

当指定 `url` 时，你还可以添加属性 `'hasLayer' => true` 来控制是否使用弹出层的方式来代替新窗口打开链接。

`class` 会影响按钮的 HTML class，可以赋予的值有 `default` `primary` `success` `warning` `danger` `info`，除了影响按钮的背景色，`danger` 和 `warning` 会给用户以『是否确认操作』的提示

最后，因为删除是很常见的操作，所以 raddy 里自带删除 action，如果要使用，只需要写以下代码就行了

```
<?php

namespace backend\resources;

// use ...

class PlatformResource extends AbstractResource
{
    // public function ...

    public function getRowActions(): array
    {
        return [
            'delete' => $this->getCommonAction('delete'),
        ];
    }
}
```


设置可新增
----------

设置了可以新增记录后，则生成的后台有『新增』入口。

定义方式也很简单：

```
<?php

namespace backend\resources;

// use ...

class BankChargeRuleResource extends AbstractResource
{
    // public function ...

    public function canCreate(): bool
    {
        return true;
    }
}
```

设置可求和字段
--------------

可通过以下方式，设置某些字段显示总和：

```
<?php

namespace backend\resources;

// use ...

class AccountBalanceLog extends AbstractResource
{
    // public function ...

    public function getSummableFields(): array
    {
        return ['amount'];
    }
}
```

设置其他针对列表的操作
----------------------

比如增加一个导出的功能，我们可以：

```
<?php

namespace backend\resources;

// use ...

class ChargeResource extends AbstractResource
{
    // public function ...

    public function getListActions(): array
    {
        return [
            'export' => [
                'label' => '导出',
                'class' => 'primary',
                'onQuerying' => function ($resource, $request) {
                    // 在筛选之前执行的代码

                    return $response; // 不同于 onQueried，onQuerying 可不返回 $response；如果返回，则不再往下执行筛选操作。
                },
                'onQueried' => function ($resource, $dataProvider, $fields) {
                    // 在筛选之后执行的代码
                    // ...
                    return $response;
                },
            ],
        ];
    }
}
```

除了 `action` 指定的匿名函数的参数和返回值有所不同外，使用方式与『操作』一节提到的用法差不多。

列表操作也可以直接设置 `url` 参数，来控制列表按钮跳转到哪里去，还可以通过 `hasLayout` 参数来控制是跳转到 URL 还是直接通过弹层加载 URL 对应的页面。

事件
----

我们可以通过 `getFields` 方法自己定义字段，也可以让 raddy 自动帮我们生成。但更多的时候，我们可能只是想在自动生成的结果上，做一些微调，事件便是用来做微调的好地方。

目前 raddy 支持『字段加载完成』，『筛选前』，『生成表单前』三个事件，分别对应 resource 的三个方法：

```
ResourceInterface::onFieldsRead(array $fields);
```
```
ResourceInterface::onListFiltering(array $fields, ActiveQueryInterface $query);
```
```
ResourceInterface::onFormRendering(array $fields);
```

至于什么时候使用这些事件，[最佳实践篇章](best-practice.md)再说
