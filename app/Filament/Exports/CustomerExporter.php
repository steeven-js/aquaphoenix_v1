<?php

namespace App\Filament\Exports;

use App\Models\Customer;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CustomerExporter extends Exporter
{
    protected static ?string $model = Customer::class;

    // Configuration spécifique pour Laravel Cloud
    protected static ?string $queue = 'exports';

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name')
                ->label('Nom'),
            ExportColumn::make('email')
                ->label('Email'),
            ExportColumn::make('address')
                ->label('Adresse'),
            ExportColumn::make('phone1')
                ->label('Téléphone 1'),
            ExportColumn::make('phone2')
                ->label('Téléphone 2'),
            ExportColumn::make('code')
                ->label('Code'),
            ExportColumn::make('commune')
                ->label('Commune'),
            ExportColumn::make('created_at')
                ->label('Date de création'),
            ExportColumn::make('updated_at')
                ->label('Date de modification'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Votre export de clients est terminé et ' . number_format($export->successful_rows) . ' ' . str('ligne')->plural($export->successful_rows) . ' exportée(s).';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('ligne')->plural($failedRowsCount) . ' ont échoué lors de l\'export.';
        }

        return $body;
    }
}
