<?php

namespace App\Filament\Resources\SupplyMaterialResource\Pages;

use App\Filament\Resources\SupplyMaterialResource;
use App\Imports\MaterialImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ListSupplyMaterials extends ListRecords
{
    protected static string $resource = SupplyMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            \EightyNine\ExcelImport\ExcelImportAction::make()
                  ->slideOver()
                  ->color('success')
                  ->label('Import Excel')
                  ->use(MaterialImport::class),
        ];
    }
}
