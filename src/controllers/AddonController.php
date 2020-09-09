<?php

namespace benbanfa\raddy\controllers;

use yii\web\Controller;

class AddonController extends Controller
{
    public function actions()
    {
        return $this->module->addonActions;
    }
}
