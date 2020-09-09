<?php

namespace benbanfa\raddy;

class ResourceRegistry
{
    private $options;

    private $resources;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function getResourceClasses()
    {
        return $this->options['resources'];
    }

    /**
     * 是否有注册指定的资源
     *
     * @param string $id 资源 id
     */
    public function hasResource(string $id): bool
    {
        if (!isset($this->options['resources'][$id])) {
            return false;
        }

        $class = $this->options['resources'][$id];

        return is_string($class) && class_exists($class);
    }

    /**
     * 获取指定的资源类
     *
     * @param string $id 资源 id
     *
     * @return string
     */
    public function getResourceClass($id)
    {
        if ($this->hasResource($id)) {
            return $this->options['resources'][$id];
        }

        throw new \LogicException(sprintf('资源 %s 没有指定资源类，或指定的类并不存在', $id));
    }

    /**
     * 获取指定的资源对象
     *
     * @param string $id 资源 id
     *
     * @return ResourceInterface
     */
    public function getResource($id)
    {
        if (!isset($this->resources[$id])) {
            $class = $this->getResourceClass($id);
            $this->resources[$id] = new $class($id);
        }

        return $this->resources[$id];
    }

    public function getResourceIdByModelClass($class)
    {
        foreach (array_keys($this->options['resources']) as $resourceId) {
            if ($this->getResource($resourceId)->getModelClass() === $class) {
                return $resourceId;
            }
        }
    }
}
