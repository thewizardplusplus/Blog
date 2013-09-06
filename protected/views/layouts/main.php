<?php
	/* @var $this CController */

	$with_aside = (Yii::app()->user->isGuest and $this->action->id == 'list');
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset = "utf-8" />
		<link rel = "stylesheet" href = "<?php echo Yii::app()->request->
			baseUrl; ?>/css/screen.css" />
		<link rel = "stylesheet" href = "<?php echo Yii::app()->request->
			baseUrl; ?>/css/main.css" />
		<link rel = "stylesheet" href = "<?php echo Yii::app()->request->
			baseUrl; ?>/css/form.css" />
		<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	</head>
	<body>
		<div id = "page" class = "container">
			<div id = "header">
				<div id = "logo">
					<?php echo CHtml::encode(Yii::app()->name); ?>
				</div>
			</div>

			<div id = "mainmenu">
				<?php $this->widget('zii.widgets.CMenu',array(
					'items' => array(
						array('label' => 'Главная', 'url' => array(
							'post/list')),
						array('label' => 'Создать пост', 'url' => array(
							'post/create'), 'visible' => !Yii::app()->user
							->isGuest),
						array('label' => 'Файлы', 'url' => array('site/files'),
							'visible' => !Yii::app()->user->isGuest),
						array('label' => 'Параметры', 'url' => array(
							'parameters/update'), 'visible' => !Yii::app()->user
							->isGuest),
						array('label' => 'Вход', 'url' => array('site/login'),
							'visible' => Yii::app()->user->isGuest),
						array('label' => 'Выход', 'url' => array('site/logout'),
							'visible' => !Yii::app()->user->isGuest)
					)
				)); ?>
			</div>

			<?php if ($with_aside) { ?>
			<div class = "span-19">
			<?php } ?>
				<div id = "content">
					<?php echo $content; ?>
				</div>
			<?php if ($with_aside) { ?>
			</div>
			<div class = "span-5 last">
				<div id = "sidebar">
					<?php $this->widget('TagCloud', array('title' => 'Теги:'));
						?>
				</div>
			</div>
			<div class = "clear"></div>
			<?php } ?>

			<div id = "footer">
				&copy; <?php echo date('Y'); ?>, thewizardplusplus.<br />
				<?php echo Yii::powered(); ?>
			</div>
		</div>
	</body>
</html>
