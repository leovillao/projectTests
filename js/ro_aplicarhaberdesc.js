if(document.getElementById("ro_aplicarhaberdesc")){
    
    
    $(document).ready(function(){
        
        
        function loadtiporol(){
            $.ajax({
                url:'./?action=ro_processaplicarhaberdesc',
                type:'POST',
                data: {option:1},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML='<option value="">Tipo Rol</option>'
                    $.each(res, function(i,item){
                        viewHTML +='<option value="' + item.id + '">' + item.name + '</option>'
                    })
                    $("#rolact").html(viewHTML)
                }
            })
        }
        
        loadtiporol()
        
        $(document).on('change', '#rolact', function(){
            let rolData = $(this).val()
            $.ajax({
                url:'./?action=ro_processaplicarhaberdesc',
                type:'POST',
                data:{option:2, rolData:rolData},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML =''
                    $("#peidhidden").val(res.data[0])
                    $("#peid").val(res.data[2])
                    $("#anio").val(res.data[1])
                    $("#aniohidden").val(res.data[1])
                    
                    $.each(res.detcab, function(i,item){
                        let checked =''
                        let hdaid = item.hdaid
                        if(item.hdaestado==1){
                            checked = "checked"
                        }
                       viewHTML += '<tr><td><input type="text" style="width:25px" name="hdaid" value="'+item.hdaid+'" id="hdaid" hidden>' + item.id + 
                       '</td><td>' + item.detalle + 
                       '</td><td>' + item.empleado + 
                       '</td><td><input type="text" style="width:25px" name="hdaid" value="'+item.ddid+'" id="hdaid" hidden>' + item.cuota + 
                       '</td><td>' + item.periodo + 
                       '</td><td>' + item.anio +
                       '</td><td style="text-align:right">' + item.valor +
                       '</td><td><input type="text" class="aplicarhidden" style="width:100px" value="'+item.loadvalor+'" name="aplicarhidden" hidden><input type="text" class="aplicards" value="'+item.loadvalor+'" style="text-align:right; width:100px" name="aplicards" disabled>' +
                       '</td><td><input type="checkbox" value="'+item.hdaestado+'"'+checked+' class="aplicar" name="aplicarvalor">'
                   })
                   $("#haberdescdata").html(viewHTML)
                }
            })
        })
        
        $(document).on('change','.aplicar',function(){
            //let id = $(this).closest('tr').find('td:eq(0)').text()
            if($(this).is(':checked')){
                let valor = $(this).closest('tr').find('td:eq(6)').text()
                $(this).closest('tr').find('.aplicards').val(valor)
                $(this).closest('tr').find('.aplicarhidden').val(valor)
            }else{
                $(this).closest('tr').find('.aplicards').val(0)
                $(this).closest('tr').find('.aplicarhidden').val(0)
            }
            
        })
        
        $(document).on('click', '#save-data', function(){
            let formDatos = new FormData()
            $("#haberdescdata tr").each(function(){
                let row = $(this)
                let hdaid = row.find('td:eq(0)').find('input').val()
                let ddid = row.find('td:eq(3)').find('input').val()
                let valor = row.find('td:eq(7)').find('input:not(:disabled)').val()
                let hdavalor = row.find('td:eq(7)').find('input:not(:disabled)').val()
                let estado = 0
                if(row.find('td:eq(8)').find('input').is(':checked')){
                    estado = 1
                }else{
                    estado = 2
                }
                let hdaestado = estado
                formDatos.append("hdaid[]", hdaid)
                formDatos.append("ddid[]", ddid)
                formDatos.append("hdavalor[]", hdavalor)
                formDatos.append("hdaestado[]", hdaestado)
            })
            formDatos.append("rolact", $("#rolact").val())
            formDatos.append("peid", $("#peidhidden").val())
            formDatos.append("hdanio", $("#aniohidden").val())
            formDatos.append("numberdata", $("#numberdata").val())
            $.ajax({
                url:'./?action=ro_processaplicarhaberdescform',
                type:'POST',
                data:formDatos,
                contentType:false,
                cache:false,
                processData: false,
                success:function(respond){
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
                                location.href='./?view=ro_aplicarhaberdesc'
                            }
                        })
                    }
                }
            })
        })
        
        
    })
    
    
}