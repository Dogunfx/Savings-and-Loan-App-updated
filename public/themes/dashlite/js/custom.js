$(document).ready(function () {

})

function isDecimalKey(el, evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    var number = el.value.split('.');
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46 &&
        ((charCode != 65 && charCode != 86 && charCode != 67 && charCode != 99 && charCode != 120 && charCode != 118 && charCode != 97))) {
        alert("Only numbers or decimals are allowed");
        return false;
    }
    //1 decimal allowed
    if (number.length > 1 && charCode == 46) {
        return false;
    }

    var caratPos = getSelectionStart(el);
    var dotPos = el.value.indexOf(".");
    if (caratPos > dotPos && dotPos > -1 && (number[1].length > 1) && (charCode > 31)) {
        return false;
    }
    return true;
}
function isInterestKey(el, evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    var number = el.value.split('.');
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46) {
        alert("Only numbers or decimals are allowed");
        return false;
    }
    //1 decimal allowed
    if (number.length > 1 && charCode == 46) {
        return false;
    }

    var caratPos = getSelectionStart(el);
    var dotPos = el.value.indexOf(".");
    if (caratPos > dotPos && dotPos > -1 && (number[1].length > 3) && (charCode > 31)) {
        return false;
    }
    return true;
}

function getSelectionStart(o) {
    if (o.createTextRange) {
        var r = document.selection.createRange().duplicate()
        r.moveEnd('character', o.value.length)
        if (r.text == '') return o.value.length
        return o.value.lastIndexOf(r.text)
    } else return o.selectionStart
}
function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57) && (charCode != 65 && charCode != 86 && charCode != 67 && charCode != 99 && charCode != 120 && charCode != 118 && charCode != 97)) {
        alert("Only numbers are allowed");
        return false;
    }
    return true;
}