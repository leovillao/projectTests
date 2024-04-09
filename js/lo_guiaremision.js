$(function () {
    $("#cdestino").mask('000')
    $("#secuencia").mask('000000')
    $("#tipoEgreso").change(function () {
        noVisible()
    })

    noVisible()

    function noVisible() {
        let tipo = $('#tipoEgreso').val()
        if (tipo == 0) {
            $(".selectIngresos").prop('disabled', true)
            $(".valorBodega").prop('disabled', true)
            $(".valorSucursal").prop('disabled', true)
        } else if (tipo == 2) {
            // aparece los egreso
            $(".secuenciaNumero").text('Documento')

            $(".labelBodega").removeClass('noVisible')
            $(".valorBodega").removeClass('noVisible')
            $(".valorBodega").val(0).trigger('change')

            $(".labelBodega").addClass('visible')
            $(".valorBodega").addClass('visible')

            $(".labelSucursal").removeClass('visible')
            $(".valorSucursal").removeClass('visible')

            $(".labelSucursal").addClass('noVisible')
            $(".valorSucursal").addClass('noVisible')


            $(".labelBodega").prop('disabled', false)
            $(".valorBodega").prop('disabled', false)

            $(".labelSucursal").prop('disabled', true)
            $(".valorSucursal").prop('disabled', true)

            $(".labelEgreso").prop('disabled', false)
            $(".valorEgreso").prop('disabled', false)

            $(".labelEgreso").removeClass('noVisible')
            $(".valorEgreso").removeClass('noVisible')

            $(".labelEgreso").addClass('visible')
            $(".valorEgreso").addClass('visible')
            // aparece los emision
            $(".labelEmision").prop('disabled', true)
            $(".valorEmision").prop('disabled', true)

            $(".labelEmision").removeClass('visible')
            $(".valorEmision").removeClass('visible')

            $(".labelEmision").addClass('noVisible')
            $(".valorEmision").addClass('noVisible')

            $(".valorEmision").val(0).trigger('change')
            $("#secuencia").val('')

        } else if (tipo == 1) {
            $(".secuenciaNumero").text('Secuencia')

            $(".labelSucursal").removeClass('noVisible')
            $(".valorSucursal").removeClass('noVisible')

            $(".labelSucursal").addClass('visible')
            $(".valorSucursal").addClass('visible')

            $(".labelBodega").removeClass('visible')
            $(".valorBodega").removeClass('visible')

            $(".labelBodega").addClass('noVisible')
            $(".valorBodega").addClass('noVisible')

            $(".labelSucursal").prop('disabled', false)
            $(".valorSucursal").prop('disabled', false)

            $(".labelBodega").prop('disabled', true)
            $(".valorBodega").prop('disabled', true)

            // aparece los emision
            $(".labelEmision").prop('disabled', false)
            $(".valorEmision").prop('disabled', false)

            $(".labelEmision").removeClass('noVisible')
            $(".valorEmision").removeClass('noVisible')

            $(".labelEmision").addClass('visible')
            $(".valorEmision").addClass('visible')
            // desaparace los egresos
            $(".labelEgreso").prop('disabled', true)
            $(".valorEgreso").prop('disabled', true)

            $(".labelEgreso").removeClass('visible')
            $(".valorEgreso").removeClass('visible')

            $(".labelEgreso").addClass('noVisible')
            $(".valorEgreso").addClass('noVisible')
            $(".valorEgreso").val(0).trigger('change')
            $("#secuencia").val('')

        }
    }

    $("#btn-grabar-guia").click(function () {
        let campos = document.querySelectorAll('.campos')
        let datos = new FormData()
        $('.campos').each(function () {
            // console.log( $(this).attr('name') + " / " + $(this).val());
            datos.append($(this).attr('name'), $(this).val())
        });

        $("#table-guiaRemision tbody tr").each(function () {
            let row = $(this)
            datos.append('idop', row.attr('idOpe'))
            $(this).find('input[type="checkbox"]').each(function () {
                if ($(this).is(":checked")) {
                    datos.append('iddetalle[]', row.attr('idDetalle'))
                    datos.append("codigo[]", $(row).find('td:eq(0)').text())
                    datos.append("producto[]", $(row).find('td:eq(1)').text())
                    datos.append("cantidad[]", $(row).find('td:eq(2)').text())
                    datos.append("unidad[]", $(row).find('td:eq(3)').text())
                    datos.append("idunidad[]", $(row).find('td').eq(3).find('input[type="hidden"]').val())

                }
            });
        })

        $.ajax({
            url: './?action=lo_guiaremision_grabar',
            type: 'POST',
            data: datos,
            processData: false,  // tell jQuery not to process the data
            contentType: false,   // tell jQuery not to set contentType
            success: function (respond) {
                console.log(respond)
                let r = JSON.parse(respond)
                if (r.msj.substr(0, 1) == 1) {
                    Swal.fire({
                        // position: 'top-end',
                        icon: 'success',
                        title: r.msj.substr(2),
                        showConfirmButton: false,
                        timer: 2000
                    })
                    if (r.tipoEmision == 1) {
                        // processDocuments(datos.documento, datos.id, datos.tipo, datos.reporte)
                        procesarEnvioGuia(r.documento, r.id, r.tipo, r.reporte)
                    }
                    let link = "./reportes/generales/"+r.reporte+".php?id=" + r.id
                    $("#impresion-btn").attr('href', link)
                    $("#impresion-btn").attr('target', "_blank")
                }else{
                    Swal.fire({
                        // position: 'top-end',
                        icon: 'error',
                        title: r.msj.substr(2),
                        showConfirmButton: false,
                        timer: 2000
                    })
                }
                /**
                 *     $r->msj = "1-Guia de remision creada con exito";
                 *     $r->documento = $docum;
                 *     $r->numero = $numFactura;
                 *     $r->reporte = $formato->fopage;
                 *     $r->tipoEmision = $electronico;
                 *     $r->id = $add[1];
                 * */
            }
        })
    })

    function procesarEnvioGuia(documento, id, tipo, reporte) {
        $.ajax({
            url: './?action=processEnvioGuia',
            type: 'POST',
            data: {
                "id": id,
                "documento": documento,
                "tipo": tipo,
                "reporte": reporte,
            },
            success: function (respond) {
                // console.log(respond)
                let r = JSON.parse(respond)
                if(r[0].msjAuto.substr(0,1) == 1)  {
                    Swal.fire({
                        // position: 'top-end',
                        icon: 'success',
                        html: '<p><h4>'+r[0].msjAuto.substr(2)+'</h4><h4>'+r[1].msjMail[0].substr(2)+'</h4></p>',
                        showConfirmButton: false,
                        timer: 2000
                    })
                }else{
                    Swal.fire({
                        // position: 'top-end',
                        icon: 'error',
                        title: r[0].msjAuto.substr(2),
                        showConfirmButton: false,
                        timer: 2000
                    })
                }
            }
        })
    }

    $("#secuencia").blur(function () {
        if (validaDocumentoEgresos() == "") {
            if ($(this).val() != "") {
                let coddoc = $("#emisiondoc option:selected").attr('coddoc')
                let estab = $("#emisiondoc option:selected").attr('estab')
                let emision = $("#emisiondoc option:selected").attr('emision');
                let numero = $("#emisiondoc").val();
                let sucursal = $("#sucursal").val();
                let fecha = $("#fecha").val();
                let secuencia = $("#secuencia").val(); // numero de la  factura de la cual se cargaran los producto para la guia de remision

                if ($("#emisiondoc").val() != 0) {
                    // si se va a ralizar la carga de una factura
                    getDataFactura(coddoc, estab, emision, numero, secuencia, fecha, sucursal)
                }
                if ($("#egresodoc").val() != 0) {
                    // si se va a ralizar la carga de un egreso
                    let tipoEgreso = $("#egresodoc").val();
                    let bodega = $("#bodega").val();
                    let fecha = $("#fecha").val();
                    let secuencia = $("#secuencia").val(); // numero del egreso del cual se cargaran los productos para la guia de remision
                    getDataEgreso(tipoEgreso, bodega, fecha, secuencia)

                    console.log("egreso")
                }
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: validaDocumentoEgresos(),
            })
        }
    })

    function getDataFactura(coddoc, estab, emision, numero, secuencia, fecha, sucursal) {
        $.ajax({
            url: './?action=lo_guiaremision_getFactura',
            type: 'POST',
            data: {
                "coddoc": coddoc,
                "estab": estab,
                "emision": emision,
                "numero": numero,
                "secuencia": secuencia,
                "fecha": fecha,
                "sucursal": sucursal
            },
            success: function (respond) {
                console.log(respond)
                $("#table-guiaRemision tbody").html('')
                let r = JSON.parse(respond)
                $('#codigoCliente').val(r.cabecera.ruc)
                $('#nameCliente').val(r.cabecera.cliente)
                $('#idCliente').val(r.cabecera.clienteID)
                $('#idFactura').val(r.cabecera.idFactura)
                let option = ""
                $.each(r.detalle, function (i, item) {
                    console.log(item.itcodigo)
                    option += "<tr idDetalle='" + item.odid + "' idOpe='" + item.opid + "'> " +
                        "<td>" + item.itcodigo + "</td>" +
                        "<td>" + item.itname + "</td>" +
                        "<td>" + item.cantidad + "</td>" +
                        "<td><input type='hidden' value='" + item.unidadid + "'>" + item.unidad + "</td>" +
                        "<td><input type='checkbox' class='selectCheck' checked></td>" +
                        "</tr>"
                })
                $("#table-guiaRemision tbody").html(option)
            }
        })
    }

    function getDataEgreso(tipoEgreso, bodega, fecha, secuencia) {
        $.ajax({
            url: './?action=lo_guiaremision_getEgreso',
            type: 'POST',
            data: {
                "tipoEgreso": tipoEgreso,
                "secuencia": secuencia,
                "fecha": fecha,
                "bodega": bodega
            },
            success: function (respond) {
                console.log(respond)
                $("#table-guiaRemision tbody").html('')
                let r = JSON.parse(respond)
                $('#codigoCliente').val(r.cabecera.ruc)
                $('#nameCliente').val(r.cabecera.cliente)
                $('#idCliente').val(r.cabecera.clienteID)
                $('#opid').val(r.cabecera.idEgreso)
                let option = ""
                $.each(r.detalle, function (i, item) {
                    console.log(item.itcodigo)
                    option += "<tr idDetalle='" + item.odid + "' idOpe='" + item.opid + "'> " +
                        "<td>" + item.itcodigo + "</td>" +
                        "<td>" + item.itname + "</td>" +
                        "<td>" + item.cantidad + "</td>" +
                        "<td><input type='hidden' value='" + item.unidadid + "'>" + item.unidad + "</td>" +
                        "<td><input type='checkbox' class='selectCheck' checked></td>" +
                        "</tr>"
                })
                if (r.transporte != "") {
                    // $("#").val(r.transporte.id)
                    $("#nameTrans").val(r.transporte.name)
                    // $("#").val(r.transporte.tipo)
                    $("#codigoTrans").val(r.transporte.identificacion)
                    $("#placa").val(r.transporte.placa)
                }
                $("#table-guiaRemision tbody").html(option);
            }
        })
    }

    function validaDocumentoEgresos() {
        let msj = ""
        if ($("#sucursal").val() == 0 && $("#bodega").val() == 0) {
            msj = "0-Debe seleccionar Sucursal."
        }
        if ($("#emisiondoc").val() == 0 && $("#egresodoc").val() == 0) {
            msj = "0-Debe seleccionar punto de emision / egreso."
        }
        return msj
    }


    $("#getModalTransportista").click(function () {
        $("#tableTransportista").DataTable({
            "destroy": true,
            "keys": true,
            "ajax": {
                "method": "POST",
                "url": "./?action=lo_guiaremision_getTransportista",
                "data": {"option": 1}
            },
            "columns": [
                {"data": "id"},
                {"data": "name"},
                {"data": "identificacion"},
                {"data": "placa"},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            },
        })
        $("#modalTransportista").modal('toggle')
    })

    $(document).on('click', '#tableTransportista tbody tr', function () {
        let codigo = $(this).closest('tr').find('td').eq(0).text()
        let nombre = $(this).closest('tr').find('td').eq(1).text()
        let identificacion = $(this).closest('tr').find('td').eq(2).text()
        let placa = $(this).closest('tr').find('td').eq(3).text()
        $("#codigoTrans").val(codigo)
        $("#nameTrans").val(nombre).prop('disabled', true)
        $("#identificacionTrans").val(identificacion)
        $("#placaTrans").val(placa)
        $("#modalTransportista").modal('toggle')
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
        let ruc = $(this).parents("tr").find("td").eq(0).text()
        let name = $(this).parents("tr").find("td").eq(1).text()
        let id = $(this).parents("tr").find("td").eq(1).find('input[type="hidden"]').val()
        $("#codigoCliente").val(ruc)
        $("#nameCliente").val(name).prop("disabled", true)
        $("#idCliente").val(id)
        $("#modalCliente").modal('hide')

    }) /* ======= SELECCIONA EL CLIENTE Y LO CARGA EN LA SECCION DE CLIENTES DE LA VENTANA DE FACTURACION  ======= */

    function viewModalClientes() {
        $("#modalCliente").modal('toggle').on("shown.bs.modal", function () {
            $('#modalCliente .dataTables_filter input').focus();
        })
        $("#modalCliente .modal-header").css("background-color", "#84AC3B")
    }/* ================ SE EJECUTA LA VISUALIZACION DE LA VENTANA MODAL CLIENTES ===============*/

}) // fin de funcion
