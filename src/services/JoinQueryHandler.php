<?php

namespace benbanfa\raddy\services;

use yii\db\ActiveQueryInterface;

class JoinQueryHandler
{
    /**
     * @param ActiveQueryInterface $query      查询语句
     * @param array                $columnInfo 列信息
     * @param string               $parent     父表
     */
    public function handle(ActiveQueryInterface $query, array $columnInfo, ?string $parent = null): bool
    {
        $return = false;
        foreach ($columnInfo as $name => $info) {
            if (!is_array($info)) {
                continue;
            }

            $return = true;

            if (null === $parent) {
                $isHandled = $this->handle($query, $info, $name);
                if (!$isHandled) {
                    $query->joinWith(sprintf('%s %s', $name, $name));
                }

                continue;
            }

            $parent = sprintf('%s.%s', $parent, $name);
            $isHandled = $this->handle($query, $info, $parent);
            if (!$isHandled) {
                $query->joinWith(sprintf('%s %s', $parent, $name));
            }
        }

        return $return;
    }
}
