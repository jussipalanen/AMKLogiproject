<?php
/* @var $this RegisterFormController */
/* @var $model RegisterForm */
/* @var $form CActiveForm */
?>
<script type="text/javascript">
    $(document).ready(function() {
        $("#dialog-message").hide();
        $(".form").show();
    });
</script>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'register-form-register-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username'); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password'); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>
        
        <div class="row">
		<?php echo $form->labelEx($model,'repassword'); ?>
		<?php echo $form->passwordField($model,'repassword'); ?>
		<?php echo $form->error($model,'repassword'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email'); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton('Rekistöröidy', array('id'=>'registerbtn')); ?>
                <?php echo CHtml::resetButton('Tyhjennä');?>
	</div>
        
        <div id="dialog-message" title="Ilmoitus">
            <p>
            <span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>
            Kiitos rekistöröinnistä!
            </p>
        </div>

<?php $this->endWidget(); ?>

</div><!-- form -->