<?php

namespace benbanfa\raddy\resources;

use benbanfa\raddy\repository\RepositoryInterface;
use yii\base\Model;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\web\Request;

interface ResourceInterface
{
    /**
     * 定义资源的所有字段
     */
    public function getFields(): array;

    /**
     * 获取 model 的 class
     */
    public function getModelClass(): string;

    /**
     * 此方法用来定义被关联的资源的显示字段
     */
    public function getNameFields(): array;

    /**
     * 每行记录包含行为 id 和对应要执行的代码，或者 url
     * 如果返回 url，则是通过打开弹层的方式，加载 url
     */
    public function getRowActions(): array;

    /**
     * 每行记录包含行为 id 和对应要执行的代码，或者 url
     * 如果返回 url，则是通过打开弹层的方式，加载 url
     */
    public function getListActions(): array;

    /**
     * 是否可以新建记录
     */
    public function canCreate(): bool;

    /**
     * 得到 id
     */
    public function getId(): string;

    /**
     * 返回可算和的字段
     *
     * @return string
     */
    public function getSummableFields(): array;

    /**
     * 监听字段读取完毕事件，此处可以进一步更改 fields 相关的配置
     *
     * @param array $fields 字段配置
     */
    public function onFieldsRead(array $fields): void;

    /**
     * 监听表单渲染前事件，此处可以进一步更改 fields 表单相关的配置
     *
     * @param array $fields 字段配置
     */
    public function onFormRendering(array $fields): void;

    /**
     * 监听列表筛选前事件，此处可以进一步更改 fields 筛选器相关的配置
     *
     * @param array                $fields 字段配置
     * @param ActiveQueryInterface $query  query
     */
    public function onListFiltering(array $fields, ActiveQueryInterface $query): void;

    /**
     * 当有数据被保存之后要做的事情
     *
     * @param ActiveRecord $model Model
     * @param Throwable    $ex    Exception
     */
    public function onModelSaveExceptionThrew(ActiveRecord $model, \Throwable $ex): void;

    /**
     * 返回 model 的唯一标识符字段，一般来说是 id
     */
    public function getModelIdentifier(): string;

    /**
     * 返回 model 的处理实例
     */
    public function getRepository(): RepositoryInterface;

    /**
     * 初始化新增表单 Model
     *
     * @param ActiveRecord $model
     * @param Request $request
     */
    public function initNewModel(Model $model, Request $request): void;

    /**
     * 初始化编辑表单 Model
     *
     * @param ActiveRecord $model
     * @param Request $request
     */
    public function initEditModel(Model $model, Request $request): void;

    /**
     * 获取权限规则
     */
    public function getAuthRules(): array;
}
