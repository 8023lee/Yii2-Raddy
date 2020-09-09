<?php

namespace benbanfa\raddy;

interface AdminInterface
{
    /**
     * 管理员在 Raddy 后台展示的名称
     *
     * @return string
     */
    public function getRaddyDisplayName();
}
