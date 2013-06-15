<?php
$model = new User();
$form = $this->beginWidget(
    'CActiveForm',
    array(
        'id' => 'upload-form',
        'enableAjaxValidation' => false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
    )
);
// ...
echo $form->labelEx($model, 'personal_image_url');
echo $form->fileField($model, 'personal_image_url');
echo $form->error($model, 'personal_image_url');
echo CHtml::submitButton('Upload');
// ...
$this->endWidget();

?>
