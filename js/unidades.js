if (document.getElementById('unidades')) {
    $(document).ready(function () {


        $(".nav-tabs a").click(function () {
            $(this).tab('show');
        });

        chk_unidBase()

        function chk_unidBase() {
            let unidBase = $("#chk_unidBase")
            if (unidBase.is(':checked')) {
                $("#unidadbase").attr('disabled', true)
            }
        }

        $('#chk_unidBase').on('click', function () {
            if ($(this).is(':checked')) {
                $("#unidadbase").attr('disabled', true)
            } else {
                $("#unidadbase").removeAttr('disabled')
            }
        });

        $(document).on('click', '#grabar-unidad', function (e) {
            e.preventDefault()
            $.ajax({
                url: './?action=loadUnidades',
                type: 'POST',
                data: $("#form-unidad").serialize(),
                success: function (respon) {
                    console.log(respon)
                    if (respon.substr(0, 1) == 1) {
                        Swal.fire({
                            icon: 'success',
                            title: respon.substr(2),
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.href = './?view=unidades';
                            }
                        })
                    } else {

                    }
                }
            })
        })


    });


}