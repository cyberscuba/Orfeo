<?php
session_start();

//if ($_SESSION["usu"] == 0) {
//    echo "Acceso denegado";
//    die();
//}
?>

<html>
    <header>
        <link href="../estilos/orfeo50/bootstrap.css" rel="stylesheet" type="text/css"/>
        <link href="./css/estilos.css" rel="stylesheet" type="text/css"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="../estilos/js/bootstrap.js" type="text/javascript"></script>        
    </header>
    <script>
        /* Esta función permite leer cada campo que se encuentra en el formulario para validar si los mismos estan 
         * llenos o en blanco, si estos se encuentran en blanco se pintan de color rojo hasta que se llene el valor
         * */
        $(document).ready(function () {
            $(document).on('change', '.classcamposgeneral ', function (event) {
                event.preventDefault();
                
                $(".classcamposgeneral").each(function () {
                    if ($(this).val() == '') {
                        $(this).css('border-color', 'red');
//                        $("#guardar").attr('disabled', 'disabled');
                    } else {
                        $(this).css('border-color', '');
                        $("#guardar").removeAttr('disabled');
                    }
                });
            });
        });

        function validarForm() {
            var dataTotal = [];
            var tabla = $("#tablaConfiguracion tbody > tr");

            // Una vez que tenemos la tabla recorremos la misma por cada TR
            // Por cada fila se obtienen los valores correspondientes
            tabla.each(function () {
                var imputsNombreCampo = $(this).find("input[id*='nombreCampo_']").val(),
                        selecTipoCampo = $(this).find("select").val(),
                        imputsDescCampo = $(this).find("input[id*='descCampo_']").val();

                // Entonces se declara un array para guardar los datos como clave - valor
                item = {};
                item ["imputsNombreCampo"] = imputsNombreCampo;
                item ["selecTipoCampo"] = selecTipoCampo;
                item ["imputsDescCampo"] = imputsDescCampo;
                dataTotal.push(item);
            });

            aInfo = JSON.stringify(dataTotal);

            $.post('guardarConfiguracion.php', {
                tipo: 1,
                numerocampo: aInfo
            })
                    .done(function (res) {
                        $('#tablaConfiguracionFondo').hide();
                        $('#mensajeConfiguracionFondo').show();
                        $('.textMessageModalDoc').text(res.message);
                    });
        }

        /* Esta función agrega el contenido de los campos que se van a insertar en la base de datos 
         * teniendo en cuenta la cantidad de filas que desea agregar el usuario
         * */
        function agregarCampo() {
            $('#mensajeConfiguracionFondo').hide();
            var total = document.getElementById('campos').value;
            for (x = 1; x <= total; x++) {
                $('#dataTablaConfiguracion').append('\
                    <tr class="classgeneral" id="' + x + '">\n\
                        <td id="td_id' + x + '"><input type="text" name="nombrecampo[]" id="nombreCampo_' + x + '" class="form-control classcamposgeneral" minlength="5" maxlength="30"></td>\n\
                        <td id="td_id' + x + '">\
                            <select name="tipodatocampo[]" class="form-control classcamposgeneral">\n\
                                <option value="0">-- seleccione --</option>\n\
                                <option value="Fecha"> Fecha </option>\n\
                                <option value="Texto"> Texto </option>\n\
                                <option value="Numero"> Número </option>\n\
                                <option value="Desplegable"> Lista deplegable </option>\n\
                            </select>\n\
                        </td>\n\
                        <td id="td_id' + x + '">\
                            <input type="text" name="descripcioncampo[]" id="descCampo_' + x + '" class="form-control classcamposgeneral" minlength="5" maxlength="150">\n\
                        </td>\n\
                        <td id="td_id' + x + '"><input type=button class="remove_button btn btn-danger" value="x"></td>\n\
                    </tr>');
            }
            
            $("#guardar").removeAttr('disabled');

            $(document).on('click', '.remove_button', function (event) {
                event.preventDefault();
                $(this).closest('tr').remove();
            });
        }
    </script>
    <body>
        <div class="panel panel-default" style="margin: 0 5px 0 5px; border-color: #8F0000;">
            <div class="panel-heading" style="background-color: #8F0000; color: #FFFFFF; text-align: center; font-size: 18px;">
                Configuraci&oacute;n de fondo acumulado (Orfeo 3.8)
            </div>
            <div class="panel-body row">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <label>Número de campos:</label>
                </div>
                <div class="col-lg-5 col-md-5 col-sm-8 col-xs-8">
                    <input class="form-control" type="text" id="campos" name="campos" placeholder="Cantidad de campos" onchange="agregarCampo()" size="2"/>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    <!--<input type="button" value="Generar" onclick="agregarCampo()" class="btn btn-info">-->
                </div>
            </div>
            <div class="panel-body">
                <div id="tablaConfiguracionFondo" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <table id="tablaConfiguracion" class="table table-striped" style="font-size: 13px;">
                        <thead>
                            <tr>
                                <th>Nombre del Campo</th>
                                <th>Tipo de dato del Campo</th>
                                <th>Descripción del campo</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="dataTablaConfiguracion"></tbody>
                    </table>
                    <input type="button" name="guardar" id="guardar" class="guardar btn btn-success" value="Grabar" disabled="" onclick="validarForm()"/>
                </div>
                <div id="mensajeConfiguracionFondo" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="display: none">
                    <div class="alert alert-success alertDoc" role="alert">
                        <span class="textMessageModalDoc"></span>
                    </div>
                </div>
            </div>                
        </div>
    </body>
</html>
