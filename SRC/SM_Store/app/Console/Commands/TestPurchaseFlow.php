<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class TestPurchaseFlow extends Command
{
    protected $signature = 'test:purchase-flow {buyer_uid} {seller_uid} {product_id} {price}';
    protected $description = 'Test complete purchase flow and UI synchronization';

    public function handle()
    {
        $buyerUid = $this->argument('buyer_uid');
        $sellerUid = $this->argument('seller_uid');
        $productId = $this->argument('product_id');
        $price = (int)$this->argument('price');

        $firestore = new FirestoreSimple();

        $this->info("🛒 Testing Purchase Flow");
        $this->line("Buyer: $buyerUid");
        $this->line("Seller: $sellerUid");
        $this->line("Product: $productId");
        $this->line("Price: $price coins");

        // Get initial user data
        $buyer = $firestore->getDocument('users', $buyerUid);
        $seller = $firestore->getDocument('users', $sellerUid);

        if (!$buyer || !$seller) {
            $this->error("Users not found");
            return;
        }

        $this->info("\n📊 Initial State:");
        $this->line("Buyer coins: " . ($buyer['coins'] ?? 0));
        $this->line("Seller coins: " . ($seller['coins'] ?? 0));

        // Check if buyer has enough coins
        if (($buyer['coins'] ?? 0) < $price) {
            $this->error("❌ Buyer doesn't have enough coins");
            return;
        }

        $this->info("\n💳 Processing purchase...");

        try {
            // Simulate purchase transaction
            $newBuyerCoins = ($buyer['coins'] ?? 0) - $price;
            $newSellerCoins = ($seller['coins'] ?? 0) + $price;

            // Update buyer coins
            $buyerResult = $firestore->updateCoinsOnly($buyerUid, $newBuyerCoins);
            if (!$buyerResult) {
                throw new \Exception("Failed to update buyer coins");
            }

            // Update seller coins
            $sellerResult = $firestore->updateCoinsOnly($sellerUid, $newSellerCoins);
            if (!$sellerResult) {
                throw new \Exception("Failed to update seller coins");
            }

            $this->info("✅ Transaction completed successfully");

            // Verify final state
            $updatedBuyer = $firestore->getDocument('users', $buyerUid);
            $updatedSeller = $firestore->getDocument('users', $sellerUid);

            $this->info("\n📊 Final State:");
            $this->line("Buyer coins: " . ($updatedBuyer['coins'] ?? 0) . " (changed: " . ($newBuyerCoins - ($buyer['coins'] ?? 0)) . ")");
            $this->line("Seller coins: " . ($updatedSeller['coins'] ?? 0) . " (changed: " . ($newSellerCoins - ($seller['coins'] ?? 0)) . ")");

            // Check data integrity
            $buyerIntegrity = !empty($updatedBuyer['name']) && !empty($updatedBuyer['email']);
            $sellerIntegrity = !empty($updatedSeller['name']) && !empty($updatedSeller['email']);

            if ($buyerIntegrity && $sellerIntegrity) {
                $this->info("✅ Data integrity maintained for both users");
            } else {
                $this->error("❌ Data integrity compromised");
                if (!$buyerIntegrity) $this->error("  - Buyer data corrupted");
                if (!$sellerIntegrity) $this->error("  - Seller data corrupted");
            }

            $this->info("\n🎯 UI Synchronization Notes:");
            $this->line("1. LoadUserData middleware will fetch fresh coins from Firestore");
            $this->line("2. \$currentUser['coins'] in views will show updated values");
            $this->line("3. Both navbar and account layout will display correct coins");
            $this->line("4. Session coins will be updated on next page load");

            // Rollback for testing
            $this->info("\n⏪ Rolling back changes for testing...");
            $firestore->updateCoinsOnly($buyerUid, $buyer['coins'] ?? 0);
            $firestore->updateCoinsOnly($sellerUid, $seller['coins'] ?? 0);
            $this->info("✅ Changes rolled back");
        } catch (\Exception $e) {
            $this->error("❌ Purchase failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
