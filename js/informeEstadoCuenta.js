$(document).ready(function () {
    getFechaActivas()
    $(document).on('click','#removeDataCliente',function () {
        $("#cliente-cod").val('')
        $("#cliente-text").val('')
        $("#cliente").val('')
    })
    function getFechaActivas() {
        let fecha = $("#fechaRango").val()
        $("#desde").val(fecha.substr(0, 10))
        $("#hasta").val(fecha.substring(fecha.length - 10))
    }

    $('#fechaRango').daterangepicker({
        locale: {
            format: "YYYY/MM/DD"
        },
        timePicker: false,
        // startDate: moment().startOf('hour'),
        // endDate: moment().startOf('hour').add(48, 'hour')
    }, function (inicio, fin) {
        $("#desde").val(inicio.format('YYYY-MM-DD'))
        $("#hasta").val(fin.format('YYYY-MM-DD'))
    });


    $(document).on('click', '#btnProduct', function (e) {
        e.preventDefault()
        $("#modalProducto").modal('show')
        // viewTableLoad()
    })
    viewTableLoad()

    function viewTableLoad() {
        $("#table-products-ingresos").DataTable().clear().destroy()
        $("#table-products-ingresos").DataTable({
            "bProcessing": true,
            "sAjaxSource": "./?action=getProductsTable",
            "aoColumns": [
                {mData: 'id'},
                {mData: 'itcodigo'},
                {mData: 'name'},
                // {mData: 'botones', className: "text-center"}
            ],
            retrieve: true,
            "pageLength": 10,
            "order": [[0, "asc"]],
            "columnDefs": [
                {
                    "visible": false,
                    "searchable": true,
                }
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            }
        })
    }

    $(document).on('click', '#table-products-ingresos tbody tr td:not(:last-child)', function () {
        let itcodigo = $(this).parents("tr").find("td").eq(1).text()
        let name = $(this).parents("tr").find("td").eq(2).text()
        let id = $(this).parents("tr").find("td").eq(0).text()
        $("#productocod").val(itcodigo.trim())
        $("#productoId").val(id)
        $("#productText").val(name.trim())
        $("#modalProducto").modal('hide')
    })

    $(document).on('click', '#tbody-cliente tr td', function () {
        let name = $(this).parents("tr").find("td").eq(1).text()
        let ruc = $(this).parents("tr").find("td").eq(0).text()
        let ceid = $(this).parents("tr").find('td').eq(1).find('input[type="hidden"]').val()
        $("#cliente-cod").val(ruc)
        $("#cliente").val(ceid)
        $("#cliente-text").val(name)
        $("#modalCliente").modal('hide')
    }) /* ======= SELECCIONA EL CLIENTE Y LO CARGA EN LA SECCION DE CLIENTES DE LA VENTANA DE FACTURACION  ======= */

    $(document).on('click', '#btnCliente', function (e) {
        console.log('clientes')
        viewModalClientes()
    })

    function viewModalClientes() {
        $("#modalCliente").modal('toggle').on("shown.bs.modal", function () {
            $('#modalCliente .dataTables_filter input').focus();
        })
    }/* ================ SE EJECUTA LA VISUALIZACION DE LA VENTANA MODAL CLIENTES ===============*/

    loadClientesData()

    function loadClientesData() {
        let opcion = 29
        $("#table-clientes").DataTable({
            "destroy": true,
            "keys": true,
            "ajax": {
                "method": "POST",
                "url": "./?action=processFacturacion",
                "data": {"option": opcion}
            },

            "columns": [
                {"data": "ruc"},
                {"data": "name"},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            },
        })
    }

    $(document).on("click", "#btn-consulta-ventas", function () {
        $("#modalLoadDocumentos").modal('toggle')
        let fecha = $('#fechaActual').val()
        loadDodDiarios(fecha)
    })

    function loadDodDiarios(fecha) {
        // let caja = $("#numeroCaja").val()
        // let usuario = $("#user_id").val()
        // let fecha = $("#fechaFacturacionReporte")
        let sucursal = $("#sucursalCventas").val()
        let option = 1

        $.ajax({
            url: './?action=docFacturados',
            type: 'POST',
            data: {"option": option, "fecha": fecha, "sucursal": sucursal},
            success: function (res) {
                console.log(res)
            }
        })

        $("#table-cab-facturas").DataTable({
            "destroy": true,
            "aaSorting": [[0, "desc"]], // Sort by first column descending
            "ajax": {
                "method": "POST",
                "url": "./?action=docFacturados",
                "data": {"option": option, "fecha": fecha, "sucursal": sucursal}
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
                {"data": "factura"},
                {"data": "cliente"},
                {"data": "total"},
                {"data": "fecha"},
                {"data": "boton"},
            ],
            dom: 'Bfrtip',
            buttons: [
                // {
                //     extend: 'excelHtml5',
                //     text: '<i class="fa fa-file-excel-o"></i> ',
                //     titleAttr: 'Exportar a Excel',
                //     className: 'btn btn-success'
                // },
                // {
                //     extend: 'pdfHtml5',
                //     text: '<i class="fa fa-file-pdf-o"></i> ',
                //     titleAttr: 'Exportar a PDF',
                //     className: 'btn btn-danger'
                // },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> ',
                    titleAttr: 'Imprimir',
                    className: 'btn btn-info'
                },
            ],
            "bAutoWidth": false,
            "lengthMenu": [[5, 10, 20, 25, 50, -1], [5, 10, 20, 25, 50, "Todos"]],
            "iDisplayLength": 5,
            dom: 'Bfrtip',
        });
    }

    $(document).on('click', '#table-cab-facturas tr td:not(:last-child)', function (event) {
        let documento = $(this).parents("tr").find("td").eq(0).text()
        loadDetDocumentos(documento)
        loadTotalVentaFactura(documento)
        $("#modalLoadDetDocumentos").modal('show')
    })

    function loadDetDocumentos(documento) {
        // event.preventDefault()
        let option = 2
        $("#modalLoadDetDocumentos").modal('show')
        $("#table-det-facturas").DataTable({
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "searching": false,
            "destroy": true,
            "bAutoWidth": false,
            "lengthMenu": [[5, 10, 20, 25, 50, -1], [5, 10, 20, 25, 50, "Todos"]],
            "iDisplayLength": 5,
            "ajax": {
                "method": "POST",
                "url": "./?action=docFacturados",
                "data": {"documento": documento, "option": option}
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
                {"data": "producto"},
                {"data": "cantidad"},
                {"data": "pvp"},
                {"data": "total"},

            ],
            // responsive: "true",
            /*dom: 'Bfrtip',

            /*"columnDefs": [
                {
                    "targets": [ 0 ], // NUMERO DE COLUMNA EL ARRAY COMIENZA DESDE 0
                    "visible": false, // COLUMNA VISIBLE
                    "searchable": false // COLUMNA INCLUIR EN EL INDEX DE BUSQUEDA
                }
            ]*/ // CODIGO PARA ESCONDER VISUAL Y COLUMNAS PARA BUSCAR EN LA TABLA
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

    $(document).on('change', '#reporte', function () {
        if ($(this).val() == 2) {
            $("#cierre").attr('disabled', false)
            $("#fechaDesde").attr('disabled', true)
            $("#fechaHasta").attr('disabled', true)
        } else {
            $("#cierre").attr('disabled', true)
            $("#cierre").val('')
            $("#fechaDesde").attr('disabled', false)
            $("#fechaHasta").attr('disabled', false)
        }
    })

    function ordenReporte() {
        let combo = $("#reporte");
        // Obtencion del elemento seleccionado
        let valorSeleccionado = combo.val();
        // Aplicacion del orden alfabetico
        combo.html(
            $("#reporte option", $(this)).sort(function (a, b) {
                return a.text == b.text ? 0 : a.text
            }));
    }

    $(document).on('change', '#tipoFact', function () {
        $("#productoId").attr('disabled', true)

        if ($(this).val() == 2) {
            $("#productoId").attr('disabled', false)
        }

        if ($(this).val() == 2) {
            $("#reporte option[value='3']").remove();
            // ordenReporte()
        } else {
            $("#reporte").append('<option value=3>Autorizacion</option>')
            // ordenReporte()
        }
        if ($(this).val() == 1) {
            $("#reporte option[value='4']").remove();
            // ordenReporte()
        } else {
            $("#reporte").append('<option value=4>Clasificacion</option>')
            // ordenReporte()
        }
    })




    $(document).on("click", "#btn-buscar", function (e) {
        e.preventDefault()
        let fecha = $("#rangoFecha").val()
        let desde = fecha.substr(0, 10)
        let hasta = fecha.substring(fecha.length - 10)

        // let desde = $("#fechaDesde").val()
        // let hasta = $("#fechaHasta").val()
        let fechaHistorico = $("#fechaHistorico").val()
        if (desde <= hasta) {
            mostrarDatosConsulta()
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Fecha Incorrecta',
                html: '<h3>Fecha <b>Desde </b> , no puede ser mayor a Fecha <b>Hasta</b>!</h3>',
            })
        }
    })

    function mostrarDatosConsulta() {
        let valores = document.getElementsByClassName('valores')
        let tipo = $('#tipoFact').val()
        let reporte = $("#reporte").val()
        let cierre = $("#cierre").val()

        if (tipo == 1 && reporte == 1) {
            mostrarDatosPorDocumento()
        } else if (tipo == 2 && reporte == 1) {

            mostrarDatosPorProductos()
        } else if (tipo == 1 && reporte == 2) { // reporte documento por cierre
            if (cierre === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Fecha Incorrecta',
                    html: '<h3>Debe ingresar numero de Caja</h3>',
                })
            } else {
                mostrarDatosCierreDoc() // crear
            }
        } else if (tipo == 2 && reporte == 2) { // reporte productos por cierre
            mostrarDatosCierrePro() // crear
        } else if (tipo == 1 && reporte == 5) { // reporte documentos por vendedor
            mostrarDatosVendDoc() // crear
        } else if (tipo == 2 && reporte == 5) { // reporte productos por vendedor
            mostrarDatosVendPro() // crear
        } else if (tipo == 1 && reporte == 6) { // reporte documentos por NCR
            mostrarDatosNcrDoc() // crear
        } else if (tipo == 2 && reporte == 6) { // reporte productos por NCR
            mostrarDatosNcrPro() // crear
        } else if (tipo == 1 && reporte == 3) { // reporte documentos por autorizacion
            mostrarDatosAutoDoc() // crear
        } else if (tipo == 2 && reporte == 4) { // reporte productos por clasificacion
            mostrarDatosClasPro() // crear
        }
    }

    function validaFechas() {
        // let fechaHistorico = $("#fechaHistorico").val()
        // let fecha = $("#fechaRango").val()
        let desde = $("#desde").val()
        let hasta = $("#hasta").val()
        // return validarFechaEnRango(desde, hasta, fechaHistorico)
        let msj = ''
        if (desde == "" && hasta == "") {
            msj = "Debe seleccionar rango de fecha"
        } else if (desde == "") {
            msj = "Debe completar rango de fecha"
        } else if (hasta == "") {
            msj = "Debe completar rango de fecha"
        }

        return msj
    }

    function validarFechaEnRango(desde, hasta, fechaHistorico) {
        // let fechaInicioMs = fechaInicio.getTime();
        // let fechaFinMs = fechaFin.setDate(fechaFin.getDate() + 1);
        // // let fechaFinMs = fechaFin.getTime();
        // let fechaValidarMs = fechaValidar.getTime();
        let fechaInicio = new Date(desde);
        let fechaFin = new Date(hasta);
        let fechaValidar = new Date(fechaHistorico);
        if (fechaValidar.getTime() >= fechaFin.getTime()) {
            return 1; // valida que la fecha a validar es mayor a la fecha final
        } else if (fechaValidar.getTime() < fechaInicio.getTime() && fechaValidar.getTime() < fechaFin.getTime()) {
            return 2; // la fecha a validar es menor q la fecha final
        } else {
            return 0;
        }
    }

    function mostrarDatosPorProductos() {
        let fechaHistorico = $("#fechaHistorico").val()
        if (validaFechas() == 0) {
            Swal.fire({
                title: 'Fecha Inconsistente?',
                html: "<h3>Rango de fecha no permitido , Historico de datos hasta : " + fechaHistorico + "</h3>",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar!'
            }).then((result) => {
                if (result.isConfirmed) {

                }
            })
        } else {
            let tipoTabla = "P"
            if (validaFechas() == 1) {
                tipoTabla = "H"
            }
            let tipo = $('#tipoFact').val();
            let secuencia = $('#secuencia').val()
            let fechadesde = $('#fechaDesde').val()
            let fechahasta = $('#fechaHasta').val()
            let estado = $('#estado').val()
            let sucursal = $('#sucursal').val()
            let cierre = $('#cierre').val()
            let cliente = $('#cliente').val()
            let vendedor = $('#vendedor').val()
            let pais = $('#pais').val()
            let provincia = $('#provincia').val()
            let ciudad = $('#ciudad').val()
            let producto = $('#productoId').val()
            $("#table-report-ventas-productos").DataTable({
                "destroy": true,
                "ajax": {
                    "processing": true,
                    "serverSide": true,
                    "method": "POST",
                    "url": "./?action=viewDataVentaProductos",
                    "data":
                        {
                            "tipo": tipo,
                            "secuencia": secuencia,
                            "fechadesde": fechadesde,
                            "fechahasta": fechahasta,
                            "estado": estado,
                            "sucursal": sucursal,
                            "cierre": cierre,
                            "cliente": cliente,
                            "vendedor": vendedor,
                            "pais": pais,
                            "provincia": provincia,
                            "producto": producto,
                            "ciudad": ciudad,
                            "tipoTabla": tipoTabla
                        },
                },
                "columns": [
                    {"data": "fecha"},
                    {"data": "tipoDoc"},
                    {"data": "documento"},
                    {"data": "codigo"},
                    {"data": "producto"},
                    {"data": "unidad"},
                    {"data": "cantidad", className: "text-right"},
                    {"data": "precio", className: "text-right"},
                    {"data": "subtotal", className: "text-right"},
                    {"data": "iva", className: "text-right"},
                    {"data": "neto", className: "text-right"},
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
                    $(api.column(6).footer()).html(
                        api.column(6, {page: 'current'}).data().sum().toFixed(2)
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
                        $(api.column(5).footer()).html(
                            "Totales :"
                        )
                }
            })
        }
        $("#content-mostrar").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-facturas").removeClass('noMostrarTable').addClass('mostrarTable')
        $("#content-mostrar-cierreDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrarProd-cierreDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-vendedorDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-vendedorProds").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-ncr-docs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-ncr-prods").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-autorizacion").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrarProd-Clasificacion").removeClass('mostrarTable').addClass('noMostrarTable')
    }

    function mostrarDatosPorDocumento() {

        let tipo = $('#tipoFact').val()
        let secuencia = $('#secuencia').val()
        let fechadesde = $('#fechaDesde').val()
        let fechahasta = $('#fechaHasta').val()
        let estado = $('#estado').val()
        let sucursal = $('#sucursal').val()
        let cierre = $('#cierre').val()
        let cliente = $('#cliente').val()
        let vendedor = $('#vendedor').val()
        let pais = $('#pais').val()
        let provincia = $('#provincia').val()
        let ciudad = $('#ciudad').val()

        $("#table-report-ventas").DataTable({
            "destroy": true,
            "ajax": {
                "async": false,
                "processing": true,
                "serverSide": true,
                "method": "POST",
                "url": "./?action=viewDataVenta",
                "data":
                    {
                        "tipo": tipo,
                        "secuencia": secuencia,
                        "fechadesde": fechadesde,
                        "fechahasta": fechahasta,
                        "estado": estado,
                        "sucursal": sucursal,
                        "cierre": cierre,
                        "cliente": cliente,
                        "vendedor": vendedor,
                        "pais": pais,
                        "provincia": provincia,
                        "ciudad": ciudad,
                        "tipoTabla": tipoTabla

                    },
            },
            "columns": [
                {"data": "tipoDoc"},
                {"data": "documento"},
                {"data": "fecha"},
                {"data": "codigo"},
                {"data": "cliente"},
                {"data": "exento"},
                {"data": "grabado", className: "text-right"},
                {"data": "subtotal", className: "text-right"},
                {"data": "descuento", className: "text-right"},
                {"data": "iva", className: "text-right"},
                {"data": "neto", className: "text-right"},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
            },
            /** DIBUJA EL TOTAL DE LAS COLUMNAS EN EL FOOTER DE LA TABLA DE INFORMES DE VENTA*/
            "drawCallback": function () {
                let api = this.api();
                $(api.column(5).footer()).html(
                    api.column(5, {page: 'current'}).data().sum().toFixed(2)
                ),
                    $(api.column(6).footer()).html(
                        api.column(6, {page: 'current'}).data().sum().toFixed(2)
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
                    $(api.column(4).footer()).html(
                        "Totales :"
                    )
            }
        })

        $("#content-mostrar").removeClass('noMostrarTable').addClass('mostrarTable')
        $("#content-mostrar-facturas").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-cierreDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrarProd-cierreDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-vendedorDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-vendedorProds").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-ncr-docs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-ncr-prods").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-autorizacion").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrarProd-Clasificacion").removeClass('mostrarTable').addClass('noMostrarTable')

    }

    function mostrarDatosCierreDoc() {

        let tipo = $('#tipoFact').val()
        let secuencia = $('#secuencia').val()
        let fechadesde = $('#fechaDesde').val()
        let fechahasta = $('#fechaHasta').val()
        let estado = $('#estado').val()
        let sucursal = $('#sucursal').val()
        let cierre = $('#cierre').val()
        let cliente = $('#cliente').val()
        let vendedor = $('#vendedor').val()
        let pais = $('#pais').val()
        let provincia = $('#provincia').val()
        let ciudad = $('#ciudad').val()

        $("#table-report-ventas-cierre").DataTable({
            "destroy": true,
            "ajax": {
                "processing": true,
                "serverSide": true,
                "method": "POST",
                "url": "./?action=viewDataVentaCierre",
                "data":
                    {
                        "tipo": tipo,
                        "secuencia": secuencia,
                        "fechadesde": fechadesde,
                        "fechahasta": fechahasta,
                        "estado": estado,
                        "sucursal": sucursal,
                        "cierre": cierre,
                        "cliente": cliente,
                        "vendedor": vendedor,
                        "pais": pais,
                        "provincia": provincia,
                        "ciudad": ciudad,
                        "tipotabla": tipoTable,
                    },
            },
            "columns": [
                {"data": "cierre"},
                {"data": "tipoDoc"},
                {"data": "documento"},
                {"data": "fecha"},
                {"data": "codigo"},
                {"data": "cliente"},
                {"data": "exento"},
                {"data": "grabado", className: "text-right"},
                {"data": "subtotal", className: "text-right"},
                {"data": "descuento", className: "text-right"},
                {"data": "iva", className: "text-right"},
                {"data": "neto", className: "text-right"},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
            },
            /** DIBUJA EL TOTAL DE LAS COLUMNAS EN EL FOOTER DE LA TABLA DE INFORMES DE VENTA*/
            "drawCallback": function () {
                let api = this.api();
                $(api.column(6).footer()).html(
                    api.column(6, {page: 'current'}).data().sum().toFixed(2)
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
                    $(api.column(5).footer()).html(
                        "Totales :"
                    )
            }
        })

        $("#content-mostrar").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-facturas").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-cierreDocs").removeClass('noMostrarTable').addClass('mostrarTable')
        $("#content-mostrarProd-cierreDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-vendedorDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-vendedorProds").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-ncr-docs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-ncr-prods").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-autorizacion").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrarProd-Clasificacion").removeClass('mostrarTable').addClass('noMostrarTable')

    }

    function mostrarDatosCierrePro() {
        let fechaHistorico = $("#fechaHistorico").val()
        if (validaFechas() == 0) {
            Swal.fire({
                title: 'Fecha Inconsistente?',
                html: "<h3>Rango de fecha no permitido , Historico de datos hasta : " + fechaHistorico + "</h3>",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar!'
            }).then((result) => {
                if (result.isConfirmed) {

                }
            })
        } else {
            let tipoTabla = "P"
            if (validaFechas() == 1) {
                tipoTabla = "H"
            }
            let tipo = $('#tipoFact').val()
            let secuencia = $('#secuencia').val()
            let fechadesde = $('#fechaDesde').val()
            let fechahasta = $('#fechaHasta').val()
            let estado = $('#estado').val()
            let sucursal = $('#sucursal').val()
            let cierre = $('#cierre').val()
            let cliente = $('#cliente').val()
            let vendedor = $('#vendedor').val()
            let pais = $('#pais').val()
            let provincia = $('#provincia').val()
            let ciudad = $('#ciudad').val()

            $("#table-report-products-cierre").DataTable({
                "destroy": true,
                "ajax": {
                    "processing": true,
                    "serverSide": true,
                    "method": "POST",
                    "url": "./?action=viewDataProdCierre",
                    "data":
                        {
                            "tipo": tipo,
                            "secuencia": secuencia,
                            "fechadesde": fechadesde,
                            "fechahasta": fechahasta,
                            "estado": estado,
                            "sucursal": sucursal,
                            "cierre": cierre,
                            "cliente": cliente,
                            "vendedor": vendedor,
                            "pais": pais,
                            "provincia": provincia,
                            "ciudad": ciudad,
                            "tipotabla": tipoTabla,
                        },
                },
                "columns": [
                    {"data": "cierre"},
                    {"data": "tipoDoc"},
                    {"data": "documento"},
                    {"data": "fecha"},
                    {"data": "codigo"},
                    {"data": "producto"},
                    {"data": "unidad"},
                    {"data": "cantidad"},
                    {"data": "precio", className: "text-right"},
                    {"data": "subtotal", className: "text-right"},
                    {"data": "iva", className: "text-right"},
                    {"data": "neto", className: "text-right"},
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                },
                /** DIBUJA EL TOTAL DE LAS COLUMNAS EN EL FOOTER DE LA TABLA DE INFORMES DE VENTA*/
                "drawCallback": function () {
                    let api = this.api();
                    // $(api.column(6).footer()).html(
                    //     api.column(6, {page: 'current'}).data().sum().toFixed(2)
                    // ),
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
                        $(api.column(5).footer()).html(
                            "Totales :"
                        )
                }
            })
        }

        $("#content-mostrar").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-facturas").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-cierreDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrarProd-cierreDocs").removeClass('noMostrarTable').addClass('mostrarTable')
        $("#content-mostrar-vendedorDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-vendedorProds").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-ncr-docs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-ncr-prods").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-autorizacion").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrarProd-Clasificacion").removeClass('mostrarTable').addClass('noMostrarTable')
    }

    function mostrarDatosVendDoc() {
        let tipo = $('#tipoFact').val()
        let secuencia = $('#secuencia').val()
        let fechadesde = $('#fechaDesde').val()
        let fechahasta = $('#fechaHasta').val()
        let estado = $('#estado').val()
        let sucursal = $('#sucursal').val()
        let cierre = $('#cierre').val()
        let cliente = $('#cliente').val()
        let vendedor = $('#vendedor').val()
        let pais = $('#pais').val()
        let provincia = $('#provincia').val()
        let ciudad = $('#ciudad').val()

        $("#table-report-vendedor-doc").DataTable({
            "destroy": true,
            "ajax": {
                "processing": true,
                "serverSide": true,
                "method": "POST",
                "url": "./?action=viewDataVendedorDoc",
                "data":
                    {
                        "tipo": tipo,
                        "secuencia": secuencia,
                        "fechadesde": fechadesde,
                        "fechahasta": fechahasta,
                        "estado": estado,
                        "sucursal": sucursal,
                        "cierre": cierre,
                        "cliente": cliente,
                        "vendedor": vendedor,
                        "pais": pais,
                        "provincia": provincia,
                        "ciudad": ciudad,
                        "tipotabla": tipoTabla,
                    },
            },
            "columns": [
                {"data": "vendedor"},
                {"data": "tipoDoc"},
                {"data": "documento"},
                {"data": "fecha"},
                {"data": "codigo"},
                {"data": "cliente"},
                {"data": "exento"},
                {"data": "grabado", className: "text-right"},
                {"data": "subtotal", className: "text-right"},
                {"data": "descuento", className: "text-right"},
                {"data": "iva", className: "text-right"},
                {"data": "neto", className: "text-right"},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
            },
            /** DIBUJA EL TOTAL DE LAS COLUMNAS EN EL FOOTER DE LA TABLA DE INFORMES DE VENTA*/
            "drawCallback": function () {
                let api = this.api();
                $(api.column(6).footer()).html(
                    api.column(6, {page: 'current'}).data().sum().toFixed(2)
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
                    $(api.column(5).footer()).html(
                        "Totales :"
                    )
            }
        })

        $("#content-mostrar").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-facturas").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-cierreDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrarProd-cierreDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-vendedorDocs").removeClass('noMostrarTable').addClass('mostrarTable')
        $("#content-mostrar-vendedorProds").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-ncr-docs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-ncr-prods").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-autorizacion").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrarProd-Clasificacion").removeClass('mostrarTable').addClass('noMostrarTable')

    }

    function mostrarDatosVendPro() {
        console.log("VendedoresProductos")
        let fechaHistorico = $("#fechaHistorico").val()
        console.log(validaFechas())
        if (validaFechas() == 0) {
            Swal.fire({
                title: 'Fecha Inconsistente?',
                html: "<h3>Rango de fecha no permitido , Historico de datos hasta : " + fechaHistorico + "</h3>",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar!'
            }).then((result) => {
                if (result.isConfirmed) {

                }
            })
        } else {
            let tipoTabla = "P"
            if (validaFechas() == 1) {
                tipoTabla = "H"
            }
            let tipo = $('#tipoFact').val()
            let secuencia = $('#secuencia').val()
            let fechadesde = $('#fechaDesde').val()
            let fechahasta = $('#fechaHasta').val()
            let estado = $('#estado').val()
            let sucursal = $('#sucursal').val()
            let cierre = $('#cierre').val()
            let cliente = $('#cliente').val()
            let vendedor = $('#vendedor').val()
            let pais = $('#pais').val()
            let provincia = $('#provincia').val()
            let ciudad = $('#ciudad').val()

            $("#table-report-products-vendedor").DataTable({
                "destroy": true,
                "ajax": {
                    "processing": true,
                    "serverSide": true,
                    "method": "POST",
                    "url": "./?action=viewDataVendedorProd",
                    "data":
                        {
                            "tipo": tipo,
                            "secuencia": secuencia,
                            "fechadesde": fechadesde,
                            "fechahasta": fechahasta,
                            "estado": estado,
                            "sucursal": sucursal,
                            "cierre": cierre,
                            "cliente": cliente,
                            "vendedor": vendedor,
                            "pais": pais,
                            "provincia": provincia,
                            "ciudad": ciudad,
                            "tipotabla": tipoTabla
                        },
                },
                "columns": [
                    {"data": "vendedor"},
                    {"data": "fecha"},
                    {"data": "tipoDoc"},
                    {"data": "documento"},
                    {"data": "codigo"},
                    {"data": "producto"},
                    {"data": "unidad"},
                    {"data": "cantidad"},
                    {"data": "precio", className: "text-right"},
                    {"data": "subtotal", className: "text-right"},
                    {"data": "iva", className: "text-right"},
                    {"data": "neto", className: "text-right"},
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                },
                /** DIBUJA EL TOTAL DE LAS COLUMNAS EN EL FOOTER DE LA TABLA DE INFORMES DE VENTA*/
                "drawCallback": function () {
                    let api = this.api();
                    /*$(api.column(5).footer()).html(
                        api.column(5, {page: 'current'}).data().sum().toFixed(2)
                    ),*/
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
                        $(api.column(6).footer()).html(
                            "Totales :"
                        )
                }
            })

            $("#content-mostrar").removeClass('mostrarTable').addClass('noMostrarTable')
            $("#content-mostrar-facturas").removeClass('mostrarTable').addClass('noMostrarTable')
            $("#content-mostrar-cierreDocs").removeClass('mostrarTable').addClass('noMostrarTable')
            $("#content-mostrarProd-cierreDocs").removeClass('mostrarTable').addClass('noMostrarTable')
            $("#content-mostrar-vendedorDocs").removeClass('mostrarTable').addClass('noMostrarTable')
            $("#content-mostrar-vendedorProds").removeClass('noMostrarTable').addClass('mostrarTable')
            $("#content-mostrar-ncr-docs").removeClass('mostrarTable').addClass('noMostrarTable')
            $("#content-mostrar-ncr-prods").removeClass('mostrarTable').addClass('noMostrarTable')
            $("#content-mostrar-autorizacion").removeClass('mostrarTable').addClass('noMostrarTable')
            $("#content-mostrarProd-Clasificacion").removeClass('mostrarTable').addClass('noMostrarTable')
        }
    }

    function mostrarDatosNcrDoc() {

        let tipo = $('#tipoFact').val()
        let secuencia = $('#secuencia').val()
        let fechadesde = $('#fechaDesde').val()
        let fechahasta = $('#fechaHasta').val()
        let estado = $('#estado').val()
        let sucursal = $('#sucursal').val()
        let cierre = $('#cierre').val()
        let cliente = $('#cliente').val()
        let vendedor = $('#vendedor').val()
        let pais = $('#pais').val()
        let provincia = $('#provincia').val()
        let ciudad = $('#ciudad').val()

        $("#table-report-ncr-doc").DataTable({
            "destroy": true,
            "ajax": {
                "processing": true,
                "serverSide": true,
                "method": "POST",
                "url": "./?action=viewDataNcrDoc",
                "data":
                    {
                        "tipo": tipo,
                        "secuencia": secuencia,
                        "fechadesde": fechadesde,
                        "fechahasta": fechahasta,
                        "estado": estado,
                        "sucursal": sucursal,
                        "cierre": cierre,
                        "cliente": cliente,
                        "vendedor": vendedor,
                        "pais": pais,
                        "provincia": provincia,
                        "ciudad": ciudad,
                        "tipotable": tipoTabla

                    },
            },
            "columns": [
                {"data": "tipoDoc"},
                {"data": "documento"},
                {"data": "fecha"},
                {"data": "codigo"},
                {"data": "cliente"},
                {"data": "exento"},
                {"data": "grabado", className: "text-right"},
                {"data": "subtotal", className: "text-right"},
                {"data": "descuento", className: "text-right"},
                {"data": "iva", className: "text-right"},
                {"data": "neto", className: "text-right"},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
            },
            /** DIBUJA EL TOTAL DE LAS COLUMNAS EN EL FOOTER DE LA TABLA DE INFORMES DE VENTA*/
            "drawCallback": function () {
                let api = this.api();
                $(api.column(5).footer()).html(
                    api.column(5, {page: 'current'}).data().sum().toFixed(2)
                ),
                    $(api.column(6).footer()).html(
                        api.column(6, {page: 'current'}).data().sum().toFixed(2)
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
                    $(api.column(4).footer()).html(
                        "Totales :"
                    )
            }
        })

        $("#content-mostrar").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-facturas").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-cierreDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrarProd-cierreDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-vendedorDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-vendedorProds").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-ncr-docs").removeClass('noMostrarTable').addClass('mostrarTable')
        $("#content-mostrar-ncr-prods").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-autorizacion").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrarProd-Clasificacion").removeClass('mostrarTable').addClass('noMostrarTable')
    }

    function mostrarDatosNcrPro() {
        let fechaHistorico = $("#fechaHistorico").val()
        if (validaFechas() == 0) {
            Swal.fire({
                title: 'Fecha Inconsistente?',
                html: "<h3>Rango de fecha no permitido , Historico de datos hasta : " + fechaHistorico + "</h3>",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar!'
            }).then((result) => {
                if (result.isConfirmed) {

                }
            })
        } else {
            let tipoTabla = "P"
            if (validaFechas() == 1) {
                tipoTabla = "H"
            }
            let tipo = $('#tipoFact').val()
            let secuencia = $('#secuencia').val()
            let fechadesde = $('#fechaDesde').val()
            let fechahasta = $('#fechaHasta').val()
            let estado = $('#estado').val()
            let sucursal = $('#sucursal').val()
            let cierre = $('#cierre').val()
            let cliente = $('#cliente').val()
            let vendedor = $('#vendedor').val()
            let pais = $('#pais').val()
            let provincia = $('#provincia').val()
            let ciudad = $('#ciudad').val()

            $("#table-report-ncr-prod").DataTable({
                "destroy": true,
                "ajax": {
                    "processing": true,
                    "serverSide": true,
                    "method": "POST",
                    "url": "./?action=viewDataNcrProd",
                    "data":
                        {
                            "tipo": tipo,
                            "secuencia": secuencia,
                            "fechadesde": fechadesde,
                            "fechahasta": fechahasta,
                            "estado": estado,
                            "sucursal": sucursal,
                            "cierre": cierre,
                            "cliente": cliente,
                            "vendedor": vendedor,
                            "pais": pais,
                            "provincia": provincia,
                            "ciudad": ciudad,
                            "tipotabla": tipoTabla

                        },
                },
                "columns": [
                    {"data": "tipoDoc"},
                    {"data": "documento"},
                    {"data": "fecha"},
                    {"data": "codigo"},
                    {"data": "producto"},
                    {"data": "unidad"},
                    {"data": "cantidad"},
                    {"data": "precio", className: "text-right"},
                    {"data": "subtotal", className: "text-right"},
                    {"data": "iva", className: "text-right"},
                    {"data": "neto", className: "text-right"},
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                },
                /** DIBUJA EL TOTAL DE LAS COLUMNAS EN EL FOOTER DE LA TABLA DE INFORMES DE VENTA*/
                "drawCallback": function () {
                    let api = this.api();
                    $(api.column(6).footer()).html(
                        api.column(6, {page: 'current'}).data().sum().toFixed(2)
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
                        /*$(api.column(9).footer()).html(
                            api.column(9, {page: 'current'}).data().sum().toFixed(2)
                        ),*/
                        $(api.column(5).footer()).html(
                            "Totales :"
                        )
                }
            })
        }

        $("#content-mostrar").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-facturas").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-cierreDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrarProd-cierreDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-vendedorDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-vendedorProds").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-ncr-docs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-ncr-prods").removeClass('noMostrarTable').addClass('mostrarTable')
        $("#content-mostrar-autorizacion").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrarProd-Clasificacion").removeClass('mostrarTable').addClass('noMostrarTable')


    }

    function mostrarDatosAutoDoc() {

        let tipo = $('#tipoFact').val()
        let secuencia = $('#secuencia').val()
        let fechadesde = $('#fechaDesde').val()
        let fechahasta = $('#fechaHasta').val()
        let estado = $('#estado').val()
        let sucursal = $('#sucursal').val()
        let cierre = $('#cierre').val()
        let cliente = $('#cliente').val()
        let vendedor = $('#vendedor').val()
        let pais = $('#pais').val()
        let provincia = $('#provincia').val()
        let ciudad = $('#ciudad').val()

        $("#table-report-autorizacion").DataTable({
            "destroy": true,
            "ajax": {
                "processing": true,
                "serverSide": true,
                "method": "POST",
                "url": "./?action=viewDataAutorizacion",
                "data":
                    {
                        "tipo": tipo,
                        "secuencia": secuencia,
                        "fechadesde": fechadesde,
                        "fechahasta": fechahasta,
                        "estado": estado,
                        "sucursal": sucursal,
                        "cierre": cierre,
                        "cliente": cliente,
                        "vendedor": vendedor,
                        "pais": pais,
                        "provincia": provincia,
                        "ciudad": ciudad,
                        "tipotable": tipoTabla

                    },
            },
            "columns": [
                {"data": "tipoDoc"},
                {"data": "documento"},
                {"data": "fecha"},
                {"data": "codigo"},
                {"data": "cliente"},
                {"data": "fecauto"},
                {"data": "clave"},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
            },
        })

        $("#content-mostrar").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-facturas").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-cierreDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrarProd-cierreDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-vendedorDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-vendedorProds").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-ncr-docs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-ncr-prods").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-autorizacion").removeClass('noMostrarTable').addClass('mostrarTable')
        $("#content-mostrarProd-Clasificacion").removeClass('mostrarTable').addClass('noMostrarTable')


    }

    function mostrarDatosClasPro() {
        let fechaHistorico = $("#fechaHistorico").val()
        if (validaFechas() == 0) {
            Swal.fire({
                title: 'Fecha Inconsistente?',
                html: "<h3>Rango de fecha no permitido , Historico de datos hasta : " + fechaHistorico + "</h3>",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar!'
            }).then((result) => {
                if (result.isConfirmed) {

                }
            })
        } else {
            let tipoTabla = "P"
            if (validaFechas() == 1) {
                tipoTabla = "H"
            }
            let tipo = $('#tipoFact').val()
            let secuencia = $('#secuencia').val()
            let fechadesde = $('#fechaDesde').val()
            let fechahasta = $('#fechaHasta').val()
            let estado = $('#estado').val()
            let sucursal = $('#sucursal').val()
            let cierre = $('#cierre').val()
            let cliente = $('#cliente').val()
            let vendedor = $('#vendedor').val()
            let pais = $('#pais').val()
            let provincia = $('#provincia').val()
            let ciudad = $('#ciudad').val()

            $("#table-report-products-clasificacion").DataTable({
                "destroy": true,
                "ajax": {
                    "processing": true,
                    "serverSide": true,
                    "method": "POST",
                    "url": "./?action=viewDataClasificacion",
                    "data":
                        {
                            "tipo": tipo,
                            "secuencia": secuencia,
                            "fechadesde": fechadesde,
                            "fechahasta": fechahasta,
                            "estado": estado,
                            "sucursal": sucursal,
                            "cierre": cierre,
                            "cliente": cliente,
                            "vendedor": vendedor,
                            "pais": pais,
                            "provincia": provincia,
                            "ciudad": ciudad,
                            "tipotable": tipoTabla

                        },
                },
                "columns": [
                    {"data": "categoria"},
                    {"data": "subcategoria"},
                    {"data": "fecha"},
                    {"data": "tipoDoc"},
                    {"data": "documento"},
                    {"data": "codigo"},
                    {"data": "producto"},
                    {"data": "unidad"},
                    {"data": "cantidad"},
                    {"data": "precio"},
                    {"data": "subtotal"},
                    {"data": "iva"},
                    {"data": "neto"},
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                },
                "drawCallback": function () {
                    let api = this.api();
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
                        /*$(api.column(9).footer()).html(
                            api.column(9, {page: 'current'}).data().sum().toFixed(2)
                        ),*/
                        $(api.column(7).footer()).html(
                            "Totales :"
                        )
                }
            })
        }
        $("#content-mostrar").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-facturas").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-cierreDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrarProd-cierreDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-vendedorDocs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-vendedorProds").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-ncr-docs").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-ncr-prods").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrar-autorizacion").removeClass('mostrarTable').addClass('noMostrarTable')
        $("#content-mostrarProd-Clasificacion").removeClass('noMostrarTable').addClass('mostrarTable')

    }

    const format = num => String(num).replace(/(?<!\..*)(\d)(?=(?:\d{3})+(?:\.|$))/g, '$1,')

    var formulario = $('#form-criterios-busquedas');

    $('#btn-pdf').click(function () {
        if (validaFechas() !== "") {
            Swal.fire({
                title: 'Rango de fecha incorrecto',
                html: "<h5>" + validaFechas() + "</h5>",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar!'
            }).then((result) => {
                if (result.isConfirmed) {
                }
            })
        } else {
            formulario.attr('action', 'reportes/cartera/informeEstadoCuenta.php');
            formulario.attr('target', '_blank');
            formulario.attr('method', 'POST');
            formulario.submit();
        }
    });

    $('#btn-excel').click(function () {
        if (validaFechas() !== "") {
            Swal.fire({
                title: 'Rango de fecha incorrecto',
                html: "<h5>" + validaFechas() + "</h5>",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar!'
            }).then((result) => {
                if (result.isConfirmed) {
                }
            })
        } else {
            formulario.attr('action', 'reportes/cartera/informeEstadoCuentaExcel.php');
            formulario.attr('target', '_blank');
            formulario.attr('method', 'POST');
            formulario.submit();
        }
    });

    function destroyTable() {
        let arrayTable = [
            "table-report-ventas",
            "table-report-ventas-productos",
            "table-report-ventas-cierre",
            "table-report-products-cierre",
            "table-report-vendedor-doc",
            "table-report-products-vendedor",
            "table-report-ncr-doc",
            "table-report-ncr-prod",
            "table-report-autorizacion",
            "table-report-products-clasificacion"
        ]
        $.each(arrayTable, function (i, tabla) {
            $("#" + tabla).DataTable().clear().destroy()
        });
    }
})
