<?php

/* @var $this PostController */
/* @var $data_provider CActiveDataProvider */

$this->pageTitle = Yii::app()->name;

if (isset($_GET['tag'])) {
	echo '<p class = "note">Посты с тегом &laquo;' . CHtml::encode($_GET['tag'])
		. '&raquo;:</p>';
}

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
