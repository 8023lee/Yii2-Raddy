<?php

namespace benbanfa\raddy;

use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

class ResourceFormView extends DetailView
{
    public $fields;

    public $form;

    public $registry;

    protected function renderAttribute($attribute, $index)
    {
        if (is_string($this->template)) {
            $captionOptions = Html::renderTagAttributes(ArrayHelper::getValue($attribute, 'captionOptions', []));
            $contentOptions = Html::renderTagAttributes(ArrayHelper::getValue($attribute, 'contentOptions', []));
            $attr = $attribute['attribute'];
            $parts = explode('.', $attr);
            $field = $this->fields;
            foreach ($parts as $part) {
                $field = $field[$part];
            }

            return strtr($this->template, [
                '{label}' => $attribute['label'],
                '{value}' => $field->wrapInputHtml($this->renderInput($attribute), $attr, $this->model),
                '{captionOptions}' => $captionOptions,
                '{contentOptions}' => $contentOptions,
            ]);
        }
    }

    private function renderInput($attribute)
    {
        $attributeName = $attribute['attribute'];
        $attrs = explode('.', $attributeName);
        $field = $this->fields;
        foreach ($attrs as $attr) {
            $field = $field[$attr];
        }

        $inputType = $field->getInputType();

        $isForeignAttribute = preg_match('/^\w+\.\w+$/', $attributeName);
        if (!$isForeignAttribute && !$field->isReadOnly() && (
                ($this->model instanceof ActiveRecord && $this->model->isNewRecord)
                || $this->model instanceof Model
                || !$inputType->isReadOnly())) {
            $input = $inputType->createInput($this->form, $this->model, $attributeName, $this->registry);
            $input->template = "{input}\n{hint}\n{error}";

            return $input;
        }

        $format = $field->getFormat();
        if (null !== $format) {
            // GridOptionsMaker 虽然已经设置过 format，但到此还并没有使用，所以如果是
            // 设置了 format 的数据，还需要在此通过 formatter->format 来处理。
            return $this->formatter->format($attribute['value'], $format);
        }

        // 这里不用 field::showHtml 是因为 DetailView 已经根据 GridOptionsMaker 设置的 value 选项处理过了
        return $attribute['value'];
    }
}
