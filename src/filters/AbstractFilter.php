<?php

namespace benbanfa\raddy\filters;

use benbanfa\raddy\Filter;
use benbanfa\raddy\resources\ResourceInterface;

abstract class AbstractFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function createSearchCondition(string $fieldName, $value): ?array
    {
        return [$fieldName => $value];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataColumnConfig(Filter $model, string $name, ResourceInterface $resource)
    {
        return null;
    }
}
