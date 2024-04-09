if(document.getElementById('ro_ralergias')){
    
    $(document).ready(function(){
        
        function loadalergias(){
            let ct = ''
            if(document.getElementById('alergia')){
                ct = $("#alergia").val()
            }
            $.ajax({
                url:'./?action=ro_processempleadosalergiasr',
                type:'POST',
                data:{option:1},
                success:function(respond){
                    let res = JSON.parse(respond)
                    viewHTML='<option value="">Seleccione...</option>'
                    selected =''
                    $.each(res, function(i, item){
                    if (ct != ''){
                            if (ct == item.id){
                                selected = "selected"
                            }else{
                                selected=''
                            }
                        }
                        viewHTML += '<option value="' +item.id + '"' +selected+'>' + item.name + '</option>'
                    })
                    $("#alid").html(viewHTML)
                }
            })
        }
        loadalergias()
        
        $(document).on('click', '.remove-ro_ralergias', function(){
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
              if(result.isConfirmed){
                  $.ajax({
                      url:'./?action=ro_processempleadosalergias',
                      type:'POST',
                      data:{id:id, tipo:3},
                      success:function(respond){
                          let res =JSON.parse(respond)
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
                                      location.href="./?view=ro_viewralergias&id="+dlt
                                  }
                              })
                          }
                      }
                  })
              }
          })
        })
        
        $("#save-modified-ro_empleadosalergias").on('click', function(e){
            let id = $("#idEmpleado").val()
            e.preventDefault()
            $.ajax({
                url:'./?action=ro_processempleadosalergias',
                type:'POST',
                data:$("#form-ro_ralergias").serialize(),
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
                                location.href="./?view=ro_viewralergias&id="+id
                            }
                        })
                    }
                }
            })
        })
        
    })
    
    
}