<?php
if (isset($_GET)):
    $resp = ValidarIdentificacionData::validarCedula($_GET['identificador']);
    var_export($resp);
//    echo $_GET['identificador'];
    endif;
?>