export function loadActiveModulo(classRuleta, classActive, classInActiv) {
    let ruletas = document.getElementsByClassName(classRuleta)
    let r = ''
    for (let step = 0; step < ruletas.length; step++) {
        if (ruletas[step].id.substr(-1) == 1) {
            r = $("#" + ruletas[step].id + "").removeClass(classInActiv).addClass(classActive)
        }
    }
    return r
}

export function getIdActiveBtn(classNam, classActive) { // function para activar el
    let puntos = document.getElementsByClassName(classNam);
    let valId = ''
    for (let step = 0; step < puntos.length; step++) {
        if (puntos[step].classList.contains(classActive)) {
            valId = puntos[step].id
        }
    }
    return valId;
}


export function getIdActive(classRuleta, classActiveBlock) {
    let ruletas = document.getElementsByClassName(classRuleta);
    let valId = ''
    for (let step = 0; step < ruletas.length; step++) {
        if (ruletas[step].classList.contains(classActiveBlock)) {
            valId = ruletas[step].id
        }
    }
    return valId;
}

let nameClassSgt = "btnSgt"

/*$(".carousel-bullet").each(function () {
    if (this.hasClass("btnSgt-1")) {
        console.log(this.classList)
    }
});*/

export function loadBotton(idElementForPuntos, classActive, ClassInActive, classRuleta) {

    let ruletas = document.getElementsByClassName(classRuleta)
    let html = '<nav aria-label="">' +
        '<ul class="pager" id="pager">' +
        '<li><a href="#" id="anterior">Anterior</a></li>';

    html += '<li><a href="#" id="siguiente">Siguiente</a></li>' +
        '</ul>' +
        '</nav>';
    $("#" + idElementForPuntos + "").html(html);
}

export function nextButtom(classRuleta, classNam, classActiveBlock, classInActBlock, classActivePto, classInActPto) {
    // console.log(classRuleta, classNam, classActiveBlock, classInActBlock, classActivePto, classInActPto)
    let tr = document.getElementsByClassName(classRuleta);
    let idNameActive = getIdActive(classRuleta, classActiveBlock)
    // console.log(getIdActive(classRuleta,classActiveBlock))
    let numId = idNameActive.substr(-1);
    let newnum = ''
    if (numId == tr.length) {
        $("#" + idNameActive + "").removeClass(classActiveBlock).addClass(classInActBlock)
        newnum = 1;
        $("#" + idNameActive.substr(0, 6) + newnum + "").removeClass(classInActBlock).addClass(classActiveBlock)
    } else {
        $("#" + idNameActive + "").removeClass(classActiveBlock).addClass(classInActBlock)
        newnum = parseInt(numId) + 1;
        $("#" + idNameActive.substr(0, 6) + newnum + "").removeClass(classInActBlock).addClass(classActiveBlock)
    }

    let puntos = document.getElementsByClassName(classNam)
    let activ = ''
    let res = ''
    for (let step = 0; step < puntos.length; step++) {
        if ((step + 1) == newnum) {
            activ = step + 1
        }
    }
    if ($("." + nameClassSgt + "-" + numId).hasClass(classActivePto)) {
        $("." + nameClassSgt + "-" + numId).removeClass(classActivePto).addClass(classInActPto)
        $("." + nameClassSgt + "-" + activ).removeClass(classInActPto).addClass(classActivePto)
    } else {
        $("." + nameClassSgt + "-" + activ).removeClass(classInActPto).addClass(classActivePto)
    }
}

export function antButton(classRuleta, classNam, classActiveBlock, classInActBlock, classActivePto, classInActPto) {
    // ruleta , carousel-bullet , active-block , in-active , active-bt-act , active-bt
    // classRuleta,classActivePtoclassActivePto,classInActPto
    let tr = document.getElementsByClassName(classRuleta);
    let o = ''
    for (let step = 0; step < tr.length; step++) {
        let num = step + 1
        o += num + ','
    }
    let cdnSin = o.slice(0, -1);
    let ultimoNum = cdnSin.substr(-1); // obtengo el ultimo caracter
    let idNameActive = getIdActive(classRuleta, classActiveBlock);
    let numId = idNameActive.substr(-1); // Id del elemento que se da CLICK
    let newnum = ''
    if (numId == 1) {
        $("#" + idNameActive + "").removeClass(classActiveBlock).addClass(classInActBlock)
        newnum = ultimoNum;
        $("#" + idNameActive.substr(0, 6) + newnum + "").removeClass(classInActBlock).addClass(classActiveBlock)
    } else {
        $("#" + idNameActive + "").removeClass(classActiveBlock).addClass(classInActBlock)
        newnum = parseInt(numId) - 1;
        $("#" + idNameActive.substr(0, 6) + newnum + "").removeClass(classInActBlock).addClass(classActiveBlock)
    }

    let puntos = document.getElementsByClassName(classNam)
    let activ = ''
    let res = ''
    for (let step = 0; step < puntos.length; step++) {
        if ((step + 1) == newnum) {
            activ = step + 1
        }
    }
    if ($("." + nameClassSgt + "-" + numId).hasClass(classActivePto)) {
        $("." + nameClassSgt + "-" + numId).removeClass(classActivePto).addClass(classInActPto)
        $("." + nameClassSgt + "-" + activ).removeClass(classInActPto).addClass(classActivePto)
    } else {
        $("." + nameClassSgt + "-" + activ).removeClass(classInActPto).addClass(classActivePto)
    }
}

export class scriptRuleta {
    constructor() {
        this.clase = "clasePadre3"
        this.clase2 = "claseHijo"
        this.texto = ""
    }

    getClasePadre() {
        let total = 4
        let div = ""
        let colMd = Math.round(12 / this.texto.length)
        let arr_icono = this.getIcons()
        let punt = 0
        $.each(this.texto, function (i, item) {
            let active = ''
            if (punt == 0) {
                active = "active-bt-act"
            } else {
                active = "active-bt"
            }
            let puntero = arr_icono.indexOf(item.icono)
            div += '<div class="btnSgt-' + (punt + 1) + ' ' + active + ' carousel-bullet" style="display:flex;justify-content: space-evenly;">' +
                '<div class=""><img src="storage/logo/' + arr_icono[item.icono] + '" class="imagen-svg" style="width: 45px;"></div>' +
                '<div class="" style="padding: 0;font-size: 12px">' + item.texto + '</div>' +
                '</div>';
            punt++;
        });
        return div
    }

    getIcons() {
        let ar_Icons = new Array("archivos.svg", "caja.svg", "clientes-01.svg", "disco-flexible.svg")
        return ar_Icons
    }

}

