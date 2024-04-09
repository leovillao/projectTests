if (document.getElementById('viewEndades')) {
    $(document).ready(function () {

        let table = $("#table-entidades").DataTable({
            "ajax": {
                "method": "POST",
                "url": "index.php?action=loadentidad",
                "data": {"tipo": 1}
            },
            "columns": [
                {"data": "id"},
                {"data": "banco"},
                {"data": "numero"},
                {"data": "tipo"},
                // {"data": "formato"},
                {"data": "estado"},
                {"defaultContent": "<div class=\"btn-group \" role=\"group\"><button class='btn btn-xs btn-success btn-edit-entidad' title='Editar' role=\"group\"><i class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></i></button><button class='btn btn-xs btn-danger btn-eliminar-entidad' title='Eliminar' role=\"group\"><i class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></i></button></div>"},
            ],
            "language": {
                "sProcessing":     "",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "searchPlaceholder": "Escribe aquí para buscar..",
                "sUrl":            "",
                "sInfoThousands":  ",",
                // "sLoadingRecords": "<img style='display: block;width:100px;margin:0 auto;' src='assets/img/loading.gif' />",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            }

        });

        function load() {
            $.ajax({
                type: "POST",
                url: "index.php?action=loadentidad",
                data: {"tipo": 1},
                success: function (e) {
                    console.log(e)
                }
            })
        }

        // load();

        $(document).on('change', '#tipoCuenta', function () {
            let id = $(this).val()
            switch (Number(id)) {
                case 1:
                    $("#entidad").text('Banco')
                    $("#numeroCuenta").text('Numero de Cuenta')
                    $("#tipocuenta").attr('disabled', false)
                    $("#cuenta").attr('disabled', false)
                    $("#formatos").attr('disabled', false)

                    break;
                case 2:
                    $("#entidad").text('Tarjeta')
                    $("#numeroCuenta").text('Numero de Tarjeta')
                    $("#tipocuenta").attr('disabled', true)
                    $("#cuenta").attr('disabled', false)
                    $("#formatos").attr('disabled', true)

                    break;
                default:
                    $("#entidad").text('Caja')
                    $("#tipocuenta").attr('disabled', true)
                    $("#cuenta").attr('disabled', true)
                    $("#formatos").attr('disabled', true)

                    break;
            }
        })
        $(document).on('change', '#procedencia', function () {
            let idp = $(this).val()
            switch (idp) {
                case 'T':
                    $("#terceros").attr('disabled', false)
                    break;
                default:
                    $("#terceros").attr('disabled', true)
                    break;

            }

        })

        $('#grabaEntidad').on('click', function (e) { // Guarda el nuevo proveeedor desde el formulario nuevo
            e.preventDefault()
            $.ajax({
                url: '?action=addentidades',
                type: 'POST',
                data: $("#form-entidad").serialize(), // Enviar formulario con archivo por medio de ajax
                success: function (e) {
                    // console.log(e)
                    let data = JSON.parse(e)
                    if (Number(data.tipo) === 1) {
                        Swal.fire({
                            "icon": 'success',
                            "title": data.mjs
                        })
                        $('#form-entidad').trigger("reset"); // reseta el formulario para nuevamente cargar documentos
                        $("#modalEntidades").modal('hide')
                    } else {
                        Swal.fire({
                            "icon": 'error',
                            "title": data.mjs
                        })
                    }
                    table.ajax.reload(null, false);
                },
            });
        });

        $('#actualizaEntidad').on('click', function (e) { // Guarda el nuevo proveeedor desde el formulario nuevo
            e.preventDefault()
            $.ajax({
                url: '?action=addentidades',
                type: 'POST',
                data: $("#form-entidad").serialize(), // Enviar formulario con archivo por medio de ajax
                success: function (e) {
                    // console.log(e)
                    let data = JSON.parse(e)
                    if (Number(data.tipo) === 1) {
                        Swal.fire({
                            "icon": 'success',
                            "title": data.mjs
                        })
                        $('#form-entidad').trigger("reset"); // reseta el formulario para nuevamente cargar documentos
                        $("#modalEntidades").modal('hide')
                    } else {
                        Swal.fire({
                            "icon": 'error',
                            "title": data.mjs
                        })
                    }
                    table.ajax.reload(null, false);
                },
            });
        });

        $(document).on('click', '.btn-edit-entidad', function () {
            let id = $(this).closest('tr').find('td:eq(0)').text()
            let banco = $(this).closest('tr').find('td:eq(1)').text()
            let tipo = 2
            $.ajax({
                url: '?action=loadentidad',
                type: 'POST',
                data: {id: id, tipo: tipo},
                success: function (res) {
                    // console.log(res)
                    let data = JSON.parse(res)
                    $("#tipoCuenta").val(data.tipo).trigger('change')
                    $("#banco").val(data.nombre)
                    $("#tipoprocss").val(2)
                    $("#id").val(data.id)
                    $("#cuenta").val(data.cuenta)
                    $("#cuenta").val(data.cuenta)
                    $("#tipocuenta").val(data.clase).trigger('change')
                    $("#procedencia").val(data.procedencia).trigger('change')
                    if (data.tercero !== null || data.tercero !== 0) {
                        $("#terceros").val(data.tercero).trigger('change')
                    } else {
                        $("#terceros").val(0).trigger('change')
                    }
                    $("#formatos").val(data.formato).trigger('change')
                    if (data.numeracion === 'S') {
                        $("#numeracion").attr('checked', true)
                    } else {
                        $("#numeracion").attr('checked', true)
                    }
                    if (data.estado === 1) {
                        $("#estado").attr('checked', true)
                    } else {
                        $("#estado").attr('checked', false)
                    }
                    if (Number(data.tipo) === 1) {
                        $("#formatoCheque").css('display', 'block')
                    } else {
                        $("#formatoCheque").css('display', 'none')
                    }
                }
            })

            $("#modalEntidades").modal('toggle')
            $("#modalEntidades #actualizaEntidad").css('display', 'block')
            $("#modalEntidades #grabaEntidad").css('display', 'none')
            $("#modalEntidades .modal-title").text(banco)

        })

        $(document).on('click', '.btn-eliminar-entidad', function () {
            let id = $(this).closest('tr').find('td:eq(0)').text()
            let tipo = 3
            $.ajax({
                url: '?action=loadentidad',
                type: 'POST',
                data: {id: id, tipo: tipo},
                success: function (res) {
                    let data = JSON.parse(res)
                    if (data.tipo === 1) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Eliminar Entidad',
                            text: "Esta entidad tiene pagos asociados , no puede ser eliminada.?",
                        })
                    } else {
                        Swal.fire({
                            title: 'ELiminar Entidad',
                            text: "Desea eliminar entidad.?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: 'green',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Confirmar...'
                        }).then((result) => {
                            if (result.value) {
                                eliminarEntidad(id)
                            }
                        })
                    }
                }
            })

        })

        function eliminarEntidad(id) {
            let tipo = 3
            $.ajax({
                url: '?action=addentidades',
                type: 'POST',
                data: {id: id, tipoprocss: tipo},
                success: function (res) {
                    // console.log(res)
                    let dato = JSON.parse(res)
                    if (Number(dato.tipo) === 1) {
                        Swal.fire({
                            "icon": 'success',
                            "title": dato.mjs
                        })
                        table.ajax.reload(null, false);
                    } else {
                        Swal.fire({
                            "icon": 'error',
                            "title": dato.mjs
                        })
                    }
                }
            })
        }


        $(document).on('click', '.btn-modal-new', function () {
            $('#form-entidad').trigger("reset"); // reseta el formulario para nuevamente cargar documentos
            $("#modalEntidades .modal-title").text('Nueva Entidad')
            $("#modalEntidades #actualizaEntidad").css('display', 'none')
            $("#modalEntidades #grabaEntidad").css('display', 'block')
            $("#modalEntidades").modal('toggle')
        })

        $('#formatoCheque').on('click', function (e) {
            let id = $("#id").val()
            e.preventDefault()
            $('#modalChq .modal-body').load('index.php?action=loadCheque&id=' + id, function () {
                $('#modalChq').modal({show: true});
            });
        });


    }) // FIN DE DOCUMENTO READY
}