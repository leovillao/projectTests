if (document.getElementById('inTransferencia')) {
    $(document).ready(function () {

        // loadDataPersonCodigo()

        function loadDataPersonCodigo() {
            let option = 4
            let viewHtml = ''
            $.ajax({
                url: "./?action=productLoad",
                type: "POST",
                data: {option: option},
                success: function (data) {
                    console.log(data)
                    /*let res = JSON.parse(data)
                    let viewHtml = ''
                    let select = ''
                    viewHtml += '<option value="">Seleccion cliente...</option>'
                    $.each(res, function (i, item) {
                        viewHtml += '<option >' + item.text + '</option>'
                    });
                    $("#list-producto").html(viewHtml)*/
                }
            })
        }

        $(document).on('click', '#btnProduct', function (e) {
            e.preventDefault()
            $("#modalProducto").modal('show')
            // viewTableLoad()
        })

        /*$(document).on('keyup','#productocod',function () {
            let texto = $(this).val()
            let validar = new RegExp("[^A-Z0-9\#\&]+")
            if(validar.test(texto)){
                texto = texto.substr(0,texto.length-1)
            } else{
                // accion cuando no coincide
            }
            $(this).val(texto)
        })*/

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
                    console.log(datos)
                    console.log("productoTrans")
                    let tableBodyProduct = ''
                    let t = 1
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
            let option = 1;
            $("#table-products-ingresos").DataTable().clear().destroy()
            $("#table-products-ingresos").DataTable({
                "destroy": true,
                "keys": true,
                "ajax": {
                    "method": "POST",
                    "url": "./?action=getProductsTable",
                    "data": {"option": option}
                },
                "columns": [
                    {mData: 'itcodigo'},
                    {mData: 'name'},
                    {mData: 'botones'},
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                },
            })

        }

        $(document).on('click', '#table-products-ingresos tbody tr td:not(:last-child)', function () {
            let itcodigo = $(this).parents("tr").find("td").eq(0).text()
            let name = $(this).parents("tr").find("td").eq(1).text()
            $("#productocod").val(itcodigo)
            $("#productText").val(name)

            loadUnit()
            validaFucProducts(itcodigo)
            loadSaldoDiarioProduct(itcodigo)
            loadStockProduct(itcodigo)
            $("#modalProducto").modal('hide')
        })

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
                    let sp = JSON.parse(e)
                    $("#stock").val(sp.saldo)
                }
            })
        }

        function loadUnit() {
            let codPro = $("#productocod").val()
            $.ajax({
                type: "POST",
                url: "./?action=processFacturacion",
                data: {"option": 3, "codigo": codPro},
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
                    console.log(result)
                    let r = JSON.parse(result)
                    console.log(r)
                    if (r[0].stock != 0) {
                        Swal.fire({
                            title: '<strong>Bodega : ' + r[0].bodega + '</strong>',
                            icon: 'info',
                            html:
                                'Stock : <b>'+r[0].stock+'</b>, ',
                            showCloseButton: true,

                        })
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Bodega :'+r[0].bodega,
                            text: 'No tiene stock disponible',
                            // footer: '<a href="">Why do I have this issue?</a>'
                        })
                    }
                }
            })
        })

        function valTtcosto(cantidad, costo) {
            return parseFloat(cantidad * costo).toFixed(2)

        }


        function calTotalProductos(totalcosto) {
            const valt = $("#totalIngresosCosto").val()
            if (valt.length == 0) {
                $("#totalIngresosCosto").val(totalcosto)
            } else {
                let t = parseFloat(totalcosto) + parseFloat(valt)
                $("#totalIngresosCosto").val(t)
            }
        }

        $(document).on("blur", "#productocod", function () {
            let producto = $(this).val()
            $.post("./?action=productLoad", {option: 5, codigo: producto})
                .done(function (data) {
                    const res = JSON.parse(data)
                    if (res != null) {
                        loadUnit()
                        validaFucProducts(producto)
                        loadSaldoDiarioProduct(producto)
                        loadStockProduct(producto)
                        $("#productText").val(res.name)
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Codigo Incorrecto',
                            text: 'Producto no Existe',
                            // footer: '<a href="">Why do I have this issue?</a>'
                        })
                        $("#productocod").val(' ')
                    }
                });
        })


        function insertLineProduct() {
            let itcodigo = $("#productocod").val()
            let cantidad = $("#cantidad").val()
            let stock = $("#stock").val()

            if (parseFloat(cantidad) <= parseFloat(stock)) {
                if (itcodigo.length != 0) {
                    if (cantidad.length != 0) {

                        let costo = $("#costo").val()
                        let nttcosto = valTtcosto(cantidad, costo)
                        $("#ttcosto").val(nttcosto)

                        let name = $("#productText").val()

                        let funcion = ($("#funcionesPro").val() == null) ? 0 : $("#funcionesPro").val();
                        let negocio = ($("#unidadNegocioPro").val() == null) ? 0 : $("#unidadNegocioPro").val();
                        let ccosto = ($("#ccostoPro").val() == null) ? 0 : $("#ccostoPro").val();

                        let uni = document.getElementById("unidad")
                        let unidtipo = $('option:selected', uni).attr('tipoUnidad')
                        let unidid = $('option:selected', uni).attr('idunidad')
                        let unidtext = $('option:selected', uni).text()

                        let ttcosto = $("#ttcosto").val()

                        const fu = funcion + ',' + negocio + ',' + ccosto
                        const unidades = unidtipo + ',' + unidid
                        /* == funcion,costo,negocio ==*/
                        let row = '<tr><td><input type="hidden" class="fun" value="' + fu + '" >' + itcodigo + '</td><td>' + name + '</td><td><input type="hidden" class="tipounidad" value="' + unidades + '" >' + unidtext + '</td><td>' + cantidad + '</td><td>' + costo + '</td><td>' + ttcosto + '</td><td><button class="btn-remove-row-eg btn btn-xs btn-danger"><i class="fa fa-remove" aria-hidden="true"></i></button></td></tr>'

                        $("#tbodyIngresos").append(row)
                        $(".reset-valor").val('')

                        calTotalProductos(ttcosto)


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
                Swal.fire({
                    icon: 'error',
                    title: 'Cantidad ingresada no valida',
                })
                $("#cantidad").focus()
                $("#cantidad").val('')
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
            let bodegaEg = $("#bodega").val()
            let bodegaIn = $("#bodega-rel").val()
            if (bodegaEg != bodegaIn) {
                if ($("#observacion").val() != '') {
                    if (valRowsProductos() != 0) {
                        const formData = new FormData()
                        formData.append('fecha', $("#fecha").val())
                        formData.append('tipoegreso', $("#tipoegreso").val())
                        formData.append('bodega', $("#bodega").val())
                        formData.append('observacion', $("#observacion").val())
                        formData.append('bodegarel', $("#bodega-rel").val())

                        $("#tbodyIngresos tr").each(function () {
                            let row = $(this)
                            row.find('td:eq(5)').text()

                            let funs = row.find('td').eq(0).find('input[type="hidden"]').val()
                            let tipounidads = row.find('td').eq(2).find('input[type="hidden"]').val()

                            formData.append("itcodigo[]", row.find("td").eq(0).text())
                            formData.append("producto[]", row.find("td").eq(1).text())
                            formData.append("costo[]", row.find("td").eq(4).text())
                            formData.append("total[]", row.find("td").eq(5).text())
                            formData.append("unidad[]", row.find("td").eq(2).text())
                            formData.append("tipoIdUni[]", tipounidads)
                            formData.append("cantidad[]", row.find("td").eq(3).text())
                            formData.append("fun[]", funs)
                        })
                        $.ajax({
                            url: './?action=in_processTransferencia',
                            type: 'POST',
                            data: formData,
                            processData: false,  // tell jQuery not to process the data
                            contentType: false,   // tell jQuery not to set contentType
                            success: function (respond) {
                                console.log(respond)
                                let res = JSON.parse(respond)
                                if (res.documento != '') {
                                    $("#documento").val(res.doc)
                                    $("#btn-graba-ingreso").prop('disabled', true);

                                    Swal.fire({
                                        icon: 'success',
                                        title: res.comentario,
                                        // text: 'Something went wrong!',
                                        // footer: '<a href="">Why do I have this issue?</a>'
                                    })

                                    let link = "./reportes/inventario/reporteTransferencias.php?id=" + res.documento
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
            }
        })


        function loadSaldoDiarioProduct(itcodigo) {
            let fecha = $("#fecha").val()
            $.ajax({
                type: "POST",
                url: "./?action=productLoad",
                data: {"option": 2, "codigo": itcodigo,"fecha":fecha},
                success: function (e) {
                    let s = JSON.parse(e)
                    if (s.saldo != '') {
                        $("#costo").val(s.saldo)
                    }
                }
            })
        }

        $(document).on("blur", "#cantidad", function (r) {
            r.preventDefault()
            let ttcosto = valTtcosto($(this).val(), $("#costo").val())

            $("#ttcosto").val(ttcosto)
        })


        $(document).on('click', '.btn-remove-row-eg', function (e) {
            e.preventDefault()
            let totalRow = $(this).parents("tr").find("td").eq(5).text()
            let totalTot = $("#totalIngresosCosto").val()
            let newTotal = totalTot - parseFloat(totalRow)
            $("#totalIngresosCosto").val(newTotal.toFixed(2))
            $(this).parents('tr').remove();

        })


    }) // document.ready
} // dacument.getElementById