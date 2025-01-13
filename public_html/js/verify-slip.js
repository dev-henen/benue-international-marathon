// Constants for configuration and error messages
const DEFAULT_CONFIG = {
    fps: 10,
    qrbox: { width: 250, height: 250 },
    aspectRatio: 1.0,
    facingMode: "environment"
};

const ERROR_MESSAGES = {
    ELEMENT_NOT_FOUND: (id) => `HTML Element with id=${id} not found`,
    NO_CAMERAS: "No cameras found on this device",
    CAMERA_ACCESS_DENIED: "Camera access was denied",
    SCANNER_RUNNING: "Scanner is already running",
    CAMERA_TROUBLESHOOTING: `Please ensure:
        1. Your device has a working camera
        2. You've granted camera permissions
        3. No other app is using the camera`
};

class QRScanner {
    constructor(elementId, config = {}) {
        this.elementId = elementId;
        this.scanner = null;
        this.config = { ...DEFAULT_CONFIG, ...config };
        this.isInitialized = false;
    }

    async getCameras() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            return devices.filter(device => device.kind === 'videoinput');
        } catch (error) {
            throw new Error(`Failed to get camera list: ${error.message}`);
        }
    }

    async findWorkingCamera() {
        const cameras = await this.getCameras();

        if (!cameras.length) {
            throw new Error(ERROR_MESSAGES.NO_CAMERAS);
        }

        const configs = [
            { facingMode: "environment" },
            { facingMode: "user" },
            { deviceId: cameras[0].deviceId }
        ];

        for (const config of configs) {
            try {
                const stream = await this.testCameraConfig(config);
                return config;
            } catch (error) {
                console.warn('Camera config failed:', config, error);
                continue;
            }
        }

        throw new Error(ERROR_MESSAGES.CAMERA_ACCESS_DENIED);
    }

    async testCameraConfig(config) {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: typeof config === 'string' ? { deviceId: config } : config
        });
        stream.getTracks().forEach(track => track.stop());
        return config;
    }

    async initialize() {
        if (this.isInitialized) return;

        const element = document.getElementById(this.elementId);
        if (!element) {
            throw new Error(ERROR_MESSAGES.ELEMENT_NOT_FOUND(this.elementId));
        }

        const cameraConfig = await this.findWorkingCamera();
        this.scanner = new Html5Qrcode(this.elementId);
        this.isInitialized = true;
        return cameraConfig;
    }

    async start(callback) {
        try {
            if (this.scanner?.isScanning) {
                throw new Error(ERROR_MESSAGES.SCANNER_RUNNING);
            }

            const cameraConfig = await this.initialize();

            await this.scanner.start(
                cameraConfig,
                this.config,
                async (decodedText, decodedResult) => {
                    try {
                        await this.stop();
                        callback(null, decodedText, decodedResult);
                    } catch (error) {
                        callback(error);
                    }
                },
                (errorMessage) => console.warn("QR scan error:", errorMessage)
            );
        } catch (error) {
            await this.cleanup();
            callback(error);
        }
    }

    async stop() {
        if (this.scanner?.isScanning) {
            await this.scanner.stop();
        }
    }

    async cleanup() {
        try {
            await this.stop();
            if (this.scanner) {
                await this.scanner.clear();
                this.scanner = null;
                this.isInitialized = false;
            }
        } catch (error) {
            console.error("Cleanup error:", error);
            throw error;
        }
    }
}

class RegistrationManager {
    static template = null;

    static async fetchRegistrationInfo(hashCode) {
        if (!hashCode) {
            throw new Error("Hash code is required");
        }

        const response = await fetch('/api/get-registration', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ hashCode }),
        });

        if (!response.ok) {
            throw new Error(`${response.statusText}`);
        }

        return response.json();
    }

    static updateModalContent(data) {
        const modal = document.getElementById("modal");
        if (!modal) {
            throw new Error("Modal element not found");
        }

        // Store the template content if it's the first time
        if (!this.template) {
            this.template = modal.innerHTML;
        }

        // Process dates if they exist
        if (data.reg_date) {
            data.reg_date = parseDate(data.reg_date);
        }
        if (data.birthday) {
            data.birthday = parseDate(data.birthday);
        }

        // Start with the template and replace all placeholders
        let newContent = this.template;
        newContent = newContent.replace(
            /\[\[(\w+)\]\]/g,
            (match, key) => data[key] ?? match
        );

        modal.innerHTML = newContent;
    }
}

class ModalManager {
    static init() {
        this.setupEventListeners();
    }

    static setupEventListeners() {
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') this.close();
        });

        document.getElementById('modal-backdrop')?.addEventListener('click', e => {
            if (e.target === e.currentTarget) this.close();
        });
    }

    static open() {
        const modal = document.getElementById('modal');
        const backdrop = document.getElementById('modal-backdrop');

        if (!modal || !backdrop) return;

        backdrop.classList.remove('hidden');
        modal.classList.remove('hidden');

        requestAnimationFrame(() => {
            backdrop.classList.add('opacity-100');
            modal.classList.add('opacity-100');
            document.body.style.overflow = 'hidden';
        });
    }

    static close() {
        const modal = document.getElementById('modal');
        const backdrop = document.getElementById('modal-backdrop');

        if (!modal || !backdrop) return;

        backdrop.classList.remove('opacity-100');
        modal.classList.remove('opacity-100');

        setTimeout(() => {
            modal.classList.add('hidden');
            backdrop.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    static toggleDetails() {
        const moreDetails = document.getElementById('more-details');
        const toggleText = document.getElementById('toggle-text');
        const chevronIcon = document.getElementById('chevron-icon');

        if (!moreDetails || !toggleText || !chevronIcon) return;

        const isHidden = moreDetails.classList.contains('hidden');
        moreDetails.classList.toggle('hidden');
        toggleText.textContent = isHidden ? 'Show Less' : 'View More Details';
        chevronIcon.classList.toggle('rotate-180');
    }
}

// Initialize modal functionality
ModalManager.init();

// Example usage
async function confirmMySlip() {
    const resultElement = document.getElementById('qr-result');
    if (!resultElement) return;

    resultElement.innerText = "Checking camera availability...";

    const scanner = new QRScanner("qr-reader");

    try {
        await scanner.start(async (error, decodedText) => {
            if (error) {
                resultElement.innerText = `Error: ${error.message}\n\n${ERROR_MESSAGES.CAMERA_TROUBLESHOOTING}`;
                return;
            }

            try {
                const data = await RegistrationManager.fetchRegistrationInfo(decodedText);
                RegistrationManager.updateModalContent(data);
                ModalManager.open();
            } catch (apiError) {
                resultElement.innerText = `Failed to fetch registration: ${apiError.message}`;
            }
        });
    } catch (error) {
        resultElement.innerText = `Scanner error: ${error.message}`;
    }
}


function parseDate(input) {
    const date = new Date(input);

    if (isNaN(date)) {
        throw new Error("Invalid date format");
    }

    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const formattedDate = date.toLocaleDateString('en-US', options);

    return formattedDate;
}