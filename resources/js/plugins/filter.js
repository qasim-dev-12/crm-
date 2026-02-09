import Vue from 'vue'
import store from '../store'

// return formatted number
Vue.filter('numberFormat', function (number) {
    if(number){
        return Number(number).toFixed(2);
    }
});

// return short text
Vue.filter('shortText', function (str) {
    if(str){
        return (str.length > 30) ? str.substr(0, 30-1) + ' ...' : str;
    }
});

// return formatted currency
Vue.filter('withCurrency', function (number) {
    const currency = store.state.operations.appInfo.currency;

    if (typeof number !== 'number') {
        number = Number(number);
    }

    if (isNaN(number)) return '';

    const decimals = currency.decimal_places ?? 2;
    const format = currency.format ?? 'en_US';

    const formats = {
        en_US: { thousand: ',', decimal: '.' },
        de_DE: { thousand: '.', decimal: ',' },
        fr_FR: { thousand: ' ', decimal: ',' },
    };

    const { thousand, decimal } = formats[format] || formats['en_US'];

    // Format the number
    function formatNumber(num, dec, thouSep, decSep) {
        let fixed = num.toFixed(dec);
        let parts = fixed.split('.');

        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thouSep);

        return parts.join(decSep);
    }

    const formattedNumber = formatNumber(number, decimals, thousand, decimal);

    if (number > 0) {
        return currency.position === 'left' ? currency.symbol + formattedNumber : formattedNumber + currency.symbol;
    } else {
        const zeroFormatted = formatNumber(0, decimals, thousand, decimal);
        return currency.position === 'left' ? currency.symbol + zeroFormatted : zeroFormatted + currency.symbol;
    }
});



// return formatted currency
Vue.filter('withAbsoluteCurrency', function (number) {
    let currency = store.state.operations.appInfo.currency
    if(number > 0){
        let newNumber = (Number(number).toFixed(2)).toLocaleString()
        return currency.position == 'left' ? currency.symbol + newNumber : newNumber + currency.symbol
    }else{
        let newNumber = (Number(number).toFixed(2)).toLocaleString()
        return currency.position == 'left' ? '-' + currency.symbol + Math.abs(number) : newNumber + currency.symbol
    }
    return
})


// return code with prefix
Vue.filter('withPrefix', function (code, prefix) {
    return prefix + '-' + code;
})
