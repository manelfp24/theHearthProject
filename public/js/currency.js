// variables globales para guardar el valor del cambio y la moneda activa
let exchangeRateEUR = 1.0;
let currentCurrency = 'USD';

// función para obtener el tipo de cambio actual desde la api externa
async function fetchExchangeRates() {
    try {
        // usamos la clave de la api que hemos definido previamente en el header
        const apiKey = window.CURRENCY_API_KEY;
        // realizamos la petición a la api usando usd como base
        const response = await fetch(`https://v6.exchangerate-api.com/v6/${apiKey}/latest/USD`);
        const data = await response.json();

        // si la respuesta es correcta, guardamos el valor del euro
        if (data.result === "success") {
            exchangeRateEUR = data.conversion_rates.EUR;
            console.log("Rates updated: 1 USD =", exchangeRateEUR, "EUR");
            
            // si el usuario ya tenía seleccionado el euro en su sesión, lo aplicamos al cargar
            const savedCurrency = sessionStorage.getItem('selectedCurrency');
            if (savedCurrency === 'EUR') {
                changeCurrency('EUR');
            }
        }
    } catch (error) {
        // si hay un error con la api, lo mostramos en la consola
        console.error("Currency API Error:", error);
    }
}

// función que recorre la web y cambia todos los precios visibles
function changeCurrency(currency) {
    currentCurrency = currency;
    // guardamos la preferencia en el navegador para que no se pierda al navegar
    sessionStorage.setItem('selectedCurrency', currency);

    // actualizamos el texto del botón en el menú de navegación
    const btn = document.getElementById('currencyBtn');
    if (btn) {
        btn.innerText = (currency === 'USD') ? 'USD $' : 'EUR €';
    }

    // buscamos todos los elementos que tengan la clase product-price
    const priceElements = document.querySelectorAll('.product-price');

    priceElements.forEach(el => {
        // recuperamos el valor original en dólares desde el atributo data-usd
        const usdValue = parseFloat(el.getAttribute('data-usd'));
        
        if (isNaN(usdValue)) return;

        // si la moneda es euro, multiplicamos el precio por el cambio obtenido
        if (currency === 'EUR') {
            const converted = usdValue * exchangeRateEUR;
            el.innerText = '€' + converted.toFixed(2);
        } else {
            // si es dólar, volvemos a mostrar el valor original
            el.innerText = '$' + usdValue.toFixed(2);
        }
    });
}

// lanzamos la descarga de tipos de cambio en cuanto se carga el script
fetchExchangeRates();