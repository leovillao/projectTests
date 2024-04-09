$(document).ready(function () {
    if (document.getElementById("proveedores")) {
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
        $("#identificador").change(function () {
            var ruc = $(this).val();
            $.ajax({
                url: 'index.php?action=validaRuc',
                type: 'GET',
                data: {ruc: ruc},
                success: function (e) {
                    if (e == 0) {
                        alert('Identificacion ingresada incorrecta..!!!')
                        $("#identificador").val('');
                        $('#identificador').focus();
                    }
                }
            })
        });
        /** =======================================
         * Carga los datos del proveedor en la ventana modal para la edicion de este
         * ========================================*/
        $('#tableProveedor').on('show.bs.modal', function (event) {
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

        $("#new-proveedor").click(function () {
            console.log("proveedores")
        })

        function loadProveedoresData() {  // Funcion para cargar la lista de Proveedores
            $.ajax({
                url: 'index.php?action=loadProvee',
                success: function (e) {
                    $('#table-proveedor').html(e);
                }
            })
        }

        if (document.getElementById('table-tags-provee')) {
            loadProveedoresData(); // Carga la lista de proveedores
        }

        let tableprovee = $('#table-tags-provee').DataTable({
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
        });

        

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
                            $('#form-setiquetas').trigger('reset');
                            // $(e).insertAfter('#msj').delay(2000).fadeOut();
                            Swal.fire(
                                "Subetiqueta grabada con exito..!!!",
                            )
                            loadDataSubEtiquetas();
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
    }
})