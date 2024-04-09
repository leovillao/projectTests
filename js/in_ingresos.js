if (document.getElementById('ingresosInventario')) {
    $(document).ready(function () {

        $(document).on("keydown", ".checkDecimales", function (r) {
            calDecimales()
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

        function validaCostos(tipo) {
            $.ajax({
                // async: false,
                url: './?action=configuraciones',
                type: 'POST',
                data: {"option": 3},
                success: function (res) {
                    console.log(res)
                    if (res == "1"){
                        $("#costo").prop('readonly',false)
                        $("#ttcosto").prop('readonly',true)
                    }else{
                        $("#costo").prop('readonly',true)
                        $("#ttcosto").prop('readonly',false)
                    }
                }
            })
        }
        $(document).on('blur','#ttcosto',function () {
            costoPorTotal()
        })

        function costoPorTotal() {
            console.log("total")
            let total = $("#ttcosto").val()
            let cantidad = $("#cantidad").val()
            let costou = total / cantidad
            $("#costo").val(costou.toFixed(calDecimales()))
        }

        $('[data-toggle="tooltip"]').tooltip()

        function loadDataPersonCodigo() {
            let option = 4
            let viewHtml = ''
            $.ajax({
                url: "./?action=productLoad",
                type: "POST",
                data: {option: option},
                success: function (data) {
                    console.log(data)
                }
            })
        } // funcion para pruebas


        $(document).on("keyup", "#productocod", function () {
            this.value = this.value.toUpperCase();
        })

        $(document).keydown(function (e) {
            if (e.which === 45) {
                insertLineProduct()
                //$.when().then(loadTotalTd());
            }
        })

        $(document).on('change', '#tipoingreso', function (e) {
            let tipo = $(this)
            if (tipo.val() == 1) {
                validaCostos($(this).val())
                $("#proveedor-cod").prop('disabled', false)
                $("#proveedor-cod").attr('disabled', false)
                $("#btnProveedor").prop('disabled', false)
                $("#btnProveedor").attr('disabled', false)
            } else {
                $("#ttcosto").prop('readonly', true)
                $("#proveedor-cod").prop('disabled', true)
                $("#proveedor-cod").attr('disabled', true)
                $("#btnProveedor").prop('disabled', true)
                $("#btnProveedor").attr('disabled', true)
            }
        })

        $(document).on('click', "#btnProveedor", function () {
            $("#modalProveedores").modal('show')
            loadProveedores()
        })

        $(document).on('click', '#table-proveedores tbody tr td', function () {
            let name = $(this).parents("tr").find("td").eq(1).text()
            let ruc = $(this).parents("tr").find("td").eq(0).text()
            $("#proveedor-cod").val(ruc)
            $("#proveedortext").val(name)
            $("#modalProveedores").modal('hide')
        }) /* ======= SELECCIONA EL CLIENTE Y LO CARGA EN LA SECCION DE CLIENTES DE LA VENTANA DE FACTURACION  ======= */

        function loadProveedores() {
            $("#table-proveedores").DataTable().clear().destroy()
            $("#table-proveedores").DataTable({
                "destroy": true,
                "keys": true,
                "ajax": {
                    "method": "POST",
                    "url": "./?action=getProveedores",
                    // "data": {"option": opcion}
                },

                "columns": [
                    {"data": "codigo"},
                    {"data": "name"},
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                }
            })
        }

        $(document).on("blur", "#productocod", function () {
            let producto = $(this).val()
            if ($(this).val().length != 0) {
                $.post("./?action=productLoad", {option: 5, codigo: producto})
                    .done(function (data) {
                        const res = JSON.parse(data)
                        if (res != null) {
                            loadUnit(producto)
                            loadSaldoDiarioProduct(producto)
                            $("#productText").val(res.name)
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Codigo Incorrecto',
                                text: 'Producto no Existe',
                                // footer: '<a href="">Why do I have this issue?</a>'
                            })
                            $("#productocod").val(' ')
                        }
                    });
            }
        })

        $(document).on('click', '#btnProduct', function (e) {
            e.preventDefault()
            // viewTableLoad()
            $("#modalProducto").modal('show')
            // viewTableLoad()
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
                    {mData: 'botones'}
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
            /*focusInput(()=>{
                $("#modalProducto :input[type='search']").focus()
            });*/
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

        $(document).on('click', '#table-products-ingresos tbody tr td:not(:last-child)', function (e) {
            e.preventDefault();
            let itcodigo = $(this).parents("tr").find("td").eq(0).text()
            let name = $(this).parents("tr").find("td").eq(1).text()
            $("#productocod").val(itcodigo.trim())
            $("#productText").val(name.trim())
            $("#modalProducto").modal('hide')
            loadUnit(itcodigo)
            // validaFucProducts(itcodigo)
            const un = $("#tipoingreso") // Se selecciona el atributo que identifica el tipo de unidad
            const costopro = $('option:selected', un).attr('costopro');
            // if (costopro == "S"){
            //     $("#costo").prop('readOnly',false)
            // }else{
            loadSaldoDiarioProduct(itcodigo.trim())
            // }

        })

        function loadSaldoDiarioProduct(itcodigo) {
            let fecha = $("#fecha").val()
            let numeroDecimales = $("#numeroDecimalesConfig").val()
            $.ajax({
                type: "POST",
                url: "./?action=productLoad",
                data: {"option": 2, "codigo": itcodigo, fecha: fecha},
                success: function (e) {
                    // console.log(e)
                    let s = JSON.parse(e)
                    if (s.saldo != '') {
                        $("#costoHidden").val(s.saldo)
                        $("#costo").val(Number(s.saldo).toFixed(calDecimales()))
                    }
                }
            })
        }

        function loadUnit(itcodigo) {
            // let codPro = $("#productocod").val()
            $.ajax({
                type: "POST",
                url: "./?action=processFacturacion",
                data: {"option": 3, "codigo": itcodigo},
                success: function (e) {
                    let data = JSON.parse(e)
                    let option = ''
                    $.each(data, function (i, item) {
                        option += '<option tipoUnidad="' + item.tipo + '" value="' + item.unidid + '" factor="' + item.factor + '"' +
                            ' idunidad="' + item.unidid + '" ' + item.selected + '>' + item.unidtext + '</option>'
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
            let resultValida = validaCamposVaciosInsert()
            if (resultValida != "") {
                Swal.fire({
                    icon: 'error',
                    title: resultValida,
                })
            } else {
                insertLineProduct()
            }
        })

        $(document).on("blur", "#costo", function (r) {
            r.preventDefault()
            /*numero decimales de acuerdo a configruaciones*/
            let ttcosto = valTtcosto($("#cantidad").val(), parseFloat($(this).val()))
            $("#ttcosto").val(Number(ttcosto).toFixed(calDecimales()))

            /* numeros decimales completos en input ocultos */
            let costoUnitario = ($("#costoHidden").val() == '') ? $(this).val() : $("#costoHidden").val()
            let ttc = calTotalCosto(parseFloat(parseFloat(costoUnitario)), $("#cantidad").val())
            $("#totalCosto").val(ttc)
        })

        $(document).on("blur", "#cantidad", function (r) {
            r.preventDefault()
            /*numero decimales de acuerdo a configruaciones*/
            let ttcosto = valTtcosto($(this).val(), parseFloat($("#costo").val()))
            $("#ttcosto").val(Number(ttcosto).toFixed(calDecimales()))

            /* numeros decimales completos en input ocultos */
            let ttc = calTotalCosto($(this).val(), $("#costoHidden").val())
            $("#totalCosto").val(ttc)
        })

        function calTotalCosto(cantidad, costo) {
            // let numerosDecimales = $("#numeroDecimalesConfig").val()
            return parseFloat(cantidad) * parseFloat(costo)
        }

        function valTtcosto(cantidad, costo) {
            let numerosDecimales = $("#numeroDecimalesConfig").val()
            return parseFloat(cantidad * costo).toFixed(calDecimales())
        }

        function calTotalProductos(totalcosto) { // funcion q calcula el total del la tabla de los productos en la tabla de ingresos , recibe el total del costo q se calcula del producto q se va a ingresar
            let totCosto = 0
            let numerosDecimales = $("#numeroDecimalesConfig").val()
            $("#tableIngresos tbody tr").each(function (item) {
                totCosto += parseFloat($(this).closest('tr').find('td').eq(5).text())
            })

            $("#totalIngresosCosto").val(totCosto.toFixed(calDecimales()))
        }

        function insertLineProduct() {
            // $("#fecha").prop('readonly', true)
            let itcodigo = $("#productocod").val()
            let name = $("#productText").val()

            let funcion = ($("#funcionesPro").val() == null) ? 0 : $("#funcionesPro").val(); // funcion
            let negocio = ($("#unidadNegocioPro").val() == null) ? 0 : $("#unidadNegocioPro").val(); // unidad de negocio
            let ccosto = ($("#ccostoPro").val() == null) ? 0 : $("#ccostoPro").val(); // centro de costo

            let costounid = ($("#costoHidden").val() == '') ? $("#costo").val() : $("#costoHidden").val(); // costo unitario valida si al cargar el producto cargar el costo caso contrario toma el valor del input visible , con todos los decimales

            let uni = document.getElementById("unidad")
            let unidtipo = $('option:selected', uni).attr('tipoUnidad')
            let unidid = $('option:selected', uni).attr('idunidad')
            let unidtext = $('option:selected', uni).text()

            let cantidad = $("#cantidad").val() // cantidad de ingresa del producto


            let totalcosto = ($("#totalCosto").val() == '') ? $("#ttcosto").val() : $("#totalCosto").val(); // valida si el campo del costo total esta vacio caso contrario toma el valor del campo visible y lo envia al campo oculto de la tabla

            let ttcosto = $("#ttcosto").val() // total del costo campo con cantidad de decimales
            let costo = ($("#costo").val() == '') ? "0.00" : $("#costo").val();


            const fu = funcion + ',' + negocio + ',' + ccosto
            const unidades = unidtipo + ',' + unidid // tipo de la unidad / id de la unidad
            /* == funcion,costo,negocio ==*/
            let row = '<tr><td><input type="hidden" class="fun" value="' + fu + '" >' + itcodigo + '</td><td>' + name + '</td><td class="text-right">' + cantidad + '</td><td><input type="hidden" class="tipounidad" value="' + unidades + '" >' + unidtext + '</td><td class="text-right"><input type="hidden" value="' + costounid + '">' + Number(costo).toFixed(calDecimales()) + '</td><td class="text-right">' + ttcosto + '<input type="hidden" value="' + totalcosto + '"></td><td><button class="btn-remove-row-ing btn btn-xs btn-danger"><i class="fa fa-remove" aria-hidden="true"></i></button></td></tr>'

            $("#tbodyIngresos").append(row)

            calTotalProductos(ttcosto)

            $("#costo").val('')
            $("#ttcosto").val('')
            $("#unidad").val('')
            $("#cantidad").val('')
            $("#productText").val('')
            $("#costoHidden").val('')
            $("#productocod").val('').focus()
            if (valida() >= 1) {
                $("#fecha").prop('readonly', true)
            }
        }

        function validaCamposVaciosInsert() {
            let productText = $("#productText").val()
            let productCod = $("#productocod").val()
            let cantidad = $("#cantidad").val()
            let r = ""
            if (productText.length == 0 || productText == '') {
                r = "Debe ingresar producto"
            }
            if (productCod.length == 0 || productCod == '') {
                r = "Debe ingresar producto"
            }
            if (cantidad.length == 0 || cantidad == '') {
                r = "Debe registrar cantidad"
            }
            return r
        }

        $(document).on('click', '.btn-remove-row-ing', function (e) {
            e.preventDefault()
            let totalRow = $(this).parents("tr").find("td").eq(5).text()
            let totalTot = $("#totalIngresosCosto").val()
            let newTotal = totalTot - totalRow
            $("#totalIngresosCosto").val(newTotal)
            $(this).parents('tr').remove();
            if (valida() >= 1) {
                $("#fecha").prop('readonly', true)
            } else {
                $("#fecha").prop('readonly', false)
            }
        })

        function valida() {
            let i = 0
            $("#tableIngresos tbody tr").each(function (item) {
                i++
            })
            return i
        }

        function valRowsProductos() {
            let t = 0
            $("#tbodyIngresos tr").each(function () {
                t++
            })
            return t
        }

        $(document).on('click', '#btn-graba-ingreso', function () {
            let numeroDecimales = $("#numeroDecimalesConfig").val()

            if ($("#observacion").val() != '') {
                if (valRowsProductos() != 0) {
                    const formData = new FormData()
                    formData.append('fecha', $("#fecha").val())
                    formData.append('tipoingreso', $("#tipoingreso").val())
                    formData.append('bodega', $("#bodega").val())
                    formData.append('observacion', $("#observacion").val())
                    formData.append('referencia', $("#referencia").val())
                    formData.append('proveedor', $("#proveedor-cod").val())

                    $("#tbodyIngresos tr").each(function () {
                        let row = $(this)
                        row.find('td:eq(5)').text()

                        let funs = row.find('td').eq(0).find('input[type="hidden"]').val()
                        let tipounidads = row.find('td').eq(3).find('input[type="hidden"]').val()
                        let costoUnd = row.find('td').eq(4).find('input[type="hidden"]').val()
                        let totalCosto = row.find('td').eq(5).find('input[type="hidden"]').val()

                        formData.append("itcodigo[]", row.find("td").eq(0).text())
                        formData.append("producto[]", row.find("td").eq(1).text())
                        formData.append("costo[]", row.find("td").eq(4).text())
                        formData.append("total[]", row.find("td").eq(5).text())
                        formData.append("unidad[]", row.find("td").eq(3).text())
                        formData.append("tipoIdUni[]", tipounidads) // tipo de unidad / Id de la unidad
                        formData.append("cantidad[]", row.find("td").eq(2).text())
                        formData.append("fun[]", funs) // funcion/unidadNegocio/ccosto
                        // formData.append("costoUnid[]", costoUnd) // costo unidad producto
                        formData.append("costoUnid[]", costoUnd) // costo unidad producto
                        formData.append("costoTotal[]", totalCosto) // total de costo por producto
                    })

                    if (!$("#s").is(":disabled")) {
                        formData.append("docrel", $("#tipodocrel").val()) // funcion/unidadNegocio/ccosto
                    }

                    $.ajax({
                        url: './?action=in_processIngreso',
                        type: 'POST',
                        data: formData,
                        processData: false,  // tell jQuery not to process the data
                        contentType: false,   // tell jQuery not to set contentType
                        success: function (respond) {
                            console.log(respond)
                            let res = JSON.parse(respond)
                            if (res.documento != '') {
                                $("#documento").val(res.documento)
                                $("#btn-graba-ingreso").prop('disabled', true);

                                Swal.fire({
                                    icon: 'success',
                                    title: res.comentario.substr(2),
                                })
                                let link = "./reportes/inventario/reporteIngreso.php?id=" + res.id
                                $("#printer").attr('href', link)
                                $("#printer").attr('target', "_blank")
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: res.comentario.substr(2),
                                })
                            }
                        }
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'No tiene produtos q registrar',
                    })
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Debe ingresar comentario',
                })
            }

        })

        $(document).on('change', '#tipoingreso', function (e) {
            e.preventDefault()
            const un = $(this) // Se selecciona el atributo que identifica el tipo de unidad
            const costopro = $('option:selected', un).attr('costopro');
            const docrel = $('option:selected', un).attr('docrel');
            if (costopro == "S") {
                $("#costo").prop('readOnly', false)
                $("#costo").val('')
                $("#ttcosto").val('')
                $("#unidad").val('')
                $("#cantidad").val('')
                $("#productText").val('')
                $("#productocod").val('')
            } else {
                $("#costo").prop('readOnly', true)
                $("#costo").val('')
                $("#ttcosto").val('')
                $("#unidad").val('')
                $("#cantidad").val('')
                $("#productText").val('')
                $("#productocod").val('')
            }

            if (docrel == "S") {
                $("#tipodocrel").prop("disabled", false)
            } else {
                $("#tipodocrel").prop("disabled", true)
            }
        })

        /* Convierte el formato para visualizarlo con formato miles */
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

        /* Convierte el formato para guardar a la base de datos*/
        function convertirFormato(numero) {
            let numero1 = numero.replace(".", "")
            let numero2 = numero1.replace(",", ".")
            return parseFloat(numero2)
        }

        function convertirFormato1(numero) {
            // let numero1 = numero.replace(".", "")
            let numero2 = numero.replace(".", ",")
            return parseFloat(numero2)
        }

    }) // document.ready
} // dacument.getElementById