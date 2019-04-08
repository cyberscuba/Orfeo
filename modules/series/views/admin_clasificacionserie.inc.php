<?php 
	$serieServices = new SeriesServices($db);
	$serieList = $serieServices->GetSeriesList();
	$clasificationsList = $controller->ViewData["clasifications"];
?>
<div class="messages">
    <?
    echo WebContext::getContext()->printMessagesAsDiv(CONFIRMATION, "MensajeConfirmacion");
    echo WebContext::getContext()->printMessagesAsDiv(ERROR, "MensajeError");
    echo WebContext::getContext()->printMessagesAsDiv(WARNING, "MensajeAlerta");
    ?>
</div>
<div>
	<form action="home.php" method="post">
		<input type="hidden" name="q" value="modules/series/views/admin_clasificacionserie"> 
		<input type="hidden" name="controllerName" value="seriesController" />
		<input type="hidden" name="callback" value="AdminClasifications">
		<h2>Administrar Series</h2>
		<table width="100%" border="0" cellspacing="3" cellpadding="0" class="borde_tab">
				<tr>
	      			<td><table width="100%" border="0" cellpadding="3" cellspacing="0" class="borde_tab">
	      					<tr>
	      						<td colspan="2" align="center" class="listado2_center">CLASIFICACI&Oacute;N SERIE</td>
	      					</tr>
	      					<tr>
	      						<td><table width="100%" border="0" cellpadding="3" cellspacing="0" class="borde_tab">
	      							<tr>
	      								<td class="titulos5" width="25%" align="right">
	      									C&oacute;digo Serie
	      								</td>
	      								<td width="25%" align="left">
	      									<select class="select" name="idSerie" onchange="submit()">
	      									<option value="0">-- Selecciones --</option>
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
                                                                        <td class="titulos5" width="25%" align="right">
	      									C&oacute;digo Serie
	      								</td>
	      								<td width="25%" align="left">
	      									<input type="text" name="code" value="<?=$_REQUEST['code']?>"/>
                                                                                <input type="submit" value="Buscar..." class="botones"/>
	      								</td>
	      							</tr>
	      						</table>
	      						</td>
	      					</tr>
	      				</table>
	      			</td>
	      		</tr>
	      		<tr>
	      			<td>
	      			<div align="center" class="listado2_center series_title">CLASIFICACI&Oacute;N SERIES</div>
                                <div align="right" class="listado2_center series_add"><a href="home.php?q=modules/series/views/edit_clasificacionserie"><img src="./imagenes/new.png" title="Nueva Clasificaci&oacute;n" border="0"/></a></div>
                                <table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#E5E5E5" class="borde_tab">
	      					<tr>
	      						<td class="titulo1" width="15%" align="left">C&oacute;digo</td>
	      						<td class="titulo1" width="60%" align="left">Descripci&oacute;n</td>
                                                        <td class="titulo1" width="10%" align="left">Serie</td>
	      						<td class="titulo1" width="15%" align="left">Opciones</td>
	      					</tr>
	      					<?php 
	      					if(is_array($clasificationsList)){
	      						$cont = 1;
	      						foreach ($clasificationsList as $clasification)
	      						{
                                                            $css = ($cont%2) ? "row1" : "row2";
	      							?>
	      							<tr class="<?=$css?>">
                                                                    <td class="vinculos">
	      									<?=$clasification->idClasificacion?>	
	      								</td>
	      								<td class="vinculos">
	      									<?=$clasification->name?>
	      								</td>
                                                                        <td class="vinculos">
	      									<?=$clasification->idSerie?>
	      								</td>
	      								<td>
	      									<a href="home.php?q=modules/series/views/edit_clasificacionserie&oldClasificationCode=<?=$clasification->idClasificacion?>&idSerie=<?=$clasification->idSerie?>"><img src="./imagenes/edit.png" title="Editar Clasificaci&oacute;n" border="0"/></a>
	      									<a href="home.php?q=modules/series/views/admin_clasificacionserie&controllerName=seriesController&callback=DeleteClasification&ClasificationCode=<?=$clasification->idClasificacion?>&idSerie=<?=$clasification->idSerie?>"><img src="./imagenes/del.png" title="Eliminar Clasificaci&oacute;n" border="0"/></a>
	      								</td>
	      							</tr>
	      						<?php 
                                                            $cont++;
                                                        }
	      					}
	      					?>
	      				</table>
	      			</td>
	      		</tr>
		</table>
	</form>
</div>