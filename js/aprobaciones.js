if (document.getElementById('aprobaciones')) {
    // $(document).ready(function () {
    //
    //     viewTableLoad()
    //
    //     function viewTableLoad() {
    //         $("#table-aprobaciones").DataTable({
    //             "destroy": true,
    //             "ajax": {
    //                 "method": "POST",
    //                 "url": "./?action=loadPedidos",
    //                 "data": {"option": 1}
    //             },
    //             "columns": [
    //                 {"data": "pedido"},
    //                 {"data": "fecha"},
    //                 {"data": "cliente"},
    //                 {"data": "cupo"},
    //                 {"data": "total"},
    //                 {"data": "aprobado"},
    //                 {"data": "creado"},
    //             ],
    //             "language": {
    //                 "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
    //             },
    //         })
    //     }
    //
    //
    //     $(document).on('change', '.aprobar', function () {
    //         let id = $(this).closest('tr').find('td:eq(0)').text()
    //
    //         if ($(this).is(":checked")) {
    //             let idsnotAprob = $("#noAprobadosIds").val()
    //
    //             let idsAprob = $("#aprobadosIds").val()
    //             let cupo = $(this).closest('tr').find('td:eq(3)').text()
    //             let total = $(this).closest('tr').find('td:eq(4)').text()
    //             if (idsAprob == '') {
    //                 $("#aprobadosIds").val(id)
    //             } else {
    //                 $("#aprobadosIds").val(idsAprob + ',' + id)
    //             }
    //
    //             if (parseFloat(cupo) >= parseFloat(total)) {
    //                 $(this).prop("checked", true)
    //             } else {
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: "No tiene cupo disponible para la aprobación de este pedido",
    //                 })
    //                 $(this).prop("checked", false)
    //             }
    //         } else {
    //             let noAprob = $("#noAprobadosIds").val()
    //             if (noAprob == '') {
    //                 $("#noAprobadosIds").val(id)
    //             } else {
    //                 $("#noAprobadosIds").val(noAprob + ',' + id)
    //             }
    //         }
    //     })
    //
    //     SelectCliente()
    //
    //     function SelectCliente() {
    //         $('#cliente').select2({
    //             placeholder: "Seleccione cliente..."
    //         });
    //     }
    //
    //     loadDataPersonId()
    //
    //     function loadDataPersonId() {
    //         let option = 2
    //         let viewHtml = ''
    //         $.ajax({
    //             url: "./?action=personLoad",
    //             type: "POST",
    //             data: {option: option},
    //             success: function (data) {
    //                 let res = JSON.parse(data)
    //                 let viewHtml = ''
    //                 let select = ''
    //                 viewHtml += '<option value="">Seleccion cliente...</option>'
    //                 $.each(res, function (i, item) {
    //                     viewHtml += '<option value="' + item.id + '" ' + select + '>' + item.text + '</option>'
    //                 });
    //                 $("#cliente").html(viewHtml)
    //             }
    //         })
    //     }
    //
    //     $(document).on('click', '#grabar-pedidos', function (e) {
    //         e.preventDefault()
    //         let ids = ''
    //         let idn = ''
    //         let aprob = $("#aprobadosIds").val()
    //         let noAprob = $("#noAprobadosIds").val()
    //
    //         $("#table-body-pedidos tr").each(function () { /*Recorro la tabla de productos para tomar el id de las lineas de operation que esten con el checkbox activo*/
    //             let row = $(this)
    //             $(this).find('input[type="checkbox"]').each(function () {
    //                 // $(this).find('input[type="checkbox"]:checked').each(function () {
    //                 if ($(this).is(":checked")) {
    //                     ids += row.find('td:eq(0)').text() + ','
    //                 } else {
    //                     idn += row.find('td:eq(0)').text() + ','
    //                 }
    //             });
    //         });
    //
    //         let option = 3
    //         $.ajax({
    //             url: "./?action=loadPedidos",
    //             type: "POST",
    //             data: {option: option, ids: ids, idn: idn, aprob: aprob, noAprob: noAprob},
    //             success: function (dato) {
    //                 let res = JSON.parse(dato)
    //                 if (res.substr(0, 1) == 0) {
    //                     Swal.fire({
    //                         icon: 'error',
    //                         title: res.substr(2),
    //                     })
    //                 } else {
    //                     Swal.fire({
    //                         icon: 'success',
    //                         title: res.substr(2),
    //                     })
    //                     viewTableLoad()
    //                 }
    //             }
    //         })
    //     })
    //
    //     $(document).on('click', '.detallePedido', function (e) {
    //         e.preventDefault()
    //         let id = $(this).closest('tr').find('td:eq(0)').text()
    //         $("#table-documentos-pedidos").DataTable({
    //             "destroy": true,
    //             "ajax": {
    //                 "method": "POST",
    //                 "url": "./?action=loadPedidos",
    //                 "data": {"option": 5, "id": id}
    //             },
    //
    //             "columns": [
    //                 {"data": "linea"},
    //                 {"data": "codigo"},
    //                 {"data": "producto"},
    //                 {"data": "cantidad"},
    //                 {"data": "botones"}, /*aqui visualizo los botones para las acciones necesarias*/
    //             ],
    //             "language": {
    //                 "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
    //             },
    //         })
    //         // $("#table-pedidosdet").html(viewHtml)
    //         $("#modalPedidosDet .modal-title").text("Pedido # " + id)
    //         $("#modalPedidosDet").modal('toggle')
    //     })
    //
    //     $(document).on('click', '.btn-anular-linea-det', function () {
    //         let detalleID = $(this).attr('iddetalle')
    //         let pedidoID = $(this).attr('idpedido')
    //         let producto = $(this).closest('tr').find('td:eq(2)').text()
    //         let cantidad = $(this).closest('tr').find('td:eq(3)').text()
    //         Swal.fire({
    //             html: '<p style="font-size: 12px">Desea ANULAR el producto : <b>' + producto + '</b>,</p>' +
    //                 '<p style="font-size: 12px">cantidad : ' + cantidad + ',</p>'+
    //                 '<p style="font-size: 12px"> del Pedido # ' + pedidoID + ' ?</p>',
    //             // text: "You won't be able to revert this!",
    //             icon: 'warning',
    //             showCancelButton: true,
    //             confirmButtonColor: '#3085d6',
    //             cancelButtonColor: '#d33',
    //             confirmButtonText: 'Borrar'
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 anularDetalleLinea(pedidoID, detalleID, $(this))
    //                 /*Swal.fire(
    //                     'Deleted!',
    //                     'Your file has been deleted.',
    //                     'success'
    //                 )*/
    //             } else {
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: "Proceso cancelado",
    //                 })
    //             }
    //         })
    //     })
    //
    //     function anularDetalleLinea(pedido, detalle, btn) {
    //
    //
    //         $.ajax({
    //             url: "./?action=loadPedidos",
    //             type: "POST",
    //             data: {"pedido": pedido, "detalle": detalle, option: 10},
    //             success: function (dato) {
    //                 console.log(dato)
    //                 let r = JSON.parse(dato)
    //                 if (r.substr(0, 1) == 1) {
    //                     Swal.fire({
    //                         icon: 'success',
    //                         title: r.substr(2),
    //                     })
    //                     btn.prop('disabled', true)
    //                 } else {
    //                     Swal.fire({
    //                         icon: 'error',
    //                         title: r.substr(2),
    //                     })
    //                 }
    //             }
    //         })
    //     }
    //
    // })
}