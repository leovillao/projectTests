if(document.getElementById('listarecaudadores')){
        $(document).ready(function(){
            
        $(document).on('click', '.remove-recaudador', function(){
            let id= $(this).closest('tr').find('td:eq(0)').text()
            
            Swal.fire({
                title: 'Eliminar registro?',
                text:'Esta accion no se puede deshacer',
                icon:'warning',
                showCancelButton:'true',
                confirmButtonColor:'3085d6',
                cancelButtonColor:'#d33',
                confirmButtonText:'Si, confirmar',
            }).then((result)=>{
                if(result.isConfirmed){
                    $.ajax({
                        url:'./?action=processrecaudadores',
                        type:'POST',
                        data:{id:id,tipo:3},
                        success:function(resp){
                            let res = JSON.parse(resp)
                            if(res.substr(0,1) == 0){
                                Swal.fire({
                                    icon:'error',
                                    title:res.substr(2),
                                })
                            }else{
                                Swal.fire({
                                    icon:'success',
                                    title:res.substr(2),
                                }).then((result)=>{
                                    if(result.isConfirmed){
                                     location.href='./?view=listRecaudadores'   
                                    }
                                })
                            }
                        }
                    })
                }
            })
        })
            
            $("#almacenar-recaudador").click(function(e){
                e.preventDefault();
              
                let nombre = $("#renombre").val()
                if(nombre.length == 0){
                    Swal.fire({
                            icon: 'error',
                            title: "Debe ingresar nombre del Recaudador",
                        })
                }else{
                    $.ajax({
                    url: './?action=processrecaudadores',
                    type: 'POST',
                    data: $("#form-recaudadores").serialize(),
                    success: function(rec){
                       console.log(rec)
                                let res = JSON.parse(rec)
                                if(res.substr(0,1) === 0){
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
                                        location.href='./?view=listRecaudadores'    
                                                  }
                                                })
                                    
                                }
                    }
                })
                }
                
            })
            
        })


}