if (document.getElementById('egresoInventario')) {
    $(document).ready(function () {

        // loadDataPersonCodigo()
        $('[data-toggle="tooltip"]').tooltip()

        $(document).on("keydown", ".checkDecimales", function (r) {
            let currentValue = r.target.value;
            let regex = new RegExp("^\\d{0,9}(\\.\\d{1," + calDecimales() + "})?$")
            setTimeout(function () {
                let newValue = r.target.value
                if (!regex.test(newValue)) {
                    r.target.value = currentValue;
                }
            }, 0);
        })

        // devulve dinamicamente el numero de decimales que acepta la ventana de ingreso para los procesos de inventario
        function calDecimales() {
            let rs = ''
            $.ajax({
                async: false,
                url: './?action=configuraciones',
                type: 'POST',
                data: {"option": 1},
                success: function (res) {
                    rs = res
                }
            })
            return rs
        }


        $("#viewModalPedidos").prop('disabled', true)

        $("#viewModalPedidos").attr('disabled', true)

        $(document).on('change', '#tipoegreso', function () {
            let texto = $(this).find('option:selected').text();
            if (texto === "Venta") {
                let d = " ";
                $("#btnCliente").attr('disabled', false)
                $("#cliente-cod").attr('disabled', false)
                $("#btn-graba-ingreso").removeClass("visBottom").addClass("noVisBottom")
                $("#btn-graba-ingreso-venta").removeClass("noVisBottom").addClass("visBottom")
                $("#viewModalPedidos").prop('disabled', false)
                $("#viewModalPedidos").attr('disabled', false)
            } else {
                $("#viewModalPedidos").prop('disabled', true)
                $("#viewModalPedidos").attr('disabled', true)
                $("#btnCliente").attr('disabled', true)
                $("#cliente-cod").attr('disabled', true)
                $("#btn-graba-ingreso-venta").removeClass("visBottom").addClass("noVisBottom")
                $("#btn-graba-ingreso").removeClass("noVisBottom").addClass("visBottom")
                $("#numeropedido").val('')
                $("#totalIngresosCosto").val('')
                $("#tbodyIngresos").html("")
            }
        })

        $(document).on('click', '#btnCliente', function (e) {
            viewModalClientes()
        })

        loadClientesData()

        function loadClientesData() {
            let opcion = 1
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

        $(document).on('click', '#tbody-cliente tr td', function () {
            let name = $(this).parents("tr").find("td").eq(1).text()
            let ruc = $(this).parents("tr").find("td").eq(0).text()
            $("#cliente-cod").val(ruc)
            $("#cliente-text").val(name)
            $("#modalCliente").modal('hide')
        }) /* ======= SELECCIONA EL CLIENTE Y LO CARGA EN LA SECCION DE CLIENTES DE LA VENTANA DE FACTURACION  ======= */

        function viewModalClientes() {
            $("#modalCliente").modal('toggle').on("shown.bs.modal", function () {
                $('#modalCliente .dataTables_filter input').focus();
            })
            $("#modalCliente .modal-header").css("background-color", "#84AC3B")
        }/* ================ SE EJECUTA LA VISUALIZACION DE LA VENTANA MODAL CLIENTES ===============*/

        function loadDataPersonCodigo() {
            let option = 4
            let viewHtml = ''
            $.ajax({
                url: "./?action=productLoad",
                type: "POST",
                data: {option: option},
                success: function (data) {
                    // console.log(data)
                }
            })
        }

        $(document).on('click', '#btnProduct', function (e) {
            e.preventDefault()
            $("#modalProducto").modal('show')
            // viewTableLoad()
        })

        $(document).on("keyup", "#productocod", function () {
            this.value = this.value.toUpperCase();
        })

        function loadProducto() {
            $.ajax({
                type: "POST",
                url: './?action=productLoad',
                data: {option: 1},
                dataType: "html",
                beforeSend: function () {
                },
                error: function () {
                },
                success: function (data) {
                    let datos = JSON.parse(data)
                    // console.log(datos)
                    let tableBodyProduct = ''
                    let t = 1
                    // $("#validaTipoProducto").val(1)
                    $.each(datos, function (i, item) {
                        tableBodyProduct += '<tr  id="row-' + t + '" class="remove-class"><td>' + t + '</td><td>' + item.itcodigo + '</td><td>' + item.name + '<input type="hidden" value="' + item.codigo + '"></td><td>' + item.unidad1 + '</td><td>' + item.stock + '</td><td><button class="btn-load-bod btn btn-listar btn-sm"><i class="glyphicon glyphicon-inbox"></i></button></td></tr>'
                        t++
                    })
                    $("#table-products-ingresos").html(tableBodyProduct)
                }
            });
        }

        viewTableLoad()

        function viewTableLoad() {
            $("#table-products-ingresos").DataTable().clear().destroy()
            $("#table-products-ingresos").DataTable({
                "bProcessing": true,
                "sAjaxSource": "./?action=getProductsTable",
                "aoColumns": [
                    {mData: 'itcodigo'},
                    {mData: 'name'},
                    {mData: 'botones', className: "text-center"}
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

        $(document).on('click', '.btn-imagen', function () {
            let srcImagen = $(this).attr('id')
            Swal.fire({
                // title: 'Sweet!',
                // text: 'Modal with a custom image.',
                imageUrl: 'https://smarttag-bi.com/' + srcImagen,
                imageWidth: 1024,
                imageHeight: 300,
                // imageAlt: 'Custom image',
            })
        })

        $(document).on('click', '.btn-stock', function () {
            let itcodigo = $(this).attr('id')
            let fecha = $("#fecha").val()
            let bodega = $("#bodega").val()
            $.ajax({
                url: "./?action=productLoad",
                type: "POST",
                data: {"itcodigo": itcodigo, "fecha": fecha, "bodega": bodega, "option": 8},
                success: function (result) {
                    let r = JSON.parse(result)
                    if (r[0].stock != 0) {
                        Swal.fire({
                            title: '<strong>Bodega : ' + r[0].bodega + '</strong>',
                            icon: 'info',
                            html:
                                'Stock : <b>' + r[0].stock + '</b>, ',
                            showCloseButton: true,

                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Bodega :' + r[0].bodega,
                            text: 'No tiene stock disponible',
                            // footer: '<a href="">Why do I have this issue?</a>'
                        })
                    }
                }
            })
        })

        $(document).on('click', '#table-products-ingresos tbody tr td:not(:last-child)', function () {
            let itcodigo = $(this).parents("tr").find("td").eq(0).text()
            let name = $(this).parents("tr").find("td").eq(1).text()
            $("#productocod").val(itcodigo.trim())
            $("#productText").val(name.trim())
            validaStock(itcodigo)
            loadUnit()
            validaFucProducts(itcodigo.trim())
            loadSaldoDiarioProduct(itcodigo.trim())
            // loadStockProduct(itcodigo.trim())
            $("#modalProducto").modal('hide')
        })

        function validaStock(codProduct) {
            let fecha = $("#fecha").val()
            let bodega = $("#bodega").val()
            $.get("./?action=validaStock", {
                "option": "1",
                "producto": codProduct,
                "bodega": bodega,
                "fecha": fecha
            })
                .done(function (data) {
                    let t = JSON.parse(data)
                    $("#stock").val(t)
                    console.log(t)
                    if (t.stock == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: "Producto no tiene unidades disponibles",
                        })
                        $("#cantidad").prop("disabled", true)
                    } else {
                        $("#cantidad").prop("disabled", false)
                    }
                });
        }

        function loadStockProduct(codigo) {
            let bodega = $("#bodega").val()
            let fecha = $("#fecha").val()
            $.ajax({
                type: "POST",
                url: "./?action=productLoad",
                // cache : false,
                // processData: false,
                data: {bodega: bodega, producto: codigo, fecha: fecha, option: 6},
                success: function (e) {
                    // console.log(e)
                    let sp = JSON.parse(e)
                    $("#stock").val(sp.saldo)
                }
            })
        }

        function loadUnit() {
            let codPro = $("#productocod").val().trim()
            $.ajax({
                type: "POST",
                url: "./?action=processFacturacion",
                data: {"option": 22, "codigo": codPro},
                success: function (e) {
                    let data = JSON.parse(e)
                    let option = ''
                    $.each(data, function (i, item) {
                        option += '<option tipoUnidad="' + item.tipo + '" value="' + item.unidid + '" idunidad="' + item.unidid + '">' + item.unidtext + '</option>'
                    })
                    $("#unidad").html(option)
                    $("#cantidad").focus()
                }
            })
        }

        function validaFucProducts(codigoProd) {

            $.ajax({
                url: './?action=processFacturacion',
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

        $(document).on("click", "#addProductTable", function () {
            insertLineProduct()
        })

        $(document).on("blur", "#costo", function (r) {
            r.preventDefault()
            let ttcosto = valTtcosto($("#cantidad").val(), $(this).val())

            $("#ttcosto").val(ttcosto)
        })
        /** CALCULAR EL TOTAL QUE SE VISUALIZA EN LA VENTANA DE EGRESOS PARA INGRESAR LA LINEA DEL PRODUCTO */
        function valTtcosto(cantidad, costo) {
            let t = parseFloat(cantidad) * parseFloat(costo)
            return t.toFixed(calDecimales())
        }

        function calTotalProductos(totalcosto) {
            let sumTotales = 0
            if (document.getElementsByClassName('totalesCosto')) {
                // console.log("existe")
                $(".totalesCosto").each(function () {
                    sumTotales += parseFloat($(this).text());
                });
                $("#totalIngresosCosto").val(sumTotales.toFixed(calDecimales()))
            } else {
                $("#totalIngresosCosto").val(totalcosto.toFixed(calDecimales()))

            }
            // const valt = $("#totalIngresosCosto").val()
            // if (valt.length == 0) {
            //     $("#totalIngresosCosto").val(formatNumber(totalcosto))
            // } else {
            //     let nttotalc = convertirFormato(valt) + parseFloat(totalcosto)
            //     // console.log(valt)
            //     $("#totalIngresosCosto").val(formatNumber(nttotalc))
            // }
        }

        $(document).on("blur", "#productocod", function () {
            let producto = $(this).val()
            if (producto === '') {

            } else {
                console.log(producto)
                $.post("./?action=productLoad", {option: 5, codigo: producto})
                    .done(function (data) {
                        let res = JSON.parse(data)
                        console.log(res)
                        if (res.estado == 1) {
                            loadUnit()
                            validaFucProducts(producto)
                            loadSaldoDiarioProduct(producto)
                            $("#productText").val(res.name)
                            validaStock(producto)
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Codigo Incorrecto',
                                text: 'Producto no Existe',
                                // footer: '<a href="">Why do I have this issue?</a>'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $("#productocod").val('').focus()
                                    // $("#productocod")
                                }
                            })
                        }
                    });
            }
        })

        function insertLineProduct() {
            $("#fecha").prop('readonly', true)
            let itcodigo = $("#productocod").val()
            let texto = $("#productText").val()
            let cantidad = $("#cantidad").val()
            let stock = $("#stock").val()
            if (parseFloat(cantidad) <= parseFloat(stock)) {
                if (itcodigo.length != 0) {
                    if (cantidad.length != 0) {
                        if (texto.length != 0) {
                            let ncosto = "0.00"
                            let costo = parseFloat($("#costo").val()).toFixed(calDecimales())
                            if (costo == "") {
                                ncosto = "0.00"
                            } else {
                                ncosto = costo
                            }
                            let nttcosto = valTtcosto(cantidad, ncosto)

                            $("#ttcosto").val(nttcosto)

                            let name = $("#productText").val()

                            let funcion = ($("#funcionesPro").val() == null) ? 0 : $("#funcionesPro").val();
                            let negocio = ($("#unidadNegocioPro").val() == null) ? 0 : $("#unidadNegocioPro").val();
                            let ccosto = ($("#ccostoPro").val() == null) ? 0 : $("#ccostoPro").val();
                            let costounid = ($("#costoHidden").val() == null) ? 0 : $("#costoHidden").val();

                            let uni = document.getElementById("unidad")
                            let unidtipo = $('option:selected', uni).attr('tipoUnidad')
                            let unidid = $('option:selected', uni).attr('idunidad')
                            let unidtext = $('option:selected', uni).text()
                            let totalcosto = $("#totalCosto").val() // total de los costos del campo inferior de la tabla de productos ingresados

                            let ttcosto = $("#ttcosto").val()

                            const fu = funcion + ',' + negocio + ',' + ccosto
                            const unidades = unidtipo + ',' + unidid
                            /* == funcion,costo,negocio ==*/
                            let row = '<tr><td><input type="hidden" class="fun" value="' + fu + '" >' + itcodigo + '</td><td>' + name + '</td><td><input type="hidden" class="tipounidad" value="' + unidades + '" >' + unidtext + '</td><td class="text-right">' + parseFloat(cantidad).toFixed(calDecimales()) + '</td><td class="text-right"><input type="hidden" value="' + costounid + '">' + ncosto + '</td><td class="text-right totalesCosto">' + parseFloat(ttcosto).toFixed(calDecimales()) + '<input type="hidden" value="' + totalcosto + '"></td><td><button class="btn-remove-row-eg btn btn-xs btn-danger"><i class="fa fa-remove" aria-hidden="true"></i></button></td></tr>'

                            $("#tbodyIngresos").append(row)
                            $(".reset-valor").val('')
                            $("#productocod").focus()

                            calTotalProductos(ttcosto)

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Debe seleccionar producto',
                            })
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Debe ingresar cantidad',
                        })
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Debe seleccionar producto',
                    })
                }
            } else {
                $("#cantidad").val('').focus()
                Swal.fire({
                    icon: 'error',
                    title: 'Cantidad ingresada no valida',
                })
            }
        }

        $(document).keydown(function (e) {
            if (e.which === 45) {
                insertLineProduct()
                //$.when().then(loadTotalTd());
            }
        })

        function valRowsProductos() {
            let t = 0
            $("#tbodyIngresos tr").each(function () {
                t++
            })
            return t
        }

        $(document).on('click', '#btn-graba-ingreso', function (w) {
            w.preventDefault()

            if ($("#observacion").val() != '') {
                if (valRowsProductos() != 0) {
                    const formData = new FormData()
                    formData.append('fecha', $("#fecha").val())
                    formData.append('tipoegreso', $("#tipoegreso").val())
                    formData.append('bodega', $("#bodega").val())
                    formData.append('observacion', $("#observacion").val())

                    $("#tbodyIngresos tr").each(function () {
                        let row = $(this)
                        row.find('td:eq(5)').text()

                        let funs = row.find('td').eq(0).find('input[type="hidden"]').val()
                        let tipounidads = row.find('td').eq(2).find('input[type="hidden"]').val()
                        let costoUnd = row.find('td').eq(4).find('input[type="hidden"]').val()
                        let totalCosto = row.find('td').eq(5).find('input[type="hidden"]').val()

                        formData.append("itcodigo[]", row.find("td").eq(0).text())
                        formData.append("producto[]", row.find("td").eq(1).text())
                        formData.append("costo[]", row.find("td").eq(4).text())
                        formData.append("total[]", row.find("td").eq(5).text())
                        formData.append("unidad[]", row.find("td").eq(2).text())
                        formData.append("tipoIdUni[]", tipounidads)
                        formData.append("cantidad[]", formatNumber(row.find("td").eq(3).text()))
                        formData.append("fun[]", funs)
                        formData.append("costoUnid[]", costoUnd) // costo unidad producto
                        formData.append("costoTotal[]", totalCosto) // total de costo por producto
                    })
                    $.ajax({
                        url: './?action=in_processEgreso',
                        type: 'POST',
                        data: formData,
                        processData: false,  // tell jQuery not to process the data
                        contentType: false,   // tell jQuery not to set contentType
                        success: function (respond) {
                            let res = JSON.parse(respond)
                            if (res.documento != '') {
                                $("#documento").val(res.doc)
                                $("#btn-graba-ingreso").prop('disabled', true);
                                Swal.fire({
                                    icon: 'success',
                                    title: res.comentario,
                                })

                                let link = "./reportes/inventario/reporteEgreso.php?id=" + res.documento
                                $("#printer").attr('href', link)
                                $("#printer").attr('target', "_blank")
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: res.comentario,
                                    // text: 'Something went wrong!',
                                    // footer: '<a href="">Why do I have this issue?</a>'
                                })
                            }
                        }
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'No tiene productos que registrar',
                        // text: 'Something went wrong!',
                        // footer: '<a href="">Why do I have this issue?</a>'
                    })
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Debe registrar comentario',
                    // text: 'Something went wrong!',
                    // footer: '<a href="">Why do I have this issue?</a>'
                })
            }
        })

        function loadSaldoDiarioProduct(itcodigo) {
            let numeroDecimales = $("#numeroDecimalesConfig").val()
            let fecha = $("#fecha").val()
            $.ajax({
                type: "POST",
                url: "./?action=productLoad",
                data: {"option": 2, "codigo": itcodigo, fecha: fecha},
                success: function (e) {
                    let s = JSON.parse(e)
                    if (s.saldo != '') {
                        $("#costo").val(parseFloat(s.saldo).toFixed(calDecimales()))
                        $("#costoHidden").val(Number(s.saldo))
                    }
                }
            })
        }

        $(document).on("blur", "#cantidad", function (r) {
            r.preventDefault()
            let ttcosto = valTtcosto($(this).val(), $("#costo").val())
            let totalCosto = calTotalCosto($(this).val(), $("#costoHidden").val())
            $("#ttcosto").val(ttcosto)
            $("#totalCosto").val(totalCosto)
        })

        function calTotalCosto(cantidad, costo) {
            return cantidad * costo
        }

        $(document).on('click', '.btn-remove-row-eg', function (e) {
            e.preventDefault()
            let totalRow = $(this).parents("tr").find("td").eq(5).text()
            let totalTot = $("#totalIngresosCosto").val()
            let newTotal = totalTot - totalRow
            $("#totalIngresosCosto").val(parseFloat(newTotal))
            $(this).parents('tr').remove();

        })

        function formatNumber(num) {
            if (!num || num == 'NaN') return '0,00';
            if (num == 'Infinity') return '&#x221e;';
            num = num.toString().replace(/\$|\,/g, '');
            if (isNaN(num))
                num = "0";
            sign = (num == (num = Math.abs(num)));
            num = Math.floor(num * 100 + 0.50000000001);
            cents = num % 100;
            num = Math.floor(num / 100).toString();
            if (cents < 10)
                cents = "0" + cents;
            for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
                num = num.substring(0, num.length - (4 * i + 3)) + '.' + num.substring(num.length - (4 * i + 3));
            return (((sign) ? '' : '-') + num + ',' + cents);
        }

        function convertirFormato(numero) {
            let numero1 = Number(numero).replace(".", "")
            let numero2 = numero1.replace(",", ".")
            return parseFloat(numero2)
        }

        $(document).on('click', '#viewModalPedidos', function () {
            $("#modalPedidos").modal("show")
            $("#table-pedidos").DataTable().clear().destroy()
            $("#table-pedidos").DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                },
            })
        })

        $(document).on('click', "#mostrarPedidosTabla", function () {
            let desde = $("#desde").val()
            let hasta = $("#hasta").val()
            $("#table-pedidos").DataTable({
                "destroy": true,
                "keys": true,
                "ajax": {
                    "method": "POST",
                    "url": "./?action=loadPedidos",
                    "data": {"desde": desde, "hasta": hasta, "option": 11}
                },
                "columns": [
                    {"data": "pedido", "width": "10%"},
                    {"data": "cliente", "width": "50%"},
                    {"data": "total", "width": "10%"},
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                },
            })
        })

        $(document).on('click', '#table-pedidos tbody tr', function () {
            // TOMO EL ID DEL PEDIDO PARA CARGAR SU CABECERA Y DETALLE
            let pedido = $(this).closest('tr').find('td:eq(0)').text()
            let fecha = $("#fecha").val()
            let bodega = $("#bodega").val()
            $("#numeropedido").val(pedido)
            $("#tipoegreso").val(8).trigger('change')
            $.ajax({
                type: "POST",
                url: "./?action=loadPedidos",
                data: {"option": 12, "fecha": fecha, "pedido": pedido, "bodega": bodega},
                success: function (e) {
                    let s = JSON.parse(e)
                    console.log(s)

                    $("#cliente-cod").val(s.cabecera.clientecod)
                    $("#cliente-text").val(s.cabecera.cliente)
                    $("#tbodyIngresos").html("")
                    $.each(s.detalle,function (i,item) {
                        /*$.each(s.detalle,function (i,item) {
                        })*/
                        let f = false
                        let htmlt = ''
                        if(f == false){
                            if (Number(item.saldo) <= 0){
                                confirm('Producto :' + item.producto + " , no unidades tiene disponibles ")
                                f = true
                            }
                        }
                        if(f == false){
                            if (parseFloat(item.saldo) <= parseFloat(item.cantidad)){
                                let o = confirm("Producto :" + item.producto + " , tiene disponibles " + item.saldo + "Y se esta solicitando la cantidad de " + item.cantidad + ", desea agregar cantidad disponible. ?")
                                if(o){
                                    f = true
                                    htmlt += "<tr>"
                                    htmlt += "<td><input type='hidden' value='0,0,0'>" + item.itcodigo + "</td><td><input type='hidden' value='" + item.idDet + "'>" + item.producto + "</td><td><input type='hidden' value='" + item.unidadid + "'>" + item.unidadname + "<input type='hidden' value='" + item.unidadid + "'></td><td>" + Number(item.saldo).toFixed(calDecimales()) + "</td><td><input type='hidden' value='" + Number(item.costou).toFixed(calDecimales()) + "'>" + Number(item.costou).toFixed(calDecimales()) + "</td><td class='totalesCosto'><input type='hidden' value='" + Number(item.costot).toFixed(calDecimales()) + "'>" + Number(item.costot).toFixed(calDecimales()) + "</td><td><button class=\"btn-remove-row-eg btn btn-xs btn-danger\"><i class=\"fa fa-remove\" aria-hidden=\"true\"></i></button></td>"
                                    htmlt += "</tr>"
                                }
                            }
                        }
                        if(f == false) {
                            htmlt += "<tr>"
                            htmlt += "<td><input type='hidden' value='0,0,0'>" + item.itcodigo + "</td><td><input type='hidden' value='" + item.idDet + "'>" + item.producto + "</td><td><input type='hidden' value='" + item.unidadid + "'>" + item.unidadname + "</td><td> <input class='cantidadEgreso' type='hidden' value='" + item.cantidad + "'><input class='nCantidadEgreso' type='text' value='" + Number(item.cantidad).toFixed(calDecimales()) + "'></td><td><input type='hidden' value='" + Number(item.costou).toFixed(calDecimales()) + "'>" + Number(item.costou).toFixed(calDecimales()) + "</td><td class='totalesCosto'><input type='hidden' value='" + Number(item.costot).toFixed(calDecimales()) + "'>" + Number(item.costot).toFixed(calDecimales()) + "</td><td><button class=\"btn-remove-row-eg btn btn-xs btn-danger\"><i class=\"fa fa-remove\" aria-hidden=\"true\"></i></button></td>"
                            htmlt += "</tr>"
                        }
                        $("#tbodyIngresos").append(htmlt)
                        calTotalProductos(Number(item.costot).toFixed(calDecimales()))
                        f = false
                        //} // fin for
                    })
                }
            })
            $("#modalPedidos").modal('hide')
        })

        $(document).on('blur', '.nCantidadEgreso', function () {
            // let cantidadEgreso = $(this).parents("tr").find('input[type="checkbox"]').attr('checked', true)
            let cantidadEgreso = $(this).parents("tr").find('input[type="hidden"]').eq(3).val() // valor origirnal del egreso
            let costo = $(this).closest('tr').find('td:eq(4)').text()// valor origirnal del egreso
            if (parseFloat(cantidadEgreso) < parseFloat($(this).val())) {
                Swal.fire({
                    icon: 'error',
                    title: 'Valor ingresado es incorrecto',
                    // text: 'Something went wrong!',
                    // footer: '<a href="">Why do I have this issue?</a>'
                }).then(() => {
                    $(this).val(cantidadEgreso)
                })
            } else {
                let costot = $(this).val() * costo
                let costoru = $(this).parents("tr").find('input[type="hidden"]').eq(4).val()

                let costor = costoru * $(this).val() // valor origirnal del costo

                $(this).closest('tr').find('td:eq(5)').html(costot.toFixed(calDecimales()) + '<input type="hidden" value="' + costor.toFixed(calDecimales()) + '">')
                calTotalProductos()
            }
        })

        $(document).on('click', '#btn-graba-ingreso-venta', function () {
            if (valRowsProductos() != 0) {
                const formData = new FormData()
                formData.append('fecha', $("#fecha").val())
                formData.append('tipoegreso', $("#tipoegreso").val())
                formData.append('bodega', $("#bodega").val())
                formData.append('observacion', $("#observacion").val())
                formData.append('numeropedido', $("#numeropedido").val())
                formData.append('cliente', $("#cliente-cod").val())

                $("#tbodyIngresos tr").each(function () {
                    let row = $(this)
                    row.find('td:eq(5)').text()

                    let funs = row.find('td').eq(0).find('input[type="hidden"]').val()
                    let iddet = row.find('td').eq(1).find('input[type="hidden"]').val()
                    let tipounidads = row.find('td').eq(2).find('input[type="hidden"]').val()
                    let costoUnd = row.find('td').eq(4).find('input[type="hidden"]').val()
                    let totalCosto = row.find('td').eq(5).find('input[type="hidden"]').val()

                    formData.append("itcodigo[]", row.find("td").eq(0).text())
                    formData.append("producto[]", row.find("td").eq(1).text())
                    formData.append("costo[]", row.find("td").eq(4).text())
                    formData.append("total[]", row.find("td").eq(5).text())
                    formData.append("unidad[]", row.find("td").eq(2).text())
                    formData.append("tipoIdUni[]", tipounidads)
                    if(row.find("td").eq(3).find('input[type="text"]').val()){
                        formData.append("cantidad[]", row.find("td").eq(3).find('input[type="text"]').val())
                    }else{
                        formData.append("cantidad[]", row.find("td").eq(3).text())
                    }
                    formData.append("fun[]", funs)
                    formData.append("costoUnid[]", costoUnd) // costo unidad producto
                    formData.append("costoTotal[]", totalCosto) // total de costo por producto
                    formData.append("idDet[]", iddet) // total de costo por producto

                })
                // console.log(JSON.stringify(formData))
                $.ajax({
                    url: './?action=in_processEgresoVenta',
                    type: 'POST',
                    data: formData,
                    processData: false,  // tell jQuery not to process the data
                    contentType: false,   // tell jQuery not to set contentType
                    success: function (respond) {
                        console.log(respond)
                        let res = JSON.parse(respond)
                        if (res.documento != '') {
                            $("#documento").val(res.documento)
                            $("#btn-graba-ingreso-venta").prop('disabled', true);
                            $("#btn-graba-ingreso-venta").attr('disabled', true);

                            Swal.fire({
                                icon: 'success',
                                title: res.comentario,
                                // text: 'Something went wrong!',
                                // footer: '<a href="">Why do I have this issue?</a>'
                            })

                            let link = "./reportes/inventario/reporteEgreso.php?id=" + res.doc
                            $("#printer").attr('href', link)
                            $("#printer").attr('target', "_blank")
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: res.comentario,
                                // text: 'Something went wrong!',
                                // footer: '<a href="">Why do I have this issue?</a>'
                            })
                        }
                    }
                })
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'No tiene productos que registrar',
                    // text: 'Something went wrong!',
                    // footer: '<a href="">Why do I have this issue?</a>'
                })
            }

        })

        $(document).on('click', "#mostrarEgresos", function () {
            let desde = $("#modalEgresos #desde").val()
            let hasta = $("#modalEgresos #hasta").val()
            $("#modalEgresos #table-egresos").DataTable({
                "destroy": true,
                "keys": true,
                "ajax": {
                    "method": "POST",
                    "url": "./?action=loadPedidos",
                    "data": {"desde": desde, "hasta": hasta, "option": 14}
                },
                "columns": [
                    {"data": "egreso", "width": "10%"},
                    {"data": "fecha", "width": "50%"},
                    {"data": "total", "width": "10%"},
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                },
            })
        })

        $(document).on('click', '#viewModalEgresos', function () {
            $("#modalEgresos").modal("show")
            $("#table-egresos").DataTable().clear().destroy()
            $("#table-egresos").DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                },
            })
        })

    }) // document.ready
} // dacument.getElementById
