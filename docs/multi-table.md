= 多表操作 =

=== 背景/问题 ===
表单提交时经常涉及到关联的两张表的修改

=== 解决方案 ===
raddy提供了一个提交表单时处理模型的接口`benbanfa\raddy\repository\RepositoryInterface`，并且已经有一个实现接口的类`benbanfa\raddy\repository\Repository`，可以通过继承`Repository`并重写`save`方法就可以实现提交表单时的自定义动作。

=== 其他解决方案及对比 ===
暂无
=== 总结 ===
raddy的多表操作可以覆盖大多数的表单需求
=== 使用中遇到的问题 ===
1、`benbanfa\raddy\controllers\ResourceController`中`responseForForm`方法没有处理表单验证错误时的情况，所以在提交表单时会遇到如果有表单验证错误，页面没有提示信息。
