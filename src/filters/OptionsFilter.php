<?php

namespace benbanfa\raddy\filters;

use benbanfa\raddy\Filter;
use benbanfa\raddy\resources\ResourceInterface;
use yii\helpers\Html;

class OptionsFilter extends AbstractFilter
{
    private $options;

    public function __construct(array $options = null)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataColumnConfig(Filter $model, string $name, ResourceInterface $resource)
    {
        if (null === $this->options) {
            $nameParts = explode('.', $name);
            $lastPart = end($nameParts);
            $this->options = $resource->getModelClass()::find()->select($lastPart)->distinct()->indexBy($lastPart)->column();
        }

        return Html::activeDropDownList($model, $name, $this->options, [
            'multiple' => true,
            'class' => 'form-control',
        ]);
    }

    /**
     * 获取选项
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
