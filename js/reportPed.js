if (document.getElementById('reportePedidos')) {
    $(document).ready(function () {

        $(document).on('click', '#buscarPedidos', function (w) {
            w.preventDefault()
            let desde = $("#desde").val()
            let hasta = $("#hasta").val()
            let localidad = $("#localidad").val()
            let usuario = $("#usuario").val()
            loadDataPedidos(usuario, localidad, hasta, desde)
            console.log(desde+'+'+hasta+'+'+localidad+'+'+usuario)
        })

        let desde = $("#desde").val()
        let hasta = $("#hasta").val()
        let localidad = $("#localidad").val()
        let usuario = $("#usuario").val()

        loadDataPedidos(usuario, localidad, hasta, desde)

        function loadDataPedidos(usuario, localidad, hasta, desde) {
            $("#table-pedidos").DataTable({
                "destroy": true,
                "ajax": {
                    "method": "POST",
                    "url": "./?action=pedidosData",
                    "data": {"option": 1, "localidad": localidad, "hasta": hasta, "desde": desde, "usuario": usuario}
                },
                "columns": [
                    {"data": "pedido"},
                    {"data": "fecha"},
                    {"data": "cliente"},
                    {"data": "localidad"},
                    {"data": "total"},
                    {"data": "estado"},
                    // {"defaultContent": "<button class='btn btn-xs btn-success btn-pago-docs'><i class=\"fa fa-money\" aria-hidden=\"true\"></i></button>"},
                ],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> ',
                        titleAttr: 'Exportar a Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> ',
                        titleAttr: 'Exportar a PDF',
                        className: 'btn btn-danger'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> ',
                        titleAttr: 'Imprimir',
                        className: 'btn btn-info'
                    },
                ],
                "bAutoWidth": false,
                "lengthMenu": [[10, 15, 20, 25, 50, -1], [10, 15, 20, 25, 50, "Todos"]],
                "iDisplayLength": 10,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                },
            })
        }

        $(document).on('click', '.detallePedido', function (e) {
            e.preventDefault()
            let id = $(this).closest('tr').find('td:eq(0)').text()
            console.log(id)
            $.ajax({
                url: "./?action=loadPedidos",
                type: "POST",
                data: {id: id, option: 5},
                success: function (dato) {
                    console.log(dato)
                    let viewHtml = ''
                    let res = JSON.parse(dato)
                    if (res == '') {
                        viewHtml += '<tr>'
                        viewHtml += '<td colspan="3">No tiene detalle</td>'
                        viewHtml += '</tr>'
                    }
                    $.each(res, function (i, item) {
                        viewHtml += '<tr>'
                        viewHtml += '<td>' + item.linea + '</td><td>' + item.producto + '</td><td>' + item.cantidad + '</td>'
                        viewHtml += '</tr>'
                    });
                    $("#table-pedidosdet").html(viewHtml)
                    $("#modalPedidosDet .modal-title").text("Pedido # " + id)
                    $("#modalPedidosDet").modal('toggle')
                }
            })
        })

    })
}