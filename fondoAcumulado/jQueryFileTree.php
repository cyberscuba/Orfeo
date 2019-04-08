<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

        <style type="text/css">
            BODY,
            HTML {
                padding: 0px;
                margin: 0px;
            }
            BODY {
                font-family: Verdana, Arial, Helvetica, sans-serif;
                font-size: 11px;
                background: #EEE;
                padding: 15px;
            }

            H1 {
                font-family: Georgia, serif;
                font-size: 20px;
                font-weight: normal;
            }

            H2 {
                font-family: Georgia, serif;
                font-size: 16px;
                font-weight: normal;
                margin: 0px 0px 10px 0px;
            }

            .example {
                float: left;
                margin: 15px;
            }

            .demo {
                width: 200px;
                height: 400px;
                border-top: solid 1px #BBB;
                border-left: solid 1px #BBB;
                border-bottom: solid 1px #FFF;
                border-right: solid 1px #FFF;
                background: #FFF;
                overflow: scroll;
                padding: 5px;
            }

        </style>

        <script src="js/jquery.js" type="text/javascript"></script>
        <script src="js/jquery.easing.js" type="text/javascript"></script>
        <script src="js/jqueryFileTree.js" type="text/javascript"></script>
        <link href="css/jqueryFileTree.css" rel="stylesheet" type="text/css" media="screen" />

        <script type="text/javascript">

            $(document).ready(function () {
                $('#fileTreeDemo_2').fileTree({
                    root: '/bodega/repositorio/ENVIADAS OFICIOS/ANEXOS/ANEXO 3720/', 
                    script: 'connectors/jqueryFileTree.php', 
                    olderEvent: 'click', 
                    expandSpeed: 750, collapseSpeed: 750, 
                    multiFolder: false
                }, 
                function (file) {
                    alert(file);
                });
            });
        </script>

    </head>

    <body>
        <div class="example">
            <h2>multiFolder = false</h2>
            <div id="fileTreeDemo_2" class="demo"></div>
        </div>
    </body>
</html>