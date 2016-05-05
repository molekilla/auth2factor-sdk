IndexCtrl.$inject = ['$scope', '$http', 'SecurityKeyService',
    'UserService', 'U2F', 'FailUnlessResolvedWithin'];

function IndexCtrl($scope, $http, SecurityKeyService,
    UserService, U2F, FailUnlessResolvedWithin) {

    $scope.u2f = {
        inputAPIKey: ''
    };

    $scope.authWithU2F = function () {

        var options = {
            token: $scope.auth.appSignRequest,
            host: $scope.creds.host
        };

        $scope.authu2f = {
            deviceStatus: ''
        };

        var signReqs = JSON.parse($scope.auth.u2fSignRequest);
        if (signReqs) {
            $scope.authu2f.deviceStatus = 'Ingresa dispositivo...';
            FailUnlessResolvedWithin(function (promise) {
                U2F.sign(signReqs, function (data) {
                    if (data.errorCode) {
                        promise.reject(data);
                    }

                    promise.resolve(data);
                });

                return promise;
            }, 40000)
                .then(function (result) {

                    $scope.authu2f.deviceStatus = '';
                    UserService
                        .u2fVerification(options, result)
                        .then(function (response) {
                            $scope.auth.resp.status = response.status;
                            $scope.auth.resp.message = response.message;
                            $scope.auth.resp.success = response.success.toString();
                        });

                }, function (error) {
                    $scope.authu2f.deviceStatus = 'Error code ' + error.errorCode + '.';
                    $scope.auth.resp.message = error.errorCode;
                    $scope.auth.resp.success = false;
                });
        }
    };


    $scope.registerU2F = function () {
        $scope.regu2f = {
            resp: {
                status: "-",
                message: "-",
                success: "",
            }
        };

        var options = {
            token: $scope.creds.apiKey,
            host: $scope.creds.host
        };

        $scope.regu2f.deviceStatus = '';
        SecurityKeyService
            .requestChallenge(options)
            .then(function (response) {
                if (response) {
                    $scope.regu2f.deviceStatus = 'Ingresa dispositivo...';
                    FailUnlessResolvedWithin(function (promise) {
                        U2F.register([response], [], function (data) {
                            if (data.errorCode) {
                                promise.reject(data);
                            }

                            promise.resolve(data);
                        });

                        return promise;
                    }, 40000)
                        .then(function (result) {

                            $scope.regu2f.deviceStatus = '';

                            SecurityKeyService
                                .register(options, result)
                                .then(function (options) {
                                    $scope.regu2f.deviceStatus = 'Dispositivo registrado';
                                });

                        }, function (error) {
                            $scope.regu2f.deviceStatus = 'Error code ' + error.errorCode + '.';
                        });
                }
            }, function (response) {
                $scope.regu2f.resp.status = response.status;
                $scope.regu2f.resp.message = response.message;
                $scope.regu2f.resp.success = response.success.toString();
            });
    };

    $scope.creds = $scope.creds || { host: 'https://localhost/api/v2' };

    $scope.examples = {
        authenticate: {
            "email": "example@example.com",
            "password": "password",
            "doRequestOtc": false
        },
        delegate: { account: 'test@gmail.com' },
        otc: { "code": "123456" },
        requestChallenge: 'https://a2f-local/api/v2/security_keys/challenge/Y_XlWGqz1NhJYs_G7hj74sACqopOWPP1kW7C75KIB60?U2F_V2',
        register: { "clientData": "...long string...", "registrationData": "...long string..." }
    };

    $scope.auth = {
        mail: "",
        password: "",
        url: '/users/authenticate',
        resp: {
            status: "-",
            message: "-",
            success: "",
        }
    };
    $scope.authenticateUser = function () {
        var email = $scope.auth.mail;
        var password = $scope.auth.password;

        UserService.login({
            email: email,
            password: password,
            doRequestOtc: $scope.auth.doRequestOtc === "true" ? true : false,
            host: $scope.creds.host
        })
            .then(function (response) {
                $scope.auth.appSignRequest = response.headers['x-app-sign-request'];
                $scope.auth.u2fSignRequest = response.headers['x-u2f-sign-request'];
                $scope.auth.resp.status = response.status;
                $scope.auth.resp.message = response.message;
                $scope.auth.resp.success = response.success.toString();
            }, function (response) {
                $scope.auth.resp.status = response.status;
                $scope.auth.resp.message = response.message;
                $scope.auth.resp.success = response.success.toString();
            });
    };

    $scope.delegate = function () {

        UserService.delegate({
            email: $scope.delegateAuth.mail,
            token: $scope.creds.apiKey,
            host: $scope.creds.host
        })
            .then(function (response) {
                $scope.auth.appSignRequest = response.headers['x-app-sign-request'];
                $scope.auth.u2fSignRequest = response.headers['x-u2f-sign-request'];
                $scope.auth.resp.status = response.status;
                $scope.auth.resp.message = response.message;
                $scope.auth.resp.success = response.success.toString();
            }, function (response) {
                $scope.auth.resp.status = response.status;
                $scope.auth.resp.message = response.message;
                $scope.auth.resp.success = response.success.toString();
            });
    };


    $scope.OTPVerification = {
        url: '/users/otc',
        resp: {
            status: "-",
            message: "-",
            success: "",
        }
    };
    $scope.verificationOTP = function () {
        var code = $scope.otc.code;

        UserService.otpVerification({
            code: code,
            token: $scope.otc.inputAPIKey,
            host: $scope.creds.host
        })
            .then(function (response) {
                $scope.OTPVerification.appBearer = response.headers['x-app-bearer'];
                $scope.OTPVerification.resp.status = response.status;
                $scope.OTPVerification.resp.message = response.message;
                $scope.OTPVerification.resp.success = response.success.toString();
            }, function (response) {
                $scope.OTPVerification.resp.status = response.status;
                $scope.OTPVerification.resp.message = response.message;
                $scope.OTPVerification.resp.success = response.success.toString();
            });
    };

    $scope.OTCrequest = {
        url: '/users/otc',
        resp: {
            status: "-",
            message: "-",
            success: "",
        }
    };
    $scope.requestOTC = function () {
        UserService.requestOtc({
            token: $scope.inputAPIKey,
            host: $scope.creds.host
        })
            .then(function (response) {
                $scope.OTCrequest.resp.status = response.status;
                $scope.OTCrequest.resp.message = response.message;
                $scope.OTCrequest.resp.success = response.success.toString();
            }, function (response) {
                $scope.OTCrequest.resp.status = response.status;
                $scope.OTCrequest.resp.message = response.message;
                $scope.OTCrequest.resp.success = response.success.toString();
            });
    };

    return IndexCtrl;
}


angular
    .module('auth2factor_angular_sdk')
    .controller('IndexCtrl', IndexCtrl);
