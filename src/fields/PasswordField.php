<?php

namespace benbanfa\raddy\fields;

use benbanfa\raddy\inputs\InputTypeInterface;
use benbanfa\raddy\inputs\PasswordType;
use yii\base\Model;

class PasswordField extends AbstractField
{
    // 显示内容
    private $displayContent;

    public function __construct(string $displayContent = '******')
    {
        $this->displayContent = $displayContent;

        // 不支持搜索
        $this->setFilter(null);

        $this->setContentGenerator(function (string $name, Model $model) {
            return $this->format($model->$name);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function format($value)
    {
        return $this->displayContent;
    }

    /**
     * {@inheritdoc}
     */
    public function getInputType(): ?InputTypeInterface
    {
        return new PasswordType($this->displayContent);
    }
}
