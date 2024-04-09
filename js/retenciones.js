$(document).ready(function () {
    if (document.getElementById('viewRetenciones')){

        let table = $("#table-retenciones").DataTable({
            "ajax": {
                "method": "POST",
                "url": "index.php?action=loadRetencion",
                "data": {"tipo": 1}
            },
            "columns": [
                {"data": "id"},
                {"data": "nombre"},
                {"data": "tipo"},
                {"data": "porcentaje"},
                {"data": "sri"},
                {"data": "estado"},
                {"defaultContent": "<div class=\"btn-group \" role=\"group\"><button class='btn btn-xs btn-success btn-edit-retencion' title='Editar' role=\"group\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i></button><button class='btn btn-xs btn-danger btn-eliminar-retencion' title='Eliminar' role=\"group\"><i class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></i></button></div>"},
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


        $('#grabaRetencion').on('click', function (e) { // Guarda el nuevo proveeedor desde el formulario nuevo
            e.preventDefault()
            let nombre = $("#tr_nombre").val()
            if (nombre !== '') {
                $.ajax({
                    url: '?action=addretenciones',
                    type: 'POST',
                    data: $("#form-retenciones").serialize(), // Enviar formulario con archivo por medio de ajax
                    success: function (e) {
                        let data = JSON.parse(e)
                        if (Number(data.tipo) === 1) {
                            Swal.fire({
                                "icon": 'success',
                                "title": data.mjs
                            })
                            $('#form-Retenciones').trigger("reset"); // reseta el formulario para nuevamente cargar documentos
                            $("#modalRetenciones").modal('hide')
                        } else {
                            Swal.fire({
                                "icon": 'error',
                                "title": data.mjs
                            })
                        }
                        table.ajax.reload(null, false);
                    },
                });
            }else{
                Swal.fire({
                    "icon": 'error',
                    "title": 'Deber registrar nombre'
                })
            }
        });

        $('#actualizaRetencion').on('click', function (e) { // Guarda el nuevo proveeedor desde el formulario nuevo
            e.preventDefault()
            $.ajax({
                url: '?action=addretenciones',
                type: 'POST',
                data: $("#form-retenciones").serialize(), // Enviar formulario con archivo por medio de ajax
                success: function (e) {
                    // console.log(e)
                    let data = JSON.parse(e)
                    if (Number(data.tipo) === 1) {
                        Swal.fire({
                            "icon": 'success',
                            "title": data.mjs
                        })
                        $('#form-retenciones').trigger("reset"); // reseta el formulario para nuevamente cargar documentos
                        $("#modalRetenciones").modal('hide')
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

        $(document).on('click', '.btn-edit-retencion', function () {
            let id = $(this).closest('tr').find('td:eq(0)').text()
            let tr_nombre = $(this).closest('tr').find('td:eq(1)').text()
            let tipo = 2
            $.ajax({
                url:'?action=loadRetencion',
                type:'POST',
                data:{id:id,tipo:tipo},
                success:function (res) {
                    console.log(res)
                    let data = JSON.parse(res)
                    $("#tr_tipo").val(data.tipo).trigger('change')
                    $("#tr_nombre").val(data.nombre)
                    $("#tipoprocss").val(2)
                    $("#tr_id").val(data.id)
                    $("#tr_codsri").val(data.sri)
                    $("#tr_porcen").val(data.porcentaje)

                    if (Number(data.estado) === 1){
                        $("#tr_activo").attr('checked', true)
                    }else{
                        $("#tr_activo").attr('checked', false)
                    }
                }
            })

            $("#modalRetenciones").modal('toggle')
            $("#modalRetenciones #actualizaRetencion").css('display','block')
            $("#modalRetenciones #grabaRetencion").css('display','none')
            $("#modalRetenciones .modal-title").text(tr_nombre)

        })

        /* Eliminar retencion*/
        $(document).on('click', '.btn-eliminar-retencion', function () {
            let id = $(this).closest('tr').find('td:eq(0)').text()
            // let tipo = 3
            $.ajax({
                url:'?action=addretenciones',
                type:'POST',
                data:{id:id,tipoprocss:3},
                success:function (res) {
                    let data = JSON.parse(res)
                    if (data.tipo === 1){
                        Swal.fire({
                            icon: 'error',
                            title: 'Eliminar Entidad',
                            text: data.mjs+".?",
                        })
                    }else{
                        Swal.fire({
                            title: 'Eliminar Entidad',
                            text: "Desea eliminar entidad.?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: 'green',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Confirmar...'
                        }).then((result) => {
                            if (result.value) {
                                eliminarTipoRet(id)
                            }
                        })
                    }
                }
            })
        })

        /* Funcion para elimianr las retenciones*/


        function eliminarTipoRet(id){
            let tipo = 4
            $.ajax({
                url:'?action=addretenciones',
                type:'POST',
                data:{id:id,tipoprocss:tipo},
                success:function (res) {
                    let dato = JSON.parse(res)
                    if (dato.mjs.substr(0,1) == 1) {
                        Swal.fire({
                            "icon": 'success',
                            "title": dato.mjs.substr(2)
                        })
                        table.ajax.reload(null,false);
                    } else {
                        Swal.fire({
                            "icon": 'error',
                            "title": dato.mjs.substr(2)
                        })
                    }
                }
            })
        }
        /*Modal nuevo tipo de transaccion*/

        $(document).on('click', '.btn-modal-retencion', function () {
            $('#form-retenciones').trigger("reset"); // reseta el formulario para nuevamente cargar documentos
            $("#modalRetenciones .modal-title").text('Nuevo Tipo de Transacción')
            $("#modalRetenciones #actualizaRetencion").css('display', 'none')
            $("#modalRetenciones #grabaRetencion").css('display', 'block')
            $("#modalRetenciones").modal('toggle')
        })




    } /*Fin de validacion de la existencia del campo ViewRetenciones*/
})