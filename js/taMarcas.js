$(function () {

    getMarcasOption()

    function getMarcasOption() {
        $.ajax({
            url: "./?action=TaMarcas_marcasAutos",
            type: "POST",
            success: function (responde) {
                let t = JSON.parse(responde)
                let option = "<option value=''>Seleccione Marca...</option>"
                $.each(t, function (i, item) {
                    option += "<option value='" + item.id + "'>" + item.name + "</option>"
                })
                $("#Marcas").html(option)
            }
        })
    }

    $("#grabaMarca").click(function () {
        $.ajax({
            url: "./?action=TaMarcas_nuevo",
            type: "POST",
            data: $("#form-marcas").serialize(),
            success: function (responde) {
                let t = JSON.parse(responde)
                if (t.substr(0, 1) == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: t.substr(2),
                    })
                    getMarcas()
                    getMarcasOption()
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: t.substr(2),
                    })
                }
            }
        })
    })

    $("#actualizaMarca").click(function () {
        $.ajax({
            url: "./?action=TaMarcas_actualiza",
            type: "POST",
            data: $("#form-marcas").serialize(),
            success: function (responde) {
                console.log(responde)
                let t = JSON.parse(responde)
                if (t.substr(0, 1) == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: t.substr(2),
                    })
                    getMarcas()
                    getMarcasOption()
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: t.substr(2),
                    })
                }
            }
        })
    })

    $('.checkMarca').click(function () {
        if ($(this).is(':checked')) {
            $("#Marcas").prop("disabled", false)
            $("#tipo").prop("disabled", false)
        } else {
            $("#tipo").prop("disabled", true)
            $("#Marcas").prop("disabled", true)
        }
    });

    $(".btn-modal-new").click(function () {
        $("#form-marcas").trigger('reset')
        $("#grabaMarca").removeClass('noVisible')
        $("#actualizaMarca").removeClass('visible')
        $("#actualizaMarca").addClass('noVisible')
        $("#modalMarcas").modal('toggle')
        $("#modalMarcas .modal-title").text('Nueva Marca / Modelo')
    })


    $(document).on("click", ".btn-editarMarca", function () {
        $("#form-marcas").trigger('reset')
        let marca = $(this).closest('tr').find('td:eq(1)').text()
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let modelo = $(this).closest('tr').find('td').eq(1).find('input[type="hidden"]').val()
        let tipo = $(this).closest('tr').find('td').eq(0).find('input[type="hidden"]').val()
        if (modelo != "") {
            $("#checkMarca").prop("checked", true);
            $("#Marcas").val(modelo).trigger('change')
            $("#Marcas").prop('disabled',false)
            $("#tipo").prop('disabled',false)
            $("#tipo").val(tipo).trigger('change')
        } else {
            $("#Marcas").prop('disabled',true)
            $("#tipo").prop('disabled',true)
            $("#checkMarca").prop("checked", false);
        }
        let mn = ""
        if (marca == "") {
            mn = modelo
        } else {
            mn = marca
        }
        $("#idMarca").val(id)
        $("#marcaNombre").val(marca)
        $("#actualizaMarca").removeClass('noVisible');
        $("#grabaMarca").removeClass('visible')
        $("#grabaMarca").addClass('noVisible')
        $("#modalMarcas").modal('toggle')
        $("#modalMarcas .modal-title").text(mn)
    })

    $(document).on("click", ".btn-delMarca", function () {
        $("#form-marcas").trigger('reset')
        let marca = $(this).closest('tr').find('td:eq(1)').text()
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let modelo = $(this).closest('tr').find('td').eq(1).find('input[type="hidden"]').val()
        let mn = ""
        if (marca == "") {
            mn = "Marca :" + marca +  ", Model : " + modelo
        } else {
            mn = "Marca :" + marca
        }
        Swal.fire({
            title: 'Eliminar',
            text: "Desea eliminar la marca / modelo " + mn + " !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Borrar!'
        }).then((result) => {
            if (result.isConfirmed) {
                delMarca(id)
            }
        })
    })

    function delMarca(id) {
        $.ajax({
            url: "./?action=TaMarcas_borrar",
            type: "POST",
            data: {"id": id},
            success: function (responde) {
                console.log(responde)
                let t = JSON.parse(responde)
                if (t.substr(0, 1) == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: t.substr(2),
                    })
                    getMarcas()
                    getMarcasOption()
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: t.substr(2),
                    })
                }
            }
        })
    }

    getMarcas()

    function getMarcas() {
        $("#table-marcas").DataTable({
            "destroy": true,
            "sort": false,
            "ajax": {
                "method": "POST",
                "url": "./?action=TaMarcas_gets",
            },

            "columns": [
                {"data": "id"},
                {"data": "modelo"},
                {"data": "marca"},
                {"data": "tipo"},
                {"defaultContent": "<button class='btn btn-xs btn-primary btn-editarMarca'>Edit</button><button class='btn btn-xs btn-danger btn-delMarca'><i class=\"fa fa-remove\" aria-hidden=\"true\"></i></button>"},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            },
        })
    }
})
