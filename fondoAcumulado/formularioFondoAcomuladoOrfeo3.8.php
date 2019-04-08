<?php
session_start();
error_reporting(7);
$url_raiz = $_SESSION['url_raiz'];
$dir_raiz = $_SESSION['dir_raiz'];
$assoc = $_SESSION['assoc'];
define('ADODB_ASSOC_CASE', $assoc);

if (!$dir_raiz)
    $dir_raiz = "..";
require_once("$dir_raiz/include/db/ConnectionHandler.php");
include_once "../class_control/AplIntegrada.php";
if (!$db)
    $db = new ConnectionHandler("$dir_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

foreach ($_GET as $key => $valor)
    ${$key} = $valor;
foreach ($_POST as $key => $valor)
    ${$key} = $valor;
?>
<html>
    <header>
        <link href="../estilos/orfeo50/bootstrap.css" rel="stylesheet" type="text/css"/>
        <link href="./css/estilos.css" rel="stylesheet" type="text/css"/>
        <!--<link rel="stylesheet" href="../estilos/orfeo50/datatables.min.css">-->
        <link rel="stylesheet" href="/css/datatables.min.css">
        <link href="../estilos/orfeo50/daterangepicker.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>        
        <script src="../js/datatables.min.js" type="text/javascript"></script>        
        <script src="../estilos/js/bootstrap.js" type="text/javascript"></script>  
        <script src="../js/bootstrap.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui.min.js" type="text/javascript"></script>

        <script src="../js/moment.min.js" type="text/javascript"></script>
        <script src="../js/daterangepicker.min.js" type="text/javascript"></script>
        <link href="../estilos50/orfeo50/daterangepicker.css" rel="stylesheet" type="text/css"/>

        <script src="js/jquery.easing.js" type="text/javascript"></script>
        <script src="js/jqueryFileTree.js" type="text/javascript"></script>
        <link href="css/jqueryFileTree.css" rel="stylesheet" type="text/css" media="screen" />
        <style type="text/css">
            .directorios {
                width: 100%;
                height: 57%;
                border-top: solid 1px #BBB;
                border-left: solid 1px #BBB;
                border-bottom: solid 1px #FFF;
                border-right: solid 1px #FFF;
                background: #FFF;
                overflow: scroll;
                padding: 5px;
            }
            
            td.details-control {
                background: url('resources/details_open.png') no-repeat center center;
                cursor: pointer;
            }
            tr.shown td.details-control {
                background: url('resources/details_close.png') no-repeat center center;
            }

            .dataTables_filter{
                margin-left: 50%;
            }
        </style>
    </header>
    <body>
        <div class="panel panel-default" style="margin: 0 5px 0 5px; border-color: #8F0000;">
            <div class="panel-heading" style="background-color: #8F0000; color: #FFFFFF; text-align: center; font-size: 18px;">
                Módulo de consulta para el fondo acumulado (Orfeo 3.8)
            </div>
            <div class="panel-body row">
                <div class="row" style="margin: 0 5px 0 5px; border-color: #8F0000;">
                    <?php
                    $selectConfig = "SELECT column_name, data_type FROM information_schema.columns WHERE table_schema = 'public' AND table_name = 'fondo_acumulado_orfeo38' AND column_name like 'campo%'";
                    $rsSelect = $db->conn->query($selectConfig);

                    $total = 0;

                    while (!$rsSelect->EOF) {
                        $nombreColumna = $rsSelect->fields["COLUMN_NAME"];
                        $tipoColumna = $rsSelect->fields["DATA_TYPE"];

                        $consultaCampos = "select descripcion_campo from configuracion_campos_enviadas where nombre_campo='$nombreColumna' and consultar=1";
                        $rsSelectCampo = $db->conn->query($consultaCampos);
                        $descripcion = $rsSelectCampo->fields["DESCRIPCION_CAMPO"];

                        switch ($tipoColumna) {
                            case 'character varying':
                                // Se valida que no se habiliten campos de busqueda para los datos que contienen las rutas
                                // ya que no se deberia poder buscar por la ruta
                                if ($nombreColumna != 'campo3' && $nombreColumna != 'campo4' && $nombreColumna != 'campo5') {
                                    if ($nombreColumna == 'campo18' or $nombreColumna == 'campo15' or $nombreColumna == 'campo2' or $nombreColumna == 'campo11') {
                                        echo '
                                        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-6">
                                            <div class="input-group">
                                                <label>' . $descripcion . '</label>
                                                <input type="text" class="form-control campoBusqueda" placeholder="" maxlength="40" size="20" id="' . $nombreColumna . '" readonly="true" name="paramFecharad">
                                            </div>
                                        </div>';
                                    } else {
                                        if ($nombreCampo == 'campo7') {
                                            $autocomplete = 'autocomplete="off"';
                                            echo '<input type="text" id="sendNegocio" />';
                                        } else {
                                            $autocomplete = '';
                                        }
                                        echo '
                                        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-6">
                                            <div class="input-group">
                                                <label>' . $descripcion . '</label>
                                                <input id="' . $nombreColumna . '" type="text" class="form-control campoBusqueda" placeholder="" ' . $autocomplete . '/>
                                            </div>
                                        </div>';
                                    }
                                }
                                break;
                            case 'date':
                                echo '
                                    <div class="col-lg-2 col-md-3 col-sm-6 col-xs-6">
                                        <div class="input-group">
                                            <label>' . $descripcion . '</label>
                                            <input type="text" class="form-control campoBusqueda" placeholder="" maxlength="40" size="20" id="' . $nombreColumna . '" readonly="true" name="paramFecharad">
                                        </div>
                                    </div>';
                                break;
                        }
                        $total++;
                        $rsSelect->MoveNext();
                    }
                    print "<input type='hidden' value='" . $total . "' name='conteoTotal' id='conteoTotal'>";
                    ?>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div>
                            <button type="button" class="btn btn-danger" id="buscar" style="margin-top: 10px; margin-left: 92.1%; border-color: #1C4056; background-color: #1C4056;">Buscar...</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="panelTableInfo" class="panel panel-default" style="display: block;">
                <div class="panel-body">
                    <table id="resDocumentosFondo" class="table table-striped display" style="font-size: 13px;">
                        <thead>
                            <tr>
                                <th></th>
                                <th>#</th>
                                <th style="display:none"></th>
                                <th>Consecutivo Remitente</th>
                                <th>Fecha Envio</th>
                                <th>Link</th>
                                <th>Link anexos</th>
                                <th>Radicado Destinatario</th>
                                <th>Remitente</th>
                                <th>Destinatario</th>
                                <th>Asunto</th>
                                <th>Areá Responsable</th>
                                <th>Fecha Entrega Fisico</th>
                            </tr>
                        </thead>
                        <tbody id="dataListaDocumentosFondo"></tbody>
                    </table>
                </div>
            </div>
            <div id="alertResultados" class="alert alert-warning" role="alert" style="display: none;">
                <span class="textMessage"></span>
            </div>
        </div>
    </body>
    <script>

        $(function () {
            $('input[name="paramFecharad"]').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Limpiar',
                    applyLabel: 'Seleccionar'
                }
            });

            $('input[name="paramFecharad"]').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            });

            $('input[name="paramFecharad"]').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });

            /* Auto complete para los campos 7, 8, 10 y 13 los cuales son los datos que ingreso el cliente */
            $("#campo7").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "consultaAutocomplete.php",
                        type: 'post',
                        dataType: "json",
                        data: {
                            search: request.term,
                            campo: 7,
                            tipo: 1
                        },
                        success: function (res) {
                            if (res.status) {
                                response(res.result);
                            }

                            $('#buscar').removeAttr('disabled');
                            $('#buscar').text('Buscar...');
                        }
                    });
                },
            });

            $("#campo8").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "consultaAutocomplete.php",
                        type: 'post',
                        dataType: "json",
                        data: {
                            search: request.term,
                            campo: 8,
                            tipo: 1
                        },
                        success: function (res) {
                            if (res.status) {
                                response(res.result);
                            }

                            $('#buscar').removeAttr('disabled');
                            $('#buscar').text('Buscar...');
                        }
                    });
                },
            });

            $("#campo10").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "consultaAutocomplete.php",
                        type: 'post',
                        dataType: "json",
                        data: {
                            search: request.term,
                            campo: 10,
                            tipo: 1
                        },
                        success: function (res) {
                            if (res.status) {
                                response(res.result);
                            }

                            $('#buscar').removeAttr('disabled');
                            $('#buscar').text('Buscar...');
                        }
                    });
                },
            });

            $("#campo13").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "consultaAutocomplete.php",
                        type: 'post',
                        dataType: "json",
                        data: {
                            search: request.term,
                            campo: 13,
                            tipo: 1
                        },
                        success: function (res) {
                            if (res.status) {
                                response(res.result);
                            }

                            $('#buscar').removeAttr('disabled');
                            $('#buscar').text('Buscar...');
                        }
                    });
                },
            });
        });


        function format(d) {
            console.log('---- '+d);
            var div = $('<div/>').addClass('loading').text('Loading...');

            $.ajax({
                data: { dato: d[2], opc:1 },
                type: 'POST',
                url: 'detallesConsulta.php',
                dataType: 'json',
                success: function (json) {
                    div.html('<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
                            '<tr><td><b>Consecutivo Concesión:</b> '+json.campo12+'</td></tr>' +
                            '<tr><td><b>Nombre de Tercero:</b> '+json.campo13+'</td></tr>' +
                            '<tr><td><b>Radicado Tercero:</b>' + json.campo14 + '</td></tr>' +
                            '<tr><td><b>Fecha de Radicado:</b>' + json.campo15 + '</td></tr>' +
                            '<tr><td><b>Carta Respuesta:</b>' + json.campo17 + '</td></tr>' +
                            '<tr><td><b>Fecha Respuesta:</b> ' + json.campo18 + '</td></tr>' +
                            '</table>').removeClass('loading');
                }
            });

            return div;
        }

        /* Carga los resultados de la consulta que se realice segun los criterios que se envien */
        $('#buscar').on('click', function () {
            $('#buscar').prop('disabled', 'true');
            $('#buscar').text('Cargando...');

            $('#panelTableInfo').hide();
            $('#alert').hide();

            var campoBusqueda = new Object();
            $('.campoBusqueda').each(function (index, el) {
                campoBusqueda[el.id] = $('#' + el.id).val();
            });

            $.post('procesoBuscarFondoAcumulado.php', {
                fondo: 1,
                total: $('#conteoTotal').val(),
                data: campoBusqueda
            })
            .done(function (res) {

                $('#resDocumentosFondo').DataTable().destroy();
                $('#dataListaDocumentosFondo').html('');

                if (res.status) {
                    $.each(res.result, function (index, val) {
                        var listCount = parseInt(index) + parseInt(1);
                        $('#dataListaDocumentosFondo').append('\
                            <tr class="shown" style="cursor: pointer;" campo1="' + val.campo1 + '" campo2="' + val.campo2 + '" campo3="' + val.campo3 + '" campo4="' + val.campo4 + '" campo5="' + val.campo5 + '" campo6="' + val.campo6 + '" campo7="' + val.campo7 + '" campo8="' + val.campo8 + '" campo9="' + val.campo9 + '" campo10="' + val.campo10 + '" campo11="' + val.campo11 + '" campo12="' + val.campo12 + '" campo13="' + val.campo13 + '" campo14="' + val.campo14 + '" campo15="' + val.campo15 + '" campo16="' + val.campo16 + '" campo17="' + val.campo17 + '" campo18="' + val.campo18 + '" >\
                                <td class="details-control"></td>\
                                <th scope="row">' + listCount + '</th>\\n\
                                <td style="display:none">' + val.id + '</td>\
                                <td>' + val.campo1 + '</td>\
                                <td>' + val.campo2 + '</td>\
                                <td class="tdDataFondoAcumulado" style="color: blue;font-weight: bold;text-decoration: underline blue;" id="' + val.campo4 + '">' + val.campo4 + '</td>\
                                <td class="tdDataFondoAcumuladoAnexos" style="color: blue;font-weight: bold;text-decoration: underline blue;" id="' + val.campo5 + '">' + val.campo5 + '</td>\
                                <td>' + val.campo6 + '</td>\n\
                                <td>' + val.campo7 + '</td>\n\
                                <td>' + val.campo8 + '</td>\n\
                                <td>' + val.campo9 + '</td>\n\
                                <td>' + val.campo10 + '</td>\n\
                                <td>' + val.campo11 + '</td>\n\
                            </tr>'
                        );
                    });

                    var dt = $('#resDocumentosFondo').DataTable({
                        "language": {
                            "url": "./json/Spanish.json"
                        },
                    });

                    // Evento de apertura y cierre de detalles
                    $('#resDocumentosFondo tbody').on('click', 'td.details-control', function () {
                        var tr = $(this).closest('tr');
                        var row = dt.row(tr);
                        if (row.child.isShown()) {
                            // This row is already open - close it
                            row.child.hide();
                            tr.removeClass('shown');
                        } else {
                            // Open this row
                            row.child(format(row.data())).show();
                            tr.addClass('shown');
                        }
                    });

                    $('#panelTableInfo').show();
                    $('#alertResultados').hide();
                } else {
                    $('#alertResultados').show();
                    $('.textMessage').html(res.message);
                }
                $('#buscar').removeAttr('disabled');
                $('#buscar').text('Buscar...');
            });
        });



        /* Esta función permite ver el directorio completo de los anexos correspondientes al criterio que se indico */
        $('body').on('click', '.tdDataFondoAcumuladoAnexos', function () {
            $('.alertDoc').hide();
            var ruta = $(this).attr('id');
            var explode = ruta.split('%20');
            var rutaNueva = explode[0] + ' ' + explode[1];

            if (ruta != null) {
                $('#fileTree').fileTree({
                    root: '/bodega/repositorio/ENVIADAS OFICIOS/ANEXOS/' + rutaNueva + '/',
                    script: 'connectors/jqueryFileTree.php',
                    olderEvent: 'click',
                    multiFolder: false
                },
                        function (file) {
                            var ext = file.split(".", 2);
                            if (ext[1] == 'pdf') {
                                verAnexos(file);
                            }

                        });
                $('.textMessageModalDocAnexos').text('Nombre del directorio: ' + rutaNueva);
            } else {
                $('.alertDoc').show();
                $('#MessageModalDocAnexos').text('No hay níngun anexo');
            }
            $('#myModalDocAnexos').modal();
        });

        function verAnexos(file) {
            $('.textMessageModalDocAnexos').text('Cargando...');
            $.post('buscarArchivoPdf.php', {
                id: file,
                tipo: 2
            })
                    .done(function (res) {
//                        var nombre = file.split("/", 4);
                        if (res.status) {
                            loadPdfAnexos(res.token);
                            $('#tituloAnexo').text('Nombre del archivo: ' + res.name);
                            $('#loadFileAnexo').show();
                        } else {
                            $('·textMessageModalDocAnexos').text(res.message);
                        }
                    });
//            alert(file);
            $('#myModalDocDirAnexos').modal();
        }

        /* Esta función permite ver el .pdf en un visor donde encodea la ruta del archivo de las imagenes principales 
         * del archivo masivo que se carga del fondo acumulado 
         * */
        $('body').on('click', '.tdDataFondoAcumulado', function () {
            $('#loadFile').hide();
            $('.textMessageModalDoc').text('Cargando...');
            $.post('buscarArchivoPdf.php', {
                id: $(this).attr('id'),
                tipo: 1
            })
                    .done(function (res) {
                        if (res.status) {
                            loadPdf(res.token);
                            $('#tituloImagen').text('Nombre del archivo: ' + res.name);
                            $('#loadFile').show();
                            $('.alertDoc').hide();
                        } else {
                            $('.textMessageModalDoc').text(res.message);
                            $('.alertDoc').show();
                        }
                    });

            $('#myModalDoc').modal();
        });

        function convertDataURIToBinary(base64) {
            var raw = window.atob(base64);
            var rawLength = raw.length;
            var array = new Uint8Array(new ArrayBuffer(rawLength));

            for (var i = 0; i < rawLength; i++) {
                array[i] = raw.charCodeAt(i);
            }
            return array;
        }

        function loadPdf(base64Document) {
            var pdfAsDataUri = base64Document;
            var pdfAsArray = convertDataURIToBinary(pdfAsDataUri);
            var url = '../pdfjs/web/viewer.php?file=';

            var binaryData = [];
            binaryData.push(pdfAsArray);
            var dataPdf = window.URL.createObjectURL(new Blob(binaryData, {type: "application/pdf"}))

            document.getElementById('the-frame').setAttribute('src', url + encodeURIComponent(dataPdf));
        }

        function loadPdfAnexos(base64Document) {
            var pdfAsDataUri = base64Document;
            var pdfAsArray = convertDataURIToBinary(pdfAsDataUri);
            var url = '../pdfjs/web/viewerAnexos.php?file=';

            var binaryData = [];
            binaryData.push(pdfAsArray);
            var dataPdf = window.URL.createObjectURL(new Blob(binaryData, {type: "application/pdf"}))

            document.getElementById('the-frame-Anexos').setAttribute('src', url + encodeURIComponent(dataPdf));
        }
    </script>
    <div id="myModalDoc" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg" style="width: 98%;" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-header">
                        <span id="tituloImagen"></span>
                    </div>
                    <div class="alert alert-warning alertDoc" role="alert">
                        <span class="textMessageModalDoc">Cargando...</span>
                    </div>
                    <div id="loadFile" style="display: none;">
                        <iframe id="the-frame" width="100%" height="100%" allowfullscreen webkitallowfullscreen ></iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="color: #FFFFFF; border-color: #1C4056; background-color: #1C4056;">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div id="myModalDocAnexos" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <span id="textMessageModalDocAnexos" class="textMessageModalDocAnexos"></span>
                </div>
                <div class="alert alert-warning alertDoc" role="alert" style="display: none">
                    <span class="MessageModalDocAnexos"></span>
                </div>
                <div class="modal-body">
                    <div id="fileTree" class="Rurasdirectorios"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="color: #FFFFFF; border-color: #1C4056; background-color: #1C4056;">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div id="myModalDocDirAnexos" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg" role="document" style="width:100%; height: 100%;margin-left: 1%;margin-top: 10px;">
            <div class="modal-content" style="width: 98%;">
                <div class="modal-header">
                    <span id="tituloAnexo"></span>
                </div>
                <div id="loadFileAnexo" style="display: none;">
                    <iframe id="the-frame-Anexos" width="100%" height="100%" allowfullscreen webkitallowfullscreen ></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="color: #FFFFFF; border-color: #1C4056; background-color: #1C4056;">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</html>