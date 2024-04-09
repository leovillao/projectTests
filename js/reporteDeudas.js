if (document.getElementById('reportedeuda')) {
    $(document).ready(function () {
        console.log("d")

        valCliente()
        SelectCliente()
        loadDataPersonId()


        function valCliente() {
            $(document).on('click', '#chkcliente', function () {
                if ($(this).is(":checked")) {
                    $("#cliente").removeAttr("disabled")
                } else {
                    $("#cliente").attr("disabled", true)
                }
            })
        }

        function SelectCliente() {
            $('#cliente').select2({
                placeholder: "Seleccione cliente..."
            });
        }


        function loadDataPersonId() {
            let option = 2
            let viewHtml = ''
            $.ajax({
                url: "./?action=personLoad",
                type: "POST",
                data: {option: option},
                success: function (respon) {
                    let res = JSON.parse(respon)
                    let viewHtml = ''
                    let select = ''
                    viewHtml += '<option value="">Seleccion cliente...</option>'
                    $.each(res, function (i, item) {
                        viewHtml += '<option value="' + item.id + '" >' + item.text + '</option>'
                    });
                    $("#cliente").html(viewHtml)
                }
            })
        }


    })
}