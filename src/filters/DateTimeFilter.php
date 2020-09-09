<?php

namespace benbanfa\raddy\filters;

use benbanfa\raddy\Filter;
use benbanfa\raddy\resources\ResourceInterface;
use kartik\daterange\DateRangePicker;

class DateTimeFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function createSearchCondition(string $fieldName, $value): ?array
    {
        [$start, $end] = explode(' - ', $value);

        return ['BETWEEN', $fieldName, $start, $end];
    }

    public function getDataColumnConfig(Filter $model, string $name, ResourceInterface $resource)
    {
        return DateRangePicker::widget([
            'model' => $model,
            'attribute' => $name,
            'convertFormat' => true,
            'pluginOptions' => [
                'timePicker' => true,
                'timePickerIncrement' => 30,
                'locale' => [
                    'format' => 'Y-m-d H:i:s',
                ],
            ],
        ]);
    }
}
