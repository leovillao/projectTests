
console.log(response)
if (response != null){
let res = JSON.parse(response)
let docs = 0
console.log(response)
if (res.documento != null) {
res.forEach(function (data, index) {
if (typeof (data.saldo) != "undefined") {
saldo1 = data.saldo
}
});
res.forEach(function (data, index) {
if (typeof (data.documento) !== "undefined") {
doc += data.documento + ','
}
});
docs = doc.substring(9)
}

if (docs.length >= 1) {
Swal.fire({
title: 'Este documento tiene asociados : ' + docs + ' Desea que estas tambien sean anuladas',
icon: 'warning',
showCancelButton: true,
confirmButtonColor: 'green',
cancelButtonColor: '#d33',
confirmButtonText: 'Si.'
}).then((result) => {
if (result.value) {
if (Number(saldo1) !== 0) {
Swal.fire({
title: 'Este documento tiene registado pago o abonos asociados, al anularlos estos se registraran como anticipos',
icon: 'warning',
showCancelButton: true,
confirmButtonColor: 'green',
cancelButtonColor: '#d33',
confirmButtonText: 'Si.'
}).then((result) => {
if (result.value) {
anularDocumento(id)
} else {
Swal.fire("Proceso cancelado",)
}
})
} else {
anularDocumento(id)
}
} else {
Swal.fire("Proceso cancelado",)
}
})
} else {
if (Number(saldo1) !== 0 || saldo1 != 'undefined') {
Swal.fire({
title: 'Este documento tiene registado pago o abonos asociados, al anularlos estos se registraran como anticipos',
icon: 'warning',
showCancelButton: true,
confirmButtonColor: 'green',
cancelButtonColor: '#d33',
confirmButtonText: 'Si.'
}).then((result) => {
if (result.value) {
anularDocumento(id)
} else {
Swal.fire("Proceso cancelado",)
}
})
} else {
anularDocumento(id)
}
} // fin condicion saldo
}else{ // Si no tiene documentos asociados ni abonos realizados
anularDocumento(id)
}
