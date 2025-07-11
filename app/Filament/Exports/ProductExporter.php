<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    // Configuration spécifique pour Laravel Cloud
    protected static ?string $queue = 'exports';

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name')
                ->label('Nom'),
            ExportColumn::make('slug')
                ->label('Slug'),
            ExportColumn::make('description')
                ->label('Description'),
            ExportColumn::make('is_visible')
                ->label('Visible'),
            ExportColumn::make('published_at')
                ->label('Date de publication'),
            ExportColumn::make('created_at')
                ->label('Date de création'),
            ExportColumn::make('updated_at')
                ->label('Date de modification'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Votre export de produits est terminé et ' . number_format($export->successful_rows) . ' ' . str('ligne')->plural($export->successful_rows) . ' exportée(s).';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('ligne')->plural($failedRowsCount) . ' ont échoué lors de l\'export.';
        }

        return $body;
    }
}
