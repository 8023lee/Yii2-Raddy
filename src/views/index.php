<?php

/* @var $this \yii\web\View */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

// $actionId = Yii::$app->controller->action->id;
$this->title = $resource->getId();

// --- 注册按钮事件 ---
// btn-danger 需要确认才执行
// a.btn-layer 会弹出对话框
$this->registerJs(<<<'JS'
$('.btn-action').on('click', function (e) {
    if (($(this).hasClass('btn-danger') || $(this).hasClass('btn-warning')) && !confirm('确认执行此操作？')) {
        e.preventDefault();
    }
});
$('a.btn-layer').on('click', function (e) {
    e.preventDefault();
    layer.open({
        type: 2,
        title: 'title',
        content: $(this).prop('href'),
        area: ['640px', '480px']
    });
});
JS
, View::POS_READY);

// --- 隐藏显示列 ---
$this->registerJs("var resourceId = '{$resource->getId()}'");
$this->registerJs(<<<'JS'
$(function () {
    var itemKey = resourceId + 'HiddenColumns';
    var hiddenColumns = localStorage.getItem(itemKey);
    hiddenColumns = null === hiddenColumns ? {} : JSON.parse(hiddenColumns);

    var $p = $('<p class="btn-show-group btn-group">');
    $('.grid-view table').after($p);

    if (Object.keys(hiddenColumns)) {
        $p.on('click', 'a', function (e) {
            e.preventDefault();
            var index = +$(this).data('index');
            $('tr > :nth-child(' + (index + 1) + ')').removeClass('hide');

            delete hiddenColumns[index];
            localStorage.setItem(itemKey, JSON.stringify(hiddenColumns));

            $(this).remove();
        });
    }

    var addShowBtn = function(index) {
        var $a = $('<a class="btn-show btn btn-default btn-sm" href="#show"><i class="fa fa-eye"></i> ' + $('th').eq(index).text() + '</a>');
        $a.data('index', index);
        $p.append($a);
    };

    for (var index in hiddenColumns) {
        index = +index;
        $('tr > :nth-child(' + (index + 1) + ')').addClass('hide');
        addShowBtn(index);
    }

    $('th:not(.action-column)').append('<a class="btn-hide" href="#hide"><i class="fa fa-eye-slash"></i></a>')
        .find('.btn-hide').click(function (e) {
           e.preventDefault();
            var index = $('th .btn-hide').index($(this));
            $('tr > :nth-child(' + (index + 1) + ')').addClass('hide');

            hiddenColumns[index] = index;
            localStorage.setItem(itemKey, JSON.stringify(hiddenColumns));

            addShowBtn(index);
        });
});
JS
, View::POS_READY);

$this->registerCss(
    <<<'CSS'
.btn-hide:before {
    content: ' ';
}

.btn-show-group {
    float: right;
}

.table-wrapper {
    overflow: scroll;
    min-height: 800px;
}

.table th {
    white-space: nowrap;
}

.filters select[multiple] {
    padding: 3px;
    width: auto;
    min-width: 100%;
}

.asc:after {
    content: ' 正序';
    font-size: .75em;
}

.desc:after {
    content: ' 倒序';
    font-size: .75em;
}

.kv-field-range input {
    min-width: 4em;
}
CSS
);

// TODO 标题
// TODO 面包屑导航
?>

<?php

$options = [
    'dataProvider' => $dataProvider,
    'columns' => $columns,
    'filterOnFocusOut' => false,
    'showFooter' => true,
    'footerRowOptions' => ['class' => 'info'],
];

if ($filter->hasAttributes()) {
    $options['filterModel'] = $filter;
}

?>

<form method="post">
    <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
    <div class="table-wrapper">
        <?= GridView::widget($options); ?>
    </div>
</form>

<div class="btn-group">
    <?php if ($resource->canCreate()): ?>
        <?= Html::a('新增', [
            'resource/new',
            'resourceId' => $resource->getId(),
            'successUrl' => Yii::$app->request->url,
        ], ['class' => 'btn btn-primary']); ?>
    <?php endif; ?>

    <?php foreach ($resource->getListActions() as $actionId => $action): ?>
        <?php
            $class = sprintf('btn btn-%s btn-action', $action['class']);
            if (isset($action['hasLayer']) && $action['hasLayer']) {
                $class = sprintf('%s btn-layer', $class);
            }

            if (!empty($action['url'])) {
                $url = $action['url'];
            } else {
                $url = array_merge([
                    'resource/list-action',
                    'resourceId' => $resource->getId(),
                    'actionId' => $actionId,
                ], $request);
            }
        ?>
        <?= Html::a($action['label'], $url, ['class' => $class, 'target' => $action['target'] ?: '_self']); ?>
    <?php endforeach; ?>
</div>
