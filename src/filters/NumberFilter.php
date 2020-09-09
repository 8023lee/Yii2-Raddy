<?php

namespace benbanfa\raddy\filters;

use benbanfa\raddy\Filter;
use benbanfa\raddy\resources\ResourceInterface;
use kartik\field\FieldRange;

class NumberFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function createSearchCondition(string $fieldName, $value): ?array
    {
        if (is_numeric($value['start']) && is_numeric($value['end'])) {
            return ['BETWEEN', $fieldName, $value['start'], $value['end']];
        }

        if (is_numeric($value['start'])) {
            return ['>=', $fieldName, $value['start']];
        }

        if (is_numeric($value['end'])) {
            return ['<=', $fieldName, $value['end']];
        }

        return null;
    }

    public function getDataColumnConfig(Filter $model, string $name, ResourceInterface $resource)
    {
        return FieldRange::widget([
            'model' => $model,
            'template' => '{widget}',
            'separator' => '-',
            'attribute1' => sprintf('%s[start]', $name),
            'attribute2' => sprintf('%s[end]', $name),
        ]);
    }
}
