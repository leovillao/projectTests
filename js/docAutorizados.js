if (document.getElementById('autorizados')) {
    /** ACTUALIZACION DE BUSQUEDA DE DOCUMENTOS POR CLICK EN BOTON */

    const tipodocumento = $("#tipodocumento").val()
    const desde = $("#desde").val()
    const hasta = $("#hasta").val()

    loadFacturas(tipodocumento, desde, hasta)


    $(document).on('click', '#btn-buscar-documentos', function (e) {
        e.preventDefault();

        let desde = $("#desde").val()
        let hasta = $("#hasta").val()
        let tipodoc = $("#tipodocumento").val()
        loadFacturas(tipodoc, desde, hasta)

    })


    /** =============================================================*/

    /* MUESTRA LA VENTANA PARA LA ANULACION DE LA FACTURA */
    $(document).on("click", "#btn-anular-fact", function (e) {
        e.preventDefault()
        $("#form-anulacion").trigger("reset")
        $("#modalAnulacion").modal("toggle")
    })
    /* PROCESA EL FORMULARIO PARA LA ANULACION DEL DOCUMENTO */
    $(document).on('click', '#anular-doc', function (e) {
        e.preventDefault()
        // if (validaCamposVacios() == '') {
        var datos = new FormData(document.getElementById("form-anulacion"));
        datos.append("emision", $("#estabemision").val());

        let combo = document.getElementById("estabemision");
        let emisionText = combo.options[combo.selectedIndex].text;

        datos.append("estab", emisionText);
        datos.append("secuencia", $("#secuencia").val());
        datos.append("motivo", $("#motivo").val());
        datos.append("tipodoc", $("#tipodoc").val());

        $.ajax({
            url: '?action=anulacionDocs',
            type: 'POST',
            data: datos,
            processData: false,  // tell jQuery not to process the data
            contentType: false,   // tell jQuery not to set contentType
            success: function (res) {
                let respon = JSON.parse(res)
                if (respon.substr(0, 1) == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: respon.substr(2),
                        // text: $(this).attr('log'),
                    })
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: respon.substr(2),
                        // text: $(this).attr('log'),
                    })
                    $("#form-anulacion-factura").trigger("reset")
                }
                $("#modalAnulacion").modal("hide")
            }
        })
        /*}else{
            Swal.fire({
                icon: 'success',
                title: validaCamposVacios(),
                // text: $(this).attr('log'),
            })
        }*/
    })

    function validaCamposVacios() {
        let result = ''
        if ($("#secuencia").val() != '') {
            return result = 'Debe ingresar secuencia'
        } else if ($("#estabemision").val() != '') {
            return result = 'Debe seleccionar punto de emisión'
        } else if ($("#motivo").val() != '') {
            return result = 'Debe ingresar motivo de anulacion'
        }

    }

    $(document).on('change', '#secuencia', function () {
        if (validaNumeroFactura($(this).val()) != false) {
            Swal.fire({
                icon: 'error',
                html: "<h3>Debe ingresar solo secuencial de la factura</h3>",
                // text: $(this).attr('log'),
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $("#secuencia").focus()
                    $("#secuencia").val('')
                }
            })
        }
    })

    function validaNumeroFactura(cadena) {
        let res = false
        for (let i = 0; i <= cadena.length; i++) {
            if (res == false) {
                if (cadena[0] == 0) {
                    res = true
                }
            }
        }
        return res
    }

    /**============ FUNCION DE PRUEBA PARA LA VISUALIZACION DE DATOS =============*/
    function ld(tipodocumento, desde, hasta) {
        console.log(tipodocumento, desde, hasta)
        $.ajax({
            type: "POST",
            url: "./?action=loadDocumentosAutoriza",
            data: {"option": 1, "tipodocumento": tipodocumento, "desde": desde, "hasta": hasta},
            success: function (d) {
                console.log(d)
            }
        })
    }

    $(document).on("click", ".btn-reenvia", function (e) {
        e.preventDefault()
        let rucliente = $(this).attr('ruccliente')
        let documento = $(this).attr('documento')
        let clave = $(this).attr('clave')
        let tipo = $(this).attr('tipo')
        let id = $(this).attr('id')
        let idFile = $(this).attr('fiFile')
        $.ajax({
            url: './?action=loadDocumentosAutoriza',
            type: 'POST',
            data: {"codigo": rucliente, "option": 2},
            success: function (resCliente) {
                let cliente = JSON.parse(resCliente)
                if (cliente.correos != "undefined") {
                    $("#correo1").val(cliente.correos.mail1)
                    $("#correo2").val(cliente.correos.mail2)
                    $("#correo3").val(cliente.correos.mail3)
                    $("#ruccliente").val(rucliente)
                    $("#documento").val(documento)
                    $("#tipo").val(tipo)
                    $("#id").val(id)
                    $("#idFile").val(idFile)
                }
            }
        })

        $("#modalEnviarCorres .modal-title .factura").text("Documento # " + documento)
        $("#modalEnviarCorres #clave").val(clave)
        $("#modalEnviarCorres").modal('show')
    });

    $(document).on('click', '#reenviar-doc', function () {
        let formReenvio = $("#form-reenviar-emails").serialize()
        reenviarCorreos(formReenvio)
    })

    function reenviarCorreos(datos) {
        $.ajax({
            type: "POST",
            url: "./?action=procesarDocsAutoriza",
            data: datos,
            beforeSend: function () {
                // setting a timeout
                $("#myModalLoading").modal('toggle')
                $("#myModalLoading #texto-modal").text("Generando proceso de reenvio...")
                $("#loading").addClass('loading');
            },
            success: function (d) {
                console.log(d)
                let res = JSON.parse(d)
                if (res.substr(0, 1) == 1) {
                    $("#modalEnviarCorres").modal('hide')
                    Swal.fire({
                        icon: 'success',
                        title: res.substr(2),
                    })
                    $("#myModalLoading").modal('hide')
                } else {
                    $("#modalEnviarCorres").modal('hide')
                    Swal.fire({
                        icon: 'error',
                        title: res.substr(2),
                    })
                    $("#myModalLoading").modal('hide')
                }
            }
        })
    }

    $(document).on("click", ".btn-reautorizar", function () {
        $.ajax({
            type: "POST",
            url: "./?action=procesarDocsAutoriza",
            data: {
                "option": 2,
                "documento": $(this).attr('documento'),
                "tipo": $(this).attr('tipo'),
                "cliente": $(this).attr('ruccliente'),
                "id": $(this).attr('id')
            },
            beforeSend: function () {
                $("#myModalLoading").modal('toggle')
                $("#myModalLoading #texto-modal").text("Generando Reenvio de autorización...")
                $("#loading").addClass('loading');
            },
            success: function (d) {
                console.log(d)
                let datos = JSON.parse(d)
                if (datos.msjCreacion !== "undefined") {
                    if (datos.estadoAuto[0]['msjAuto'].substr(0, 1) == 1) {
                        Swal.fire({
                            icon: 'success',
                            title: "Autorización",
                            html: '' +
                                '<ul class="list-unstyled">' +
                                '<li>' + datos.estadoAuto[0]['msjAuto'].substr(2) + '</li>' +
                                '</ul>',
                        }).then((result) => {
                            if (datos.estadoAuto[1]['msjMail'] != null || datos.estadoAuto[1]['msjMail'] != "") {
                                Swal.fire({
                                    icon: 'success',
                                    title: "Autorización",
                                    html: '' +
                                        '<ul class="list-unstyled">' +
                                        '<li>' + datos.estadoAuto[1]['msjMail'][0].substr(2) + '</li>' +
                                        '</ul>',
                                })
                            }
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: "Autorización",
                            html: '' +
                                '<ul class="list-unstyled">' +
                                '<li>' + datos.estadoAuto[0]['msjAuto'].substr(2) + '</li>' +
                                '</ul>',
                        })
                    }
                    $("#myModalLoading").modal('hide')
                    $("#numerofact").val(datos.numFact.substr(6))
                    $("#btn-comprar").prop('disabled', true)
                }
            }
        })
    });

    $(document).on("click", ".btn-log", function () {
        Swal.fire({
            icon: 'error',
            title: 'LogAuto',
            text: $(this).attr('log'),
        })
    });

    $(document).on("click", ".btn-clave", function () {
        Swal.fire({
            title: 'Clave Acceso',
            text: $(this).attr('claveAcceso'),
        })

    });

    function loadFacturas(tipo, desde, hasta) {

        $.ajax({
            type: "POST",
            url: "./?action=loadDocumentosAutoriza",
            data: {"option": 1, "tipodocumento": tipo, "desde": desde, "hasta": hasta},
            success: function (d) {
                console.log(d)
            }
        })

        $("#table-documentoAutoriza").DataTable({
            "destroy": true,
            "ajax": {
                "method": "POST",
                "url": "./?action=loadDocumentosAutoriza",
                "data": {"option": 1, "tipodocumento": tipo, "desde": desde, "hasta": hasta}
            },

            "columns": [
                {"data": "num"},
                {"data": "documento"},
                {"data": "cliente"},
                {"data": "fecha"},
                {"data": "total"},
                {"data": "estado"},
                {"data": "clave"},
                {"data": "log"},
                {"data": "accion"},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            },
        })
    }

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    /* ========================================================
    *   VALIDA EL EL TIPO DE DOCUMENTO Y EL PUNTO DE EMSION EN LA VENTANA DE REVERTIR ANULACION*/
    const tipoDocumento = $("#tipodoc").val()


    $(document).on('click', '#btn-revrt-anulacion', function () {
        $("#modalRevertir").modal('toggle')
    })

    $(document).on('change', '#tipodoc', function () {
        loadEmision($(this).val())
    })

    loadEmision(tipoDocumento)

    function loadEmision(tipoDocumento) {
        $.ajax({
            type: "POST",
            url: "./?action=loadDocumentosAutoriza",
            data: {'codigo': tipoDocumento, 'option': 3},
            success: function (d) {
                let res = JSON.parse(d)
                let option = ''
                if (res != '') {

                    $("#estabemision").prop('disabled', false)
                    $("#secuencia").prop('disabled', false)
                    $("#motivo").prop('disabled', false)
                    $.each(res, function (i, item) {
                        option += '<option value="' + item.id + '">' + item.name + '</option>'
                    })
                    $(".puntoEmision").html(option)
                } else {
                    $("#estabemision").prop('disabled', true)
                    $("#secuencia").prop('disabled', true)
                    $("#motivo").prop('disabled', true)
                }
            }
        })
    }

    /*============================================================================================*/


    $(document).on('click', '#revertir-doc', function () {
        let datos = $("#form-revertir").serialize()
        $.ajax({
            url: './?action=loadDocumentosAutoriza',
            type: 'POST',
            data: datos,
            success: function (res) {
                let respon = JSON.parse(res)
                if (respon.substr(0, 1) == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: respon.substr(2),
                        // text: $(this).attr('log'),
                    })
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: respon.substr(2),
                        // text: $(this).attr('log'),
                    })
                    $("#form-revertir").trigger("reset")

                }
                $("#modalRevertir").modal("toggle")

            }
        })
    })

}
