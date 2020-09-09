<?php

namespace benbanfa\raddy\filters;

class StringFilter extends AbstractFilter
{
    private $isFuzzyFindEnabled;

    public function __construct(bool $isFuzzyFindEnabled = false)
    {
        $this->isFuzzyFindEnabled = $isFuzzyFindEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function createSearchCondition(string $fieldName, $value): ?array
    {
        $operator = $this->isFuzzyFindEnabled ? 'like' : '=';

        return [$operator, $fieldName, $value];
    }
}
