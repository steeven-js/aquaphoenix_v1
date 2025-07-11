<?php

namespace App\Filament\Imports;

use App\Models\Customer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class CustomerImporter extends Importer
{
    protected static ?string $model = Customer::class;

    // Configuration spécifique pour Laravel Cloud
    protected static ?string $queue = 'exports';

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255']),
            ImportColumn::make('address')
                ->rules(['max:255']),
            ImportColumn::make('phone1')
                ->rules(['max:255']),
            ImportColumn::make('phone2')
                ->rules(['max:255']),
            ImportColumn::make('code')
                ->rules(['max:255']),
            ImportColumn::make('commune')
                ->rules(['max:255']),
        ];
    }

    public function resolveRecord(): ?Customer
    {
        return Customer::firstOrNew([
            'email' => $this->data['email'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Votre import de clients est terminé et ' . number_format($import->successful_rows) . ' ' . str('ligne')->plural($import->successful_rows) . ' importée(s).';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('ligne')->plural($failedRowsCount) . ' ont échoué lors de l\'importation.';
        }

        return $body;
    }
} 