<?php

namespace benbanfa\raddy\inputs;

use benbanfa\raddy\ResourceRegistry;
use yii\base\Model;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;

class MoneyType extends TextType
{
    private $unit;

    public function __construct(string $unit)
    {
        $this->unit = $unit;
    }

    public function createInput(ActiveForm $form, Model $model, string $attribute, ResourceRegistry $registry): ActiveField
    {
        $field = parent::createInput($form, $model, $attribute, $registry);
        if ('cent' === $this->unit) {
            $field->textInput(['value' => \bcdiv($model->$attribute, 100, 2)]);
        }

        return $field;
    }
}
