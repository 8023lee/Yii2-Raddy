<?php

namespace benbanfa\raddy\repository;

interface RepositoryInterface
{
    /**
     * 验证 $object 类型是否支持
     *
     * @param $object
     */
    public function supports($object): bool;

    /**
     * 新增/编辑
     *
     * @param $object
     */
    public function save($object): bool;

    /**
     * 删除
     *
     * @param $object
     */
    public function delete($object): bool;
}
