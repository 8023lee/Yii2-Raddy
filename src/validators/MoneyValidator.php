<?php

namespace benbanfa\raddy\validators;

use yii\validators\NumberValidator;

class MoneyValidator extends NumberValidator
{
    /**
     * {@inheritdoc}
     */
    public function getClientOptions($model, $attribute)
    {
        $options = parent::getClientOptions($model, $attribute);
        $options['pattern'] = $this->numberPattern;

        return $options;
    }
}
