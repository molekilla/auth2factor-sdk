function UserService($window, $q, $http) {
    
    UserService.login = function (model) {
        var data = {
            email: model.email,
            password: model.password
        };

        var deferred = $q.defer();

        $http.post(model.host + '/users/authenticate', data)
        .success(function (data, status, headers, config) {
            if (status === 404) {
                deferred.reject({
                    status: status,
                    message: 'Contraseña o correo desconocido',
                    success: false
                });
            } 
            else if (status === 400) {
                deferred.reject({
                    status: status,
                    message: 'Contraseña o correo desconocido',
                    success: false
                });
            } 
            else if (status === 201) {
                deferred.resolve({
                    status: status,
                    message: 'El código ha sido enviado con exito',
                    headers: headers(),
                    success: true,
                });
            }
            else {
                deferred.reject({
                    status: status,
                    message: 'Error. Código de estado HTTP ' + status,
                    success: false
                });
            }
        })
        .error(function (data, status, headers, config) {
            if (status === 404) {
                deferred.reject({
                    status: status,
                    message: 'Contraseña o correo desconocido',
                    success: false
                });
            } 
            else if (status === 400) {
                deferred.reject({
                    status: status,
                    message: 'Contraseña o correo desconocido',
                    success: false
                });
            } 
            else {
                deferred.reject({
                    status: status,
                    message: 'Error. Código de estado HTTP ' + status,
                    success: false
                });
            }
        });

        return deferred.promise;
    };

    UserService.otpVerification = function (model) {
        var data = {
            code: model.code
        };

        var deferred = $q.defer();

        $http.post(model.host + '/users/otc', data, {
            headers: {
                authorization: "Bearer " + model.token
            }
        })
        .success(function (data, status, headers, config) {
            if (status === 404) {
                deferred.reject({
                    status: status,
                    message: 'No se encontro el recurso solicitado',
                    success: false
                });
            } 
            else if (status === 400) {
                deferred.reject({
                    status: status,
                    message: 'Código Incorrecto',
                    success: false
                });
            } 
            else if (status === 201) {
                deferred.resolve({
                    status: status,
                    message: 'El código ha sido verificado con exito',
                    headers: headers(),
                    success: true,
                });
            } 
            else {
                deferred.reject({
                    status: status,
                    message: 'Error. Código de estado HTTP ' + status,
                    success: false
                });
            }
        })
        .error(function (data, status, headers, config) {
            if (status === 401) {
                deferred.reject({
                    status: status,
                    message: 'Token o autenticación Expirada',
                    success: false
                });
            } 
            else if (status === 400) {
                deferred.reject({
                    status: status,
                    message: 'Código Incorrecto',
                    success: false
                });
            } 
            else {
                deferred.reject({
                    status: status,
                    message: 'Error. Código de estado HTTP ' + status,
                    success: false
                });
            }
        });

        return deferred.promise;
    };

    UserService.requestOtc = function (model) {
        var deferred = $q.defer();

        $http.get(model.host + '/users/otc', {
            headers: {
                authorization: "Bearer " + model.token
            }
        })
        .success(function (data, status, headers, config) {
            if (status === 404) {
                deferred.reject({
                    status: status,
                    message: 'No se encontro OTC',
                    success: false
                });
            } 
            else if (status === 400) {
                deferred.reject({
                    status: status,
                    message: 'Solicitud Incorrecta',
                    success: false
                });
            } 
            else if (status === 401) {
                deferred.reject({
                    status: status,
                    message: 'Accesso no autorizado',
                    success: false
                });
            }
            else if (status === 200) {
                deferred.resolve({
                    status: status,
                    message: 'El código ha sido enviado',
                    success: true
                });
            } 
            else {
                deferred.reject({
                    status: status,
                    message: 'Error. Código de estado HTTP ' + status,
                    success: false
                });
            }
        })
        .error(function (data, status, headers, config) {
            if (status === 404) {
                deferred.reject({
                    status: status,
                    message: 'No se encontro OTC',
                    success: false
                });
            } 
            else if (status === 400) {
                deferred.reject({
                    status: status,
                    message: 'Solicitud Incorrecta',
                    success: false
                });
            } 
            else if (status === 401) {
                deferred.reject({
                    status: status,
                    message: 'Accesso no autorizado',
                    success: false
                });
            }
            else {
                deferred.reject({
                    status: status,
                    message: 'Error. Código de estado HTTP ' + status,
                    success: false
                });
            }
        });

        return deferred.promise;
    };

    return UserService;
}


angular
    .module('auth2factor_angular_sdk')
    .factory('UserService', UserService);