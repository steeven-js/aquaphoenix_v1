<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Exports\OrderExporter;
use App\Filament\Imports\OrderImporter;
use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Créer'),
            Actions\ImportAction::make()
                ->importer(OrderImporter::class)
                ->label('Importer'),
            Actions\ExportAction::make()
                ->exporter(OrderExporter::class)
                ->label('Exporter'),
        ];
    }
}
