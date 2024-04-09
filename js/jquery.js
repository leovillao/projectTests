$(document).ready(function () {
    $("#tDocumento").change(function () {
        var tipo = $(this).val();
        if (tipo == 1) {
            $("#iva").attr("readonly", "readonly");
            $("#total").attr("readonly", "readonly");
            $("#neto").attr("readonly", "readonly");
            $("#subtotal").attr("readonly", "readonly");
        }
    })

    $("#grabado").change(function () {
        var grabado = $(this).val();
        if (!isNaN(grabado)) {
            subtotal();
        } else {
            alert("solo numeros");
            $("#grabado").val('');
            $("#grabado").focus();
        }
    });

    $("#subtotal").change(function () {
        var subt = $(this).val();
        if (!isNaN(subt)) {
            subtotal();
        } else {
            alert("solo numeros");
            $("#grabado").val('');
            $("#grabado").focus();
        }
    });

// grabado.match(/^[0-9]/)
    $("#exento").change(function () {
        var exento = $(this).val();
        if (!isNaN(exento)) {
            subtotal();
        } else {
            alert("Ingrese solo numeros..!!! ");
            $("#exento").val('');
            $("#exento").focus();
        }
    });

    $("#otros").change(function () {
        var otros = $(this).val();
        if (!isNaN(otros)) {
            subtotal();
        } else {
            alert("Ingrese solo numeros..!!! ");
            $("#otros").val('');
            $("#otros").focus();
        }

    });

    function subtotal() {
        let grabado = Number($("#grabado").val());
        let excento = Number($("#exento").val());
        let suma = Number(grabado) + Number(excento);
        let iva = Number(grabado * 0.12).toFixed(2);
        let subtotal = Number(suma) + Number(iva);
        let otros = Number($("#otros").val());
        let neto = subtotal + otros;

        if (grabado == '' && excento == '') {
            iva = ''
            suma = Number($("#subtotal").val())
            neto = suma
            subtotal = suma
        }

        $("#subtotal").val(suma);
        $("#iva").val(Number(iva).toFixed(2));
        $("#total").val(subtotal);
        $("#neto").val(neto);
    };
    /*===================================================================
              Funcion para enviar Documentos Preimpresos
    ===================================================================*/
    $('#guardaDocs').click(function (e) { // Graba los datos de los documentos Preimpresos
        e.preventDefault();
        $.ajax({ // Ajax
            url: './?action=addfiles',
            type: 'POST',
            data: $('#form-preimpreso').serialize(),
            success: (function (e) {
                let res = JSON.parse(e)
                if (res.substr(0, 1) == 1) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: res.substr(2),
                        // showConfirmButton: false,
                        timer: 1500
                    })
                    $('#form-preimpreso').trigger('reset');
                    $('#form-retenciones').load(' #form-retenciones');
                } else {
                    Swal.fire({
                        // position: 'top-end',
                        icon: 'error',
                        title: res.substr(2),
                        // showConfirmButton: false,
                        timer: 1500
                    })
                }
            })
        }) // Fin Ajax
    });
    /*===================================================================
                Funcion para enviar Retenciones Emitidas
    ===================================================================*/
    $('#guardaDocs-ret').click(function (e) { // Graba los datos de los documentos Preimpresos
        e.preventDefault();
        $.ajax({ // Ajax
            url: 'index.php?action=addfiles',
            type: 'POST',
            data: $('#form-preimpreso-ret').serialize(),
            success: (function (e) {
                $('#form-preimpreso-ret').trigger('reset');
                $('#form-preimpreso-ret').load(' #form-preimpreso-ret');
                // $(e).insertAfter('#respdocs-ret').delay(2000).fadeOut();
                console.log(e)
            })
        }) // Fin Ajax
    });

    /* ====================================================
                 Mantenimiento de Etiquetas
     ===================================================== */

    $('.del-setiqueta').on('click', function () {
        var id = $(this).val();
        $('.modal-body').load('?action=delsEtiqueta&id=' + id, function () {
            $('#delModalSetiqueta').modal({show: true});
        });
    });

    $('#eliminar-setiqueta').click(function (e) { // todo revisar esta opcion
        e.preventDefault();
        $.ajax({
            url: 'index.php?action=eliminarsetiq',
            type: 'POST',
            data: $('#form-del-setiqueta').serialize(),
            success: function (e) {
                $('#table-setiquetas').load(' #table-setiquetas');
            }
        })
    });

// Se muestra las ventana modal para la edicion y eliminacion de Centro de Costo

    $('.edit-costo').on('click', function () {
        var id = $(this).val();
        $('.modal-body').load('?action=editCosto&id=' + id, function () {
            $('#editModalCosto').modal({show: true});
        });
    });

    $('.del-costo').on('click', function () {
        var id = $(this).val();
        $('.modal-body').load('?action=delCosto&id=' + id, function () {
            $('#delModalCosto').modal({show: true});
        });
    });

    $('#eliminar-costo').click(function (e) {
        e.preventDefault();
        $.ajax({
            url: 'index.php?action=delCatalogo',
            type: 'POST',
            data: $('#form-del-costo').serialize(),
            success: function (e) {
                console.log(e);
                $('#table-costos').load(' #table-costos');
            }
        })
    });
// Edicion y creacion de Proveedores

    $('#ruc-provee').on('keydown', function () {
        var id = $(this).val();
        let identificacion = $("#identificacion").val
        if (id != '') {
            $.ajax({
                url: 'index.php?action=searchProvee',
                type: 'POST',
                data: {'id': id},
                success: function (e) {
                    console.log(e);
                    $('#result').html(e)
                }
            })
        }
    });

// Validacion del ruc ingresado
//     $("#identificador").change(function () {
//         var ruc = $(this).val();
//         $.ajax({
//             url: 'index.php?action=validaRuc',
//             type: 'GET',
//             data: {ruc: ruc},
//             success: function (e) {
//                 if (e == 0) {
//                     alert('Identificacion ingresada incorrecta..!!!')
//                     $("#identificador").val('');
//                     $('#identificador').focus();
//                 }
//             }
//         })
//     });
    $("#identificador").change(function () {
        var ruc = $(this).val();
        $.ajax({
            url: './?action=ingresos_getProveedores',
            type: 'POST',
            data: {ruc: ruc},
            success: function (e) {
                let r = JSON.parse(e)
                if (r != "") {
                    $("#razon").val(r.nombre)
                    $("#namecom").val(r.comercial)
                }
            }
        })
    });


    $('#tableProveedor').on('show.bs.modal', function (event) { // Carga los datos del proveedor en la ventana modal para la edicion de este
        var button = $(event.relatedTarget);
        var tipo = button.data('tipo');
        var ruc = button.data('ruc');
        var especial = button.data('especial');
        var name = button.data('name');
        var razon = button.data('razon');
        var comercial = button.data('comercial');
        var ciudad = button.data('ciudad');
        var direccion = button.data('direccion');
        var id = button.data('id');
        var pago = button.data('pago');
        var legal = button.data('legal');
        var forma = button.data('forma');
        var state = button.data('state');
        var mail1 = button.data('mail1');
        var mail2 = button.data('mail2');
        var mail3 = button.data('mail3');

        var modal = $(this);
        modal.find('.modal-title').text(name);
        modal.find('.modal-body #ruc').val(ruc);
        modal.find('.modal-body #name').val(name);
        modal.find('.modal-body #razon').val(razon);
        modal.find('.modal-body #comercial').val(comercial);
        modal.find('.modal-body #ciudad').val(ciudad);
        modal.find('.modal-body #direccion').val(direccion);
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #email1').val(mail1);
        modal.find('.modal-body #email2').val(mail2);
        modal.find('.modal-body #email3').val(mail3);
        modal.find('select#pago').val(pago).trigger('change');
        modal.find('select#identificacion').val(tipo).trigger('change');
        modal.find('select#forma').val(forma).trigger('change');
        modal.find('select#legal').val(legal).trigger('change');
        if (Number(state) == 1) {
            modal.find('.modal-body #state').attr('checked', true);
        }
        if (Number(especial) == 1) {
            modal.find('.modal-body #especial').attr('checked', true);
        }
        if (Number(state) == 0) {
            modal.find('.modal-body #state').attr('checked', false);
        }
    });

    $(".identificacion").change(function () {
        let tipo = $(this).val()
        if (tipo == 4 || tipo == 5) {
            $(".especial").attr('disabled', false)
        } else {
            $(".especial").attr('disabled', true)
        }
    })


    function loadProveedoresData() {  // Funcion para cargar la lista de Proveedores
        $.ajax({
            url: 'index.php?action=loadProvee',
            success: function (e) {
                $('#table-tags-provee').DataTable().clear().destroy()
                $('#table-proveedor').html(e);
                $('#table-tags-provee').DataTable({
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
                    // destroy: true,
                });
            }
        })
    }

    $(document).on('click', '#new-proveedor', function () {
        $("#modalNewProveedor").modal('show')
    })

    // loadProveedoresData(); // Carga la lista de proveedores

    $(document).on('click', '#actualizar-provee', function () { // Actualiza los proveedores
        $.ajax({
            url: 'index.php?action=updateprovee',
            type: 'POST',
            data: $('#actualizar-provee-form').serialize(),
            success: function (event) {
                loadProveedoresData();
                if (Number(event) == 1) {
                    Swal.fire({
                        'icon': 'success',
                        'title': 'Proveedor actualizado correctamente.'
                    })
                } else {
                    Swal.fire({
                        'icon': 'erro',
                        'title': 'Fallo al actualizar datos del proveedor.'
                    })
                }
                // $(event).insertAfter('#last-msj').delay(2500).fadeOut();
            }
        })
    })

    $(document).on('click', '#grabarprove', function (e) { // Guarda el nuevo proveeedor desde el formulario nuevo
        e.preventDefault()
        $.ajax({
            url: 'index.php?action=addprovee',
            type: 'POST',
            data: $("#form-provee").serialize(), // Enviar formulario con archivo por medio de ajax
            success: function (e) {
                let res = JSON.parse(e)
                if (res.substr(0, 1) == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: res.substr(2),
                    })
                    loadProveedoresData()
                    $("#modalNewProveedor").modal('hide')
                    $("#form-provee").trigger('reset')
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: res.substr(2),
                    })
                }
            },
        });
    });

    $("#ruc-provee").change(function () { // Valida el numero de ruc ingresado
        var ruc = $(this).val();
        let tp = $("#identificacion").val()
        if (tp == 04 || tp == 05) {
            $.ajax({
                url: 'index.php?action=validaRuc',
                type: 'GET',
                data: {ruc: ruc},
                success: function (e) {
                    if (e == 0) {
                        alert('Identificacion ingresada incorrecta..!!!')
                        $("#ruc-provee").val('');
                        $('#ruc-provee').focus();
                    }
                }
            })
        }
    });

    function loadDataSubEtiquetas() { // Listar las Subetiquetas del sistema
        $.ajax({
            url: 'index.php?action=loadSubetiquetas',
            type: 'GET',
            success: function (data) {
                $('#table-data-setiquetas').html(data);
            }
        });
    }

    loadDataSubEtiquetas(); // Ejecuta listar las Subetiquetas en la vista

    function loadDataEtiquetas() { // Listar las Etiquetas del Sistema
        $.ajax({
            url: 'index.php?action=loadEtiquetas',
            type: 'GET',
            success: function (data) {
                $('#table-data-etiquetas').html(data);
            }
        });
    };

    loadDataEtiquetas(); // Ejecuta listar de etiquetas en la tabla a mostrar

    $('#editModalSetiqueta').on('show.bs.modal', function (event) { // Carga la informacion para la edicion de las subetiquetas
        var button = $(event.relatedTarget);
        var name = button.data('name');
        var state = button.data('state');
        var etiqueta = button.data('etiqueta');
        var tipo = button.data('tipo');
        var id = button.data('id');

        var modal = $(this);
        modal.find('.modal-title').text('Modificar Subetiqueta : ' + name);
        modal.find('.modal-body #name-etiq').val(name);
        modal.find('.modal-body #id-setiq').val(id);
        if (tipo != 0) {
            tipo = tipo
        } else {
            tipo = 0
        }
        modal.find('.modal-body #tipoRte-ret').val(tipo).trigger('change');
        modal.find('.modal-body #etiqueta-etiq').val(etiqueta).trigger('change');

        if (Number(state) == 1) {
            modal.find('.modal-body #state-etiq').attr('checked', true);
        }
        if (Number(state) == 0) {
            modal.find('.modal-body #state-etiq').removeAttr('checked');
        }
    });

    $('#actualizar-setiqueta').click(function (e) { // Actualizar la Subetiqueta del sistema
        e.preventDefault()
        let tretq = $('#tipoRte-ret').val();
        if (tretq === 0) {
            Swal.fire(
                '"Seleccione tipo de retención..."',
            )
        } else {
            $.ajax({
                url: '?action=updateti',
                type: 'POST',
                data: $('#form-setiquetas-update').serialize(),
                success: function (event) {
                    if (event == 1) {
                        loadDataSubEtiquetas()
                        Swal.fire(
                            "Subetiqueta actualizada con exito...",
                        )
                    } else {
                        Swal.fire(
                            "Error al actualizar, intente nuevamente...",
                        )
                    }
                }
            });
        }
    });

    $(document).on('click', '.delSubEtiqueta', function () { // ELiminar la Subetiqueta del sistema
        var id = $(this).data('id');
        var name = $(this).data('name');
        Swal.fire({
            title: 'Eliminar',
            text: "Desea eliminar la etiqueta " + name + " ...?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'green',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Eliminarlo..!!!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: 'index.php?action=eliminarEtiq',
                    type: 'POST',
                    data: {id: id},
                    success: function (e) {
                        loadDataSubEtiquetas();
                    }
                });
                Swal.fire(
                    'Elimnado!',
                    'Registro eliminado con exito...!!! .',
                    'success'
                )
            }
        })
    });

    $('#form-setiquetas').submit(function (e) { // Guarda la nueva Subetiqueta
        e.preventDefault();
        // console.log($(this).serialize())
        var etiqueta = document.getElementById('etiqueta').value;
        var tipoRet = document.getElementById('tipoRte').value;
        if (etiqueta != 0) {
            if (tipoRet != 0) {
                $.ajax({
                    url: 'index.php?action=addsetiquetas',
                    type: 'GET',
                    data: $(this).serialize(),
                    success: function (e) {
                        // console.log(e)
                        let res = JSON.parse(e)
                        if (res.substr(0, 1) == 0) {
                            Swal.fire({
                                icon: 'error',
                                title: res.substr(2),
                            })
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: res.substr(2),
                            })
                            $('#form-setiquetas').trigger('reset');
                            loadDataSubEtiquetas();
                        }
                    }
                })
            } else {
                Swal.fire(
                    "Debe seleccionar tipo de retención..!!!",
                )
            }
        } else {
            Swal.fire(
                '"Debe seleccionar Etiqueta..!!!"',
            )
        }
    });


    /* SECCION PARA LA CREACION FUNCIONES */

    function loadDataFunciones() { // Listar las Funciones del Sistema
        $.ajax({
            url: 'index.php?action=loadDataFunciones',
            type: 'GET',
            success: function (data) {
                $('#table-data-funciones').html(data);
            }
        });
    };

    // loadDataFunciones();

    $('#form-funciones').submit(function (e) { // Guarda las funciones en el sistema
        e.preventDefault();
        $.ajax({
            url: 'index.php?action=funciones',
            type: 'POST',
            data: $(this).serialize(),
            success: function (e) {
                $('#form-funciones').trigger('reset');
                $(e).insertAfter('#msj-funciones').delay(2000).fadeOut();
                loadDataFunciones();
            }
        })
    });


    $('#modalFunciones').on('show.bs.modal', function (event) { // Carga la informacion para la edicion de las Funciones
        var button = $(event.relatedTarget);
        var name = button.data('name');
        var id = button.data('id');
        var state = button.data('state');

        var modal = $(this);
        modal.find('.modal-title').text('Modificar Funcion : ' + name);
        modal.find('.modal-body #name').val(name);
        modal.find('.modal-body #id-funcion').val(id);
        modal.find('.modal-body #id').val(id);

        if (Number(state) == 1) {
            modal.find('.modal-body #state').attr('checked', true);
        }
        if (Number(state) == 0) {
            modal.find('.modal-body #state').removeAttr('checked');
        }
    });

    $(document).on('click', '.delFuncion', function () { // ELiminar la Funciones del sistema
        var id = $(this).data('id');
        var name = $(this).data('name');
        var tipo = $(this).data('tipo');
        Swal.fire({
            title: 'Eliminar',
            text: "Desea eliminar la etiqueta " + name + " ...?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'green',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Eliminarlo..!!!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: 'index.php?action=delCatalogo',
                    type: 'POST',
                    data: {id: id, tipo: tipo},
                    success: function (e) {
                        loadDataFunciones();
                    }
                });
                Swal.fire(
                    'Elimnado!',
                    'Registro eliminado con exito...!!! .',
                    'success'
                )
            }
        })
    });

    $('#actualizar-funcion').click(function (e) { // Funcion para actualizar la funciones del sistema
        e.preventDefault();
        $.ajax({
            url: 'index.php?action=updateCatalogo',
            type: 'POST',
            data: $('#form-edit-funcion').serialize(),
            success: function (e) {
                loadDataFunciones();
            }
        })
    });

    /* SECCION PARA LA CREACION COSTOS */

    function loadDataCostos() { // Listar las Funciones del Sistema
        $.ajax({
            url: 'index.php?action=loadDataCostos',
            type: 'GET',
            success: function (data) {
                $('#table-data-costos').html(data);
            }
        });
    };

    // loadDataCostos();

    /*////////////////////////////////////////
        Ventana modal Edicion Centro de Costo
    ////////////////////////////////////////*/

    $('#editModalCosto').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var name = button.data('name');
        var id = button.data('id');
        var state = button.data('state');
        var negocio = button.data('negocio');

        var modal = $(this);
        modal.find('.modal-title').text('Modificar Centro de Costo :' + name);
        modal.find('.modal-body #name').val(name);
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #unidad-edit').val(negocio).trigger('change');

        if (state === 1) {
            modal.find('.modal-body #state').attr('checked', true);
        }
        if (state === 0) {
            modal.find('.modal-body #state').attr('checked', false);
        }
    });

    /*////////////////////////////////////////
            Actualiza Centro de Costo
    ////////////////////////////////////////*/

    $('#actualizar-costo').click(function (e) { // Funcion para actualizar la funciones del sistema
        e.preventDefault();
        var negocio = document.getElementById('unidad-edit').value;
        if (negocio != 0) {
            $.ajax({
                url: 'index.php?action=updateCatalogo',
                type: 'POST',
                data: $('#form-edit-costo').serialize(),
                success: function (e) {
                    loadDataCostos();
                    $(e).insertAfter('#msj-costo').delay(2000).fadeOut();
                }
            })
        } else {
            alert('Debe seleccionar Unidad de Negocio');
        }
    });

    /*////////////////////////////////////////
        Elimina Centro de Costo
    ////////////////////////////////////////*/

    $(document).on('click', '.delCosto', function () { // ELiminar la Subetiqueta del sistema
        var id = $(this).data('id');
        var name = $(this).data('name');
        var tipo = $(this).data('tipo');
        Swal.fire({
            title: 'Eliminar',
            text: "Desea eliminar la etiqueta " + name + " ...?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'green',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Eliminarlo..!!!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: 'index.php?action=delCatalogo',
                    type: 'POST',
                    data: {id: id, tipo: tipo},
                    success: function (e) {
                        loadDataCostos();
                    }
                });
                console.log(
                    'Elimnado!',
                    'Registro eliminado con exito...!!! .',
                    'success'
                )
            }
        })
    });

    /*////////////////////////////////////////
        Graba Centro de Costo
    ////////////////////////////////////////*/

    $(document).on('click', '#grabar-centro-costo', function (e) {
        e.preventDefault();
        var negocio = document.getElementById('unidad').value;
        if (negocio != 0) {
            $.ajax({
                url: 'index.php?action=costos',
                type: 'POST',
                data: $('#form-costos').serialize(),
                success: function (e) {
                    $('#form-costos').trigger('reset');
                    loadDataCostos();
                }
            })
        } else {
            alert("Debe seleccionar Unidad de Negocio");
        }
    });

    /*////////////////////////////////////////
        Nombre Comercial del Proveedor
    ////////////////////////////////////////*/

    $('#modalComercial').on('show.bs.modal', function (event) { // Carga la informacion para la edicion de las subetiquetas
        var button = $(event.relatedTarget);
        var comercial = button.data('comercial');
        var modal = $(this);
        modal.find('.modal-title').text(comercial);
    });

    /*////////////////////////////////////////
        Ventana modal Modifica Etiqueta
    ////////////////////////////////////////*/

    $('#exampleModalCenter').on('show.bs.modal', function (event) { // Se carga los valores para la edicion de la etiqueta  ...
        var button = $(event.relatedTarget);
        var name = button.data('name');
        var se = button.data('se');
        var id = button.data('id');
        var sc = button.data('sc');
        var su = button.data('su');
        var sf = button.data('sf');
        var tiporet = button.data('tiporet');
        var state = button.data('state');

        var modal = $(this);
        modal.find('.modal-title').text('Modificar etiqueta : ' + name);
        modal.find('.modal-body .id').val(id);
        modal.find('.modal-body .name').val(name);
        if (Number(state) == 1) {
            modal.find('.modal-body .state').attr('checked', true);
        } else {
            modal.find('.modal-body .state').removeAttr('checked');
        }
        if (Number(se) == 1) {
            modal.find('.modal-body .se').attr('checked', true);
        } else {
            modal.find('.modal-body .se').removeAttr('checked');
        }
        if (Number(sc) == 1) {
            modal.find('.modal-body .sc').attr('checked', true);
        } else {
            modal.find('.modal-body .sc').removeAttr('checked');
        }
        if (Number(su) == 1) {
            modal.find('.modal-body .su').attr('checked', true);
        } else {
            modal.find('.modal-body .su').removeAttr('checked');
        }
        if (Number(sf) == 1) {
            modal.find('.modal-body .sf').attr('checked', true);
        } else {
            modal.find('.modal-body .sf').removeAttr('checked');
        }
        modal.find(".modal-body #tipoRte").val(tiporet).trigger('change');
    });

    /*////////////////////////////////////////
            Graba etiqueta
    ////////////////////////////////////////*/

    $('#form-etiquetas').submit(function (e) {
        e.preventDefault();
        // var id = $(this).val(); // obtengo el valor del elemento al que se crea el efecto de change
        $.ajax({
            url: 'index.php?action=addetiquetas',
            type: 'GET',
            data: $(this).serialize(),
            success: function (e) {
                $('#form-etiquetas').trigger('reset');
                $(e).insertAfter('#span').delay(2500).fadeOut();
                loadDataEtiquetas();
            }
        })
    });

    /*////////////////////////////////////////
        Actualiza Etiqueta
    ////////////////////////////////////////*/

    $('#actualizar-etiqueta').click(function (e) { // Actualizar etiqueta
        e.preventDefault();
        $.ajax({
            url: 'index.php?action=updateti',
            type: 'POST',
            data: $('#form-etiquetas-update').serialize(),
            success: function (event) {
                console.log(event);
                $(event).insertAfter('#span-etiquetas').delay(2500).fadeOut();
                loadDataEtiquetas();
            }
        })
    });

    /*////////////////////////////////////////
        Elimina la Etiqueta
    ////////////////////////////////////////*/

    $(document).on('click', '.delEtiqueta', function () { // ELiminar etiqueta
        var id = $(this).data('id');
        var name = $(this).data('name');
        Swal.fire({
            title: 'Eliminar',
            text: "Desea eliminar la etiqueta " + name + " ...?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'green',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Eliminarlo..!!!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: 'index.php?action=eliminarEtiq',
                    type: 'POST',
                    data: {id: id},
                    success: function (e) {
                        loadDataEtiquetas();
                    }
                });
                console.log(
                    'Elimnado!',
                    'Registro eliminado con exito...!!! .',
                    'success'
                )
            }
        })
    });

////////////////////////////////////////
    /* Generacion de Reporte en Json      */
////////////////////////////////////////

    $(document).on('click', '#reporte-search', function (e) {
        e.preventDefault();
        var pivot = new WebDataRocks({
            container: "#wdr-component",
            toolbar: true,
            report: {
                dataSource: {
                    data: getJSONData()
                },
                formats: [{
                    name: "calories",
                    maxDecimalPlaces: 2,
                    maxSymbols: 20,
                    textAlign: "right"
                }],
                slice: {
                    rows: [{
                        uniqueName: "Food"
                    }],
                    columns: [{
                        uniqueName: "[Measures]"
                    }],
                    measures: [{
                        uniqueName: "Calories",
                        aggregation: "average",
                        format: "calories"
                    }]
                }
            }
        });

        function getJSONData() {
            var data1 = '';
            $.ajax({
                url: 'index.php?action=reports',
                success: function (data) {
                    data1 = data;
                }
            })
            return data1;
        }
    });

    $("#totales").click(function () {
        var year = $("#year").val();
        var mes = $("#mes").val();
        var option = $("#option").val();
        /*===============================
        Seleccionamos el select el cual queremes tomar el texto
        ===============================*/
        var combo = document.getElementById("mes");
        var selected = combo.options[combo.selectedIndex].text;

        if (mes == 0) {
            Swal.fire({
                icon: 'error',
                title: "Seleccione mes",
            })
        } else if (year == 0) {
            Swal.fire({
                icon: 'error',
                title: "Seleccione año",
            })
        } else if (option == 0) {
            Swal.fire({
                icon: 'error',
                title: "Seleccione ciclo",
            })
        } else {
            let ivasi = 0
            let ivano = 0
            let subtotal = 0
            let iva = 0
            let total = 0
            let otros = 0
            let neto = 0
            let rfuente = 0
            let riva = 0
            /***/
            let nivasi = 0
            let nivano = 0
            let nsubtotal = 0
            let niva = 0
            let ntotal = 0
            let notros = 0
            let nneto = 0
            let nrfuente = 0
            let nriva = 0
            $.ajax({
                url: './?action=loadSum',
                method: 'POST',
                dataType: 'json',
                data: {mes: mes, year: year, option: option},
                success: function (data) {
                    console.log(data);
                    /*=================================================
                    EL objeto recibido se lo debe de convertir y parsear con JSON.parse -->> Se recorreo como objeto la repuesta recibida
                    =================================================*/

                    let datajson = JSON.parse(JSON.stringify(data));
                    $.each(datajson.valoress, function (i, item) {
                        ivasi += parseFloat(item.ivasi)
                        ivano += parseFloat(item.ivano)
                        subtotal += parseFloat(item.subtotal)
                        iva += parseFloat(item.iva)
                        total += parseFloat(item.total)
                        otros += parseFloat(item.otros)
                        neto += parseFloat(item.neto)
                        rfuente += parseFloat(item.rfuente)
                        riva += parseFloat(item.riva)
                    })
                    $.each(datajson.valoresn, function (i, item) {
                        nivasi += parseFloat(item.ivasi)
                        nivano += parseFloat(item.ivano)
                        nsubtotal += parseFloat(item.subtotal)
                        niva += parseFloat(item.iva)
                        ntotal += parseFloat(item.total)
                        notros += parseFloat(item.otros)
                        nneto += parseFloat(item.neto)
                        nrfuente += parseFloat(item.rfuente)
                        nriva += parseFloat(item.riva)
                    })
                    //numero.toLocaleString('es', noTruncarDecimales)
                    const noTruncarDecimales = {maximumFractionDigits: 20};
                    $("#body-table").html("" +
                        "<tr><td><b></b></td><td><b>FISCALES</b></td><td><b>NO FISCALES</b></td></tr>" +
                        "<tr><td><b>Grabado</b></td><td class='alignRight'>" + ivasi.toLocaleString('en-US',noTruncarDecimales) + "</td><td class='alignRight'>" + nivasi.toLocaleString('en-US',noTruncarDecimales) + "</td></tr>" +
                        "<tr><td><b>Exento</b></td><td class='alignRight'>" + ivano.toLocaleString('en-US',noTruncarDecimales) + "</td><td class='alignRight'>" + nivano.toLocaleString('en-US',noTruncarDecimales) + "</td></tr>" +
                        "<tr><td><b>Subtotal</b></td><td class='alignRight'>" + subtotal.toLocaleString('en-US',noTruncarDecimales) + "</td><td class='alignRight'>" + nsubtotal.toLocaleString('en-US',noTruncarDecimales) + "</td></tr>" +
                        "<tr><td><b>Iva</b></td><td class='alignRight'>" + iva.toLocaleString('en-US',noTruncarDecimales) + "</td><td class='alignRight'>" + niva.toLocaleString('en-US',noTruncarDecimales) + "</td></tr>" +
                        "<tr><td><b>Total</b></td><td class='alignRight'>" + total.toLocaleString('en-US',noTruncarDecimales) + "</td><td class='alignRight'>" + ntotal.toLocaleString('en-US',noTruncarDecimales) + "</td></tr>" +
                        "<tr><td><b>Otros</b></td><td class='alignRight'>" + otros.toLocaleString('en-US',noTruncarDecimales) + "</td><td class='alignRight'>" + notros.toLocaleString('en-US',noTruncarDecimales) + "</td></tr>" +
                        "<tr><td><b>Neto</b></td><td class='alignRight'>" + neto.toLocaleString('en-US',noTruncarDecimales) + "</td><td class='alignRight'>" + nneto.toLocaleString('en-US',noTruncarDecimales) + "</td></tr>" +
                        "<tr><td><b>Total Rte. Fuente</b></td><td class='alignRight'>" + rfuente.toLocaleString('en-US',noTruncarDecimales) + "</td><td class='alignRight'>" + nrfuente.toLocaleString('en-US',noTruncarDecimales) + "</td></tr>" +
                        "<tr><td><b>Total Rte. IVA</b></td><td class='alignRight'>" + riva.toLocaleString('en-US',noTruncarDecimales) + "</td><td class='alignRight'>" + nriva.toLocaleString('en-US',noTruncarDecimales) + "</td></tr>"
                    );
                    /*=================================================
                    Se modifica el Titulo de la ventana modal , mostrando el mes y el año consultado
                    =================================================*/
                    $(".modal-title").text('Totales del mes de ' + selected + ' del ' + year);
                    $("#modalTotales").modal('show');
                }
            })
        }
    });

    /*======================================================================================
        Se cargan los tags para etiquetar los proveedores.
    * ====================================================================================*/

    $('#modalTags').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var proveedor = button.data('proveedor') // Extract info from data-* attributes
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this)
        $("#modalTags .modal-title").text(proveedor)
        // $('#modalTags').modal('toggle')
    })


    $(document).on('click', '.btn-tags', function (e) {
        e.preventDefault()
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let proveedor = $(this).closest('tr').find('td:eq(2)').text()
        let table = 'de_provee'
        let tipo = 1
        $.ajax({
            url: './?action=loadTags',
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

        $("#modalTags #idproveedorTags").val(id)
        $("#aplicar-btn").css("display", "none")
        $("#nueva-btn").css("display", "block")
        $("#cerrar-btn").css("display", "block")

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
            $("#aplicar-btn").css("display", "none")
            $("#nueva-btn").css("display", "block")
            $("#cerrar-btn").css("display", "block")
        } else {
            $("#aplicar-btn").css("display", "block")
            $("#nueva-btn").css("display", "none")
            $("#cerrar-btn").css("display", "none")
        }
    }

    /*======================================================================
    Se muestra la ventana para la creacion de la nueva etiqueta de proveedor
    * ====================================================================*/
    $(document).on('click', '#nueva-btn', function (e) {
        e.preventDefault();
        $('#modalNewTags').modal('toggle')
        $('#newTags-form').trigger('reset')
        $('#modalNewTags .modal-title').text("Nueva Etiqueta")
        let id = $("#modalTags #idproveedorTags").val()
        $('#modalNewTags #idprovNewTags').val(id)
        $('#modalNewTags #tipo').val(1)
        $('#modalNewTags #actualizarTags').css('display', 'none')
        $('#modalNewTags #save-newTags').css('display', 'block')
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
        Eliminacion de la etiqueta o quitar etiqueta de un proveedor
    * ====================================================================*/
    $(document).on('click', '.btn-tags-close', function (e) {
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let idEtq = $(this).val()
        let tipo = 3
        $.ajax({
            url: './?action=addTags',
            type: 'POST',
            data: {id: id, idEtq: idEtq, tipo: tipo},
            success: function (res) {
            }
        })
        $(this).parent().remove()
    })
    /*======================================================================
    Se guarda la nueva etiqueta y al mismo tiempo se etiqueta el proveedore con la nueva etiqueta
    * ====================================================================*/
    $(document).on('click', '#save-newTags', function (e) {
        let tagName = $("#tags-name").val()
        if (tagName.length != '') {
            let data = $("#newTags-form").serialize()
            $.ajax({
                url: './?action=addTags',
                type: 'POST',
                data: $("#newTags-form").serialize(),
                success: function (res) {
                    let cod = res.substr(0, 1);
                    let msj = res.substr(2)

                    if (cod == 1) {
                        Swal.fire({
                            "icon": 'success',
                            "title": msj
                        })
                        $("#modalNewTags").modal('hide')
                        $("#modalTags").modal('hide')
                        $("#newTags-form").trigger('reset')
                        loadProveedoresData()
                        // loadProveedoresData.reload(null,false)
                    } else if (cod == 0) {
                        Swal.fire({
                            "icon": 'error',
                            "title": msj
                        })
                    }
                }
            })
        } else {
            Swal.fire({
                "icon": 'error',
                "title": "Debe Ingresar nombre de etiqueta"
            })
        }

    })
    /*======================================================================
   Proceso para guradar o actualizar las etiquetas de un proveedor por medio del boton aplicar habiendo seleccionado o deseleccionado los cheks o
    * ====================================================================*/

    $(document).on('click', '#aplicar-btn', function (e) {
        let dataNewTags = $("#form-new-tags").serialize()
        var tags = '';
        let tipo = 2
        let id = $("#idproveedorTags").val()
        $('#form-new-tags input[type=checkbox]').each(function () {
            if (this.checked) {
                tags += $(this).val() + '-';
            }
        });
        $.ajax({
            url: './?action=addTags',
            type: 'POST',
            data: {tags: tags, tipo: tipo, id: id},
            success: function (res) {
                loadProveedoresData()
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
    /*======================================================================
         Abre la ventana modal para la administracion de las etiquetas
    * ====================================================================*/

    $(document).on('click', '#cerrar-btn', function () {
        $("#modalAdministrar").modal('toggle')
        $("#modalAdministrar .modal-title").text('Administración de etiquetas')
        loadTag()
        $("#modalAdministrar").css("overflow", "scroll")
    })

    /*======================================================================
   Funcion para mostrar las etiquetas y para realizar el proceso de administrar , editar , eliminar , mostrar , ocultar
    * ====================================================================*/

    let loadTag = function LoadTagss() {
        let tipo = 4
        let table = 'de_provee'
        $.ajax({
            url: './?action=loadTags',
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
                    table += '<td>' + item.id + '</td><td>' + item.name + '</td><td><button class=" btn-mostrar btn btn-primary btn-xs" ' + disable + ' title="Mostrar"><i class="fa fa-eye" aria-hidden="true"></i></button></td><td><button class="btn-ocultar btn btn-primary btn-xs" ' + disablen + ' title="Ocultar"><i class="fa fa-eye-slash" aria-hidden="true"></i></button></td><td><button class=" btn-editar btn btn-btn-piramide btn-xs" title="Editar"><i class="fa fa-pencil" aria-hidden="true"></i></button></td><td><button class=" btn-eliminar btn btn-danger btn-xs" title="ELiminar"><i class="fa fa-times" aria-hidden="true"></i></button></td>'
                    table += '</tr>'
                })
                $("#tableBodyTags").html(table)
            }
        })
    }

    /*======================================================================
        Ejecuta el proceso que actualiza el estado para mostrar la etiqueta
    * ====================================================================*/
    $(document).on('click', '.btn-mostrar', function () {
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let tipo = 5
        $.ajax({
            url: './?action=loadTags',
            type: 'POST',
            data: {id: id, tipo: tipo},
            success: function (e) {
                if (Number(e) == 1) {
                    loadTag()
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
    $(document).on('click', '.btn-ocultar', function () {
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let tipo = 6
        $.ajax({
            url: './?action=loadTags',
            type: 'POST',
            data: {id: id, tipo: tipo},
            success: function (e) {
                if (Number(e) == 1) {
                    loadTag()
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
      Carga la informacion de la etiqueta para el proceso de Actualizacion
    * ====================================================================*/

    $(document).on('click', '.btn-editar', function () {
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let tipo = 7 // Rescata los datos de la etiqueta para edicion
        $.ajax({
            url: './?action=loadTags',
            type: 'POST',
            data: {id: id, tipo: tipo},
            success: function (e) {
                console.log(e)
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
                // console.log(data.id)
            }
        })
        $('#modalNewTags').modal('toggle')
        $("#modalNewTags #tipo").val(4)
        $('#modalNewTags #actualizarTags').css('display', 'block')
        $('#modalNewTags #save-newTags').css('display', 'none')

    })

    /*======================================================================
      Se ejecuta al dar clic en el boton actualizar , para la edicion de la etiqueta
    * ====================================================================*/

    $(document).on('click', '#actualizarTags', function () {
        let data = $("#newTags-form").serialize()
        $.ajax({
            url: '?action=addTags',
            type: 'POST',
            data: $("#newTags-form").serialize(),
            success: function (res) {
                let cod = res.substr(0, 1);
                let msj = res.substr(2)
                if (cod == 1) {
                    Swal.fire({
                        "icon": 'success',
                        "title": msj
                    })
                    loadProveedoresData()
                    loadTag()
                    $("#modalNewTags").modal('hide')
                    $("#newTags-form").trigger('reset')
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
        ELiminar etiqueta / desEtiquetar
    * ====================================================================*/
    $(document).on('click', '.btn-eliminar', function () {
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let tipo = 8
        $.ajax({
            url: '?action=loadTags',
            type: 'POST',
            data: {id: id, tipo: tipo},
            success: function (e) {
                console.log(e)
                if (Number(e) !== 0) {
                    let tipo = 5
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
                            eliminarEtiqueta(id, tipo)
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
                            eliminarEtiqueta(id, tipo)
                        }
                    })
                }

            }
        })
    })

    function eliminarEtiqueta(id, tipo) {
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
                    loadProveedoresData()
                    loadTag()
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
    }

})
