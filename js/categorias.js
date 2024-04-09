if (document.getElementById('categoryfluid')) {
    $(document).ready(function () {

        $(document).on('click', '.remove-categoria', function () {
            let id = $(this).closest('tr').find('td:eq(0)').text()
            // console.log(id); 
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
                        url: './?action=processcategorias',
                        type: 'POST',
                        data: {id: id, tipo: 3},
                        success: function (resp) {
                            let res = JSON.parse(resp)
                            console.log(resp)
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
                                        location.href = './?view=categorias'
                                    }
                                })
                            }
                        }
                    })
                }
            })
        })


        $('#save-category').click(function (e) {
            e.preventDefault()
            let nombre = $("#ctname").val()
            if (nombre.length == 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Debe ingresar el nombre",
                })
            } else {
                let descripcion = $("#ctdescription").val()
                if (descripcion.length == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: "Debe ingresar la descripcion",
                    })
                } else {
                    let orden = $("#ctorden").val()
                    if (orden.length == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: "Debe ingresar el numero correspondiente a la categoria"
                        })
                    } else {
                        $.ajax({
                            url: './?action=processcategorias',
                            type: 'POST',
                            data: $('#form-categoria').serialize(),
                            success: function (respond) {
                                //console.log(respond)
                                let res = JSON.parse(respond)
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
                                            location.href = './?view=categorias'
                                        }
                                    })
                                }
                            }
                        })
                    }
                }
            }

        })

    })

}