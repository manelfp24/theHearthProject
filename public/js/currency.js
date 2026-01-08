// Global variables to store conversion data
let exchangeRateEUR = 1.0;
let currentCurrency = 'USD';

/**
 * Fetch the latest EUR rate relative to USD
 */
async function fetchExchangeRates() {
    try {
        // We use your API Key from the header
        const apiKey = window.CURRENCY_API_KEY;
        const response = await fetch(`https://v6.exchangerate-api.com/v6/${apiKey}/latest/USD`);
        const data = await response.json();

        if (data.result === "success") {
            exchangeRateEUR = data.conversion_rates.EUR;
            console.log("Rates updated: 1 USD =", exchangeRateEUR, "EUR");
            
            // If user previously chose EUR (saved in session), apply it now
            const savedCurrency = sessionStorage.getItem('selectedCurrency');
            if (savedCurrency === 'EUR') {
                changeCurrency('EUR');
            }
        }
    } catch (error) {
        console.error("Currency API Error:", error);
    }
}

/**
 * Updates all prices on the page
 * Looks for elements with class 'product-price' and a 'data-usd' attribute
 */
function changeCurrency(currency) {
    currentCurrency = currency;
    sessionStorage.setItem('selectedCurrency', currency);

    // Update the button text in the Navbar
    const btn = document.getElementById('currencyBtn');
    if (btn) {
        btn.innerText = (currency === 'USD') ? 'USD $' : 'EUR €';
    }

    // Find all price elements
    const priceElements = document.querySelectorAll('.product-price');

    priceElements.forEach(el => {
        const usdValue = parseFloat(el.getAttribute('data-usd'));
        
        if (isNaN(usdValue)) return;

        if (currency === 'EUR') {
            const converted = usdValue * exchangeRateEUR;
            el.innerText = '€' + converted.toFixed(2);
        } else {
            el.innerText = '$' + usdValue.toFixed(2);
        }
    });
}

// Start the fetch when the script loads
fetchExchangeRates();