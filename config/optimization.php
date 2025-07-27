<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Performance Optimization Settings
    |--------------------------------------------------------------------------
    |
    | Configuration untuk optimasi aplikasi e-commerce untuk handle ribuan user
    |
    */

    'cache' => [
        // Cache duration in seconds
        'product_listing' => 300, // 5 minutes
        'product_details' => 1800, // 30 minutes  
        'categories' => 3600, // 1 hour
        'user_dashboard' => 300, // 5 minutes
        'cart_count' => 120, // 2 minutes
        'search_results' => 600, // 10 minutes
        'price_ranges' => 3600, // 1 hour
        'navigation_data' => 7200, // 2 hours
    ],

    'rate_limiting' => [
        // Rate limits per minute
        'login_attempts' => 5,
        'registration_attempts' => 3,
        'cart_additions' => 10,
        'api_requests' => 60,
        'search_requests' => 20,
    ],

    'pagination' => [
        'products_per_page' => 12,
        'orders_per_page' => 10,
        'reviews_per_page' => 5,
        'notifications_per_page' => 15,
    ],

    'database' => [
        // Query optimization settings
        'eager_load_limits' => [
            'product_images' => 5,
            'product_reviews' => 10,
            'related_products' => 4,
            'recent_orders' => 5,
        ],
        
        // Connection settings for high load
        'max_connections' => 100,
        'connection_timeout' => 30,
        'query_timeout' => 10,
    ],

    'security' => [
        // Input validation
        'max_username_length' => 50,
        'max_email_length' => 255,
        'max_product_name_length' => 200,
        'max_description_length' => 2000,
        'max_address_length' => 500,
        
        // File uploads
        'max_image_size' => 2048, // KB
        'allowed_image_types' => ['jpg', 'jpeg', 'png', 'webp'],
        'max_images_per_product' => 10,
    ],

    'performance' => [
        // Image optimization
        'thumbnail_sizes' => [
            'small' => [150, 150],
            'medium' => [300, 300], 
            'large' => [600, 600],
        ],
        
        // Lazy loading
        'enable_lazy_loading' => true,
        'lazy_load_threshold' => 3,
        
        // CDN settings
        'use_cdn' => env('USE_CDN', false),
        'cdn_url' => env('CDN_URL', ''),
        
        // Compression
        'enable_gzip' => true,
        'enable_image_optimization' => true,
    ],

    'monitoring' => [
        // Performance monitoring
        'log_slow_queries' => true,
        'slow_query_threshold' => 1000, // milliseconds
        'enable_query_profiling' => env('APP_ENV') !== 'production',
        
        // Error tracking
        'log_errors' => true,
        'error_reporting_level' => E_ALL & ~E_NOTICE,
        
        // Health checks
        'health_check_endpoints' => [
            'database' => '/health/database',
            'cache' => '/health/cache', 
            'storage' => '/health/storage',
        ],
    ],

    'scaling' => [
        // Auto-scaling triggers
        'cpu_threshold' => 80, // percent
        'memory_threshold' => 85, // percent
        'response_time_threshold' => 2000, // milliseconds
        
        // Load balancing
        'enable_session_affinity' => false,
        'session_store' => 'database', // database, redis, memcached
        
        // Queue processing
        'async_notifications' => true,
        'async_email_sending' => true,
        'background_image_processing' => true,
    ],

    'maintenance' => [
        // Cleanup schedules
        'cleanup_temp_files' => '0 2 * * *', // Daily at 2 AM
        'optimize_database' => '0 3 * * 0', // Weekly on Sunday 3 AM
        'clear_expired_sessions' => '0 1 * * *', // Daily at 1 AM
        'backup_database' => '0 4 * * *', // Daily at 4 AM
        
        // Log rotation
        'max_log_size' => '100M',
        'keep_logs_days' => 30,
    ],
];