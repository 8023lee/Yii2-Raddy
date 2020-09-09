<?php

namespace benbanfa\raddy\fields;

use benbanfa\raddy\filters\FilterInterface;
use benbanfa\raddy\filters\MoneyFilter;
use benbanfa\raddy\inputs\InputTypeInterface;
use benbanfa\raddy\inputs\MoneyType;
use yii\base\Model;
use yii\db\ColumnSchema;

class MoneyField extends StringField
{
    private $unit;

    public function __construct(string $unit = null)
    {
        $this->unit = $unit;

        parent::__construct();

        $this->setContentGenerator(function (string $name, Model $model) {
            if (is_numeric($model->$name)) {
                return $this->format($model->$name);
            }

            throw new \InvalidArgumentException(sprintf('MoneyField 只用于数字类型的数据，%s 不被支持', $model->$name));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function init(ColumnSchema $columnSchema): void
    {
        if (null === $this->unit && 'integer' === $columnSchema->phpType) {
            $this->unit = 'cent';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function format($value)
    {
        if ('cent' === $this->unit) {
            $value = \bcdiv($value, 100, 2);
        }

        return sprintf('￥%.2f', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getInputType(): ?InputTypeInterface
    {
        return new MoneyType($this->unit);
    }

    /**
     * {@inheritdoc}
     */
    public function preDataValidation(string $name, Model $model): void
    {
        $value = $model->$name;
        if ('cent' === $this->unit) {
            $value = \bcmul($value, 100, 0);
        }

        $model->$name = $value;
    }

    public function getFilter(): ?FilterInterface
    {
        $filter = parent::getFilter();
        if (null === $filter) {
            $filter = new MoneyFilter($this->unit);
            $this->setFilter($filter);
        }

        return $filter;
    }
}
