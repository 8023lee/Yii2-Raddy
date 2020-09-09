<?php

namespace benbanfa\raddy\inputs;

use benbanfa\raddy\ResourceRegistry;
use yii\base\Model;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;

class FileType extends AbstractInputType
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function createInput(ActiveForm $form, Model $model, string $attribute, ResourceRegistry $registry): ActiveField
    {
        return parent::createInput($form, $model, $this->name, $registry)->fileInput();
    }
}
