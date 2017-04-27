
<?php //echo $this->headScript()->appendFile('./js/gseguros.poliza.poliza.edit-poliza-automotores.js');?>

<!-- 
//Si es agente no puede modificar fechas y siempre va su nombre como agente
//Nº de Poliza / Premio Compañia / Prima /. Y además tendría que visualizar como se cargaron los valores:
//importe = (cantidad de cuotas x valor cuotas) = Plus + Premio asegurado
-->
<div id="content">

	<fieldset>
		<legend>Poliza Automotores</legend>

		<!--  form action="./entidad/compania/add"  onSubmit="return editForm(this)">-->
		<form id="poliza_poliza" name="poliza_poliza"
			action="./poliza/poliza/view-poliza-automotores"
			onSubmit="return savePolizaAutomotores(this)">
			<input type="hidden" name="save" value=1>

			<h3 id="datos_poliza_poliza_show">Datos de la Poliza</h3>
			<div id="datos_poliza_poliza">
				<table>
				<tr>
						<td>Número de Solicitud:</td>
						<td><input readonly type="text" name="poliza_id"
							value="<?=$this->escape($this->poliza->poliza_id)?>" /></td>
					</tr>
					<tr>
						<td>Número de poliza:</td>
						<td><input type="text" name="numero_poliza"
							value="<?=$this->escape($this->poliza->numero_poliza)?>" /></td>
					<td>Poliza que renueva ID:</td>
						<td><input type="text" name="poliza_poliza_id"
								value="<?=$this->poliza->poliza_poliza_id?>"></input>
						</td>
					</tr>

					<!-- Autocomplente para Asegurados -->
					<tr>
						<td>Asegurado:</td>
						<?if( empty($this->poliza->poliza_id) ):?>
						<td><input readonly id="asegurado_poliza" type="text" name="asegurado"
							value="<?=$this->escape($this->poliza->asegurado_id)?>"></input> <input
							type="hidden" id="asegurado_id" name="asegurado_id" value=""></input>
						</td>
						<?else:?>
						<td><input readonly id="asegurado_poliza" type="text"
							name="asegurado" value="<?=$this->escape($this->asegurado_nombre)?>"></input> <input
							type="hidden" id="asegurado_id" name="asegurado_id"
							value="<?=$this->poliza->asegurado_id?>"></input></td>
							<?endif;?>
					</tr>
					<tr>
						<td>Agente:</td>
						<?if($this->isAgente):?>
						<td><?=$this->escape($this->agente_nombre)?> <input readonly name="agente_id"
							value=<?=$this->agente_id?> type="hidden"></input>
						</td>
						<?else:?>
						<td><select name="agente_id">
								<option value="0">Seleccione Agente</option>
								<? foreach($this->agentes as $row ):?>
								<?if($this->poliza->agente_id==$row['agente_id']):?>
								<option selected value=<?=$this->escape($row['agente_id'])?>>
								<?=$row['nombre']?>
								<?else:?>
								
								<option value=<?=$this->escape($row['agente_id'])?>>
								<?=$row['nombre']?>
								</option>
								<?endif; ?>
								<? endforeach; ?>
						</select> <?endif;?>
						</td>
					</tr>
					<tr>
						<td>Compania Aseguradora:</td>
						<td><select name="compania_id">
								<option value="0">Seleccione Compania</option>
								<? foreach($this->companias as $row ):?>
								<?if($this->poliza->compania_id==$row['compania_id']):?>
								<option selected value=<?=$this->escape($row['compania_id'])?>>
								<?=$row['nombre']?>
								<?else:?>
								
								<option value=<?=$this->escape($row['compania_id'])?>>
								<?=$row['nombre']?>
								</option>
								<?endif; ?>
								<? endforeach; ?>
						</select>
						</td>
					</tr>
					<tr>
						<td>Productor:</td>
						<td><select name="productor_id">
								<option value="0">Seleccione Productor</option>
								<? foreach($this->productores as $row ):?>
								<?if($this->poliza->productor_id==$row['productor_id']):?>
								<option selected value=<?=$this->escape($row['productor_id'])?>>
								<?=$row['nombre']?>
								<?else:?>
								
								<option value=<?=$this->escape($row['productor_id'])?>>
								<?=$row['nombre']?>
								</option>
								<?endif; ?>
								<? endforeach; ?>
						</select>
						</td>
					</tr>
					<tr>
						<td>Cobrador:</td>
						<td><select name="cobrador_id">
								<option value="0">Seleccione Cobrador</option>
								<? foreach($this->cobradores as $row ):?>
								<?if($this->poliza->cobrador_id==$row['cobrador_id']):?>
								<option selected value=<?=$this->escape($row['cobrador_id'])?>>
								<?=$row['nombre']?>
								<?else:?>
								
								<option value=<?=$this->escape($row['cobrador_id'])?>>
								<?=$row['nombre']?>
								</option>
								<?endif; ?>
								<? endforeach; ?>

						</select>
						</td>
					</tr>
				</table>
			</div>
			<hr>
			<h3 id="datos_seguro_automotor_poliza_show">Datos del Seguro</h3>
			<div id="datos_seguro_automotor_poliza">
				<table>
					<tr>
						<td>Fecha de pedido:</td>
						<td><?if($this->isAgente):?> <input readonly type="text"
							id="fecha_pedido_agente_poliza" name="fecha_pedido"
							value="<?=$this->escape($this->poliza->fecha_pedido)?>" />
						</td>
						<?else:?>
						<input readonly type="text" id="fecha_pedido_poliza" name="fecha_pedido"
							value="<?=$this->escape($this->poliza->fecha_pedido)?>" />
						</td>
						<?endif;?>
					</tr>

					<!--  tr>
						<td>Seguimiento:</td>
						<td><select name="tipo_poliza_id">
								<option value="0">Seleccione tipo</option>
						</select>
						</td>
					</tr>-->

					<tr>
						<td>Fecha Vigencia:</td>
						<?if($this->isAgente): ?>
						<td><input readonly type="text" id="fecha_vigencia_agente_poliza"
							name="fecha_vigencia" value="<?=$this->escape($this->poliza->fecha_vigencia)?>" />
						</td>
						<?else:?>
						<td><input readonly type="text" id="fecha_vigencia_poliza"
							name="fecha_vigencia" value="<?=$this->escape($this->poliza->fecha_vigencia)?>" />
						</td>
						<?endif; ?>
					</tr>

					<tr>
						<td>Periodo:</td>
						<td><select id="periodo_id" name="periodo_id">
								<option value="0">Seleccione periodo</option>
								<? foreach($this->periodos as $row ):?>
								<?if($this->poliza->periodo_id==$row['dominio_id']):?>
								<option selected value=<?=$this->escape($row['dominio_id'])?>>
								<?=$row['nombre']?>
								<?else:?>
								
								<option value=<?=$this->escape($row['dominio_id'])?>>
								<?=$row['nombre']?>
								</option>
								<?endif; ?>
								<? endforeach; ?>
						</select>
						</td>
					</tr>



					<!--  tr>
						<td>Plan Comision:</td>
						<td><select name="plan_comision">
								<option value="0">Seleccione Plan</option>
						</select>
						</td>
					<tr>-->
				</table>
			</div>

			<hr>
			<h3 id="detalle_riesgo_automotor_poliza_show">Detalle del Riesgo</h3>
			<div id="detalle_riesgo_automotor_poliza">
				<table>

					<tr>
						<td>Tipo de Vehiculo:</td>
						<td><select name="tipo_vehiculo_id" id="tipo_vehiculo_id">
								<option value="0">Seleccione Tipo de Vehiculo</option>
								<? foreach($this->tipo_vehiculo_automotores as $row ):?>
								<?if($this->poliza_detalle->tipo_vehiculo_id==$row['dominio_id']):?>
								<option selected value=<?=$this->escape($row['dominio_id'])?>>
								<?=$row['nombre']?>
								<?else:?>
								
								<option value=<?=$this->escape($row['dominio_id'])?>>
								<?=$row['nombre']?>
								</option>
								<?endif; ?>
								<? endforeach; ?>
						</select>
						</td>
					</tr>
					<tr>
						<td>Tipo de Cobertura:</td>
						<td><select name="tipo_cobertura_id" id="tipo_cobertura_id">
								<option value="0">Seleccione Tipo de Cobertura</option>
								<? foreach($this->tipo_cobertura_automotores as $row ):?>
								<?if($this->poliza_detalle->tipo_cobertura_id==$row['dominio_id']):?>
								<option selected value=<?=$this->escape($row['dominio_id'])?>>
								<?=$row['nombre']?>
								<?else:?>
								
								<option value=<?=$this->escape($row['dominio_id'])?>>
								<?=$row['nombre']?>
								</option>
								<?endif; ?>
								<? endforeach; ?>
						</select>
						</td>
					</tr>
					<tr>
						<td colspan="2"><b>Datos del vehículo</b></td>
					</tr>
					<tr>
						<td>Año:</td>
						<td><input readonly id="anio_automotor" name="anio_automotor"
							value="<?=$this->escape($this->poliza_detalle->anio)?>"></input>
						</td>
						<td>Marca:</td>
						<td><input readonly id="marca_automotor" name="marca_automotor"
							value="<?=$this->escape($this->poliza_detalle->marca)?>"></input>
						</td>
					</tr>
					<tr>
						<td>Tipo:</td>
						<td><input readonly id="tipo_automotor" name="tipo_automotor"
							value="<?=$this->escape($this->poliza_detalle->tipo)?>"></input>
						</td>
						<td>Modelo:</td>
						<td><input readonly id="modelo_automotor" name="modelo_automotor"
							value="<?=$this->escape($this->poliza_detalle->modelo)?>"></input>
						</td>
					</tr>
					<tr>
						<td>Color:</td>
						<td><input readonly id="color_automotor" name="color_automotor"
							value="<?=$this->escape($this->poliza_detalle->color)?>"></input>
						</td>
						<td>Nro Patente:</td>
						<td><input readonly id="patente_automotor" name="patente_automotor"
							value="<?=$this->escape($this->poliza_detalle->patente)?>"></input></td>
						<td>Cilindrada:</td>
						<td><input readonly id="cilindrada_automotor" name="cilindrada_automotor"
							value="<?=$this->escape($this->poliza_detalle->cilindrada_id)?>"></input></td>
					</tr>
					<tr>
						<td>Serial Chasis:</td>
						<td><input readonly id="serial_c_automotor" name="serial_c_automotor"
							value="<?=$this->escape($this->poliza_detalle->serial_carroceria)?>"></input></td>
						<td>Serial Automotor:</td>
						<td><input readonly id="serial_automotor" name="serial_automotor"
							value="<?=$this->escape($this->poliza_detalle->serial_motor)?>"></input>
						</td>
						<td>Accesorios:</td>
						<td><input readonly id="accesorios_automotor" name="accesorios_automotor"
							value="<?=$this->escape($this->poliza_detalle->accesorios)?>"></input>
						</td>
					</tr>

					<tr>
						<td>Uso Vehiculo:</td>
						<td><input readonly id="uso_automotor" name="uso_automotor"
							value="<?=$this->escape($this->poliza_detalle->uso_vehiculo)?>"></input>
						</td>
						<td>Capacidad:</td>
						<td><select id="capacidad_automotor" name="capacidad_automotor">
								<option value="0">Seleccione Capacidad</option>
								<? foreach($this->capacidad_automotores as $row ):?>
								<?if($this->poliza_detalle->capacidad_id==$row['dominio_id']):?>
								<option selected value=<?=$this->escape($row['dominio_id'])?>>
								<?=$row['nombre']?>
								<?else:?>
								
								<option value=<?=$this->escape($row['dominio_id'])?>>
								<?=$row['nombre']?>
								</option>
								<?endif; ?>
								<? endforeach; ?>

						</select>
						</td>
						<td>Pasajeros:</td>
						<td><select id="pasajeros_automotor" name="pasajeros_automotor">
								<option value="0">Seleccione Pasajeros</option>
								<? foreach($this->pasajeros_automotores as $row ):?>
								<?if($this->poliza_detalle->pasajeros_id==$row['dominio_id']):?>
								<option selected value=<?=$this->escape($row['dominio_id'])?>>
								<?=$row['nombre']?>
								<?else:?>
								
								<option value=<?=$this->escape($row['dominio_id'])?>>
								<?=$row['nombre']?>
								</option>
								<?endif; ?>
								<? endforeach; ?>
						</select></td>
					</tr>
					<tr>
						<td>Flota Nro Vehiculos:</td>
						<td><select id="flota_automotor" name="flota_automotor">
								<option value="0">Seleccione Flota</option>
								<? foreach($this->capacidad_automotores as $row ):?>
								<?if($this->poliza_detalle->flota_id==$row['dominio_id']):?>
								<option selected value=<?=$row['dominio_id']?>>
								<?=$row['nombre']?>
								<?else:?>
								
								<option value=<?=$row['dominio_id']?>>
								<?=$row['nombre']?>
								</option>
								<?endif; ?>
								<? endforeach; ?>
						</select></td>
					</tr>

					<tr>
						<td colspan="2"><b>Titulo de Propiedad</b></td>
						<td></td>
					</tr>
					<tr>
						<td>Fecha Titulo:</td>
						<td><input readonly id="fecha_titulo_automotor_poliza"
							name="fecha_titulo_automotor"
							value="<?=$this->poliza_detalle->fecha_titulo?>"></input></td>
						<td>Titular:</td>
						<td><input readonly id="titular_automotor" name="titular_automotor"
							value="<?=$this->poliza_detalle->titular?>"></input></td>
						<td>Nro Certificado:</td>
						<td><input readonly id="numero_certificado_automotor"
							name="numero_certificado_automotor"
							value="<?=$this->poliza_detalle->numero_certificado?>"></input></td>
					</tr>


					<tr>
						<td colspan="2"><b>Otros Datos</b></td>
						<td></td>
					</tr>
					<tr>
						<td>Estado Vehiculo:</td>
						<td><select id="estado_vehiculo_automotor"
							name="estado_vehiculo_automotor">
								<option value="0">Seleccione Estado</option>
								<? foreach($this->estado_vehiculo_automotores as $row ):?>
								<?if($this->poliza_detalle->estado_vehiculo_id==$row['dominio_id']):?>
								<option selected value=<?=$row['dominio_id']?>>
								<?=$row['nombre']?>
								<?else:?>
								
								<option value=<?=$row['dominio_id']?>>
								<?=$row['nombre']?>
								</option>
								<?endif; ?>
								<? endforeach; ?>

						</select>
						</td>
						<td>Estado Luces:</td>
						<td><select id="estado_luces_automotor"
							name="estado_luces_automotor">
								<option value="0">Seleccione Estado</option>
								<? foreach($this->estado_vehiculo_automotores as $row ):?>
								<?if($this->poliza_detalle->estado_luces_id==$row['dominio_id']):?>
								<option selected value=<?=$row['dominio_id']?>>
								<?=$row['nombre']?>
								<?else:?>
								
								<option value=<?=$row['dominio_id']?>>
								<?=$row['nombre']?>
								</option>
								<?endif; ?>
								<? endforeach; ?>

						</select>
						</td>

						<td>Estado Motor:</td>
						<td><select id="estado_motor_automotor"
							name="estado_motor_automotor">
								<option value="0">Seleccione Estado</option>
								<? foreach($this->estado_vehiculo_automotores as $row ):?>
								<?if($this->poliza_detalle->estado_motor_id==$row['dominio_id']):?>
								<option selected value=<?=$row['dominio_id']?>>
								<?=$row['nombre']?>
								<?else:?>
								
								<option value=<?=$row['dominio_id']?>>
								<?=$row['nombre']?>
								</option>
								<?endif; ?>
								<? endforeach; ?>

						</select>
						</td>

					</tr>
					<tr>
						<td>Estado Carroceria:</td>
						<td><select id="estado_carroceria_automotor"
							name="estado_carroceria_automotor">
								<option value="0">Seleccione Estado</option>
								<? foreach($this->estado_vehiculo_automotores as $row ):?>
								<?if($this->poliza_detalle->estado_vehiculo_id==$row['dominio_id']):?>
								<option selected value=<?=$row['dominio_id']?>>
								<?=$row['nombre']?>
								<?else:?>
								
								<option value=<?=$row['dominio_id']?>>
								<?=$row['nombre']?>
								</option>
								<?endif; ?>
								<? endforeach; ?>

						</select>
						</td>

						<td>Estado de Cubiertas:</td>
						<td><select id="estado_cubiertas_automotor"
							name="estado_cubiertas_automotor">
								<option value="0">Seleccione Estado</option>
								<? foreach($this->estado_vehiculo_automotores as $row ):?>
								<?if($this->poliza_detalle->estado_cubiertas_id==$row['dominio_id']):?>
								<option selected value=<?=$row['dominio_id']?>>
								<?=$row['nombre']?>
								<?else:?>
								
								<option value=<?=$row['dominio_id']?>>
								<?=$row['nombre']?>
								</option>
								<?endif; ?>
								<? endforeach; ?>

						</select>
						</td>


						<td>Tipo Combustion:</td>
						<td><select id="tipo_combustion_automotor"
							name="tipo_combustion_automotor">
								<option value="0">Seleccione Tipo Combustion</option>
								<? foreach($this->tipo_combustion_automotores as $row ):?>
								<?if($this->poliza_detalle->tipo_combustion_id==$row['dominio_id']):?>
								<option selected value=<?=$row['dominio_id']?>>
								<?=$row['nombre']?>
								<?else:?>
								
								<option value=<?=$row['dominio_id']?>>
								<?=$row['nombre']?>
								</option>
								<?endif; ?>
								<? endforeach; ?>
						</select>
						</td>
						<td>Sistema de Seguridad:</td>
						<td><select id="sistema_seguridad_automotor"
							name="sistema_seguridad_automotor">
								<option value="0">Seleccione Sistema de Seguridad</option>
								<? foreach($this->sistema_seguridad_automotores as $row ):?>
								<?if($this->poliza_detalle->sistema_seguridad_id==$row['dominio_id']):?>
								<option selected value=<?=$row['dominio_id']?>>
								<?=$row['nombre']?>
								<?else:?>
								
								<option value=<?=$row['dominio_id']?>>
								<?=$row['nombre']?>
								</option>
								<?endif; ?>
								<? endforeach; ?>
						</select>
						</td>
					</tr>
					<tr>
						<td>Acreedor Prendario:</td>
						<td><input readonly id="acreedor_prendario_automotor"
							name="acreedor_prendario_automotor"
							value="<?=$this->poliza_detalle->acreedor_prendario?>"></input></td>
						<td>Otros:</td>
						<td><input readonly id="otros_automotor" name="otros_automotor"
							value="<?=$this->poliza_detalle->otros?>"></input>
						</td>
					</tr>

				</table>
			</div>


			<hr>
			<h3 id="valores_seguro_automotor_poliza_show">Valores del seguro</h3>
			<div id="valores_seguro_automotor_poliza">
				<table>
					<tr>
						<td>Forma de Pago:</td>
					</tr>
					<tr id="forma_pago">
						<td>Pago:</td>
						<td><select name="forma_pago_id" id="forma_pago_id">
								<option value="1">Forma Pago</option>
								<? foreach($this->forma_pagos as $row ):?>
								<?if($this->detalle_pago->forma_pago_id==$row['dominio_id']):?>
								<option selected value=<?= $row['dominio_id']?>>
								<?=$row['nombre']?>
								<?else: ?>
								
								<option value=<?= $row['dominio_id']?>>
								<?=$row['nombre']?>
								</option>
								<?endif;?>
								<? endforeach; ?>
						</select>
						</td>
					</tr>
					<tr id="cuotas">
						<td>Cuotas:</td>
						<td><select name="cuotas">
								<option value="0">Cuotas</option>
								<? foreach($this->cuotas as $row ):?>

								<?if($this->cantidad_cuotas==$row['dominio_id']):?>
								<option selected value=<?= $row['dominio_id']?>>
								<?=$row['nombre']?>
								<?else: ?>
								
								<option value=<?= $row['dominio_id']?>>
								<?=$row['nombre']?>
								</option>
								<?endif;?>
								</option>
								<? endforeach; ?>
						</select>
						</td>
						<td>Valor Cuota:</td>
						<td><input readonly id="valor_cuota" name="valor_cuota" value="<?=$this->valor_cuotas ?>"></td>
					</tr>
					<tr>
						<td>Monto Asegurado:</td>
						<td><input readonly type="text" name="monto_asegurado"
							value="<?=$this->escape($this->poliza_valores->monto_asegurado)?>" />
						</td>
					</tr>
					<tr>
						<td>Moneda:</td>
						<td><select name="moneda_id">
								<option value="0">Seleccione Moneda</option>
								<? foreach($this->monedas as $row ):?>
								<?if($this->poliza_valores->moneda_id==$row['dominio_id']):?>
								<option selected value=<?=$row['dominio_id']?>>
								<?=$row['nombre']?>
								<?else:?>
								
								<option value=<?=$row['dominio_id']?>>
								<?=$row['nombre']?>
								</option>
								<?endif; ?>
								<? endforeach; ?>
						</select>
						</td>
					</tr>
					<tr>
						<td>Iva:</td>
						<td><input readonly type="text" name="iva"
							value="<?=$this->escape($this->poliza_valores->iva)?>" /></td>
					</tr>
					<tr>
					<td>Prima Comision:</td>
					<td><input readonly type="text" name="prima_comision"
						value="<?=$this->escape($this->poliza_valores->prima_comision)?>" />
					</td>
					</tr>
					<tr>
						<td>Prima Tarifa:</td>
						<td><input readonly type="text" name="prima_tarifa"
							value="<?=$this->escape($this->poliza_valores->prima_tarifa)?>" />
						</td>

					</tr>


					<tr>
						<td>Premio Compania:</td>
						<td><input  type="text" name="premio_compania" id="premio_compania"
							value="<?=$this->escape($this->poliza_valores->premio_compania)?>" />
						</td>

					</tr>

					<tr>
						<td>Premio Asegurado:</td>
						<td><input  type="text" name="premio_asegurado" id="premio_asegurado"
							value="<?=$this->escape($this->poliza_valores->premio_asegurado)?>" />
						</td>
					</tr>
					<tr>
						<td>Plus:</td>
						<td><input  type="text" name="plus" id="plus"
							value="<?=$this->escape($this->poliza_valores->plus)?>" /></td>
					</tr>
					<tr>
						<td>Importe:</td>
						<td><input readonly type="text" id="importe"
							value=<?=$this->importe?> /></td>
					</tr>
					<tr>
						<td>Observaciones:</td>
						<td></td>
					</tr>
					<tr>
						<td>Asegurado:</td>
						<td><textarea name="observaciones_asegurado">
						<?=$this->escape($this->poliza->observaciones_asegurado)?>
						</textarea>
						</td>
					</tr>
					<tr>
						<td>Compania</td>
						<td><textarea name="observaciones_compania">
						<?=$this->escape($this->poliza->observaciones_compania)?>
						</textarea>
						</td>
					</tr>
				</table>
			</div>
			<!-- `moneda_id`, `monto_asegurado`, `iva`, `prima_comision`, `prima_tarifa`, 
		 * `premio_compania`, `premio_asegurado`, `plus` -->

			<hr>


			<!--  chequear los estados de la poliza -->
			<input type="submit" name="save_poliza" value="Guardar Cambios"></input>

			<input type="button" name="Cancel" onclick="" value="Cancelar" />
		</form>
	</fieldset>
</div>

