<?php

namespace benbanfa\raddy\fields;

use benbanfa\raddy\filters\OptionsFilter;
use benbanfa\raddy\inputs\InputTypeInterface;
use benbanfa\raddy\inputs\SelectType;
use yii\base\Model;

class EnumField extends AbstractField
{
    private $values;

    public function __construct(array $values = null)
    {
        $this->values = $values;
        $this->setFilter(new OptionsFilter($this->values));

        $this->setContentGenerator(function (string $name, Model $model) {
            return $this->format($model->$name);
        });
    }

    public function getInputType(): ?InputTypeInterface
    {
        return new SelectType($this->values);
    }

    /**
     * {@inheritdoc}
     */
    public function format($data)
    {
        if (null === $this->values) {
            return $data;
        }

        if (!isset($this->values[$data])) {
            return '(未知)';
        }

        return $this->values[$data];
    }
}
