if (document.getElementById('paymenths')) {
    $(document).ready(function () {
        // console.log("paymenths")
        /** EJECUCION PARA VISUALIZAR EL TOOLTIP */

        function viewModalClientes() {
            $("#modalCliente").modal('toggle').on("shown.bs.modal", function () {
                $('#modalCliente .dataTables_filter input').focus();
            })
            $("#modalCliente .modal-header").css("background-color", "#84AC3B")
        }

        $(document).on('click', '#btnCliente', function (e) {
            viewModalClientes()
        })

        let opcion = 1

        $("#table-clientes").DataTable({
            "destroy": true,
            "keys": true,
            "ajax": {
                "method": "POST",
                "url": "./?action=personLoad",
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

        $(document).on('click', '#tbody-cliente tr td', function () { /* ==== Click sobre tbody CLIENTES ===== */
            // event.preventDefault()
            let name = $(this).parents("tr").find("td").eq(1).text()
            let ruc = $(this).parents("tr").find("td").eq(0).text()
            $("#clientecod").val(ruc)
            $("#clienteTxt").val(name)
            $("#modalCliente").modal('hide')
        })

        $(document).on('change', '#clientecod', function (e) {
            e.preventDefault()
            let cliente = $(this).val()
            if (cliente.length != "") {
                $.ajax({
                    url: "./?action=personLoad",
                    type: "POST",
                    data: {option: 5, cliente: cliente},
                    success: function (data) {
                        let d = JSON.parse(data)
                        $("#clienteTxt").val(d[0])
                    }
                })
            } else {
                $("#clienteTxt").val(' ')
            }
        })


        $('[data-toggle="tooltip"]').tooltip()

        $(document).on("change", "#tipoCobro", function () {
            if ($(this).val() == 4) {
                $("#formapago").val(9).trigger('change')
                $("#formapago").attr('readonly')
                $("#referencia").focus()
            } else {
                $("#formapago").val(0).trigger('change')
                $("#formapago").removeAttr('readonly')
            }
        })

        $(document).on('click', '#table-docs-asociados tbody tr td', function () { /* ==== Click sobre tbody CLIENTES ===== */
            let factura = $(this).parents("tr").find("td").eq(2).text()
            $("#num-doc-ret").val(factura.trim())
            $("#modalRetenciones").modal('hide')

        })

        $(document).on('change', '#formapago', function (e) {
            e.preventDefault()
            let option = 1
            let idfp = $(this).val()
            let ccinf = $('option:selected', $(this)).attr('cc_inf');
            /** SE OBTIENE EL ATTRIBUTO DEL SELECT */
            let cliente = $("#clientecod").val()

            let formaP = $("#tipoCobro").val()
            let tipoEntidad = event.target.options[event.target.selectedIndex].dataset.entidad
            /** se obtiene el atributo de la opcion seleccionada */
            let namefp = $("#formapago option:selected").text()
            if (idfp == 9 && formaP != 4) {
                $("#formapago").val(0).trigger('change')
                Swal.fire({
                    icon: 'error',
                    title: 'El tipo de cobro de ser CXC ',
                })
                idfp.focus()

            } else {
                if (tipoEntidad != 0 && idfp != 9) {
                    if (idfp != 0) {
                        $("#efectivo").attr('disabled', true)
                        $("#addFormaPago").attr('disabled', true)
                        $("#nameId").val(namefp).attr('hidden', true)
                        $.ajax({
                            url: '?action=loadFormas',
                            type: 'POST',
                            data: {option: option, idfp: idfp},
                            success: function (result) {
                                let dato = JSON.parse(result)
                                $("#fpID").val(dato.id)
                                if (dato.documento === "N") { /*VALIDA LA PETICION DEL INGRESO DEL NUMERO DE DOCUMENTO DE PAGO */
                                    $("#cuentaDocumento").attr("disabled", "disabled")
                                } else if (dato.documento == "S") {/*VALIDA LA PETICION DEL INGRESO DEL NUMERO DE DOCUMENTO DE PAGO */
                                    $("#cuentaDocumento").removeAttr("disabled")
                                }
                                if (dato.cuenta === "N") {
                                    $("#cuentaCliente").attr("disabled", "disabled")
                                } else if (dato.cuenta === "S") {
                                    $("#cuentaCliente").removeAttr("disabled")
                                }
                                if (dato.tipo == 1) {
                                    $("#labelDocumento").text("Numero de Deposito")
                                } else {
                                    $("#labelDocumento").text("Numero de Documento")
                                }
                                if (dato.tipo == 1) {
                                    $("#labelCuenta").text("Numero de cuenta")
                                } else {
                                    $("#labelCuenta").text("Numero de tarjeta")
                                }
                                if (dato.tipo == 1) {
                                    $("#bancoCliente").text("Banco")
                                } else {
                                    $("#bancoCliente").text("Tarjeta")
                                }
                                let tags = ''
                                /** =============================================================================
                                 Valida si la entidad trae datos con respecto a la visualizar y seleccionar la misma
                                 * ================================================================================*/
                                if ($.isEmptyObject(dato.entidades)) {
                                    $("#bancoPago").attr('disabled', 'disabled')
                                } else {
                                    tags += '<option value="0">Seleccione Entidad...</option>'
                                    $.each(dato.entidades, function (i, item) {
                                        tags += '<option value="' + item.id + '">' + item.name + '</option>'
                                    });
                                    $("#bancoPago").removeAttr('disabled')
                                    sumar()
                                }
                                $("#bancoPago").html(tags)
                                $("#modalFormaPago").modal('toggle')
                            }
                        })
                    }
                } else if (ccinf == 1) {
                    if (cliente.length != 0) {
                        loadDataModalRetenciones(cliente)
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: "Debe Seleccionar Cliente",
                        })
                        $("#formapago").val(0).trigger('change')
                    }
                } else {
                    $("#efectivo").removeAttr('disabled')
                    $("#addFormaPago").removeAttr('disabled')
                }
            }
        })

        function loadDataModalRetenciones(cliente) {
            $.ajax({
                url: './?action=cc_loadDocsRetenciones',
                type: 'POST',
                data: {"option": 1, "cliente": cliente},
                success: function (respuesta) {
                    console.log(respuesta)
                    let dato = JSON.parse(respuesta)
                    let t = 1
                    let html = ''
                    $.each(dato, function (i, item) {
                        html += '<tr><td>' + t + '</td><td> <input type="hidden" value="' + item.id + '" > ' + item.documento + '</td><td>' + item.factura + '</td></tr>'
                        t++
                    })
                    $("#table-docs-retenciones").html(html)
                    $("#modalRetenciones").modal('toggle')
                    // $("#modalDocumentosDeudas .modal-title").text(texto)
                }
            })
        }

        /** CARGAR INFORMACION A TABLA DE DETALLE DE FORMA DE PAGOS */

        $(document).on("click", "#add_val_ret", function () {
            loadDetPagos()
        })

        function loadDetPagos() {
            let valFormaPago = $("#formapago").val()
            let valor = $('#efectivo').val()
            let referencia = $('#referencia').val()

            let formapago = $('select[id="formapago"] option:selected').text()
            let fecha = $('#date').val()

            let tableHtml = '<tr><td></td><td>' + formapago + '</td><td></td><td></td><td>' + fecha + '</td><td></td><td>' + referencia + '</td><td class="valor_pago">' + valor + '</td><td><button class="btn btn-danger btn-xs remove-row"><i class="fa fa-times" aria-hidden="true"></i></button></td></tr>'

            $("#table-forma-pagos").append(tableHtml)
            $("#totalPagos").val(sumar().toFixed(2))
            // $('#efectivo').val('')
        }

        $(document).on("click", "#add_val_ret", function () {
            let valor = ''
            $("#table-docs-retenciones tr").each(function () {
                let row = $(this)
                let htmlView = ''
                let totValor = ''
                const arrayNumdoc = []
                $(this).find('input[type="checkbox"]:checked').each(function () {
                    let documento = row.find('td:eq(1)').text()
                    let numerofact = row.find('td:eq(2)').text()
                    let numDoc = numerofact.replace(/^\s*|\s*$/g, "")
                    arrayNumdoc.push(numDoc)
                    let cuota = row.find('td:eq(3)').text()
                    let id = row.find('input[type="hidden"]').val()
                    let valor = row.find('input[type="number"]').val()
                    totValor += valor
                    htmlView = '<tr>\n' +
                        '                        <td></td>\n' +
                        '                        <td> <input type="hidden" value="' + id + '">' + documento + '</td>\n' +
                        '                        <td>' + numDoc + '</td>\n' +
                        '                        <td>' + cuota + '</td>\n' +
                        '                        <td class="valor_cobro">' + valor + '</td>\n' +
                        '                        <td>\n' +
                        '                            <button class="btn btn-xs btn-danger remove-fact"><i class="fa fa-times" aria-hidden="true"></i>\n' +
                        '                            </button>\n' +
                        '                        </td>\n' +
                        '                    </tr>'
                })

                const objDeudas = loadtableDetalle();
                const objExiste = []
                const objNoExiste = []
                for (let i = 0; i < arrayNumdoc.length; i++) {
                    if (objDeudas.includes(arrayNumdoc[i])) {
                        objExiste.push(1)
                    } else {
                        objExiste.push(0)
                    }
                }
                if (objExiste.includes(0)) {
                    $("#tabla-docs-deuda tbody").append(htmlView)
                    $("#totalcobro").val(sumarDocumentos().toFixed(2))
                }
            })
        })

        $("#addFormaP").click(function (e) {
            e.preventDefault()
            let tot = sumar()
            let fp = $('#nameId').val()
            let textobanco = $('select[id="bancoPago"] option:selected').text()
            let fechapago = $('#fechadocumento').val()
            let documento = $('#cuentaDocumento').val()
            let cuenta = $('#cuentaCliente').val()
            let referencia = $('#referencia').val()
            let banco = $('#bancoPago').val()
            let valor = $('#valor').val()
            let tableHtml = '<tr><td></td><td>' + fp + '</td><td>' + textobanco + '</td><td>' + cuenta + '</td><td>' + fechapago + '</td><td>' + documento + '</td><td>' + referencia + '</td><td></td><td class="valor_pago">' + valor + '</td><td><button class="btn btn-danger btn-xs remove-row"><i class="fa fa-times" aria-hidden="true"></i></button></td></tr>'
            $("#table-forma-pagos").append(tableHtml)
            let tl = parseFloat(tot) + parseFloat(valor)
            $("#totalPagos").val(tl.toFixed(2))
            $("#modalFormaPago").modal('hide')
        })

        $(document).on('click', '#btn-buscar-documentos', function (e) {
            e.preventDefault()
            validaDocumentoAsociado()
            let client = $("#clientecod").val()
            let option = 3
            //let texto = $('select[id="cliente"] option:selected').text()
            let texto = $("#clienteTxt").val()
            // console.log(client)
            if (client == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Debe seleccionar un cliente...',
                })
            } else {
                $.ajax({
                    url: "./?action=personLoad",
                    type: 'POST',
                    data: {client: client, option: option},
                    success: function (resultado) {
                        // console.log(resultado)
                        let dato = JSON.parse(resultado)
                        let t = 1
                        let html = ''
                        $.each(dato, function (i, item) {
                            html += '<tr><td>' + t + '</td><td> <input type="hidden" value="' + item.id + '" > ' + item.documento + '</td><td>' + item.factura + '</td><td>' + item.cuota + '</td><td><label for="" class="val">' + item.saldo + '</label></td><td><input type="number" class="form-control valor-abonado"></td><td><input type="checkbox" class="addValor"></td></tr>'
                            t++
                        })
                        $("#table-deudas").html(html)
                        $("#modalDocumentosDeudas").modal('toggle')
                        $("#modalDocumentosDeudas .modal-title").text(texto)
                        // addValor()
                    }
                })
            }
        })

        $(document).on('click', '#btn-observation', function (e) {
            e.preventDefault()
            $("#modalObservation").modal('toggle')

        })

        $(document).on('blur', ".valor-abonado", function () {
            let abonado = $(this).val()
            let valor = $(this).parents("tr").find("td").eq(4).text()
            let saldo = ''
            if (Number(abonado) <= Number(valor)) {
                saldo = Number(valor) - Number(abonado)
                $(this).parents("tr").find("td").eq(4).text(saldo.toFixed(2))
                $(this).parents("tr").find('input[type="checkbox"]').attr('checked', true)
                // console.log(saldo.toFixed(2) + '/' + abonado)
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'No puede ingresar valor mayor al saldo ...',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).parents("tr").find('input[type="number"]').val('').focus()
                    }
                })
            }
        })

        $(document).on('click', '.addValor', function () { // detecto el click sobre el checkbox
            if ($(this).is(':checked')) { // valida el estado del checkbox para detectar el estado del checkbox
                let total = $(this).parents("tr").find("td").eq(4).text()
                $(this).parents("tr").find('input[type="number"]').val(total)
                $(this).parents("tr").find('input[type="number"]').attr('readOnly', true)
                $(this).parents("tr").find("td").eq(4).text("0.00")
            } else {
                let tot = $(this).parents("tr").find('input[type="number"]').val()
                let saldo = $(this).parents("tr").find("td").eq(4).text()
                let sum = 0
                if (Number(saldo) != 0) {
                    sum = Number(saldo) + Number(tot)
                    $(this).parents("tr").find("td").eq(4).text(sum)
                    $(this).parents("tr").find('input[type="number"]').val('')
                } else {
                    $(this).parents("tr").find('input[type="number"]').val('')
                    $(this).parents("tr").find('input[type="number"]').removeAttr('readOnly')
                    $(this).parents("tr").find("td").eq(4).text(tot)
                }
            }
        });


        loadDataPersonCodigo()

        function loadDataPersonCodigo() {
            let option = 4
            let viewHtml = ''
            $.ajax({
                url: "./?action=personLoad",
                type: "POST",
                data: {option: option},
                success: function (data) {
                    let res = JSON.parse(data)
                    let viewHtml = ''
                    let select = ''
                    viewHtml += '<option value="">Seleccion cliente...</option>'
                    $.each(res, function (i, item) {
                        viewHtml += '<option >' + item.text + '</option>'
                    });
                    $("#list-cliente").html(viewHtml)
                }
            })
        }

        $(document).on('change', '#clientecod', function (e) {
            e.preventDefault()
            if ($(this).val() == '') {
                $("#clienteTxt").val('')
            } else {
                let cliente = $('option:selected', this).attr('cliente');
                $("#clienteTxt").val(cliente)
                $("#clienteTxt").attr('readOnly', 'readOnly')
            }
        })

        /** SE RECORRE LA TABLA DE LOS FORMA DE PAGO PARA VALIDAR EL NUMERO DE RETENCION SE ENCUENTRE EL DOCUMENTO ASOCIADO */
        function validaDocumentoAsociado() {
            let bandResult = false
            $("#table-forma-pagos tr").each(function (row, tr) {
                let ccInf = $('td:eq(1) input', this).val()
                if (ccInf == 1) {
                    let arrayDocs = validaNumdDocAsociado()
                    if (arrayDocs.length != 0) {
                        if (bandResult == false) {
                            if (arrayDocs.includes($('td:eq(7)', this).text().trim())) {
                                bandResult = true
                            }
                        }
                    }
                }
            })
            return bandResult
        }

        function validaNumdDocAsociado() {
            let arrayDocs = []
            $("#table-docs-pagos tr").each(function (row, tr) {
                arrayDocs.push($('td:eq(2)', this).text())
            })
            return arrayDocs
        }

        function validaEstadoCcInf() {
            let arrayDocs = []
            $("#table-forma-pagos tr").each(function (row, tr) {
                arrayDocs.push($('td:eq(1) input', this).val())
            })
            return arrayDocs
        }


        $("#addFormaPago").click(function (e) {
            e.preventDefault()
            let valFormaPago = $("#formapago").val()
            let valor = $('#efectivo').val()
            let referencia = $('#referencia').val()
            let numFactRet = $("#num-doc-ret").val()
            if (valFormaPago != 0) {
                if (valor != "" || valor != 0) {
                    let formapago = $('select[id="formapago"] option:selected').text()
                    let ccinf = $('select[id="formapago"] option:selected').attr('cc_inf')
                    let fecha = $('#date').val()
                    let tableHtml = '<tr><td></td><td><input type="hidden" value="' + ccinf + '" >' + formapago + '</td><td></td><td></td><td>' + fecha + '</td><td></td><td>' + referencia + '</td><td>' + numFactRet + '</td><td class="valor_pago">' + valor + '</td><td><button class="btn btn-danger btn-xs remove-row"><i class="fa fa-times" aria-hidden="true"></i></button></td></tr>'
                    $("#table-forma-pagos").append(tableHtml)
                    $("#totalPagos").val(sumar().toFixed(2))
                    $('#efectivo').val('')
                    $("#num-doc-ret").val('')
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Debe ingresar valor...',
                    })
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Debe seleccionar forma de pago...',
                })
            }
        })

        $("#add_factura").click(function (e) {
            e.preventDefault()
            let valor = ''
            $("#table-deudas tr").each(function () {
                let row = $(this)
                let htmlView = ''
                let totValor = ''
                const arrayNumdoc = []
                $(this).find('input[type="checkbox"]:checked').each(function () {
                    let documento = row.find('td:eq(1)').text()
                    let numerofact = row.find('td:eq(2)').text()
                    let numDoc = numerofact.replace(/^\s*|\s*$/g, "")
                    arrayNumdoc.push(numDoc)
                    let cuota = row.find('td:eq(3)').text()
                    let id = row.find('input[type="hidden"]').val()
                    let valor = row.find('input[type="number"]').val()
                    totValor += valor
                    htmlView = '<tr>\n' +
                        '                        <td></td>\n' +
                        '                        <td> <input type="hidden" value="' + id + '">' + documento + '</td>\n' +
                        '                        <td>' + numDoc + '</td>\n' +
                        '                        <td>' + cuota + '</td>\n' +
                        '                        <td class="valor_cobro">' + valor + '</td>\n' +
                        '                        <td>\n' +
                        '                            <button class="btn btn-xs btn-danger remove-fact"><i class="fa fa-times" aria-hidden="true"></i>\n' +
                        '                            </button>\n' +
                        '                        </td>\n' +
                        '                    </tr>'
                })

                const objDeudas = loadtableDetalle();
                const objExiste = []
                const objNoExiste = []
                for (let i = 0; i < arrayNumdoc.length; i++) {
                    if (objDeudas.includes(arrayNumdoc[i])) {
                        objExiste.push(1)
                    } else {
                        objExiste.push(0)
                    }
                }
                if (objExiste.includes(0)) {
                    $("#tabla-docs-deuda").append(htmlView)
                    $("#totalcobro").val(sumarDocumentos().toFixed(2))
                    $("#modalDocumentosDeudas").modal('hide')
                }
            })
        })

        function loadtableDetalle() {
            const documentos = [];
            $("#table-docs-pagos tr").each(function () {
                let doc = $(this).find('td:eq(2)').text()
                if (doc.lenght != '') {
                    let documt = doc.replace(/^\s*|\s*$/g, "");
                    documentos.push(documt)
                }
            })
            // console.log(documentos)
            return documentos
        }

        /** SUMA LOS VALORES DE LA TABLA DE PAGOS REALIZADOS*/
        $(document).on('click', ".remove-row", function (e) {
            e.preventDefault()
            $(this).closest('tr').remove();
            // console.log(sumar())
            $("#totalPagos").val(sumar().toFixed(2))
        })
        /** SUMA LOS VALORES DE LA TABLA DE FACTURAS POR COBRAR*/
        $(document).on('click', ".remove-fact", function (e) {
            e.preventDefault()
            $(this).closest('tr').remove();
            // console.log(sumarDocumentos())
            $("#totalcobro").val(sumarDocumentos().toFixed(2))
        })

        function sumar() {
            let totCel = 0
            let valCel = 0
            let sum = 0
            $(".valor_pago").each(function () {
                var value = $(this).text();
                if (!isNaN(value) && value.length != 0) {
                    sum += parseFloat(value);
                } else {
                    sum = 0
                }
            });
            return sum
        }

        function sumarDocumentos() {
            let totCel = 0
            let valCel = 0
            let sum = 0
            $(".valor_cobro").each(function () {
                var value = $(this).text();
                if (!isNaN(value) && value.length != 0) {
                    sum += parseFloat(value);
                } else {
                    sum = 0
                }
            });
            return sum
        }

        function sumarValoresRet() {
            let totCel = 0
            let valCel = 0
            let sum = 0
            $(".valor-abonado").each(function () {
                var value = $(this).text();
                if (!isNaN(value) && value.length != 0) {
                    sum += parseFloat(value);
                } else {
                    sum = 0
                }
            });
            return sum
        }

        function countFilasPagos() {
            return $("#table-forma-pagos tr").length

        }

        function countFilasDocs() {
            return $("#table-docs-pagos tr").length

        }


        $(document).on('click', "#grabarPago", function (e) {
            e.preventDefault()
            let bandEstado = false
            let res = validaEstadoCcInf()
            if(res.includes('1')){
                if(validaDocumentoAsociado() == false){
                    console.log(validaDocumentoAsociado())
                    Swal.fire({
                        icon: 'error',
                        title: "El documento retenido no se encuentra en proceso",
                    })
                }else{
                    bandEstado = true
                }
            }else{
                bandEstado = true
            }

            if(bandEstado == true){
                let totalingreso = $("#totalPagos").val()
                let totaldeuda = $("#totalcobro").val()
                let tipoCobro = $("#tipoCobro").val()
                if (parseFloat(totalingreso) != parseFloat(totaldeuda)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'valor de los totales deben cuadrar...',
                    })
                } else if (countFilasPagos() <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Debe ingresar pagos...',
                    })
                } else if (countFilasDocs() <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Debe ingresar Documentos...',
                    })
                } else {
                    let formData = new FormData(document.getElementById('form-documentos-cobros'))
                    $("#table-forma-pagos tr").each(function () {
                        let formapago = $(this).find('td:eq(1)').text()
                        let banco = $(this).find('td:eq(2)').text()
                        let tarjeta = $(this).find('td:eq(3)').text()
                        let fecha = $(this).find('td:eq(4)').text()
                        let numdoc = $(this).find('td:eq(5)').text()
                        let numdoFact_Ret = $(this).find('td:eq(7)').text()
                        let valor = $(this).find('td:eq(8)').text()
                        formData.append("formapago[]", formapago);
                        formData.append("banco[]", banco);
                        formData.append("tarjeta[]", tarjeta);
                        formData.append("fecha[]", fecha);
                        formData.append("numdoc[]", numdoc);
                        formData.append("valor[]", valor);
                        formData.append("numDocAsoc[]", numdoFact_Ret);

                    })
                    $("#table-docs-pagos tr").each(function () {
                        let id = $(this).find('input[type="hidden"]').val()
                        let tipo = $(this).find('td:eq(1)').text()
                        let factura = $(this).find('td:eq(2)').text()
                        let cuota = $(this).find('td:eq(3)').text()
                        let valorfactura = $(this).find('td:eq(4)').text()
                        formData.append("ideuda[]", id);
                        formData.append("tipo[]", tipo);
                        formData.append("factura[]", factura);
                        formData.append("cuota[]", cuota);
                        formData.append("valorfactura[]", valorfactura);
                    })

                    formData.append("option", 1);
                    let observacion = $("#observation").val()
                    let referencia = $("#referencia").val()
                    let cliente = $("#clientecod").val()
                    formData.append("option", 1);
                    formData.append("referencia", referencia);
                    formData.append("tipoCobro", tipoCobro);
                    formData.append("observacion", observacion);
                    formData.append("observacion", observacion);
                    formData.append("cliente", cliente);

                    formData.append("totalPago", totalingreso);
                    formData.append("totalIngreso", totaldeuda);

                    $.ajax({
                        url: './?action=processCobros',
                        type: 'POST',
                        data: formData,
                        processData: false,  // tell jQuery not to process the data
                        contentType: false,   // tell jQuery not to set contentType
                        beforeSend: function () {
                            // setting a timeout
                            $("#myModalLoading").modal('toggle')
                            $("#loading").addClass('loading');
                        },
                        success: function (resultado) {
                            console.log(resultado)
                            /*=============================*/
                            let respons = JSON.parse(resultado)
                            if (respons.mensaje.substr(0, 1) == 1) {
                                Swal.fire({
                                    icon: 'success',
                                    title: respons.mensaje.substr(2),
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.open('reportes/cobros/ticket-cobro.php?id=' + parseInt(respons.id), '_blank');
                                        location = "./?view=paymenths";
                                    }
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: respons.mensaje.substr(2),
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.href = './?view=paymenths';
                                    }
                                })
                            }
                            /*================*/
                        }
                    })
                }
            }
        })
    }) /** ============== FIN DE FUNCION DOCUMENT.READY ==================== */
}