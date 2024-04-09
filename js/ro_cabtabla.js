if(document.getElementById('ro_cabtabla')){
    
    
    $(document).ready(function(){
        
        
        $(document).on('change', '#tbtipo', function(){
            let tipo = $(this).val()
            if(tipo==3){
                $("#tbobligacion").attr('disabled', true)
            }else{
                $("#tbobligacion").removeAttr('disabled')
            }
        })
        
        let selected = $("#tbselected").val()
        obligacionupdate(selected)
        function obligacionupdate(selected){
            if(selected==3){
                $("#tbobligacion").attr('disabled', true)
            }else{
                $("#tbobligacion").removeAttr('disabled')
            }
        }
        
        $(document).on('click', '.remove-ro_cabtabla', function(){
            let id = $(this).closest('tr').find('td:eq(0)').text()
            Swal.fire({
                icon:'warning',
                title: 'Eliminar registro?',
                text:'Esta accion no se puede deshacer',
                showCancelButton:'true',
                confirmButtonColor:'3085d6',
                cancelButtonColor:'#d33',
                confirmButtonText:'Si, confirmar',
            }).then((result)=>{
                if(result.isConfirmed){
                    $.ajax({
                        url:'./?action=ro_processcabtabla',
                        type:'POST',
                        data:{id:id,tipo:3},
                        success:function(respond){
                            let res = JSON.parse(respond)
                            if(res.substr(0,1)==0){
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
                                        location.href='./?view=ro_viewcabtabla'
                                    }
                                })
                            }
                        }
                    })
                }
            })
        })
        
        
        $(document).on('click', '#save-modified-ro_cabtabla', function(){
            let descripcion = $("#tbdescrip").val()
            if(descripcion.length==0){
                Swal.fire({
                    icon:'warning',
                    title:'Debe ingresar el índice de tabla',
                })
            }else{
                $.ajax({
                    url:'./?action=ro_processcabtabla',
                    type:'POST',
                    data:$("#form-ro_cabtabla").serialize(),
                    success:function(respond){
                       let res = JSON.parse(respond)
                       if(res.substr(0,1)==0){
                           Swal.fire({
                               icon:'error',
                               title:res.substr(2),
                           })
                       }else{
                           Swal.fire({
                               icon:'success',
                               title:res.substr(2),
                           }).then((result)=>{
                               location.href='./?view=ro_viewcabtabla'
                           })
                       }
                   }
                })
            }
        })
        
    })
    
    
}