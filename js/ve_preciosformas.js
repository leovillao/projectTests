if(document.getElementById('listaspreciosforma')){
    $(document).ready(function () {
        $(".edit-formas-precios").on('click',function (e) {
            e.preventDefault()
            $("#modalEditFormasCobro").modal('show')
            $("#form-formas-pago").trigger('reset')
            let name = $(this).closest('tr').find('td:eq(1)').text()
            $("#modalEditFormasCobro .modal-title").text(name)
            $("#modalEditFormasCobro #btn-process").text("Actualizar")
            let id = $(this).attr('id')
            let forma = $(this).attr('formapago')
            let unidad = $(this).attr('unidad')
            let estado = $(this).attr('estado')
            $("#fpid").val(forma).trigger('change')
            $("#id").val(id)
            $("#unidad").val(unidad).trigger('change')
            if (estado == 1){
                $("#estado").prop("checked", true);
            }else{
                $("#estado").prop("checked", false);
            }
        })

        $("#btn-process").on('click',function (e) {
            e.preventDefault()
            $.ajax({
                url:'./?action=ve_processFormasPrecios',
                type:'POST',
                // cache:false,
                // contentType:false,
                // processData:false,
                data:$('#form-formas-pago').serialize(),
                success:function (respon) {
                    let res = respon
                    if (res.substr(0, 1) == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: res.substr(2),
                            // footer: '<a href="">Why do I have this issue?</a>'
                        })
                    } else {
                        Swal.fire({
                            icon: 'success',
                            // title: '',
                            text: res.substr(2),
                            // footer: '<a href="">Why do I have this issue?</a>'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                loadPagPrincipal()
                            }
                        })
                    }
                }
            })
        })
        function loadPagPrincipal(){
            location.href = "./?view=ve_formasprecios";
        }

    })
}