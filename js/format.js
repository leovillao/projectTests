function aplicate(){

    let id = $("#id_etiq_modal").val()
    let tipo = $("#id_tipo").val()
    let etiqueta = $("#etiq_id").val()
    let subetiqueta = "0"
    let unidad = "0"
    let costo = "0"
    let funcion = "0"
    let valor  = "0"
    let valorb = "0"
    let valors = "0"

    let tipoRet = TipoRet()
    if (document.getElementById("valor")){
        valor = $("#valor").val()
    }
    if (document.getElementById("subeti")){
        subetiqueta = $("#subeti").val()
    }
    if (document.getElementById("unidad")){
        unidad = $("#unidad").val()
    }
    if (document.getElementById("costo")){
        costo = $("#costo").val()
    }
    if (document.getElementById("funcion")){
        funcion = $("#funcion").val()
    }

    if (document.getElementById("valorb")){
        valorb = $("#valorb").val()
    }
    if (document.getElementById("valors")){
        valors = $("#valors").val()
    }
    /** se crea el array con los datos validados */
    let array = {id,tipo,etiqueta,subetiqueta,unidad,costo,funcion,tipoRet,valor,valors,valorb}
    let subtotal = Subtotal()
    let valorTotal = parseFloat(valor) + parseFloat(valorb) + parseFloat(valors)
    /* Validacion de los valores de acuerdo a sub total para el almacenamient*/

    if (localStorage.getItem('etiquetas') != null){
    let total = sumarTotales()
    let to = parseFloat(total) + parseFloat(valorTotal)
    if (parseFloat(to.toFixed(2)) <= parseFloat(subtotal).toFixed(2)){
        agregar(array)
        console.log(array.length);
        console.log(parseFloat(subtotal).toFixed(2) <= to.toFixed(2))
        console.log(to.toFixed(2))
        console.log(parseFloat(subtotal).toFixed(2))
        Swal.fire({
            icon:"success",
            title: "Etiqueta aplicada con exito."
        })
    }else{
        Swal.fire({
            icon:"error",
            title: "Valor ingresado no puede ser mayor al subtotal."
        })
    }
}else{
    if (parseFloat(valorTotal).toFixed(2) <= parseFloat(subtotal).toFixed(2)){
        agregar(array)
        Swal.fire({
            icon:"success",
            title: "Etiqueta aplicada con exito."
        })
    }else{
        Swal.fire({
            icon:"error",
            title: "Valor ingresado no puede ser mayor al subtotal."
        })
    }
}
}

function agregar(datos){
    let etiquetas = obtener()
    etiquetas.push(datos)
    let array= []
    localStorage.setItem('etiquetas',JSON.stringify(etiquetas))
}

function TipoRet(){
    let id =$("#subeti").val()
    let tipoRet = 60
    let resultado
    $.ajax({
        url:"?action=verifiqTipoRet",
        type:"POST",
        async: false,
        data:{id:id,tipoRet:tipoRet},
        success:function(res) {
            resultado = res
        }
    })
    return resultado
}

function Subtotal(){
    let id =$("#id_etiq_modal").val()
    let tipoRet = 62
    let resultado
    $.ajax({
        url:"?action=verifiqTipoRet",
        type:"POST",
        async: false,
        data:{id:id,tipoRet:tipoRet},
        success:function(res) {
            resultado = res
        }
    })
    return resultado
}

function obtener(){
    let etiquetas
    if (localStorage.getItem("etiquetas") == null){
    etiquetas = []
}else{
    etiquetas = JSON.parse(localStorage.getItem("etiquetas"))
}
return etiquetas
}

function sumarTotales() {
    let datos = JSON.parse(localStorage.getItem("etiquetas"))
    let total = 0
    datos.forEach(function(data, index) {
        total += parseFloat(data.valorb)+ parseFloat(data.valors) + parseFloat(data.valor)
    });
    return total
}