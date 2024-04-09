if (document.getElementById('devolucionNCR')) {
    $(document).ready(function () {

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
                        "url": "./?action=processDevolucion",
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

            /* ====================== BUSCAR LOS PRODUCTOS SEGUN LOS CRITERIOS INGRESADOS EN EL CUADRO DE BUSQUEDA  =============================*/
            let objetoProduct = ''
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    objetoProduct = JSON.parse(this.responseText)
                }
            }
            xhttp.open("GET", './?action=processDevolucion&option=10')
            xhttp.send()

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
                            tableBodyProduct += '<tr id="row-' + inc + '" class="remove-class"><td>' + inc + '</td><td>' + item.itcodigo + '<input type="hidden" value="' + item.iniva + '"></td><td>' + item.name + '<input type="hidden" value="' + item.codigo + '"></td><td>' + item.stock + '</td><td><buttom class="btn-load-bod btn btn-listar btn-sm"><i class="glyphicon glyphicon-inbox"></i></buttom></td></tr>'
                        }
                        inc++
                    })
                } else {
                    tableBodyProduct += '<tr><td colspan="3">Buscando...</td></tr>'
                }
                $("#table-tbody-products").html(tableBodyProduct)
            })

            $(document).on("click", "#btn-comprar", function (e) {
                e.preventDefault()
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
                } else if ($("#cliente-cod").val().length == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Debe seleccionar cliente',
                    })
                } else if ($("#sucursalid").val().length == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Debe seleccionar sucursal',
                    })
                } else {
                    procesarCompra()
                }
            })

            validaPrecionCero()

            function procesarCompra(tpago) {

                let pagoTipo = (tpago == 1) ? "contado" : "credito";
                let formData = new FormData(document.getElementById("form-pago-venta"))
                formData.append('option', 18)
                formData.append('tipopago', pagoTipo)
                let docrelacionado = $("#documentoRelacionado").val()
                formData.append('relacionado', docrelacionado)

                let arr = new Array()

                $("#tbody-saleProducts tr").each(function () {
                    let row = $(this)
                    row.find('td:eq(5)').text()
                    let itcodigo = row.find("td").eq(0).text()
                    let nameItcodigo = row.find("td").eq(1).text()
                    // td:not(:last-child)
                    let cantidad = row.find('td').eq(2).find('input[type="number"]').val()
                    let iniva = row.find('td').eq(0).find('input[type="hidden"]').val()
                    let unidad = row.find('td').eq(3).find('input[type="hidden"]').val()

                    let tipoD = row.find('td').eq(7).find('input[type="hidden"]').val()

                    /*let porcDes1 = row.find('td').eq(5).find('input[type="hidden"]').val()
                    let porcDes2 = row.find('td').eq(6).find('input[type="hidden"]').val()*/

                    let funcion = row.find('td').eq(2).find('input[type="hidden"]').val()
                    let iddet = row.find('td').eq(4).find('input[type="hidden"]').val()
                    let unidadNego = row.find('td').eq(7).find('input[type="hidden"]').val()

                    let pvp = row.find("td").eq(4).text()
                    let desc1 = row.find("td").eq(5).text()
                    let desc2 = row.find("td").eq(6).text()
                    let subtotal = row.find("td").eq(5).text()
                    let total = row.find("td").eq(6).text()

                    formData.append("tipoD[]", tipoD)
                    formData.append("iddetalle[]", iddet)
                    formData.append("itcodigo[]", itcodigo)
                    formData.append("producto[]", nameItcodigo)
                    formData.append("iniva[]", iniva)
                    formData.append("precio[]", pvp)
                    // formData.append("descuento1[]", desc1)
                    // formData.append("descuento2[]", desc2)
                    formData.append("subtotal[]", subtotal)
                    formData.append("total[]", total)
                    formData.append("unidad[]", unidad)
                    // formData.append("porcDes1[]", porcDes1)
                    // formData.append("porcDes2[]", porcDes2)
                    formData.append("cantidad[]", cantidad)
                    formData.append("funcion[]", funcion)
                    formData.append("ccosto[]", ccosto)
                    formData.append("unidadNego[]", unidadNego)
                })

                /*$("#tableBody-fp tr").each(function () {
                    let row = $(this)
                    row.find('td:eq(5)').text()
                    // let itcodigo = row.find("td").eq(0).text()
                    // console.log(itcodigo)
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
                })*/

                formData.append('codCliente', $("#cliente-cod").val())
                /*if (document.getElementById("contado").checked) {
                    formData.append('tipopago', "contado")
                } else if (document.getElementById("credito").checked) {
                    formData.append('tipopago', "credito")
                }*/
                formData.append('nameCliente', $("#cliente-text").val())
                formData.append('sucursal', $("#sucursalid").val())
                formData.append('vendedor', $("#vendedorid").val())
                formData.append('fechaDoc', $("#fechaDoc").val())
                formData.append('ptoEmision', $("#ptoemision").val())
                if (document.getElementById("transportista")) {
                    formData.append('transportista', $("#transportista").val())
                }

                formData.append('idFactura', $("#idFactura").val())
                formData.append('undNegoCl', $("#unidadNegocio").val())
                formData.append('ccostoCl', $("#ccosto").val())
                formData.append('funcCl', $("#funciones").val())
                formData.append('comentario', $("#comentario").val())
                formData.append('bodega', $("#bodega").val())

                $.ajax({
                    url: './?action=processNCR',
                    method: "POST",
                    data: formData,
                    processData: false,  // tell jQuery not to process the data
                    contentType: false,   // tell jQuery not to set contentType
                    beforeSend: function () {
                        // setting a timeout
                        $("#myModalLoading").modal('toggle')
                        $("#loading").addClass('loading');
                    },
                    success: function (result) {
                        console.log(result)
                        let datos = JSON.parse(result)
                        if (datos.msj.substr(0, 1) == 0) {
                            $("#myModalLoading").modal('hide')
                            Swal.fire({
                                position: 'center',
                                icon: 'error',
                                html: '<h4>' + datos.msj.substr(2) + '</h4>',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            $(".btn-comprar-procesar").prop('disabled', true)
                            $(".btn-comprar-procesar").attr('disabled', true)
                        } else {
                            $("#myModalLoading").modal('hide')
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                html: '<h4>' + datos.msj.substr(2) + ' notadecredito</h4>',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                let link = "./reportes/inventario/reportDevolucion.php?id=" + datos.id

                                $("#impresion-btn").attr('href', link)
                                $("#impresion-btn").attr('target', "_blank")

                                $("#numerofact").val(datos.documento.substr(6))
                                $(".btn-comprar-procesar").prop('disabled', true)
                                $(".btn-comprar-procesar").attr('disabled', true)
                            });

                            if (datos.tipoEmision == 1) {
                                $("#myModalLoadingRennvio").modal('toggle')
                                processDocuments(datos.documento, datos.id, datos.tipo)
                            }
                        }
                    }
                })
            }

            $(document).on('change', '#sucursalid', function (e) {
                let id = $(this).val()
                let option = 23
                $.ajax({
                    url: './?action=processDevolucion',
                    type: 'POST',
                    data: {id: id, option: option},
                    success: function (e) {
                        console.log(e)
                        let s = JSON.parse(e)
                        $.each(s.datos, function (i, item) {
                            option += '<option value="' + item.id + '">' + item.estab + '-' + item.emision + '</option>'
                        })
                        $("#ptoemision").html(option)
                    }
                })
            })

            function processDocuments(documento, id, tipo) {
                $.ajax({
                    type: "POST",
                    url: './?action=processEnvioDocs',
                    data: {option: 1, "documento": documento, "id": id, "tipo": tipo},
                    beforeSend: function () {
                    },
                    /*error: function (data) {
                        console.log(data)
                    },*/
                    success: function (data) {
                        let dat = JSON.parse(data)
                        // console.log(data)
                        let msjMail = ''
                        // console.log(data)
                        if (dat[1].msjMail !== null) {
                            if (dat[1]['msjMail'][0].substr(0, 1) == 1) {
                                msjMail = '<h4>' + dat[1]['msjMail'][0].substr(2) + '</h4>'
                            }
                        }
                        if (dat[0]['msjAuto'].substr(0, 1) == 1) {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                html: '<h4>' + dat[0]['msjAuto'].substr(2) + ' </h4>' + msjMail,
                                showConfirmButton: false,
                                timer: 1500
                            })
                            $("#myModalLoadingRennvio").modal('hide')
                        } else if (dat[0]['msjAuto'].substr(0, 1) == 0) {
                            Swal.fire({
                                position: 'center',
                                icon: 'error',
                                html: '<h4>' + dat[0]['msjAuto'].substr(2) + ' </h4>' + msjMail,
                                showConfirmButton: false,
                                timer: 1500
                            })
                            $("#myModalLoadingRennvio").modal('hide')
                        }
                    }
                });
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
                xhttpLimit.open("GET", './?action=processDevolucion&option=11&bodega=')
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
                        url: './?action=processDevolucion',
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
                        url: './?action=processDevolucion',
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
                    $("#modalProducto").focus()
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
                let ruc = $(this).parents("tr").find("td").eq(0).text()
                $("#cliente-cod").val(ruc)
                $("#cliente-text").val(name)
                $("#modalCliente").modal('hide')
                validaFucClient(ruc)

            }) /* ======= SELECCIONA EL CLIENTE Y LO CARGA EN LA SECCION DE CLIENTES DE LA VENTANA DE FACTURACION  ======= */

            /* ===============================================================================================
            * Valida el codigo de cliente para visualizar la modal de funciones, costos , unidades de negocios
            * */
            function validaFucClient(codigoCliente) {
                $.ajax({
                    url: './?action=processDevolucion',
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
                    url: './?action=processDevolucion',
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
                let stock = row.parents("tr").find("td").eq(4).text()
                let codigoProduct = row.parents("tr").find("td").eq(1).text()
                let cl = $(this).parent()
                if (parseFloat(stock) == 0.00) {
                    $.ajax({
                        type: "POST",
                        url: "./?action=processDevolucion",
                        data: {"option": 17},
                        success: function (e) {
                            // console.log(e)
                            if (parseInt(e) == 1) {
                                if (!cl.hasClass('cell-focus')) {
                                    $(".remove-class").removeClass('cell-focus');
                                    row.parent().addClass('cell-focus');
                                    selectProducto(row)
                                    $("#ctrSinStock").val(e);
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Sin Stock...',
                                    text: 'Producto no tiene Stock para la venta.!',
                                    // footer: '<a href="">Why do I have this issue?</a>'
                                })
                            }
                        }
                    })
                    validaFucProducts(codigoProduct)

                } else {
                    selectProducto(row)
                    validaFucProducts(codigoProduct)

                    let cl = $(this).parent()
                    if (!cl.hasClass('cell-focus')) {
                        $(".remove-class").removeClass('cell-focus');
                        $(this).parent().addClass('cell-focus');
                        $("#ctrSinStock").val('');
                    }
                }
                /*else{
                    $(this).parent().removeClass('cell-focus');
                }*/
            }) /* MEDIANTE CLICK SOBRE TD DE LA TABLA DE PRODUCTO EJECTUA LA FUNCION PARA VALIDAD LA SELECCION DE ESTE */

            /*=================================================================================
            *   FUNCION PARA VALIDAR LA CARGA DE FUNCION , CENTRO DE COSTO , UNIDADES DE NEGOCIO
            *  */
            function validaFucProducts(codigoProd) {

                $.ajax({
                    url: './?action=processDevolucion',
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
                    url: './?action=processDevolucion',
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
                    url: './?action=processDevolucion',
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
                    url: './?action=processDevolucion',
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

            function selectProducto(row) {
                let name = row.parents("tr").find("td").eq(2).text()
                let itcodigo = row.parents("tr").find("td").eq(1).text()
                let iniva = row.parent('tr').find('td').eq(1).find('input[type="hidden"]').val()
                let id = row.parent('tr').find('td').eq(2).find('input[type="hidden"]').val()
                $("#producto-cod").val(itcodigo)
                $("#iniva").val(iniva)
                $("#producto-text").val(name)
                // $('#modalProducto').modal('hide') // ESCONDE LA VENTANA DE PRODUCTO LUEGO DE SELECCIONADO
                loadUnit()
                loadInfoProduct(itcodigo) // VISUALIZA LA INFORMACION QUE PUEDA TENER EL PRODUCTO (PROMOCION O DETALLE)
            } /* ============ VALIDA LA SELECCION DE UN PRODUCTO DESDE LA VENTANA DE PRODUCTOS PARA SU FACTURACION  =============== */

            $(document).on('click', '.btn-load-bod', function (e) {
                /* FUNCION QUE CARGA LOS SALDOS POR BODEGA DE ACUERDO AL ITCODIGO ENVIADO AL DAR CLIC AL BOTON DE BODEGA QUE SE ENCUENTRA EN CADA FILA DE LA TABLA PRODUCTOS*/
                event.preventDefault()
                let id = $(this).closest('tr').find('td').eq(1).find('input[type="hidden"]').val()
                let itcodigo = $(this).closest('tr').find('td').eq(1).text()
                // console.log(itcodigo)
                $.ajax({
                    type: "POST",
                    url: './?action=processDevolucion',
                    data: {option: 12, valor: itcodigo},
                    dataType: "html",
                    beforeSend: function () {
                    },
                    error: function () {
                    },
                    success: function (data) {
                        let datos = JSON.parse(data)
                        let tableBodyProduct = ''
                        $.each(datos, function (i, item) {
                            tableBodyProduct += '<tr><td>' + item.bodega + '</td><td>' + item.saldo + '</td></tr>'
                        })
                        $("#table-body-productsBod").html(tableBodyProduct)
                        $("#modalBodegas .modal-header").css('background', '#75A03B')
                        $("#modalBodegas .modal-header").text('Stock Bodegas')
                        $("#modalBodegas .modal-header").css('color', 'white')
                        $("#modalBodegas").modal('show')
                    }
                });
            })  /* ==== Click sobre tbody PRODUCTOS / CARGA DE CANTIDADES POR BODEGA ===== */

            function loadInfoProduct(producto) {
                let option = 9
                let htmlInfo = ''
                $.ajax({
                    type: "POST",
                    url: './?action=processDevolucion',
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
                        if (datos.length > 0 && datos.length != '') {
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
                    url: './?action=processDevolucion',
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
                    url: "./?action=processDevolucion",
                    data: {"option": 3, "codigo": codPro},
                    success: function (e) {
                        let data = JSON.parse(e)
                        let option = ''
                        $.each(data, function (i, item) {
                            option += '<option tipoUnidad="' + item.tipo + '" value="' + item.unidid + '">' + item.unidtext + '</option>'
                        })
                        $("#unidadProducto").html(option)
                        $("#cantidad").focus()
                        loadPriceFromList()
                    }
                })
            }

            /* ============================================================================================================ */

            /* ============================================================================================================
            *  FUNCION QUE SE EJECUTA AL CARGAR EL PRODUCTO PARA MOSTRAR LOS PRECIOS SEGUN LA LISTA DEL PRECIOS , (SU TIPO DE PAGO Y UNIDAD ASOCIADA) */
            function loadPriceFromList() {
                /*  */
                const unidad = $("#unidadProducto").val() // UNIDAD DEL PRODUCTO
                const ptoemision = $("#ptoemision").val()
                if (unidad != '') {
                    const un = document.getElementById("unidadProducto") // Se selecciona el atributo que identifica el tipo de unidad
                    const tipo = $('option:selected', un).attr('tipoUnidad');
                    const formapago = $("#formapago").val()
                    const itcodigo = $("#producto-cod").val()
                    const ccliente = $("#cliente-cod").val()
                    const fecha = $("#fechaDoc").val()
                    const cantidad = $("#cantidad").val()

                    $.ajax({
                        type: "POST",
                        url: "./?action=posFac",
                        data: {
                            // "option": 15,
                            "unidad": unidad,
                            "formapago": formapago,
                            "producto": itcodigo,
                            "emision": ptoemision,
                            "fecha": fecha,
                            "tipoUnidad": tipo,
                            "cliente": ccliente
                        },
                        success: function (e) {
                            // console.log(e)
                            let dat = JSON.parse(e)
                            // se carga el precio en el campo respectivo , y se valida si este esta disponible para la edicion desde la ventana de facturacion pos
                            $("#precion").val(dat.precio)
                            const nprice = parseFloat(dat.precio * cantidad).toFixed(5)
                            $("#total").val(nprice)
                            // se valida si esta habilitada la opcion para que el usuario edite el precio
                            if (dat.editPvp == 1) {
                                $("#precion").removeAttr('readOnly')
                            } else {
                                $("#precion").attr('readOnly', 'readOnly')
                            }

                        }
                    })
                }
                loadTotal()
            }

            /* ========================================================================================== */

            $(document).on('change', "#unidadProducto", function (e) {
                e.preventDefault()
                loadPriceFromList()

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
                e.preventDefault()
                let cant = $("#cantidad").val()
                if (cant.length == 0 || cant == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Debe ingresar cantidad...',
                    })
                } else {
                    $.when(loadLineaProduct()).done(loadTotalTd());
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
                    let unidtipo = $('option:selected', un).attr('tipoUnidad')
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
                        /*2*/'<td><input type="hidden" value="' + funcion + '">' + cantidad + '</td>' +
                        '<td><input type="hidden" name="unidad" value="' + unidtipo + '">' + unidtext + '</td>' +
                        /*4*/'<td><input type="hidden" value="' + ccosto + '">' + precio + '</td>' +
                        '<td><input type="hidden" value="' + desc1 + '" >' + valDesc1.toFixed(2) + '</td>' +
                        '<td><input type="hidden" value="' + desc2 + '" >' + valDesc2.toFixed(2) + '</td>' +
                        /*7*/'<td><input type="hidden" value="' + unidad + '">' + nw_precio.toFixed(2) + '</td>' +
                        '<td class="subtotal">' + total.toFixed(2) + '</td>' +
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
                    url: "./?action=processDevolucion",
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
                    let ptoemision = $("#ptoemision").val()
                    let unidad = $("#unidadProducto").val() // UNIDAD DEL PRODUCTO
                    let total = ''
                    let tipo = row.find('td').eq(3).find('input[type="hidden"]').val()
                    let itcodigo = row.find('td').eq(0).text()
                    let cantidad = row.find('td').eq(2).text()
                    $.ajax({
                        type: "POST",
                        url: "./?action=processDevolucion",
                        data: {
                            "option": 16,
                            "unidad": unidad,
                            "formapago": formapago,
                            "producto": itcodigo,
                            "tipo": tipo,
                            "emision": ptoemision
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
                        valoriva += parseFloat(row.find('td').eq(6).text())
                    } else {
                        valorsiva += parseFloat($(this).find('td').eq(6).text())
                    }
                    valor += parseFloat(row.find('td').eq(6).text())
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
                bloqueFecha()
                console.log("remove")
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

            if (ctrBod == 1) {
                loadBodega(ctrSucursal)
            }

            $(document).on('change', '#sucursalid', function (e) {
                e.preventDefault()
                loadBodega($(this).val())
            })

            loadPtoEmi()

            function loadPtoEmi() {
                let id = $('#sucursalid').val()
                let option = 5
                $.ajax({
                    url: './?action=processDevolucion',
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
                    url: './?action=processDevolucion',
                    type: 'POST',
                    data: {id: sucursal, option: option},
                    success: function (e) {
                        let data = JSON.parse(e)
                        if (data.validacion == 1) {
                            if (data.total != 0) {
                                $.each(data.bodegas, function (i, item) {
                                    option += '<option value="' + item.id + '">' + item.bodega + '</option>'
                                })
                                $("#bodega").html(option)
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Sin Bodegas',
                                    text: 'No tiene bodegas asociadas...!',
                                })
                            }
                        } else {
                            if (data.total != 0) {
                                $.each(data.bodegas, function (i, item) {
                                    option += '<option value="' + item.id + '">' + item.bodega + '</option>'
                                })
                                $("#bodega").html(option)
                            }
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
                        url: './?action=processDevolucion',
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
                        url: './?action=processDevolucion',
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

            /*====================================================================
            *               ACTUALIZACION 11 DE AGOSTO DEL 2021
            * ===================================================================*/

            $(document).on('blur', "#documentoRelacionado", function () {
                if ($(this).val() != '') {
                    loadDataNCR($(this).val())
                }
            })
            /* EVENTO QUE CONTROLO EL INGRESO DE SOLO NUMEROS EN INPUT DE DOCUMENTO RELACIONADO */
            $('#documentoRelacionado').keyup(function () {
                this.value = (this.value + '').replace(/[^0-9]/g, '');
            });


            function loadDataNCR(documentoRelacionado) {
                $.ajax({
                    url: './?action=processDevolucion',
                    type: 'POST',
                    data: {"factura": documentoRelacionado, "option": 22},
                    success: function (resp) {
                        let datos = JSON.parse(resp)
                        if (datos.productos == null) {
                            Swal.fire({
                                icon: 'error',
                                title: "Numero de factura incorrecto",
                            })
                            $("#documentoRelacionado").val('')
                        } else {
                            let html = ''
                            let tiva = 0
                            let tsiva = 0
                            let valor = 0
                            let cliente = datos.cabecera.cliente
                            let ruc = datos.cabecera.ruc
                            let idFactura = datos.cabecera.idFactura
                            let vendedor = datos.cabecera.vendedor

                            if (datos.productos == null) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Documento no tiene unidades disponibles para esta transaccion',
                                })
                            } else {
                                let p = 0
                                $.each(datos.productos, function (i, etem) {
                                    let total = etem.cantidad * etem.precio
                                    html += '<tr>'
                                    html += /*1*/'<td>' + etem.itcodigo + '<input type="hidden" name="iniva" value="' +
                                        etem.iniva + '"></td>' + /*2*/'<td><input type="hidden" value="' + etem.iddeta + '">' + etem.name + '</td>' + /*3*/'<td><input type="number" id="' + etem.id + '" class="cantDetNcr col-md-1 col-lg-1 form-control" precio="' + etem.cantidad + '" value="' + etem.cantidad + '" ></td><td><input name="unidadid" type="hidden" value="' + etem.undid + '"> ' + etem.undtext + '</td>' +
                                        /*4*/'<td><input type="hidden" value="' + etem.iddeta + '">' + etem.precio + '</td>' +
                                        /*7*/'<td><input type="hidden" value="' + etem.undid + '">' + parseFloat(total).toFixed(2) + '</td>' +
                                        '<td class="subtotal">' + parseFloat(total).toFixed(2) + '</td>' +
                                        '<td><input type="hidden" value="' + etem.proD + '"><buttom class="btn btn-deldoc btn-xs btn-removeRow"><i class="glyphicon glyphicon-minus"></i></buttom></td>'
                                    html += '</tr>'
                                    p++
                                })
                                if (p >= 1) {
                                    $("#documentoRelacionado").attr("readonly", true)
                                }
                                $("#cliente-cod").val(ruc)
                                $("#cliente-text").val(cliente)
                                $("#idFactura").val(idFactura)
                                /**
                                 * subtotal
                                 * ivasi
                                 * ivano
                                 * iva
                                 * porDescuento
                                 * valDescuento
                                 * totaldoc*/

                                $("#bgravada").val(datos.cabecera.ivasi)
                                $("#bexenta").val(datos.cabecera.ivano)
                                $("#ventab").val(datos.cabecera.subtotal)
                                $("#subtotal").val(datos.cabecera.subtotal)

                                $("#dvalor").val(datos.cabecera.valDescuento)
                                $("#iva").val(datos.cabecera.iva)
                                $("#totalpago").val(datos.cabecera.totaldoc)
                                $("#totaFormapago").val(datos.cabecera.totaldoc)

                                $("#tbody-saleProducts").html(html)
                            }
                        }
                    }
                })
            }

            function bloqueFecha() {
                // $("#table-productos-ventas tbody tr").length
                let filas = $("#table-productos-ventas").find('tbody tr').length;
                if (filas >= 1) {
                    console.log("111")
                    $("#documentoRelacionado").attr("readonly", true)
                } else {
                    console.log("000")
                    $("#documentoRelacionado").attr("readonly", false)
                }
            }

            $(document).on('blur', '.cantDetNcr', function (e) {
                if (parseFloat($(this).val()) <= parseFloat($(this).attr('precio'))) {
                    e.preventDefault()
                    let id = $(this).attr('id')
                    let valor = $(this).val()
                    $(this).val(parseFloat(valor).toFixed(4)) // se toma el precio del producto
                    let can = $(this).closest('tr').find('td:eq(4)').text()
                    let nvalor = parseFloat(valor) * can
                    $(this).closest('tr').find('td:eq(5)').text(nvalor.toFixed(5))
                    $(this).closest('tr').find('td:eq(6)').text(nvalor.toFixed(5))
                    loadTotalTd()
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: "Valor incorrecto",
                    }).then(() => {
                        let precio = $(this).attr('precio')
                        $(this).val(precio).focus()
                    });
                }
            })
        }
    )
}


/*
* Keyboard key Pressed	IE JavaScript Key Code value	Firefox JavaScript Key Code value
backspace	8	8
tab	9	9
enter	13	13
shift	16	16
ctrl	17	17
alt	18	18
pause/break	19	19
caps lock	20	20
escape	27	27
page up	33	33
Space	32	32
page down	34	34
end	35	35
home	36	36
arrow left	37	37
arrow up	38	38
arrow right	39	39
arrow down	40	40
print screen	44	44
insert	45	45
delete	46	46
0	48	48
1	49	49
2	50	50
3	51	51
4	52	52
5	53	53
6	54	54
7	55	55
8	56	56
9	57	57
a	65	65
b	66	66
c	67	67
d	68	68
e	69	69
f	70	70
g	71	71
h	72	72
i	73	73
j	74	74
k	75	75
l	76	76
m	77	77
n	78	78
o	79	79
p	80	80
q	81	81
r	82	82
s	83	83
t	84	84
u	85	85
v	86	86
w	87	87
x	88	88
y	89	89
z	90	90
left window key	91	91
right window key	92	92
select key	93	93
numpad 0	96	96
numpad 1	97	97
numpad 2	98	98
numpad 3	99	99
numpad 4	100	100
numpad 5	101	101
numpad 6	102	102
numpad 7	103	103
numpad 8	104	104
numpad 9	105	105
multiply	106	106
add	107	107
subtract	109	109
decimal point	110	110
divide	111	111
f1	112	112
f2	113	113
f3	114	114
f4	115	115
f5	116	116
f6	117	117
f7	118	118
f8	119	119
f9	120	120
f10	121	121
f11	122	122
f12	123	123
num lock	144	144
scroll lock	145	145
My Computer (multimedia keyboard)	182	182
My Calculator (multimedia keyboard)	183	183
semi-colon	186	186
equal sign	187	107
comma	188	188
dash	189	189
period	190	190
forward slash	191	191
open bracket	219	219
back slash	220	220
close bracket	221	221
single quote	222	222
* */