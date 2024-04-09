if(document.getElementById('creditosfluid')){
    
    $(document).ready(function(){
        
        $(document).on('click', '.remove-creditos', function(){
            let id = $(this).closest('tr').find('td:eq(0)').text()
            Swal.fire({
                icon:'warning',
                title:"Eliminar crédito?",
                text:"Ésta acción no se puede deshacer",
                showCancelButton:'true',
                confirmButtonColor:'3085d6',
                cancelButtonColor:'#d33',
                confirmButtonText:'Si, confirmar',
            }).then((result)=>{
                if(result.isConfirmed){
                    $.ajax({
                        url:'./?action=processcreditos',
                        type:'POST',
                        data:{id:id, tipo:3},
                        success:function(respond){
                            let res = JSON.parse(respond)
                            if(res.substr(0,1)==0){
                                Swal.fire({
                                    icon:'error',
                                    title: res.substr(2),
                                })
                            }else{
                                Swal.fire({
                                    icon:'success',
                                    title: res.substr(2),
                                }).then((result)=>{
                                    if(result.isConfirmed){
                                        location.href='./?view=creditos'
                                    }
                                })
                            }
                        }
                    })
                }
            })
        })
        
        $("#save-modified-creditos").click(function(e){
            e.preventDefault()
            let codigo = $("#cod").val()
            if(codigo.length==0){
                Swal.fire({
                    icon:'error',
                    title:"Debe ingresar el código",
                })
            } else {
                let nombre = $("#name").val()
                if(nombre.length==0){
                Swal.fire({
                    icon:'error',
                    title:"Debe ingresar el nombre",
                })
                }else{
                    $.ajax({
                        url:'./?action=processcreditos',
                        type:'POST',
                        data: $("#form-creditos").serialize(),
                        success:function(respond){
                           //console.log(respond)
                           let creditos = JSON.parse(respond)
                            if(creditos.substr(0,1)==0){
                                Swal.fire({
                                    icon:'error',
                                    title:creditos.substr(2),
                                })
                            }else{
                                Swal.fire({
                                    icon:'success',
                                    title:creditos.substr(2),
                                }).then((result)=>{
                                    if(result.isConfirmed){
                                        location.href='./?view=creditos'
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