/*global _:false*/
function SecurityKeyService($log, $q, $http) {

    var route = '/security_keys';

    SecurityKeyService.requestChallenge = function (options) {
        var deferred = $q.defer();

        $http.post(options.host + route + '/challenge?view=location', null, {
            headers: {
                authorization: "Bearer " + options.token
            }
        })
        .success(function (data, status, headers, config) {
                if (status === 201) {
                    var location = headers()['location'];
                    var tuple = location.split('?');
                    var segments = tuple[0].substring(8).split('/');
                    deferred.resolve({
                        version: tuple[1],
                        appId: 'https://' + segments[0],
                        challenge: segments[segments.length - 1]
                    });
                } else if (status === 400) {
                    deferred.reject(data.message);
                } else {
                    deferred.reject(status);
                }
            })
            .error(function (data) {
                deferred.reject(data.message);
            });

        return deferred.promise;
    };

    SecurityKeyService.register = function (options, model) {
        var deferred = $q.defer();

        var data = {
            clientData: model.clientData,
            registrationData: model.registrationData
        };



        $http.post(options.host + route, data, {
            headers: {
                authorization: "Bearer " + options.token
            }
        })
        .success(function (data, status, headers, config) {
                if (status === 201) {
                    deferred.resolve({
                        done: true
                        //location: response.headers().location
                    });
                } else if (status === 400) {
                    deferred.reject(data.message);
                } else {
                    deferred.reject(status);
                }
            })
            .error(function (data) {
                deferred.reject(data.message);
            });

        return deferred.promise;
    };

    return SecurityKeyService;
}

angular
    .module('auth2factor_angular_sdk')
    .factory('SecurityKeyService', SecurityKeyService);
