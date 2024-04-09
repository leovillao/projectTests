// if (document.getElementById("in_kardex")) {
//     $(document).ready(function () {
//         // console.log("kardex")

//         $(document).on('change', '#producto', function (e) {
//             e.preventDefault()
//             let itcodigo = $(this).val()
//             loadDataPro(itcodigo)
//         })


//         loadDataProductCodigo()

//         function loadDataProductCodigo() {
//             let option = 2
//             let viewHtml = ''
//             $.ajax({
//                 url: "./?action=in_productLoad",
//                 type: "POST",
//                 data: {option: option},
//                 success: function (data) {
//                     let res = JSON.parse(data)
//                     let viewHtml = ''
//                     let select = ''
//                     viewHtml += '<option value="">Seleccion producto...</option>'
//                     $.each(res, function (i, item) {
//                         viewHtml += '<option >' + item.text + '</option>'
//                     });
//                     $("#list-productos").html(viewHtml)
//                 }
//             })
//         }

//         $("#buscarProductos").on('click', function (e) {
//             e.preventDefault()
//             let bodega = $("#bodega").val()
//             let productoTxt = $("#productTxt").val()
//             let datos = new FormData()
//             datos.append("codigo", $("#producto").val())
//             datos.append("unidad", $("#unidad").val())
//             datos.append("bodega", bodega)
//             datos.append("desde", $("#desde").val())
//             datos.append("hasta", $("#hasta").val())
//             datos.append("option", 5)
//             // console.log(datos)
//             $.ajax({
//                 url: './?action=in_productLoad',
//                 type: 'POST',
//                 data: datos,
//                 processData: false,
//                 contentType: false,
//                 success: function (result) {
//                     let datos = JSON.parse(result)
//                     console.log(datos)
//                     if (datos.length == 0) {
//                         $(".comentario-bloque").text("El Producto " + productoTxt + ", No tiene informacion para mostrar....")
//                         $("#bloque-comentario").removeClass("no-visible").addClass("visible");
//                         $("#tabla-bodegas").removeClass("visible").addClass("no-visible");
//                         $("#tabla-allBodegas").removeClass("visible").addClass("no-visible");
//                     } else {
//                         let htmlTable = ''
//                         // if (bodega == 0) {
//                         //     $.each(datos, function (i, item) {
//                         //         // console.log(item.control)
//                         //         htmlTable += "<tr>"
//                         //         htmlTable += '<td class="">' + item.fecha + '</td>'
//                         //         htmlTable += '<td class="">' + item.bodega + '</td>'
//                         //
//                         //         htmlTable += '<td class="align-number">' + item.saldoanterior + '</td>'
//                         //         htmlTable += '<td class="align-number">' + item.ingreso + '</td>'
//                         //         htmlTable += '<td class="align-number">' + item.egreso + '</td>'
//                         //         htmlTable += '<td class="align-number">' + item.saldo + '</td>'
//                         //
//                         //         htmlTable += '<td class="align-number">' + item.saldocosto + '</td>'
//                         //         htmlTable += '<td class="align-number">' + item.costoi + '</td>'
//                         //         htmlTable += '<td class="align-number">' + item.costoe + '</td>'
//                         //         htmlTable += '<td class="align-number">' + item.costotot + '</td>'
//                         //         htmlTable += '<td class="align-number">' + item.costounit + '</td>'
//                         //         htmlTable += "</tr>"
//                         //     })
//                         //     $("#table-resutl-kardex-allBodega").html(htmlTable)
//                         //     colorPaint("table-resutl-kardex-allBodega")
//                         //     $("#bloque-comentario").removeClass("visible").addClass("no-visible");
//                         //     $("#tabla-bodegas").removeClass("visible").addClass("no-visible");
//                         //     $("#tabla-allBodegas").removeClass("no-visible").addClass("visible");
//                         // } else {
//                             $.each(datos, function (i, item) {
//                                 htmlTable += "<tr>"
//                                 htmlTable += '<td class="">' + item.fecha + '</td>'
//                                 htmlTable += '<td class="">' + item.bodega + '</td>'

//                                 htmlTable += '<td class="align-number">' + item.saldoanterior + '</td>'
//                                 htmlTable += '<td class="align-number">' + item.ingreso + '</td>'
//                                 htmlTable += '<td class="align-number">' + item.egreso + '</td>'
//                                 htmlTable += '<td class="align-number">' + item.saldo + '</td>'
//                                 htmlTable += "</tr>"
//                             })
//                             $("#table-resutl-kardex-bodega").html(htmlTable)
//                             colorPaint("table-resutl-kardex-bodega")
//                             $("#bloque-comentario").removeClass("visible").addClass("no-visible");
//                             $("#tabla-allBodegas").removeClass("visible").addClass("no-visible");
//                             $("#tabla-bodegas").removeClass("no-visible").addClass("visible");
//                         // }
//                     }

//                 }
//             })
//         })


//         $(document).on('click', '.btn-imagen', function () {
//             let srcImagen = $(this).attr('id')
//             Swal.fire({
//                 // title: 'Sweet!',
//                 // text: 'Modal with a custom image.',
//                 imageUrl: 'https://smarttag-bi.com/' + srcImagen,
//                 imageWidth: 1024,
//                 imageHeight: 300,
//                 // imageAlt: 'Custom image',
//             })
//         })

//         function colorPaint(tabla) {
//             let count = 0
//             $("#" + tabla + " tr").each(function () {
//                 count = count + 1
//                 if (count % 2 == 0) {
//                     // alert("El número "+value+" es par");
//                     $(this).addClass('fondo-color-row')
//                 }
//             })
//         }

//         $("#btnProducto").on('click', function (e) {
//             e.preventDefault()
//             $("#modalProducto").modal('show')
//             $('.btn-stock').prop('disabled',true)
//         })
//         loadProductos()

//         function loadProductos() {
//             let option = 1
//             $("#table-productos").DataTable({
//                 "destroy": true,
//                 "aaSorting": [[0, "asc"]], // Sort by first column descending
//                 "ajax": {
//                     "method": "POST",
//                     "url": "./?action=getProductsTable",
//                     "data": {"option": option}
//                 },
//                 "language": {
//                     "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
//                 },
//                 "columns": [
//                     {mData: 'itcodigo'},
//                     {mData: 'name'},
//                     {mData: 'botones'},
//                 ],
//                 "bAutoWidth": false,
//                 "lengthMenu": [[5, 10, 20, 25, 50, -1], [5, 10, 20, 25, 50, "Todos"]],
//                 "iDisplayLength": 5,
//                 responsive: "true",
//             });
//         }

//         $(document).on('click', '#table-tbody-products tr td:not(:last-child)', function () {
//             let row = $(this)
//             let itcodigo = row.parents("tr").find("td").eq(0).text()
//             let itname = row.parents("tr").find("td").eq(1).text()
//             let cl = $(this).parent()
//             $('#producto').val(itcodigo)
//             $('#productTxt').val(itname)
//             $("#modalProducto").modal('hide')
//             loadDataPro(itcodigo)
//         })

//         function loadDataPro(itcodigo) {
//             if (producto.length != "") {
//                 $.ajax({
//                     url: "./?action=in_productLoad",
//                     type: "POST",
//                     data: {option: 1, itcodigo: itcodigo},
//                     success: function (data) {
//                         console.log(data)
//                         let d = JSON.parse(data)
//                         if (d != '') {
//                             $("#productTxt").val(d[0])
//                             let rhtml = '<option value="' + d[1]['idunit'] + '">' + d[1]['unitText'] + '</option>'
//                             rhtml += '<option value="' + d[2]['idunit'] + '">' + d[2]['unitText'] + '</option>'
//                             $("#unidad").html(rhtml)
//                         }else{
//                             Swal.fire({
//                                 icon: 'error',
//                                 title: 'Codigo Incorrecto',
//                                 text: 'Producto no Existe',
//                                 // footer: '<a href="">Why do I have this issue?</a>'
//                             })
//                             $("#producto").val(' ')
//                         }
//                     }
//                 })
//             } else {
//                 $("#productTxt").val(' ')
//             }
//         }

//     })
// }