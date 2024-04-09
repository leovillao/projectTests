$(function () {
    $('[data-toggle="tooltip"]').tooltip()

    $("#contado").click(function () {
        validaTipoCompra($(this).val())
    })
    /* =========== CONSTANTES ==========*/

    $(document).keydown(function (e) {
        if (e.shiftKey && e.which === 79 /*82*/) {
            validaTipoCompra($("#contado").val())
            document.querySelector('#contado').checked = true;
        }
    })

    $(document).keydown(function (e) {
        if (e.shiftKey && e.which === 82) {
            validaTipoCompra($("#credito").val())
            document.querySelector('#credito').checked = true;
        }
    })

    $("#credito").click(function () {
        validaTipoCompra($(this).val())
    })

    function validaTipoCompra(tipocompra) {
        if (tipocompra == "contado") {
            // console.log("contado")
            $(".vis_tip_pago").css('display', 'block')
        } else {
            // console.log("credito")
            $(".vis_tip_pago").css('display', 'none')
        }
    }

    loadClientesData()

    function loadClientesData() {
        let opcion = 1
        $("#table-clientes").DataTable({
            "destroy": true,
            "keys": true,
            "ajax": {
                "method": "POST",
                "url": "./?action=cotizacionCRE_getData",
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
        // $.ajax({
        //     url: './?action=processNewPedidofm',
        //     method: "POST",
        //     data: {"option": opcion},
        //     // processData: false,  // tell jQuery not to process the data
        //     // contentType: false,   // tell jQuery not to set contentType
        //     success: function (result) {
        //         console.log(result)
        //     }
        // })
    }

    /* ====================== BUSCAR LOS PRODUCTOS SEGUN LOS CRITERIOS INGRESADOS EN EL CUADRO DE BUSQUEDA  =============================*/
    let objetoProduct = ''
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            objetoProduct = JSON.parse(this.responseText)
        }
    }
    xhttp.open("GET", './?action=processNewPedidofm&option=10')
    xhttp.send()
    /** BUSQUEDA DE PRODUCTO POR NOMBRE EN LA VENTANA MODAL DE PRODUCTOS*/
    $("#buscar-productos").keyup(function () {
        let tableBodyProduct = ''
        if ($(this).val() !== '') {
            let expresion = new RegExp(`${$(this).val()}.*`, "i");
            let productosobj = objetoProduct.filter(producto => expresion.test(producto.name));
            let top = 10
            let inc = 1
            $("#validaTipoProducto").val(0)
            $.each(productosobj, function (i, item) {
                if (inc <= top) {
                    tableBodyProduct += '<tr id="row-' + inc + '" class="remove-class"><td>' + inc + '</td><td>' + item.itcodigo + '<input type="hidden" value="' + item.iniva + '"></td><td>' + item.name + '<input type="hidden" value="' + item.codigo + '"></td><td>' + item.unidad + '</td><td><buttom class="btn-load-bod btn btn-listar btn-xs"><i class="glyphicon glyphicon-inbox"></i></buttom><buttom class="btn btn-imagen btn-xs" imagen="' + item.imagen + '"><i class="glyphicon glyphicon-picture"></i></buttom></td></tr>'
                }
                inc++
            })
        } else {
            tableBodyProduct += '<tr><td colspan="3">Buscando...</td></tr>'
        }
        $("#table-tbody-products").html(tableBodyProduct)
    })
    /** BUSQUEDA DE PRODUCTO POR CODIGO EN LA VENTANA MODAL DE PRODUCTOS*/
    $("#buscar-codigo").keyup(function () {
        let tableBodyProduct = ''
        if ($(this).val() !== '') {
            let expresion = new RegExp(`${$(this).val()}.*`, "i");
            let productosobj = objetoProduct.filter(producto => expresion.test(producto.itcodigo));
            let top = 10
            let inc = 1
            $("#validaTipoProducto").val(0)
            $.each(productosobj, function (i, item) {
                if (inc <= top) {
                    tableBodyProduct += '<tr id="row-' + inc + '" class="remove-class"><td>' + inc + '</td><td>' + item.itcodigo + '<input type="hidden" value="' + item.iniva + '"></td><td>' + item.name + '<input type="hidden" value="' + item.codigo + '"></td><td>' + item.unidad + '</td><td><buttom class="btn-load-bod btn btn-listar btn-xs"><i class="glyphicon glyphicon-inbox"></i></buttom><buttom class="btn btn-imagen btn-xs" imagen="' + item.imagen + '"><i class="glyphicon glyphicon-picture"></i></buttom></td></tr>'
                }
                inc++
            })
        } else {
            tableBodyProduct += '<tr><td colspan="3">Buscando...</td></tr>'
        }
        $("#table-tbody-products").html(tableBodyProduct)
    })

    validaPrecionCero()

    $(document).on("click", "#btn-grabarCotizacion", function (e) {
        e.preventDefault()
        let tipoProcesso = $(this).attr('tipo')
        let sucursal = $("#sucursalid").val()
        let cliente = $("#cliente-cod").val()
        let observacion = $('#observacion').val()
        if (validaNumProducts() == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Debe ingresar productos para la venta',
            })
        } else if (validaPrecionCero() >= 1) {
            Swal.fire({
                icon: 'error',
                title: 'Tiene productos con precios 0..!!',
            })
        } else if (cliente.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Debe seleccionar cliente',
            })
        } else if (sucursal == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Debe seleccionar sucursal',
            })
        } else if (observacion.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Debe debe registrar observacion',
            })
        } else if ($("#tipocotizacion").val() == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Debe seleccionar tipo de cotización',
            })
        } else {
            procesarCompra(tipoProcesso)
        }
    })

    $(document).on("click", "#btn-updateCotizacion", function (e) {
        e.preventDefault()
        let tipoProcesso = $(this).attr('tipo')
        let sucursal = $("#sucursalid").val()
        let cliente = $("#cliente-cod").val()
        let observacion = $('#observacion').val()
        if (validaNumProducts() == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Debe ingresar productos para la venta',
            })
        } else if (validaPrecionCero() >= 1) {
            Swal.fire({
                icon: 'error',
                title: 'Tiene productos con precios 0..!!',
            })
        } else if (cliente.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Debe seleccionar cliente',
            })
        } else if (sucursal == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Debe seleccionar sucursal',
            })
        } else if (observacion.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Debe registrar observacion',
            })
        } else if ($("#tipocotizacion").val() == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Debe seleccionar tipo de cotización',
            })
        } else {
            procesarCompra(tipoProcesso)
        }
    })

    function procesarCompra(tipoProcesso) {

        let pagoTipo = "credito";
        let formData = new FormData(document.getElementById("form-pago-venta"))
        formData.append('option', 18)
        formData.append('tipopago', pagoTipo)
        formData.append('idCliente', $("#clienteIdCotizacion").val())
        let arr = new Array()

        $("#tbody-saleProducts tr").each(function () {
            let row = $(this)
            row.find('td:eq(5)').text()
            let itcodigo = row.find("td").eq(0).text()
            let nameItcodigo = row.find("td").eq(1).text()
            // td:not(:last-child)
            let cantidad = row.find("td").eq(2).text()
            let iniva = row.find('td').eq(0).find('input[type="hidden"]').val()
            let unidad = row.find('td').eq(3).find('input[type="hidden"]').val()

            let porcDes1 = row.find('td').eq(5).find('input[type="hidden"]').val()
            let porcDes2 = row.find('td').eq(6).find('input[type="hidden"]').val()

            let funcion = row.find('td').eq(2).find('input[type="hidden"]').val()
            let ccosto = row.find('td').eq(4).find('input[type="hidden"]').val()
            let unidadNego = row.find('td').eq(7).find('input[type="hidden"]').val()

            let pvp = row.find("td").eq(4).text()
            let desc1 = row.find("td").eq(5).text()
            let desc2 = row.find("td").eq(6).text()
            let subtotal = row.find("td").eq(7).text()
            let total = row.find("td").eq(8).text()

            formData.append("itcodigo[]", itcodigo)
            formData.append("producto[]", nameItcodigo)
            formData.append("iniva[]", iniva)
            formData.append("precio[]", pvp)
            formData.append("descuento1[]", desc1)
            formData.append("descuento2[]", desc2)
            formData.append("subtotal[]", subtotal)
            formData.append("total[]", total)
            formData.append("unidad[]", unidad)
            formData.append("porcDes1[]", porcDes1)
            formData.append("porcDes2[]", porcDes2)
            formData.append("cantidad[]", cantidad)
            formData.append("funcion[]", funcion)
            formData.append("ccosto[]", ccosto)
            formData.append("unidadNego[]", unidadNego)
        })
        $("#tableBody-fp tr").each(function () {
            let row = $(this)
            row.find('td:eq(5)').text()
            let fpId = row.find('td').eq(0).find('input[type="hidden"]').val()
            let fecha = row.find("td").eq(1).text()
            let doc = row.find("td").eq(2).text()
            let entidadId = row.find("td").eq(3).text()
            let valor = row.find("td").eq(4).text()
            formData.append("fpid[]", fpId)
            formData.append("fecha[]", fecha)
            formData.append("doc[]", doc)
            formData.append("entidad[]", entidadId)
            formData.append("valor[]", valor)
        })

        formData.append('codCliente', $("#cliente-cod").val())
        formData.append('tipocotizacion', $("#tipocotizacion").val())
        formData.append('tipopago', "credito")
        formData.append('nameCliente', $("#cliente-text").val())
        formData.append('sucursal', $("#sucursalid").val())
        formData.append('vendedor', $("#vendedorid").val())
        formData.append('fechaDoc', $("#fechaDoc").val())
        // formData.append('ptoEmision', $("#ptoemision").val())
        if (document.getElementById("transportista")) {
            formData.append('transportista', $("#transportista").val())
        }

        formData.append('undNegoCl', $("#unidadNegocio").val())
        formData.append('ccostoCl', $("#ccosto").val())
        formData.append('funcCl', $("#funciones").val())
        formData.append('observacion', $("#observacion").val())

        formData.append('porcenDescuento', $("#dporcen").val())
        formData.append('valorDescuento', $("#dvalor").val())
        formData.append('numeroDocumento', $("#numeroDocumento").val())
        formData.append('tipo', tipoProcesso)
        formData.append('valorTotalCotizacion', $("#totalpago").val())
        formData.append('valorIVACot', $("#iva").val())
        formData.append('baseExentaCot', $("#bexenta").val())
        formData.append('baseGrabadaCot', $("#bgravada").val())
        formData.append('ventab', $("#ventab").val())

        $.ajax({
            url: './?action=cotizacion_grabar',
            method: "POST",
            data: formData,
            processData: false,  // tell jQuery not to process the data
            contentType: false,   // tell jQuery not to set contentType
            success: function (result) {
                // console.log(result)
                let respuesta = JSON.parse(result)
                if (respuesta.error === "") {
                    Swal.fire({
                        icon: 'success',
                        title: respuesta.msj.substr(2),
                    })
                    $("#numeroDocumento").val(respuesta.cotizacion).attr('readonly', true)
                    $("#btn-grabarCotizacion").prop('disabled', true)
                    $("#btn-grabarCotizacion").attr('disabled', true)

                    let link = "./reportes/generales/" + respuesta.formato + ".php?id=" + respuesta.cotizacion
                    $("#impresion-btn-cotizacion").attr('href', link)
                    $("#impresion-btn-cotizacion").attr('target', "_blank")

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: respuesta.error.substr(2),
                    })
                }
            }
        })
    }

    $(document).keydown(function (e) {
        if (e.shiftKey && e.which === 67) {
            viewModalClientes()
        } else if (e.shiftKey && e.which === 68) {
            viewModalDesc()
        } else if (e.shiftKey && e.which === 80) {
            viewModalProduct()
            $("#form-buscar-pro").trigger('reset')
            $("#table-tbody-products").html('<tr><td colspan="4"><b>Ingrese datos para realizar busqueda...</b></td></tr>');
            $("#bodegaTable").html('');
            $("#divInfoDataProduct").html('');
            viewProducts()
        } else if (e.which === 45) {
            $.when(loadLineaProduct()).then(loadTotalTd());
        } else if (e.shiftKey && e.which === 46) {
            // calSubtDesTotals()
        }
    })

    $(document).on('click', '#btnCliente', function (e) {
        viewModalClientes()
        loadClientesData()
    })

    $("#modalProducto").on("hidden.bs.modal", function () {
        $("#cantidad").focus()
        $("#cantidad").val('')
    });

    $(document).on('click', '#btnProductos', function (e) { // EJECUTA LA VISUALIZACION DE LA VENTANA DE AYUDA DE PRODUCTO
        viewModalProduct()
        $("#form-buscar-pro").trigger('reset')
        $("#table-tbody-products").html('<tr><td colspan="2"><b>Ingrese datos para realizar busqueda...</b></td></tr>');
        $("#bodegaTable").html('');
        $("#divInfoDataProduct").html('');
        viewProducts()

    })

    function countFilasTable() {
        let nrows = 0
        let codigos = []
        let lastProd = ''
        $("#tbody-saleProducts tr").each(function () {
            codigos.push($(this).find('td').eq(0).text());
        })
        let totCodigos = codigos.length
        if (totCodigos >= 1) {
            lastProd = codigos[totCodigos - 1]
        }
        return lastProd
    }/* =========== Funcion para obtener el ultimo producto agregado en la tabla de productos a facturar ========== */

    // loadProBod(bodega)
    //
    // function loadProBod(bodega){
    //
    // }

    // const bodega = $("#sucursalid").val()
    // loadProductForBodega(bodega)
    function loadProductForBodega() {
        let objetoProductLimit = ''

        let xhttpLimit = new XMLHttpRequest();
        xhttpLimit.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                return objetoProductLimit = JSON.parse(this.responseText)
            }
        }
        xhttpLimit.open("GET", './?action=processNewPedidofm&option=11&bodega=')
        xhttpLimit.send()
    }

    /* ============================
    * FUNCION PARA VISUALIZAR LOS PRODUCTOS DE ACUERDO A LA BODEGA Y ALGORITMO
    * */
    function viewProducts() {
        /* FUNCION QUE CARGA LA VENTANA DE PRODUCTOS Y SUS DATOS DE ACUERDO A BODEGA*/
        let bodega = $("#sucursalid").val()
        if (countFilasTable().length == '') {
            $.ajax({
                type: "GET",
                url: './?action=processNewPedidofm',
                data: {option: 11, bodega: bodega},
                dataType: "html",
                beforeSend: function () {
                },
                error: function () {
                },
                success: function (data) {
                    // console.log(data)
                    let datos = JSON.parse(data)
                    let tableBodyProduct = ''
                    let t = 1
                    $("#validaTipoProducto").val(1)
                    $.each(datos, function (i, item) {
                        /*primera columna el numero , sgda columna ticodigo (iniva) , tercera columna nombre del producto */
                        tableBodyProduct += '<tr  id="row-' + t + '" class="remove-class"><td>' + t + '</td><td>' + item.itcodigo + '<input type="hidden" value="' + item.iniva + '"></td><td>' + item.name + '<input type="hidden" value="' + item.codigo + '"></td><td>' + item.unidad + '</td><td>' + item.stock + '</td><td><button class="btn-load-bod btn btn-listar btn-sm"><i class="glyphicon glyphicon-inbox"></i></button></td></tr>'
                        t++
                    })
                    $("#table-tbody-products").html(tableBodyProduct)
                }
            });
        } else {
            $.ajax({
                type: "GET",
                url: './?action=processNewPedidofm',
                data: {option: 12, lastprod: countFilasTable(), "bodega": bodega},
                dataType: "html",
                beforeSend: function () {
                },
                error: function () {
                },
                success: function (data) {
                    let datos = JSON.parse(data)
                    let tableBodyProduct = ''
                    let t = 1
                    $("#validaTipoProducto").val(1)
                    $.each(datos, function (i, item) {
                        tableBodyProduct += '<tr  id="row-' + t + '" class="remove-class"><td>' + t + '</td><td>' + item.itcodigo + '<input type="hidden" value="' + item.iniva + '"></td><td>' + item.name + '<input type="hidden" value="' + item.codigo + '"></td><td>' + item.unidad + '</td><td>' + item.stock + '</td><td><button class="btn-load-bod btn btn-listar btn-sm"><i class="glyphicon glyphicon-inbox"></i></button></td></tr>'
                        t++
                    })
                    $("#table-tbody-products").html(tableBodyProduct)
                }
            });
        }
    }

    /* ============================
    * CONVINACION DE TECLAS PARA EJECUTAR LOS PROCESOS DE DENTRO DE LA VENTANA DE FACURACION - POS
    * */
    document.addEventListener('keyup', event => {
        if (event.shiftKey && event.keyCode === 49) {
            $(".remove-class").removeClass('cell-focus');
            $("#row-1").addClass('cell-focus');
            $("#row-1").focus(selectProducto($('.cell-focus').children().first()));
        }
        if ((event.shiftKey && event.keyCode === 50)) {
            $(".remove-class").removeClass('cell-focus');
            $("#row-2").addClass('cell-focus');
            $("#row-2").focus(selectProducto($('.cell-focus').children().first()));
        }
        if ((event.shiftKey && event.keyCode === 51)) {
            $(".remove-class").removeClass('cell-focus');
            $("#row-3").addClass('cell-focus');
            $("#row-3").focus(selectProducto($('.cell-focus').children().first()));
        }
        if ((event.shiftKey && event.keyCode === 52)) {
            $(".remove-class").removeClass('cell-focus');
            $("#row-4").addClass('cell-focus');
            $("#row-4").focus(selectProducto($('.cell-focus').children().first()));
        }
        if ((event.shiftKey && event.keyCode === 53)) {
            $(".remove-class").removeClass('cell-focus');
            $("#row-5").addClass('cell-focus');
            $("#row-5").focus(selectProducto($('.cell-focus').children().first()));
        }
        if ((event.shiftKey && event.keyCode === 54)) {
            $(".remove-class").removeClass('cell-focus');
            $("#row-6").addClass('cell-focus');
            $("#row-6").focus(selectProducto($('.cell-focus').children().first()));
        }
        if ((event.shiftKey && event.keyCode === 55)) {
            $(".remove-class").removeClass('cell-focus');
            $("#row-7").addClass('cell-focus');
            $("#row-7").focus(selectProducto($('.cell-focus').children().first()));
        }
        if ((event.shiftKey && event.keyCode === 56)) {
            $(".remove-class").removeClass('cell-focus');
            $("#row-8").addClass('cell-focus');
            $("#row-8").focus(selectProducto($('.cell-focus').children().first()));
        }
        if ((event.shiftKey && event.keyCode === 57)) {
            $(".remove-class").removeClass('cell-focus');
            $("#row-9").addClass('cell-focus');
            $("#row-9").focus(selectProducto($('.cell-focus').children().first()));
        }
        if ((event.shiftKey && event.keyCode === 48)) {
            $(".remove-class").removeClass('cell-focus');
            $("#row-10").addClass('cell-focus');
            $("#row-10").focus(selectProducto($('.cell-focus').children().first()));
        }
        if ((event.shiftKey && event.keyCode === 66)) {
            $("#buscar-productos").focus()
        }
        if (event.keyCode && event.which === 13) {  /* ===== DETECTA PRESION DE LA TECLA ENTER EN LA VENTANA DE AYUDA DE PRODUCTO */
            let ar = ''
            let tipo = $("#validaTipoProducto").val()
            $('#modalProducto').modal('hide');
            ar = validaSeleccion()
            if (ar.includes(1)) { /* === VALIDA SI EL VALOR DE 1 SE ENCUENTRA DENTRO DEL ARRAY => COMPROBANDO LA SELECCION DE UN PRODUCTO DE LA TABLA DE AYUDA */
                if (tipo == 1) {
                    setAlgoritmoOk()
                } else {
                    setAlgoritmoFail()
                }
            }
            $("#form-buscar-pro").trigger('reset')
            $("#table-tbody-products").html('')
        }
        if (event.keyCode && event.which === 27) { /* ===== DETECTA PRESION DE LA TECLA SCAPE EN LA VENTANA DE AYUDA DE PRODUCTO */
            let ar = ''
            let tipo = $("#validaTipoProducto").val()
            $('#modalProducto').modal('hide');
            ar = validaSeleccion()
            if (ar.includes(1)) {  /* === VALIDA SI EL VALOR DE 1 SE ENCUENTRA DENTRO DEL ARRAY => COMPROBANDO LA SELECCION DE UN PRODUCTO DE LA TABLA DE AYUDA */
                if (tipo == 1) {
                    setAlgoritmoOk()
                } else {
                    setAlgoritmoFail()
                }
            }
            $("#form-buscar-pro").trigger('reset')
            $("#table-tbody-products").html('')
        }
    }, false)

    /* ============================
    * FUNCION QUE VALIDA LA ASIGNACION DE LA CLASE QUE VISUALIZA LA FILA SELECCIONADA EN LA TABLA DE PRODUCTOS
    * */
    function validaSeleccion() {
        let arrayEstado = []
        $("#table-tbody-products tr").each(function () {
            if ($(this).hasClass('cell-focus')) {
                arrayEstado.push(1)
            } else {
                arrayEstado.push(0)
            }
        })
        return arrayEstado
    }

    /* ============================
    * EJECUTA LA ACTIVACION DE LA VENTANA MODAL DE PRO
    * */

    /*$("#modalProducto").keypress(function (event) {
        if (event.keyCode == 13) {
            alert('hi')
        }
    })*/

    function viewModalProduct() {
        $('#modalProducto').modal('toggle').on('shown.bs.modal', function () {
            $("#modalProducto #buscar-codigo").focus()
        })
        $("#modalProducto .modal-header").css("background-color", "#C2CF2D")
    }/* ================ SE EJECUTA LA VISUALIZACION DE LA VENTANA MODAL PRODUCTOS ===============*/

    function viewModalClientes() {
        $("#modalCliente").modal('toggle').on("shown.bs.modal", function () {
            $('#modalCliente .dataTables_filter input').focus();
        })
        $("#modalCliente .modal-header").css("background-color", "#84AC3B")
    }/* ================ SE EJECUTA LA VISUALIZACION DE LA VENTANA MODAL CLIENTES ===============*/

    $("#modalCliente").on("hidden.bs.modal", function () {
        $("#btnProductos").focus()
    });/* ================ ESCONDER VENTANA MODAL CLIENTES , FOCUS EN BOTON DE PRODUCTOS ===============*/

    $(document).on('click', '#tbody-cliente tr td', function () {
        let name = $(this).parents("tr").find("td").eq(1).text()
        let id = $(this).parents('tr').find('td').eq(0).find('input[type="hidden"]').val()
        let ruc = $(this).parents("tr").find("td").eq(0).text()
        $("#cliente-cod").val(ruc)
        $("#clienteIdCotizacion").val(id)
        $("#cliente-text").val(name)
        $("#modalCliente").modal('hide')
        validaFucClient(ruc)

    }) /* ======= SELECCIONA EL CLIENTE Y LO CARGA EN LA SECCION DE CLIENTES DE LA VENTANA DE FACTURACION  ======= */

    /* ===============================================================================================
    * Valida el codigo de cliente para visualizar la modal de funciones, costos , unidades de negocios
    * */
    function validaFucClient(codigoCliente) {
        $.ajax({
            url: './?action=processNewPedidofm',
            type: 'POST',
            data: {option: 19, cliente: codigoCliente},
            success: function (respond) {
                // console.log(respond)
                let res = JSON.parse(respond)
                if (res.band == true) {
                    let htmlUnd = "<option value='0'>Seleccione unidad de negocio...</option>"
                    let htmlFun = "<option value='0'>Seleccione funcion...</option>"
                    let htmlCosto = "<option value='0'>Seleccione centro de costo...</option>"

                    if (res.funciones != '') { // se valida que el objeto funciones no este vacio
                        // if($("#funciones").hasOwnProperty(disabled)){
                        $("#funciones").removeAttr("disabled")
                        // }
                        $.each(res.funciones, function (item, value) {
                            htmlFun += "<option value='" + value.id + "'>" + value.name + "</option>"
                        })
                    } else {
                        $("#funciones").attr("disable", "disabled")
                    }

                    if (res.unidades != '') { // se valida que el objeto funciones no este vacio
                        // if($("#unidadNegocio").hasOwnProperty(disabled)){
                        $("#unidadNegocio").removeAttr("disabled")
                        // }
                        $.each(res.unidades, function (item, value) {
                            htmlUnd += "<option value='" + value.id + "'>" + value.name + "</option>"
                        })
                    } else {
                        $("#unidadNegocio").attr("disabled", "disabled")
                        $("#ccosto").attr("disabled", "disabled")
                    }

                    $("#unidadNegocio").html(htmlUnd)
                    $("#funciones").html(htmlFun)
                    $("#ccosto").html(htmlCosto)
                    $("#modalFucCliente").modal('show')
                }
            }
        })
    }

    /* ===============================================================================================*/

    /*========================================================================================
    *Se valida el valor de la unidad de negocio para la visualizacion de los centros de costos
    * */
    $(document).on("change", "#unidadNegocio", function (e) {
        e.preventDefault()
        let valor = $(this).val()
        $.ajax({
            url: './?action=processNewPedidofm',
            type: 'POST',
            data: {option: 20, unidad: valor},
            success: function (respond) {
                let centros = JSON.parse(respond)
                $("#ccosto").removeAttr("disabled")
                let htmlCentros = "<option value='0'>Seleccione centro de costo...</option>"
                $.each(centros, function (item, value) {
                    htmlCentros += "<option value='" + value.id + "'>" + value.name + "</option>"
                })
                $("#ccosto").html(htmlCentros)
            }
        })
    })
    /*========================================================================================*/

    $(document).on('click', '#table-tbody-products tr td:not(:last-child)', function () {
        /* FUNCION QUE SE EJECUTA CUANDO DA CLIC EN LA TABLA MENOS EN LA ULTIMA FILA (BOTON BODEGA) , LA TABLA DE PRODUCTO */
        let row = $(this)
        if (document.getElementById('pedidoVendedor')) {
            loadFilaPedidoVendedor(row)
        } else {
            loadFilaPedidoNormal(row)
        }
    })

    function loadFilaPedidoNormal(row) {
        let codigoProduct = row.parents("tr").find("td").eq(1).text()
        let pedido = 0
        if (document.getElementById('pedidoVendedor')) {
            pedido = $("#pedidoVendedor").val()
        }
        let name = row.parents("tr").find("td").eq(2).text();
        let iniva = row.parent('tr').find('td').eq(1).find('input[type="hidden"]').val()
        let id = row.parent('tr').find('td').eq(2).find('input[type="hidden"]').val()

        let bodega = $("#bodega").val()
        let fecha = $("#fechaDoc").val()
        let marcador = ''
        $.get("./?action=validaStock", {
            "option": "3",
            "producto": codigoProduct,
            "bodega": bodega,
            "fecha": fecha,
            "pedido": pedido,
        })
            .done(function (data) {
                console.log(data)
                let j = JSON.parse(data)
                if (j.validacion == true) {
                    if (j.stock <= 0) {
                        Swal.fire({
                            title: 'Producto sin unidades!',
                            text: "Desea agregar este producto para su venta!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Agregarlo!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                marcador = '**'
                                let cl = $(this).parent()
                                selectProducto(codigoProduct, name, iniva, id, marcador)
                                // getPrecio(codigoProduct)
                                validaFucProducts(codigoProduct)
                                if (!cl.hasClass('cell-focus')) {
                                    $(".remove-class").removeClass('cell-focus');
                                    $(this).parent().addClass('cell-focus');
                                    $("#ctrSinStock").val('');
                                }
                            }
                        })
                    } else {
                        let cl = $(this).parent()
                        selectProducto(codigoProduct, name, iniva, id, marcador)
                        validaFucProducts(codigoProduct)
                        // getPrecio(codigoProduct)
                        if (!cl.hasClass('cell-focus')) {
                            $(".remove-class").removeClass('cell-focus');
                            $(this).parent().addClass('cell-focus');
                            $("#ctrSinStock").val('');
                        }
                    }
                } else {
                    let cl = $(this).parent()
                    selectProducto(codigoProduct, name, iniva, id, marcador)
                    validaFucProducts(codigoProduct)
                    // getPrecio(codigoProduct)
                    if (!cl.hasClass('cell-focus')) {
                        $(".remove-class").removeClass('cell-focus');
                        $(this).parent().addClass('cell-focus');
                        $("#ctrSinStock").val('');
                    }
                }
            });
        $("#modalProducto").modal("hide")
    }

    function loadFilaPedidoVendedor(row) {
        // console.log("Vendedor")
        let codigoProduct = row.parents("tr").find("td").eq(1).text()
        let pedido = 0
        if (document.getElementById('pedidoVendedor')) {
            pedido = $("#pedidoVendedor").val()
        }
        let name = row.parents("tr").find("td").eq(2).text();
        let iniva = row.parent('tr').find('td').eq(1).find('input[type="hidden"]').val()
        let id = row.parent('tr').find('td').eq(2).find('input[type="hidden"]').val()

        let bodega = $("#bodega").val()
        let fecha = $("#fechaDoc").val()
        let marcador = ''
        $.get("./?action=validaStock", {
            "option": "6",
            "producto": codigoProduct,
            "bodega": bodega,
            "fecha": fecha,
            "pedido": pedido,
        })
            .done(function (data) {
                let j = JSON.parse(data)
                // if (j.validacion == true) {
                let msj = ''
                msj += '<h5>Producto : ' + name + '</h5>'
                msj += '<h5>Cantidad : ' + j.stock + '</h5>'
                // if (j.stock) {
                Swal.fire({
                    title: 'Stock de producto!',
                    html: msj,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Agregarlo!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        marcador = '**'
                        let cl = $(this).parent()
                        selectProducto(codigoProduct, name, iniva, id, marcador)
                        // getPrecio(codigoProduct)
                        validaFucProducts(codigoProduct)
                        if (!cl.hasClass('cell-focus')) {
                            $(".remove-class").removeClass('cell-focus');
                            $(this).parent().addClass('cell-focus');
                            $("#ctrSinStock").val('');
                        }
                    }
                })
                // }
                /*else {
                    let cl = $(this).parent()
                    selectProducto(codigoProduct, name, iniva, id, marcador)
                    validaFucProducts(codigoProduct)
                    // getPrecio(codigoProduct)
                    if (!cl.hasClass('cell-focus')) {
                        $(".remove-class").removeClass('cell-focus');
                        $(this).parent().addClass('cell-focus');
                        $("#ctrSinStock").val('');
                    }
                }*/
                /*} else {
                    let cl = $(this).parent()
                    selectProducto(codigoProduct, name, iniva, id, marcador)
                    validaFucProducts(codigoProduct)
                    // getPrecio(codigoProduct)
                    if (!cl.hasClass('cell-focus')) {
                        $(".remove-class").removeClass('cell-focus');
                        $(this).parent().addClass('cell-focus');
                        $("#ctrSinStock").val('');
                    }
                }*/
            });
        $("#modalProducto").modal("hide")
    }

    $(document).on('blur', '#producto-cod', function () {
        let codigo = $(this).val()
        $.ajax({
            url: './?action=validaStock',
            type: 'GET',
            data: {"itcodigo": codigo, "option": 5},
            success: function (resp) {
                let r = JSON.parse(resp)
                if (r.itcodigo != null) {
                    cargarProducto(r.itcodigo, r.name, r.iniva, r.itid)
                } else {
                    Swal.fire({
                        title: 'Codigo incorrecto',
                        // text: "Desea agregar este producto para su venta!",
                        icon: 'warning',
                    }).then(() => {
                        $('#producto-cod').val('')
                        $('#producto-text').val('')
                        $('#cantidad').val('')
                        $('#precion').val('')
                        $('#total').val('')
                    })
                }
            }
        })
    })

    function cargarProducto(codigoProduct, name, iniva, id) {
        let bodega = $("#bodega").val()
        let fecha = $("#fechaDoc").val()
        let marcador = ''
        $.get("./?action=validaStock", {"option": "3", "producto": codigoProduct, "bodega": bodega, "fecha": fecha})
            .done(function (data) {
                // console.log(data)
                let j = JSON.parse(data)
                if (j.validacion == true) {
                    if (j.stock <= 0) {
                        Swal.fire({
                            title: 'Producto sin unidades!',
                            text: "Desea agregar este producto para su venta!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Agregarlo!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                marcador = '**'
                                let cl = $(this).parent()
                                selectProducto(codigoProduct, name, iniva, id, marcador)
                                // getPrecio(codigoProduct)
                                validaFucProducts(codigoProduct)
                                if (!cl.hasClass('cell-focus')) {
                                    $(".remove-class").removeClass('cell-focus');
                                    $(this).parent().addClass('cell-focus');
                                    $("#ctrSinStock").val('');
                                }
                            }
                        })
                    } else {
                        let cl = $(this).parent()
                        selectProducto(codigoProduct, name, iniva, id, marcador)
                        validaFucProducts(codigoProduct)
                        // getPrecio(codigoProduct)
                        if (!cl.hasClass('cell-focus')) {
                            $(".remove-class").removeClass('cell-focus');
                            $(this).parent().addClass('cell-focus');
                            $("#ctrSinStock").val('');
                        }
                    }
                } else {
                    let cl = $(this).parent()
                    selectProducto(codigoProduct, name, iniva, id, marcador)
                    validaFucProducts(codigoProduct)
                    // getPrecio(codigoProduct)
                    if (!cl.hasClass('cell-focus')) {
                        $(".remove-class").removeClass('cell-focus');
                        $(this).parent().addClass('cell-focus');
                        $("#ctrSinStock").val('');
                    }
                }
            });
    }

    function selectProducto(codigoProduct, name, iniva, id, marcador) {
        $("#producto-cod").val(codigoProduct)
        $("#iniva").val(iniva)
        $("#producto-text").val(name)
        loadUnit()
        loadInfoProduct(codigoProduct) // VISUALIZA LA INFORMACION QUE PUEDA TENER EL PRODUCTO (PROMOCION O DETALLE)
    } /* ============ VALIDA LA SELECCION DE UN PRODUCTO DESDE LA VENTANA DE PRODUCTOS PARA SU FACTURACION  =============== */

    /*=================================================================================
    *   FUNCION PARA VALIDAR LA CARGA DE FUNCION , CENTRO DE COSTO , UNIDADES DE NEGOCIO
    *  */

    function validaFucProducts(codigoProd) {
        $.ajax({
            url: './?action=processNewPedidofm',
            type: 'POST',
            data: {option: 21, producto: codigoProd},
            success: function (respond) {
                let res = JSON.parse(respond)
                if (res.band == true) {
                    let htmlUnd = "<option value='0'>Seleccione unidad de negocio...</option>"
                    let htmlFun = "<option value='0'>Seleccione funcion...</option>"
                    let htmlCosto = "<option value='0'>Seleccione centro de costo...</option>"

                    if (res.funciones != '') { // se valida que el objeto funciones no este vacio
                        // if($("#funciones").hasOwnProperty(disabled)){
                        $("#funcionesPro").removeAttr("disabled")
                        // }
                        $.each(res.funciones, function (item, value) {
                            htmlFun += "<option value='" + value.id + "'>" + value.name + "</option>"
                        })
                    } else {
                        $("#funcionesPro").attr("disable", "disabled")
                    }

                    if (res.unidades != '') { // se valida que el objeto funciones no este vacio
                        // if($("#unidadNegocio").hasOwnProperty(disabled)){
                        $("#unidadNegocioPro").removeAttr("disabled")
                        // }
                        $.each(res.unidades, function (item, value) {
                            htmlUnd += "<option value='" + value.id + "'>" + value.name + "</option>"
                        })
                    } else {
                        $("#unidadNegocioPro").attr("disabled", "disabled")
                        $("#ccostoPro").attr("disabled", "disabled")
                    }

                    $("#unidadNegocioPro").html(htmlUnd)
                    $("#funcionesPro").html(htmlFun)
                    $("#ccostoPro").html(htmlCosto)
                    $("#modalFucProducto").modal('show')
                }
            }
        })
    }

    /*===============================================================================*/

    /*========================================================================================
    *Se valida el valor de la unidad de negocio para la visualizacion de los centros de costos
    * */

    $(document).on("change", "#unidadNegocioPro", function (e) {
        e.preventDefault()
        let valor = $(this).val()
        $.ajax({
            url: './?action=processNewPedidofm',
            type: 'POST',
            data: {option: 20, unidad: valor},
            success: function (respond) {
                let centros = JSON.parse(respond)
                $("#ccosto").removeAttr("disabled")
                let htmlCentros = "<option value='0'>Seleccione centro de costo...</option>"
                $.each(centros, function (item, value) {
                    htmlCentros += "<option value='" + value.id + "'>" + value.name + "</option>"
                })
                $("#ccostoPro").html(htmlCentros)
            }
        })
    })

    /*========================================================================================*/

    function setAlgoritmoOk() {
        $.ajax({
            type: "POST",
            url: './?action=processNewPedidofm',
            data: {option: 13},
            dataType: "html",
            beforeSend: function () {
            },
            error: function () {
            },
            success: function (data) {
                let datos = JSON.parse(data)
                // console.log(datos)
            }
        });
    } /* ======== FUNCION QUE SE EJECUTA PARA VALIDAR EL ALGORITMO COMO OK */

    function setAlgoritmoFail() {
        $.ajax({
            type: "POST",
            url: './?action=processNewPedidofm',
            data: {option: 14},
            dataType: "html",
            beforeSend: function () {
            },
            error: function () {
            },
            success: function (data) {
                let datos = JSON.parse(data)
                // console.log(datos)
            }
        });
    } /* ======== FUNCION QUE SE EJECUTA PARA VALIDAR EL ALGORITMO COMO FAIL */

    function getPrecio(itcodigo) {
        let cliente = $("#cliente-cod").val()
        $.get("./?action=validaStock", {"option": "4", "producto": itcodigo, "cliente": cliente})
            .done(function (data) {
                let r = JSON.parse(data)
                if (r.sinlista == false) {
                    $.each(r.resultado, function (i, item) {
                        let edit = 'false'
                        if (item.editPvp != '') {
                            edit = "true"
                        }
                        $("#precion").val(item.precio).attr('readonly', edit)
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Debe asociar lista de precio a cliente++++',
                    })
                }
            });
    }

    $(document).on('click', '.btn-load-bod', function (e) {
        let row = $(this)
        if (document.getElementById('pedidoVendedor')) { // desde la ventana de la pedidos normales
            // console.log('1')
            loadStockProductPedidoVendedor(row)
        } else { // desde la ventana de pedidos por vendedor
            loadStockProductPedido(row)
            // console.log(`2`)
        }
    })  /* ==== Click sobre tbody PRODUCTOS / CARGA DE CANTIDADES POR BODEGA ===== */

    function loadStockProductPedido(row) {
        /* FUNCION QUE CARGA LOS SALDOS POR BODEGA DE ACUERDO AL ITCODIGO ENVIADO AL DAR CLIC AL BOTON DE BODEGA QUE SE ENCUENTRA EN CADA FILA DE LA TABLA PRODUCTOS*/
        let fecha = $("#fechaDoc").val()
        let id = row.closest('tr').find('td').eq(1).find('input[type="hidden"]').val()
        let itcodigo = row.closest('tr').find('td').eq(1).text()
        $.ajax({
            type: "POST",
            url: './?action=processFacturacion',
            data: {"option": 12, "valor": itcodigo, "fecha": fecha},
            dataType: "html",
            beforeSend: function () {
            },
            error: function () {
            },
            success: function (data) {
                console.log(data)
                let datos = JSON.parse(data)
                let tableBodyProduct = ''
                if (datos.length == 0) {
                    tableBodyProduct += '<tr><td colspan="2"><b>Sin Stock</b></td></tr>'
                } else {
                    $.each(datos, function (i, item) {
                        tableBodyProduct += '<tr><td>' + item.bodega + '</td><td>' + item.saldo + '</td></tr>'
                    })
                }
                $("#table-body-productsBod").html(tableBodyProduct)
                $("#modalBodegas .modal-header").css('background', '#75A03B')
                $("#modalBodegas .modal-header").text('Stock Bodegas')
                $("#modalBodegas .modal-header").css('color', 'white')
                $("#modalBodegas").modal('show')
            }
        });
    }

    function loadStockProductPedidoVendedor(row) {
        /* FUNCION QUE CARGA LOS SALDOS POR BODEGA DE ACUERDO AL ITCODIGO ENVIADO AL DAR CLIC AL BOTON DE BODEGA QUE SE ENCUENTRA EN CADA FILA DE LA TABLA PRODUCTOS*/
        let fecha = $("#fechaDoc").val()
        let id = row.closest('tr').find('td').eq(1).find('input[type="hidden"]').val()
        let itcodigo = row.closest('tr').find('td').eq(1).text()
        $.ajax({
            type: "POST",
            url: './?action=processFacturacion',
            data: {"option": 26, "valor": itcodigo, "fecha": fecha},
            dataType: "html",
            beforeSend: function () {
            },
            error: function () {
            },
            success: function (dato) {
                // console.log(dato)
                let datos = JSON.parse(dato)
                let tableBodyProduct = ''
                if (datos.length == 0) {
                    tableBodyProduct += '<tr><td colspan="2"><b>Sin Stock</b></td></tr>'
                } else {
                    $.each(datos, function (i, item) {
                        tableBodyProduct += '<tr><td>' + item.bodega + '</td><td>' + item.saldo + '</td></tr>'
                    })
                }
                $("#table-body-productsBod").html(tableBodyProduct)
                $("#modalBodegas .modal-header").css('background', '#75A03B')
                $("#modalBodegas .modal-header").text('Stock Bodegas')
                $("#modalBodegas .modal-header").css('color', 'white')
                $("#modalBodegas").modal('show')
            }
        });
    }

    function loadInfoProduct(producto) {
        let option = 9
        let htmlInfo = ''
        $.ajax({
            type: "POST",
            url: './?action=processFactCred',
            data: {option: option, valor: producto},
            dataType: "html",
            beforeSend: function () {
                // $("#table-tbody-products").html('<tr><td colspan="2">Buscando....</td</tr>');
            },
            error: function () {
                // $("#table-tbody-products").html('<tr><td colspan="2">Sin resultados....</td</tr>');
            },
            success: function (data) {
                // console.log(data)
                let datos = JSON.parse(data)
                if (datos != '') {
                    htmlInfo = "<label class='span-listar'>" + datos + "</label>"
                }
                $("#divInfoDataProduct").html(htmlInfo)
            }
        });
    }  /* ==== CARGA LA INFORMACION ADICIONAL COMO OFERTAS EN LA PARTE INFERIOR DE LA VENTANA COMO UNA SPAN ===== */

    function load_data_bod(producto) {
        let option = 8
        let htmlBodega = ''
        $.ajax({
            type: "POST",
            url: './?action=processNewPedidofm',
            data: {option: option, valor: producto},
            dataType: "html",
            beforeSend: function () {
                // $("#table-tbody-products").html('<tr><td colspan="2">Buscando....</td</tr>');
            },
            error: function () {
                // $("#table-tbody-products").html('<tr><td colspan="2">Sin resultados....</td</tr>');
            },
            success: function (data) {
                let datos = JSON.parse(data)
                htmlBodega = "<table class='table table-bordered table-hovered table-condensed'><thead><tr><th>Cod</th><th>Bodega</th><th>Cantida</th></tr></thead><tbody>"
                $.each(datos, function (i, item) {
                    htmlBodega += '<tr><td>' + item.id + '</td><td>' + item.bodega + '</td><td>' + item.cantidad + '</td></tr>'
                })
                htmlBodega += "</tbody>"
                $("#bodegaTable").html(htmlBodega)
            }
        });
    }  /* ==== MUESTRA EL SALDO DEL PRODUCTO QUE SELECCIONO , EN TODAS LAS BODEGAS Y LO MUESTRA EN UNA VENTANA MODAL ===== */

    /* ============================================================================================================
    *    CARGA LA INFORMACION DE LAS UNIDADES DEL PRODUCTO SELECCIONADO DESDE LA VENTANA DE AYUDA DE PRODUCTOS */
    function loadUnit() {
        let codPro = $("#producto-cod").val()
        $.ajax({
            type: "POST",
            url: "./?action=processNewPedidofm",
            data: {"option": 3, "codigo": codPro},
            success: function (e) {
                let data = JSON.parse(e)
                let option = ''
                $.each(data, function (i, item) {
                    option += '<option tipoUnidad="' + item.tipo + '" factor="' + item.factor + '" value="' + item.unidid + '">' + item.unidtext + '</option>'
                })
                $("#unidadProducto").html(option)
                $("#cantidad").focus()
                loadPriceFromList(1)
            }
        })
    }

    function loadPriceFromList(factor) {
        const unidad = $("#unidadProducto").val() // UNIDAD DEL PRODUCTO
        if (unidad != '') {
            const un = document.getElementById("unidadProducto") // Se selecciona el atributo que identifica el tipo de unidad
            const tipo = $('option:selected', un).attr('tipoUnidad');
            // const formapago = $("#formapago").val()
            const itcodigo = $("#producto-cod").val()
            const ccliente = $("#cliente-cod").val()
            const fecha = $("#fechaDoc").val()
            const cantidad = $("#cantidad").val()
            const pedido = 1

            $.ajax({
                type: "POST",
                url: "./?action=pedidocre_getPrecio_val",
                data: {
                    "unidad": unidad,
                    "formapago": 1,
                    "producto": itcodigo,
                    "emision": 0,
                    "fecha": fecha,
                    "tipoUnidad": tipo,
                    "cliente": ccliente,
                    "pedido": pedido
                },
                success: function (e) {
                    console.log("precioNuveo")
                    // console.log("dd")
                    let dat = JSON.parse(e)
                    // se carga el precio en el campo respectivo , y se valida si este esta disponible para la edicion desde la ventana de facturacion pos
                    if (dat.definicion == true) {
                        $("#precion").val(dat.precio.precio * factor)
                        const nprice = parseFloat(dat.precio.precio * cantidad).toFixed(5)
                        $("#total").val(nprice)
                        // se valida si esta habilitada la opcion para que el usuario edite el precio
                        if (dat.precio.editPvp == 1) {
                            $("#precion").attr('readOnly', false)
                        } else {
                            $("#precion").attr('readOnly', true)
                        }
                    } else {
                        if (dat.lista === true) {
                            $("#precion").val(dat.precio.precio * factor)
                            const nprice = parseFloat(dat.precio.precio * cantidad).toFixed(5)
                            $("#total").val(nprice)
                            // se valida si esta habilitada la opcion para que el usuario edite el precio
                            if (dat.precio.editPvp == 1) {
                                $("#precion").attr('readOnly', false)
                            } else {
                                $("#precion").attr('readOnly', true)
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Debe asociar lista de precio a cliente',
                            })
                        }
                    }
                }
            })
        }
        loadTotal()
    }

    /* ========================================================================================== */

    $(document).on('change', "#unidadProducto", function (e) {
        let factor = $('option:selected', $(this)).attr('factor')
        e.preventDefault()
        loadPriceFromList(factor)

    }) /* ======== CUANDO SE REALIZA EN EL SELECT DE UNIDAD , SE EJECUTA LA FUNCION PARA MOSTRAR PRECIO DEL PRODUCTO , (SU TIPO DE PAGO Y UNIDAD ASOCIADA) */

    $("#precion").blur(function (w) {
        w.preventDefault()
        loadTotal()
    }) /* ====== EJECUTA LA FUNCION QUE CALCULA EL TOTAL DEL PRECIO POR LA CANTIDAD QUE SE VA A INGRESAR AL FORMULARIO DE COMPRA */

    $("#cantidad").blur(function (w) {
        w.preventDefault()
        loadTotal()
    }) /* ====== EJECUTA LA FUNCION QUE CALCULA EL TOTAL DEL PRECIO POR LA CANTIDAD QUE SE VA A INGRESAR AL FORMULARIO DE COMPRA */

    function loadTotal() {
        let cantidad = $("#cantidad").val()
        let precio = $("#precion").val()
        total = cantidad * precio
        $("#total").val(total.toFixed(2))
    } /* FUNCION QUE CALCULA EL TOTAL DEL PRECIO POR LA CANTIDAD QUE SE VA A INGRESAR AL FORMULARIO DE COMPRA */

    $(document).on('click', '#addLineaProducto', function (e) {
        let cant = $("#cantidad").val()
        let precio = $("#precion").val()
        if (cant.length == 0 || cant == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Debe ingresar cantidad...',
            })
        } else {
            if (precio.length == 0 || precio == '' || precio == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'No puede registrar precio 0...',
                })
            } else {
                $.when(loadLineaProduct()).done(loadTotalTd());
            }
        }
    }) /* MEDIANTE LA PULSACION DEL BOTON MAS(+), EJECUTA LA FUNCION PARA ADDLINEA DE COMPRA EL FORMULARIO DE COMPRA  */

    function loadLineaProduct() {
        let producto = $("#producto-text").val()
        if (producto != '') {
            let codigo = $("#producto-cod").val()
            let descripcion = $("#producto-text").val()
            let cantidad = $("#cantidad").val()
            let sinStock = $("#ctrSinStock").val()
            let funcion = $("#funcionesPro").val()
            let unidad = $("#unidadNegocioPro").val()
            let ccosto = $("#ccostoPro").val()

            let desc1 = ''
            let desc2 = ''

            if (document.getElementById("desc1")) {
                desc1 = $("#desc1").val()
            }
            if (document.getElementById("desc2")) {
                desc2 = $("#desc2").val()
            }

            let txtStock = ''
            if (sinStock.length != 0 || sinStock != "") {
                txtStock = " / Sn"
            } else {
                txtStock = ""
            }
            const un = document.getElementById("unidadProducto")
            let unidtipo = $('#unidadProducto').val()
            let unidtext = $('option:selected', un).text()
            // $('option:selected', el).attr('parametro2')
            let precio = $("#precion").val()
            desc1 = 0
            let valDesc1 = 0

            if (document.getElementById('desc1')) {
                desc1 = $("#desc1").val()
                valDesc1 = precio * desc1 / 100
            }
            desc2 = 0
            let valDesc2 = 0
            if (document.getElementById('desc2')) {
                desc2 = $("#desc2").val()
                if (valDesc1 == 0 || valDesc1 == '') {
                    valDesc2 = precio * desc2 / 100
                } else {
                    valDesc2 = (precio - valDesc1) * desc2 / 100
                }
            }
            let nw_precio = precio - valDesc1.toFixed(2) - valDesc2.toFixed(2)
            let total = cantidad * nw_precio

            let iniva = $("#iniva").val()
            let txtIva = ''
            if (iniva != 0) {
                txtIva = " / **"
            } else {
                txtIva = ''
            }
            let option = ''

            option += '<tr>'
            option += '<td>' + codigo + '<input type="hidden" name="iniva" value="' + iniva + '"></td>' +
                '<td>' + descripcion + txtStock + txtIva + '</td>' +
                /*2*/'<td class="alignTextInputNumber"><input type="hidden" value="' + funcion + '">' + cantidad + '</td>' +
                '<td><input type="hidden" name="unidad" value="' + unidtipo + '">' + unidtext + '</td>' +
                /*4*/'<td class="alignTextInputNumber"><input type="hidden" value="' + ccosto + '">' + precio + '</td>' +
                '<td class="alignTextInputNumber"><input type="hidden" value="' + desc1 + '" >' + valDesc1.toFixed(2) + '</td>' +
                '<td class="alignTextInputNumber"><input type="hidden" value="' + desc2 + '" >' + valDesc2.toFixed(2) + '</td>' +
                /*7*/'<td class="alignTextInputNumber"><input type="hidden" value="' + unidad + '">' + nw_precio.toFixed(2) + '</td>' +
                '<td class="subtotal alignTextInputNumber">' + total.toFixed(2) + '</td>' +
                '<td><buttom class="btn btn-deldoc btn-xs btn-removeRow"><i class="glyphicon glyphicon-minus"></i></buttom></td>'
            option += '</tr>'
            $("#tbody-saleProducts").append(option)
            $("#producto-cod").val('')
            $("#producto-text").val('')
            $("#cantidad").val('')
            $("#unidadProducto").val('')
            $("#precion").val('')
            $("#total").val('')
            if (document.getElementById("desc1")) {
                $("#desc1").val('')
            }
            if (document.getElementById("desc2")) {
                $("#desc2").val('')
            }

            $("#formFucProduct").trigger('reset')

        } else {
            Swal.fire({
                icon: 'error',
                title: "Debe seleccionar producto...",
            })
        }
    } /* =========== FUNCION QUE CARGA LOS PRODUCTOS DENTRO DE LA TABLA PARA DESPACHO Y PAGO DE ESTOS ============ */

    $("#formapago").change(function () {
        let tipoforma = $(this).val()
        let option = 11
        $.ajax({
            url: "./?action=processNewPedidofm",
            type: "POST",
            data: {option: option, forma: tipoforma},
            success: function (resp) {
                let datos = JSON.parse(resp)
                $("#totaFormapago").removeAttr("disabled")
                $("#btn-add-pago").removeAttr("disabled")
                if (datos.tip_doc == 1 || datos.tip_doc == 2) {
                    $("#totaFormapago").attr("disabled", true)
                    $("#btn-add-pago").attr("disabled", true)
                    $("#modalFormaPago").modal("toggle")
                    $("#modalFormaPago .modal-header").css("background", '#75A03B')
                    $("#modalFormaPago .modal-header").css("color", 'white')
                    if (datos.req_banco == "S") {
                        $("#entidad").removeAttr("disabled")
                    } else {
                        $("#entidad").attr("disabled", true)
                    }
                    if (datos.req_cta_tar == "S") {
                        $("#cuentatarjeta").removeAttr("disabled")
                    } else {
                        $("#cuentatarjeta").attr("disabled", true)
                    }
                    if (datos.req_pap_chq == "S") {
                        $("#cuentachque").removeAttr("disabled")
                    } else {
                        $("#cuentachque").attr("disabled", true)
                    }
                    if (datos.req_fec_doc == "S") {
                        $("#fechadocumento").removeAttr("disabled")
                    } else {
                        $("#fechadocumento").attr("disabled", true)
                    }
                }
                if (datos[0] !== 'undefined') {
                    let opcion = '<option value="">Seleccione entidad...</option>'
                    $.each(datos[0], function (i, item) {
                        opcion += '<option value="' + item.id + '">' + item.bname + '</option>'
                    })
                    $("#entidad").html(opcion)
                }
            }
        })
        // $.when(changePvpFpago($(this).val())).then(loadTotalTd())
        changePvpFpago($(this).val())
        setTimeout(loadTotalTd, 1000)
    }) /* FUNCION QUE MUESTRA LA VENTANA MODAL DE PAGOS DE ACUERDO A LA FORMA DE PAGO */

    function changePvpFpago(formapago) {
        $("#tbody-saleProducts tr").each(function () {
            let row = $(this)
            // let ptoemision = $("#ptoemision").val()
            let unidad = $("#unidadProducto").val() // UNIDAD DEL PRODUCTO
            let total = ''
            let tipo = row.find('td').eq(3).find('input[type="hidden"]').val()
            let itcodigo = row.find('td').eq(0).text()
            let cantidad = row.find('td').eq(2).text()
            $.ajax({
                type: "POST",
                url: "./?action=processNewPedidofm",
                data: {
                    "option": 16,
                    "unidad": unidad,
                    "formapago": formapago,
                    "producto": itcodigo,
                    "tipo": tipo,
                    "emision": 0
                },
                success: function (precio) {
                    let d = JSON.parse(precio)
                    row.find('td').eq(4).text(d.precio) // Nuevo precio para el producto de la tabla de venta
                    row.find('td').eq(7).text(d.precio) // Nuevo precio para el producto de la tabla de venta
                    total = parseInt(cantidad) * d.precio
                    row.find('td').eq(8).text(total.toFixed(2)) // Nuevo precio para el producto de la tabla de venta
                }
            })
        })
    }

    function loadTotalTd() {
        let sum = ''
        let r = ''
        let valor = 0
        let ventabruta = 0
        let desvalor = 0
        let subtotal = 0
        let ivaval = 0
        let iva = 0
        let total = 0
        let valoriva = 0
        let valorsiva = 0
        // console.log($("#tbody-saleProducts tr"))
        $("#tbody-saleProducts tr").each(function () {
            let row = $(this)
            // console.log(row.find('td').eq(8).text() + '/')
            let iniva = row.find('td').eq(0).find('input[type="hidden"]').val()
            if (iniva == 1) {
                valoriva += parseFloat(row.find('td').eq(8).text())
            } else {
                valorsiva += parseFloat($(this).find('td').eq(8).text())
            }
            valor += parseFloat(row.find('td').eq(8).text())
        });
        $("#bgravada").val(valoriva.toFixed(2))
        $("#bexenta").val(valorsiva.toFixed(2))
        $("#ventab").val(valor.toFixed(2))
        desvalor = $("#dvalor").val()
        subtotal = valor - desvalor
        $("#subtotal").val(subtotal.toFixed(2))
        ivaval = $("#ivaval").val()
        iva = valoriva * ivaval / 100
        $("#iva").val(iva.toFixed(2))
        total = subtotal + iva
        $("#totalpago").val(total.toFixed(2))
        $("#totaFormapago").val(total.toFixed(2))
    } /* LEE Y CALCULA EL TOTAL DE LOS DE ACUERDO A LOS DATOS DE LA TABLA DE VENTA DE PRODUCTO*/

    if (document.getElementById('desc1')) {
        $("#desc1").blur(function () {
            calcularDescuento1()
        })
    } /* === SE EJECUTA PARA CALCULAR EL DESCUENTO # 1 ==== */

    if (document.getElementById('desc2')) {
        $("#desc2").blur(function () {
            calcularDescuento2()
        })
    } /* === SE EJECUTA PARA CALCULAR EL DESCUENTO # 2 ==== */

    function calcularDescuento1() {
        let pvp = $("#precion").val()
        let dsc1 = $("#desc1").val()
        let pvpDes1 = pvp - parseFloat(pvp * dsc1 / 100).toFixed(2)
        let cantidad = $("#cantidad").val()
        let valTotal = cantidad * pvpDes1
        $("#total").val(valTotal.toFixed(2))
    } /* === CALCULA EL DESCUENTO # 1 ==== */

    function calcularDescuento2() {
        if (document.getElementById('desc1')) {
            let pvp = $("#precion").val()
            let dsc1 = $("#desc1").val()
            let pvpDes1 = pvp - parseFloat(pvp * dsc1 / 100).toFixed(2)
            let dsc2 = $("#desc2").val()
            let pvpDes2 = pvpDes1 - parseFloat(pvpDes1 * dsc2 / 100).toFixed(2)
            let cantidad = $("#cantidad").val()
            let valTotal = cantidad * pvpDes2
            $("#total").val(valTotal.toFixed(2))
        } else {
            let pvp = $("#precion").val()
            let dsc2 = $("#desc2").val()
            let pvpDes2 = pvp - parseFloat(pvp * dsc2 / 100).toFixed(2)
            let cantidad = $("#cantidad").val()
            let valTotal = cantidad * pvpDes1
            $("#total").val(valTotal.toFixed(2))
        }
    } /* === CALCULA EL DESCUENTO # 1 ==== */

    $(document).on('click', '.btn-removeRow', function (e) {
        e.preventDefault()
        $(this).parents('tr').remove();
        loadTotalTd()
    }) /* === REMUEVA LA FILA DE LA TABLA DE PRODUCTOS ==== */

    $(document).keydown(function (e) {
        if (e.shiftKey && e.which === 70) {
            $("#formapago").focus()
        }
    }) /* === COLOCA EL FOCUS EN LA FORMA DE PAGO ==== */

    $(document).keydown(function (e) {
        if (e.shiftKey && e.which === 112) {
            modalQuestions()
        }
    }) /* === EJECUTA LA VENTANA MODAL DE AYUDA PARA LA COMBINACION DE TECLAS EN LA VENTANA DE FACTURACION ==== */

    $("#question").click(function (e) {
        e.preventDefault()
        modalQuestions()
    }) /* === EJECUTA LA VENTANA MODAL DE AYUDA PARA LA COMBINACION DE TECLAS EN LA VENTANA DE FACTURACION ==== */

    function modalQuestions() {
        $("#modalQuestions").modal('toggle')
        $("#modalQuestions .modal-header").css('background', '#87ADDD')
        $("#modalQuestions .modal-header").css('color', 'white')
        $("#modalQuestions .modal-title").text('Ayuda Accesos')
    } /* === EJECUTA LA VENTANA MODAL DE AYUDA PARA LA COMBINACION DE TECLAS EN LA VENTANA DE FACTURACION ==== */

    $(document).on("click", "#procesaFormaPago", function (e) {
        e.preventDefault()
        let entid = $("#entidad").val() // ID de la entidad
        let valor = $("#valorpago").val() // Valor para pago por entidades
        let totValor = $("#totalpago").val() // Total valor de la factura
        if (entid == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Debe seleccionar entidad...',
            })
        } else {
            if (parseFloat(valor) <= parseFloat(totValor)) {
                let entidadid = $("#formapago").val(); // Id de la forma de pago
                let entidad = $("#formapago option:selected").text(); // texto de la forma de pago
                let cheq = $("#cuentachque").val()
                let fecha = $("#fechadocumento").val()
                let cuenta = $("#cuentatarjeta").val()
                let enttext = $("#entidad option:selected").text() // texto de la entidad
                // F/P,fecha,#Chq/Doc,Entidad,Cta,Valor
                let htmlFPBod = "<tr class='row-fp' title='" + cuenta + "'><td>" + entidad + "<input type='hidden' value='" + entidadid + "'></td><td>" + fecha + "<input type='hidden' value='" + cuenta + "'></td><td>" + cheq + "</td><td>" + enttext + "<input type='hidden' value='" + entid + "'></td><td>" + valor + "</td></tr>"
                $("#tableBody-fp").append(htmlFPBod)
            }
        }
    }) /* (OTRAS FORMAS DE PAGO) CLICK SOBRE ESTE BOTON CREA LA LINEA DE FORMA DE PAGO EN LA TABLA DE FORMA DE PAGO */

    $(document).on("click", "#btn-add-pago", function (e) {
        e.preventDefault()
        addPago()
    }) /* (EFECTIVO) CLICK SOBRE ESTE BOTON CREA LA LINEA DE FORMA DE PAGO EN LA TABLA DE FORMA DE PAGO */

    $(document).keydown(function (e) {
        if (e.shiftKey && e.which === 76) {
            addPago()
        }
    })  /* (EFECTIVO) CLICK SOBRE ESTE BOTON CREA LA LINEA DE FORMA DE PAGO EN LA TABLA DE FORMA DE PAGO */

    function addPago() {
        let entidadid = $("#formapago").val(); // Id de la forma de pago
        let valorFP = $("#totaFormapago").val() // Valor para pago por entidades / efectivo ó tarjetas
        let valorPG = $("#totalpago").val() // Total valor de la factura / total de factura
        let htmlpagos = ''
        let valorpagado = 0
        let saldo = 0
        let fecha = $("#fechadocumento").val()
        let entidadTxt = $("#formapago option:selected").text(); // texto de la forma de pago

        if (entidadid == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Debe seleccionar forma de pago...',
            })
        } else {
            if (valorFP != 0) {
                let cambio = ''
                if (parseFloat(valorFP) > parseFloat(valorPG)) {
                    cambio = valorFP - valorPG
                    htmlpagos = "<tr class='row-fp' ><td>" + entidadTxt + "<input type='hidden' value='" + entidadid + "'></td><td>" + fecha + "</td><td></td><td></td><td>" + valorPG + "</td></tr>"
                    $("#tableBody-fp").append(htmlpagos)
                    $("#cambio").text("$ " + cambio.toFixed(2))
                    $("#totaFormapago").val(0)
                    $("#totaFormapago").attr('readOnly', 'readOnly')
                } else if (parseFloat(valorFP) <= parseFloat(valorPG)) {
                    if (calTotalInTablaFP() == 0) {
                        saldo = (parseFloat(valorPG) + calTotalInTablaFP()) - parseFloat(valorFP)
                    } else {
                        saldo = parseFloat(valorPG) - (parseFloat(valorFP) + calTotalInTablaFP())
                    }
                    // console.log(calTotalInTablaFP())
                    htmlpagos = "<tr class='row-fp' ><td>" + entidadTxt + "<input type='hidden' value='" + entidadid + "'></td><td>" + fecha + "</td><td></td><td></td><td>" + valorFP + "</td></tr>"
                    $("#tableBody-fp").append(htmlpagos)
                    $("#cambio").text("$ 0.00")
                    $("#totaFormapago").val(saldo.toFixed(2))
                    // $("#totaFormapago").attr('readOnly', 'readOnly')
                }
            }
        }
    }

    function calTotalInTablaFP() {
        let valor = 0
        $("#tableBody-fp tr").each(function () {
            let pago = $(this).find('td').eq(4).text()
            valor += parseFloat(pago)
        })
        return valor
    } /* FUNCION QUE CALCULA EL TOTAL DE PAGOS EN LA TABLA DE FORMAS DE PAGO */

    $("#tableBody-fp").on("click", "tr", function () {
        let valormenos = $(this).find('td').eq(4).text()
        let valor = 0
        let vfp = $("#totalpago").val()
        if ((calTotalInTablaFP() - valormenos) == 0) {
            valor = vfp
        } else {
            valor = valormenos
            // console.log("d")
        }
        $("#totaFormapago").val(valor)
        $(this).remove()
        $("#totaFormapago").removeAttr('readOnly')
    })

    $('#dporcen').on('blur', function () {
        calDesPorcentaje($(this).val())
    })

    function calDesPorcentaje(porcentaje) {
        if (parseInt(porcentaje) == 0 || porcentaje == '') {
            $("#dvalor").val('')
            loadTotalTd()
            $('#dvalor').removeAttr('readOnly')
        } else {
            $('#dvalor').attr('readOnly', 'readOnly')
            let ventb = $("#ventab").val()
            let ivapor = $("#ivaval").val()
            let valorDescuento = ventb * porcentaje / 100
            let grabado = $("#bgravada").val()
            let exento = $("#bexenta").val()
            $("#dvalor").val(valorDescuento.toFixed(2))
            let nsubt = ventb - valorDescuento // subtotal menos descuento
            $("#subtotal").val(nsubt.toFixed(2))
            let ngrab = grabado - (grabado * porcentaje / 100)
            $("#bgravada").val(ngrab.toFixed(2))
            let nexe = exento - (exento * porcentaje / 100)
            $("#bexenta").val(nexe.toFixed(2))
            let niva = ngrab * ivapor / 100
            $("#iva").val(niva)
            let ntotal = ngrab + nexe + niva
            $("#totalpago").val(ntotal.toFixed(2))
            $("#totaFormapago").val(ntotal.toFixed(2))
        }
    } /* === FUNCION CALCULA EL DESCUENTOS == */

    $('#dvalor').on('blur', function () {
        calPorDescuento($(this).val())
    })

    function calPorDescuento(porcentaje) {
        if (parseInt(porcentaje) == 0 || porcentaje == '') {
            $("#dporcen").val('')
            loadTotalTd()
            $('#dporcen').removeAttr('readOnly')
        } else {
            $('#dporcen').attr('readOnly', 'readOnly')
            let descuentoPorcentaje = parseFloat(porcentaje) / 100
            let ventabruta = $("#ventab").val()
            let ivapor = $("#ivaval").val()
            let val_des = Number(ventabruta) * descuentoPorcentaje
            $("#val_des").val(val_des.toFixed(2))
            $("#dporcen").val(descuentoPorcentaje.toFixed(2))
            let grabado = $("#bgravada").val()
            let exento = $("#bexenta").val()
            let desGrab = Number(grabado) - (Number(grabado) * descuentoPorcentaje)
            let subtotal = Number(ventabruta) - val_des
            let desExe = Number(exento) - (Number(exento) * descuentoPorcentaje)
            $("#subtotal").val(subtotal.toFixed(2))
            $("#bgravada").val(desGrab.toFixed(2))
            $("#bexenta").val(desExe.toFixed(2))
            let niva = desGrab * ivapor / 100
            $("#iva").val(niva)
            ntotal = desExe + desGrab
            $("#totalpago").val(ntotal.toFixed(2))
            $("#totaFormapago").val(ntotal.toFixed(2))
        }
    }

    const ctrBod = $("#ctrlBodega").val()
    const ctrSucursal = $('#sucursalid').val()

    // if (ctrBod == 1) {
    loadBodega(ctrSucursal)
    // }

    $(document).on('change', '#sucursalid', function (e) {
        e.preventDefault()
        if (ctrBod == 1) {
            // console.log("ee")
            loadBodega($(this).val())
        } else {
            // console.log("rrr")
        }
    })

    // loadPtoEmi()

    function loadPtoEmi() {
        let id = $('#sucursalid').val()
        // console.log("Sucursal : " + id)
        let option = 5
        $.ajax({
            url: './?action=processNewPedidofm',
            type: 'POST',
            data: {id: id, option: option},
            success: function (e) {
                let s = JSON.parse(e)
                let select = "<option value='" + s.id + "' " + s.selected + " >" + s.emision + "</option>"
                $("#ptoemision").html(select)
                if (s.disabled.length != 0) {
                    $("#ptoemision").attr(s.disabled, true)
                }
            }
        })
    } /* === CARGA EL PUNTO DE EMISION EN LA VENTANA DE FACTURACION  ===*/

    function loadBodega(sucursal) {
        let option = 6
        $.ajax({
            url: './?action=processNewPedidofm',
            type: 'POST',
            data: {id: sucursal, option: option},
            success: function (e) {
                // console.log(e)
                if (e.length != 0) {
                    let data = JSON.parse(e)
                    $.each(data, function (i, item) {
                        option += '<option value="' + item.id + '">' + item.bodega + '</option>'
                    })
                    $("#bodega").html(option)
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Sin Bodegas',
                        text: 'No tiene bodegas asociadas...!',
                        // footer: '<a href="">Why do I have this issue?</a>'
                    })
                }
            }
        })
    }

    $(document).on('click', "#btn-modalDesc", function () {
        viewModalDesc()
    })

    function viewModalDesc() {
        $('#modalDesc').modal('toggle').on('shown.bs.modal', function () {
            let tipodesc = $("#tipodesc").val()
            if (tipodesc != '') {
                if (tipodesc == 1) {
                    $('#desc1').focus()
                } else if (tipodesc == 2) {
                    $('#desc2').focus()
                }
            } else {
                if (document.getElementById('desc1')) {
                    $('#desc1').focus()
                } else {
                    $('#desc2').focus()
                }
            }
        })
        $("#modalDesc .modal-header").css("background", "#75A03B")
        $("#modalDesc .modal-header").css("color", "white")
        // $('#desc1').val('')
    }

    if (document.getElementById('desc1')) {
        $('#desc1').blur(function () {
            let valor = $(this).val()
            $.ajax({
                url: './?action=processNewPedidofm',
                type: 'post',
                data: {option: 7, desc1: valor, tdes: 1},
                success: function (resp) {
                    let r = JSON.parse(resp)
                    // console.log(r)
                    let modal = 0 // VARIABLE QUE VALIDARA EL LLAMAMIENTO DE LA VENTANA MODAL DE DESCUENTOS
                    $.each(r, function (i, item) {
                        if (r.max == 1 && r.sup == 1) {
                            modal = 1
                        } else if (r.max == 1 && r.sup == 0) {
                            Swal.fire({
                                icon: 'error',
                                title: "Valor mayor al permitido",
                            })
                        }
                    })
                    if (modal == 1) {
                        $("#modalSupervisor").modal('toggle')
                        $("#modalSupervisor #tipodesc").val(1)
                        $("#modalSupervisor .modal-header").css("background", "#BCCC2F")
                        $("#modalSupervisor .modal-header").css("color", "white")
                        $("#modalDesc").modal('hide')
                    }
                }
            })
        })
    }

    if (document.getElementById('desc2')) {
        $('#desc2').blur(function () {
            let valor = $(this).val()
            // console.log(valor)
            $.ajax({
                url: './?action=processNewPedidofm',
                type: 'post',
                data: {option: 7, desc2: valor, tdes: 2},
                success: function (resp) {
                    // console.log(resp)
                    let r = JSON.parse(resp)
                    let modal = 0
                    $.each(r, function (i, item) {
                        if (r.max == 1 && r.sup == 1) {
                            modal = 1
                        } else if (r.max == 1 && r.sup == 0) {
                            Swal.fire({
                                icon: 'error',
                                title: "Valor mayor al permitido",
                            })
                        }
                    })
                    if (modal == 1) {
                        $("#modalSupervisor").modal('toggle')
                        $("#modalSupervisor #tipodesc").val(2)
                        $("#modalSupervisor .modal-header").css("background", "#BCCC2F")
                        $("#modalSupervisor .modal-header").css("color", "white")
                        $("#modalDesc").modal('hide')
                    }
                }
            })
        })
    }

    $(document).on("click", ".closemodal", function () {
        viewModalDesc()
    })

    /* ====================================================================
    * SECCION PARA LA VALIDACION DE DATOS EN EL PROCESO DE FACTURACION
    * ===================================================================*/

    function validaPrecionCero() {
        let count = 0
        let respuesta = 0
        $("#tbody-saleProducts tr").each(function () {
            count = count + 1
        })
        if (count >= 1) {
            let valorCero = 0
            let valor = 0
            $("#tbody-saleProducts tr").each(function () {
                let row = $(this)
                if (row.find('td:eq(4)').text() == "0.00") {
                    valorCero = valorCero + 1
                } else {
                    valor = valor + 1
                }
            })
            respuesta = valorCero
        }
        return respuesta
    }

    function validaNumProducts() {
        let count = 0
        let respuesta = 0
        $("#tbody-saleProducts tr").each(function () {
            count = count + 1
        })
        return count
    }

    function validaFormaPagos() {
        let count = 0
        let respuesta = 0
        $("#tableBody-fp tr").each(function () {
            count = count + 1
        })
        return count
    }

    /*==============================================
        MUESTRA LA VENTANA PARA CREAR NUEVO CLIENTE
    * ============================================*/
    $("#newCliente").on('click', function () {
        // r.preventDefault()
        $("#modalClienteNew").modal('show')
    })
    /*==============================================
        MUESTRA LA VENTANA PARA CREAR NUEVO CLIENTE
    * ============================================*/
    $("#processCliente").on('click', function (e) {
        e.preventDefault()
        let datos = new FormData(document.getElementById('form-newCliente'))
        $.ajax({
            url: './?action=processCliente',
            type: 'POST',
            contentType: false,
            processData: false,
            data: datos,
            success: function (e) {
                // console.log(e)
                let res = JSON.parse(e)
                $.each(res, function (i, item) {
                    if (item.error.length == 0) {
                        Swal.fire({
                            icon: 'success',
                            title: "Cliente creado con exito",
                        })
                        $("#cliente-cod").val(item.ruc)
                        $("#cliente-text").val(item.name)
                        $("#modalClienteNew").modal('hide')
                        loadClientesData()
                        $("#form-newCliente").trigger('reset')
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: item.error.substr(2),
                        })
                    }
                })
            }
        })
    })

    // Validacion del ruc ingresado
    $("#identificacion").change(function () {
        let ruc = $(this).val();
        let tipoId = $("#tipoId").val();
        $.ajax({
            url: 'index.php?action=validaRuc',
            type: 'GET',
            data: {ruc: ruc, tipoId: tipoId, option: 3},
            success: function (e) {
                if (e == 0) {
                    alert('Identificacion ingresada incorrecta..!!!')
                    $("#identificacion").val('');
                    $('#identificacion').focus();
                }
            }
        })
    });
    /*=====================================================
        VALIDA EL TIPO DE INDENTIFICACION DEL NUEVO CLIENTE
    * ====================================================*/
    /*            $("#identificacion").on('onFocus', function () {

                })*/
    /*=====================================================
        ACTUALIZACION AGOSTO - 07 - 2021
    * ====================================================*/
    $(document).on('click', '#modalDocumentosVentas', function (e) {
        e.preventDefault()
        $("#modalLoadDocumentos").modal('show')
        let fecha = $('#fechaActual').val()
        loadDodDiarios(fecha)
    })

    function loadDocumentos(documento) {
        $.ajax({
            url: './?action=docFacturados',
            type: 'POST',
            data: {"option": 2, "documento": documento},
            success: function (res) {
                // console.log(res)
                // console.log("loadDOcumentos")
            }
        })
    }

    function loadDodDiarios(fecha) {
        // let caja = $("#numeroCaja").val()
        // let usuario = $("#user_id").val()
        // let fecha = $("#fechaFacturacionReporte")
        let option = 1
        // console.log("funcion")
        $("#table-cab-facturas").DataTable({
            "destroy": true,
            "aaSorting": [[0, "desc"]], // Sort by first column descending
            "ajax": {
                "method": "POST",
                "url": "./?action=docFacturados",
                "data": {"option": option, "fecha": fecha}
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
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> ',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-success'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf"></i> ',
                    titleAttr: 'Exportar a PDF',
                    className: 'btn btn-danger'
                },
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
        $("#modalLoadDetDocumentos").modal('show')

    })

    $(document).on('change', '#fechaActual', function (e) {
        e.preventDefault()
        loadDodDiarios($(this).val())
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
        });
    }

    $(document).on("click", ".btn-imagen", function () {
        let name = $(this).closest('tr').find('td:eq(2)').text()
        let imagen = $(this).attr('imagen')
        if (imagen.length == 0) {
            imagen = "storage/productos/sinimagen.png"
        }
        $("#modalImagen").modal('toggle')
        $("#modalImagen img").attr('src', imagen)
        $("#modalImagen .modal-title").text(name)
    })

    $(document).on("click", "#btn-anular-cotizacion", function () {
        $("#modalDeleteCotizacion").modal("show")
    })

    $(document).on('click', '#procesarAnulacion', function () {
        let numerocotizacion = $("#numeroCotizacion").val()
        Swal.fire({
            html: "<h3><b>Dese anular el pedido # " + numerocotizacion + ". ?</b></h3>",
            // text:
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Anularlo!'
        }).then((result) => {
            if (result.isConfirmed) {
                procesDelete(numerocotizacion)
            }
        })
    })

    function procesDelete(numerocotizacion) {
        // console.log(numeropedido)
        $.ajax({
            url: './?action=cotizacion_delDocumento',
            type: 'POST',
            data: {"cotizacion": numerocotizacion},
            success: function (respon) {
                let r = JSON.parse(respon)
                if (r.substr(0, 1) == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: r.substr(2),
                    })
                    $("#numeroCotizacion").val('')
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: r.substr(2),
                    })
                }

            }
        })
    }

    $(document).on('click', '#viewModalCotizaciones', function () {
        $("#modalCotizaciones").modal("toggle")
        $("#table-cotizaciones").DataTable().clear().destroy()
        $("#table-cotizaciones").DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            },
        })
    })

    $(document).on('click', "#mostrarCotizacionTabla", function () {
        let tipoPag = 0
        if (document.getElementById("pedidoVendedor")) {
            tipoPag = 1
        }
        let desde = $("#desde").val()
        let hasta = $("#hasta").val()
        $("#table-cotizaciones").DataTable().clear().destroy()
        $("#table-cotizaciones").DataTable({
            "destroy": true,
            "keys": true,
            "ajax": {
                "method": "POST",
                "url": "./?action=cotizacion_load",
                "data": {"desde": desde, "hasta": hasta, "option": 8, "tipoPag": tipoPag}
            },
            "columns": [
                {"data": "cotizacion"},
                {"data": "cliente"},
                {"data": "total", "className": "text-right"},
            ],
            "columnDefs": [
                {"width": "10%", "targets": 0},
                {"width": "50%", "targets": 1},
                {"width": "10%", "targets": 2},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            },
        })
    })

    $(document).on('click', '#table-cotizaciones tbody tr', function () {
        let idCotizacion = $(this).closest('tr').find('td').eq(0).text()
        $.ajax({
            url: './?action=cotizacion_getOne',
            type: 'POST',
            async: false,
            data: {'id': idCotizacion},
            success: function (res) {
                let r = JSON.parse(res)
                let option = ''
                $("#ventab").val(r.cabecera.pesubtotal)
                $("#subtotal").val(r.cabecera.pesubtotal)
                $("#bgravada").val(r.cabecera.peivasi)
                $("#bexenta").val(r.cabecera.peivano)
                $("#iva").val(r.cabecera.peiva)
                $("#totalpago").val(r.cabecera.petotal)

                $("#numeroDocumento").val(r.cabecera.peid)
                $("#cliente-cod").val(r.cabecera.clienteruc)
                $("#cliente-text").val(r.cabecera.cliente)
                $("#observacion").val(r.cabecera.observacion)
                $("#fechaDoc").val(r.cabecera.fecha)
                $("#sucursalid").val(r.cabecera.sucursal).trigger("change")
                $("#tipocotizacion").val(r.cabecera.tcid).trigger("change")
                $("#vendedor").val(r.cabecera.vendedor).trigger('çhange')
                $.each(r.data, function (i, item) {
                    option += '<tr>'
                    option += '<td>' + item.codigo + '<input type="hidden" name="iniva" value="' + item.iiva + '"></td>' +
                        '<td>' + item.producto + '</td>' +
                        /*2*/'<td class="alignTextInputNumber"><input type="hidden" value="3">' + item.cantidad + '</td>' +
                        '<td><input type="hidden" name="unidad" value="1">' + item.unidTexto + '</td>' +
                        /*4*/'<td class="alignTextInputNumber"><input type="hidden" value="">' + item.precio + '</td>' +
                        '<td class="alignTextInputNumber"><input type="hidden" value="' + item.desc1 + '" >' + item.desc1 + '</td>' +
                        '<td class="alignTextInputNumber"><input type="hidden" value="' + item.desc2 + '" >' + item.desc2 + '</td>' +
                        /*7*/'<td class="alignTextInputNumber"><input type="hidden" value="' + item.unidad + '">' + item.subtotal + '</td>' +
                        '<td class="subtotal alignTextInputNumber">' + item.total + '</td>' +
                        '<td><buttom class="btn btn-deldoc btn-xs btn-removeRow"><i class="glyphicon glyphicon-minus"></i></buttom></td>'
                    option += '</tr>'
                })
                $('#table-productos-ventas tbody').html(option)
                $("#btn-grabarCotizacion").addClass('noVisual').removeClass("visual")
                $("#btn-updateCotizacion").removeClass("noVisual").addClass('visual')
                loadTotalTd()
            }
        })
    })


})  // fin de funcion document.ready
