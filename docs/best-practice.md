最佳实践
========

美化 raddy 的访问地址
---------------------

默认配置下，某个 raddy 资源的访问地址为 `/raddy/resource/index?resourceId=xxx`，地址比较长，也不美观；编辑/新增页的地址也有同样的问题。

我们可以通过 urlManager 将其访问路径缩短，使其看上去更加精炼（比如 `raddy/xxx` 即 index 页地址）

```
// backend/config/main.php

return [
    'component' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'raddy/<resourceId>' => 'raddy/resource/index',
                'raddy/<resourceId>/<id>/edit' => 'raddy/resource/edit',
                'raddy/<resourceId>/new' => 'raddy/resource/new',
            ],
        ],
    ],
];
```

继承抽象类减少代码量
--------------------

无论是资源类，还是自定义输入类型类和筛选类，都需要实现对应的接口。

raddy 为资源类、输入类型、筛选类提供了抽象类，这些抽象类都对其对应的接口，提供了合理的默认实现。继承这些抽象类，可以不再用实现每一个接口。此文档里所有的资源实例都是继承的抽象资源类，此处不再举例。

---

虽然在资源类的 `getFields` 方法里，我们已经可以通过配置字段对象，并设置其筛选和表单输入，但为了最大享受到 raddy 为我们做的自动配置工作，我们可以在指定的事件里对某些配置进行调整，而不是在 `getFields` 方法里从头进行配置。

修改筛选行为
------------

资源类的 `onListFiltering` 方法负责定义在列表筛选之前对生成的字段的进一步处理。如果需要修改字段的筛选行为，建议在此方法里实现：

```
<?php

namespace backend\resources;

// use ...

class ChargeResource extends AbstractResource
{
    // public function ...

    /**
     * {@inheritdoc}
     */
    public function onListFiltering(array $fields, ActiveQueryInterface $query): void
    {
        $fields['errorMessage']->setFilter(new StringFilter(true)); // 改成支持模糊查询

        // 去掉筛选
        $fields['legalName']->setFilter(null);
        $fields['accountNumber']->setFilter(null);
        $fields['idCertNumber']->setFilter(null);

        // 以下代码等同于上面代码
        $this->removeFilters($fields, ['legalName', 'accountNumber', 'idCertNumber']);
    }
}
```

修改表单输入
------------

资源类的 `onFormRendering` 方法负责定义在表单渲染之前的处理。如果需要修改表单输入的类型，建议在此方法里实现。

另外 AbstractResource 还提供了 `setFieldsInputable` 快捷方式，可以进一步减少代码量：

```
<?php

namespace backend\resources;

// use ...

class BankResource extends AbstractResource
{
    // public function ...

    /**
     * {@inheritdoc}
     */
    public function onFormRendering(array $fields): void
    {
        // 设置以下字段可以输入
        $fields['id']->setInputable(true);
        $fields['bankName']->setInputable(true);
        $fields['shortName']->setInputable(true);
        $fields['service']->setInputable(true);

        // 以下代码完全等同于上述代码
        $this->setFieldsInputable($fields, ['id', 'bankName', 'shortName', 'service']);
    }
}
```

修改内容展示
------------

资源类的 `onFieldsRead` 方法负责定义在所有字段选项配置好了之后的处理。如果你想针对某些数据的默认显示做调整，建议在这个事件里处理。
