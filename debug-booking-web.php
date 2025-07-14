<?php

// Simple debug script using artisan tinker approach
echo "=== DEBUGGING BOOKING ISSUE ===\n";
echo "Silakan jalankan perintah berikut di terminal terpisah:\n";
echo "php artisan tinker\n";
echo "\nKemudian jalankan kode berikut di tinker:\n";
echo "\n";
echo "\$user = App\\Models\\User::where('email', 'mega.wati@banyakisland.com')->first();\n";
echo "Auth::login(\$user);\n";
echo "\$package = App\\Models\\TravelPackage::find(1);\n";
echo "\$controller = new App\\Http\\Controllers\\BookingController();\n";
echo "\$request = request();\n";
echo "\$request->merge([\n";
echo "    'travel_package_id' => '1',\n";
echo "    'total_price' => '2145000',\n";
echo "    'name' => 'Mega Wati',\n";
echo "    'contact' => '+62 818 9012 3456',\n";
echo "    'booking_date' => '2025-07-30',\n";
echo "    'person_count' => '2',\n";
echo "    'special_requests' => 'test',\n";
echo "    'terms' => 'on'\n";
echo "]);\n";
echo "\$response = \$controller->store(\$request);\n";
echo "echo get_class(\$response);\n";
echo "\n";
echo "Atau coba langsung test MidtransService:\n";
echo "\$service = new App\\Services\\MidtransService();\n";
echo "\$booking = App\\Models\\Booking::latest()->first();\n";
echo "\$payment = \$booking->payment ?? \$booking->createPayment(['payment_status' => 'Unpaid', 'total_price' => \$booking->total_price, 'payment_reference' => 'TEST-'.time()]);
";
echo "\$url = \$service->createSnapRedirectUrl(\$booking, \$payment);\n";
echo "echo \$url;\n";