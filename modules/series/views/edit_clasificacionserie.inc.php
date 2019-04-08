<?php
$serieServices = new SeriesServices($db);
$clasification = $serieServices->GetClasificationsBySerieAndCode($_REQUEST["idSerie"], $_REQUEST["oldClasificationCode"]);
$serieList = $serieServices->GetSeriesList();
?>
<script type="text/javascript">
$(document).ready(function() {
    $("#EditForm").validate({
        rules: {
            Serie : {
                required: true
            },
            clasificationCode: {
                required: true
            },
            clasificationName: {
                required: true,
                minlength: 5
            }
        },
        messages: {
            Serie : {
                required: "* Debe seleccionar la serie."
            },
            clasificationCode: {
                required: "* Debe ingresar el codigo."
            },
            clasificationName: {
                required: "* Debe ingresar el nombre.",
                minlength: "* El nombre debe ser mayor a 5 caracteres."
            }
        }
    });
});

function changeSerie(idSerie) {
    $("#idSerie").val(idSerie);
}
</script>
<h2>Editar Clasificaci&oacute;n</h2>
<form action="home.php" method="post" id="EditForm">
	<input type="hidden" name="q" value="modules/series/views/admin_clasificacionserie">
	<input type="hidden" name="controllerName" value="seriesController" />
	<input type="hidden" name="callback" value="EditClasificacionSerie">
	<input type="hidden" name="oldClasificationCode" value="<?=$clasification->idClasificacion?>">
	<input type="hidden" name="idSerie" id="idSerie" value="<?=$clasification->idSerie?>">
	<table width="100%" border="0" cellpadding="3" cellspacing="0" class="borde_tab">
		
		<tr>
			<td colspan="2" align="center" class="listado2_center">
				Clasificaci&oacute;n
			</td>
		</tr>
		<tr>
			<td class="titulos5" width="50%" align="right">
      			C&oacute;digo Serie
      		</td>
			<td width="50%" align="left">
	      		<select class="select" id="Serie" name="Serie" onchange="changeSerie(this.value)" <? if($clasification->idClasificacion > 0) echo "disabled='disabled'"?>>
                            <option value="">-- Seleccione --</option>
	      		<?php 
	      			foreach ($serieList as $serie)
	      			{
	      				if($_REQUEST["idSerie"] == $serie->code)
	      				{
	      					$selected = "Selected";
	      				}
	      				else {
	      					$selected = "";
	      				}
	      				echo "<option value=".$serie->code." ".$selected.">".$serie->name."</option>";
	      			}
	      		?>
	      		</select>
	      	</td>
      	</tr>
		<tr>
			<td class="titulos5" width="50%" align="right">
				Codigo clasificaci&oacute;n
			</td>
			<td width="50%" align="left">
				<input type="text" name="clasificationCode" value="<?=$clasification->idClasificacion?>">
			</td>
		</tr>
		<tr>
			<td class="titulos5" width="50%" align="right">
				Nombre clasificaci&oacute;n
			</td>
			<td width="50%" align="left">
				<input type="text" name="clasificationName" value="<?=$clasification->name?>">
			</td>
		</tr>
		<tr>
                    <td align="center" colspan="2" height="40">
				<input class="botones" type="submit" value="Guardar">&nbsp;&nbsp;&nbsp;
                                <input class="botones" type="button" onclick="history.back();" value="Volver">
			</td>
		</tr>
	</table>
</form>