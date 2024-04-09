if (document.getElementById('bancosclientesf')) {
    $(document).ready(function () {

        $(document).on('click', '.remove-bankcliente', function () {
            let id = $(this).closest('tr').find('td:eq(0)').text()
            Swal.fire({
                title: 'Eliminar registro?',
                text: 'Esta accion no se puede deshacer',
                icon: 'warning',
                showCancelButton: 'true',
                confirmButtonColor: '3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, confirmar',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: './?action=ProcessBancos',
                        type: 'POST',
                        data: {id: id, tipo: 3},
                        success: function (resp) {
                            let res = JSON.parse(resp)

                            if (res.substr(0, 1) == 0) {
                                Swal.fire({
                                    icon: 'error',
                                    title: res.substr(2),
                                })
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: res.substr(2),
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.href = './?view=bancoscliente'
                                    }
                                })
                            }
                        }
                    })
                }
            })
        })

        $("#save-banco").click(function (e) {
            e.preventDefault();
            // var formbancos = new formbancos();
            let nombre = $("#bbnombre").val()
            if (nombre.length == 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Debe ingresar nombre de banco",
                })
            } else {
                $.ajax({
                    url: './?action=ProcessBancos',
                    type: 'POST',
                    data: $("#form-bancosclientes").serialize(),
                    success: function (respond) {
                        console.log(respond)
                        let res = JSON.parse(respond)
                        if (res.substr(0, 1) === 0) {
                            Swal.fire({
                                icon: 'error',
                                title: res.substr(2),
                            })
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: res.substr(2),
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.href = './?view=bancoscliente'
                                }
                            })
                        }
                    }
                })
            }
        })
    })
}