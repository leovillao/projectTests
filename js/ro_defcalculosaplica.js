if(document.getElementById('ro_defcalculosaplica')){
    
    $(document).ready(function(){
        
        function loadtiporol(){
            $.ajax({
                url:'./?action=ro_processdefaplicarcalculos',
                type:'POST',
                data: {option:1},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML='<option value="">Tipo Rol</option>'
                    $.each(res, function(i,item){
                        viewHTML +='<option value="' + item.id + '">' + item.name + '</option>'
                    })
                    $("#tiporol").html(viewHTML)
                }
            })
        }
        
        loadtiporol()
        
        $(document).on('change', "#tiporol", function(){
            let rol = $(this).val()
            $.ajax({
                url:'./?action=ro_processdefaplicarcalculos',
                type:'POST',
                data:{option:2, rol:rol},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML='<option value="">Seleccione...</option>'
                    $.each(res, function(i,item){
                        viewHTML +='<option value="' + item.id + '">' + item.name + '</option>'
                    })
                    $("#desde").html(viewHTML)
                    $("#hasta").html(viewHTML)
                    
                }
            })
        })
        
        function loadcalculos(){
            $.ajax({
                url:'./?action=ro_processdefaplicarcalculos',
                type:'POST',
                data: {option:3},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML='<option value="">Cálculos del Rol</option>'
                    $.each(res, function(i,item){
                        viewHTML +='<option value="' + item.id + '">' + item.name + '</option>'
                    })
                    $("#calculorol").html(viewHTML)
                }
            })
        }
        loadcalculos()
        
        $(document).on('change', '#calculorol', function(){
            let calculo = $(this).val()
            $.ajax({
                url:'./?action=ro_processdefaplicarcalculos',
                type:'POST',
                data:{option:4, calculo:calculo},
                success:function(respond){
                    let res = JSON.parse(respond)
                    $("#operacion").val(res[0])
                    $("#condicion").val(res[1])
                }
            })
        })
        
        $(document).on('click', '#procesarcalculos', function(){
            let tiporol = $("#tiporol").val()
            let desde = $("#desde").val()
            let hasta = $("#hasta").val()
            let calculo = $("#calculorol").val()
            let operacion = $("#operacion").val()
            let condicion = $("#condicion").val()
            $.ajax({
                url:'./?action=ro_updatedefaplicarcalculos',
                type:'POST',
                data:{tiporol:tiporol, desde:desde, hasta:hasta, calculo:calculo, operacion:operacion, condicion:condicion},
                success:function(respond){
                    //console.log(respond)
                    let res = JSON.parse(respond)
                    if(res.substr(0,1)==0){
                        Swal.fire({
                            icon:'error',
                            text:res.substr(2),
                        })
                    }else{
                        Swal.fire({
                            icon:'success',
                            title:res.substr(2),
                        }).then((result)=>{
                            if(result.isConfirmed){
                                location.href='./?view=ro_defcalculosaplica'   
                            }
                        })
                    }
                }
            })
        })
        
    })
    
}