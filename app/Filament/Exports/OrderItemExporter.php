<?php

namespace App\Filament\Exports;

use App\Models\OrderItem;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class OrderItemExporter extends Exporter
{
    protected static ?string $model = OrderItem::class;

    // Configuration spécifique pour Laravel Cloud
    protected static ?string $queue = 'exports';

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('order_id')
                ->label('ID Commande'),
            ExportColumn::make('order.number')
                ->label('Numéro de commande'),
            ExportColumn::make('order.customer.name')
                ->label('Client'),
            ExportColumn::make('product_id')
                ->label('ID Produit'),
            ExportColumn::make('product.name')
                ->label('Nom du produit'),
            ExportColumn::make('qty')
                ->label('Quantité'),
            ExportColumn::make('sort')
                ->label('Ordre de tri'),
            ExportColumn::make('created_at')
                ->label('Date de création'),
            ExportColumn::make('updated_at')
                ->label('Date de modification'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Votre export d\'articles de commande est terminé et ' . number_format($export->successful_rows) . ' ' . str('ligne')->plural($export->successful_rows) . ' exportée(s).';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('ligne')->plural($failedRowsCount) . ' ont échoué lors de l\'export.';
        }

        return $body;
    }
}
