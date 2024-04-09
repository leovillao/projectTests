if(document.getElementById('ma_transportistas')){
    $(document).ready(function(){
        $(document).on('click',"#almacenar-transportista",function(e){
            e.preventDefault()
            console.log("Edicion")
                let nombre = $("#trnombre").val()
                if(nombre.length == 0){
                    Swal.fire({
                            icon: 'error',
                            title: "Debe ingresar nombre del Transportista",
                        })
                }else{
                    $.ajax({
                    url: './?action=ma_transportistas',
                    type: 'POST',
                    data: $("#form-transportista").serialize(),
                    success: function(rec){
                       console.log(rec)
                                 //let res = JSON.parse(rec)
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
                                         location.href='./?view=listTransportistas'    
                                                  }
                                                })
                                    
                                }
                    }
                })
                }
                
            })
            
         $(".eliminar-transportista").click(function (e) {
            e.preventDefault()
            let id = $(this).attr('trid')
            //let tipo = $(this).attr('trtipo')
            let nombre = $(this).attr('trnombre');
            let row = $(this)
            // console.log(nombre, id)
            Swal.fire({
                title: 'Eliminar Registro?',
                text: "Desea eliminar el Transportista " + nombre + " ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: './?action=ma_transportistas',
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
                                        location.href='./?view=listTransportistas'    
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