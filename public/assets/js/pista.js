$(document).ready(function () {
    $("#botonpista").click(peticionPista);
});

function peticionPista(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    $.ajax({
        type: "POST",
        url: "juego.php",
        dataType: "json",
        data: {botonpista: true},
        success: function (response)
        {
            muestraPista(response.letra);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });
}

function muestraPista(pista) {
    $("#pista").text(`La pista solicitada es: ${pista}`);
}