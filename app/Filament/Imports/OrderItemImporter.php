<?php

namespace App\Filament\Imports;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class OrderItemImporter extends Importer
{
    protected static ?string $model = OrderItem::class;

    // Configuration spécifique pour Laravel Cloud
    protected static ?string $queue = 'exports';

    public static function getColumns(): array
    {
        return [
            // Import par ID de commande (prioritaire)
            ImportColumn::make('order_id')
                ->numeric()
                ->rules(['integer', 'exists:orders,id']),
            
            // Import par numéro de commande (alternative)
            ImportColumn::make('order_number')
                ->rules(['string', 'exists:orders,number']),
            
            // Import par ID de produit (prioritaire)
            ImportColumn::make('product_id')
                ->numeric()
                ->rules(['integer', 'exists:products,id']),
            
            // Import par nom de produit (alternative)
            ImportColumn::make('product_name')
                ->rules(['string', 'exists:products,name']),
            
            ImportColumn::make('qty')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'min:1']),
            
            ImportColumn::make('sort')
                ->numeric()
                ->rules(['integer', 'min:0'])
                ->default(0),
        ];
    }

    public function resolveRecord(): ?OrderItem
    {
        // Résoudre l'order_id
        $orderId = $this->data['order_id'] ?? null;
        if (!$orderId && !empty($this->data['order_number'])) {
            $order = Order::where('number', $this->data['order_number'])->first();
            $orderId = $order?->id;
        }

        // Résoudre le product_id
        $productId = $this->data['product_id'] ?? null;
        if (!$productId && !empty($this->data['product_name'])) {
            $product = Product::where('name', $this->data['product_name'])->first();
            $productId = $product?->id;
        }

        if (!$orderId || !$productId) {
            return null; // Échec de résolution des relations
        }

        return OrderItem::firstOrNew([
            'order_id' => $orderId,
            'product_id' => $productId,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Votre import d\'articles de commande est terminé et ' . number_format($import->successful_rows) . ' ' . str('ligne')->plural($import->successful_rows) . ' importée(s).';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('ligne')->plural($failedRowsCount) . ' ont échoué lors de l\'importation.';
        }

        return $body;
    }
} 