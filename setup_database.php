<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Setting up database...\n";

try {
    // Create database file if it doesn't exist
    if (!file_exists('database/database.sqlite')) {
        touch('database/database.sqlite');
        echo "Created database file\n";
    }

    // Run migrations
    echo "Running migrations...\n";
    Artisan::call('migrate:fresh');
    echo "Migrations completed\n";

    // Run seeders
    echo "Running seeders...\n";
    Artisan::call('db:seed');
    echo "Seeders completed\n";

    // Verify data
    echo "Verifying data...\n";
    $users = DB::table('users')->get();
    echo "Found " . $users->count() . " users:\n";
    
    foreach ($users as $user) {
        echo "- {$user->username} ({$user->email}) - Phone: {$user->phone}\n";
    }

    echo "\nDatabase setup completed successfully!\n";
    echo "You can now test the password reset functionality.\n";
    echo "\nTest accounts:\n";
    echo "- Admin: admin@example.com / admin123\n";
    echo "- Seller: seller1@example.com / seller123\n";
    echo "- Customer: customer1@example.com / customer123\n";
    echo "- Test: test@example.com / password123\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}