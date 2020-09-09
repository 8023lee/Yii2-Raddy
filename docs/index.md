Raddy
=====

一个 Yii2 快速开发工具

安装
----

```
composer install raddy/raddy:dev-master
```

快速入门
--------

假设我们有一个 Model 叫 `Platform`，我们将为此创建一个具有 CRUD 功能的后台功能。在 raddy 的帮助下，我们只需进行以下步骤：

### 配置 raddy

```
// backend/config/main.php
// ...
$config = [
    // ...
    'modules' => [
        'raddy' => [
            'class' => \raddy\modules\admin\Module::class,
            'mainNavWidget' => \backend\widgets\MainNavWidget::class,
            'subNavWidget' => \backend\widgets\SubNavWidget::class,
        ],
    ],
    'container' => [
        'singletons' => [
            \raddy\modules\admin\ResourceRegistry::class => function () {
                return new \raddy\modules\admin\ResourceRegistry([
                    'resources' => [
                        'platform' => \backend\resources\PlatformResource::class,
                    ],
                ]);
            },
        ],
    ],
];

// ...
```

### 创建负责菜单显示的类

以上配置提到以下两个文件，是需要我们创建的

```
'mainNavWidget' => \backend\widgets\MainNavWidget::class,
'subNavWidget' => \backend\widgets\SubNavWidget::class,
```

这两个文件只是用于创建 raddy 后台管理界面的菜单，实现 run 方法并返回 HTML 代码即可

```
<?php

namespace backend\widgets;

use dmstr\widgets\Menu;
use Yii;
use yii\base\Widget;

class SubNavWidget extends Widget
{
    public function run()
    {
        return Menu::widget([
            'defaultIconHtml' => '',
            'items' => [
                [
                    'label' => '付款管理',
                    'url' => '#',
                    'items' => [
                        ['label' => '提现申请', 'url' => ['/payout']],
                    ],
                ],
                // more item ...
            ],
        ]);
    }
}
```

### 创建并配置资源类

Raddy 里每个要访问的 Model，关于每个字段如何显示，是否可以编辑等，都需要通过创建一个 Resource 类来配置，比如上面的例子用到的 Resource 类就是 `\backend\resources\PlatformResource::class` 

```
<?php

namespace backend\resources;

use common\models\Platform;
use raddy\modules\admin\resource\AbstractResource;

class PlatformResource extends AbstractResource
{
    public function getModelClass(): string
    {
        return Platform::class;
    }
}
```

### 检验 Raddy 是否正常工作

按上面的步骤创建并配置好后，我们在浏览器里尝试访问后台地址 `/raddy/resource/?resourceId=platform`，正常情况我们就可以看到 raddy 生成的后台界面了。

### 安装可选依赖库

如果资源包含了日期类型的字段，则需要安装 `kartik-v/yii2-date-range` 才能正常使用。

如果需要标记某个字段为金额类型（如何标记请见[字段](fields.md)），则需要安装 `kartik-v/yii2-field-range`。

查看更多信息
------------

- [运行流程](workflow.md)
- [资源](resources.md)
- [字段](fields.md)
- [筛选器](filters.md)
- [输入类型](inputs.md)
- [最佳实践](best-practice.md)
