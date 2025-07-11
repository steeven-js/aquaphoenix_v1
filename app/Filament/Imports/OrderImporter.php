<?php

namespace App\Filament\Imports;

use App\Models\Order;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class OrderImporter extends Importer
{
    protected static ?string $model = Order::class;

    // Configuration spécifique pour Laravel Cloud
    protected static ?string $queue = 'exports';

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('number')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('customer_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'exists:customers,id']),
            ImportColumn::make('status')
                ->requiredMapping()
                ->rules(['required', 'in:en progression,livré,annulé']),
            ImportColumn::make('published_at')
                ->rules(['date']),
            ImportColumn::make('delivered_date')
                ->rules(['date']),
            ImportColumn::make('notes')
                ->rules(['max:65535']),
        ];
    }

    public function resolveRecord(): ?Order
    {
        return Order::firstOrNew([
            'number' => $this->data['number'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Votre import de commandes est terminé et ' . number_format($import->successful_rows) . ' ' . str('ligne')->plural($import->successful_rows) . ' importée(s).';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('ligne')->plural($failedRowsCount) . ' ont échoué lors de l\'importation.';
        }

        return $body;
    }
} 