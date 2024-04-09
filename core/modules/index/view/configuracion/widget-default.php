<?php
$categoriasConfigs = CategoryconfigData::getAllActive();
$configuraciones = ConfigurationData::getAll();
?>
    <style>
        .loading {
            background-image: url("https://smarttag-bi.com/test/storage/img/giphy.gif");
            background-size: 100%;
            margin: auto;
        }
    </style>
    <div class="row" style="margin-bottom:2rem">
        <div class="col-md-3">
            <button class="btn btn-default" id="procesos"><i class="glyphicon glyphicon-wrench"></i> Procesos</button>
        </div>
    </div>


    <!---->
    <!-- Modal -->
    <div class="modal fade" id="modalProcesos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Procesos de Configuración</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <style>
                                .list-group > a {
                                    cursor: pointer;
                                }
                            </style>
                            <div class="list-group ">
                                <a id="btnStock" class="list-group-item ">Stock Procedure</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
<!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModalLoading" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="" style="padding: 1.5rem">
                    <p>Procesando...</p>
                </div>
                <div class="modal-body">
                    <div id="" class="loading" style="width: 150px;height: 150px"></div>
                </div>
            </div>
        </div>
    </div>

    <!---->
    <div class="row">
        <div class="col-md-12">
            <div role="tabpanel">
                <input type="text" id="configuracion" value="1" hidden>
                <!--/*=====================================================
                AUTOMATIZACION DE LAS VISTA DEL NOMBRE DE LAS CATEGORIAS DE LA CONFIGURACION
                ======================================================*/-->
                <ul class="nav nav-tabs nav-justified">
                    <?php
                    foreach ($categoriasConfigs as $categoriasConfig) {
                        if ($categoriasConfig->ccid == 1) {
                            $active = "active";
                        } else {
                            $active = "";
                        }
                        echo '<li role="presentation" class="' . $active . '">
                      <a href="#' . str_replace(' ', '', strtolower($categoriasConfig->ccname)) . '" class="input-sm" aria-controls="' . str_replace(' ', '', strtolower($categoriasConfig->ccname)) . '" role="tab" data-toggle="tab">' . $categoriasConfig->ccname . '</a>
                      </li>';
                    }
                    ?>
                </ul>
                <!--ciclo al cual pertenece el parametro para agruparlo en pantallas para el mantenimiento 1=ciclo compras 2= ciclo ventas 3 = tesoreria 4= inventarios, 5= cartera clientes-->
                <!-- Tab panes -->
                <!--/*====================================================
                ====================================================*/ -->
                <div class="tab-content">

                    <?php
                    foreach ($categoriasConfigs as $categoriasConfig) {
                        if ($categoriasConfig->ccid == 1) {
                            $active = "active";
                        } else {
                            $active = "";
                        }
                        echo '<div role="tabpanel" class="tab-pane ' . $active . '" id="' . str_replace(' ', '', strtolower($categoriasConfig->ccname)) . '">';
                        echo '<div class="row" style="padding: 2.5rem"><div class="col-md-12">';
                        echo '<table class="table table-hovered table-bordered" style="font-size: .9em">';
                        echo '<thead>';
                        echo '<tr><th width="10px">#</th><th>NAME_CAMPO</th><th width="150px">NOMBRE</th><th width="650px">DESCRIPCION</th><th width="250px"></th><th width="15px">ACCION</th></tr>';
                        echo '</thead>';
                        $i = 1;
                        foreach ($configuraciones as $configuracione):
                            if ($configuracione->cgcategoria == $categoriasConfig->ccid) {
                                echo '<tr class="form-group">';
                                echo '<td>' . $i . '</td><td>' . $configuracione->short_name . '</td><td>' . $configuracione->cgnombre .
                                    '</td ><td>' .
                                    $configuracione->cgconcepto . '</td><td>' . validaTipoDato($configuracione->cgtipodato, $configuracione->cgtabla, $configuracione->cgmodelo, $configuracione->cgfuncion, $configuracione->cgdatov, $configuracione->cgdatoi, $configuracione->cgdatoc, $configuracione->cgdatod, $configuracione->cgdatof, $configuracione->cgidrel, $configuracione->cgnameid, $configuracione->cgname_nm) . '</td><td><button class="btn btn-success btn-actualizar btn-xs" num="' . $configuracione->cgid . '" modelo="' . $configuracione->cgmodelo . '" funcion="' . $configuracione->cgfuncion . '" ><i class="glyphicon glyphicon-refresh"></i></button></td>';
                                echo '</tr>';
                            }
                            $i++;
                        endforeach;
                        echo '</table>';
                        echo '</div></div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>


<?php
//tipo de dato usado por el parametro 1= integer 2=char, 3=decimal, 4=fecha, 5=varchar
function validaTipoDato($tipo, $tabla, $modelo, $funcion, $datov, $datoi, $datoc, $datod, $datof, $rel, $datoid, $datonm)
{
    $tipoDato = '';
    switch ($tipo) {
        case 1:
            if (strlen($tabla) >= 1) {
                if (!empty($modelo) && !empty($funcion)) {
                    $arr_ids = explode(",", $datov);
                    if (count($arr_ids) > 1) {
                        $multipled = 'multiple="multiple"';
                    } else {
                        $multipled = "";
                    }
                    $fname = 'viewData';
                    $datas = $fname($modelo, $funcion);
                    $tipoDato = '<select name ="cgdatoi" class="form-control select-multipled dato" ' . $multipled . ' style="width: 100%" tipodato="' . $tipo . '">';
                    foreach ($datas as $data) {
                        if (is_null($data->se)) {
                            if ($data->$datoid == $datoi) {
                                $selected = "selected";
                            } else {
                                $selected = "";
                            }
                            $tipoDato .= '<option value="' . $data->$datoid . '" ' . $selected . ' >' . $data->$datonm . '</option>';
                        }
                    }
                    $tipoDato .= '</select>';
                }
            } else {
                if ($datoi == 1) {
                    $selectedsi = "selected";
                    $selectedno = "";
                } else {
                    $selectedno = "selected";
                    $selectedsi = "";
                }
                $tipoDato .= '<select name ="cgdatoi" class="form-control input-sm dato" tipodato="' . $tipo . '">
                    <option value="1" ' . $selectedsi . '>SI</option>
                    <option value="0" ' . $selectedno . '>NO</option>
                    </select>';
            }
            break;
        case 2:
            if ($datoc == "S") {
                $selectedsi = "selected";
                $selectedno = "";
            } else {
                $selectedno = "selected";
                $selectedsi = "";
            }
            $tipoDato = '<select name ="cgdatoc" class="form-control input-sm dato" tipodato="' . $tipo . '">
                    <option value="S" ' . $selectedsi . '>SI</option>
                    <option value="N" ' . $selectedno . '>NO</option>
                    </select>';
            break;
        case 3:
            $tipoDato = '<input type="number" step="any" class="form-control dato "  value="' . $datod . '" tipodato="' . $tipo . '">';
            break;
        case 4:
            $tipoDato = '<input type="date" class="form-control dato" value="' . $datof . '" tipodato="' . $tipo . '">';
            break;
        case 6:
            $tipoDato = '<input type="number" class="form-control dato" value="' . $datoi . '" tipodato="' . $tipo . '">';
            break;
        case 7:
            $tipoDato = '<input type="text" class="form-control dato" value="' . $datov . '" tipodato="' . $tipo . '">';
            break;
        case 8:
            if ($datoi == 1) {
                $selected1 = "selected";
                $selected2 = "";
                $selected3 = "";
                $selected4 = "";
            } elseif ($datoi == 2) {
                $selected1 = "";
                $selected2 = "selected";
                $selected3 = "";
                $selected4 = "";
            } elseif ($datoi == 3) {
                $selected1 = "";
                $selected2 = "";
                $selected3 = "selected";
                $selected4 = "";
            } elseif ($datoi == 4) {
                $selected1 = "";
                $selected2 = "";
                $selected3 = "";
                $selected4 = "selected";
            } elseif ($datoi == 5) {
                $selected1 = "";
                $selected2 = "";
                $selected3 = "";
                $selected4 = "";
                $selected5 = "selected";
            }
            $tipoDato .= '<select name ="cgdatoi" class="form-control input-sm dato" tipodato="' . $tipo . '">
                    <option value="0">Seleccione...</option>
                    <option value="1" ' . $selected1 . '>Precio editable</option>
                    <option value="2" ' . $selected2 . '>Forma de cobro </option>
                    <option value="3" ' . $selected3 . '>Ficha de cliente</option>
                    <option value="4" ' . $selected4 . '>Definición Lista de precio</option>
                    <option value="5" ' . $selected5 . '>Punto de Emision</option>
                    </select>';
            break;
        case 9:
            if ($datoi == 1) {
                $selected1 = "selected";
                $selected2 = "";
                $selected3 = "";
                $selected4 = "";
            } elseif ($datoi == 2) {
                $selected1 = "";
                $selected2 = "selected";
                $selected3 = "";
                $selected4 = "";
            } elseif ($datoi == 3) {
                $selected1 = "";
                $selected2 = "";
                $selected3 = "selected";
                $selected4 = "";
            } elseif ($datoi == 4) {
                $selected1 = "";
                $selected2 = "";
                $selected3 = "";
                $selected4 = "selected";
            }
            $tipoDato .= '<select name ="cgdatoi" class="form-control input-sm dato" tipodato="' . $tipo . '">
                    <option value="0">Seleccione...</option>
                    <option value="1" ' . $selected1 . '>Adopta modelo de facturacion</option>
                    <option value="2" ' . $selected2 . '>Pvp Editable</option>
                    <option value="3" ' . $selected3 . '>Ficha de cliente</option>
                    <option value="4" ' . $selected4 . '>Definicion Lista de precios</option>
                    </select>';
            break;
        case 10:
            $tipoDato = '<input type="number" class="form-control dato control-decimales-costos-unit" value="' . $datoi . '" tipodato="' . $tipo . '">';
            break;
        case 11:
            $tipoDato = '<input type="text" class="input-sm form-control dato" value="' . $datov . '" tipodato="' . $tipo . '">';
            break;
        case 12:
            $tiposCobros = TipocobroData::getAll();
            $tipoDato = "<select name='cgdatoi' class='form-control input-sm dato' tipodato='" . $tipo . "'>";
            $tipoDato .= "<option value=''>Seleccione tipo de cobro...</option>";
            foreach ($tiposCobros as $tiposCobro) {
                $select = ($datov == $tiposCobro->tcid) ? "selected" : "";
                $tipoDato .= "<option value='" . $tiposCobro->tcid . "' " . $select . ">" . $tiposCobro->tcdescrip . "</option>";
            }
            $tipoDato .= "</select>";
            break;
        case 13:
            $tipoDato = "<select name='cgdatoi' class='form-control input-sm dato' tipodato='" . $tipo . "'>";
//            $tipoDato .= "<option value=''>Seleccione tipo de cobro...</option>";
            if ($datov == 1) {
                $selected1 = 'selected';
            } else {
                $selected1 = '';
            }
            $tipoDato .= "<option value='1' " . $selected1 . ">Boton impresion , guarda el documento </option>";
            if ($datov == 2) {
                $selected2 = 'selected';
            } else {
                $selected2 = '';
            }
            $tipoDato .= "<option value='2' " . $selected2 . ">Boton Guardar , imprime el documento </option>";
            if ($datov == 3) {
                $selected3 = 'selected';
            } else {
                $selected3 = '';
            }
            $tipoDato .= "<option value='3' " . $selected3 . ">Accion de Botones independiente </option>";
            $tipoDato .= "</select>";
            break;
        case 14:
            $tipoDato = "<select name='cgdatoi' class='form-control input-sm dato' tipodato='" . $tipo . "'>";
            $tipoDato .= "<option value=''>Seleccione tipo de cobro...</option>";
            if ($datov == 1) {
                $selected1 = 'selected';
            } else {
                $selected1 = '';
            }
            $tipoDato .= "<option value='1' " . $selected1 . ">Ingres de Costo Unitario</option>";
            if ($datov == 2) {
                $selected2 = 'selected';
            } else {
                $selected2 = '';
            }
            $tipoDato .= "<option value='2' " . $selected2 . ">Ingreso de Costo total </option>";
            $tipoDato .= "</select>";
            break;
        case 15:
            $tiposCobros = AnticipoData::getAll();
            $tipoDato = "<select name='cgdatoi' class='form-control input-sm dato' tipodato='" . $tipo . "'>";
            $tipoDato .= "<option value=''>Seleccione tipo de anticipo...</option>";
            foreach ($tiposCobros as $tiposCobro) {
                $select = ($datov == $tiposCobro->taid) ? "selected" : "";
                $tipoDato .= "<option value='" . $tiposCobro->taid . "' " . $select . ">" . $tiposCobro->tanombre . "</option>";
            }
            $tipoDato .= "</select>";
            break;
        case 17:
            $clientes = PersonData::getAll();
            $clienteSeleccionadoName = "";
            $clienteSeleccionadoID = 0;
            foreach ($clientes as $cliente) {
                if ($cliente->ceid == $datov){
                    $clienteSeleccionadoName = $cliente->cename;
                    $clienteSeleccionadoID = $cliente->cerut;
                }
            }
            $tipoDato = '<div class="form-group input-group" style="width: 100%">
                    <input type="text"  class="input-sm dato form-control" idCliente="'.$clienteSeleccionadoID.'" value="'.$clienteSeleccionadoName.'" id="clienteDefaultText" placeholder="Cliente default" tipodato="' . $tipo . '" name="cgdatoi">
                    <span class="input-group-btn"><button class="btn btn-default btn-sm" type="button" id="clienteDefault" data-toggle="tooltip" data-placement="bottom"><i class="glyphicon glyphicon-search" aria-hidden="true"></i></button></span>
                </div>';
            break;
    }
    return $tipoDato;
}

/*===== FUNCION QUE RECIBE LOS DATOS DEL MODELO Y FUNCION RESPECTIVA PARA LA CARGA Y VISUALIZACION  DE DATOS */
function viewData($modelo, $funcion)
{
    $obj2 = new $modelo; // Modelo
    $data = $obj2->$funcion();
//  var_export($data);
    return $data;
}

?>