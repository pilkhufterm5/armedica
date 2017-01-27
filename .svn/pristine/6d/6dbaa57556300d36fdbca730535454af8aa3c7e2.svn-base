<?php
/* @var $this RhCrmcontactoController */
/* @var $model RhCrmcontacto */
/* @var $form CActiveForm */
/*<script src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/jquery-ui-1.10.4.custom.min.js"></script>*/
?>
<!-- <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl;?>/themes/found/css/content/styles.css"> -->


<script>
    $(document).on('ready',function(){
        $("#Contacto_idProspecto").select2();

         $('#Contacto_fechaAlta').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });

    });
</script>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'rh-crmcontacto-form',
	'htmlOptions'=>array('data-abide' => '' ),
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>
    <?php FB::INFO($model,'___________________________MODEL'); ?>


	<?php echo $form->errorSummary($model); ?>

	<fieldset>
    		<legend>Datos de Contacto</legend>
		<div class="row">
			<div class="small-12 large-6 columns">
				<label for="">Nombre <small>*</small> </label>
				<?php echo $form->textField($model,'nombre',array('required'=>'required','maxlength'=>30)); ?>
				<?php echo $form->error($model,'nombre'); ?>

			</div>
			<div class="small-12 large-6 columns">
				<label for="">Apellido Paterno <small>*</small> </label>
				<?php echo $form->textField($model,'apellidoPaterno',array('required'=>'required','maxlength'=>30)); ?>
				<?php echo $form->error($model,'apellidoPaterno'); ?>
			</div>
		</div>

		<div class="row">
			<div class="small-12 large-6 columns">
				<label for="">Apellido materno</label>
				<?php echo $form->textField($model,'apellidoMaterno',array('maxlength'=>30)); ?>
				<?php echo $form->error($model,'apellidoMaterno'); ?>
			</div>
			<div class="small-12 large-6 columns">
				<label for="">E-mail <small>*</small> </label>
				<?php echo $form->textField($model,'email',array('required'=>'required', 'maxlength'=>100)); ?>
				<?php echo $form->error($model,'email'); ?>
			</div>
		</div>

		<div class="row">
			<div class="small-6 columns">
				<label>Teléfono <small>*</small></label>
				<?php echo $form->textField($model,'telefono',array('required'=>'required', 'maxlength'=>30)); ?>
				<?php echo $form->error($model,'telefono'); ?>
			</div>
			<div class="small-6 columns">
				<label for="">Celular</label>
				<?php echo $form->textField($model,'celular'); ?>
				<?php echo $form->error($model,'celular'); ?>
			</div>
		</div>
		<div class="row">
			<div class="small-6 columns">
				<label for="">Tel. Empresa</label>
				<?php echo $form->textField($model,'tel_empresa',array('size'=>30,'maxlength'=>30)); ?>
				<?php echo $form->error($model,'tel_empresa'); ?>
			</div>
			<div class="row">
			<?php if($model->isNewRecord){ ?>
			<div class="small-6 columns">
				<label for="Contacto_idProspecto">Prospecto</label>
				<?php echo $form->dropDownList($model,'idProspecto',$ListaLeads); ?>
				<?php echo $form->error($model,'idProspecto'); ?>
			</div>
			<?php } ?>
		</div>
		</div>

		<div class="row">
			<div class="small-12 columns">
				<label for="">Descripción</label>
				<?php echo $form->textField($model,'descripcion',array('size'=>60,'maxlength'=>200)); ?>
				<?php echo $form->error($model,'descripcion'); ?>
			</div>
		</div>

		<div class="row">
			<div class="small-12 large-6 columns">
				<label for="">Contactar por E-mail</label>
				<?php echo $form->checkBox($model,'contactarPorEmail'); ?>
				<?php echo $form->error($model,'contactarPorEmail'); ?>
			</div>
			<div class="small-12 large-6 columns">
				<label for="">Contactar por Celular </label>
				<?php echo $form->checkBox($model,'contactarPorCelular'); ?>
				<?php echo $form->error($model,'contactarPorCelular'); ?>
			</div>
		</div>

		<div class="row">
		<div class="small-6 columns">
				<label for="">Estatus</label>
				<?php echo $form->checkBox($model,'estatus', array('checked'=>'checked')); ?>
				<?php echo $form->error($model,'estatus'); ?>
			</div>

		</div>
	</fieldset>

	<fieldset>
        <legend>Direccion Principal</legend>
			<div class="row">
				<div class="small-12 large-6 columns">
					<label>Calle:</label>
					<?php echo $form->textField($model,'direccion1',array('maxlength'=>85)); ?>
					<?php echo $form->error($model,'direccion1'); ?>
				</div>
				<div class="small-12 large-6 columns">
					<label>Num. Exterior </label>
					<?php echo $form->textField($model,'direccion2',array('maxlength'=>85)); ?>
					<?php echo $form->error($model,'direccion2'); ?>
				</div>
			</div>

			<div class="row">
				<div class="small-12 large-6 columns">
					<label>Num. Interior </label>
					<?php //echo $form->labelEx($model,'direccion3'); ?>
					<?php echo $form->textField($model,'direccion3',array('maxlength'=>85)); ?>
					<?php echo $form->error($model,'direccion3'); ?>
				</div>
				<div class="small-12 large-6 columns">
					<label>Colonia </label>
					<?php //echo $form->labelEx($model,'direccion4'); ?>
					<?php echo $form->textField($model,'direccion4',array('maxlength'=>85)); ?>
					<?php echo $form->error($model,'direccion4'); ?>
				</div>
			</div>

			<div class="row">
				<div class="small-12 large-6 columns">
					<label>Localidad </label>
					<?php //echo $form->labelEx($model,'direccion5'); ?>
					<?php echo $form->textField($model,'direccion5',array('maxlength'=>85)); ?>
					<?php echo $form->error($model,'direccion5'); ?>
				</div>
				<div class="small-12 large-6 columns">
					<label>Referencia</label>
					<?php //echo $form->labelEx($model,'direccion6'); ?>
					<?php echo $form->textField($model,'direccion6',array('maxlength'=>85)); ?>
					<?php echo $form->error($model,'direccion6'); ?>
				</div>
			</div>

			<div class="row">
				<div class="small-12 large-6 columns">
					<label>Municipio</label>
					<?php //echo $form->labelEx($model,'direccion7'); ?>
					<?php echo $form->textField($model,'direccion7',array('maxlength'=>85)); ?>
					<?php echo $form->error($model,'direccion7'); ?>
				</div>
				<div class="small-12 large-6 columns">
					<label>Estado</label>
					<?php //echo $form->labelEx($model,'direccion8'); ?>
					<?php echo $form->textField($model,'direccion8',array('maxlength'=>85)); ?>
					<?php echo $form->error($model,'direccion8'); ?>
				</div>
			</div>

			<div class="row">
				<div class="small-12 large-6 columns">
					<label>Pais</label>
					<?php //echo $form->labelEx($model,'direccion9'); ?>
					<?php echo $form->textField($model,'direccion9',array('maxlength'=>85)); ?>
					<?php echo $form->error($model,'direccion9'); ?>
				</div>
				<div class="small-12 large-6 columns">
					<label>Código Postal</label>
					<?php //echo $form->labelEx($model,'direccion10'); ?>
					<?php echo $form->textField($model,'direccion10',array('required'=>'required','maxlength'=>85)); ?>
					<?php echo $form->error($model,'direccion10'); ?>
				</div>
			</div>
	</fieldset>

    <fieldset>
    		<legend>Social Media</legend>
			<div class="row">
				<div class="small-12 large-6 columns">
					<?php echo $form->labelEx($model,'skype'); ?>
					<?php echo $form->textField($model,'skype',array('size'=>60,'maxlength'=>85)); ?>
					<?php echo $form->error($model,'skype'); ?>
				</div>
				<div class="small-12 large-6 columns">
					<?php echo $form->labelEx($model,'facebook'); ?>
					<?php echo $form->textField($model,'facebook',array('size'=>45,'maxlength'=>45)); ?>
					<?php echo $form->error($model,'facebook'); ?>
				</div>
			</div>

			<div class="row">
				<div class="small-12 large-6 columns">
					<?php echo $form->labelEx($model,'twitter'); ?>
					<?php echo $form->textField($model,'twitter',array('size'=>45,'maxlength'=>45)); ?>
					<?php echo $form->error($model,'twitter'); ?>
				</div>
				<div class="small-12 large-6 columns">
					<?php echo $form->labelEx($model,'googlePlus'); ?>
					<?php echo $form->textField($model,'googlePlus',array('size'=>45,'maxlength'=>45)); ?>
					<?php echo $form->error($model,'googlePlus'); ?>
				</div>
			</div>

    </fieldset>

            <div class="large-12 columns">
                <div class="row buttons">
                        <?php echo CHtml::submitButton($model->isNewRecord ? 'Crear' : 'Guardar',array('class'=>'button')); ?>
                </div>
            </div>
<?php $this->endWidget(); ?>

</div><!-- form -->
