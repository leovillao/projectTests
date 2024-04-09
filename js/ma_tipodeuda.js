if(document.getElementById('ma_tipodeuda')){
    $(document).ready(function(){
        $(document).on('click',"#almacenar-tipodeuda",function(e){
            e.preventDefault()
            // console.log("Edicion")
                let nombre = $("#tdnombre").val()
                if(nombre.length == 0){
                    Swal.fire({
                            icon: 'error',
                            title: "Debe ingresar nombre del Tipo de Deuda",
                        })
                }else{
                    $.ajax({
                    url: './?action=ma_tipodeuda',
                    type: 'POST',
                    data: $("#form-tipodeuda").serialize(),
                    success: function(rec){
                      console.log(rec)
                                 let res = JSON.parse(rec)
                                 if(rec.substr(0,1) == 0){
                                     Swal.fire({
                                         icon: 'error',
                                         title: rec.substr(2),
                                     })
                                 }else{
                                     Swal.fire({
                                         icon: 'success',
                                         title: rec.substr(2),
                                     }).then((result) => {
                                         if (result.isConfirmed) {
                                          location.href='./?view=listTipoDeudas'    
                                                   }
                                                 })
                                    
                                 }
                    }
                })
                }
                
            })
         $(".eliminar-tipodeuda").click(function (e) {
            e.preventDefault()
            let id = $(this).attr('tdid')
            //let tipo = $(this).attr('tdtipo')
            let nombre = $(this).attr('tdnombre');
            let row = $(this)
            // console.log(nombre, id)
            Swal.fire({
                title: 'Eliminar Registro?',
                text: "Desea eliminar el Tipo de deuda " + nombre + " ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: './?action=ma_tipodeuda',
                        type: 'POST',
                        data: {delete: 1, id: id},
                        success:function(rec){
                            console.log(rec)
                            //let res = JSON.parse(respuesta)
                            if(rec.substr(0,1) == 0){
                                    Swal.fire({
                                        icon: 'error',
                                        title: rec.substr(2),
                                    })
                                }else{
                                    Swal.fire({
                                        icon: 'success',
                                        title: rec.substr(2),
                                    }).then((result) => {
                                      if (result.isConfirmed) {
                                        location.href='./?view=listTipoDeudas'    
                                      }
                                    })
                                }
                        }
                    })
                }
            })
        })    
        
    })
}