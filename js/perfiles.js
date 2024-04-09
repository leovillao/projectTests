if (document.getElementById('perfiles')) {
    $(document).ready(function () {
        var tableProTot = $("#table-perfiles").DataTable({
            "destroy": true,
            "ajax": {
                "method": "POST",
                "url": "index.php?action=loadPerfiles",
                "data": {"tipo": 1}
            },

            "columns": [
                {"data": "id"},
                {"data": "nombre"},
                {"data": "estado"},
                {"defaultContent": "<button class='btn btn-xs btn-success btn-edit-perf'><i class=\"fa fa-edit\" aria-hidden=\"true\"></i></button><button class='btn-deleted-perf btn btn-danger btn-xs'><i class=\"fa fa-remove\" aria-hidden=\"true\"></i></button>"},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            },
        });

        /* Creacion de perfiles */

        $(document).on("click", "#btn-perfil", function () {
            $("#form-perfiles").trigger('reset')
            $("#modalPerfiles").modal("toggle")
            $("#modalPerfiles .modal-title").text("Nuevo Perfil")
            $("#modalPerfiles #process").text("Grabar")
        })

        /* Edicion de perfiles */

        $(document).on("click", ".btn-edit-perf", function (e) {
            e.preventDefault()
            let id = $(this).closest('tr').find('td:eq(0)').text()
            let perfil = $(this).closest('tr').find('td:eq(1)').text()
            let estado = $(this).closest('tr').find('input[type="hidden"]').val()
            // let estado = $(this).closest('tr').find('td:eq(2)').text()
            $("#modalPerfiles").modal("toggle")
            $("#modalPerfiles .modal-title").text(perfil)
            $("#modalPerfiles #process").text("Actualizar")
            $("#modalPerfiles #nameperfil").val(perfil)
            $("#modalPerfiles #id").val(id)
            $("#modalPerfiles #tipo").val(3)
            $("#modalPerfiles #estado").val(estado).trigger('change')
        })

        /* Eliminacion de perfiles */

        $(document).on("click", ".btn-deleted-perf", function (e) {
            e.preventDefault()
            let id = $(this).closest('tr').find('td:eq(0)').text()
            let perfil = $(this).closest('tr').find('td:eq(1)').text()

            Swal.fire({
                title: 'Eliminar?',
                text: "Desea eliminar el perfil :" + ' ' + perfil + '!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Borrar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url:'./?action=loadPerfiles',
                        type:'POST',
                        data:{id:id,tipo:5},
                        success:function (respo) {
                            if (respo.substr(2) == "conDatos"){
                                Swal.fire(
                                    'Error!',
                                    'Datos asociados , no puede ser borrado',
                                    'error'
                                )
                            }else{
                                eliminaPerfil(id)
                            }
                        }
                    })

                }
            })
        })

        function eliminaPerfil(id){
            $.ajax({
                url:'./?action=loadPerfiles',
                type:'POST',
                data:{id:id,tipo:6},
                success:function (respo) {
                    console.log(respo)
                    if (respo.substr(0,1) == 1){
                        Swal.fire(
                            'Borrado!',
                            respo.substr(2),
                            'success'
                        )
                        tableProTot.ajax.reload(null, false)
                    }else{
                        Swal.fire(
                            'Error!',
                            respo.substr(2),
                            'error'
                        )
                    }
                }
            })
        }

        $(document).on('click', '#process', function (e) {
            e.preventDefault()
            let nombre = $("#nameperfil").val()
            if (nombre != '') {
                $.ajax({
                    url: './?action=loadPerfiles',
                    type: 'POST',
                    data: $("#form-perfiles").serialize(),
                    success: function (respons) {
                        console.log(respons)
                        if (respons.substr(0, 1) == 1) {
                            Swal.fire({
                                'icon': 'success',
                                'title': respons.substr(2),
                            })
                            $("#modalPerfiles").modal("hide")
                            tableProTot.ajax.reload(null, false)

                        }
                    }
                })
            } else {
                Swal.fire({
                    'icon': 'error',
                    'title': 'Debe ingresar nombre de perfil.'
                })
            }
        })


    })
}