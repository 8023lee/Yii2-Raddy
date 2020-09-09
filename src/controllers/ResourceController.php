<?php

namespace benbanfa\raddy\controllers;

use benbanfa\raddy\Filter;
use benbanfa\raddy\ResourceRegistry;
use benbanfa\raddy\services\GridColumnsOptionMaker;
use benbanfa\raddy\services\JoinQueryHandler;
use benbanfa\raddy\services\ResourceFieldsReader;
use Yii;
use yii\base\Module;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ResourceController extends Controller
{
    private $registry;

    private $resourceId;

    private $resource;

    private $fieldsReader;

    private $joinQueryHandler;

    private $columnsOptionMaker;

    private $modelClass;

    private $query;

    private $filter;

    private $fields;

    private $dataProvider;

    public function __construct(
        string $id,
        Module $module,
        ResourceRegistry $registry,
        ResourceFieldsReader $fieldsReader,
        JoinQueryHandler $joinQueryHandler,
        GridColumnsOptionMaker $columnsOptionMaker,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);

        $this->registry = $registry;
        $this->fieldsReader = $fieldsReader;
        $this->joinQueryHandler = $joinQueryHandler;
        $this->columnsOptionMaker = $columnsOptionMaker;

        $request = Yii::$app->request;
        $resourceId = $request->get('resourceId');
        if (!$this->registry->hasResource($resourceId)) {
            throw new NotFoundHttpException(sprintf('无 id 为 %s 的资源', $resourceId));
        }

        $this->resourceId = $resourceId;
        $this->resource = $this->registry->getResource($resourceId);

        if ('list-action' === $this->action->id) {
            $actionId = $request->get('actionId');
            $action = $this->resource->getListActions()[$actionId];
            if (isset($action['onQuerying'])) {
                $response = $action['onQuerying']($this->resource, $request);
                if ($response) {
                    return $response->send();
                }
            }
        }

        $this->modelClass = $this->resource->getModelClass();

        $this->fields = $this->fieldsReader->read($this->resource);
        $this->resource->onFieldsRead($this->fields);

        // 如果是AR类，从 Modle 上获取 ActiveQuery 对象
        if (is_subclass_of($this->modelClass, ActiveRecord::class)) {
            $this->query = $this->modelClass::find();
        } else {
            $this->query = new ActiveQuery($this->modelClass);
        }

        $this->joinQueryHandler->handle($this->query, $this->fields);
    }

    public function behaviors()
    {
        $rules = $this->resource->getAuthRules();
        if (empty($rules)) {
            return [];
        }

        // 通过 actionRowAction 内部判断是否权限到位，外部都通过
        $rules[] = [
            'allow' => true,
            'actions' => ['row-action'],
            'roles' => ['@'],
        ];

        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $rules,
            ],
        ];
    }

    public function actionIndex($resourceId)
    {
        $this->initList();

        return $this->render('@raddy/views/index.php', [
            'dataProvider' => $this->dataProvider,
            'columns' => $this->columnsOptionMaker->make($this->fields, $this->resource, $this->filter),
            'filter' => $this->filter,
            'resource' => $this->resource,
            'request' => Yii::$app->request->queryParams,
        ]);
    }

    public function actionEdit($resourceId, $id)
    {
        $idField = sprintf('%s.%s', $this->query->modelClass::tableName(), $this->resource->getModelIdentifier());
        $model = $this->query->where([$idField => $id])->one();
        if (null === $model) {
            throw new NotFoundHttpException(sprintf('无 id 为 %s 的 %s 资源', $id, $resourceId));
        }

        $this->resource->initEditModel($model, Yii::$app->request);

        return $this->responseForForm($model);
    }

    public function actionNew($resourceId)
    {
        $model = new $this->modelClass();

        $this->resource->initNewModel($model, Yii::$app->request);

        return $this->responseForForm($model);
    }

    public function actionRowAction($resourceId, $actionId, $id)
    {
        $action = $this->resource->getRowActions()[$actionId];

        if (!empty($action['roles']) && !$this->hasRoles(Yii::$app->user, (array) $action['roles'], $actions['roleParams'] ?? [])) {
            throw new ForbiddenHttpException('您没有执行此操作的权限');
        }

        $model = $this->getModel($id);
        $action['action']($model);

        return $this->redirect(Yii::$app->request->headers->get('Referer'));
    }

    public function actionListAction($resourceId, $actionId)
    {
        $this->initList();

        $actions = $this->resource->getListActions();
        if (!isset($actions[$actionId]['onQueried'])) {
            throw new \Exception(sprintf('列表动作 %s 的 onQueried 事件处理没有定义', $actionId));
        }

        return $action[$actionId]['onQueried']($this->resource, $this->dataProvider, $this->fields);
    }

    private function responseForForm($model)
    {
        $this->resource->onFormRendering($this->fields);

        $request = Yii::$app->request;
        if ($request->isPost && empty($request->post()[$model->formName()])) {
            return $this->redirect($request->getQueryParam('successUrl', $request->headers->get('Referer')));
        }
        if ($request->isPost && $model->load($request->post())) {
            foreach ($this->fields as $name => $config) {
                if (is_object($config)) {
                    $config->preDataValidation($name, $model);
                }
            }

            if ($model->validate()) {
                foreach ($this->fields as $name => $config) {
                    if (is_object($config)) {
                        $config->handlePostData($name, $model);
                    }
                }

                $this->resource->getRepository()->save($model);

                return $this->redirect($request->getQueryParam('successUrl', $request->headers->get('referer')));
            }
        }

        return $this->render('@raddy/views/form.php', [
            'model' => $model,
            'resource' => $this->resource,
            'fields' => $this->fields,
            'attributes' => $this->columnsOptionMaker->make($this->fields, $this->resource),
            'registry' => $this->registry,
        ]);
    }

    private function getModel($id)
    {
        $model = $this->modelClass::findOne($id);
        if (null === $model) {
            throw new NotFoundHttpException(sprintf('无 id 为 %s 的 %s 资源', $id, $modelClass));
        }

        return $model;
    }

    private function initList()
    {
        $request = Yii::$app->request;

        $this->resource->onListFiltering($this->fields, $this->query);

        $this->filter = new Filter($this->fields, $this->query);
        $this->filter->load($request->queryParams);
        $this->filter->search();

        $this->dataProvider = new ActiveDataProvider([
            'query' => $this->query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
    }

    private function hasRoles($user, $roles, $roleParams = []): bool
    {
        foreach ($roles as $role) {
            if ($user->can($role, $roleParams)) {
                return true;
            }
        }

        return false;
    }
}
