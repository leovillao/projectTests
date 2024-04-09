import {validaTipoDocumento, validaFecha} from './funciones.js';

$(document).ready(function () {
    $("#tDocumento").change(function () { // Valida el tipo de documento preimpreso que se va a ingresar en la ventana modal
        var facturas = document.getElementById('val_facturas'); // para la visualizacion de los campos necesarios
        var tipo = document.getElementById('tDocumento').value;
        var relacionado = document.getElementById('relacionado');
        if (tipo == 7) {
            facturas.setAttribute("hidden", "hidden");
            document.getElementById("subtotal").value = "";
            document.getElementById("grabado").value = "";
            document.getElementById("exento").value = "";
            document.getElementById("iva").value = "";
            document.getElementById("total").value = "";
            document.getElementById("otros").value = "";
        } else {
            facturas.removeAttribute("hidden");
            document.getElementById("relacionado").value = "";
        }
    });

    // $("#autorizacion").change(function () { // Valida la cantidad de caracteres que se ingresan en el campo AUTORIZACION
    //     var autoLenght = document.getElementById("autorizacion").value;
    //     if (autoLenght.length != 10) {
    //         Swal.fire({
    //             "icon": 'error',
    //             "title": "Numero de autorizacion Incorrecto"
    //         })
    //         $("#autorizacion").focus();
    //         document.getElementById("autorizacion").value = "";
    //     }
    // });

    $("#ruc").change(function () { // Valida la cantidad de caracteres que se ingresan en el campo de RUC
        var autoLenght = document.getElementById("ruc").value;
        if (autoLenght.length != 13) {
            Swal.fire({
                "icon": 'error',
                "title": "Numero de Caracteres Incorrecto"
            })
            $("#ruc").focus();
            document.getElementById("ruc").value = "";
        }
    });

    $("#estab").change(function () { // Valida la cantidad de caracteres que se ingresan en el campo ESTABLECIMIENTO
        var autoLenght = document.getElementById('estab').value;
        if ((autoLenght.length != 3) || isNaN(autoLenght)) {
            Swal.fire({
                "icon": 'error',
                "title": "Numero de establecimiento incorrecto"
            })
            $("#estab").focus();
            document.getElementById("estab").value = "";
        }
    });

    $("#emision").change(function () { // Valida la cantidad de caracteres que se ingresan el campo EMISION
        var autoLenght = document.getElementById('emision').value;
        if ((autoLenght.length != 3) || isNaN(autoLenght)) {
            Swal.fire({
                "icon": 'error',
                "title": "Numero de emisión incorrecto"
            })
            $("#emision").focus();
            document.getElementById("emision").value = "";
        }
    });


    $("#fechaDoc").focusout(function () { // Valida las fechas del Documento con las Fecha del Caducidad
        // console.log($("#tDocumento").val())
        if ($("#tDocumento").val() != "ZZ") {
            var caducidad = $('#fechaCaducidad').val().replace(/-/g, '/');
            var documento = $('#fechaDoc').val().replace(/-/g, '/');
            if (caducidad < documento) {
                Swal.fire({
                    "icon": 'error',
                    "title": 'Fecha del documento no puede ser mayor a la fecha de caducidad...'
                })
                $("#fechaCaducidad").val("");
                $("#fechaDoc").val("");
                $("#fechaCaducidad").focus();
            }
        }
    });


    $('#upLoadFile').on('submit', function (event) { // Envio del archivo XML
        event.preventDefault();
        console.log("modal de Carga")
        $.ajax({
            url: 'index.php?action=validaXml',
            type: 'POST',
            data: new FormData(this), // Enviar formulario con archivo por medio de ajax
            contentType: false,
            cache: false,
            processData: false,
            success: function (res) {
                let cod = JSON.parse(res)
                if (Number(cod.tipo) == 1) {
                    if (cod.proveedor != 0) {
                        Swal.fire({
                            "icon": 'success',
                            "title": cod.mjs
                        })
                        document.getElementById('file').value = "";
                        loadEtiquetarNcr(cod.ncr, cod.factura)
                        if (cod.proveedor[0].length != 0) {
                            newProveedor(cod.proveedor[0])
                        }
                    } else {
                        Swal.fire({
                            "icon": 'success',
                            "title": cod.mjs
                        })
                        document.getElementById('file').value = "";
                        // console.log(res)
                    }
                } else if (Number(cod.tipo) == 0) {
                    Swal.fire({
                        "icon": 'error',
                        "title": cod.mjs
                    })
                }
            },
        });
    });

    function loadEtiquetarNcr(id, ncr) {
        let tipo = 3
        $.ajax({
            url: 'index.php?action=etiqDocs',
            type: 'GET',
            data: {id: id, ncr: ncr},
            success: function (e) {
                $('#modalEtiqNcr #body-etiqueta_ncr').html(e);
                $("#modalEtiqNcr").modal('toggle')
            }
        })
    }


    function newProveedor(ruc) {
        $.ajax({
            url: '?action=loadInfProv',
            type: 'POST',
            data: {ruc: ruc},
            success: function (res) {
                let data = JSON.parse(res)
                $("#tableProveedor #id-p").val(data.id)
                $("#tableProveedor #ruc-p").val(data.ruc)
                $("#tableProveedor #razon-p").val(data.razon)
                $("#tableProveedor #comercial-p").val(data.comercial)
                $("#tableProveedor #direccion-p").val(data.direccion)
                $("#tableProveedor #ciudad-p").val(data.ciudad)
                $("#tableProveedor #email1-p").val(data.mail1)
                $("#tableProveedor #email2-p").val(data.mail2)
                $("#tableProveedor #email3-p").val(data.mail3)
                $("#tableProveedor #pago-p").val(data.pago).trigger('change')
                $("#tableProveedor #legal-p").val(data.legal).trigger('change')
                $("#tableProveedor").modal('toggle')
            }
        })
    }


    $('#close_modal').click(function () { // luego de cerrar la ventana RESETEA el formulario de carga de archivo
        $('#upLoadFile').trigger("reset");
    });

    function nuevaEtiq() {
        $.ajax({ //Comienzo Funcion AJAX///////
            url: 'core/crearEtiqueta.php',
            data: $('#form_new_etiq').serialize(),
            type: 'post',
            success: function (e) {
                $(e).insertAfter('#newE').delay(3000).fadeOut();
                $('#form-nueva-etiqueta').trigger("reset");
            },
        }); //fin de funcion AJAX/////////
    }


    function nuevaSubEtiq() { // Funcion para guardar la subetiqueta correspondiente
        if (document.getElementById("etiqueta")) {
            $('#modalSubEtiqueta').modal({show: true});
        } else {
            if (document.getElementById('newSubetiq').value != '') {
                var idFather = document.getElementById('id_etiq').value;
                var newfirstetiq = document.getElementById('newSubetiq').value;
                var firstetiq = 'sub';
                $.ajax({ //Comienzo Funcion AJAX///////
                    url: 'core/crearEtiqueta.php',
                    data: {newfirstetiq: newfirstetiq, idFather: idFather, firstetiq: firstetiq},
                    type: 'post',
                    success: function (e) {
                        $(e).insertAfter('#newE').delay(3000).fadeOut();
                        // $('#form').trigger("reset");
                        $('#newE').load(' #newE');
                    },
                }); //fin de funcion AJAX/////////
            } else {
                console.log("Debe ingresar nueva Subetiqueta...")
            }
        }
    }


    $("#load-data").click(function mostrar(e) {
        e.preventDefault();
        var ciclo = $('#ciclo').val();
        var year = $('#year').val();
        var mes = $('#mes').val();
        var option = $('#option').val();
        var tipodoc = $('#tipodoc').val();
        // console.log(tipodoc)
        $("#myTable").DataTable().clear().destroy()
        // loadListar()
        var table = $("#myTable").DataTable({
            "destroy": true,
            "ajax": {
                "method": "POST",
                "url": "/?action=listar ",
                "data": {"mes": mes, "year": year, "option": option, "tipodoc": tipodoc, "ciclo": ciclo}
            },
            columns: [
                {"data": "fi_id"},
                {"data": "fi_etiquetar"},
                {"data": "fi_er_ruc"},
                {"data": "fi_er_name"},
                {"data": "fi_tipo"},
                {"data": "fi_docum"},
                {"data": "fi_estado"},
                {"data": "fi_fecauto"},
                {"data": "fi_fechadoc"},
                {"data": "fi_subtotal"},
                {"data": "fi_ivasi"},
                {"data": "fi_ivano"},
                {"data": "fi_iva"},
                {"data": "fi_totaldoc"},
                {"data": "fi_otros_c"},
                {"data": "fi_neto"},
                {"data": "fi_retfte"},
                {"data": "fi_retiva"},
                {"data": "fi_docrel"}
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            },
        });
    });

    function loadListar(){
        var ciclo = document.getElementById('ciclo').value;
        var year = $('#year').find(":selected").val();
        var mes = $('#mes').find(":selected").val();
        var option = $('#option').find(":selected").val();
        var tipodoc = $('#tipodoc').find(":selected").val();
        $.ajax({
            url: './?action=listar',
            type: 'POST',
            data: {"mes": mes, "year": year, "option": option, "tipodoc": tipodoc, "ciclo": ciclo},
            success: function (response) {
                console.log(response)
            }
        })
    }

    /*======== Visualizacion de las etiquetas que se aplican al documento ===========*/
    $('#visualiza').on('click', function () {
        if (localStorage.getItem('etiquetas') != null) {
            let dataString = JSON.stringify(localStorage.getItem("etiquetas"));
            // console.log(dataString)
            $.ajax({
                // contentType: "application/json; charset=utf-8",
                type: 'POST',
                data: {dato: dataString},
                url: '?action=tablaEtiquetas',
                success: function (e) {
                    $("#table-etiquetada").html(e)
                    // console.log(e)
                }
            })
            $('#tablaEtiqueta').modal('show');
        } else {
            let id = $("#id_etiq_modal").val()
            // console.log(id)
            $.ajax({
                // contentType: "application/json; charset=utf-8",
                type: 'POST',
                data: {id: id},
                url: '?action=tablaEtiquetas',
                success: function (e) {
                    $("#table-etiquetada").html(e)
                    $('#tablaEtiqueta').modal('show');

                }
            })
        }
    });


    /*======== Visualizacion de las etiquetas aplicadas a los documentos NCR - NBD ===========*/
    $('#visualizaDocs').on('click', function () {
        let idDoc = $("#id_etiq_modal").val()
        if (localStorage.getItem('etiquetas') != null) {
            let dataString = JSON.stringify(localStorage.getItem("etiquetas"));
            $.ajax({
                // contentType: "application/json; charset=utf-8",
                type: 'POST',
                data: {dato: dataString, docs: idDoc},
                url: '?action=tablaEtiquetas',
                success: function (e) {
                    $("#table-etiquetada").html(e)
                }
            })
            $('#tablaEtiqueta').modal('show');
        } else {
            let id = $("#id_etiq_modal").val()
            $.ajax({
                // contentType: "application/json; charset=utf-8",
                type: 'POST',
                data: {id: id, docs: idDoc},
                url: '?action=tablaEtiquetas',
                success: function (e) {
                    $("#table-etiquetada").html(e)
                    $('#tablaEtiqueta').modal('show');

                }
            })
        }
    });

    /*=================================================================
    Funcion : para validar la visualizacion de la opcion de retenciones
    * ===============================================================*/

    $("#cerrarModal").click(function (e) {
        e.preventDefault()
        $("#form-preimpreso").trigger('reset')
    })
    /*===================================================
        Funcion para visualizar la ventana de Retencion
    * ==================================================*/
    $(document).on('click', '.btn-retenciones', function () {

        let id = $(this).closest('tr').find('td:eq(0)').text()
        let ruc = $(this).closest('tr').find('td:eq(2)').text()
        let razon = $(this).closest('tr').find('td:eq(3)').text()
        let factura = $(this).closest('tr').find('td:eq(5)').text()

        $("#modalRetenciones-emitidas  #identificador-ret").val(ruc).attr('readonly', true)
        $("#modalRetenciones-emitidas  #relacionado-ret").val(factura).attr('readonly', true)
        $("#modalRetenciones-emitidas  #razon-ret").val(razon)
        $("#modalRetenciones-emitidas  #idocument-ret").val(id)
        $("#modalRetenciones-emitidas  #tipoDocumento-ret").val('07')
        $("#modalRetenciones-emitidas  #tDocumento-ret").val('07').trigger('change')
        $("#modalRetenciones-emitidas  #tDocumento-ret").attr('disabled', true)
        $('#modalRetenciones-emitidas #totalIva').attr('readonly', true)
        $('#modalRetenciones-emitidas #totalFte').attr('readonly', true)
        $("#modalRetenciones-emitidas").modal('show')

    });
    /*===========================================
    Muestra la ventana para agregar las retenciones
    * =========================================*/
    $(document).on('click', '#clon', function (e) {
        e.preventDefault()
        $("#modal-retenciones").modal('show')
    })

    /*================================================================
            Funcion : Muestra al Usuario las retenciones creadas
    * ==============================================================*/
    $(document).on('click', '#agr-ret-ret', function (e) {
        e.preventDefault()
        let tRetencion = $("#tipoRetencion-ret").val()
        let nRetencion = $("#nombRetencion-ret").val()
        let base = $("#baseImponible-ret").val() // Obtenemos la base Imponible

        if (tRetencion == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Seleccione el tipo de retención',
            })
        } else if (nRetencion == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Seleccione retención',
            })
        } else if (base == '') {
            Swal.fire({
                icon: 'error',
                title: 'Ingrese base Imponible',
            })
        } else {
            let nombre = document.getElementById("nombRetencion-ret")
            let nombreTipo = document.getElementById("tipoRetencion-ret")
            let tipo = $("#tipoRetencion-ret").val() // Obtenemos el ID del tipo de Retención
            let nombreval = $("#nombRetencion-ret").val() // Obtenemos el ID del Impuesto
            var tipoRet = nombreTipo.options[nombreTipo.selectedIndex].text; // Obtenemos el nombre del Tipo de Impuesto
            var nombretext = nombre.options[nombre.selectedIndex].text; // Obtenemos el nombre del Impuesto
            let porcentaje = $("#porcentaje-ret").val() // Obtenemos el porcentaje
            let valoretenido = $("#valoretenido-ret").val() // Obtenemos el Valor retenido
            let html = '<tr><td><center><input type="text" name="tiporet[]" value="' + tipoRet + '" readonly style="width: 50px" ></center></td><td><input type="text" name="tipoRetencion[]" value="' + nombreval + '" hidden><input type="text" value="' + nombretext + '" readonly="readonly" style="width: 250px"></td><td><input type="text" name="porcentaje[]" value="' + porcentaje + '" style="width: 100px" readonly></td><td><input type="text" name="baseImponible[]" value="' + base + '" readonly style="width: 100px" ></td><td><input type="text" name="valorRetenido[]" value="' + valoretenido + '" readonly style="width: 100px" ></td><td><a class="btn btn-danger btn-sm eliminarFila"><i class="glyphicon glyphicon-trash"></i></a></td></tr>'
            let valretIva = $('#totalIva').val()
            let valretRet = $('#totalFte').val()
            let trts = totalretenciones(tipo, valoretenido, valretIva, valretRet)
            if (tipo == 2) {
                $('#totalIva').val(trts.toFixed(2))
            } else {
                $('#totalFte').val(trts.toFixed(2))
            }
            $("#rows-ret").append(html)
        }
    })

    /*===========================================
    Funcion para la suma de totales de retencion
    ===========================================*/
    function totalretenciones(tipo, valor, i, r) {
        let sumRte
        let sumIva
        let val
        let retIva
        if (tipo == 2) {
            val = Number(i) + Number(valor)
        } else {
            val = Number(r) + Number(valor)
        }
        return val
    }

    /*===========================================
    * =========================================*/

    $(document).on('click', '.btn-ret-elect', function (e) {
        let id = $(this).closest('tr').find('td:eq(0)').text();
        Swal.fire({
            title: 'Retención Electronica',
            text: "Desea crear Retención Electronica.?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'green',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirmar...'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: 'index.php?action=setRetencion',
                    type: 'POST',
                    data: {id: id},
                    success: function (repon) {
                        Swal.fire({
                            title: repon,
                        })
                    }
                });
            }
        })
    })

    /*===========================================
        Elimina una fila de la tabla
    * =========================================*/
    $(document).on('click', '.eliminarFila', function (e) {
        e.preventDefault();
        let tipo = $(this).closest('tr').find('input').eq(0).val();
        let valor = $(this).closest('tr').find('input').eq(5).val();
        if (tipo == 'IVA') {
            let iva = $('#totalIva').val()
            let nuevoIva = iva - valor
            $('#totalIva').val(nuevoIva.toFixed(2))
            $(this).closest('tr').remove()
        } else {
            let fte = $('#totalFte').val()
            let nuevoFte = fte - valor
            $('#totalFte').val(nuevoFte.toFixed(2))
            $(this).closest('tr').remove()
        }
    });

    $(".tipoRetencion-ret").change(function () {
        let tipo = 1
        let id = $(this).val()
        $.ajax({
            url: '?action=loadRetenciones',
            type: 'POST',
            data: {id: id, tipo: tipo},
            success: function (response) {
                $('.reTipo-ret').html(response)
            }
        })
    })

    $('.reTipo-ret').change(function () {
        let tipo = 2
        let id = $(this).val()
        $.ajax({
            url: '?action=loadRetenciones',
            type: 'POST',
            data: {id: id, tipo: tipo},
            success: function (response) {
                $('.porcentaje-ret').val(response)
                valorRetenido()
            }
        })
    })

    function valorRetenido() {
        let baseImponible = $('.baseImponible-ret').val()
        let porcentaje = $('.porcentaje-ret').val()
        let total = Number(baseImponible * porcentaje / 100)
        let respon = $('.valorRetenido-ret').val(Number(total).toFixed(2))
        return respon
    }

    $('.baseImponible-ret').change(function () {
        valorRetenido()
    })

    $(document).on('click', '#btnRetenciones', function (e) {
        e.preventDefault()
        console.log($('#form-preimpreso').serialize())
    });


    let validaSessionEti = function () {
        if (localStorage.getItem('etiquetas') == null) {
            $("#myModalmostrar").modal('hide')
        } else {
            // La ventana se cierra sin novedad
            Swal.fire({
                title: 'Tienes etiquetas pendientes por procesar...!',
                text: "Deseas salir ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'green',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si.'
            }).then((result) => {
                if (result.value) {
                    // function para eliminar la variable session
                    $("#myModalmostrar").modal('hide')
                    localStorage.clear()
                }
            })
        }
    }
    /*======== Function para validar salida en ventana modal para etiquetar documentos =============*/
    let validaSessionEtiDocs = function () {
        if (localStorage.getItem('etiquetas') == null) {
            $("#modalEtiqNcr").modal('hide')
        } else {
            // La ventana se cierra sin novedad
            Swal.fire({
                title: 'Tienes etiquetas pendientes por procesar...!',
                text: "Deseas salir ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'green',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si.'
            }).then((result) => {
                if (result.value) {
                    // function para eliminar la variable session
                    $("#modalEtiqNcr").modal('hide')
                    localStorage.clear()
                }
            })
        }
    }

    /*================================================================
    Graba el iva segun el tipo de retencion que tenga el asignado la subetiqueta
    ==============================================================*/

    let validaDocumento = function () { // Graba las etiquetas asignadas al DOCUMENTO
        let id = $("#id_etiq_modal").val()
        let tipoRet = 1
        let data
        $.ajax({
            url: 'index.php?action=validaTipoIva',
            type: 'POST',
            data: {id: id, tipoRet: tipoRet},
            success: function (response) {
                let datajson = JSON.parse(response)
                if (datajson.error != 2) {
                    if (datajson.tipo == 'A') {
                        $("#modalGetTipoRet #id-documento").val(id)
                        $("#modalGetTipoRet #tipoRet-Modal").val(datajson.tipo)
                        $("#modalGetTipoRet .modal-title").text('Iva del Documento : $' + datajson.valor)
                        $("#modalGetTipoRet #ivaBienes").val('').attr('readonly', false)
                        $("#modalGetTipoRet #ivaret").val(datajson.valor)
                        $("#modalGetTipoRet #ivaServicio").val('').attr('readonly', false)
                        $("#modalGetTipoRet").modal('show')
                    } else if (datajson.tipo == 'K') {
                        console.log(response)
                    }
                } else {
                    $("#myModalmostrar").modal('hide')
                }
            }
        })
    };

    /*================================================================
    Graba el iva segun el tipo de retencion que tenga el asignado la subetiqueta
    ==============================================================*/

    let validaDocumentoDocs = function () { // Graba las etiquetas asignadas al DOCUMENTO
        let id = $("#id_etiq_modal").val()
        let tipoRet = 1
        let data
        $.ajax({
            url: 'index.php?action=validaTipoIva',
            type: 'POST',
            data: {id: id, tipoRet: tipoRet},
            success: function (response) {
                datajson = JSON.parse(response)
                if (datajson.error != 2) {
                    if (datajson.tipo == 'A') {
                        $("#modalGetTipoRet #id-documento").val(id)
                        $("#modalGetTipoRet #tipoRet-Modal").val(datajson.tipo)
                        $("#modalGetTipoRet .modal-title").text('Iva del Documento : $' + datajson.valor)
                        $("#modalGetTipoRet #ivaBienes").val('').attr('readonly', false)
                        $("#modalGetTipoRet #ivaret").val(datajson.valor)
                        $("#modalGetTipoRet #ivaServicio").val('').attr('readonly', false)
                        $("#modalGetTipoRet").modal('show')
                    } else if (datajson.tipo == 'K') {
                        console.log(response)
                    }
                } else {
                    $("#modalEtiqNcr").modal('hide')
                }
            }
        })
    };

    $('#validacionIva-salir').click(function (e) { // Graba el Tipo de IVA correspondiente para cada documento segun configuracion
        e.preventDefault();
        $.when(validaSessionEti()).done(function () {
            validaDocumento()
        });
    });
    /*========= Valida la salida para la ventana de Etiquetar documentos NCR - NDB */

    $('#validacionIva-salir_docs').click(function (e) { // Graba el Tipo de IVA correspondiente para cada documento segun configuracion
        e.preventDefault();
        $.when(validaSessionEtiDocs()).done(function () {
            validaDocumentoDocs()
        });
    });

    /*=============================================================================
    Funcion para grabar el IVA del documento segun corresponda el tipo de retencion
    =============================================================================*/

    $("#grabarDetaIva").click(function () {
        let iva = $("#ivaret").val()
        let ivaServicio = $("#ivaServicio").val()
        let ivaBienes = $("#ivaBienes").val()
        let suma = Number($("#ivaBienes").val()) + Number($("#ivaServicio").val())
        if (suma == iva) {
            let tipoRet = 75
            let idDoc = $("#id-documento").val()
            let retTipo = $("#tipoRet-Modal").val()
            //let ivaServicio = $("#ivaServicio").val()
            //let ivaBienes = $("#ivaBienes").val()
            $.ajax({
                url: 'index.php?action=validaTipoIva',
                type: 'POST',
                data: {
                    idDoc: idDoc,
                    retTipo: retTipo,
                    ivaBienes: ivaBienes,
                    ivaServicio: ivaServicio,
                    tipoRet: tipoRet
                },
                success: function (e) {
                    if (Number(e) == 1) {
                        console.log("Iva Grabado Con Exito...!!!")
                        $("#modalGetTipoRet").modal('hide')
                    } else {
                        console.log("Error...!!!")
                    }
                }
            })
        } else {
            console.log("Valor ingresado debe ser igual al iva del documento")
        }

    })

    /*========================================================
        Funcion para guardar la etiqueta del documento
    ========================================================*/

    $('#grabaEtiqueta').click(function () { // Graba las etiquetas asignadas al DOCUMENTO
        let id = $("#id_etiq_modal").val()
        let etiqueta = $("#subeti").val()
        let suma = sumarValoresLocalStorage()
        console.log(suma.toFixed(2))
        let subtotal = subTDocumento()
        if (parseFloat(subtotal).toFixed(2) == suma.toFixed(2)) {
            let dataString = JSON.stringify(localStorage.getItem("etiquetas"));
            $.ajax({
                type: 'POST',
                data: {dato: dataString},
                url: '?action=savetiq',
                success: function (e) {
                    if (Number(e) == 1) {
                        localStorage.removeItem('etiquetas')
                        let array = []
                        Swal.fire({
                            icon: 'success',
                            title: "Registro guardado con exito.!"
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: "Error al registrar etiqueta...!"
                        })
                        console.log(e)
                    }
                }
            })
        } else {
            Swal.fire({
                icon: 'error',
                title: "Debe cuadrar los valores de la etiqueta para poder grabar.!",
            })
        }
    });
    /*========================================================
        Funcion para guardar la etiqueta de los documentos NCR - NDB
    ========================================================*/

    $('#grabaEtiquetaDocs').click(function () { // Graba las etiquetas asignadas al DOCUMENTO
        let id = $("#id_etiq_modal").val()
        let etiqueta = $("#subeti").val()
        let suma = sumarValoresLocalStorage()
        console.log(suma.toFixed(2))
        let subtotal = subTDocumento()
        if (parseFloat(subtotal).toFixed(2) == suma.toFixed(2)) {
            let dataString = JSON.stringify(localStorage.getItem("etiquetas"));
            $.ajax({
                type: 'POST',
                data: {dato: dataString},
                url: '?action=savetiq',
                success: function (e) {
                    if (Number(e) == 1) {
                        localStorage.removeItem('etiquetas')
                        let array = []
                        Swal.fire({
                            icon: 'success',
                            title: "Registro guardado con exito.!"
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: "Error al registrar etiqueta...!"
                        })
                        console.log(e)
                    }
                }
            })
        } else {
            Swal.fire({
                icon: 'error',
                title: "Debe cuadrar los valores de la etiqueta para poder grabar.!",
            })
        }
    });

    function subTDocumento() {
        let id = $("#id_etiq_modal").val()
        let tipoRet = 62
        let resultado
        $.ajax({
            url: "?action=verifiqTipoRet",
            type: "POST",
            async: false,
            data: {id: id, tipoRet: tipoRet},
            success: function (res) {
                resultado = res
            }
        })
        return resultado
    }

    function sumarValoresLocalStorage() {
        let datos = JSON.parse(localStorage.getItem("etiquetas"))
        let total = 0
        let t = 0
        let resultado = datos.length
        datos.forEach(function (data, index) {
            total += parseFloat(data.valorb) + parseFloat(data.valors) + parseFloat(data.valor)
        });
        return total
    }


///--------------------------------------------------------
    $(document).on('click', '.btn-etiquetar', function (e) {
        e.preventDefault();

        var id = $(this).closest('tr').find('td:eq(0)').text();
        let estado = $(this).closest('tr').find('td:eq(6)').text()
        if (estado == 'Anulado') {
            $("#grabaEtiqueta").attr('disabled', true)
            $("#aplica").attr('disabled', true)
        } else {
            $("#grabaEtiqueta").attr('disabled', false)
            $("#aplica").attr('disabled', false)
        }
        $.ajax({
            url: 'index.php?action=etiqModal',
            type: 'GET',
            data: {id: id},
            success: function (e) {
                $('#body-etiqueta').html(e);
                $("#myModalmostrar").modal('show')
            }
        })
    });


    $('#sub_etiq').change(function () {
        var se = document.getElementById('sub_etiq').value;
        if (se == 0) {
            alert('vacio');
        }
    });
//Seccion para mantemiento de los catalogos

    $("#tDocumento").change(function () { // Valida el tipo de documento preimpreso que se va a ingresar en la ventana modal
        if ($(this).val() == 7 || $(this).val() == 5 || $(this).val() == 4) {
            $("#relacionado").removeAttr('readonly');
        } else {
            $("#relacionado").attr('readonly', 'readonly');
        }
    });


    $('.btn-setRet').click(function (e) {
        e.preventDefault()
        let id = $(this).closest('tr').find('td:eq(0)').text()
        console.log(id)
        let nombre = $(this).closest('tr').find('td:eq(1)').text()
        if (nombre.length == 0) {
            nombre = $(this).closest('tr').find('td:eq(2)').text()
        }

        $('#modalsetRetenciones .modal-body').load('?action=asigEstructura&id=' + id, function () {
            $('#modalsetRetenciones').modal({show: true});
        });
        $('#modalsetRetenciones .modal-title').text(nombre)
        $('#modalsetRetenciones .modal-header').css('background', '#3c763d')
        $('#modalsetRetenciones .modal-header').css('color', 'white')

    })

    /*================== Evento para llamar a la ventana modal de PAGOEXPRESS*/
    function addZero(i) {
        if (i < 10) {
            i = '0' + i;
        }
        return i;
    }

    $(document).on('change', '.selectSet', function () {
        let id = $("#id").val();
        let campo = $(this).attr('campo')
        let valor = $(this).val();

        $.ajax({
            url: "?action=updateRetenciones",
            type: "POST",
            data: {id: id, campo: campo, valor: valor},
            success: function (e) {
                console.log(e)
                let datos = JSON.parse(e)
                if (datos.substr(0, 1) == 0) {
                    Swal.fire({
                        icon: "error",
                        title: datos.substr(2),
                    }).then((result) => {})
                } else {
                    Swal.fire({
                        icon: "success",
                        title: datos.substr(2),
                    }).then((result) => {})
                }

            }
        })
    })

    $(document).on('click', '.pagoExpress', function (e) {
        /*==============================================
        Se obtiene la fecha actual para mostrar en el input correspondiente
        * ==============================================*/
        let now = new Date();
        let day = ("0" + now.getDate()).slice(-2);
        let month = ("0" + (now.getMonth() + 1)).slice(-2);
        let today = now.getFullYear() + "-" + (month) + "-" + (day);
        e.preventDefault();
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let idproveedor = $(this).closest('tr').find('td:eq(2)').text()
        let proveedor = $(this).closest('tr').find('td:eq(3)').text()
        let tipo = $(this).closest('tr').find('td:eq(4)').text()
        let documento = $(this).closest('tr').find('td:eq(5)').text()
        let fechaDoc = $(this).closest('tr').find('td:eq(8)').text()
        let total = $(this).closest('tr').find('td:eq(13)').text()
        let otros = $(this).closest('tr').find('td:eq(14)').text()
        let neto = $(this).closest('tr').find('td:eq(15)').text()
        let suma = parseFloat(total) + parseFloat(otros)
        // console.log(suma)
        $.ajax({
            url: '?action=pagoexpress',
            type: 'POST',
            data: {id: id},
            success: function (e) {
                $('#modalPagoExpress').modal({show: true});
                $('#modalPagoExpress .modal-body').html(e);
                $('#modalPagoExpress .modal-header').css('background', '#3c763d')
                $('#modalPagoExpress .modal-header').css('color', 'white')
                $('#modalPagoExpress .modal-title').text(documento)
                $('#modalPagoExpress #totaldocumento').val(suma.toFixed(2))
                $('#modalPagoExpress #fechadoc').val(fechaDoc)
                $('#modalPagoExpress #fechareg').val(today)
                $('#modalPagoExpress #iddoc').val(id)
                $('#modalPagoExpress #idproveedor').val(idproveedor)
                $('#modalPagoExpress #glosa').val(proveedor + ' / ' + tipo + ' / ' + documento)
            }
        })
    });
    /*==========================================================
    * Valida del tipo de pago que se esta
    * =========================================================*/
    $(document).on('change', '#tipopago', function () {
        validaTipoDocumento($(this).val()) /*=================FUNCION EXPORTADA*/
    })
    /*==========================================================
    * Valida La fecha del documento y la fecha del pago
    * =========================================================*/
    $(document).on('blur', '#fechapago', function () {
        let fechapago = $("#fechapago").val()
        let fechadoc = $("#fechareg").val()
        let r = validaFecha(fechapago, fechadoc) /*=================FUNCION EXPORTADA*/

        let cod = r.substr(0, 1);
        let msj = r.substr(2)
        if (cod == 1) {
            Swal.fire({
                "icon": 'error',
                "title": 'Fecha de documento no puede ser mayor a la fecha de pago'
            })
            $("#fechapago").val(msj)
        }
    })

    /*
    $(document).on('change', '#tipopago', function (e) {
        e.preventDefault()
        let now = new Date();
        let day = ("0" + now.getDate()).slice(-2);
        let month = ("0" + (now.getMonth() + 1)).slice(-2);
        let today = now.getFullYear() + "-" + (month) + "-" + (day);
        let valor = $(this).val()
        if (Number(valor) == 1) {
            $("#fechapago").attr('disabled', false)
        } else {
            $("#fechapago").attr('disabled', true)
        }
        $.ajax({
            url: '?action=loadEntidades',
            type: 'POST',
            data: {id: valor},
            success: function (response) {
                $("#entidades").html(response)
            }
        })
        if (Number(valor) == 1) {
            $("#labelNumero").text('# de Cheque')
        } else if (Number(valor) == 2 || Number(valor) == 3) {
            $("#labelNumero").text('# de Referencia')
        } else if (Number(valor) == 4) {
            $("#labelNumero").text('# de Voucher')
        }
        let hoy = $("#fechareg").val()
        let fechadoc = $("#fechadoc").val()
        if (new Date(hoy).getTime() < new Date(fechadoc).getTime()) {
            Swal.fire({
                icon: 'error',
                title: 'Fecha de registro no puede ser menor a fecha del documento',
            })
            $('#modalPagoExpress #fechareg').val(today)
        }
    })*/

    $(document).on('click', '#grabaPago', function (e) {
        e.preventDefault()
        // console.log($("#form-pago").serialize())
        $.ajax({
            url: '?action=addPagoExpress',
            type: 'POST',
            data: $("#form-pago").serialize(),
            success: function (response) {
                console.log(response)
                let res = JSON.parse(response)
                if (Number(res.tipo) === 1) {
                    Swal.fire({
                        "icon": 'success',
                        "title": res.msj
                    })
                    viewEgreso(res.id)
                } else {
                    Swal.fire({
                        'icon':'error',
                        'title':res.msj
                    })
                }
            }
        })
    })

    function viewEgreso(id) {
        window.open('?action=egresoPdf&id=' + id, '_blank')
    }
    $(document).on('change', '#entidades', function (e) {
        e.preventDefault()
        let id = $(this).val()
        let tipo = 25
        $.ajax({
            url: '?action=loadEntidades',
            type: 'POST',
            data: {id: id, tipo: tipo},
            success: function (response) {
                if (response == 'T') {
                    $("#recargo").attr('disabled', false)
                    $("#valueTercero").val(1)
                } else {
                    $("#recargo").attr('disabled', true)
                    $("#valueTercero").val(0)
                }
            }
        })
    })

    $(document).on('click', '.btn-change-state', function () {
        let proveedor = $(this).closest('tr').find('td:eq(3)').text()
        let tipoDoc = $(this).closest('tr').find('td:eq(4)').text()
        let documento = $(this).closest('tr').find('td:eq(5)').text()
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let tipo = 1

        $.ajax({
            url: '?action=validaRetenciones',
            type: 'POST',
            data: {documento: documento, tipo: tipo, id: id},
            success: function (response) {
                let doc
                let saldo
                let saldo1 = 0
                /*===== Se define con valor predeterminado para que no muestre error si la variable no se llena en el proceso ======*/
                let docum = ''
                let abono = ''
                /*=================*/
                Swal.fire({
                    title: 'Desea anular el tipo de documento ' + tipoDoc + ' # ' + documento + ' , del Proveedor ' + proveedor,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'green',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si.'
                }).then((result1) => {
                    if (result1.value) {
                        let res = JSON.parse(response)
                        /* ============ Parsear el JSON como respuesta para almacenar la de documentos asociados o abonos========== */
                        $.each(res, function (i, item) {
                            if (item.documento) {
                                docum += item.documento + ','
                            }
                            if (item.saldo) {
                                abono = item.saldo
                            }
                        });
                        if (docum != '') {
                            Swal.fire({
                                title: 'Este documento tiene asociados : ' + docum + ' Desea que estas tambien sean anuladas',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: 'green',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Si.'
                            }).then((result) => {
                                if (result.value) {
                                    if (abono != '') {
                                        Swal.fire({
                                            title: 'Este documento tiene registado pago o abonos asociados, al anularlos estos se registraran como anticipos',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: 'green',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Si.'
                                        }).then((result) => {
                                            if (result.value) {
                                                anularDocumento(id) // Funcion para anular el documento
                                            } else {
                                                Swal.fire("Proceso cancelado",)
                                            }
                                        })
                                    } else {
                                        anularDocumento(id) // Funcion para anular el documento
                                    }
                                } else {
                                    Swal.fire("Proceso cancelado",)
                                }
                            })
                        } else {
                            /* =========== Si no tiene documentos asociados se valida la variable Abonos para verificar si el documentos tiene pagos =====*/
                            if (abono != '') {
                                Swal.fire({
                                    title: 'Este documento tiene registado pago o abonos asociados, al anularlos estos se registraran como anticipos',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: 'green',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Si.'
                                }).then((result) => {
                                    if (result.value) {
                                        anularDocumento(id) // Funcion para anular el documento
                                    } else {
                                        Swal.fire("Proceso cancelado",)
                                    }
                                })
                            } else {
                                anularDocumento(id) // Function para anular el Documentos
                            }
                        }

                    } // fin primera confirmacion
                })
            }
        })
    })

    function anularDocumento(id) {
        let tipo = 2
        $.ajax({
            url: '?action=anulaDocs',
            type: 'POST',
            data: {id: id},
            success: function (res) {
                Swal.fire({
                    'icon': 'success',
                    'title': res
                })
            }
        })
    }

    $(document).on('click', '.btn-reenviar', function (e) {
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let doc = $(this).closest('tr').find('td:eq(4)').text()
        e.preventDefault()
        $.ajax({
            url: '?action=' + doc + '-send',
            type: 'POST',
            data: {id: id},
            success: function (response) {
                console.log(response)
            }
        })
    })
    /*=======================================================
        Query para la edicion de documentos preimpresos
    * =====================================================*/
    $(document).on('click', '.btn-doc-preimpreso', function (e) {
        $("#form-preimpreso").trigger('reset')
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let doc = $(this).closest('tr').find('td:eq(4)').text()
        e.preventDefault()
        if (doc == 'Factura') {
            $.ajax({
                url: '?action=' + doc + '-preEdit',
                type: 'POST',
                data: {id: id},
                success: function (r) {
                    let res = JSON.parse(r)
                    $("#identificador").val(res.ruc)
                    $("#idocument").val(id)
                    $("#tDocumento").val(res.tipoDoc).trigger('change')
                    $("#estab").val(res.establecimiento)
                    $("#emision").val(res.emision)
                    $("#nDocumento").val(res.documento)
                    $("#autorizacion").val(res.autorizacion)
                    $("#fechaCaducidad").val(res.fechacaducidad)
                    $("#fechaDoc").val(res.fechadoc)
                    $("#relacionado").val(res.relacionado)
                    $("#razon").val(res.razon)
                    $("#namecom").val(res.comercial)
                    $("#grabado").val(res.grabado)
                    $("#exento").val(res.exento)
                    $("#subtotal").val(res.subtotal)
                    $("#iva").val(res.iva)
                    $("#total").val(res.total)
                    $("#otros").val(res.otros)
                    $("#neto").val(res.neto)
                    $("#concepto").val(res.glosa)
                    $("#guardaDocs").hide()
                    $("#actualizar-docs").show()
                    $("#miModal .modal-title").text("Actualizacion de datos del documento # " + res.documento)
                    $("#miModal").modal('show')

                }
            })
        } else if (doc == 'Retencion') {
            let documento = $(this).closest('tr').find('td:eq(18)').text()
            $.ajax({
                url: '?action=' + doc + '-preEdit',
                type: 'POST',
                data: {id: id, documento: documento},
                success: function (r) {
                    // console.log(r)
                    let res = JSON.parse(r)
                    $("#form-preimpreso-ret").trigger('reset')
                    $("#identificador-ret").val(res.ruc).attr('readonly', true)
                    $("#idocument-ret").val(id)
                    $("#tDocumento-ret").val(res.tipoDoc).trigger('change').attr('readonly', true)
                    $("#estab-ret").val(res.establecimiento)
                    $("#emision-ret").val(res.emision)
                    $("#nDocumento-ret").val(res.documento)
                    $("#autorizacion-ret").val(res.autorizacion)
                    $("#fechaCaducidad-ret").val(res.fechacaducidad)
                    $("#fechaDoc-ret").val(res.fechadoc)
                    $("#relacionado-ret").val(res.relacionado)
                    $("#razon-ret").val(res.razon)
                    $("#namecom-ret").val(res.comercial)
                    $("#concepto-ret").val(res.glosa)
                    $("#totalFte").val(res.fuente).attr('readonly', true)
                    $("#totalIva").val(res.iva).attr('readonly', true)
                    $("#idoc").val(res.idoc)

                    $("#guardaDocs-ret").hide()
                    $("#updateDocs-ret").show()
                    /*===========================================
                    Funcion para visualizar dentro de la tabla las retenciones del documento
                    * ==========================================*/
                    displayTable(res.retenciones);

                    $("#modalRetenciones-emitidas .modal-title").text("Actualizacion de datos del documento # " + res.documento)

                    $("#modalRetenciones-emitidas").modal('show')
                }
            })
        }

    })

    function displayTable(data) {
        let containerHtml = document.getElementById('rows-ret')
        let html = ''
        for (i = 0; i < data.length; i++) {
            html += '<tr><td><center><input type="text" name="tiporet[]" value="' + data[i].tiporet + '" readonly style="width: 50px" ></center></td><td><input type="text" name="tipoRetencion[]" value="' + data[i].idRet + '" hidden><input type="text" value="' + data[i].nameRet + '" readonly="readonly" style="width: 250px"></td><td><input type="text" name="porcentaje[]" value="' + data[i].porcentaje + '" style="width: 100px" readonly></td><td><input type="text" name="baseImponible[]" value="' + data[i].base + '" readonly style="width: 100px" ></td><td><input type="text" name="valorRetenido[]" value="' + data[i].valor + '" readonly style="width: 100px" ></td><td><a class="btn btn-danger btn-sm eliminarFila"><i class="glyphicon glyphicon-trash"></i></a></td></tr>'
        }
        containerHtml.insertAdjacentHTML('beforebegin', html)
    }

    $(document).on('click', '#btn-carga-documentos', function (e) {
        e.preventDefault()
        $("#form-preimpreso").trigger('reset')
        $("#guardaDocs").show()
        $("#actualizar-docs").hide()
        $("#miModal .modal-title").text("Nuevo Documento Preimpreso")
    })


    $(document).on('click', '#updateDocs-ret', function (e) {
        e.preventDefault()
        $.ajax({
            url: './?action=upDateRetFiles',
            type: 'POST',
            data: $("#form-preimpreso-ret").serialize(),
            success: function (res) {
                let cod = res.substr(0, 1); // Extraigo el primer caracter identificador del tipo de mensaje a mostrar
                let msj = res.substr(2) // Se estrae el mensaje a mostrar
                if (cod == 1) {
                    Swal.fire({
                        "icon": 'success',
                        "title": msj
                    })
                    $("#modalRetenciones-emitidas").modal('hide') // Se oculta la ventana modal
                    $("#form-preimpreso").trigger('reset') // reset al formulario
                    $("#rows-ret").load(' #rows-ret') // se recarga la tabla que muestra las retenciones del documento
                } else if (cod == 0) {
                    Swal.fire({
                        "icon": 'error',
                        "title": msj
                    })
                }
            }
        })
    })
    /*=======================================================================
                Actualiza los documentos preimpresos - FACTURAS
    * =====================================================================*/
    $(document).on('click', '#actualizar-docs', function (e) {
        e.preventDefault()
        $.ajax({
            url: '?action=upDateFiles',
            type: 'POST',
            data: $("#form-preimpreso").serialize(),
            success: function (res) {
                let cod = res.substr(0, 1);
                let msj = res.substr(2)
                if (cod == 1) {
                    Swal.fire({
                        "icon": 'success',
                        "title": msj
                    })
                    $("#miModal").modal('hide')
                    $("#form-preimpreso").trigger('reset')
                } else if (cod == 0) {
                    Swal.fire({
                        "icon": 'error',
                        "title": msj
                    })
                }
            }
        })
    })

    if (document.getElementById('tercero')) {
        loadTerceros()
    }

    function loadTerceros() {
        var tipo = 1;
        // console.log(tipodoc)
        var table = $("#table-terceros").DataTable({
            // autoWidth: false,
            "language": { // Cambio de lenguaje para el datatable
                "emptyTable": "No hay datos disponibles en la tabla.",
                "info": "Del _START_ al _END_ de _TOTAL_ ",
                "infoEmpty": "Mostrando 0 registros de un total de 0.",
                "infoFiltered": "(filtrados de un total de _MAX_ registros)",
                "infoPostFix": "(actualizados)",
                "lengthMenu": "Mostrar _MENU_ registros",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "searchPlaceholder": "Dato para buscar",
                "zeroRecords": "No se han encontrado coincidencias.",
                "paginate": {
                    "first": "Primera",
                    "last": "Última",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "aria": {
                    "sortAscending": "Ordenación ascendente",
                    "sortDescending": "Ordenación descendente"
                }
            },
            "ajax": {
                "method": "POST",
                "url": "index.php?action=loadTerceros",
                "data": {"tipo": 1}
            },
            // responsive: true,
            columns: [
                {"data": "id"},
                {"data": "identificacion"},
                {"data": "nombre", class: 'break-text'},
                {"data": "tipo"},
                {"data": "estado"},
                {
                    "render": function () {
                        return '<button type="button" class="btn-editar-tcr btn btn-success btn-xs"><i class="glyphicon glyphicon-pencil"></i></button><button type="button" class="btn-del-tcr btn btn-danger btn-xs"><i class="glyphicon glyphicon-remove"></i></button>';
                    }
                },
            ],
            destroy: true,
        });
        table.columns.adjust().draw();
    };
    /*============================================================
            Carga los datos del TERCERO para su edicion
    * ==========================================================*/
    $(document).on('click', '.btn-editar-tcr', function () {
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let tipo = 2

        $.ajax({
            url: '?action=loadTerceros',
            type: 'POST',
            data: {id: id, tipo: tipo},
            success: function (res) {
                // console.log(res)
                let cod = JSON.parse(res)
                $("#ruc").val(cod.identificador)
                $("#id").val(cod.id)
                $("#identificacion").val(cod.tipodoc).trigger('change')
                $("#legal-edit").val(cod.tipo).trigger('change')
                $("#razon").val(cod.nombre)
                $("#ciudad").val(cod.ciudad)
                $("#email1").val(cod.email)
                $("#tipo-process").val(2)
                if(cod.estado == 1){
                    $("#state").prop("checked",true)
                }else{
                    $("#state").prop("checked",false)
                }
            }
        })
        $("#tableTerceros").modal('toggle')
    })
    /*============================================================
    Realiza el proceso de edicion del tercero desde la ventana modal
    * ==========================================================*/
    $(document).on('click', '#actualizarTercero', function () {
        $.ajax({
            url: '?action=addTerceros',
            type: 'POST',
            data: $("#actualizar-terceros").serialize(),
            success: function (res) {
                let cod = res.substr(0, 1);
                let msj = res.substr(2)
                if (cod == 1) {
                    Swal.fire({
                        "icon": 'success',
                        "title": msj
                    })
                    $("#tableTerceros").modal('hide')
                    $("#actualizar-terceros").trigger('reset')
                    loadTerceros();
                } else if (cod == 0) {
                    Swal.fire({
                        "icon": 'error',
                        "title": msj
                    })
                }
            }
        })
        // $("#tableTerceros").modal('toggle')
    });

    /*============================================================
      Guarda el nuevo tercero desde el formulario desplegable
    * ==========================================================*/
    $('#form-tercero').on('submit', function (e) { // Guarda el nuevo Tercero desde el formulario nuevo
        e.preventDefault()
        $.ajax({
            url: '?action=addTerceros',
            type: 'POST',
            data: $(this).serialize(), // Enviar formulario con archivo por medio de ajax
            success: function (e) {
                // console.log(e)
                let cod = e.substr(0, 1);
                let msj = e.substr(2)
                if (cod == 1) {
                    Swal.fire({
                        "icon": 'success',
                        "title": msj
                    })
                    // $("#tableTerceros").modal('hide')
                    $('#form-tercero').trigger("reset"); // reseta el formulario para nuevamente cargar documentos
                    loadTerceros();
                } else if (cod == 0) {
                    Swal.fire({
                        "icon": 'error',
                        "title": msj
                    })
                }
            },
        });
    });
    /*======================================================================================
        Se cargan los tags para etiquetar los terceros.
    * ====================================================================================*/

    $('#modalTags_t').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var proveedor = button.data('tercero') // Extract info from data-* attributes
        var modal = $(this)
        $("#modalTags_t .modal-title").text(proveedor)
    })
    /*======================================================================================
        Muestra las etiquetas disponibles en ventana modal para el proceso de etiquetado
    * ====================================================================================*/
    $(document).on('click', '.btn-tags_t', function (e) {
        e.preventDefault()
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let proveedor = $(this).closest('tr').find('td:eq(2)').text()
        let table = 'de_terceros'
        let tipo = 9
        $.ajax({
            url: '?action=loadTags',
            type: 'POST',
            data: {id: id, table: table, tipo: tipo},
            success: function (responde) {
                let data = JSON.parse(responde)
                let i = 0
                let tags = ''
                tags += '<ul class="list-group list-group-sm">'
                $.each(data.etiquetas, function (i, item) {
                    let chk = ''
                    let nodoName = ''
                    let nodo = ''
                    if (item.nodoName != '') {
                        nodo = '<b> / ' + item.nodoName + '</b>'
                    }
                    if (Number(item.checked == 1)) {
                        chk = 'checked'
                    }
                    tags += '<div class="checkbox list-group-item "><label><input type="checkbox" name="tags-list[]" class="tags-list" value="' + item.id + '" ' + chk + '> ' + item.name + nodo + '</label></div>'
                });
                tags += '</ul>'
                $("#tags-id").html(tags)
            }
        })

        $("#modalTags_t #idproveedorTags").val(id) /*========== Carga el id del tercero para realizar el proceso de etiquetado ========*/
        $("#aplicar-btn_t").css("display", "none")
        $("#nueva-btn_t").css("display", "block")
        $("#cerrar-btn_t").css("display", "block")
    })

    /*======================================================================================
        Valido el click para cada checkbox y se ejecuta la funcion ValidaCheck
    * ====================================================================================*/
    $(document).on('click', '.tags-list', function () {
        if ($(this).prop('checked')) {
            // console.log($(this).val())
            validaCheck()
        } else {
            validaCheck()
        }
    })

    /*======================================================================================
    Validacion de el estado de todos los chekbox para visulizar los botones correspondientes
    * ====================================================================================*/
    function validaCheck() {
        if ($('input[type=checkbox]:checked').length === 0) {
            $("#aplicar-btn_t").css("display", "none")
            $("#nueva-btn_t").css("display", "block")
            $("#cerrar-btn_t").css("display", "block")
        } else {
            $("#aplicar-btn_t").css("display", "block")
            $("#nueva-btn_t").css("display", "none")
            $("#cerrar-btn_t").css("display", "none")
        }
    }


    /*======================================================================
    Se muestra la ventana para la creacion de la nueva etiqueta de proveedor
    * ====================================================================*/
    $(document).on('click', '#nueva-btn_t', function (e) {
        e.preventDefault();
        $('#modalNewTags_t').modal('toggle')
        $('#newTags-form').trigger('reset')
        $('#modalNewTags_t .modal-title').text("Nueva Etiqueta")
        let id = $("#modalTags_t #idproveedorTags").val()
        $('#modalNewTags_t #idprovNewTags').val(id)
        $('#modalNewTags_t #tipo').val(9)
        $('#modalNewTags_t #actualizarTags_t').css('display', 'none')
        $('#modalNewTags_t #save-newTags_t').css('display', 'block')
    })
    /*======================================================================
        Habilita o desabilita el select para asociar a una etiqueta padre
    * ====================================================================*/
    $(document).on('click', '#nodo', function (e) {
        if ($(this).prop('checked')) {
            $("#etiquetapadre").attr("disabled", false)
        } else {
            $("#etiquetapadre").attr("disabled", true)
        }
    })
    /*======================================================================
    Se guarda la nueva etiqueta y al mismo tiempo se etiqueta el proveedore con la nueva etiqueta
    * ====================================================================*/
    $(document).on('click', '#save-newTags_t', function (e) {
        let nameTags = $("#tags-name").val()
        if (nameTags.length == '') {
            Swal.fire({
                "icon": 'error',
                "title": "Debe registrar nombre de la etiqueta"
            })
        } else {
            let data = $("#newTags-form_t").serialize()
            $.ajax({
                url: '?action=addTags',
                type: 'POST',
                data: $("#newTags-form_t").serialize(),
                success: function (res) {
                    // console.log(res)
                    let cod = res.substr(0, 1);
                    let msj = res.substr(2)


                    if (cod == 1) {
                        Swal.fire({
                            "icon": 'success',
                            "title": msj
                        })
                        $("#modalNewTags_t").modal('hide')
                        $("#modalTags_t").modal('hide')
                        $("#newTags-form_t").trigger('reset')
                        loadTerceros()
                        loadTag_t()

                    } else if (cod == 0) {
                        Swal.fire({
                            "icon": 'error',
                            "title": msj
                        })
                    }
                }
            })
        }
    })

    /*======================================================================
         Abre la ventana modal para la administracion de las etiquetas
    * ====================================================================*/

    $(document).on('click', '.btn-administrar', function () {
        loadTag_t()
        $("#modalAdministrar_t").modal('toggle')
        $("#modalAdministrar_t .modal-title").text('Administración de etiquetas')
        // $("#modalAdministrar").css("overflow", "scroll")
    })

    /*======================================================================
   Funcion para mostrar las etiquetas y para realizar el proceso de administrar , editar , eliminar , mostrar , ocultar
    * ====================================================================*/

    let loadTag_t = function LoadTagss_t() {
        let tipo = 4
        let table = 'de_terceros'
        $.ajax({
            url: '?action=loadTags',
            type: 'POST',
            data: {tipo: tipo, table: table},
            success: function (data) {
                let inf = JSON.parse(data)
                let table = ''
                $.each(inf.etiqueta, function (i, item) {
                    let disable = ''
                    if (Number(item.estado) == 1) {
                        disable = 'disabled'
                        disablen = ''
                    } else {
                        disablen = 'disabled'
                        disable = ''
                    }
                    table += '<tr>'
                    table += '<td>' + item.id + '</td><td>' + item.name + '</td><td><button class=" btn-mostrar_t btn btn-primary btn-xs" ' + disable + ' title="Mostrar"><i class="fa fa-eye" aria-hidden="true"></i></button></td><td><button class="btn-ocultar_t btn btn-primary btn-xs" ' + disablen + ' title="Ocultar"><i class="fa fa-eye-slash" aria-hidden="true"></i></button></td><td><button class=" btn-editar_t btn btn-btn-piramide btn-xs" title="Editar"><i class="fa fa-pencil" aria-hidden="true"></i></button></td><td><button class=" btn-eliminar_t btn btn-danger btn-xs" title="ELiminar"><i class="fa fa-times" aria-hidden="true"></i></button></td>'
                    table += '</tr>'
                })
                $("#tableBodyTags_t").html(table)
            }
        })
    }
    /*======================================================================
      Carga la informacion de la etiqueta para el proceso de Actualizacion
    * ====================================================================*/

    $(document).on('click', '.btn-editar_t', function () {
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let tipo = 7 // Rescata los datos de la etiqueta para edicion
        $.ajax({
            url: '?action=loadTags',
            type: 'POST',
            data: {id: id, tipo: tipo},
            success: function (e) {
                // console.log(e)
                let data = JSON.parse(e)
                $("#idetiq").val(data.id)
                $("#tags-name").val(data.nombre)
                if (Number(data.etiqueta) == 0) {
                    $("#nodo").attr('checked', false)
                    $("#etiquetapadre").attr('disabled', true)
                } else {
                    $("#nodo").attr('checked', true)
                    $("#etiquetapadre").attr('disabled', false)
                    $("#etiquetapadre").val(data.etiqueta).trigger('change')
                }
            }
        })
        $('#modalNewTags_t').modal('toggle')
        $("#modalNewTags_t #tipo").val(4)
        $('#modalNewTags_t #actualizarTags_t').css('display', 'block')
        $('#modalNewTags_t #save-newTags_t').css('display', 'none')

    })
    /*======================================================================
      Se ejecuta al dar clic en el boton actualizar , para la edicion de la etiqueta
    * ====================================================================*/

    $(document).on('click', '#actualizarTags_t', function () {
        let data = $("#newTags-form_t").serialize()
        $.ajax({
            url: '?action=addTags',
            type: 'POST',
            data: $("#newTags-form_t").serialize(),
            success: function (res) {
                let cod = res.substr(0, 1);
                let msj = res.substr(2)
                if (cod == 1) {
                    Swal.fire({
                        "icon": 'success',
                        "title": msj
                    })
                    loadTerceros()
                    loadTag_t()
                    $("#modalNewTags_t").modal('hide')
                    $("#newTags-form_t").trigger('reset')
                } else if (cod == 0) {
                    Swal.fire({
                        "icon": 'error',
                        "title": msj
                    })
                }
            }
        })
    })


    /*======================================================================
        Ejecuta el proceso que actualiza el estado para mostrar la etiqueta
    * ====================================================================*/
    $(document).on('click', '.btn-mostrar_t', function () {
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let tipo = 5
        $.ajax({
            url: '?action=loadTags',
            type: 'POST',
            data: {id: id, tipo: tipo},
            success: function (e) {
                if (Number(e) == 1) {
                    loadTag_t()
                } else {
                    Swal.fire({
                        "icon": 'error',
                        "title": "Error al actulizar estado, intente nuevamente..."
                    })
                }
            }
        })
    })

    /*======================================================================
        Ejecuta el proceso que actualiza el estado para Ocultar la etiqueta
    * ====================================================================*/
    $(document).on('click', '.btn-ocultar_t', function () {
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let tipo = 6
        $.ajax({
            url: '?action=loadTags',
            type: 'POST',
            data: {id: id, tipo: tipo},
            success: function (e) {
                if (Number(e) == 1) {
                    loadTag_t()
                } else {
                    Swal.fire({
                        "icon": 'error',
                        "title": "Error al actulizar estado, intente nuevamente..."
                    })
                }
            }
        })
    })


    /*======================================================================
        ELiminar etiqueta / desEtiquetar
    * ====================================================================*/
    $(document).on('click', '.btn-eliminar_t', function () {
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let tipo = 8
        $.ajax({
            url: '?action=loadTags',
            type: 'POST',
            data: {id: id, tipo: tipo},
            success: function (e) {
                // console.log(e)
                if (Number(e) !== 0) {
                    let tipo = 10
                    Swal.fire({
                        title: 'Eliminar etiqueta?',
                        text: "Desea eliminar esta etiqueta y quitarla de los proveedores asociados?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Si!'
                    }).then((result) => {
                        if (result.value) {
                            eliminarEtiqueta_t(id, tipo)
                        }
                    })
                } else {
                    let tipo = 6
                    Swal.fire({
                        title: 'Desea eliminar esta etiqueta?',
                        // text: "Desea eliminar esta etiqueta y quitarla de los proveedores asociados?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Si!'
                    }).then((result) => {
                        if (result.value) {
                            eliminarEtiqueta_t(id, tipo)
                        }
                    })
                }

            }
        })
    })

    function eliminarEtiqueta_t(id, tipo) {
        $.ajax({
            url: '?action=addTags',
            type: 'POST',
            data: {tipo: tipo, id: id},
            success: function (res) {
                let cod = res.substr(0, 1);
                let msj = res.substr(2)
                if (cod == 1) {
                    Swal.fire({
                        "icon": 'success',
                        "title": msj
                    })
                    loadTerceros()
                    loadTag_t()
                    $("#modalTags_t").modal('hide')
                    // $("#newTags-form").trigger('reset')
                } else if (cod == 0) {
                    Swal.fire({
                        "icon": 'error',
                        "title": msj
                    })
                }
            }
        })
    }

    /*======================================================================
        Eliminacion de la etiqueta o quitar etiqueta de un proveedor
    * ====================================================================*/
    $(document).on('click', '.btn-tags-close_t', function (e) {
        /*Tomo el id de la fila -- En mi caso , coloco el id en la primera columna asi puedo recuperlo mas rapido */
        let id = $(this).closest('tr').find('td:eq(0)').text() /* tomo el dato de la primera columna -- que es el id del dato a borrar*/
        let idEtq = $(this).val()
        let tipo = 7
        /*Envio el id de la fila al action para que realize el proceso de eliminacion*/
        $.ajax({
            url: '?action=addTags',
            type: 'POST',
            data: {id: id, idEtq: idEtq, tipo: tipo},
            success: function (res) {
                // console.log(res)
            }
        })
        $(this).parent().remove() /* Elimino el elmento de la vista o del html para completar el proceso*/
    })
    /*======================================================================
   Proceso para guradar o actualizar las etiquetas de un proveedor por medio del boton aplicar habiendo seleccionado o deseleccionado los cheks o
    * ====================================================================*/

    $(document).on('click', '#aplicar-btn_t', function (e) {
        let dataNewTags = $("#form-new-tags").serialize()
        var tags = '';
        let tipo = 8
        let id = $("#idproveedorTags").val()
        $('#form-new-tags input[type=checkbox]').each(function () {
            if (this.checked) {
                tags += $(this).val() + '-';
            }
        });
        $.ajax({
            url: '?action=addTags',
            type: 'POST',
            data: {tags: tags, tipo: tipo, id: id},
            success: function (res) {
                loadTerceros()
                let cod = res.substr(0, 1);
                let msj = res.substr(2)
                if (cod == 1) {
                    Swal.fire({
                        "icon": 'success',
                        "title": msj
                    })
                    $("#modalTags").modal('hide')
                    // $("#newTags-form").trigger('reset')
                } else if (cod == 0) {
                    Swal.fire({
                        "icon": 'error',
                        "title": msj
                    })
                }
            }
        })
    })

    /*
    * */

    /*$(document).on('click', '.btn-retenciones-cero', function (e) {
        let id = $(this).closest('tr').find('td:eq(0)').text();
        Swal.fire({
            title: 'Retención Electronica',
            text: "Desea crear retencion electronica en 0 , COD 332.?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'green',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirmar...'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: 'index.php?action=setRetCero',
                    type: 'POST',
                    data: {id: id},
                    success: function (repon) {
                        Swal.fire({
                            title: repon,
                        })
                    }
                });
            }
        })
    }) */


}) // document READY
