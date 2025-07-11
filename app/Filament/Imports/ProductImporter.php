<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    // Configuration spécifique pour Laravel Cloud
    protected static ?string $queue = 'exports';

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('slug')
                ->rules(['max:255']),
            ImportColumn::make('description')
                ->rules(['max:65535']),
            ImportColumn::make('is_visible')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('published_at')
                ->rules(['nullable', 'date']),
        ];
    }

    public function resolveRecord(): ?Product
    {
        // Gérer les dates vides en les convertissant en null
        $data = $this->data;
        if (isset($data['published_at']) && empty($data['published_at'])) {
            $data['published_at'] = null;
        }
        
        $product = Product::firstOrNew([
            'slug' => $data['slug'] ?? null,
        ]);
        
        // Appliquer les données nettoyées
        $product->fill($data);
        
        return $product;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Votre import de produits est terminé et ' . number_format($import->successful_rows) . ' ' . str('ligne')->plural($import->successful_rows) . ' importée(s).';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('ligne')->plural($failedRowsCount) . ' ont échoué lors de l\'importation.';
        }

        return $body;
    }
}
