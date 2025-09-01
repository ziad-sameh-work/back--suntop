/**
 * SunTop API Client - JavaScript/TypeScript
 * Complete API client for SunTop Backend with HMVC architecture
 * Base URL: https://suntop-eg.com
 */

class SunTopAPI {
    constructor(baseURL = 'https://suntop-eg.com', token = null) {
        this.baseURL = baseURL;
        this.token = token;
        this.defaultHeaders = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
    }

    /**
     * Set authentication token
     * @param {string} token - JWT access token
     */
    setToken(token) {
        this.token = token;
    }

    /**
     * Get headers with optional authorization
     * @param {boolean} withAuth - Include authorization header
     * @returns {Object} Headers object
     */
    getHeaders(withAuth = false) {
        const headers = { ...this.defaultHeaders };
        if (withAuth && this.token) {
            headers['Authorization'] = `Bearer ${this.token}`;
        }
        return headers;
    }

    /**
     * Make HTTP request
     * @param {string} method - HTTP method
     * @param {string} endpoint - API endpoint
     * @param {Object} data - Request data
     * @param {boolean} withAuth - Include authorization
     * @returns {Promise} API response
     */
    async request(method, endpoint, data = null, withAuth = false) {
        const url = `${this.baseURL}${endpoint}`;
        const options = {
            method,
            headers: this.getHeaders(withAuth)
        };

        if (data && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
            options.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(url, options);
            const result = await response.json();
            
            if (!response.ok) {
                throw new Error(result.error?.message || `HTTP ${response.status}`);
            }
            
            return result;
        } catch (error) {
            console.error(`API Error [${method} ${endpoint}]:`, error);
            throw error;
        }
    }

    // ========================================
    // 🔐 AUTHENTICATION METHODS
    // ========================================

    /**
     * User login
     * @param {string} username - Username or email
     * @param {string} password - User password
     * @returns {Promise} Login response with token and user data
     */
    async login(username, password) {
        const response = await this.request('POST', '/api/auth/login', {
            username,
            password
        });
        
        // Auto-set token after successful login
        if (response.success && response.data.access_token) {
            this.setToken(response.data.access_token);
        }
        
        return response;
    }

    /**
     * Get user profile
     * @returns {Promise} User profile data
     */
    async getProfile() {
        return await this.request('GET', '/api/auth/profile', null, true);
    }

    /**
     * Reset password
     * @param {string} oldPassword - Current password
     * @param {string} newPassword - New password
     * @param {string} confirmPassword - Confirm new password
     * @returns {Promise} Reset password response
     */
    async resetPassword(oldPassword, newPassword, confirmPassword) {
        return await this.request('POST', '/api/auth/reset-password', {
            old_password: oldPassword,
            new_password: newPassword,
            new_password_confirmation: confirmPassword
        }, true);
    }

    /**
     * Refresh access token
     * @param {string} refreshToken - Refresh token
     * @returns {Promise} New access token
     */
    async refreshToken(refreshToken) {
        const response = await this.request('POST', '/api/auth/refresh-token', {
            refresh_token: refreshToken
        });
        
        // Auto-set new token
        if (response.success && response.data.access_token) {
            this.setToken(response.data.access_token);
        }
        
        return response;
    }

    /**
     * User logout
     * @returns {Promise} Logout response
     */
    async logout() {
        const response = await this.request('POST', '/api/auth/logout', null, true);
        
        // Clear token after logout
        if (response.success) {
            this.setToken(null);
        }
        
        return response;
    }

    // ========================================
    // 🛍️ PRODUCTS METHODS
    // ========================================

    /**
     * Get all products with filters
     * @param {Object} filters - Filter options
     * @param {string} filters.category - Product category
     * @param {number} filters.page - Page number (default: 1)
     * @param {number} filters.limit - Items per page (default: 20)
     * @param {string} filters.search - Search query
     * @param {string} filters.sort_by - Sort field (default: 'created_at')
     * @param {string} filters.sort_order - Sort order (default: 'desc')
     * @returns {Promise} Products list with pagination
     */
    async getProducts(filters = {}) {
        const queryParams = new URLSearchParams();
        
        Object.keys(filters).forEach(key => {
            if (filters[key] !== null && filters[key] !== undefined) {
                queryParams.append(key, filters[key]);
            }
        });
        
        const queryString = queryParams.toString();
        const endpoint = `/api/products${queryString ? `?${queryString}` : ''}`;
        
        return await this.request('GET', endpoint);
    }

    /**
     * Get featured products
     * @param {number} limit - Number of products (default: 10)
     * @returns {Promise} Featured products
     */
    async getFeaturedProducts(limit = 10) {
        return await this.request('GET', `/api/products/featured?limit=${limit}`);
    }

    /**
     * Get product categories
     * @returns {Promise} Available categories
     */
    async getProductCategories() {
        return await this.request('GET', '/api/products/categories');
    }

    /**
     * Search products
     * @param {string} query - Search query
     * @param {number} limit - Results limit (default: 20)
     * @returns {Promise} Search results
     */
    async searchProducts(query, limit = 20) {
        const endpoint = `/api/products/search?q=${encodeURIComponent(query)}&limit=${limit}`;
        return await this.request('GET', endpoint);
    }

    /**
     * Get product details by ID
     * @param {string|number} productId - Product ID
     * @returns {Promise} Product details
     */
    async getProduct(productId) {
        return await this.request('GET', `/api/products/${productId}`);
    }

    // ========================================
    // 🛒 ORDERS METHODS
    // ========================================

    /**
     * Create new order
     * @param {Object} orderData - Order information
     * @param {string} orderData.merchant_id - Merchant ID
     * @param {Array} orderData.items - Order items
     * @param {Object} orderData.delivery_address - Delivery address
     * @param {string} orderData.payment_method - Payment method
     * @param {string} orderData.offer_code - Optional offer code
     * @param {number} orderData.use_loyalty_points - Optional loyalty points
     * @param {string} orderData.notes - Optional notes
     * @returns {Promise} Created order details
     */
    async createOrder(orderData) {
        return await this.request('POST', '/api/orders', orderData, true);
    }

    /**
     * Get user orders
     * @param {Object} filters - Filter options
     * @param {string} filters.status - Order status
     * @param {number} filters.page - Page number (default: 1)
     * @param {number} filters.limit - Items per page (default: 20)
     * @returns {Promise} User orders list
     */
    async getOrders(filters = {}) {
        const queryParams = new URLSearchParams();
        
        Object.keys(filters).forEach(key => {
            if (filters[key] !== null && filters[key] !== undefined) {
                queryParams.append(key, filters[key]);
            }
        });
        
        const queryString = queryParams.toString();
        const endpoint = `/api/orders${queryString ? `?${queryString}` : ''}`;
        
        return await this.request('GET', endpoint, null, true);
    }

    /**
     * Get order details by ID
     * @param {string|number} orderId - Order ID
     * @returns {Promise} Order details
     */
    async getOrder(orderId) {
        return await this.request('GET', `/api/orders/${orderId}`, null, true);
    }

    /**
     * Track order by ID
     * @param {string|number} orderId - Order ID
     * @returns {Promise} Order tracking information
     */
    async trackOrder(orderId) {
        return await this.request('GET', `/api/orders/${orderId}/tracking`, null, true);
    }

    /**
     * Reorder by order ID
     * @param {string|number} orderId - Original order ID
     * @returns {Promise} New order details
     */
    async reorder(orderId) {
        return await this.request('POST', `/api/orders/${orderId}/reorder`, null, true);
    }

    /**
     * Cancel order by ID
     * @param {string|number} orderId - Order ID
     * @returns {Promise} Cancellation response
     */
    async cancelOrder(orderId) {
        return await this.request('POST', `/api/orders/${orderId}/cancel`, null, true);
    }

    // ========================================
    // 🔧 UTILITY METHODS
    // ========================================

    /**
     * Check if user is authenticated
     * @returns {boolean} Authentication status
     */
    isAuthenticated() {
        return !!this.token;
    }

    /**
     * Get current token
     * @returns {string|null} Current access token
     */
    getToken() {
        return this.token;
    }

    /**
     * Clear authentication
     */
    clearAuth() {
        this.token = null;
    }
}

// ========================================
// 🚀 USAGE EXAMPLES
// ========================================

/**
 * Example usage:
 * 
 * // Initialize API client
 * const api = new SunTopAPI();
 * 
 * // Login
 * const loginResult = await api.login('testuser', 'password123');
 * console.log('Login successful:', loginResult.success);
 * 
 * // Get products
 * const products = await api.getProducts({
 *     category: 'Citrus',
 *     page: 1,
 *     limit: 10,
 *     sort_by: 'rating'
 * });
 * 
 * // Search products
 * const searchResults = await api.searchProducts('سن توب');
 * 
 * // Create order
 * const order = await api.createOrder({
 *     merchant_id: '1',
 *     items: [
 *         { product_id: '1', quantity: 2, unit_price: 2.50 }
 *     ],
 *     delivery_address: {
 *         street: 'شارع النيل',
 *         building: 'رقم 15',
 *         city: 'القاهرة',
 *         district: 'المعادي',
 *         phone: '+20 109 999 9999'
 *     },
 *     payment_method: 'cash_on_delivery'
 * });
 * 
 * // Track order
 * const tracking = await api.trackOrder(order.data.order.id);
 * 
 * // Logout
 * await api.logout();
 */

// Export for different module systems
if (typeof module !== 'undefined' && module.exports) {
    // Node.js
    module.exports = SunTopAPI;
} else if (typeof window !== 'undefined') {
    // Browser
    window.SunTopAPI = SunTopAPI;
}

// ES6 modules
export default SunTopAPI;
