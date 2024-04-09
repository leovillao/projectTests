<style>
    .form-group {
        padding-bottom: 0.5rem;
    }

    form {
        display: flex;
        padding: 2rem;
        gap: 10px;
        flex-wrap: wrap;
    }

    form > div {
        flex-grow: 1;
    }
</style>
<div class="container">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading" style="display: flex;justify-content: space-between;align-items:center;
            padding: 0.1rem 0.5rem 0.1rem 0.5rem">
                <h3>&nbsp;&nbsp;&nbsp;<b>Consulta Bitacora</b></h3>
                <div class=" btn-group pull-right" role="group">
                    <!--                    <a class="btn btn-primary btn-sm " id="btn-buscar"><i-->
                    <!--                            class="glyphicon glyphicon-search"></i></a>-->
                    <a class="btn btn-primary btn-sm " id="btn-consulta-ventas" title="Consulta de venta"><i
                                class="glyphicon glyphicon-list-alt"></i></a>
                    <a class="btn btn-success  btn-sm" name="excelBtn" value="btnExcel"
                       id="btn-excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>
                    <button class="btn btn-danger btn-sm " id="btn-pdf"><i class="fa fa-file-pdf-o"
                                                                           aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            <div class="panel-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab"
                                                              data-toggle="tab">General</a></li>
                </ul>
                <form class="form form-inline" id="form-criterios-busquedas"
                      style="display: flex;justify-content: space-evenly">
                    <div class="form-group">
                        <label for="sucursalCventas">Usuarios</label>
                        <select class="input-sm form-control" name="usuarios" id="usuarios">
                            <?php
                            $usuarios = UserData::getAll();
                            echo "<option value=''>Todos...</option>";
                            foreach ($usuarios as $usuario) {
                                echo "<option value='" . $usuario->usr_id . "'  >" . $usuario->usr_nombre . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="desde">Desde</label>
                        <input type="date" id="desde" name="desde" class="input-sm" value="<?= date("Y-m-d") ?>">
                    </div>
                    <div class="form-group">
                        <label for="hasta">Hasta</label>
                        <input type="date" id="hasta" name="hasta" class="input-sm" value="<?= date("Y-m-d") ?>">
                    </div>
            </div>
            </form>
        </div>
    </div>
    <div class="row" id="contenedor-tableRmdo" width="500px">
        <div class="col-md-12">
            <table class="display compact" width="100%" id="tableConsultaBitacora">
                <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Fecha</th>
                    <th>Novedad</th>
                    <th>Pagina</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(function () {


        $('#btn-excel').click(function () {
            let datos = new FormData()
            datos.append('desde', $("#desde").val())
            datos.append('hasta', $("#hasta").val())
            datos.append('usuario', $('#usuarios').val())
            $.ajax({
                url: './?action=in_bitacora_get_xls',
                type: 'POST',
                data: datos,
                contentType: false,
                cache: false,
                processData: false,
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (response, status, xhr) {
                    let r = new Blob([response])
                    if (r.size <= 6580) {
                        Swal.fire({
                            icon: 'error',
                            title: "No se registran datos",
                        })
                    } else {
                        const fileName = `Consulta_Bitacora_${moment().format('YYYYMMDD_hmm')}.xlsx`;
                        if (window.navigator.msSaveOrOpenBlob) {
                            window.navigator.msSaveBlob(res, fileName)
                        } else {
                            const downloadLink = window.document.createElement('a')
                            downloadLink.href = window.URL.createObjectURL(new Blob([response]), {type: 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'})
                            downloadLink.download = fileName
                            document.body.appendChild(downloadLink)
                            downloadLink.click()
                            document.body.removeChild(downloadLink)
                        }
                    }
                },
            })
        });


        $('#btn-pdf').click(function () {
            let datos = new FormData()
            datos.append('desde', $("#desde").val())
            datos.append('hasta', $("#hasta").val())
            datos.append('usuario', $('#usuarios').val())
            $.ajax({
                url: './?action=in_bitacora_get_pdf',
                type: 'POST',
                data: datos,
                contentType: false,
                cache: false,
                processData: false,
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (response, status, xhr) {
                    let r = new Blob([response]);
                    console.log(r)
                    if (r.size <= 1944) {
                        Swal.fire({
                            icon: 'error',
                            title: "No se registran datos",
                        })
                    } else {
                        try {
                            //Obtenemos la respuesta para convertirla a blob
                            var blob = new Blob([response], {type: 'application/pdf'});
                            var URL = window.URL || window.webkitURL;
                            //Creamos objeto URL
                            var downloadUrl = URL.createObjectURL(blob);
                            //Abrir en una nueva pestaña
                            window.open(downloadUrl);
                        } catch (ex) {
                            console.log(ex);
                        }
                    }
                },
                error: function (err) {
                    console.log(err)
                    ShowModalCargando(false);
                    console.log("Error al intentar realizar el pdf: " + JSON.stringify(err));
                }
            })
        });

        $(document).on("click", "#btn-consulta-ventas", function (e) {
            let desde = $('#desde').val()
            let hasta = $('#hasta').val()
            let usuario = $('#usuario').val()
            loadDodDiarios(desde, hasta, usuario)
        })

        $("#tableConsultaBitacora").DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            },
        });

        function loadDodDiarios(desde, hasta, usuario) {
            $("#tableConsultaBitacora").DataTable({
                "destroy": true,
                "aaSorting": [[0, "desc"]], // Sort by first column descending
                "ajax": {
                    "method": "POST",
                    "url": "./?action=in_bitacora_get",
                    "data": {"desde": desde, "hasta": hasta, "usuario": usuario}
                },
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                },
                "columns": [
                    {"data": "usuario", "width": "10%"},
                    {"data": "fecha", "width": "10%"},
                    {"data": "accion", "width": "70%"},
                    {"data": "pagina", "width": "10%"},
                ],
            });
        }

    })


    $(document).on('click', '#table-cab-facturas tr td:not(:last-child)', function (event) {
        let documento = $(this).parents("tr").find("td").eq(0).text()
        let fecha = $("#fechaActual").val()
        console.log(fecha)
        loadDetDocumentos(documento, fecha)
        loadTotalVentaFactura(documento)
        $("#modalLoadDetDocumentos").modal('show')
    })

    function loadDetDocumentos(documento, fecha) {
        // event.preventDefault()
        let option = 2
        $("#modalLoadDetDocumentos").modal('show')
        $("#table-det-facturas").DataTable({
            // "bPaginate": false,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            // "searching": false,
            "destroy": true,
            "bAutoWidth": false,
            // "lengthMenu": [[5, 10, 20, 25, 50, -1], [5, 10, 20, 25, 50, "Todos"]],
            // "iDisplayLength": 5,
            "ajax": {
                "method": "POST",
                "url": "./?action=docFacturados",
                "data": {"documento": documento, "fecha": fecha, "option": option}
            },
            language: {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "sProcessing": "Procesando...",
            },
            "columns": [
                {"data": "id"},
                {"data": "codigo"},
                {"data": "producto"},
                {"data": "unidad"},
                {"data": "cantidad", className: "text-right"},
                {"data": "pvp", className: "text-right"},
                {"data": "total", className: "text-right"},

            ],
            "drawCallback": function () {
                let api = this.api();
                $(api.column(3).footer()).html(
                    "Totales :"
                ),
                    $(api.column(4).footer()).html(
                        api.column(4, {page: 'current'}).data().sum().toFixed(2)
                    ),
                    $(api.column(5).footer()).html(
                        api.column(5, {page: 'current'}).data().sum().toFixed(2)
                    ),
                    $(api.column(6).footer()).html(
                        api.column(6, {page: 'current'}).data().sum().toFixed(2)
                    )
            }
        });
    }

    function loadTotalVentaFactura(documento) {
        $.ajax({
            url: './?action=docFacturados',
            type: 'POST',
            data: {"option": 3, "factura": documento},
            success: function (res) {
                console.log(res)
                let r = JSON.parse(res)
                let lista = '<ul class="list-group" style="display: inline-flex">\n'
                $.each(r.data, function (i, item) {
                    // lista += '<li class="list-group-item">'+item.formapago+'</li><li class="list-group-item">$ '+item.valor+'</li>'
                    lista += '<li class="list-group-item"><span class="badge">&nbsp;$ ' + item.valor + '</span>' + item.formapago + '&nbsp;</li>'
                })
                lista += '</ul>'
                $('.list-totales').html(lista)
            }
        })
    }

    function mostrarDataResumido() {
        $.ajax({
            url: './?action=viewDataPedidos',
            type: 'POST',
            data: {
                "desde": $("#desde").val(),
                "hasta": $("#hasta").val(),
                "cliente": $("#cliente").val(),
                "tipo": $("#tipo").val(),
                "vendedor": $("#vendedor").val(),
            },
            success: function (respon) {
                console.log(respon)
            }
        })
    }

    function mostrarDataDetallado() {
        $.ajax({
            url: './?action=viewDataAnticipos',
            type: 'POST',
            data: {
                "desde": $("#desde").val(),
                "hasta": $("#hasta").val(),
                "cliente": $("#cliente").val(),
                "tipo": $("#tipo").val(),
            },
            success: function (respon) {
                console.log(respon)
            }
        })
    }

    function dataDatatableRsmdo() {
        $("#contenedor-tableRmdo").removeClass("noVisible").addClass("visible")
        $("#contenedor-tableDetalle").removeClass('visible').addClass("noVisible")
        $("#contenedor-tableDetalleCorte").removeClass('visible').addClass("noVisible")

        $("#table-report-pedidos").DataTable({
            "destroy": true,
            "ajax": {
                "processing": true,
                "serverSide": true,
                "method": "POST",
                "url": "./?action=viewDataPedidos",
                "data": {
                    "desde": $("#desde").val(),
                    "hasta": $("#hasta").val(),
                    "cliente": $("#cliente").val(),
                    "tipo": $("#tipo").val(),
                    "vendedor": $("#vendedor").val(),
                },
            },
            "columns": [
                {"data": "pedido"},
                {"data": "fecha", className: "text-left"},
                {"data": "codigo", className: "text-left"},
                {"data": "cliente", className: "text-left"},
                {"data": "estado", className: "text-left"},
                {"data": "aprobado", className: "text-left"},
                {"data": "total", className: "text-right"},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
            },
            /** DIBUJA EL TOTAL DE LAS COLUMNAS EN EL FOOTER DE LA TABLA DE INFORMES DE VENTA*/
            "drawCallback": function () {
                let api = this.api();
                // $(api.column(4).footer()).html(
                //     api.column(4, {page: 'current'}).data().sum().toFixed(2)
                // ),
                // $(api.column(5).footer()).html(
                //     api.column(5, {page: 'current'}).data().sum().toFixed(2)
                // ),
                //     $(api.column(6).footer()).html(
                //         api.column(6, {page: 'current'}).data().sum().toFixed(2)
                //     ),
                //     $(api.column(7).footer()).html(
                //         api.column(7, {page: 'current'}).data().sum().toFixed(2)
                //     ),
                //     $(api.column(8).footer()).html(
                //         api.column(8, {page: 'current'}).data().sum().toFixed(2)
                //     ),
                $(api.column(6).footer()).html(
                    api.column(6, {page: 'current'}).data().sum().toFixed(2)
                ),
                    $(api.column(5).footer()).html(
                        "Totales :"
                    )
            }
        })
    }

    function mostrarDataDetallada() {
        $("#contenedor-tableRmdo").removeClass('visible').addClass("noVisible")
        $("#contenedor-tableDetalle").removeClass("noVisible").addClass("visible")
        $("#contenedor-tableDetalleCorte").removeClass('visible').addClass("noVisible")

        $("#table-report-pedidosDetalle").DataTable({
            "destroy": true,
            "ajax": {
                "processing": true,
                "serverSide": true,
                "method": "POST",
                "url": "./?action=viewDataPedidos",
                "data": {
                    "desde": $("#desde").val(),
                    "hasta": $("#hasta").val(),
                    "cliente": $("#cliente").val(),
                    "tipo": $("#tipo").val(),
                    "vendedor": $("#vendedor").val(),
                },
            },
            "columns": [
                {"data": "pedido"},
                {"data": "codigo", className: "text-left"},
                {"data": "cliente", className: "text-left"},
                {"data": "cod", className: "text-left"},
                {"data": "producto", className: "text-left"},
                {"data": "cantidad", className: "text-right"},
                {"data": "unidad", className: "text-left"},
                {"data": "pvp", className: "text-right"},
                {"data": "desc1", className: "text-right"},
                {"data": "desc2", className: "text-right"},
                {"data": "subtotal", className: "text-right"},
                {"data": "iva", className: "text-right"},
                {"data": "total", className: "text-right"},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
            },
            /** DIBUJA EL TOTAL DE LAS COLUMNAS EN EL FOOTER DE LA TABLA DE INFORMES DE VENTA*/
            "drawCallback": function () {
                let api = this.api();
                // $(api.column(4).footer()).html(
                //     api.column(4, {page: 'current'}).data().sum().toFixed(2)
                // ),
                $(api.column(5).footer()).html(
                    api.column(5, {page: 'current'}).data().sum().toFixed(2)
                ),
                    $(api.column(7).footer()).html(
                        api.column(7, {page: 'current'}).data().sum().toFixed(2)
                    ),
                    $(api.column(8).footer()).html(
                        api.column(8, {page: 'current'}).data().sum().toFixed(2)
                    ),
                    $(api.column(9).footer()).html(
                        api.column(9, {page: 'current'}).data().sum().toFixed(2)
                    ),
                    $(api.column(10).footer()).html(
                        api.column(10, {page: 'current'}).data().sum().toFixed(2)
                    ),
                    $(api.column(11).footer()).html(
                        api.column(11, {page: 'current'}).data().sum().toFixed(2)
                    ),
                    $(api.column(12).footer()).html(
                        api.column(12, {page: 'current'}).data().sum().toFixed(2)
                    ),
                    $(api.column(4).footer()).html(
                        "Totales :"
                    )
            }
        })
    }

    function mostrarDataPorCorte() {
        $("#contenedor-tableRmdo").removeClass('visible').addClass("noVisible")
        $("#contenedor-tableDetalle").removeClass("visible").addClass("noVisible")
        $("#contenedor-tableDetalleCorte").removeClass('noVisible').addClass("visible")

        $("#table-report-pedidosDetalleEntrega").DataTable({
            "destroy": true,
            "ajax": {
                "processing": true,
                "serverSide": true,
                "method": "POST",
                "url": "./?action=viewDataPedidos",
                "data": {
                    "desde": $("#desde").val(),
                    "hasta": $("#hasta").val(),
                    "cliente": $("#cliente").val(),
                    "tipo": $("#tipo").val(),
                    "vendedor": $("#vendedor").val(),
                },
            },
            "columns": [
                {"data": "pedido"},
                {"data": "codigo", className: "text-left"},
                {"data": "cliente", className: "text-left"},
                {"data": "cod", className: "text-left"},
                {"data": "producto", className: "text-left"},
                {"data": "cantidad", className: "text-right"},
                {"data": "unidad", className: "text-left"},
                {"data": "pvp", className: "text-right"},
                {"data": "desc1", className: "text-right"},
                {"data": "desc2", className: "text-right"},
                {"data": "subtotal", className: "text-right"},
                {"data": "iva", className: "text-right"},
                {"data": "total", className: "text-right"},
                {"data": "entregada", className: "text-right"},
                {"data": "pendiente", className: "text-right"},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
            },
            /** DIBUJA EL TOTAL DE LAS COLUMNAS EN EL FOOTER DE LA TABLA DE INFORMES DE VENTA*/
            "drawCallback": function () {
                let api = this.api();
                // $(api.column(4).footer()).html(
                //     api.column(4, {page: 'current'}).data().sum().toFixed(2)
                // ),
                $(api.column(5).footer()).html(
                    api.column(5, {page: 'current'}).data().sum().toFixed(2)
                ),
                    $(api.column(7).footer()).html(
                        api.column(7, {page: 'current'}).data().sum().toFixed(2)
                    ),
                    $(api.column(8).footer()).html(
                        api.column(8, {page: 'current'}).data().sum().toFixed(2)
                    ),
                    $(api.column(9).footer()).html(
                        api.column(9, {page: 'current'}).data().sum().toFixed(2)
                    ),
                    $(api.column(10).footer()).html(
                        api.column(10, {page: 'current'}).data().sum().toFixed(2)
                    ),
                    $(api.column(11).footer()).html(
                        api.column(11, {page: 'current'}).data().sum().toFixed(2)
                    ),
                    $(api.column(12).footer()).html(
                        api.column(12, {page: 'current'}).data().sum().toFixed(2)
                    ),
                    $(api.column(13).footer()).html(
                        api.column(13, {page: 'current'}).data().sum().toFixed(2)
                    ),
                    $(api.column(14).footer()).html(
                        api.column(14, {page: 'current'}).data().sum().toFixed(2)
                    ),
                    $(api.column(4).footer()).html(
                        "Totales :"
                    )
            }
        })
    }
</script>
