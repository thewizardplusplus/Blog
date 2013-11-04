<?php
	/* @var $this PostController */
	/* @var $model Post */
	/* @var $form CActiveForm */

	Yii::app()->getClientScript()->registerCssFile(CHtml::asset(
		'jQueryFormStyler/jquery.formstyler.css'));
	Yii::app()->getClientScript()->registerCssFile(CHtml::asset(
		'styles/styler.css'));

	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/editor.js'), CClientScript::POS_HEAD);
	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'jQueryFormStyler/jquery.formstyler.min.js'), CClientScript::POS_HEAD);
	Yii::app()->getClientScript()->registerScriptFile(CHtml::asset(
		'scripts/styler.js'), CClientScript::POS_HEAD);
?>

<?php $form=$this->beginWidget('CActiveForm', array(
		'id' => 'post-form',
		'enableAjaxValidation' => true,
		'enableClientValidation' => true,
		'errorMessageCssClass' => 'alert alert-danger'
)); ?>

<div class = "panel panel-default">
	<?php echo $form->errorSummary($model, NULL, NULL, array('class' => 'alert '
		. 'alert-danger')); ?>

	<fieldset>
		<legend><?php echo $model->isNewRecord ? 'Создать пост:' : 'Изменить ' .
			'пост:'; ?></legend>

		<div class = "form-group">
			<?php echo $form->labelEx($model, 'title'); ?>
			<?php
				echo $form->textField($model, 'title', array(
					'class' => 'form-control',
					'maxlength' => Constants::MAXIMAL_LENGTH_OF_TITLE_FIELD
				));
			?>
			<?php echo $form->error($model, 'title'); ?>
		</div>

		<div class = "form-group">
			<?php echo $form->labelEx($model, 'text'); ?>
			<div id = "editor"><?php echo CHtml::encode($model->text); ?></div>
			<?php echo $form->error($model, 'text'); ?>
			<?php echo CHtml::hiddenField('Post[text]'); ?>
		</div>

		<div class = "form-group">
			<?php echo $form->labelEx($model, 'tags'); ?>
			<?php echo $form->textField($model, 'tags', array('class' =>
				'form-control')); ?>
			<?php echo $form->error($model, 'tags'); ?>
		</div>

		<div class = "checkbox">
			<?php echo $form->checkBox($model,'published'); ?>
			<?php echo $form->label($model,'published'); ?>
			<?php echo $form->error($model,'published'); ?>
		</div>

		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' :
			'Сохранить', array('class' => 'btn btn-primary')); ?>
	</fieldset>
</div>

<?php $this->endWidget(); ?>
