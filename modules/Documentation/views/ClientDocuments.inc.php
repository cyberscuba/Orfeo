<?php
$_SESSION['DocumentClient'] = null; //Se resetea el documento actual de la sesi�n

$DocumentList = $controller->ViewData["DocumentList"];
$RClient = $controller->ViewData["RClient"];
?>
<div class="messages">
    <?php
    echo WebContext::getContext()->printMessagesAsDiv(CONFIRMATION, "MensajeConfirmacion");
    echo WebContext::getContext()->printMessagesAsDiv(ERROR, "MensajeError");
    echo WebContext::getContext()->printMessagesAsDiv(WARNING, "MensajeAlerta");
    ?>
</div>
<script type="text/javascript" src="js/jquery.iviewer.min.js"></script>
<script type="text/javascript" src="js/jquery.mousewheel.min.js"></script>
<link rel="stylesheet" type="text/css" href="./estilos/jquery.iviewer.css" />
<script type="text/javascript">
    var iv1 = null;
    $(document).ready(function () {
        iv1 = $("#viewer").iviewer({
            src: "./imagenes/pixel.png", //Por defecto 
            update_on_resize: false,
            ui_disabled: true
        });

        $("#in").click(function () {
            iv1.iviewer('zoom_by', 1);
        });
        $("#out").click(function () {
            iv1.iviewer('zoom_by', -1);
        });
        $("#fit").click(function () {
            iv1.iviewer('fit');
        });
        $("#orig").click(function () {
            iv1.iviewer('set_zoom', 100);
        });
    });

    var currentClient = null;
    var currentPage = 0;
    var totalPages = 0;

    function changePage(page) {
        currentPage = page;
        $("#firstPage").html(currentPage);
        $("#lastPage").html(totalPages);
    }

    function next() {
        if (currentPage < totalPages) {
            currentPage++;
            changePage(currentPage);
            loadImage(document.getElementById("Documentos").value, currentPage, false);
        }
    }

    function prev() {
        if (currentPage > 1) {
            currentPage--;
            changePage(currentPage);
            loadImage(document.getElementById("Documentos").value, currentPage, false);
        }
    }

    /**
     * Carga la im�gen del documento dependiendo la p�gina y el documento seleccionado.
     */
    function loadImage(IdDocument, Page, reset) {
        try {
            if (reset == true || reset == 'true') {
                currentPage = 1;
            }
            iv1.iviewer("loadImage", "ajax.php?controllerName=DocumentController&callback=GetDocument&Identification=<?= $_REQUEST['Identification'] ?>&IdDocument=" + IdDocument + "&krd=<?= $_REQUEST['krd'] ?>&IndexPage=" + Page + "&RowType=C");
            GetCurrentAttachmentPages();
        } catch (e) {
            alert("Error al cargar la im�gen");
        }
    }

    /*
     * Se obtiene el total de p�ginas del archivo que se est� visualizando
     */
    function GetCurrentAttachmentPages() {
        $.ajax({
            url: 'ajax.php?controllerName=DocumentController&callback=GetDocumentPages',
            cache: false,
            success: function (result) {
                totalPages = result;
                changePage(currentPage);
            }
        });
    }
    ;

</script>
<style>
    .viewer
    {
        width: 100%;
        height: 1500px;
        overflow: hidden;
        border: 1px solid black;
        position: relative;
    }

    .wrapper
    {
        overflow: hidden;
    }
</style>
<div>
    <form action="home.php" method="post">
        <input type="hidden" name="krd" value="<?= $_REQUEST['krd'] ?>">
        <input type="hidden" name="q" value="modules/Documentation/views/ClientDocuments">
        <input type="hidden" name="controllerName" value="DocumentController">
        <input type="hidden" name="callback" value="GetDocumentsByIdentification">
        <input type="hidden" name="SearchType" value="C">
        <input type="hidden" name="LockImage" value="1">
        <table class="borde_tab" width="100%" cellspacing="3" cellpadding="0" border="0">
            <tbody>
                <tr>
                    <td>
                        <table class="borde_tab" width="100%" cellspacing="0" cellpadding="3" border="0">
                            <tbody>
                                <tr>
                                    <td class="listado2_center" align="center" colspan="2">B&Uacute;SQUEDA DE DOCUMENTOS</td>
                                </tr>
                                <tr>
                                    <td class="titulos5" width="50%" align="right">N&uacute;mero de identificaci&oacute;n:</td>
                                    <td width="50%">
                                        <label>
                                            <input class="tex_area" type="text" name="Identification" value="<?= $_POST['Identification'] ?>"/>
                                            <input id="" class="botones" type="submit" value="Buscar..." name="search">
                                        </label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table class="borde_tab" width="100%"  cellspacing="1" cellpadding="0" border="0">
                            <tbody>
                                <tr>
                                    <td class="titulos5" width="10%" align="right">Nombre:</td>
                                    <td width="15%"><?= utf8_decode($RClient->Name) ?></td>

                                    <td class="titulos5" width="10%" align="right">Apellido:</td>
                                    <td width="15%"><?= utf8_decode($RClient->LastName) ?></td>
                                </tr>
                                <tr>
                                    <td class="titulos5" width="10%" align="right">Tipo de Identificaci&oacute;n:</td>
                                    <td width="15%"><?= utf8_decode($RClient->IdentificationType) ?></td>

                                    <td class="titulos5" width="10%" align="right">N&uacute;mero de Identificaci&oacute;n:</td>
                                    <td width="15%"><?= $RClient->Identification ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="borde_tab" width="100%"  cellspacing="1" cellpadding="0" border="0" bgcolor="#E5E5E5">
            <tbody>
                <tr>
                    <td colspan="2">
                        <div id="toolbar">
                            <div class="item">
                                <div class="item-pager">P�gina <span id="firstPage">0</span> de <span id="lastPage">0</span></div>
                                <div class="icon-pager"><a href="javascript:prev()"><img src="./imagenes/prev.png" border="0" title="Anterior" /></a></div>
                                <div class="icon-pager"><a href="javascript:next()"><img src="./imagenes/next.png" border="0" title="Siguiente"/></a></div>
                            </div>
                            <div class="item">
                                <div class="documentSelector">
                                    Selecciona el documento:
                                    <select id="Documentos" onchange="loadImage(this.value, 1, 'true')">
                                        <option value="0">-- Seleccione --</option>
                                        <?php
                                        if (is_array($DocumentList)) {
                                            foreach ($DocumentList as $Document) {
                                                ?>
                                                <option value="<?= $Document->IdDocument ?>"><?= utf8_decode($Document->Name) ?></option>

                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="item">
                                <div class="icon-pager"><a id="in" href="#"><img src="./imagenes/zoom_in.png" border="0" title="Acercar"/></a></div>
                                <div class="icon-pager"><a id="out" href="#"><img src="./imagenes/zoom_out.png" border="0" title="Alejar"/></a></div>
                                <div class="icon-pager"><a id="fit" href="#"><img src="./imagenes/adjust.png" border="0" title="Ajustar"/></a></div>
                                <div class="icon-pager"><a id="orig" href="#"><img src="./imagenes/original.png" border="0" title="Tama�o original"/></a></div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div id="documentImage">

                            <div class="wrapper">
                                <div id="viewer" class="viewer"></div> 
                                <br /> 
                                <div id="viewer2"></div> 
                                <br />
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>