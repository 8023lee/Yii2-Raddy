<?php

namespace benbanfa\raddy\inputs;

use benbanfa\raddy\ResourceRegistry;
use yii\base\Model;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;

class PasswordType extends TextType
{
    private $placeholder;

    public function __construct(string $placeholder)
    {
        $this->placeholder = $placeholder;
    }

    public function createInput(ActiveForm $form, Model $model, string $attribute, ResourceRegistry $registry): ActiveField
    {
        $field = parent::createInput($form, $model, $attribute, $registry);

        $field->textInput(['placeholder' => $this->placeholder]);

        return $field;
    }
}
