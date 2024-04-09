if(document.getElementById('ma_tiposcobros')){
    $(document).ready(function(){
        $(document).on('click',"#almacenar-tipocobro",function(e){
            e.preventDefault()
            console.log("Edicion")
                let nombre = $("#tcdescrip").val()
                if(nombre.length == 0){
                    Swal.fire({
                            icon: 'error',
                            title: "Debe ingresar nombre del Tipo de Cobro",
                        })
                }else{
                    $.ajax({
                    url: './?action=ma_tiposcobros',
                    type: 'POST',
                    data: $("#form-tipocobro").serialize(),
                    success: function(rec){
                       console.log(rec)
                                 let res = JSON.parse(rec)
                                if(res.substr(0,1) == 0){
                                    Swal.fire({
                                        icon: 'error',
                                        title: res.substr(2),
                                    })
                                }else{
                                    Swal.fire({
                                        icon: 'success',
                                        title: res.substr(2),
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                         location.href='./?view=listTipoCobros'    
                                                  }
                                                })
                                    
                                }
                    }
                })
                }
                
            })
            
         $(".eliminar-tipocobro").click(function (e) {
            e.preventDefault()
            let id = $(this).attr('tcid')
            //let tipo = $(this).attr('tdtipo')
            let nombre = $(this).attr('tcdescrip');
            let row = $(this)
            console.log(nombre, id)
            Swal.fire({
                title: 'Eliminar Registro?',
                text: "Desea eliminar el Tipo de cobro " + nombre + " ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: './?action=ma_tiposcobros',
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
                                        location.href='./?view=listTipoCobros'    
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