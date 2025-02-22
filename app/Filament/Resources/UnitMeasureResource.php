<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitMeasureResource\Pages;
use App\Filament\Resources\UnitMeasureResource\RelationManagers;
use App\Models\UnitMeasure;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Facades\Filament;

class UnitMeasureResource extends Resource
{
    protected static ?string $model = UnitMeasure::class;
    protected static ?string $label = 'Unidad de Medida';
    protected static ?string $pluralLabel = 'Unidades de Medida';
    protected static ?string $navigationLabel = 'Unidad de Medida';
    protected static ?string $navigationGroup = 'Inventarios';
    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required(),
                Forms\Components\TextInput::make('abreviatura')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('abreviatura')
                    ->searchable(),
                    Tables\Columns\IconColumn::make('estado')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado el')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado el')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_by')
                    ->label('Creado por')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($state) {
                          $relatedRecord = User::find($state);
                          return $relatedRecord ? $relatedRecord->name : 'Desconocido';
                      })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
              Tables\Actions\EditAction::make()
                ->icon('heroicon-s-pencil-square')
                ->label('')
                ->tooltip('Editar registro')
                ->modalHeading('Editar Unidad de Medida')
                ->modalWidth('md')
                ->modalDescription('Modifique los datos de la unidad de medida')
                ->modalIcon('heroicon-o-pencil-square'),
              Tables\Actions\DeleteAction::make()
                ->icon('heroicon-s-trash')
                ->label('')
                ->tooltip('Eliminar registro')
                ->action(fn ($record) => $record->update(['estado' => 0]))
                ->requiresConfirmation()
                ->hidden(fn ($record) => $record->estado === 0),
              Tables\Actions\Action::make('Activar')
                ->icon('heroicon-s-trash')
                ->color('info')
                ->label('')
                ->tooltip('Activar registro')
                ->action(fn ($record) => $record->update(['estado' => 1]))
                ->requiresConfirmation()
                ->modalHeading('Activar registro')
                ->modalDescription('¿Estás seguro de que deseas activar este registro?')
                ->hidden(fn ($record) => $record->estado === 1),
          ])
          ->actionsColumnLabel('Acciones')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnitMeasures::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();
        $role = $user->getRoleNames()->first();
        return parent::getEloquentQuery()
          ->when($role !== 'Super Admin', fn ($query) => 
              $query->where('estado', 1)
          );
    }
}
