<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplyMaterialResource\Pages;
use App\Filament\Resources\SupplyMaterialResource\RelationManagers;
use App\Models\Category;
use App\Models\SupplyMaterial;
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

class SupplyMaterialResource extends Resource
{
    protected static ?string $model = SupplyMaterial::class;
    protected static ?string $label = 'Suministro/Material';
    protected static ?string $pluralLabel = 'Suministros/Materiales';
    protected static ?string $navigationLabel = 'Suministros/Materiales';
    protected static ?string $navigationGroup = 'Inventarios';
    protected static ?string $navigationIcon = 'heroicon-o-fire';
    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo')
                    ->required(),
                Forms\Components\TextInput::make('nombre')
                    ->required(),
                Forms\Components\TextInput::make('descripcion'),
                Forms\Components\Select::make('category_id')
                ->label('Categoria')
                ->options(Category::all()->pluck('nombre', 'id'))
                ->searchable()
                ->required(),
                Forms\Components\Select::make('unit_measure_id')
                ->label('Unidad de Medida')
                ->options(UnitMeasure::all()->pluck('nombre', 'id'))
                ->searchable()
                ->required(),
                Forms\Components\TextInput::make('stock_actual')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('stock_minimo')
                    ->numeric(),
                Forms\Components\TextInput::make('stock_comprometido')
                    ->numeric(),
                Forms\Components\TextInput::make('precio_compra')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('precio_venta')
                    ->numeric(),
                Forms\Components\TextInput::make('ubicacion'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('Category.nombre')
                    ->label('Categoría')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('UnitMeasure.abreviatura')
                    ->label('Unidad Medida')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_actual')
                    ->numeric()
                    ->formatStateUsing(function ($state, $record) {
                          $unidad = $record->UnitMeasure;
                          return $state . ' ' . ($unidad ? $unidad->abreviatura : 'Desconocido');
                      })
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_minimo')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_comprometido')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_compra')
                    ->label('P. Compra')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_venta')
                    ->label('P. Venta')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ubicacion')
                    ->toggleable(isToggledHiddenByDefault: true)
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
              Tables\Actions\EditAction::make()->icon('heroicon-s-pencil-square')->label('')->tooltip('Editar registro'),
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
            'index' => Pages\ListSupplyMaterials::route('/'),
            'create' => Pages\CreateSupplyMaterial::route('/create'),
            'edit' => Pages\EditSupplyMaterial::route('/{record}/edit'),
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
