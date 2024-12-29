angular.module('registrationApp', [])
    .config(['$interpolateProvider', function ($interpolateProvider) {
        $interpolateProvider.startSymbol('[[');
        $interpolateProvider.endSymbol(']]');
    }])
    .controller('RegistrationController', ['$scope', '$http', function ($scope, $http) {
        // Initialize form state
        $scope.formData = {};
        $scope.errors = {};
        $scope.isSubmitting = false;

        // Required fields validation
        const REQUIRED_FIELDS = [
            //'surname', 'firstName', 'gender', 'bloodGroup', 'birthday',
            //'nationality', 'stateOfOrigin', 'stateOfResidence', 'email',
            //'phoneNumber', 'contactAddress', 'myHeight', 'myWeight',
            //'emergencyPhoneNumber', 'passport', 'confirm'
        ];

        // Client-side validation
        function validateForm() {
            const errors = {};

            // Required fields validation
            REQUIRED_FIELDS.forEach(field => {
                if (!$scope.formData[field]) {
                    errors[field] = `${field.charAt(0).toUpperCase() +
                        field.slice(1).replace('my', '')} is required`;
                }
            });

            // Email validation
            if ($scope.formData.email &&
                !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($scope.formData.email)) {
                errors.email = 'Invalid email format';
            }

            // Phone number validation
            if ($scope.formData.phoneNumber &&
                !/^\+?[\d\s-]{10,}$/.test($scope.formData.phoneNumber)) {
                errors.phoneNumber = 'Invalid phone number format';
            }

            return errors;
        }

        // Debounce helper
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Validate field on change (debounced)
        $scope.validateField = debounce((fieldName) => {
            const errors = validateForm();
            $scope.$apply(() => {
                $scope.errors[fieldName] = errors[fieldName] || null;
            });
        }, 300);

        $scope.submitForm = async function () {
            if ($scope.isSubmitting) return;

            try {
                $scope.isSubmitting = true;
                $scope.errors = {};

                // Validate all fields
                const errors = validateForm();
                if (Object.keys(errors).length > 0) {
                    $scope.errors = errors;
                    return;
                }

                // Create form data
                const formData = new FormData();
                Object.entries($scope.formData).forEach(([key, value]) => {
                    if (value !== null && value !== undefined) {
                        formData.append(key, value);
                    }
                });

                // Submit form
                const response = await $http.post('/api/register', formData, {
                    headers: { 'Content-Type': undefined },
                    transformRequest: angular.identity
                });

                if (response.data.success) {
                    // Clear form
                    $scope.formData = {};
                    alert('Registration successful!');
                } else {
                    throw new Error(response.data.message || 'Registration failed');
                }

            } catch (error) {
                console.error('Registration error:', error);

                if (error.data?.errors) {
                    $scope.errors = error.data.errors;
                } else {
                    alert('An error occurred during submission. Please try again.');
                }

            } finally {
                $scope.isSubmitting = false;
                $scope.$apply();
            }
        };
    }]);