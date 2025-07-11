<?php

namespace App\Filament\Resources\OrderItemResource\Pages;

use App\Filament\Exports\OrderItemExporter;
use App\Filament\Imports\OrderItemImporter;
use App\Filament\Resources\OrderItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrderItems extends ListRecords
{
    protected static string $resource = OrderItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Créer'),
            Actions\ImportAction::make()
                ->importer(OrderItemImporter::class)
                ->label('Importer'),
            Actions\ExportAction::make()
                ->exporter(OrderItemExporter::class)
                ->label('Exporter'),
        ];
    }
}
