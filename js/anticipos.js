$(document).ready(function () {
    if (document.getElementById('anticipos')) {

        $(document).on('click', '#btn-del-anticipo', function () {
            $("#moalAnulaAnticipo").modal('show')
        })

        function anulaAnticipo(id) {
            let option = 6
            let res = ''
            $.ajax({
                url: './?action=loadAnticipos',
                type: 'POST',
                data: {option: option, id: id},
                success: function (datos) {
                    res = JSON.parse(datos)
                    if (res.substr(0, 1) == 1) {
                        Swal.fire({
                            title : 'Anular!',
                            html : "<h3>"+res.substr(2)+"</h3>",
                            icon : 'success',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $("#moalAnulaAnticipo").modal('hide')
                                viewTableLoad()
                                $("#numForAnulacion").val('')
                            }
                        })
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            html: "<h3>" + res.substr(2) + "</h3>",
                            icon: 'error',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $("#numForAnulacion").val('')
                            }
                        })
                    }
                }
            })
        }

        $(document).on('click', '#btn-anular-anticipo', function () {
            let id = $("#numForAnulacion").val()
            Swal.fire({
                title: 'Anular Anticipo?',
                html: "<h3>Desea anula el anticipo # " + id + "</h3>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, Anular!'
            }).then((result) => {
                if (result.isConfirmed) {
                    anulaAnticipo(id)
                }
            })
        })

        $(document).on('click', '#buscarAnticipos', function (w) {
            w.preventDefault()
            let desde = $("#desde").val()
            let hasta = $("#hasta").val()
            let cliente = ''
            if ($("#chkcliente").is(":checked")) {
                cliente = $("#cliente").val()
            }
            viewTableLoad(desde, hasta, cliente)
        })

        $(document).on('click', '#listarAnticipos', function () {
            $("#table-anticipos").DataTable().clear().destroy()
            $("#table-anticipos").DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                },
            })
            $("#modalAnticipos").modal('show')
        })

        $(document).on("click", "#table-anticipos tbody tr td", function () {
            let id = $(this).parents("tr").find("td").eq(0).text()
            loadAnticipoModificacion(id)
        })

        function loadAnticipoModificacion(id) {
            $.ajax({
                url: './?action=loadAnticipos',
                type: 'POST',
                data: {"option": 2, "id": id},
                success: function (resp) {
                    let datos = JSON.parse(resp)
                    if (datos.estado == 0) {
                        // defecha,tdid,devence,derefer,decuota,codigo,cliente,plazo,deobserva,desaldo,
                        $("#fechaDocumento").val(datos.cabecera.fecha);
                        $("#clientecod").val(datos.cabecera.clientecod)
                        $("#clienteTxt").val(datos.cabecera.clientetext)
                        $("#observation").val(datos.cabecera.observacion)
                        $("#numeroAnticipo").val(datos.cabecera.anticipo)
                        let html = ''
                        let tValor = 0
                        $.each(datos.detalle, function (i, item) {
                            let banco = ''
                            if (item.bancoText == "") {
                                banco = item.bbText
                            } else {
                                banco = item.bancoText
                            }
                            html += '<tr><td></td><td>' + item.formaText + '</td><td>' + banco + '</td><td>' + item.ctaTar + '</td><td>' + item.fecDoc + '</td><td>' + item.chq + '</td><td class="valor_pago">' + item.valor + '</td><td><button class="btn btn-danger btn-xs remove-row"><i class="fa fa-times" aria-hidden="true"></i></button></td></tr>';
                            tValor += parseFloat(item.valor)
                        })
                        $("#table-forma-pagos").html(html)
                        $("#totalPagos").val(tValor.toFixed(2))
                        $("#ActualizarPago").removeClass('Novisible').addClass('visible')
                        $("#grabarAnticipo").addClass('Novisible')
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: "Anticipo tiene abonos , no puede ser editado",
                        }).then((result) => {
                            if (result.isConfirmed) {
                            }
                        })
                    }
                }
            })
            $("#modalAnticipos").modal('hide')
        }

        $(document).on('click', "#ActualizarPago", function (e) {
            e.preventDefault()
            let totalingreso = $("#totalPagos").val()
            if (parseFloat(totalingreso) == "" || parseFloat(totalingreso) == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Debe ingresar valores...',
                })
            } else {
                let formData = new FormData(document.getElementById('form-documentos-cobros'))
                $("#table-forma-pagos tr").each(function () {
                    let formapago = $(this).find('td:eq(1)').text()
                    let banco = $(this).find('td:eq(2)').text()
                    let tarjeta = $(this).find('td:eq(3)').text()
                    let fecha = $(this).find('td:eq(4)').text()
                    let numdoc = $(this).find('td:eq(5)').text()
                    let valor = $(this).find('td:eq(6)').text()
                    formData.append("formapago[]", formapago);
                    formData.append("banco[]", banco);
                    formData.append("tarjeta[]", tarjeta);
                    formData.append("fecha[]", fecha);
                    formData.append("numdoc[]", numdoc);
                    formData.append("valor[]", valor);
                    formData.append("idDet[]", $(this).find('input[type="hidden"]').val())
                })

                if (document.getElementById("sucursal")) {
                    formData.append("sucursal", $("#sucursal").val());
                } else {
                    formData.append("sucursal", "");
                }

                let observacion = $("#observation").val()
                let totalAnticipo = $("#totalPagos").val()
                formData.append("option", 1);
                formData.append("fechaDoc", $("#fechaDocumento").val());
                formData.append("numeroAnticipo", $("#numeroAnticipo").val());
                formData.append("observacion", observacion);
                formData.append("totalAnticipo", totalAnticipo);
                $.ajax({
                    url: './?action=processEditAnticipos',
                    type: 'POST',
                    data: formData,
                    processData: false,  // tell jQuery not to process the data
                    contentType: false,   // tell jQuery not to set contentType
                    beforeSend: function () {
                        // setting a timeout
                        // $("#myModalLoading").modal('toggle')
                        // $("#loading").addClass('loading');
                    },
                    success: function (resultado) {
                        console.log(resultado)
                        let respons = JSON.parse(resultado)
                        if (respons.respuesta.substr(0, 1) == 0) {
                            Swal.fire({
                                icon: 'error',
                                title: respons.respuesta.substr(2),
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.href = './?view=anticipos';
                                }
                            })
                            $("#myModalLoading").modal('hide')
                            $("#grabarAnticipo").prop('disabled', true)
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: respons.respuesta.substr(2),
                            }).then((result) => {
                                $("#myModalLoading").modal('hide')
                                $("#numeroAnticipo").val(respons.documento)
                                $("#impresion-btn").attr('href', 'reportes/cartera/reporteAnticipo.php?id=' + respons.documento)
                                $("#impresion-btn").attr('target', "_blank")
                                $("#grabarAnticipo").prop('disabled', true)
                            })
                        }
                    }
                })
            }
        })

        $(document).on('click', '#buscar', function () {
            let desde = $("#desde").val()
            let hasta = $("#hasta").val()
            let cliente = ''
            viewTableLoad(desde, hasta, cliente)
        })

        function viewTableLoad(desde, hasta, cliente) {
            $("#table-anticipos").DataTable({
                "destroy": true,
                "ajax": {
                    "method": "POST",
                    "url": "./?action=loadAnticipos",
                    "data": {"option": 1, "desde": desde, "hasta": hasta, "cliente": cliente}
                },

                "columns": [
                    {"data": "anticipo"},
                    {"data": "fecha"},
                    {"data": "cliente"},
                    {"data": "total"},
                    // {"data": "estado"},
                    // {"data": "creado"},
                    // {"defaultContent": "<button class='btn btn-xs btn-danger remove-anticipo '><i class=\"fa fa-remove\" aria-hidden=\"true\"></i></button>"},
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                },
            })
        }

        $("#table-clientes").DataTable({
            "destroy": true,
            "keys": true,
            "ajax": {
                "method": "POST",
                "url": "./?action=personLoad",
                "data": {"option": 1}
            },
            "columns": [
                {"data": "ruc"},
                {"data": "name"},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            },
        })

        $(document).on('click', '#table-clientes > tbody tr td', function () { /* ==== Click sobre tbody CLIENTES ===== */
            // event.preventDefault()
            let name = $(this).parents("tr").find("td").eq(1).text()
            let ruc = $(this).parents("tr").find("td").eq(0).text()
            $("#clientecod").val(ruc)
            $("#clienteTxt").val(name)
            $("#modalCliente").modal('hide')
        })

        function viewModalClientes() {
            $("#modalCliente").modal('toggle').on("shown.bs.modal", function () {
                $('#modalCliente .dataTables_filter input').focus();
            })
            $("#modalCliente .modal-header").css("background-color", "#84AC3B")
        }

        $(document).on('click', '#btnCliente', function (e) {
            viewModalClientes()
        })

        SelectCliente()

        function SelectCliente() {
            $('#cliente').select2({
                placeholder: "Seleccione cliente..."
            });
        }

        $(document).on('click', '#btn-observation', function (e) {
            e.preventDefault()
            $("#modalObservation").modal('toggle')
        })

        loadDataPersonId()

        function loadDataPersonId() {
            let option = 2
            let viewHtml = ''
            $.ajax({
                url: "./?action=personLoad",
                type: "POST",
                data: {option: option},
                success: function (respon) {
                    let res = JSON.parse(respon)
                    let viewHtml = ''
                    let select = ''
                    viewHtml += '<option value="">Seleccion cliente...</option>'
                    $.each(res, function (i, item) {
                        viewHtml += '<option value="' + item.id + '" >' + item.text + '</option>'
                    });
                    $("#cliente").html(viewHtml)
                }
            })
        }

        $(document).on('change', '#formapago', function (e) {
            e.preventDefault()
            let option = 1
            let idfp = $(this).val()
            let namefp = $("#formapago option:selected").text()
            if (Number(idfp) != 1) {
                $("#efectivo").attr('disabled', true)
                $("#addFormaPago").attr('disabled', true)
                $("#modalFormaPago").modal('toggle')
                $("#nameId").val(namefp)
                $.ajax({
                    url: '?action=loadFormas',
                    type: 'POST',
                    data: {option: option, idfp: idfp},
                    success: function (result) {
                        console.log(result)
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
                            $("#labelDocumento").text("Numero de Cheque")
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
                        /*=================================================================================
                        Valida si la entidad trae datos con respecto a la visualizar y seleccionar la misma
                        * ================================================================================*/
                        /*if (dato.bcpropio != 'S') {*/
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
                        /*} else {
                            if ($.isEmptyObject(dato.entidades)) {
                                $("#bancopropio").attr('disabled', 'disabled')
                            } else {
                                tags += '<option value="0">Seleccione Entidad...</option>'
                                $.each(dato.entidades, function (i, item) {
                                    tags += '<option value="' + item.id + '">' + item.name + '</option>'
                                });
                                $("#bancopropio").removeAttr('disabled')
                                sumar()
                            }
                            $("#bancopropio").html(tags)
                        }*/
                    }
                })

            } else {
                $("#efectivo").removeAttr('disabled')
                $("#addFormaPago").removeAttr('disabled')
            }
        })

        $("#addFormaPago").click(function (e) {
            e.preventDefault()
            let cliente = $("#cliente").val()
            let valor = $("#efectivo").val()
            if (cliente != "") {
                if (Number(valor) != "" || Number(valor) != 0) {
                    let formapago = $('select[id="formapago"] option:selected').text()
                    let valor = $('#efectivo').val()
                    let fecha = $('#date').val()
                    let tableHtml = '<tr><td></td><td>' + formapago + '</td><td></td><td></td><td>' + fecha + '</td><td></td><td class="valor_pago">' + valor + '</td><td><button class="btn btn-danger btn-xs remove-row"><i class="fa fa-times" aria-hidden="true"></i></button></td></tr>'
                    $("#table-forma-pagos").append(tableHtml)
                    $("#totalPagos").val(sumar().toFixed(2))
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: "Debe ingresar cantidad...",
                    })
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: "Debe seleccionar cliente...",
                })/*.then((result) => {
                    if (result.isConfirmed) {
                        location.href = './?view=paymenths';
                    }
                })*/
            }
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

        $("#addFormaP").click(function (e) {
            e.preventDefault()
            let banco = $('#bancoPago').val()
            let valor = $('#valor').val()
            if (banco != 0) {
                if (valor != 0 || valor != '') {
                    let tot = sumar()
                    let fp = $('#nameId').val()
                    let textobanco = $('select[id="bancoPago"] option:selected').text()
                    let fechapago = $('#fechadocumento').val()
                    let documento = $('#cuentaDocumento').val()
                    let cuenta = $('#cuentaCliente').val()
                    let tableHtml = '<tr><td></td><td>' + fp + '</td><td>' + textobanco + '</td><td>' + cuenta + '</td><td>' + fechapago + '</td><td>' + documento + '</td><td class="valor_pago">' + valor + '</td><td><button class="btn btn-danger btn-xs remove-row"><i class="fa fa-times" aria-hidden="true"></i></button></td></tr>'
                    $("#table-forma-pagos").append(tableHtml)
                    let tl = parseFloat(tot) + parseFloat(valor)
                    $("#totalPagos").val(tl.toFixed(2))
                    $("#modalFormaPago").modal('hide')
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Debe ingresar valores...',
                    })
                }

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Debe seleccionar Entidad...',
                })
            }
        })

        /** SUMA LOS VALORES DE LA TABLA DE PAGOS REALIZADOS*/
        $(document).on('click', ".remove-row", function (e) {
            e.preventDefault()
            $(this).closest('tr').remove();
            console.log(sumar())
            $("#totalPagos").val(sumar().toFixed(2))
        })

        $(document).on('click', "#grabarAnticipo", function (e) {
            e.preventDefault()
            let totalingreso = $("#totalPagos").val()
            if (parseFloat(totalingreso) == "" || parseFloat(totalingreso) == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Debe ingresar valores...',
                })
            } else if($("#tipoanticipo").val() == '' || $("#tipoanticipo").val() == 0){
                Swal.fire({
                    icon: 'error',
                    title: 'Debe seleccionar tipo de anticipo...',
                })
            }else{
                let formData = new FormData(document.getElementById('form-documentos-cobros'))
                $("#table-forma-pagos tr").each(function () {
                    let formapago = $(this).find('td:eq(1)').text()
                    let banco = $(this).find('td:eq(2)').text()
                    let tarjeta = $(this).find('td:eq(3)').text()
                    let fecha = $(this).find('td:eq(4)').text()
                    let numdoc = $(this).find('td:eq(5)').text()
                    let valor = $(this).find('td:eq(6)').text()
                    formData.append("formapago[]", formapago);
                    formData.append("banco[]", banco);
                    formData.append("tarjeta[]", tarjeta);
                    formData.append("fecha[]", fecha);
                    formData.append("numdoc[]", numdoc);
                    formData.append("valor[]", valor);
                })

                if (document.getElementById("sucursal")) {
                    formData.append("sucursal", $("#sucursal").val());
                } else {
                    formData.append("sucursal", "");
                }

                let observacion = $("#observation").val()
                let totalAnticipo = $("#totalPagos").val()
                formData.append("option", 1);
                formData.append("observacion", observacion);
                formData.append("tipoanticipo", $("#tipoanticipo").val());
                formData.append("fechaDoc", $("#fechaDocumento").val());
                formData.append("totalAnticipo", totalAnticipo);
                $.ajax({
                    url: './?action=processAnticipos',
                    type: 'POST',
                    data: formData,
                    processData: false,  // tell jQuery not to process the data
                    contentType: false,   // tell jQuery not to set contentType
                    beforeSend: function () {
                        // setting a timeout
                        // $("#myModalLoading").modal('toggle')
                        // $("#loading").addClass('loading');
                    },
                    success: function (resultado) {
                        console.log(resultado)
                        let respons = JSON.parse(resultado)
                        if (respons.respuesta.substr(0, 1) == 0) {
                            Swal.fire({
                                icon: 'error',
                                title: respons.respuesta.substr(2),
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.href = './?view=anticipos';
                                }
                            })
                            $("#myModalLoading").modal('hide')
                            $("#grabarAnticipo").prop('disabled', true)
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: respons.respuesta.substr(2),
                            }).then((result) => {
                                $("#myModalLoading").modal('hide')
                                $("#numeroAnticipo").val(respons.documento)
                                $("#impresion-btn").attr('href', 'reportes/cartera/reporteAnticipo.php?id=' + respons.documento)
                                $("#impresion-btn").attr('target', "_blank")
                                $("#grabarAnticipo").prop('disabled', true)
                            })
                        }
                    }
                })
            }
        })

        /*        $(document).on('click', '.remove-anticipo', function (e) {
                    e.preventDefault()
                    let estado = $(this).closest('tr').find('td:eq(4)').text()
                    let id = $(this).closest('tr').find('td:eq(0)').text()
                    let valor = $(this).closest('tr').find('td:eq(3)').text()
                    if (estado == "ANULADO") {
                        Swal.fire(
                            'Anulado!',
                            'Documento ya se encuentra anulado',
                            'error'
                        )
                    } else {

                    }
                })*/


        $(document).on('click', '#chkcliente', function () {
            if ($(this).is(":checked")) {
                $("#cliente").removeAttr("disabled")
            } else {
                $("#cliente").attr("disabled", true)
            }
        })

    } /* ===== VALIDACION DE EXISTENCIA CAMPO ANTICPO ID*/
}) /* ======= FIN DE FUNCION DOCUMENT READY ==============*/
