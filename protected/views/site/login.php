<?php
	/* @var $this SiteController */
	/* @var $model LoginForm */
	/* @var $form CActiveForm  */

	$this->pageTitle = Yii::app()->name;
?>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'login-form',
	'enableAjaxValidation' => true,
	'enableClientValidation' => true,
	'errorMessageCssClass' => 'alert alert-danger'
)); ?>

<div class = "panel panel-default">
	<fieldset>
		<legend>Вход:</legend>

		<?php echo $form->errorSummary($model, NULL, NULL, array('class' =>
			'alert alert-danger')); ?>

		<div class = "form-group">
			<?php echo $form->labelEx($model, 'password'); ?>
			<?php echo $form->passwordField($model, 'password', array('class' =>
				'form-control')); ?>
			<?php echo $form->error($model, 'password'); ?>
		</div>

		<?php echo CHtml::submitButton('Вход', array('class' => 'btn btn-' .
			'primary')); ?>
	</fieldset>
</div>

<?php $this->endWidget(); ?>
