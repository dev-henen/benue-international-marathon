angular.module('getSlipApp', [])
  .config(['$interpolateProvider', function ($interpolateProvider) {
    $interpolateProvider.startSymbol('[[').endSymbol(']]');
  }])
  .controller('SlipController', ['$scope', '$http', function ($scope, $http) {
    $scope.email = '';
    $scope.emailError = '';
    
    // RFC 5322 compliant email validation
    const emailRegex = /^(?:[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-zA-Z0-9-]*[a-zA-Z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$/;
    
    function validateEmail(email) {
      if (!email) {
        return 'Please enter an email address.';
      }
      
      // Remove any leading/trailing whitespace
      email = email.trim();
      
      if (email.length > 254) {
        return 'Email address is too long.';
      }
      
      if (!emailRegex.test(email)) {
        return 'Please enter a valid email address.';
      }
      
      return '';
    }
    
    $scope.getSlip = function () {
      $scope.emailError = validateEmail($scope.email);
      
      if ($scope.emailError) {
        return;
      }
      
      const payload = { email: $scope.email.trim() };
      
      $http.post('/api/get-registration-slip', payload)
        .then(function (response) {
          alert('A link to your registration slip has been sent to your email.');
          $scope.email = '';
        })
        .catch(function (error) {
          console.error('Error:', error);
          alert(error.data.message || 'An error occurred while trying to send the registration slip.');
        });
    };
  }]);