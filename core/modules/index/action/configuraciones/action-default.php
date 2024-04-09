<?php
if (isset($_POST)) {
    switch ($_POST['option']) {
        /** FORMATO NUMERICO , CANTIDAD DE DECIMALES PERMITIDOS EN INVENTARIO , INGRESOS, EGRESOS, COSTOS */
        case 1:
            echo ConfigurationData::getByShortName('num_decimales_inv')->cgdatoi;
            break;
        /** FORMATO NUMERICO , CANTIDAD DE DECIMALES PERMITIDOS LAS VENTANAS DE VENTAS */
        case 2:
            echo ConfigurationData::getByShortName('conf_num_decimal_precio')->cgdatoi;
            break;
        case 3:
            echo ConfigurationData::getByShortName('ingresos_costos')->cgdatoi;
            break;
        case 4:
            echo ConfigurationData::getByShortName('etiq_clientes_conf')->cgdatoi;
            break;
        case 5:
            echo ConfigurationData::getByShortName('num_decimales_compras')->cgdatoi;
            break;
        case 6:
            $sucursal = UserData::getById($_SESSION['user_id'])->sucursal_id;
            $array = array();
            if (!empty($sucursal)){
                $bodegas = BodegasData::getAllSuid($sucursal);
                foreach($bodegas as $bodega){
                    $ar = array(
                        "id" => $bodega->boid,
                        "bodega" => $bodega->bodescrip
                    );
                    array_push($array,$ar);
                }
            }
            echo json_encode($array);
            break;
    }
}
