<?php
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs=array(
	'Users'=>array('index'),
	$model->username,
);

$this->menu=array(
	array('label'=>'Lisää kuva', 'url'=>array('upload')),
);
?>

<h1><?php echo $model->username; ?></h1>
<?php $this->widget('ProfileInfo', array(
        'title'=>'Info',
)); ?>

<?php $this->widget('UserComments');?>
