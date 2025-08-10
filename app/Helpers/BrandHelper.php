<?php

namespace App\Helpers;

class BrandHelper
{
    /**
     * Get the brand name from environment variables
     * 
     * @return string
     */
    public static function getBrandName()
    {
        return env('MAIL_FROM_NAME', 'Penjualan Panjaratan');
    }

    /**
     * Get the app name from environment variables
     * 
     * @return string
     */
    public static function getAppName()
    {
        return env('APP_NAME', 'Laravel');
    }
}