<?php

/**
 * SunTop API Client - PHP
 * Complete API client for SunTop Backend with HMVC architecture
 * Base URL: http://127.0.0.1:8000
 * 
 * @author SunTop Development Team
 * @version 1.0.0
 */

class SunTopAPIClient
{
    private $baseURL;
    private $token;
    private $defaultHeaders;

    /**
     * Constructor
     * 
     * @param string $baseURL API base URL
     * @param string|null $token JWT access token
     */
    public function __construct($baseURL = 'http://127.0.0.1:8000', $token = null)
    {
        $this->baseURL = rtrim($baseURL, '/');
        $this->token = $token;
        $this->defaultHeaders = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];
    }

    /**
     * Set authentication token
     * 
     * @param string $token JWT access token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Get headers with optional authorization
     * 
     * @param bool $withAuth Include authorization header
     * @return array Headers array
     */
    private function getHeaders($withAuth = false)
    {
        $headers = $this->defaultHeaders;
        
        if ($withAuth && $this->token) {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }
        
        return $headers;
    }

    /**
     * Make HTTP request
     * 
     * @param string $method HTTP method
     * @param string $endpoint API endpoint
     * @param array|null $data Request data
     * @param bool $withAuth Include authorization
     * @return array API response
     * @throws Exception On request failure
     */
    private function request($method, $endpoint, $data = null, $withAuth = false)
    {
        $url = $this->baseURL . $endpoint;
        
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $this->getHeaders($withAuth),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        
        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        
        curl_close($curl);
        
        if ($error) {
            throw new Exception("cURL Error: " . $error);
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode >= 400) {
            $errorMessage = isset($result['error']['message']) 
                ? $result['error']['message'] 
                : "HTTP Error $httpCode";
            throw new Exception($errorMessage);
        }
        
        return $result;
    }

    // ========================================
    // 🔐 AUTHENTICATION METHODS
    // ========================================

    /**
     * User login
     * 
     * @param string $username Username or email
     * @param string $password User password
     * @return array Login response with token and user data
     */
    public function login($username, $password)
    {
        $response = $this->request('POST', '/api/auth/login', [
            'username' => $username,
            'password' => $password
        ]);
        
        // Auto-set token after successful login
        if ($response['success'] && isset($response['data']['access_token'])) {
            $this->setToken($response['data']['access_token']);
        }
        
        return $response;
    }

    /**
     * Get user profile
     * 
     * @return array User profile data
     */
    public function getProfile()
    {
        return $this->request('GET', '/api/auth/profile', null, true);
    }

    /**
     * Reset password
     * 
     * @param string $oldPassword Current password
     * @param string $newPassword New password
     * @param string $confirmPassword Confirm new password
     * @return array Reset password response
     */
    public function resetPassword($oldPassword, $newPassword, $confirmPassword)
    {
        return $this->request('POST', '/api/auth/reset-password', [
            'old_password' => $oldPassword,
            'new_password' => $newPassword,
            'new_password_confirmation' => $confirmPassword
        ], true);
    }

    /**
     * Refresh access token
     * 
     * @param string $refreshToken Refresh token
     * @return array New access token
     */
    public function refreshToken($refreshToken)
    {
        $response = $this->request('POST', '/api/auth/refresh-token', [
            'refresh_token' => $refreshToken
        ]);
        
        // Auto-set new token
        if ($response['success'] && isset($response['data']['access_token'])) {
            $this->setToken($response['data']['access_token']);
        }
        
        return $response;
    }

    /**
     * User logout
     * 
     * @return array Logout response
     */
    public function logout()
    {
        $response = $this->request('POST', '/api/auth/logout', null, true);
        
        // Clear token after logout
        if ($response['success']) {
            $this->setToken(null);
        }
        
        return $response;
    }

    // ========================================
    // 🛍️ PRODUCTS METHODS
    // ========================================

    /**
     * Get all products with filters
     * 
     * @param array $filters Filter options
     * @return array Products list with pagination
     */
    public function getProducts($filters = [])
    {
        $queryString = http_build_query(array_filter($filters));
        $endpoint = '/api/products' . ($queryString ? '?' . $queryString : '');
        
        return $this->request('GET', $endpoint);
    }

    /**
     * Get featured products
     * 
     * @param int $limit Number of products
     * @return array Featured products
     */
    public function getFeaturedProducts($limit = 10)
    {
        return $this->request('GET', "/api/products/featured?limit=$limit");
    }

    /**
     * Get product categories
     * 
     * @return array Available categories
     */
    public function getProductCategories()
    {
        return $this->request('GET', '/api/products/categories');
    }

    /**
     * Search products
     * 
     * @param string $query Search query
     * @param int $limit Results limit
     * @return array Search results
     */
    public function searchProducts($query, $limit = 20)
    {
        $endpoint = '/api/products/search?' . http_build_query([
            'q' => $query,
            'limit' => $limit
        ]);
        
        return $this->request('GET', $endpoint);
    }

    /**
     * Get product details by ID
     * 
     * @param string|int $productId Product ID
     * @return array Product details
     */
    public function getProduct($productId)
    {
        return $this->request('GET', "/api/products/$productId");
    }

    // ========================================
    // 🛒 ORDERS METHODS
    // ========================================

    /**
     * Create new order
     * 
     * @param array $orderData Order information
     * @return array Created order details
     */
    public function createOrder($orderData)
    {
        return $this->request('POST', '/api/orders', $orderData, true);
    }

    /**
     * Get user orders
     * 
     * @param array $filters Filter options
     * @return array User orders list
     */
    public function getOrders($filters = [])
    {
        $queryString = http_build_query(array_filter($filters));
        $endpoint = '/api/orders' . ($queryString ? '?' . $queryString : '');
        
        return $this->request('GET', $endpoint, null, true);
    }

    /**
     * Get order details by ID
     * 
     * @param string|int $orderId Order ID
     * @return array Order details
     */
    public function getOrder($orderId)
    {
        return $this->request('GET', "/api/orders/$orderId", null, true);
    }

    /**
     * Track order by ID
     * 
     * @param string|int $orderId Order ID
     * @return array Order tracking information
     */
    public function trackOrder($orderId)
    {
        return $this->request('GET', "/api/orders/$orderId/tracking", null, true);
    }

    /**
     * Reorder by order ID
     * 
     * @param string|int $orderId Original order ID
     * @return array New order details
     */
    public function reorder($orderId)
    {
        return $this->request('POST', "/api/orders/$orderId/reorder", null, true);
    }

    /**
     * Cancel order by ID
     * 
     * @param string|int $orderId Order ID
     * @return array Cancellation response
     */
    public function cancelOrder($orderId)
    {
        return $this->request('POST', "/api/orders/$orderId/cancel", null, true);
    }

    // ========================================
    // 🔧 UTILITY METHODS
    // ========================================

    /**
     * Check if user is authenticated
     * 
     * @return bool Authentication status
     */
    public function isAuthenticated()
    {
        return !empty($this->token);
    }

    /**
     * Get current token
     * 
     * @return string|null Current access token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Clear authentication
     */
    public function clearAuth()
    {
        $this->token = null;
    }
}

// ========================================
// 🚀 USAGE EXAMPLES
// ========================================

/*
// Initialize API client
$api = new SunTopAPIClient();

try {
    // Login
    $loginResult = $api->login('testuser', 'password123');
    echo "Login successful: " . ($loginResult['success'] ? 'Yes' : 'No') . "\n";
    
    // Get products
    $products = $api->getProducts([
        'category' => 'Citrus',
        'page' => 1,
        'limit' => 10,
        'sort_by' => 'rating'
    ]);
    echo "Found " . count($products['data']['products']) . " products\n";
    
    // Search products
    $searchResults = $api->searchProducts('سن توب');
    echo "Search found " . count($searchResults['data']['products']) . " results\n";
    
    // Create order
    $order = $api->createOrder([
        'merchant_id' => '1',
        'items' => [
            [
                'product_id' => '1',
                'quantity' => 2,
                'unit_price' => 2.50
            ]
        ],
        'delivery_address' => [
            'street' => 'شارع النيل',
            'building' => 'رقم 15',
            'city' => 'القاهرة',
            'district' => 'المعادي',
            'phone' => '+20 109 999 9999'
        ],
        'payment_method' => 'cash_on_delivery'
    ]);
    echo "Order created: " . $order['data']['order']['order_number'] . "\n";
    
    // Track order
    $tracking = $api->trackOrder($order['data']['order']['id']);
    echo "Order status: " . $tracking['data']['current_status'] . "\n";
    
    // Logout
    $api->logout();
    echo "Logged out successfully\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
*/

?>
