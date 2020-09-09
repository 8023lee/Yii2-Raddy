<?php

use benbanfa\raddy\ResourceFormView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

// 通过 Resource 生成标的时，设置 Title
if ($resource) {
    $this->title = sprintf('%s#%s', $resource->getId(), $model->{$resource->getModelIdentifier()});
}

?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <?= ResourceFormView::widget([
        'fields' => $fields,
        'model' => $model,
        'attributes' => $attributes,
        'form' => $form,
        'registry' => $registry,
    ]); ?>
    <div class="form-group">
        <?= Html::submitButton('确定', ['class' => 'btn btn-primary btn-block']); ?>
    </div>
<?php ActiveForm::end(); ?>
