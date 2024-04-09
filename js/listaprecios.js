$(document.getElementById('listaprecios'))
{
    $(document).ready(function () {
        $("#btn-graba").on('click', function (e) {
            e.preventDefault()
            let numPrecios
            let arrayPadre = [];
            let valida = false

            $("#table-body-precios tbody tr").each(function () { /*Recorro la tabla de productos para tomar el id de las lineas de operation que esten con el checkbox activo*/
                let row = $(this)
                let id = '' // EL ID DEL PRODUCTO
                let i = 3
                while (i < 13) {
                    if (row.find('td').eq(i).find('input').hasClass('valor-editado')) { // SE VALIDA SI EL INPUT TIENE LA CLASE PARA SABER QUE EL VALOR HA SIDO EDITADO
                        let arrayHijo = {};
                        if (valida == false) {
                            arrayHijo.id = row.find('td:eq(0)').text()
                            arrayHijo.precio = (i - 2)
                            arrayHijo.valor = row.find('td').eq(i).find('input').val() // SE TOMA EL VALOR EDITADO
                            arrayPadre.push(arrayHijo)
                            console.log(arrayPadre)
                            if (row.find('td').eq(i).find('input').val() == '') {
                                valida = true
                            }else{
                                row.find('td').eq(i).find('input').removeClass('valor-editado') // SE ELIMINA LA CLASE DEL VALOR EDITADO
                            }
                        }
                    }
                    i++
                }
            });
            if (valida == false) {
                $.ajax({
                    url: './?action=processlprecios',
                    type: 'POST',
                    // contentType: 'application/json; charset=utf-8',
                    data: {tuArrJson: JSON.stringify(arrayPadre)},
                    success: function (respon) {
                        if (respon.substr(0, 1) == 0) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error...',
                                text: respon.substr(2),
                                // footer: '<a href="">Why do I have this issue?</a>'
                            })
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Actualización',
                                text: respon.substr(2),
                                // footer: '<a href="">Why do I have this issue?</a>'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.href = "./?view=listaprecios"
                                }
                            })
                        }
                    }
                })
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error...',
                    text: 'No se permite valores vacios',
                    // footer: '<a href="">Why do I have this issue?</a>'
                })
            }


        })

        colorPaintTable()

        function colorPaintTable() {
            let count = 0
            $("#table-body-precios tbody tr").each(function () {
                count = count + 1
                if (count % 2 == 0) {
                    $(this).addClass('fondo-color-row')
                }
            })
        } // SE RECORRE LA TABLA VALIDANDO LAS FILAS PARES CONFIGURAR EL COLOR DE FONDO
    })
}