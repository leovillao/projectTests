if (document.getElementById('secuencias')) {
    $(document).ready(function () {

        $(document).on('change','#documento',function () {
            // let documento = $(this).val()
            $.ajax({
                url:'./?action=loadReportes',
                type:'POST',
                data:{"option":1,"codigo":$(this).val()},
                success:function (repon) {
                    let r = JSON.parse(repon)
                    if (r.data != ''){
                        let td = ''
                        $.each(r.data,function (i,item) {
                            td += "<option value='"+item.id+"'>"+item.name+"</option>"
                        })
                        $("#reporte").html(td)
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Sin reportes',
                            text: 'Este documento no tiene reporte asignado!',
                            // footer: '<a href="">Why do I have this issue?</a>'
                        })
                        $("#reporte").html("<option value=''>Seleccione reporte...</option>")
                    }
                }
            })
        })

        $(document).on('click', '.edit-secuencia', function (e) {
            e.preventDefault()

            let id = $(this).attr('id')
            let descripcion = $(this).attr('descripcion')
            let estab = $(this).attr('estab')
            let emision = $(this).attr('emision')
            let documento = $(this).attr('documento')
            let dir = $(this).attr('dir')
            let secuencia = $(this).attr('secuencia')
            let formato = $(this).attr('formato')
            let sucursal = $(this).attr('sucursal')
            let tipodef = $(this).attr('tipodef')
            let tpemision = $(this).attr('tpemision')
            let iniva = $(this).attr('iniva')

            $("#modalSecuencias .modal-title").text(descripcion)
            $("#modalSecuencias #descripcion").val(descripcion)
            $("#modalSecuencias #documento").val(documento).trigger('change')
            $("#modalSecuencias #estab").val(estab)
            $("#modalSecuencias #option").val(2)
            $("#modalSecuencias #id").val(id)
            $("#modalSecuencias #emision").val(emision)
            $("#modalSecuencias #direccion").val(dir)
            $("#modalSecuencias #secuencia").val(secuencia)
            $("#modalSecuencias #sucursal").val(sucursal).trigger('change')
            $("#modalSecuencias #tipodef").val(tipodef).trigger('change')
            $("#modalSecuencias #reporte").val(formato).trigger('change')
            $("#modalSecuencias #btn-accion").text("Actualizar")

            if(tpemision == 1){
                $("#modalSecuencias #tipoEmision").prop("checked",true)
            }else{
                $("#modalSecuencias #tipoEmision").prop("checked",false)
            }
            if(iniva == 1){
                $("#modalSecuencias #iniva").prop("checked",true)
            }else{
                $("#modalSecuencias #iniva").prop("checked",false)
            }

            $("#modalSecuencias #btn-accion").text("Actualizar")
            $("#modalSecuencias").modal('show')

        })

        $(document).on('click', '#btn-accion', function () {
            const datos = $("#form-secuencia").serialize()
            $.ajax({
                url: './?action=processSecuencias',
                type: 'POST',
                data: datos,
                success: function (respond) {
                    let rs = JSON.parse(respond)
                    // console.log(rs.substr(2))
                    if(rs.substr(0,1) == 0){
                        Swal.fire({
                            title: 'ERROR',
                            text: rs.substr(2),
                            icon: 'error',
                        })
                    }else{
                        Swal.fire({
                            title: rs.substr(2),
                            icon: 'success',

                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.href = './?view=secuencias'
                            }
                        })
                    }
                }
            })
        })

        $(document).on('click',"#btn-ptoemision",function (e) {
            // e.preventDefault()
            console.log("nuevaSecuencias")
            $("#modalSecuencias").modal('show')
            $("#modalSecuencias .modal-title").text('Nuevo punto de emisión')
            $("#form-secuencia").trigger("reset")
            $("#modalSecuencias #option").val(1)
            $("#modalSecuencias #btn-accion").text("Crear")
        })

    })
}