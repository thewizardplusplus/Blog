<?php
	/* @var $this PostController */
	/* @var $data_provider CActiveDataProvider */

	$this->pageTitle = Yii::app()->name;
?>

<?php if (isset($_GET['tag'])) { ?>
	<p class = "panel panel-default note">
		Посты с тегом <span class = "label label-success"><?php echo CHtml::
			encode($_GET['tag']); ?></span>:
	</p>
<?php } ?>

<?php
	$this->widget('zii.widgets.CListView', array(
		'dataProvider' => $data_provider,
		'template' => '{items} {pager}',
		'itemView' => '_view',
		'loadingCssClass' => 'wait',
		'pager' => array(
			'maxButtonCount' => 0,
			'header' => '',
			'prevPageLabel' => '&lt;&lt; Следующие',
			'nextPageLabel' => 'Предыдующие &gt;&gt;',
			'firstPageCssClass' => 'hidden',
			'lastPageCssClass' => 'hidden',
			'hiddenPageCssClass' => 'disabled',
			'htmlOptions' => array('class' => 'pagination')
		)
	));
?>
