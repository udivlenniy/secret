<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>

<div class="form" id="admin_login">

    <h1>Авторизация администратора</h1>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'admin-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>
        <?php if($showFileds){?>
            <div id="show_sms" ">
                <div class="row">
                    <?php echo $form->labelEx($model,'smsCode'); ?>
                    <?php echo $form->passwordField($model,'smsCode'); ?>
                    <?php echo $form->error($model,'smsCode'); ?>
                    <?php
                        echo $form->hiddenField($model,'username');
                        echo $form->hiddenField($model,'password');
                    ?>
                </div>
            </div>
        <?php }else{?>

            <div class="row">
                <?php echo $form->labelEx($model,'username'); ?>
                <?php echo $form->textField($model,'username'); ?>
                <?php echo $form->error($model,'username'); ?>
            </div>

            <div class="row">
                <?php echo $form->labelEx($model,'password'); ?>
                <?php echo $form->passwordField($model,'password'); ?>
                <?php echo $form->error($model,'password'); ?>
            </div>
        <?php } ?>

<!--	<div class="row buttons">-->
<!--		--><?php //echo CHtml::submitButton('Авторизация'); ?>
<!--	</div>-->
    <?

    echo CHtml::ajaxLink(
        ($showFileds)?"Подтвердить СМС":"Авторизация",
            Yii::app()->createUrl('admin/profil/login'),
        array( // ajaxOptions
            'type' =>'POST',
            'beforeSend' => "function(request){
            // Set up any pre-sending stuff like initializing progress indicators
            }",
            'success' => "function(data){
                // handle return data
                if(data=='ok'){
                    location.href='';
                }else{
                    $('#admin_login').html(data);
                }
            }",
            'data' => 'js:$("#admin-form").serialize()',
        ),
        array( //htmlOptions
        'href' => Yii::app()->createUrl('profil/login'),
        //'class' => $class
        )
    );
    ?>

<?php $this->endWidget(); ?>
</div><!-- form -->
