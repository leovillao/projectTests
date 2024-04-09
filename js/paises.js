if (document.getElementById('paisesfluid')) {
    $(document).on('click', '.remove-paises', function () {
        let id = $(this).closest('tr').find('td:eq(0)').text()
        Swal.fire({
            icon: 'warning',
            title: "Está seguro de eliminar éste registro?",
            text: "Ésta accion no se puede deshacer",
            showCancelButton: 'true',
            confirmButtonColor: '3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, confirmar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: './?action=processpaises',
                    type: 'POST',
                    data: {id: id, tipo: 3},
                    success: function (respond) {
                        //console.log(respond)
                        let request = JSON.parse(respond)
                        if (request.substr(0, 1) == 0) {
                            Swal.fire({
                                icon: 'error',
                                title: request.substr(2),
                            })
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: request.substr(2),
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.href = "./?view=paises"
                                }
                            })
                        }
                    }
                })
            }
        })
    })

    $("#save-modified-paises").click(function (e) {
        e.preventDefault()

        let pais = $("#panombre").val()
        if (pais.length == 0) {
            Swal.fire({
                icon: 'error',
                title: "Debe ingresar el País",
            })
        } else {
            $.ajax({
                url: './?action=processpaises',
                type: 'POST',
                data: $("#form-paises").serialize(),
                success: function (respond) {
                    //console.log(respond)
                    let respuesta = JSON.parse(respond)
                    if (respuesta.substr(0, 1) == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: respuesta.substr(2),
                        })
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: respuesta.substr(2),
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.href = './?view=paises'
                            }
                        })
                    }
                }
            })
        }
    })
}