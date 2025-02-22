<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Facades\Filament;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $label = 'Cliente';
    protected static ?string $pluralLabel = 'Clientes';
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ci_nit')
                    ->label('CI/NIT')
                    ->required(),
                Forms\Components\TextInput::make('razonSocial')
                    ->required(),
                Forms\Components\TextInput::make('nombre'),
                Forms\Components\TextInput::make('celular'),
                Forms\Components\TextInput::make('email')
                    ->email(),
                Forms\Components\TextInput::make('direccion'),
                Forms\Components\TextInput::make('observacion'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('razonSocial')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('celular')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('direccion')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('observacion')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('estado')
                    ->alignment('center')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado en')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado en')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('User.name')
                    ->label('Creado por')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
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
