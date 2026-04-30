<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Suppliers
        DB::table('suppliers')->insert([
            ['id' => 1, 'name' => 'Skintelle', 'contact_num' => '092345678901', 'address' => 'Philippines', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
        ]);

        // Customers
        DB::table('customers')->insert([
            ['id' => 1,  'first_name' => 'Jean',    'last_name' => 'Fe',        'address' => 'Cebu City',           'contact_num' => '09421258927', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 2,  'first_name' => 'Anna',    'last_name' => 'Paye',      'address' => 'Mati City',           'contact_num' => '09411054535', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 3,  'first_name' => 'Dahlia',  'last_name' => 'Powers',    'address' => 'Davao City',          'contact_num' => '09341129092', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 4,  'first_name' => 'Rob',     'last_name' => 'Vera',      'address' => 'Quezon City',         'contact_num' => '09987056271', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 5,  'first_name' => 'Maris',   'last_name' => 'Racales',   'address' => 'Tagum City',          'contact_num' => '09352814423', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 6,  'first_name' => 'Barson',  'last_name' => 'Baker',     'address' => 'Davao City',          'contact_num' => '09387592152', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 7,  'first_name' => 'Richard', 'last_name' => 'Poblacion', 'address' => 'Digos City',          'contact_num' => '09239514991', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 8,  'first_name' => 'Tony',    'last_name' => 'Bowler',    'address' => 'Quezon City',         'contact_num' => '09897639036', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 9,  'first_name' => 'Queenie', 'last_name' => 'Jo',        'address' => 'Davao City',          'contact_num' => '09293421206', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 10, 'first_name' => 'Love',    'last_name' => 'Quinn',     'address' => 'Davao City',          'contact_num' => '09325452025', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 11, 'first_name' => 'Maya',    'last_name' => 'Roberts',   'address' => 'Digos City',          'contact_num' => '09325012244', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 12, 'first_name' => 'Devin',   'last_name' => 'Kim',       'address' => 'General Santos City', 'contact_num' => '09325012244', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
        ]);

        // Delivery Methods
        DB::table('delivery_methods')->insert([
            ['id' => 1, 'type' => 'shipping', 'courier_name' => 'JRS',   'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 2, 'type' => 'rider',    'courier_name' => 'Maxim', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 3, 'type' => 'pickup',   'courier_name' => null,    'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
        ]);

        // Payment Methods
        DB::table('payment_methods')->insert([
            ['id' => 1, 'name' => 'Cash',          'description' => 'Physical bills and coins',      'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 2, 'name' => 'GCash',         'description' => 'Payment via GCash app',         'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 3, 'name' => 'Bank Transfer',  'description' => 'Payment via Bank Transfer',    'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
        ]);

        // Employees (passwords are kept as-is from the dump — already bcrypt hashed)
        DB::table('employees')->insert([
            [
                'id'          => 1,
                'first_name'  => 'Elizabeth',
                'last_name'   => 'Dequina',
                'position'    => 'Owner',
                'contact_num' => '09326762202',
                'username'    => 'elizabeth',
                'password'    => '$2y$12$mvYLPfqp453z9uEIiGHv4.FqzNsBolT8Qj7vUsuIEPiMriMSU9xMC',
                'role'        => 'owner',
                'created_at'  => '2026-04-12 10:01:31',
                'updated_at'  => '2026-04-12 10:01:31',
            ],
            [
                'id'          => 2,
                'first_name'  => 'Julliann',
                'last_name'   => 'Casenas',
                'position'    => 'Staff',
                'contact_num' => '09323421028',
                'username'    => 'julliann',
                'password'    => '$2y$12$KJ//8e0D.IKHKbHIOxu7.e0A.0XxoPLdvW68xESxI0gjDdn7Nb.nK',
                'role'        => 'staff',
                'created_at'  => '2026-04-13 04:10:26',
                'updated_at'  => '2026-04-13 04:10:26',
            ],
        ]);

        // Products
        DB::table('products')->insert([
            ['id' => 1, 'supplier_id' => 1, 'name' => 'Signature Kit',      'description' => 'The classic skincare collection with the basics',          'price' => 600.00, 'quantity' => 14, 'status' => 'available',   'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-21 20:06:43'],
            ['id' => 2, 'supplier_id' => 1, 'name' => 'Overhaul Kit',       'description' => 'An upgraded rejuvenating kit for day and night skincare',   'price' => 800.00, 'quantity' => 0,  'status' => 'unavailable', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 22:40:12'],
            ['id' => 3, 'supplier_id' => 1, 'name' => 'Kojic Acid Soap',    'description' => 'An antibacterial whitening bar for the face',               'price' => 150.00, 'quantity' => 0,  'status' => 'unavailable', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 4, 'supplier_id' => 1, 'name' => 'Pearl Bleaching Soap','description' => 'For skin renewal and dark spots',                          'price' => 150.00, 'quantity' => 0,  'status' => 'unavailable', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 5, 'supplier_id' => 1, 'name' => 'Rejuvenating Toner', 'description' => 'Exfoliates dead skin cells',                                'price' => 200.00, 'quantity' => 0,  'status' => 'unavailable', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 6, 'supplier_id' => 1, 'name' => 'Rejuvenating Cream', 'description' => 'Reduces dark circles and slows aging',                      'price' => 100.00, 'quantity' => 0,  'status' => 'unavailable', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 7, 'supplier_id' => 1, 'name' => 'Sunblock Cream',     'description' => 'Prevents skin damage and aging from sunburn',                'price' => 100.00, 'quantity' => 0,  'status' => 'unavailable', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 8, 'supplier_id' => 1, 'name' => 'Collagen Cream',     'description' => 'Hydrates skin and treats fine lines',                       'price' => 100.00, 'quantity' => 0,  'status' => 'unavailable', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
        ]);

        // Orders
        DB::table('orders')->insert([
            ['id' => 1, 'customer_id' => 2,  'delivery_method_id' => 1, 'order_date' => '2026-04-21', 'subtotal' => 600.00,  'delivery_fee' => 130.00, 'total' => 730.00,  'payment_status' => 'fully_paid', 'archived_at' => '2026-04-21 20:06:43', 'created_at' => '2026-04-20 18:03:53', 'updated_at' => '2026-04-21 20:06:43'],
            ['id' => 2, 'customer_id' => 10, 'delivery_method_id' => 2, 'order_date' => '2026-04-22', 'subtotal' => 1200.00, 'delivery_fee' => 75.00,  'total' => 1275.00, 'payment_status' => 'pending',    'archived_at' => null,                  'created_at' => '2026-04-21 20:07:48', 'updated_at' => '2026-04-21 20:07:48'],
        ]);

        // Order Lines
        DB::table('order_lines')->insert([
            ['id' => 1, 'order_id' => 1, 'product_id' => 1, 'quantity' => 1, 'unit_price' => 600.00, 'line_total' => 600.00,  'created_at' => '2026-04-20 18:03:53', 'updated_at' => '2026-04-20 18:03:53'],
            ['id' => 2, 'order_id' => 2, 'product_id' => 1, 'quantity' => 2, 'unit_price' => 600.00, 'line_total' => 1200.00, 'created_at' => '2026-04-21 20:07:48', 'updated_at' => '2026-04-21 20:07:48'],
        ]);

        // Payments
        DB::table('payments')->insert([
            ['id' => 1, 'order_id' => 1, 'payment_method_id' => 3, 'amount' => 730.00, 'payment_date' => '2026-04-21', 'created_at' => '2026-04-20 18:08:02', 'updated_at' => '2026-04-20 18:08:02'],
        ]);

        // Receipts
        DB::table('receipts')->insert([
            ['id' => 1, 'payment_id' => 1, 'receipt_num' => 'RCP-28B6F180', 'issued_at' => '2026-04-20 18:08:02', 'created_at' => '2026-04-20 18:08:02', 'updated_at' => '2026-04-20 18:08:02'],
        ]);

        // Stock Ins
        DB::table('stock_ins')->insert([
            ['id' => 1, 'product_id' => 1, 'supplier_id' => 1, 'employee_id' => 1, 'quantity' => 15, 'stock_in_date' => '2026-04-21', 'created_at' => '2026-04-20 18:02:51', 'updated_at' => '2026-04-20 18:02:51'],
        ]);

        // System Settings
        DB::table('system_settings')->insert([
            ['id' => 1, 'key' => 'low_stock_threshold', 'value' => '5',            'description' => 'Minimum quantity before a low stock warning is triggered on the order form', 'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 2, 'key' => 'business_name',       'value' => 'SKINETIQUE',    'description' => 'Business name displayed across the system',                                  'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 3, 'key' => 'timezone',            'value' => 'Asia/Manila',   'description' => 'System timezone',                                                            'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 4, 'key' => 'currency',            'value' => 'PHP',           'description' => 'Currency symbol used across the system',                                     'created_at' => '2026-04-12 10:01:31', 'updated_at' => '2026-04-12 10:01:31'],
            ['id' => 5, 'key' => 'dark_mode',           'value' => '0',             'description' => null,                                                                         'created_at' => '2026-04-12 22:57:04', 'updated_at' => '2026-04-21 20:09:50'],
            ['id' => 6, 'key' => 'color_correction',    'value' => 'none',          'description' => null,                                                                         'created_at' => '2026-04-12 22:57:04', 'updated_at' => '2026-04-12 22:57:04'],
            ['id' => 7, 'key' => 'system_sound',        'value' => '1',             'description' => null,                                                                         'created_at' => '2026-04-12 22:57:04', 'updated_at' => '2026-04-12 22:57:04'],
        ]);
    }
}