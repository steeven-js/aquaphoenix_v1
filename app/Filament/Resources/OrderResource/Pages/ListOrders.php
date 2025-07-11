<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Imports\OrderImporter;
use App\Filament\Exports\OrderExporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ImportAction::make()
                ->importer(OrderImporter::class),
            Actions\ExportAction::make()
                ->exporter(OrderExporter::class),
        ];
    }
}
