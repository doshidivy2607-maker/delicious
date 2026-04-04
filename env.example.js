// Environment Configuration
// IMPORTANT: This file contains sensitive API keys
// DO NOT commit this file to version control
//
// Copy this file and rename it to env.js, then add your actual keys

const ENV = {
    RAZORPAY: {
        KEY_ID: 'YOUR_RAZORPAY_KEY_ID_HERE', // Replace with your Razorpay Key ID
        // For production, use your Live Key ID
        // For testing, use your Test Key ID
    }
};

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ENV;
}