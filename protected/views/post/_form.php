<?php
	/* @var $this PostController */
	/* @var $model Post */
	/* @var $form CActiveForm */
?>

<div class = "form">
	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id' => 'post-form',
			'enableAjaxValidation' => true,
			'enableClientValidation' => true
	)); ?>

	<?php echo $form->errorSummary($model); ?>

	<fieldset>
		<legend><?php echo $model->isNewRecord ? 'Создать пост:' : 'Изменить ' .
			'пост:'; ?></legend>

		<div class = "row">
			<?php echo $form->labelEx($model, 'title'); ?>
			<?php echo $form->textField($model, 'title', array('maxlength' =>
				Post::MAXIMAL_LENGTH_OF_TITLE_FIELD)); ?>
			<?php echo $form->error($model, 'title'); ?>
		</div>

		<div class = "row">
			<?php echo $form->labelEx($model, 'text'); ?>
			<?php echo $form->textArea($model, 'text'); ?>
			<?php echo $form->error($model, 'text'); ?>
		</div>

		<div class = "row">
			<?php echo $form->labelEx($model, 'tags'); ?>
			<?php echo $form->textField($model, 'tags'); ?>
			<?php echo $form->error($model, 'tags'); ?>
		</div>

		<div class = "row buttons">
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' :
				'Сохранить'); ?>
		</div>
	</fieldset>

	<?php $this->endWidget(); ?>
</div>
