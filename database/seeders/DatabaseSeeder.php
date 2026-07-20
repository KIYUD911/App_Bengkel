<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerVehicle;
use App\Models\SparePart;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. USERS (3 role) ──────────────────────────────────
        $owner = User::create([
            'name'     => 'Masman Sejahtera',
            'email'    => 'owner@bengkel.com',
            'password' => Hash::make('password'),
            'role'     => 'owner',
        ]);

        $kasir = User::create([
            'name'     => 'Budi Kasir',
            'email'    => 'kasir@bengkel.com',
            'password' => Hash::make('password'),
            'role'     => 'kasir',
        ]);

        User::create([
            'name'     => 'Agus Gudang',
            'email'    => 'gudang@bengkel.com',
            'password' => Hash::make('password'),
            'role'     => 'staf_gudang',
        ]);

        // ─── 2. CUSTOMERS (5 pelanggan, 2 VIP) ─────────────────
        $customers = [
            [
                'name'        => 'Rizky Pratama',
                'phone'       => '081234567890',
                'email'       => 'rizky@email.com',
                'address'     => 'Jl. Kebon Jeruk No. 12, Jakarta Barat',
                'visit_count' => 8,
                'total_spent' => 1500000,
                'is_vip'      => true,
            ],
            [
                'name'        => 'Siti Rahayu',
                'phone'       => '082345678901',
                'email'       => 'siti@email.com',
                'address'     => 'Jl. Merdeka No. 5, Jakarta Pusat',
                'visit_count' => 12,
                'total_spent' => 2300000,
                'is_vip'      => true,
            ],
            [
                'name'        => 'Hendra Wijaya',
                'phone'       => '083456789012',
                'email'       => null,
                'address'     => 'Jl. Sudirman No. 88, Jakarta Selatan',
                'visit_count' => 3,
                'total_spent' => 450000,
                'is_vip'      => false,
            ],
            [
                'name'        => 'Dewi Lestari',
                'phone'       => '084567890123',
                'email'       => 'dewi@email.com',
                'address'     => 'Jl. Gatot Subroto No. 33, Jakarta Selatan',
                'visit_count' => 2,
                'total_spent' => 180000,
                'is_vip'      => false,
            ],
            [
                'name'        => 'Andi Santoso',
                'phone'       => '085678901234',
                'email'       => 'andi@email.com',
                'address'     => 'Jl. Cempaka Putih No. 7, Jakarta Timur',
                'visit_count' => 5,
                'total_spent' => 750000,
                'is_vip'      => false,
            ],
        ];

        $createdCustomers = [];
        foreach ($customers as $data) {
            $createdCustomers[] = Customer::create($data);
        }

        // ─── 3. KENDARAAN (8 kendaraan) ─────────────────────────
        $vehicles = [
            // Rizky Pratama — 2 motor
            [
                'customer_id'   => $createdCustomers[0]->id,
                'license_plate' => 'B 1234 ABC',
                'vehicle_type'  => 'Motor',
                'brand'         => 'Honda',
                'model'         => 'Vario 150',
                'year'          => 2021,
                'vin'           => null,
            ],
            [
                'customer_id'   => $createdCustomers[0]->id,
                'license_plate' => 'B 5678 DEF',
                'vehicle_type'  => 'Motor',
                'brand'         => 'Yamaha',
                'model'         => 'NMAX',
                'year'          => 2022,
                'vin'           => null,
            ],
            // Siti Rahayu — 2 motor
            [
                'customer_id'   => $createdCustomers[1]->id,
                'license_plate' => 'B 2345 GHI',
                'vehicle_type'  => 'Motor',
                'brand'         => 'Honda',
                'model'         => 'Beat',
                'year'          => 2020,
                'vin'           => null,
            ],
            [
                'customer_id'   => $createdCustomers[1]->id,
                'license_plate' => 'B 6789 JKL',
                'vehicle_type'  => 'Motor',
                'brand'         => 'Suzuki',
                'model'         => 'Address',
                'year'          => 2019,
                'vin'           => null,
            ],
            // Hendra Wijaya — 1 motor
            [
                'customer_id'   => $createdCustomers[2]->id,
                'license_plate' => 'B 3456 MNO',
                'vehicle_type'  => 'Motor',
                'brand'         => 'Kawasaki',
                'model'         => 'KLX 150',
                'year'          => 2023,
                'vin'           => null,
            ],
            // Dewi Lestari — 1 motor
            [
                'customer_id'   => $createdCustomers[3]->id,
                'license_plate' => 'B 4567 PQR',
                'vehicle_type'  => 'Motor',
                'brand'         => 'Honda',
                'model'         => 'Scoopy',
                'year'          => 2022,
                'vin'           => null,
            ],
            // Andi Santoso — 2 motor
            [
                'customer_id'   => $createdCustomers[4]->id,
                'license_plate' => 'B 7890 STU',
                'vehicle_type'  => 'Motor',
                'brand'         => 'Yamaha',
                'model'         => 'Mio M3',
                'year'          => 2018,
                'vin'           => null,
            ],
            [
                'customer_id'   => $createdCustomers[4]->id,
                'license_plate' => 'B 8901 VWX',
                'vehicle_type'  => 'Motor',
                'brand'         => 'Honda',
                'model'         => 'Revo Fit',
                'year'          => 2017,
                'vin'           => null,
            ],
        ];

        foreach ($vehicles as $v) {
            CustomerVehicle::create($v);
        }

        // ─── 4. SPAREPART (15 item, 3 stok kritis qty=2) ────────
        $spareParts = [
            // Normal stock
            ['part_code' => 'SP-001', 'name' => 'Oli Mesin 10W-40',           'category' => 'Oli',        'purchase_price' => 35000,  'selling_price' => 50000,  'quantity_available' => 30, 'unit' => 'liter'],
            ['part_code' => 'SP-002', 'name' => 'Oli Mesin 20W-50',           'category' => 'Oli',        'purchase_price' => 30000,  'selling_price' => 45000,  'quantity_available' => 25, 'unit' => 'liter'],
            ['part_code' => 'SP-003', 'name' => 'Filter Udara Honda Beat',    'category' => 'Filter',     'purchase_price' => 25000,  'selling_price' => 40000,  'quantity_available' => 20, 'unit' => 'pcs'],
            ['part_code' => 'SP-004', 'name' => 'Filter Oli Universal',       'category' => 'Filter',     'purchase_price' => 15000,  'selling_price' => 25000,  'quantity_available' => 15, 'unit' => 'pcs'],
            ['part_code' => 'SP-005', 'name' => 'Busi NGK CR7HSA',            'category' => 'Pengapian',  'purchase_price' => 18000,  'selling_price' => 30000,  'quantity_available' => 40, 'unit' => 'pcs'],
            ['part_code' => 'SP-006', 'name' => 'Kampas Rem Depan Honda Vario','category' => 'Rem',        'purchase_price' => 45000,  'selling_price' => 70000,  'quantity_available' => 10, 'unit' => 'set'],
            ['part_code' => 'SP-007', 'name' => 'Kampas Rem Belakang Universal','category' => 'Rem',       'purchase_price' => 35000,  'selling_price' => 55000,  'quantity_available' => 12, 'unit' => 'set'],
            ['part_code' => 'SP-008', 'name' => 'Rantai Motor 428H',          'category' => 'Transmisi',  'purchase_price' => 55000,  'selling_price' => 85000,  'quantity_available' => 8,  'unit' => 'pcs'],
            ['part_code' => 'SP-009', 'name' => 'Aki Motor MF 5Ah',           'category' => 'Kelistrikan','purchase_price' => 85000,  'selling_price' => 130000, 'quantity_available' => 6,  'unit' => 'pcs'],
            ['part_code' => 'SP-010', 'name' => 'Lampu Depan LED H4',         'category' => 'Kelistrikan','purchase_price' => 45000,  'selling_price' => 75000,  'quantity_available' => 10, 'unit' => 'pcs'],
            ['part_code' => 'SP-011', 'name' => 'Ban Dalam 80/90-17',         'category' => 'Ban',        'purchase_price' => 35000,  'selling_price' => 55000,  'quantity_available' => 15, 'unit' => 'pcs'],
            ['part_code' => 'SP-012', 'name' => 'Kabel Gas Universal',        'category' => 'Kabel',      'purchase_price' => 20000,  'selling_price' => 35000,  'quantity_available' => 8,  'unit' => 'pcs'],
            // Stok kritis (qty = 2)
            ['part_code' => 'SP-013', 'name' => 'Kampas Kopling Honda CBR',   'category' => 'Transmisi',  'purchase_price' => 120000, 'selling_price' => 180000, 'quantity_available' => 2,  'unit' => 'set'],
            ['part_code' => 'SP-014', 'name' => 'Karburator Keihin PE28',     'category' => 'Karburator', 'purchase_price' => 250000, 'selling_price' => 380000, 'quantity_available' => 2,  'unit' => 'pcs'],
            ['part_code' => 'SP-015', 'name' => 'Shock Absorber Belakang YSS','category' => 'Suspensi',   'purchase_price' => 180000, 'selling_price' => 280000, 'quantity_available' => 2,  'unit' => 'pcs'],
        ];

        foreach ($spareParts as $part) {
            SparePart::create($part);
        }
    }
}
