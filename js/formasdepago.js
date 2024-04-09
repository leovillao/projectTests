if(document.getElementById('formasfluid')){
    $(document).ready(function(){
        
        $(document).on('click', '.remove-formas', function(){
            let id= $(this).closest('tr').find('td:eq(0)').text()
            // console.log(id); 
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
                        url:'./?action=processformas',
                        type:'POST',
                        data:{id:id,tipo:3},
                        success:function(resp){
                        //   console.log(resp)
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
                                     location.href='./?view=listFormas'   
                                    }
                                })
                            }
                        }
                    })
                }
            })
        })
        
        $('#guardar-formas').click(function(e){
            e.preventDefault()
            
            let nombre = $("#cfname").val()
            if(nombre.length==0){
                Swal.fire({
                    icon:'error',
                    text:"Debe ingresar el nombre",
                })
            }else{
                let codigo = $("#cfcodSri").val()
                if(codigo.length==0){
                    Swal.fire({
                        icon:'error',
                        text:"Debe ingresar el codigo",
                    })
                }else{
                    $.ajax({
                    url:'./?action=processformas',
                    type:'POST',
                    data:$('#form-formas').serialize(),
                    success:function(respon){
                        //console.log(respon)
                        let request = JSON.parse(respon)
                        if(request.substr(0,1)==0){
                            Swal.fire({
                                icon:'error',
                                text:request.substr(2),
                            })
                        }else{
                            Swal.fire({
                                icon:'success',
                                text: request.substr(2),
                            }).then((result)=>{
                                if(result.isConfirmed){
                                    location.href='./?view=listFormas'
                                }
                            })
                        }
                    }
                })
                }
            }
            
        })
        
    })
}