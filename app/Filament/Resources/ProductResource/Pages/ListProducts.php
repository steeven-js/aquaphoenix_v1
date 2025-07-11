<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Filament\Imports\ProductImporter;
use App\Filament\Exports\ProductExporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ImportAction::make()
                ->importer(ProductImporter::class),
            Actions\ExportAction::make()
                ->exporter(ProductExporter::class),
        ];
    }
}
