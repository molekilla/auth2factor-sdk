

var u2fUtils = (function () {
    var u2fErrorMap = {
        1: 'Ha ocurrido un error, intente otra vez.',
        2: "Bad request error, try again",
        3: "This key isn't supported, please try another one",
        4: 'Dispositivo no activado',
        5: 'Tiempo de espera ha pasado, intente otra vez.'
    };

    var utils = {};
    utils.errorMap = u2fErrorMap;
    utils.getErrorDescription = function(idx) {
        return u2fErrorMap[idx];
    }

    return utils;
} ());