<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Services\FirestoreSimple;

class DebugSoldCountUpdate extends Command
{
    protected $signature = 'debug:sold-count-update {product_id}';
    protected $description = 'Debug sold count update to check for data loss';

    public function handle()
    {
        $productId = $this->argument('product_id');

        $this->info('🔍 Debugging sold count update...');
        $this->info(str_repeat('=', 80));
        $this->line("Product ID: {$productId}");
        $this->line("");

        try {
            $firestore = new FirestoreSimple();
            $productModel = new Product();

            // Step 1: Get product before update
            $this->info('Step 1: Getting product data BEFORE update');
            $productBefore = $firestore->getDocument('products', $productId);

            if (!$productBefore) {
                $this->error('Product not found');
                return 1;
            }

            $this->line("📋 BEFORE UPDATE:");
            foreach ($productBefore as $key => $value) {
                $displayValue = is_array($value) ? json_encode($value) : $value;
                $this->line("  {$key}: {$displayValue}");
            }

            $soldCountBefore = $productBefore['sold_count'] ?? 0;
            $this->line("");
            $this->line("🎯 Current sold_count: {$soldCountBefore}");

            // Step 2: Perform increment
            $this->info('Step 2: Performing sold count increment...');
            $result = $productModel->incrementSoldCount($productId);

            if (!$result) {
                $this->error('Failed to increment sold count');
                return 1;
            }

            $this->line("✅ Increment operation completed");

            // Step 3: Get product after update
            $this->info('Step 3: Getting product data AFTER update');
            $productAfter = $firestore->getDocument('products', $productId);

            $this->line("📋 AFTER UPDATE:");
            foreach ($productAfter as $key => $value) {
                $displayValue = is_array($value) ? json_encode($value) : $value;

                // Check if value changed
                $beforeValue = $productBefore[$key] ?? 'NOT_SET';
                $changed = ($beforeValue !== $value) ? ' 🔄 CHANGED' : '';

                $this->line("  {$key}: {$displayValue}{$changed}");
            }

            // Step 4: Compare data
            $this->line("");
            $this->info('Step 4: Data comparison');

            $soldCountAfter = $productAfter['sold_count'] ?? 0;
            $this->line("🎯 sold_count: {$soldCountBefore} → {$soldCountAfter}");

            // Check for lost fields
            $lostFields = [];
            foreach ($productBefore as $key => $value) {
                if (!isset($productAfter[$key])) {
                    $lostFields[] = $key;
                }
            }

            if (!empty($lostFields)) {
                $this->error("❌ LOST FIELDS: " . implode(', ', $lostFields));
            } else {
                $this->line("✅ No fields lost");
            }

            // Check for modified fields (other than sold_count and updated_at)
            $modifiedFields = [];
            foreach ($productBefore as $key => $value) {
                if (
                    isset($productAfter[$key]) && $productAfter[$key] !== $value &&
                    !in_array($key, ['sold_count', 'updated_at'])
                ) {
                    $modifiedFields[] = $key;
                }
            }

            if (!empty($modifiedFields)) {
                $this->warn("⚠️  MODIFIED FIELDS (unexpected): " . implode(', ', $modifiedFields));
                foreach ($modifiedFields as $field) {
                    $this->line("  {$field}: '{$productBefore[$field]}' → '{$productAfter[$field]}'");
                }
            } else {
                $this->line("✅ No unexpected field modifications");
            }

            $this->line("");
            if ($soldCountAfter === $soldCountBefore + 1 && empty($lostFields) && empty($modifiedFields)) {
                $this->info("🎉 Update successful - no data loss detected!");
            } else {
                $this->error("❌ Issues detected in update process");
            }
        } catch (\Exception $e) {
            $this->error("Debug failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
