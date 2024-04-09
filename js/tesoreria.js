import {validaFecha, validaTipoDocumento} from './funciones.js';

if (document.getElementById('tesoreria')) {

    $(document).ready(function () {

        let opcion = 1

        var tableProTot = $("#table-deudas-prov").DataTable({
            "destroy": true,
            "keys": true,
            "ajax": {
                "method": "POST",
                "url": "./?action=getProveeTes",
                "data": {"option": opcion}
            },
            "columns": [
                {"data": "ruc"},
                {"data": "razon"},
                {"data": "documentos" , "className": "text-center"},
                {"data": "retencion" , "className": "text-right"},
                {"data": "abonado" , "className": "text-right"},
                {"data": "saldo" , "className": "text-right"},
                {"data": "boton" , "className": "text-right"},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            },
        })

        /*Se da click al boton verde para visualizar la ventana modal con la tabla con de los documentos asociados*/
        $(document).on('click', '.btn-pago-docs', function () {
            $("#total").val('')
            $("#form-addpago").trigger('reset')
            // $("#tipopago").trigger('reset')
            $("#fechapago").prop('disabled',true)
            // $("#entidad").val('')
            // $("#numerocheq").val('')
            // $("#glosa").val('')
            let razon = $(this).closest('tr').find('td:eq(1)').text()
            let documento = $(this).closest('tr').find('td:eq(0)').text()
            let opcion = 2
            $('#table-docs-pago').DataTable().clear().destroy()
            var tableProTot = $("#table-docs-pago").DataTable({
                "destroy": true,
                "keys": true,
                "ajax": {
                    "method": "POST",
                    "url": "./?action=getProveeTes",
                    "data": {"option": opcion,"documento":documento}
                },
                "columns": [
                    {"data": "documentos"},
                    {"data": "tipo"},
                    {"data": "fecha"},
                    {"data": "total"},
                    {"data": "abono"},
                    {"data": "saldo"},
                    {"data": "aplicar"},
                    {"data": "abonar"},
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                },
            })


            // $.ajax({
            //     url: '?action=loadproPago',
            //     type: 'POST',
            //     data: {documento: documento, tipo: tipo},
            //     success: function (response) {
            //         console.log(response)
            //         $("#table-docs-pago").css('font-size', '11px')
            //         let res = JSON.parse(response)
            //         console.log(res.documentos)
            //         /* ============ Parsear el JSON como respuesta para almacenar la de documentos asociados o abonos========== */
            //         let viewHtml = ''
            //         $.each(res, function (i, item) {
            //             viewHtml += '<tr>'
            //             viewHtml += '<td>' + item.documentos + '</td><td>' + item.tipo + '</td><td>' + item.fecha + '</td><td>' + item.total + '</td><td>' + item.abono + '</td><td>' + item.saldo + '</td><td><button class=\'btn btn-xs btn-success btn-pago-total\' ><i class="fa fa-money" aria-hidden="true"></i></button></td><td><input class=\'form-control val-pago\' name=\'val-pago\' type=\'number\'></td>'
            //             viewHtml += '</tr>'
            //         });
            //         $("#table-body-pagos").html(viewHtml)
            //
            //         $('#table-docs-pago').DataTable().clear().destroy()
            //         $('#table-docs-pago').DataTable({
            //             "language": {
            //                 "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            //             },
            //         });
            //         /*if ($.fn.DataTable.isDataTable('#table-docs-pago')) {
            //             $('#table-docs-pago').DataTable().destroy();
            //         } else {
            //             $('#table-docs-pago').DataTable({
            //                 "language": {
            //                     "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            //                 },
            //             });
            //         }*/
            //     }
            // })
            $("#modalDocsPago").modal('toggle')
            $("#modalDocsPago .modal-title").text(razon)
            $("#modalDocsPago #idproveedor").val(documento)
            $("#modalDocsPago #row-form-pago").css('display', 'none')
        })

        function cargaDocs(documento) {

        }

        // cargadata()

        $(document).on('click', '.btn-pago-total', function (e) {
            e.preventDefault()
            let razon = $(this).closest('tr').find('td:eq(1)').text()
            let total = $(this).closest('tr').find('td:eq(5)').text()
            if (total != 0.00) {
                $(this).closest('tr').find('input[type="number"]').val(total)
                sumarValores()
            } else {
                Swal.fire({
                    "icon": 'error',
                    "title": 'Documento no tiene saldo'
                })
            }
        })

        $(document).on('change', '.val-pago', function () {
            let saldo = $(this).closest('tr').find('td:eq(5)').text()
            let valor = $(this).closest('tr').find('input[type="number"]').val()
            if (parseFloat(valor).toFixed(2) <= parseFloat(saldo).toFixed(2)) {
                sumarValores()
            } else {
                Swal.fire({
                    "icon": 'error',
                    "title": 'Valor ingresado no debe ser mayor al saldo del documento'
                })
                $(this).focus()
                $(this).val('')
            }
        })

        function sumarValores() {
            let sum = 0;
            $(".val-pago").each(function () {
                sum += +$(this).val();
            });
            $("#total").val(sum.toFixed(2))
            // return sum
        }

        $(document).on('click', '#gabar-pago-prov', function () {
            let fechareg = $("#fechareg").val()
            let numerocheq = $("#numerocheq").val()
            let tipopago = $("#tipopago").val()
            let glosa = $("#glosa").val()

            if (fechareg.length != '') {
                if (numerocheq.length != '') {
                    if (tipopago != 0) {
                        if (glosa.length != 0) {

                            let idproveedor = $("#idproveedor").val()
                            let conceptpago = $("#conceptopago").val()
                            let fechapago = $("#fechapago").val()
                            let entidades = $("#entidades").val()
                            let total = $("#total").val()
                            let tipo = 1
                            let arreglo = []
                            let documento = 0
                            let valor = 0

                            /* Se recorre las filas de la tabla para verificar sus campos alamcenarlos y crear el array para enviarlo por post*/
                            $("#table-docs-pago tbody tr").each(function () {
                                if ($(this).closest('tr').find('input[type="number"]').val() != 0) {
                                    arreglo.push({
                                        "valor": $(this).closest('tr').find('input[type="number"]').val(),
                                        "id": $(this).closest('tr').find('input[type="hidden"]').val()
                                    });
                                }
                            })

                            $.ajax({
                                url: './?action=addPago',
                                type: 'POST',
                                data: {
                                    fechareg: fechareg,
                                    idproveedor: idproveedor,
                                    conceptpago: conceptpago,
                                    tipopago: tipopago,
                                    fechapago: fechapago,
                                    entidades: entidades,
                                    numerocheq: numerocheq,
                                    glosa: glosa,
                                    total: total,
                                    arreglo: arreglo,
                                    tipo: tipo
                                },
                                success: function (res) {
                                    let cod = JSON.parse(res)
                                    console.log(cod)
                                    if (cod.tipo === 1) {
                                        //tableProTot.ajax.reload(null, false);
                                        Swal.fire({
                                            "icon": 'success',
                                            "title": cod.mjs
                                        })
                                        viewEgreso(cod.pago)
                                        $("#modalDocsPago").modal('hide')
                                        $("#form-addpago").trigger('reset')
                                    } else {
                                        Swal.fire({
                                            "icon": 'error',
                                            "title": cod.mjs
                                        })
                                    }
                                    tableProTot.ajax.reload( null, false );
                                }
                            })
                        } else {
                            Swal.fire({
                                "icon": 'error',
                                "title": 'Debe ingresar glosa o concepto de pago'
                            })
                        }
                    } else {
                        Swal.fire({
                            "icon": 'error',
                            "title": 'Debe seleccionar tipo de pago'
                        })
                    }
                } else {
                    Swal.fire({
                        "icon": 'error',
                        "title": 'Debe registrar numero de documento'
                    })
                }
            } else {
                Swal.fire({
                    "icon": 'error',
                    "title": 'Debe registrar fecha'

                })
            }
        })

        function viewEgreso(id) {
            window.open('?action=egresoPdf&id=' + id, '_blank')
        }

        function viewEgresoAnticipo(id) {
            window.open('?action=egresopdfA&id=' + id, '_blank')
        }

        /*Funcion para validar la fecha del documento con la fecha del pago*/

        $(document).on('blur', '#fe_pago', function () {
            let fechapago = $("#fe_pago").val()
            let fechadoc = $("#fe_doc").val()
            let r = validaFecha(fechapago, fechadoc) /*=================FUNCION EXPORTADA*/
            if (r.substr(0, 1) != "0") {
                let cod = r.substr(0, 1)
                let msj = r.substr(2)
                if (cod == 1) {
                    Swal.fire({
                        "icon": 'error',
                        "title": 'Fecha de pago no puede ser menor a fecha del documento'
                    })
                    $("#fe_pago").val(msj)
                }
            }
        })

        /*==========================================================
        * Valida del tipo de pago que se esta
        * =========================================================*/
        $(document).on('change', '#tipopago', function () {
            validaTipoDocumento($(this).val()) /*=================FUNCION EXPORTADA*/
        })

        /*============================================*/

        /* =============== Muestra la ventana para Crear nuevo anticipo =================*/
        $(document).on('click', '#btn-anticipo', function (e) {
            e.preventDefault()
            console.log("tesoreria")
            $("#modalAnticipos").modal('toggle')
            $("#modalAnticipos .modal-title").text('Nuevo Anticipo')
            $("#actualizaranticipo").css('display', 'none')
            $("#anularanticipo").css('display', 'none')
            $("#gabaranticipo").css('display', 'inline-block')
            $("#row-editar-anticipo").css('display', 'none')
            $("#modalAnticipos .modal-header").css('color', 'white')
            $("#modalAnticipos .modal-header").css('background', '#00541F')

        })
        /* =============== Muestra la ventana para Editar anticipo =================*/
        $(document).on('click', '#btn-anticipo-mod', function (e) {
            e.preventDefault()
            $("#modalAnticipos").modal('toggle')
            $("#modalAnticipos .modal-title").text('Editar Anticipo')
            $("#actualizaranticipo").css('display', 'inline-block')
            $("#anularanticipo").css('display', 'inline-block')
            $("#gabaranticipo").css('display', 'none')
            $("#row-editar-anticipo").css('display', 'block')
            $("#modalAnticipos .modal-header").css('color', 'white')
            $("#modalAnticipos .modal-header").css('background', '#009436')
            $("#modalAnticipos #id-anticipo").focus()
            $("#modalAnticipos #tipo").val(2)

        })

        $(document).on('change', '#tpago_anticipo', function () {
            let now = new Date();
            let day = ("0" + now.getDate()).slice(-2);
            let month = ("0" + (now.getMonth() + 1)).slice(-2);
            let today = now.getFullYear() + "-" + (month) + "-" + (day);
            let valor = $(this).val()
            if (Number(valor) == 1) {
                $("#fe_pago").attr('disabled', false)
            } else {
                $("#fe_pago").attr('disabled', true)
            }
            $.ajax({
                url: '?action=loadEntidades',
                type: 'POST',
                data: {id: valor},
                success: function (response) {
                    $("#entidad_anticipo").html(response)
                }
            })
            if (Number(valor) == 1) {
                $("#tag_num_anticipo").text('# de Cheque')
            } else if (Number(valor) == 2 || Number(valor) == 3) {
                $("#tag_num_anticipo").text('# de Referencia')
            } else if (Number(valor) == 4) {
                $("#tag_num_anticipo").text('# de Voucher')
            }
            let hoy = $("#fechareg").val()
            let fechadoc = $("#fe_doc").val()
            if (new Date(hoy).getTime() < new Date(fechadoc).getTime()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Fecha de registro no puede ser menor a fecha del documento',
                })
                $('#modalPagoExpress #fe_doc').val(today)
            }
        })

        $(document).on('click', '#gabaranticipo', function (e) {
            e.preventDefault()
            // let form =
            $.ajax({
                url: '?action=addanticipo',
                type: 'POST',
                data: $("#form-pago-anticipo").serialize(),
                success: function (e) {
                    // console.log(e)
                    let dato = JSON.parse(e)
                    let msj = ''
                    if (Number(dato.id) !== 0) {
                        $("#modalAnticipos").modal('hide')
                        $("#form-pago-anticipo").trigger('reset')
                        viewEgresoAnticipo(dato.id)
                        Swal.fire({
                            icon: 'success',
                            title: dato.msj
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: dato.msj
                        })
                    }
                }
            })
        })

        $('.select-ruc').select2({
            placeholder: 'SELECCIONE RUC...',
            allowClear: true
        });

        $(document).on('change', '.select-ruc', function () {
            let id = $(this).val()
            let tipo = 2
            if (id !== '') {
                $.ajax({
                    url: '?action=loadForAnticipo',
                    type: 'POST',
                    data: {id: id, tipo: tipo},
                    success: function (data) {
                        let dato = JSON.parse(data)
                        $("#razonAnticipo").val(dato.razon)
                        $("#beneficiarioAnticipo").val(dato.razon)
                    }
                })
            } else {
                $("#razonAnticipo").val('')
                $("#beneficiarioAnticipo").val('')
            }
        })

        function loadRuc() {
            let tipo = 1
            let html = ''
            $.ajax({
                url: '?action=loadForAnticipo',
                type: 'POST',
                data: {tipo: tipo},
                success: function (e) {
                    let data = JSON.parse(e)
                    html += '<option value="">Seleccione ruc...</option>'
                    data.forEach(function (d, index) {
                        html += '<option value="' + d.id + '">' + d.nombre + '</option>'
                    });
                    $("#select-ruc").html(html)
                }
            })
        }

        loadRuc()

        $(document).on('blur', '#id-anticipo', function (e) {
            let idanticipo = $(this).val()
            let tipo = 3
            let banc = 0
            $.ajax({
                url: '?action=loadForAnticipo',
                type: 'POST',
                data: {id: idanticipo, tipo: tipo},
                success: function (respond) {
                    let dato = JSON.parse(respond)
                    $("#select-ruc").val(dato.idproveedor).trigger('change')
                    $("#beneficiarioAnticipo").val(dato.beneficiario)
                    $("#tpago_anticipo").val(dato.tipopago).trigger('change')
                    $("#comentarioanticipo").val(dato.comentario)
                    $("#fe_pago").val(dato.fechapago)
                    $("#fe_doc").val(dato.fechareg)
                    $("#val_anticipo").val(dato.total)
                    $("#numeroDocumento").val(dato.numero)
                    if (dato.tipopago !== 0) {
                        $("#entidad_anticipo").val(dato.entidad)
                    }
                }
            })
        })

        $(document).on('click', '#actualizaranticipo', function (e) {
            e.preventDefault()
            $.ajax({
                url: '?action=addanticipo',
                type: 'POST',
                data: $("#form-pago-anticipo").serialize(),
                success: function (respon) {
                    // console.log(respon)
                    let dato = JSON.parse(respon)
                    let msj = ''
                    if (dato.id !== 0) {
                        msj = dato.msj.substr(2)
                        $("#modalAnticipos").modal('hide')
                        viewEgresoAnticipo(dato.id)
                        Swal.fire({
                            icon: 'success',
                            title: msj
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: msj
                        })
                    }
                }
            })
        })
        $(document).on('click', '#cerraranticipo', function (e) {
            e.preventDefault()
            $(".select-ruc").select2("val", "");
            $("#form-pago-anticipo").trigger('reset')
        })
        $(document).on('click', '#anularanticipo', function (e) {
            e.preventDefault()
            let tipo = 3
            let id = $("#id-anticipo").val()
            if (id === '' || id === 0) {
                Swal.fire({
                    icon: 'error',
                    title: "ingrese numero de Anticipo"
                })
            } else {
                Swal.fire({
                    title: 'Eliminar Anticipo',
                    text: "Desea eliminar anticipo #" + id + " .?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'green',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Confirmar...'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: '?action=addanticipo',
                            type: 'POST',
                            data: {id: id, tipo: tipo},
                            success: function (repon) {
                                let dato = JSON.parse(repon)
                                if (dato.tipo === 0) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: dato.msj
                                    })
                                    $("#form-pago-anticipo").trigger('reset')
                                    $("#modalAnticipos").modal('hide')
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: dato.msj
                                    })
                                }

                            }
                        });
                    }
                })
            }
        })

        $(document).on('click', '#btn-pago-mod', function (e) {
            e.preventDefault()
            $("#modalDocsPago").modal('toggle')
            $("#modalDocsPago .modal-title").text('Edicion de pago')
            $("#modalDocsPago #row-form-pago").css('display', 'block')
            $("#form-addpago").load(" #form-addpago")
        })

        $(document).on('blur', '#numPago', function () {
            let num = $(this).val()
            let concepto = $("#conceptopago").val()
            let tipo = 3
            // console.log(concepto + ' ++ ' + num)
            $.ajax({
                url: '?action=loadproPago',
                type: 'POST',
                data: {concepto: concepto, num: num, tipo: tipo},
                success: function (response) {
                    console.log(response)
                }
            })
        })
    })

} /* VALIDA LA EXISTENCIA DEL ELEMENTO QUE IDENTIFICA LA PAGINA DE TESORERIA PARA EJECUTAR LAS FUNCIONES SOLO DE ESTE PAGINA */
