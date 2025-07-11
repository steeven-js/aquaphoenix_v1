<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Exports\CustomerExporter;
use App\Filament\Imports\CustomerImporter;
use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Créer'),
            Actions\ImportAction::make()
                ->importer(CustomerImporter::class)
                ->label('Importer'),
            Actions\ExportAction::make()
                ->exporter(CustomerExporter::class)
                ->label('Exporter'),
        ];
    }
}
