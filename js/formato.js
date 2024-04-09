export function cargaFile(datos, puntero) {
    $.ajax({
        url: "./?action=ro_tablarolesexcelreader",
        type: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respond) {
            let res = JSON.parse(respond)
            if (res.substr(0, 1) == 0) {
                Swal.fire({
                    icon: "error",
                    showConfirmButton: false,
                    title: res.substr(2),
                    timer: 1000,
                    width: 250,
                    height: 250,
                    toast: true,
                })
            } else {
                Swal.fire({
                    icon: "success",
                    position: "top",
                    showConfirmButton: false,
                    title: res.substr(2),
                    showClass: {
                        popup: `
                                                     animate__animated
                                                     animate__fadeInUp
                                                     animate__faster
                                                `
                    },
                    timer: 2000,
                    width: 200,
                })
                let rol = $("#rolact").val()
                cargaDatosEmpleados(rol)
            }
        }
    })
}

function cargaDatosEmpleados(rol){
    $.ajax({
        url:'./?action=ro_processtablaroles',
        type:'POST',
        data:{option:3,tiporol:rol},
        success:function(respond){
            let res = JSON.parse(respond)
            let i=0
            let arrayfijo = document.getElementsByClassName("nametable")
            viewHTML=''
            $.each(res.empleados, function(i,item){
                let view1 = item.item1
                let view2 = item.item5
                let view3 = item.item7
                let view4 = item.item8
                let view5 = item.item9
                let view6 = item.item10
                let view7 = item.item18
                let viewsino = '<option value="">Seleccione</option>'
                let viewsino1 = '<option value="">Seleccione</option>'
                let viewsino2 = '<option value="">Seleccione</option>'
                let viewsino3 = '<option value="">Seleccione</option>'
                let viewsino4 = '<option value="">Seleccione</option>'
                let viewsino5 = '<option value="">Seleccione</option>'
                let viewsino6 = '<option value="">Seleccione</option>'
                let selected = '<option value="">Seleccione</option>'
                viewsino='<option value="">Seleccione</option>'
                viewHTML+='<tr><td>' + item.id +
                    '</td><td>' + item.costo +
                    '</td><td>' + item.empleado + ' ' + item.empleado1 +
                    '</td><td>' + item.empleado2 +
                    '</td><td>' + item.cargo +
                    '</td><td>' + '<select name="afiliacion" class="tablaroles" namecampo="'+arrayfijo[0].value+'" namDef="afiliacion" style="width:75px">'+
                    $.each(res.sino, function(i,item){
                        if(view1!=''){
                            if(view1 == item.id1){

                                selected = "selected"
                            }else{
                                selected = ''
                            }

                        }
                        viewsino+='<option value="'+item.id1+'"' +selected+'>'+item.name1+'</option>'
                    })


                    +viewsino+'</select>' +
                    '</td><td>' + '<input type="number" value="'+item.item2+'" class="tablaroles" namecampo="'+arrayfijo[1].value+'" name="diastrab" namDef="diastrab" id="diastrab" step="any" style=" width:140px">' +
                    '</td><td>' + '<input type="number" value="'+item.item3+'" class="tablaroles" namecampo="'+arrayfijo[2].value+'" name="retju" namDef="retju" id="retju" style=" step="any" width:140px">' +
                    '</td><td>' + '<input type="number" value="'+item.item4+'" class="tablaroles" namecampo="'+arrayfijo[3].value+'" name="sueldobass" namDef="sueldobass" id="sueldobass" step="any" style=" width:140px">' +
                    '</td><td>' + '<select name="quincena" class="tablaroles" namecampo="'+arrayfijo[4].value+'" namDef="quincena" style="width:75px">'+
                    $.each(res.sino, function(i,item){
                        if(view2!=''){
                            if(view2 == item.id1){

                                selected = "selected"
                            }else{
                                selected = ''
                            }

                        }
                        viewsino1+='<option value="'+item.id1+'"'+selected+'>'+item.name1+'</option>'
                    })
                    +viewsino1+'</select>' +
                    '</td><td>' + '<input type="number" value="'+item.item6+'" class="tablaroles" namecampo="'+arrayfijo[5].value+'" name="quincena1" namDef="quincena1" id="quincena1" step="any" style=" width:140px">' +
                    '</td><td>' + '<select name="terol"  class="tablaroles" namecampo="'+arrayfijo[6].value+'" namDef="terol" style="width:75px">'+
                    $.each(res.sino, function(i,item){
                        if(view3!=''){
                            if(view3 == item.id1){

                                selected = "selected"
                            }else{
                                selected = ''
                            }

                        }
                        viewsino2+='<option value="'+item.id1+'"' +selected+'>'+item.name1+'</option>'
                    })
                    +viewsino2+'</select>' +
                    '</td><td>' + '<select name="terol" class="tablaroles" namecampo="'+arrayfijo[7].value+'" namDef="terol" style="width:75px">'+
                    $.each(res.sino, function(i,item){
                        if(view4!=''){
                            if(view4 == item.id1){

                                selected = "selected"
                            }else{
                                selected = ''
                            }

                        }
                        viewsino3+='<option value="'+item.id1+'"' +selected+'>'+item.name1+'</option>'
                    })
                    +viewsino3+'</select>' +
                    '</td><td>' + '<select name="frol" class="tablaroles" namecampo="'+arrayfijo[8].value+'" namDef="frol" style="width:75px">'+
                    $.each(res.sino, function(i,item){
                        if(view5!=''){
                            if(view5 == item.id1){

                                selected = "selected"
                            }else{
                                selected = ''
                            }

                        }
                        viewsino4+='<option value="'+item.id1+'"' +selected+'>'+item.name1+'</option>'
                    })
                    +viewsino4+'</select>' +
                    '</td><td>' + '<select name="cobcon" class="tablaroles" namecampo="'+arrayfijo[9].value+'" namDef="cobcon" style="width:75px">'+
                    $.each(res.sino, function(i,item){
                        if(view5!=''){
                            if(view5 == item.id1){

                                selected = "selected"
                            }else{
                                selected = ''
                            }

                        }
                        viewsino5+='<option value="'+item.id1+'"' +selected+'>'+item.name1+'</option>'
                    })
                    +viewsino5+'</select>' +
                    '</td><td>' + '<input type="number" value="'+item.item11+'" class="tablaroles" namecampo="'+arrayfijo[10].value+'" name="dquincena" namDef="dquincena" id="dquincena" style=" step="any" width:140px">' +
                    '</td><td>' + '<input type="number" value="'+item.item12+'" class="tablaroles" namecampo="'+arrayfijo[11].value+'" name="gdirvivienda" namDef="gdirvivienda" id="gdirvivienda" step="any" style=" width:140px">' +
                    '</td><td>' + '<input type="number" value="'+item.item13+'" class="tablaroles" namecampo="'+arrayfijo[12].value+'" name="gdirsalud" namDef="gdirsalud" id="gdirsalud" style=" step="any" width:140px">' +
                    '</td><td>' + '<input type="number" value="'+item.item14+'" class="tablaroles" namecampo="'+arrayfijo[13].value+'" name="gdireducacion" namDef="gdireducacion" id="gdireducacion" step="any" style=" width:140px">' +
                    '</td><td>' + '<input type="number" value="'+item.item15+'" class="tablaroles" namecampo="'+arrayfijo[14].value+'" name="gdiralimentacion" namDef="gdiralimentacion" id="gdiralimentacion" step="any" style=" width:140px">' +
                    '</td><td>' + '<input type="number" value="'+item.item16+'" class="tablaroles" namecampo="'+arrayfijo[15].value+'" name="gdirvestimenta" namDef="gdirvestimenta" id="gdirvestimenta" style=" step="any" width:140px">' +
                    '</td><td>' + '<input type="number" value="'+item.item17+'" class="tablaroles" namecampo="'+arrayfijo[16].value+'" name="decimocuarto" namDef="decimocuarto" id="decimocuarto" style=" step="any" width:140px">' +
                    '</td><td>' + '<select name="antprev" class="tablaroles" namecampo="'+arrayfijo[17].value+'" namDef="antprev" style="width:75px">'+
                    $.each(res.sino, function(i,item){
                        if(view7!=''){
                            if(view7 == item.id1){

                                selected = "selected"
                            }else{
                                selected = ''
                            }

                        }
                        viewsino6+='<option value="'+item.id1+'"' +selected+'>'+item.name1+'</option>'
                    })
                    +viewsino6+'</select>' +
                    '</td><td>' + '<input type="number" value="'+item.item19+'" class="tablaroles" namecampo="'+arrayfijo[18].value+'" name="viaticos" namDef="viaticos" id="viaticos" step="any" style=" width:140px">' +
                    '</td><td>' + '<input type="number" value="'+item.item20+'" class="tablaroles" namecampo="'+arrayfijo[19].value+'" name="dvacaciones" namDef="dvacaciones" id="dvacaciones" step="any" style=" width:140px">' +
                    '</td><td>' + '<input type="number" value="'+item.item21+'" class="tablaroles" namecampo="'+arrayfijo[20].value+'" name="lvacaciones" namDef="lvacaciones" id="lvacaciones" step="any" style=" width:140px">' +
                    '</td><td>' + '<input type="number" value="'+item.item22+'" class="tablaroles" namecampo="'+arrayfijo[21].value+'" name="pbvacaciones" namDef="pbvacaciones" id="pbvacaciones" step="any" style=" width:140px">' +
                    '</td><td>' + '<input type="number" value="'+item.item23+'" class="tablaroles" namecampo="'+arrayfijo[22].value+'" name="vacaciones" namDef="vacaciones" id="vacaciones" step="any" style=" width:140px">' +
                    '</td>'
            })
            $("#tiporoldata").html(viewHTML)
        }
    })
}