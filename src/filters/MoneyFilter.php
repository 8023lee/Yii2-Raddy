<?php

namespace benbanfa\raddy\filters;

class MoneyFilter extends NumberFilter
{
    private $unit;

    public function __construct(string $unit = 'cent')
    {
        $this->unit = $unit;
    }

    /**
     * {@inheritdoc}
     */
    public function createSearchCondition(string $fieldName, $value): ?array
    {
        if ('cent' === $this->unit) {
            $value = array_map(function ($element) {
                if (is_numeric($element)) {
                    return $element * 100;
                }

                return null;
            }, $value);
        }

        return parent::createSearchCondition($fieldName, $value);
    }
}
