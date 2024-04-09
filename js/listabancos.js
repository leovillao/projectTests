if(document.getElementById('listbancosform')){
    
    $(document).ready(function(){
        
        $(document).on('click', '.remove-bank', function(){
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
                        url:'./?action=processBancosList',
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
                                     location.href='./?view=listBancos'   
                                    }
                                })
                            }
                        }
                    })
                }
            })
        })
        
        
        $("#almacenar-banco").click(function(e){
            
            e.preventDefault()
            
            let nombre = $("#bcnombre").val()
            if(nombre.length === 0){
                Swal.fire({
                    icon:'error',
                    title:"Debe ingresar el nombre del Banco",
                })
            }else {
                let cuenta = $("#bcclase").val()
                if(cuenta.length === 0){
                    Swal.fire({
                        icon:'error',
                        title:"Debe seleccionar el tipo de Cuenta",
                    })
                } else{
                    let saldo = $("#bcsaldoini").val()
                    if(saldo.lenght === 0){
                        Swal.fire({
                            icon:'error',
                            title:"Debe ingresar el Saldo inicial",
                        })
                    }else{
                        $.ajax({
                            url: './?action=processBancosList',
                            type: 'POST',
                            data: $("#form-banco").serialize(),
                            success: function(respond){
                                
                                let res = JSON.parse(respond)
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
                                        location.href='./?view=listBancos'    
                                      }
                                    })
                                }
                            }
                        })
                    }
                }
            }
            
        })
        
    })
    
}