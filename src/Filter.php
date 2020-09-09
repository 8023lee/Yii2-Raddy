<?php

namespace benbanfa\raddy;

use yii\base\Model;
use yii\base\UnknownPropertyException;

class Filter extends Model
{
    private $attributes = [];

    private $config;

    private $query;

    public function __construct(array $fields, $query)
    {
        $this->initConfig($fields);
        $this->query = $query;
    }

    public function activeAttributes()
    {
        return array_keys($this->attributes);
    }

    public function safeAttributes()
    {
        return $this->activeAttributes();
    }

    public function hasAttributes()
    {
        return !empty($this->attributes);
    }

    public function __get($name)
    {
        if (!array_key_exists($name, $this->attributes)) {
            throw new UnknownPropertyException('Getting unknown property: '.$name);
        }

        return $this->attributes[$name];
    }

    public function __set($name, $value)
    {
        if (!array_key_exists($name, $this->attributes)) {
            throw new UnknownPropertyException('Getting unknown property: '.$name);
        }

        $this->attributes[$name] = $value;
    }

    public function search()
    {
        foreach ($this->attributes as $name => $value) {
            if ('' === $value || null === $value) {
                continue;
            }

            $fieldName = $name;
            if (preg_match('/^(\w+)$/', $name)) {
                $fieldName = sprintf('%s.%s', $this->query->modelClass::tableName(), $name);
            } elseif (preg_match('/\.(\w+\.\w+)$/', $name, $match)) {
                $fieldName = $match[1];
            }

            $condition = $this->config[$name]->createSearchCondition($fieldName, $value);

            if (is_array($condition)) {
                $this->query->andWhere($condition);
            }
        }
    }

    public function getQuery()
    {
        return $this->query;
    }

    private function initConfig(array $fields, $parent = null)
    {
        foreach ($fields as $name => $config) {
            if (null !== $parent) {
                $name = sprintf('%s.%s', $parent, $name);
            }

            $this->attributes[$name] = null;
            if (is_array($config)) {
                $this->initConfig($config, $name);

                continue;
            }

            $this->config[$name] = $config->getFilter();
        }
    }
}
