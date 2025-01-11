angular.module('registrationApp', [])
    .constant('CONFIG', {
        PAYSTACK_KEY: 'pk_test_03a119ce0db36dd02719332a3e85d5664cc43507',
        API_ENDPOINTS: {
            REGISTER: '/api/register',
            TRANSACTION: '/api/generate-transaction-reference',
            VERIFY: '/api/register/verify'
        },
        VALIDATION: {
            REQUIRED_FIELDS: [
                'surname', 'firstName', 'gender', 'bloodGroup', 'birthday',
                'nationality', 'stateOfOrigin', 'stateOfResidence', 'email',
                'phoneNumber', 'contactAddress', 'myHeight', 'myWeight',
                'emergencyPhoneNumber', 'confirm'
            ],
            FILE_TYPES: ['image/jpeg', 'image/png', 'application/pdf'],
            MAX_FILE_SIZE: 1 * 1024 * 1024 // 1MB
        }
    })
    .config(['$interpolateProvider', function ($interpolateProvider) {
        $interpolateProvider.startSymbol('[[');
        $interpolateProvider.endSymbol(']]');
    }])
    .factory('ValidationService', function () {
        return {
            validateEmail: function (email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            },
            validatePhone: function (phone) {
                return /^\+?[\d\s-]{10,}$/.test(phone);
            },
            validateFile: function (file, allowedTypes, maxSize) {
                if (!file) return { valid: false, error: 'File is required' };
                if (!allowedTypes.includes(file.type)) {
                    return { valid: false, error: `Invalid file type. Allowed: ${allowedTypes.join(', ')}` };
                }
                if (file.size > maxSize) {
                    return { valid: false, error: `File size exceeds ${maxSize / 1024 / 1024}MB limit` };
                }
                return { valid: true };
            },
            debounce: function (func, wait) {
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
        };
    })
    .factory('PaymentService', ['$http', 'CONFIG', function ($http, CONFIG) {
        function initializePaystack(transactionData) {
            return new Promise((resolve, reject) => {
                const handler = PaystackPop.setup({
                    key: CONFIG.PAYSTACK_KEY,
                    email: transactionData.email,
                    amount: transactionData.amount,
                    reference: transactionData.reference,
                    callback: (response) => resolve(response),
                    onClose: () => reject(), //reject(new Error('Payment window closed'))
                });
                handler.openIframe();
            });
        }

        return {
            processPayment: async function (email) {
                try {
                    const { data } = await $http({
                        method: 'POST',
                        url: CONFIG.API_ENDPOINTS.TRANSACTION,
                        data: { email },
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });

                    if (!data.success) throw new Error(data.error);

                    const paystackResponse = await initializePaystack(data.data);

                    const verificationResponse = await $http({
                        method: 'POST',
                        url: CONFIG.API_ENDPOINTS.VERIFY,
                        data: { reference: paystackResponse.reference },
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });

                    if (!verificationResponse.data.success) {
                        throw new Error(verificationResponse.data.error);
                    }

                    return verificationResponse.data;
                } catch (error) {
                    throw error;
                }
            }
        };
    }])
    .controller('RegistrationController', ['$scope', '$http', 'CONFIG', 'ValidationService', 'PaymentService',
        function ($scope, $http, CONFIG, ValidationService, PaymentService) {
            // Initialize state
            $scope.formData = {};
            $scope.errors = {};
            $scope.isSubmitting = false;
            $scope.isProcessing = false;

            // File handling
            const fileToBase64 = (file) => {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = () => {
                        resolve({
                            name: file.name,
                            type: file.type,
                            size: file.size,
                            base64: reader.result.split(',')[1]
                        });
                    };
                    reader.onerror = reject;
                    reader.readAsDataURL(file);
                });
            };

            // Prepare form data for submission
            const prepareFormData = (formData, fileData) => {
                const preparedData = { ...formData };
                if (preparedData.birthday && preparedData.birthday instanceof Date) {
                    preparedData.birthday = preparedData.birthday.toISOString().split('T')[0];
                }
                preparedData.passport = fileData;
                return preparedData;
            };

            // Validation
            const validateForm = () => {
                const errors = {};

                CONFIG.VALIDATION.REQUIRED_FIELDS.forEach(field => {
                    if (!$scope.formData[field] && !($scope.formData[field] === 0)) {
                        errors[field] = `${field.charAt(0).toUpperCase() + field.slice(1).replace('my', '')} is required`;
                    }
                });

                const fileInput = document.querySelector('input[type="file"][name="passport"]');
                if (fileInput?.files[0]) {
                    const fileValidation = ValidationService.validateFile(
                        fileInput.files[0],
                        CONFIG.VALIDATION.FILE_TYPES,
                        CONFIG.VALIDATION.MAX_FILE_SIZE
                    );
                    if (!fileValidation.valid) errors.passport = fileValidation.error;
                } else {
                    errors.passport = 'Passport is required';
                }

                if ($scope.formData.email && !ValidationService.validateEmail($scope.formData.email)) {
                    errors.email = 'Invalid email format';
                }
                if ($scope.formData.phoneNumber && !ValidationService.validatePhone($scope.formData.phoneNumber)) {
                    errors.phoneNumber = 'Invalid phone number format';
                }

                return errors;
            };

            $scope.validateField = ValidationService.debounce((fieldName) => {
                const errors = validateForm();
                if (!$scope.$$phase) {
                    $scope.$apply(() => {
                        $scope.errors[fieldName] = errors[fieldName] || null;
                    });
                }
            }, 300);

            // Form submission
            $scope.submitForm = async function () {
                if ($scope.isSubmitting || $scope.isProcessing) return;

                try {
                    $scope.isSubmitting = true;
                    $scope.errors = {};

                    const errors = validateForm();
                    if (Object.keys(errors).length > 0) {
                        $scope.errors = errors;
                        return;
                    }

                    const fileInput = document.querySelector('input[type="file"][name="passport"]');
                    const fileData = await fileToBase64(fileInput.files[0]);
                    const submitData = prepareFormData($scope.formData, fileData);

                    const response = await $http({
                        method: 'POST',
                        url: CONFIG.API_ENDPOINTS.REGISTER,
                        data: submitData,
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });

                    if (response.data.success) {
                        $scope.isProcessing = true;
                        await PaymentService.processPayment($scope.formData.email);
                        //alert('Registration and payment completed successfully!');
                        window.location.replace('/register/get-slip');
                    } else {
                        throw new Error(response.data.message || 'Registration failed');
                    }

                } catch (error) {
                    console.error('Registration error:', error);
                    if (error.data?.errors) {
                        $scope.errors = error.data.errors;
                    } else {
                        alert(error.message || 'An error occurred. Please try again.');
                    }
                } finally {
                    $scope.isSubmitting = false;
                    $scope.isProcessing = false;
                    if (!$scope.$$phase) {
                        $scope.$apply();
                    }
                }
            };
        }]);