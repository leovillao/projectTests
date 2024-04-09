if (document.getElementById('listvendedor')) {

    $(document).ready(function () {

        $(document).on('click', '.remove-vend', function () {
            let id = $(this).closest('tr').find('td:eq(0)').text()
            Swal.fire({
                title: 'Eliminar Vendedor?',
                text: "Desea eliminar vendedor!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, borrar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: './?action=processvendedores',
                        type: 'POST',
                        data: {id: id, tipo: 3},
                        success: function (respuesta) {
                            let res = JSON.parse(respuesta)
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
                                        location.href = './?view=listVendedores'
                                    }
                                })
                            }
                        }
                    })
                }
            })
        })


        $("#almacenar-vendedor").click(function (e) {
            e.preventDefault()
            let nombre = $("#venombre")
            if (nombre.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Debes agregar el nombre",
                })
            } else {
                $.ajax({
                    url: './?action=processvendedores',
                    type: 'POST',
                    data: $("#form-vendedores").serialize(),
                    success: function (resp) {
                        console.log(resp)
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
                                    location.href = './?view=listVendedores'
                                }
                            })
                        }
                    }
                })
            }
            /*if (nombre.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Debes agregar el nombre",
                })
            } else {
                let telefono = $("#vefono")
                if (telefono.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: "Debes ingresar el telefono",
                    })
                } else {
                    let mail = $("#veemail")
                    if (mail === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: "Debes ingresar el correo",
                        })
                    } else {
                        let direccion = $("#vedirec")
                        if (direccion === 0) {
                            Swal.fire({
                                icon: 'error',
                                title: "Debes ingresar la direccion",
                            })
                        } else {
                            let ruc = $("#veruc")
                            if (ruc === 0) {
                                Swal.fire({
                                    icon: 'error',
                                    title: "Debes ingresar el ruc",
                                })
                            } else {
                                let cajero = $("#vecajero")
                                if (cajero.length === 0) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: "Debes seleccionar cajero",
                                    })
                                } else {
                                    let activo = $("#veactivo")
                                    if (activo.length === 0) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: "Dbes seleccionar el estado",
                                        })
                                    } else {

                                    }
                                }
                            }
                        }
                    }
                }
            }*/
        }) /** ======= */

    })

}