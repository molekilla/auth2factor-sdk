function U2FService() {
  return window.u2f;
}

angular
  .module('auth2factor_angular_sdk')
  .factory('U2F', U2FService);