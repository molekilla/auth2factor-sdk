IndexCtrl.$inject = ['$scope', '$http', 'UserService', 'U2F', 'FailUnlessResolvedWithin'];

function IndexCtrl($scope, $http, UserService, U2F, FailUnlessResolvedWithin) {

    $scope.u2f = {
        inputAPIKey: ''
    };
    
    $scope.testU2F = function (req) {
        req = req || $scope.u2f.inputAPIKey;
        
        FailUnlessResolvedWithin(function (promise) {
            U2F.sign(req, function (data) {
debugger
                if (data.errorCode) {
                    //dialog.close();
                    promise.reject(data.errorCode);
                }

                promise.resolve(data);
            });

            return promise;
        }, 40000).then(function (result) {

            //dialog.close();
            console.log(result);
        });
    };

    $scope.creds = $scope.creds || { host: 'https://domain/api/v2' };

    $scope.examples = {
        authenticate: {
            "email": "example@example.com",
            "password": "password",
            "doRequestOtc": false
        }
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
