<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $label = 'Categoria';
    protected static ?string $pluralLabel = 'Categorias';
    protected static ?string $navigationLabel = 'Categorias';
    protected static ?string $navigationGroup = 'Inventarios';
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo')
                    ->required(),
                Forms\Components\TextInput::make('nombre')
                    ->required(),
                Forms\Components\TextInput::make('descripcion'),
                Forms\Components\Select::make('tipo_categoria')
                ->options([
                    'PRIMERO' => 'Suministros/Materiales',
                    'SEGUNDO' => 'Productos/Servicios',
                ])
                    ->required(),
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
          ->searchable(),
        Tables\Columns\TextColumn::make('tipo_categoria')
          ->searchable()
          ->badge()
          ->color(fn (string $state): string => match ($state) {
              'PRIMERO' => 'info',
              'SEGUNDO' => 'success',
          })
          ->formatStateUsing(function ($state) {
            return match ($state) {
              'PRIMERO' => 'Suministros | Material',
              'SEGUNDO' => 'Servicios | Productos'
            };
            }),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
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
