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

        // Required fields for validation
        const REQUIRED_FIELDS = [
            'surname', 'firstName', 'gender', 'bloodGroup', 'birthday',
            'nationality', 'stateOfOrigin', 'stateOfResidence', 'email',
            'phoneNumber', 'contactAddress', 'myHeight', 'myWeight',
            'emergencyPhoneNumber', 'confirm'
        ];

        // Client-side validation function
        function validateForm() {
            const errors = {};

            // Validate required fields except passport
            REQUIRED_FIELDS.forEach(field => {
                if (!$scope.formData[field] && !($scope.formData[field] === 0)) {
                    errors[field] = `${capitalizeField(field)} is required`;
                }
            });

            // Separate passport validation
            const fileInput = document.querySelector('input[type="file"][name="passport"]');
            if (!fileInput || !fileInput.files || !fileInput.files[0]) {
                errors.passport = 'Passport is required';
            } else {
                const file = fileInput.files[0];
                const validFileTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                if (!validFileTypes.includes(file.type)) {
                    errors.passport = `Invalid file type. Allowed types: ${validFileTypes.join(', ')}`;
                }
            }

            // Email validation
            if ($scope.formData.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($scope.formData.email)) {
                errors.email = 'Invalid email format';
            }

            // Phone number validation
            if ($scope.formData.phoneNumber && !/^\+?[\d\s-]{10,}$/.test($scope.formData.phoneNumber)) {
                errors.phoneNumber = 'Invalid phone number format';
            }

            return errors;
        }

        // Function to convert file to base64
        function fileToBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => {
                    // Get the base64 string without the data URL prefix
                    const base64String = reader.result.split(',')[1];
                    resolve({
                        name: file.name,
                        type: file.type,
                        size: file.size,
                        base64: base64String
                    });
                };
                reader.onerror = reject;
                reader.readAsDataURL(file);
            });
        }

        // Capitalize the first letter of a field name for error messages
        function capitalizeField(field) {
            return field.charAt(0).toUpperCase() + field.slice(1).replace('my', '');
        }

        // Debounce helper for validating fields on change
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

        // Validate individual field when user types (debounced)
        $scope.validateField = debounce((fieldName) => {
            const errors = validateForm();
            if (!$scope.$$phase) {
                $scope.$apply(() => {
                    $scope.errors[fieldName] = errors[fieldName] || null;
                });
            }
        }, 300);

        // Submit the form
        $scope.submitForm = async function () {
            if ($scope.isSubmitting) return;

            try {
                $scope.isSubmitting = true;
                $scope.errors = {};

                // Validate all fields
                const errors = validateForm();
                if (Object.keys(errors).length > 0) {
                    $scope.errors = errors;
                    if (!$scope.$$phase) {
                        $scope.$digest();
                    }
                    return;
                }

                // Get the file input
                const fileInput = document.querySelector('input[type="file"][name="passport"]');
                const file = fileInput.files[0];

                // Convert file to base64
                const fileData = await fileToBase64(file);

                // Prepare the data for submission
                const submitData = {
                    ...$scope.formData,
                    passport: fileData
                };

                // Submit as JSON
                const response = await $http.post('/api/register', JSON.stringify(submitData), {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (response.data.success) {
                    alert('Registration successful!');
                    // $scope.formData = {};
                    // fileInput.value = '';
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
                if (!$scope.$$phase) {
                    $scope.$apply();
                }
            }
        };
    }]);