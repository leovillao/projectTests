if(document.getElementById('legalform')){
    
    $(document).ready(function(){
        
        $(document).on('click', '.remove-legal', function(){
            let id = $(this).closest('tr').find('td:eq(0)').text()
            Swal.fire({
                icon:'warning',
                title:"Desea eliminar éste registro?",
                text:"Ésta acción no se puede deshacer",
                showCancelButton:'true',
                confirmButtonColor:'3085d6',
                cancelButtonColor:'#d33',
                confirmButtonText:'Si, confirmar',
            }).then((result)=>{
                if(result.isConfirmed){
                    $.ajax({
                        url:'./?action=processlegal',
                        type:'POST',
                        data :{id:id, tipo:3},
                        success:function(res){
                            let request = JSON.parse(res)
                            if(request.substr(0,1)==0){
                                Swal.fire({
                                    icon:'error',
                                    title:request.substr(2),
                                })
                            }else{
                                Swal.fire({
                                    icon:'success',
                                    title:request.substr(2),
                                }).then((result)=>{
                                    if(result.isConfirmed){
                                        location.href='./?view=legal'
                                    }
                                })
                            }
                        }
                    })
                }
            })
        })
        
        $("#save-modified-legal").click(function(e){
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
                        url:'./?action=processlegal',
                        type:'POST',
                        data: $("#form-legal").serialize(),
                        success:function(respond){
                           //  console.log(respond)
                           let legal = JSON.parse(respond)
                            if(legal.substr(0,1)==0){
                                Swal.fire({
                                    icon:'error',
                                    title:legal.substr(2),
                                })
                            }else{
                                Swal.fire({
                                    icon:'success',
                                    title:legal.substr(2),
                                }).then((result)=>{
                                    if(result.isConfirmed){
                                        location.href='./?view=legal'
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