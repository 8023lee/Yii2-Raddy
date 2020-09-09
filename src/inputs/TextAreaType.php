<?php

namespace benbanfa\raddy\inputs;

use benbanfa\raddy\ResourceRegistry;
use yii\base\Model;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;

class TextAreaType extends AbstractInputType
{
    private $nbRow;

    public function __construct(int $nbRow = 4)
    {
        $this->nbRow = $nbRow;
    }

    /**
     * {@inheritdoc}
     */
    public function createInput(ActiveForm $form, Model $model, string $attribute, ResourceRegistry $registry): ActiveField
    {
        return parent::createInput($form, $model, $attribute, $registry)->textarea(['rows' => $this->nbRow]);
    }
}
