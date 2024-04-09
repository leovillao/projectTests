if(document.getElementById('ro_rdocumentos')){
    
    $(document).ready(function(){
        
        function loaddocumentos(){
            let ct =''
            if(document.getElementById('documentos')){
                ct = $("#documentos").val()
            }
            $.ajax({
                url:'./?action=ro_processempleadosdrData',
                type:'POST',
                data:{option:1},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML = '<option value="">Seleccione...</option>'
                    let selected = ''
                    $.each(res, function(i, item){
                        if(ct!==''){
                            if(ct==item.id){
                                selected="selected"
                            }else{
                                selected=''
                            }
                        }
                        viewHTML+='<option value="' +item.id+ '"' +selected+'>' +item.name+ '</option>'
                    })
                    $("#doid").html(viewHTML)
                }
            })
        }
        loaddocumentos()
        
        function inputFile(input){
            if(input.files && input.files[0]){
                var reader = new FileReader()
                
                reader.onload = function(e){
                    $('#miniwindow').html('<img src="'+e.target.result+'" width="145" height="100" align="center"/> ')
                }
                reader.readAsDataURL(input.files[0])
            }
        }
        $("#doimg").change(function(){
            inputFile(this)
        })
        
        $(document).on('click', '.remove-ro_rdocumentos', function(){
            let dlt = $("#idEmpleado").val()
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
                $.ajax({
                    url:'./?action=ro_processempleadosdr',
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
                                location.href="./?view=ro_viewrdocumentos&id="+dlt
                            })
                        }
                    }
                })
            })
        })
        
        $("#save-modified-ro_rdocumentos").on('click', function(e){
            let id = $("#idEmpleado").val()
            let datos = new FormData(document.getElementById('form-ro_rdocumentos'))
            $.ajax({
                url:'./?action=ro_processempleadosdr', 
                type:'POST',
                data: datos,
                contentType:false,
                cache:false,
                processData: false,
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
                                location.href="./?view=ro_viewrdocumentos&id="+id
                            }
                        })
                    }
                }
            })
        })
        
    })
    
}