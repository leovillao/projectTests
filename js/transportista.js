$(function () {
    $("#tridentificacion").mask('0000000000000')
    $("#trplaca").mask('AAA0000')
    $("#trtelefono").mask('0000000000')

    $("#grabarTransportista").click(function () {
        let datos = new FormData()
        $(".valor-input").each(function () {
            datos.append($(this).attr('name'), $(this).val())
        });
        $.ajax({
            url: "./?action=transportistas_grabar",
            type: "POST",
            processData: false,
            contentType: false,
            cache: false,
            data: datos,
            success: function (respond) {
                let r = JSON.parse(respond)
                if (r.substr(0, 1) == 1) {
                    Swal.fire({
                        icon: 'success',
                        html: '<h4>' + r.substr(2) + '</h4>',
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        html: '<h4>' + r.substr(2) + '</h4>',
                        // footer: '<a href="">Why do I have this issue?</a>'
                    })
                }
            }
        })
    })

    $("#actualizarTransportista").click(function () {
        let datos = new FormData()
        $(".valor-input").each(function () {
            datos.append($(this).attr('name'), $(this).val())
        });
        $.ajax({
            url: "./?action=transportistas_update",
            type: "POST",
            processData: false,
            contentType: false,
            cache: false,
            data: datos,
            success: function (respond) {
                let r = JSON.parse(respond)
                if (r.substr(0, 1) == 1) {
                    Swal.fire({
                        icon: 'success',
                        html: '<h4>' + r.substr(2) + '</h4>',
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        html: '<h4>' + r.substr(2) + '</h4>',
                        // footer: '<a href="">Why do I have this issue?</a>'
                    })
                }
            }
        })
    })

    $(".deleteTransportista").click(function () {
        let id = $(this).attr('trid')
        let name = $(this).attr('trnombre')
        Swal.fire({
            title: name,
            html: "Desea eliminar este registro ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Eliminar!'
        }).then((result) => {
            if (result.isConfirmed) {
                eliminarTransportista(id)
            }
        })
    })

    function eliminarTransportista(id) {
        $.ajax({
            url: "./?action=transportistas_delete",
            type: "POST",
            data: {"id": id},
            success: function (responde) {
                let r = JSON.parse(responde)
                if (r.substr(0, 1) == 1) {
                    Swal.fire({
                        icon: 'success',
                        html: '<h4>' + r.substr(2) + '</h4>',
                    }).then((result) => {
                        location.href = "./?view=transportistas";
                    })

                } else {
                    Swal.fire({
                        icon: 'error',
                        html: '<h4>' + r.substr(2) + '</h4>',
                        // footer: '<a href="">Why do I have this issue?</a>'
                    })
                }
            }
        })
    }

})
