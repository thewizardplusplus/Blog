<?php
	/* @var $this PostController */
	/* @var $model Post */
	/* @var $form CActiveForm */
?>

<?php
	$form=$this->beginWidget('CActiveForm', array(
		'id' => 'post-form',
		'enableAjaxValidation' => true,
		'enableClientValidation' => true,
		'errorMessageCssClass' => 'alert alert-danger'
)); ?>

<div class = "panel panel-default">
	<?php echo $form->errorSummary($model); ?>

	<fieldset>
		<legend><?php echo $model->isNewRecord ? 'Создать пост:' : 'Изменить ' .
			'пост:'; ?></legend>

		<div class = "form-group">
			<?php echo $form->labelEx($model, 'title'); ?>
			<?php
				echo $form->textField($model, 'title', array(
					'class' => 'form-control',
					'maxlength' => Post::MAXIMAL_LENGTH_OF_TITLE_FIELD
				));
			?>
			<?php echo $form->error($model, 'title'); ?>
		</div>

		<div class = "form-group">
			<?php echo $form->labelEx($model, 'text'); ?>
			<?php echo $form->textArea($model, 'text', array('class' =>
				'form-control')); ?>
			<?php echo $form->error($model, 'text'); ?>
		</div>

		<div class = "form-group">
			<?php echo $form->labelEx($model, 'tags'); ?>
			<?php echo $form->textField($model, 'tags', array('class' =>
				'form-control')); ?>
			<?php echo $form->error($model, 'tags'); ?>
		</div>

		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' :
			'Сохранить', array('class' => 'btn btn-primary')); ?>
	</fieldset>
</div>

<?php $this->endWidget(); ?>
