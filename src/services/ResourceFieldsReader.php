<?php

namespace benbanfa\raddy\services;

use benbanfa\raddy\fields\BooleanField;
use benbanfa\raddy\fields\DateTimeField;
use benbanfa\raddy\fields\FieldInterface;
use benbanfa\raddy\fields\StringField;
use benbanfa\raddy\ResourceRegistry;
use benbanfa\raddy\resources\ResourceInterface;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;

class ResourceFieldsReader
{
    private $registry;

    public function __construct(ResourceRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * 通过 resource 设置的 model class 以及 getFields 方法的返回
     * 设置每一个要显示的字段的 field
     *
     * 返回的值应当类似于
     *
     * [
     *     'field' => FieldInterface,
     *     'field2' => [
     *         'field2_1' => FieldInterface,
     *     ],
     * ]
     * 这样的多维数组结构
     *
     * @param ResourceInterface $resource         资源对象
     * @param bool              $isNameFieldsOnly 只返回 nameFields 提到的
     */
    public function read(ResourceInterface $resource, bool $isNameFieldsOnly = false): array
    {
        $modelClass = $resource->getModelClass();

        // 如果是AR类，从数据表分析字段
        $columns = [];
        if (is_subclass_of($modelClass, ActiveRecord::class)) {
            $columns = $modelClass::getTableSchema()->columns;
        }

        $fields = $resource->getFields();
        $nameFields = $resource->getNameFields();

        // 先去掉不用返回的字段
        if ($isNameFieldsOnly) {
            foreach ($fields as $name => $field) {
                if (!in_array($name, $nameFields)) {
                    unset($fields[$name]);
                }
            }
        }

        // 通过表结构自动设置字段类型
        foreach ($columns as $name => $column) {
            if (isset($fields[$name])) { // 用户设置过的直接跳过；设置为 null 的下面自动补类型，主要是为了排序
                if ($fields[$name] instanceof FieldInterface) {
                    $fields[$name]->init($column);
                }

                continue;
            }

            if ($isNameFieldsOnly && !in_array($name, $nameFields)) {
                continue;
            }

            // 用户没有设置过的，通过表字段名或者字段数据类型做猜测
            if (preg_match('/^(\w+)(?:_i|I)d$/', $name, $matches)) { // 通过字段名带 id 猜测需要关联其他 resource
                $relatedResourceId = $matches[1];
                $getter = sprintf('get%s', ucfirst($relatedResourceId));
                if (!method_exists($modelClass, $getter)) { // model 上无对应的 getter 方法，处理成普通文本字段
                    $fields[$name] = new StringField();

                    continue;
                }

                $model = new $modelClass();
                $query = $model->$getter();
                if (!$query instanceof ActiveQueryInterface) { // getter 返回的不是多对一的 ActiveQuery，处理成普通文本字段
                    $fields[$name] = new StringField();

                    continue;
                }
                $relatedModelClass = $query->modelClass;
                $actualRelatedResourceId = $this->registry->getResourceIdByModelClass($relatedModelClass);
                if (null === $actualRelatedResourceId) { // 通过 getter 找到的关联 model 并没有在 registry 注册为 resource，也处理成普通文本
                    $fields[$name] = new StringField();

                    continue;
                }

                $relatedResource = $this->registry->getResource($actualRelatedResourceId);

                $fields[$relatedResourceId] = array_merge($this->read($relatedResource, true), $fields[$relatedResourceId] ?? []);
            }

            if (in_array($column->type, ['datetime', 'timestamp'])) {
                $fields[$name] = new DateTimeField();

                continue;
            }

            if ('tinyint' === $column->type && 1 === $column->size) {
                $fields[$name] = new BooleanField();

                continue;
            }

            $fields[$name] = new StringField();
        }

        // nameFields 里提到的字段，是肯定需要的，最后处理，默认都处理成 string field
        foreach ($nameFields as $nameField) {
            if (!isset($fields[$nameField])) {
                $fields[$nameField] = new StringField();
            }
        }

        return array_filter($fields); // 用户设置为 false 的去掉
    }
}
