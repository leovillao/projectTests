/*============================================
Funcion para validar la fecha del documento en la ventana de pago
* ==========================================*/
export function validaFecha(fechapago ,fechadocumento){
    let now = new Date();
    let day = ("0" + now.getDate()).slice(-2);
    let month = ("0" + (now.getMonth() + 1)).slice(-2);
    let today = now.getFullYear()+"-"+(month)+"-"+(day) ;

    let diapago = fechapago
    let fechadoc = fechadocumento
    let result = "0-Fecha correcta"
    /*if(new Date(diapago).getTime() < new Date(fechadoc).getTime()){
        Swal.fire({
            icon:'error',
            title:'Fecha de Pago no puede ser menor a fecha del documento',
        })
        $('#modalPagoExpress #fechapago').val(today)
    }*/
    if(new Date(diapago).getTime() < new Date(fechadoc).getTime()){
        result = '1-'+today
    }
    return result
}
/*============================================
Funcion para validar el tipo de documento en la ventana de pago express
* ==========================================*/
export function validaTipoDocumento(tipoPago){
    let now = new Date();
    let day = ("0" + now.getDate()).slice(-2);
    let month = ("0" + (now.getMonth() + 1)).slice(-2);
    let today = now.getFullYear() + "-" + (month) + "-" + (day);
    let valor = tipoPago
    if (Number(valor) == 1) {
        $("#fechapago").attr('disabled', false)
    } else {
        $("#fechapago").attr('disabled', true)
    }
    $.ajax({
        url: '?action=loadEntidades',
        type: 'POST',
        data: {id: valor},
        success: function (response) {
            $("#entidades").html(response)
        }
    })
    if (Number(valor) == 1) {
        $("#labelNumero").text('# de Cheque')
    } else if (Number(valor) == 2 || Number(valor) == 3) {
        $("#labelNumero").text('# de Referencia')
    } else if (Number(valor) == 4) {
        $("#labelNumero").text('# de Voucher')
    }
    let hoy = $("#fechareg").val()
    let fechadoc = $("#fechadoc").val()
    if (new Date(hoy).getTime() < new Date(fechadoc).getTime()) {
        Swal.fire({
            icon: 'error',
            title: 'Fecha de registro no puede ser menor a fecha del documento',
        })
        $('#modalPagoExpress #fechareg').val(today)
    }
}

export function SelectCliente() {
    $('#cliente').select2({
        placeholder: "Seleccione cliente..."
    });
}

export default class mensajes {

    constructor(msj) {
        this.msj = msj;
    }

    confirmed() {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: this.msj,
            showConfirmButton: true,
            // timer: 1500
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {

            }
        })
    }

    error() {
        Swal.fire({
            position: 'top-end',
            icon: 'error',
            title: this.msj,
            showConfirmButton: true,
            // timer: 1500
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {

            }
        })
    }
}