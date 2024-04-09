if (document.getElementById('ciudadesfluid')) {
    $(document).ready(function () {

        $(document).on('click', '.remove-ciudades', function () {
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
                        url: './?action=processciudades',
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
                                        location.href = "./?view=ciudades"
                                    }
                                })
                            }
                        }
                    })
                }
            })
        })

        loadprovincia()

        function loadprovincia() {
            let pro = $("#provin").val()
            console.log(pro)
            $.ajax({
                url: './?action=processciudadesdata',
                type: 'POST',
                data: {option: 3},
                success: function (resultado) {
                    //console.log(resultado)
                    let res = JSON.parse(resultado)
                    let viewHtml = '<option value="">Seleccione Provincia...</option>'
                    let selected = ''
                    $.each(res, function (i, item) {
                        if (pro != '') {
                            if (pro == item.id) {
                                selected = "selected"
                            }else{
                                selected = ""
                            }
                        }
                        viewHtml += '<option value="' + item.id + '" ' + selected + ' >' + item.name + '</option>'
                    })
                    $("#provincia").html(viewHtml)
                }
            })
        }


        $("#save-modified-ciudades").click(function (e) {
            e.preventDefault()
            let nombre = $("#name").val()
            if (nombre.length == 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Debe ingresar el nombre"
                })
            } else {
                $.ajax({
                    url: './?action=processciudades',
                    type: 'POST',
                    data: $('#form-ciudades').serialize(),
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
                                    location.href = "./?view=ciudades"
                                }
                            })
                        }
                    }
                })
            }
        })
    })
}