<?php

namespace benbanfa\raddy\inputs;

use benbanfa\raddy\ResourceRegistry;
use yii\base\Model;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;

interface InputTypeInterface
{
    /**
     * 设置是否只读（不可编辑）
     *
     * @param bool $isReadOnly 是否可读
     */
    public function setReadOnly(bool $isReadOnly): self;

    /**
     * 返回是否可读
     */
    public function isReadOnly(): bool;

    /**
     * 创建 input 对象
     *
     * @param ActiveForm $form  表单对象
     * @param Model $model      模型
     * @param string $attribute 属性名称
     * @param ResourceRegistry $registry  资源注册器对象，猜测选项时可能用到
     */
    public function createInput(ActiveForm $form, Model $model, string $attribute, ResourceRegistry $registry): ActiveField;
}
