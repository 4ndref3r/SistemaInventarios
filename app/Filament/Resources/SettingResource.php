<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static ?string $label = 'Información';
    protected static ?string $pluralLabel = 'Informaciones';
    protected static ?string $navigationLabel = 'Config';
    protected static ?string $navigationGroup = 'Configuraciones';
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nit')
                    ->required(),
                Forms\Components\TextInput::make('razonSocial')
                    ->required(),
                Forms\Components\TextInput::make('gerente'),
                Forms\Components\TextInput::make('celular'),
                Forms\Components\TextInput::make('email')
                    ->email(),
                Forms\Components\TextInput::make('direccion'),
                Forms\Components\TextInput::make('cod_orden'),
                Forms\Components\TextInput::make('cod_empleado'),
                Forms\Components\TextInput::make('cod_factura'),
                Forms\Components\TextInput::make('cod_cotizacion'),
                Forms\Components\TextInput::make('iva')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('razonSocial')
                    ->label('Datos de la Empresa')
                    ->description(fn (Setting $record): string => $record->nit.' - '.$record->gerente),
                Tables\Columns\TextColumn::make('email')
                    ->label('Contacto')
                    ->description(fn (Setting $record): string => $record->celular.' - '.$record->direccion),
                Tables\Columns\TextColumn::make('cod_orden')
                    ->label('Orden')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('cod_empleado')
                    ->label('Empleado')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('cod_factura')
                    ->label('Factura')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('cod_cotizacion')
                    ->label('Cotización')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('iva')
                    ->numeric()
                    ->suffix(' %')
                    ->alignCenter()
                    ->label('IVA'),
                Tables\Columns\IconColumn::make('estado')
                    ->alignCenter()
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->paginated(false);
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
