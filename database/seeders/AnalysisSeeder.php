<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AnalysisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Products
        $productsData = [
            ['name' => 'Intel Core i9-14900K', 'sku' => 'CPU-INT-I9149', 'price' => 9500000, 'stock' => 15],
            ['name' => 'AMD Ryzen 7 7800X3D', 'sku' => 'CPU-AMD-R7780', 'price' => 6800000, 'stock' => 20],
            ['name' => 'ASUS ROG Strix RTX 4080 Super', 'sku' => 'GPU-ASU-4080S', 'price' => 19500000, 'stock' => 8],
            ['name' => 'MSI Ventus RTX 4060 Ti 8GB', 'sku' => 'GPU-MSI-4060T', 'price' => 7200000, 'stock' => 12],
            ['name' => 'Kingston Fury Beast DDR5 32GB', 'sku' => 'RAM-KIN-D532G', 'price' => 1850000, 'stock' => 40],
            ['name' => 'Corsair Vengeance RGB DDR5 32GB', 'sku' => 'RAM-COR-D532G', 'price' => 2100000, 'stock' => 30],
            ['name' => 'Samsung 990 Pro M.2 SSD 1TB', 'sku' => 'SSD-SAM-9901T', 'price' => 1650000, 'stock' => 25],
            ['name' => 'Crucial P3 Plus SSD 2TB', 'sku' => 'SSD-CRU-P3P2T', 'price' => 2300000, 'stock' => 18],
            ['name' => 'Gigabyte B650 AORUS Elite AX', 'sku' => 'MBO-GIG-B650A', 'price' => 3400000, 'stock' => 15],
            ['name' => 'ASRock Z790 Pro RS', 'sku' => 'MBO-ASR-Z790P', 'price' => 3800000, 'stock' => 10],
            ['name' => 'Corsair RM850x 850W Gold PSU', 'sku' => 'PSU-COR-RM850', 'price' => 2200000, 'stock' => 15],
            ['name' => 'Lian Li O11 Dynamic EVO Case', 'sku' => 'CAS-LIA-O11DE', 'price' => 2800000, 'stock' => 14],
            ['name' => 'Noctua NH-D15 CPU Cooler', 'sku' => 'COL-NOC-NHD15', 'price' => 1500000, 'stock' => 22],
            ['name' => 'Deepcool LT720 360mm AIO', 'sku' => 'COL-DEP-LT720', 'price' => 1850000, 'stock' => 10],
            ['name' => 'Logitech G Pro X Superlight 2', 'sku' => 'MSE-LOG-GPXS2', 'price' => 2400000, 'stock' => 25],
        ];

        $products = [];
        foreach ($productsData as $data) {
            $products[] = Product::create($data);
        }

        // 2. Generate Orders for the past 30 days
        $now = Carbon::now();
        
        for ($i = 30; $i >= 0; $i--) {
            $date = (clone $now)->subDays($i);
            
            // Random number of orders per day (3 to 8)
            $ordersCount = rand(3, 8);
            for ($o = 0; $o < $ordersCount; $o++) {
                $statusSeed = rand(1, 100);
                if ($statusSeed <= 80) {
                    $status = 'completed';
                } elseif ($statusSeed <= 95) {
                    $status = 'pending';
                } else {
                    $status = 'cancelled';
                }

                // Create Order
                $orderTime = (clone $date)->setHour(rand(9, 21))->setMinute(rand(0, 59))->setSecond(rand(0, 59));
                $orderNumber = 'AJM-' . $orderTime->format('Ymd') . '-' . strtoupper(Str::random(5));
                
                $order = Order::create([
                    'order_number' => $orderNumber,
                    'total_amount' => 0, // Will update below
                    'status' => $status,
                    'created_at' => $orderTime,
                    'updated_at' => $orderTime,
                ]);

                // Create 1 to 4 OrderItems for this order
                $itemsCount = rand(1, 4);
                $selectedProducts = array_rand($products, $itemsCount);
                if (!is_array($selectedProducts)) {
                    $selectedProducts = [$selectedProducts];
                }

                $totalAmount = 0;
                foreach ($selectedProducts as $prodIdx) {
                    $product = $products[$prodIdx];
                    $qty = rand(1, 2);
                    $price = $product->price;
                    $itemAmount = $qty * $price;
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'price' => $price,
                        'created_at' => $orderTime,
                        'updated_at' => $orderTime,
                    ]);

                    $totalAmount += $itemAmount;
                }

                // Update Order total_amount
                $order->update(['total_amount' => $totalAmount]);
            }
        }
    }
}
