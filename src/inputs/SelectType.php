<?php

namespace benbanfa\raddy\inputs;

use benbanfa\raddy\ResourceRegistry;
use yii\base\Model;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;

class SelectType extends AbstractInputType
{
    private $options;

    /**
     * 当 $options 设置为 null 时，将会自动根据字段名是否匹配 xxId 来推测是否要从 xx 表获取选项
     *
     * @param array $options 选项
     */
    public function __construct(array $options = null)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function createInput(ActiveForm $form, Model $model, string $attribute, ResourceRegistry $registry): ActiveField
    {
        $input = parent::createInput($form, $model, $attribute, $registry);

        if (null === $this->options && preg_match('/^(\w+)_?(i|I)d$/', $input->attribute, $matches)) {
            $relatedResourceId = $matches[1];

            if (!$registry->hasResource($relatedResourceId)) {
                throw new \Exception(sprintf('Select 类型字段 %s 没有设置选项', $input->attribute));
            }

            $relatedResource = $registry->getResource($relatedResourceId);
            $nameField = $relatedResource->getNameFields()[0];

            $this->options = $relatedResource->getModelClass()::find()->select($nameField)->distinct()->indexBy('id')->column();
        }

        // 如果最终还得不到选项，只能给空数组
        if (null === $this->options) {
            $this->options = [];
        }

        return $input->dropDownList($this->options);
    }
}
