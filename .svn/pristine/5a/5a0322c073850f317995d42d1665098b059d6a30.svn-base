<script type="text/javascript">
    $(document).on('ready',function() {

		$("#source_id option[value='<?=$model->source_id?>']").attr('selected',true);

        $("#Lead_RFC").blur(function(){
            vRfcs($(this));
        });
        $("#Lead_email").blur(function(){
            emailValido($(this).val());
        })
    });


</script>

<?php FB::INFO($model->source_id, 'Origen del prospecto'); ?>
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

	<?php echo $form->errorSummary($model); ?>

	<fieldset>
    		<legend>Datos de Prospecto</legend>
		<div class="row">
			<div class="large-12 columns">
				<div class="small-12 large-6 columns">
					<label>* Nombre:</label>
					<?php echo $form->textField($model,'nombre',array('required'=>'required','maxlength'=>30)); ?>
					<!-- <small class="error">El nombre es requerido.</small> -->
				</div>
				<div class="small-12 large-6 columns">
				<label for="">* Apellido Paterno:</label>
					<?php echo $form->textField($model,'apellidoPaterno',array('required'=>'required','maxlength'=>30)); ?>
					<?php echo $form->error($model,'apellidoPaterno'); ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="large-12 columns">
				<div class="small-12 large-6 columns">
					<label>Apellido Materno:</label>
					<?php echo $form->textField($model,'apellidoMaterno',array('maxlength'=>30)); ?>
					<?php echo $form->error($model,'apellidoMaterno'); ?>
				</div>
				<div class="small-12 large-6 columns">
					<label>* E-mail:</label>
					<?php echo $form->emailField($model,'email',array('required'=>'required','maxlength'=>100)); ?>
					<?php echo $form->error($model,'email'); ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="large-12 columns">
				<div class="small-6 columns">
					<label>* Teléfono:</label>
					<?php echo $form->textField($model,'telefono',array('required'=>'required','maxlength'=>30)); ?>
					<?php echo $form->error($model,'telefono'); ?>
				</div>
				<div class="small-6 columns">
					<label>Celular:</label>
					<?php echo $form->textField($model,'celular',array('maxlength'=>30)); ?>
					<?php echo $form->error($model,'celular'); ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="large-12 columns">
				<div class="small-6 columns">
					<label>Tipo de persona:</label>
					<select name="tipopersona" id="tipopersona">
						<option value="MORAL">MORAL</option>
						<option value="FISICA">FISICA</option>
					</select>
				</div>
				<div class="small-6 columns">
					<label>RFC:</label>
					<?php echo $form->textField($model,'RFC'); ?>
					<?php echo $form->error($model,'RFC'); ?>
				</div>
			</div>
			<div class="large-12 columns">
				<div class="small-3 columns">
                    <?php echo $form->checkBox($model,'contactarPorEmail'); ?>
					<label>Contactar por email</label>
					<?php echo $form->error($model,'contactarPorEmail'); ?>
				</div>
				<div class="small-3 columns">
                    <?php echo $form->checkBox($model,'contactarPorCelular'); ?>
                    <label>Contactar por celular</label>
					<?php echo $form->error($model,'contactarPorCelular'); ?>
				</div>
                <div class="large-6 small-12 columns">
                    <label>Empresa:</label>
                    <?php echo $form->textField($model,'company'); ?>
                    <?php echo $form->error($model,'company'); ?>
                </div>
			</div>
		</div>

		<div class="row">
			<div class="large-12 columns">
				<div class="small-12 large-6 columns">
					<label>Estatus del prospecto</label>
					<?php echo $form->dropDownList($model,'status_prospecto', array(
						'ACTIVO' => 'ACTIVO',
						'CERRADO GANADO' => 'CERRADO GANADO',
						'CERRADO CANCELADO'=>'CERRADO CANCELADO',
						'CERRADO PERDIDO'=>'CERRADO PERDIDO'
						)); ?>
					<?php echo $form->error($model,'status_prospecto'); ?>
				</div>
                <div class="large-6 small-12 columns">
                    <label>Origen del Prospecto:</label>
                    <select name="Lead[source_id]" id="source_id">
                        <option value="">Seleccione una opción</option>
                        <?php foreach($SourceList as $idx => $value){ ?>
                        <option value="<?=$idx?>"><?=$value?></option>
                        <?php } ?>
                    </select>
                    <script type="text/javascript">
                    	$("#source_id option[value='<?=$model->source_id?>']").attr('selected',true);
                    </script>
                </div>
			</div>
		</div>

		<div class="row">
			<div class="large-12 columns">
				<div class="small-12 large-12 columns">
					<label>Descripción</label>
					<?php echo $form->textArea($model,'descripcion',array('size'=>60,'maxlength'=>200, 'rows'=>4)); ?>
					<?php echo $form->error($model,'descripcion'); ?>
				</div>
			</div>
		</div>
	</fieldset>

	<fieldset>
        <legend>Direccion Principal</legend>
			<div class="row">
				<div class="small-12 large-6 columns">
					<label>Calle</label>
					<?php //echo $form->labelEx($model,'direccion1'); ?>
					<?php echo $form->textField($model,'direccion1',array('maxlength'=>85 )); ?>
					<?php echo $form->error($model,'direccion1'); ?>
				</div>
				<div class="small-12 large-6 columns">
					<label>Número Exterior </label>
					<?php //echo $form->labelEx($model,'direccion2'); ?>
					<?php echo $form->textField($model,'direccion2',array('maxlength'=>85)); ?>
					<?php echo $form->error($model,'direccion2'); ?>
				</div>
			</div>

			<div class="row">
				<div class="small-12 large-6 columns">
					<label>Número Interior </label>
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
					<label>Referencia </label>
					<?php //echo $form->labelEx($model,'direccion6'); ?>
					<?php echo $form->textField($model,'direccion6',array('maxlength'=>85)); ?>
					<?php echo $form->error($model,'direccion6'); ?>
				</div>
			</div>

			<div class="row">
				<div class="small-12 large-6 columns">
					<label>Municipio </label>
					<?php //echo $form->labelEx($model,'direccion7'); ?>
					<?php echo $form->textField($model,'direccion7',array('maxlength'=>85)); ?>
					<?php echo $form->error($model,'direccion7'); ?>
				</div>
				<div class="small-12 large-6 columns">
					<label>Estado </label>
					<?php //echo $form->labelEx($model,'direccion8'); ?>
					<?php echo $form->textField($model,'direccion8',array('maxlength'=>85)); ?>
					<?php echo $form->error($model,'direccion8'); ?>
				</div>
			</div>

			<div class="row">
				<div class="small-12 large-6 columns">
					<label>País </label>
					<?php //echo $form->labelEx($model,'direccion9'); ?>
					<?php echo $form->textField($model,'direccion9',array('maxlength'=>85)); ?>
					<?php echo $form->error($model,'direccion9'); ?>
				</div>
				<div class="small-12 large-6 columns">
					<label>Código Postal </label>
					<?php //echo $form->labelEx($model,'direccion10'); ?>
					<?php echo $form->textField($model,'direccion10',array('maxlength'=>85)); ?>
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
