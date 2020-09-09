<?php

namespace benbanfa\raddy\services;

use benbanfa\raddy\Filter;
use benbanfa\raddy\ResourceRegistry;
use benbanfa\raddy\resources\ResourceInterface;
use Yii;
use yii\grid\ActionColumn;
use yii\grid\DataColumn;
use yii\helpers\Html;
use yii\helpers\Url;

class GridColumnsOptionMaker
{
    private $registry;

    public function __construct(ResourceRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * 通过 ['field' => FieldInterface] 形式的配置，得到
     * Yii2 GridView 能识别的 option
     *
     * @param array $config 最终配置
     * @param ResourceInterface $resource 相关 resource
     */
    public function make(array $config, ResourceInterface $resource, Filter $search = null): array
    {
        $isInGrid = null !== $search;

        $result = [];
        $this->doMake($config, $resource, $search, $result);

        if (!$isInGrid) {
            return $result;
        }

        $actions = $resource->getRowActions();
        if (empty($actions)) {
            return $result;
        }

        $actionBar = [
            'class' => ActionColumn::className(),
            'template' => sprintf('<div class="btn-group" style="display: flex">{%s}</div>', implode('}{', array_keys($actions))),
        ];

        $user = Yii::$app->user;
        foreach ($actions as $actionId => $action) {
            $actionBar['buttons'][$actionId] = function ($url, $model, $key) use ($action, $actionId, $resource, $user) {
                $isHidden = $action['isHidden'] ?? null;
                if (is_callable($isHidden) && $isHidden($model)) {
                    return;
                }

                if (!empty($action['roles'])  ) {
                    $roleParams = $action['roleParams'] ?? [];
                    if (is_callable($roleParams)) {
                        $roleParams = $roleParams($model);
                    }

                    if (!$this->hasRoles($user, (array) $action['roles'], $roleParams)) {
                        return;
                    }
                }

                $label = $action['label'] ?? $actionId;
                if (is_callable($label)) {
                    $label = $label($model);
                }

                $class = $action['class'] ?? 'primary';
                if (is_callable($class)) {
                    $class = $class($model);
                }

                if (isset($action['action']) && is_callable($action['action'])) {
                    return Html::button($label, [
                        'class' => sprintf('btn btn-sm btn-%s btn-action', $class),
                        'type' => 'submit',
                        'formaction' => Url::to([
                            'resource/row-action',
                            'resourceId' => $resource->getId(),
                            'actionId' => $actionId,
                            'id' => $key,
                        ]),
                    ]);
                }

                $url = $action['url'] ?? null;
                if (is_callable($url)) {
                    $url = $url($model);
                }

                if (!empty($url)) {
                    $options = ['class' => sprintf('btn btn-%s btn-sm', $class)];
                    if (!empty($action['hasLayer'])) {
                        $options['class'] .= ' btn-layer';
                    } else {
                        $options['target'] = $action['target'] ?: '_self';
                    }

                    return Html::a($label, $url, $options);
                }
            };
        }

        $result[] = $actionBar;

        return $result;
    }

    private function doMake(
        array $config,
        ResourceInterface $resource,
        Filter $search = null,
        array &$result,
        string $parent = null
    ): void {
        $isInGrid = null !== $search;

        foreach ($config as $name => $field) {
            $flattedName = $name;
            if (null !== $parent) {
                $flattedName = sprintf('%s.%s', $parent, $name);
            }

            if (is_array($field)) { // 关联模型
                $method = sprintf('get%s', ucfirst($name));
                $class = $resource->getModelClass();
                if (!method_exists($class, $method)) {
                    continue;
                }

                $model = new $class();
                $query = $model->$method();
                $relatedResourceId = $this->registry->getResourceIdByModelClass($query->modelClass);
                if (null === $relatedResourceId) {
                    continue;
                }

                $this->doMake($field, $this->registry->getResource($relatedResourceId), $search, $result, $flattedName, $isInGrid);

                continue;
            }

            $result[$flattedName] = [
                'attribute' => $flattedName,
            ];

            if ($isInGrid) {
                $result[$flattedName]['class'] = DataColumn::className();

                $filter = $field->getFilter();
                $result[$flattedName]['filter'] = null === $filter ? false : $filter->getDataColumnConfig($search, $flattedName, $resource);

                if (in_array($name, $resource->getSummableFields())) {
                    $sumField = $name;
                    if (!preg_match('/^\w+\.\w+$/', $name)) {
                        $sumField = sprintf(
                            '%s.%s',
                            $resource->getModelClass()::tableName(),
                            $name
                        );
                    }
                    $result[$flattedName]['footer'] = $field->format($search->getQuery()->sum($sumField));
                }
            }

            $result[$flattedName]['format'] = 'html'; // 默认
            $format = $field->getFormat();
            if (null !== $format) {
                $result[$flattedName]['format'] = $format;

                continue; // 因 format 和 content 都是控制内容的，所以设置了 format 就不需要再管 content
            }

            $result[$flattedName][$isInGrid ? 'content' : 'value'] = function ($model) use ($flattedName, $resource, $field) {
                $names = explode('.', $flattedName);
                $name = array_pop($names);
                foreach ($names as $attr) {
                    $model = $model->$attr;
                    if (null === $model) {
                        return '(未设置)';
                    }
                }

                if (null === $model->$name) {
                    return '(未设置)';
                }

                $generator = $field->getContentGenerator();
                if (null !== $generator) {
                    return $generator($name, $model);
                }

                $content = $model->$name;
                if ($resource->getNameFields()[0] !== $name) {
                    return $content;
                }

                return Html::a($content, [
                    'resource/edit',
                    'resourceId' => $resource->getId(),
                    'id' => $model->{$resource->getModelIdentifier()},
                    'successUrl' => Yii::$app->request->url,
                ]);
            };
        }
    }

    private function hasRoles($user, $roles, $roleParams = []): bool
    {
        foreach ($roles as $role) {
            if ($user->can($role, $roleParams)) {
                return true;
            }
        }

        return false;
    }
}
