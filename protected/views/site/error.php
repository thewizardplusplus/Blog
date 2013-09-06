<?php
	/* @var $this SiteController */
	/* @var $error array */

	$this->pageTitle = Yii::app()->name . ' - Ошибка';
?>

<div class = "error">
	<h2>Ошибка <?php echo $code; ?></h2>
	<p><?php echo CHtml::encode($message); ?></p>
</div>
