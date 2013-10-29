<?php
	/* @var $this ParametersFormController */
	/* @var $model ParametersForm */
	/* @var $form CActiveForm */

	$this->pageTitle = Yii::app()->name . ' - Параметры';
?>

<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'parameters-form',
		'enableAjaxValidation' => true,
		'enableClientValidation' => true,
		'errorMessageCssClass' => 'alert alert-danger'
	));
?>

<div class = "panel panel-default">
	<fieldset>
		<legend>Параметры:</legend>

		<?php echo $form->errorSummary($model); ?>

		<div class = "panel panel-default">
			<fieldset>
				<legend>Пароль:</legend>

				<div class = "form-group">
					<?php echo $form->labelEx($model, 'password'); ?>
					<?php echo $form->passwordField($model, 'password', array(
						'class' => 'form-control',
						'autocomplete' => 'off'
					)); ?>
					<?php echo $form->error($model, 'password'); ?>
				</div>

				<div class = "form-group">
					<?php echo $form->labelEx($model, 'password_copy'); ?>
					<?php
						echo $form->passwordField($model, 'password_copy',
							array(
								'class' => 'form-control',
								'autocomplete' => 'off'
							));
					?>
					<?php echo $form->error($model, 'password_copy'); ?>
				</div>
			</fieldset>
		</div>

		<div class = "form-group">
			<?php echo $form->labelEx($model, 'posts_on_page'); ?>
			<?php echo $form->numberField($model, 'posts_on_page', array(
					'min' => Parameters::MINIMUM_POSTS_ON_PAGE,
					'max' => Parameters::MAXIMUM_POSTS_ON_PAGE
				)); ?>
			<?php echo $form->error($model, 'posts_on_page'); ?>
		</div>

		<?php echo CHtml::submitButton('Сохранить', array('class' =>
			'btn btn-primary')); ?>
	</fieldset>
</div>

<?php $this->endWidget(); ?>

<?php
	echo CHtml::script(
		'jQuery("#ParametersForm_posts_on_page").spinner();' .
		'jQuery("#ParametersForm_maximum_number_of_tags").spinner();'
	);
?>
