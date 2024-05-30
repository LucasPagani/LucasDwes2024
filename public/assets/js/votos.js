$(document).ready(function () {
    $("#verVotos").click(peticionVotos);
});

function peticionVotos(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    $.ajax({
        type: "POST",
        url: "index.php",
        dataType: "json",
        data: {verVotos: true}, // Ajuste en el parámetro enviado
        success: function (response) {
            muestraVotos(response.voto);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
            console.log("Respuesta del servidor (error):", xhr.responseText); // Depuración
        }
    });
}

function muestraVotos(voto) {
    $("#votosTotales").text(`Valoración media de nuestros usuarios: ${voto}`);
}
