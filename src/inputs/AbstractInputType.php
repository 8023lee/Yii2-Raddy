<?php

namespace benbanfa\raddy\inputs;

use benbanfa\raddy\ResourceRegistry;
use yii\base\Model;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;

abstract class AbstractInputType implements InputTypeInterface
{
    private $isReadOnly = false;

    /**
     * {@inheritdoc}
     */
    public function setReadOnly(bool $isReadOnly): InputTypeInterface
    {
        $this->isReadOnly = $isReadOnly;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadOnly(): bool
    {
        return $this->isReadOnly;
    }

    /**
     * {@inheritdoc}
     */
    public function createInput(ActiveForm $form, Model $model, string $attribute, ResourceRegistry $registry): ActiveField
    {
        return $form->field($model, $attribute);
    }
}
