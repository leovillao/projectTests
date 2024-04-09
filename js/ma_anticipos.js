if(document.getElementById('ma_anticipos')){
    $(document).ready(function(){
        $(document).on('click',"#almacenar-tipoanticipo",function(e){
            e.preventDefault();
            console.log("Edicion");
                let nombre = $("#tanombre").val();
                if(nombre.length == 0){
                    Swal.fire({
                            icon: 'error',
                            title: "Debe ingresar nombre del Tipo de Anticipo",
                        });
                }else{
                    $.ajax({
                    url: './?action=ma_anticipos',
                    type: 'POST',
                    data: $("#form-tipoanticipo").serialize(),
                    success: function(rec){
                       console.log(rec);
                                 //let res = JSON.parse(rec)
                                if(rec.substr(0,1) == 0){
                                    Swal.fire({
                                        icon: 'error',
                                        title: rec.substr(2),
                                    });
                                }else{
                                    Swal.fire({
                                        icon: 'success',
                                        title: rec.substr(2),
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                         location.href='./?view=listTipoAnticipos';    
                                                  }
                                                })
                                    
                                }
                    }
                })
                }
                
            });
         $(".eliminar-tipoanticipo").click(function (e) {
            e.preventDefault()
            let id = $(this).attr('taid')
            //let tipo = $(this).attr('tdtipo')
            let nombre = $(this).attr('tanombre');
            let row = $(this)
            // console.log(nombre, id)
            Swal.fire({
                title: 'Eliminar Registro?',
                text: "Desea eliminar el Tipo de anticipo " + nombre + " ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: './?action=ma_anticipos',
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
                                        location.href='./?view=listTipoAnticipos'    
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