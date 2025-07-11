<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Filament\Imports\CustomerImporter;
use App\Filament\Exports\CustomerExporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ImportAction::make()
                ->importer(CustomerImporter::class),
            Actions\ExportAction::make()
                ->exporter(CustomerExporter::class),
        ];
    }
}
