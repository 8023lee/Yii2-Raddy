<?php

namespace benbanfa\raddy\resources;

use benbanfa\raddy\repository\Repository;
use benbanfa\raddy\repository\RepositoryInterface;
use yii\base\Model;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\web\Request;

abstract class AbstractResource implements ResourceInterface
{
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function canCreate(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getNameFields(): array
    {
        return ['id'];
    }

    /**
     * {@inheritdoc}
     */
    public function getRowActions(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getListActions(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFields(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getSummableFields(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function onFieldsRead(array $fields): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onFormRendering(array $fields): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onListFiltering(array $fields, ActiveQueryInterface $query): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onModelSaveExceptionThrew(ActiveRecord $model, \Throwable $ex): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getModelIdentifier(): string
    {
        return 'id';
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthRules(): array
    {
        return [];
    }

    /**
     * 返回 model 的处理实例
     *
     * @return Repository
     */
    public function getRepository(): RepositoryInterface
    {
        return new Repository();
    }

    /**
     * 获取常见动作的快捷方法
     *
     * @param string $key 动作名称
     *
     * @return array
     *
     * @throw OutOfRangeException
     */
    protected function getCommonRowAction(string $key): ?array
    {
        static $actions;
        if (null === $actions) {
            $actions = [
                'delete' => [
                    'action' => function ($model) {
                        $model->delete();
                    },
                    'label' => '删除',
                    'class' => 'danger',
                ],
            ];
        }

        if (isset($actions[$key])) {
            return $actions[$key];
        }

        throw new \OutOfRangeException(sprintf('找不到叫 %s 的动作', $key));
    }

    /**
     * 设置字段可编辑的快捷方法
     *
     * @param array $fields      所有字段
     * @param array $fieldNames  要设置可编辑的字段名
     * @param bool  $isReadOnly 是否可输入
     */
    protected function setReadOnly(array $fields, array $fieldNames, bool $isReadOnly): void
    {
        foreach ($fieldNames as $name) {
            $fields[$name]->setReadOnly($isReadOnly);
        }
    }

    /**
     * 取消可筛选字段的快捷方法
     *
     * @param array $fields      所有字段
     * @param array $fieldNames  要取消筛选的字段名
     */
    protected function removeFilters(array $fields, array $fieldNames)
    {
        foreach ($fieldNames as $name) {
            $fields[$name]->setFilter(null);
        }
    }

    /**
     * 初始化新增表单 Model
     *
     * @param ActiveRecord $model
     * @param Request $request
     */
    public function initNewModel(Model $model, Request $request): void
    {
        $this->initModel($model, $request->get());
    }

    /**
     * 初始化编辑表单 Model
     *
     * @param ActiveRecord $model
     * @param Request $request
     */
    public function initEditModel(Model $model, Request $request): void
    {
        $this->initModel($model, $request->get());
    }

    private function initModel(Model $model, array $config)
    {
        foreach ($config as $key => $value) {
            if ($model->canSetProperty($key)) {
                $model->$key = $value;
            }
        }
    }
}
