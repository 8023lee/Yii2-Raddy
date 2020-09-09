<?php

namespace benbanfa\raddy\services;

use benbanfa\raddy\Filter;
use benbanfa\raddy\ResourceRegistry;
use benbanfa\raddy\resources\ResourceInterface;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\helpers\Url;

class DetailAttributesOptionMaker
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
    public function make(array $config, ResourceInterface $resource, Filter $search): array
    {
        $result = [];
        $this->doMake($config, $resource, $search, $result);

        $actions = $resource->getActions();
        if (empty($actions)) {
            return $result;
        }

        $actionBar = [
            'class' => ActionColumn::className(),
            'template' => sprintf('<div class="btn-group">{%s}</div>', implode('}{', array_keys($actions))),
        ];

        foreach ($actions as $actionId => $action) {
            $actionBar['buttons'][$actionId] = function ($url, $model, $key) use ($action, $actionId, $resource) {
                $isHidden = $action['isHidden'] ?? null;
                if (is_callable($isHidden) && $isHidden($model)) {
                    return;
                }

                $label = is_callable($action['label']) ? $action['label']($model) : $action['label'];
                $class = 'primary';
                if (isset($action['class'])) {
                    $class = is_callable($action['class']) ? $action['class']($model) : $action['class'];
                }
                if (is_callable($action['action'])) {
                    return Html::button($label, [
                        'class' => sprintf('btn btn-sm btn-%s btn-action', $class),
                        'type' => 'submit',
                        'formaction' => Url::to([
                            'resource/action',
                            'resourceId' => $resource->getId(),
                            'actionId' => $actionId,
                            'id' => $key,
                        ]),
                    ]);
                }

                return Html::a($label, $action['action'], ['class' => 'btn btn-primary btn-sm btn-layer']);
            };
        }

        $result[] = $actionBar;

        return $result;
    }
}
