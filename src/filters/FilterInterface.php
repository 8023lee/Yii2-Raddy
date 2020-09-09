<?php

namespace benbanfa\raddy\filters;

use benbanfa\raddy\Filter;
use benbanfa\raddy\resources\ResourceInterface;

interface FilterInterface
{
    /**
     * 创建搜索查询条件
     *
     * @param string       $fieldName 字段名
     * @param string|array $value     值
     */
    public function createSearchCondition(string $fieldName, $value): ?array;

    /**
     * 获取 DataColumn 的 filter 的配置
     *
     * @param Filter            $model    搜索对象
     * @param string            $name     字段名
     * @param ResourceInterface $resource 资源对象
     *
     * @return string|array|null
     */
    public function getDataColumnConfig(Filter $model, string $name, ResourceInterface $resource);
}
