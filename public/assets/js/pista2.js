document.addEventListener("DOMContentLoaded", function () {
    const botonpista = document.getElementById('botonpista');
    botonpista.addEventListener("click", peticionPista);
});

function peticionPista(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    const form = e.target;
    var data = JSON.stringify({'botonpista': true});
    var objXMLHttpRequest = new XMLHttpRequest();
    objXMLHttpRequest.responseType = 'json';
    objXMLHttpRequest.onreadystatechange = function () {
        if (objXMLHttpRequest.readyState === 4) {
            if (objXMLHttpRequest.status === 200) {
                const response = objXMLHttpRequest.response;
                muestraPista(response.letra);
            }
        }
    };
    
    objXMLHttpRequest.open('GET', 'juego.php?botonpista');
    objXMLHttpRequest.send();
}

function muestraPista(pista) {
    const elementPista = document.getElementById('pista');
    elementPista.innerText=`La pista solicitada es: ${pista}`;
}


