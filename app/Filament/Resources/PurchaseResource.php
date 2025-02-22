<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Filament\Resources\PurchaseResource\RelationManagers;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplyMaterial;
use App\Models\User;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Facades\Filament;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;
    protected static ?string $label = 'Compra';
    protected static ?string $pluralLabel = 'Compras';
    protected static ?string $navigationLabel = 'Compras';
    protected static ?string $navigationGroup = 'Compras y Ventas';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationSort = 12;

    public static function form(Form $form): Form
    {
    return $form
      ->schema([
        Forms\Components\TextInput::make('nro_documento')
          ->required(),
        Forms\Components\TextInput::make('nro_autorizacion'),
        Forms\Components\Select::make('supplier_id')
          ->label('Proveedor')
          ->options(Supplier::query()->selectRaw("id, razonSocial || ' - ' || nombre as display_name")->pluck('display_name', 'id'))
          ->searchable()
          ->required(),
        Forms\Components\DatePicker::make('fecha_compra')
          ->default(now())
          ->required(),
        Forms\Components\DatePicker::make('fecha_vencimiento'),
        Forms\Components\Select::make('moneda')
          ->options([
            'BOB' => 'Bolivianos',
            'USD' => 'Dólares',
          ])
          ->default('BOB')
          ->required(),
        Forms\Components\TextInput::make('tipo_cambio')
          ->default(6.96)
          ->required()
          ->numeric(),
        Forms\Components\TextInput::make('subtotal')
          ->reactive()
          ->required()
          ->numeric(),
        Forms\Components\TextInput::make('iva')
          ->numeric()
          ->required(),
        Forms\Components\TextInput::make('total')
          ->required()
          ->numeric(),
        Forms\Components\TextInput::make('pagado')
          ->required()
          ->reactive()
          ->numeric()
          ->debounce(500)
          ->afterStateUpdated(function ($state, $get, callable $set) {
            $total=$get('total');
            $pendiente = $total- $state;
            $set('pago_pendiente', $pendiente);
            $set('estado_pago', $pendiente== 0 ? 'PAGADO' : ($pendiente == $total ? 'PENDIENTE':'PARCIAL'));
            $set('tipo_compra', $pendiente== 0 ? 'CONTADO' : 'CREDITO');
          }),
        Forms\Components\TextInput::make('pago_pendiente')
          ->required()
          ->default(0)
          ->numeric(),
        Forms\Components\Select::make('tipo_compra')
          ->default('CONTADO')
          ->options([
            'CONTADO' => 'Contado',
            'CREDITO' => 'Crédito',
          ])
          ->required(),
        Forms\Components\Select::make('estado_pago')
          ->default('PAGADO')
          ->options([
            'PENDIENTE' => 'Pendiente',
            'PARCIAL' => 'Parcial',
            'PAGADO' => 'Pagado',
          ])
          ->required(),
        Forms\Components\DatePicker::make('fecha_plazo'),
        Forms\Components\TextInput::make('observacion'),
        Forms\Components\Repeater::make('purchaseDetails')
          ->label('Materiales')
          ->relationship('purchaseDetails')
          ->schema([
            Forms\Components\Select::make('product_material_id')
              ->label('Material')
              ->options(SupplyMaterial::all()->pluck('nombre', 'id'))
              ->required()
              ->reactive()
              ->columnSpan(3)
              ->afterStateUpdated(function ($state, callable $set) {
                $material = SupplyMaterial::find($state);
                if ($material) {
                  $set('precio_unitario', $material->precio_compra);
                  $set('subtotal', $material->precio_compra * 1);
                }
              }),
            Forms\Components\TextInput::make('cantidad')
              ->label('Cantidad')
              ->required()
              ->default(1)
              ->numeric()
              ->columnSpan(3)
              ->reactive()
              ->live(onBlur: true)
              ->afterStateUpdated(function ($state, $get, callable $set, $livewire) {
                $cantidad = $state;
                $precioUnitario = $get('precio_unitario') ?? 0;
                $subtotal = $state * $precioUnitario;
                $set('subtotal', $subtotal);
                $livewire->calculateTotals();
              }),
            Forms\Components\TextInput::make('precio_unitario')
              ->label('Precio Unitario')
              ->required()
              ->numeric()
              ->columnSpan(3)
              ->live(onBlur: true)
              ->reactive()
              ->afterStateUpdated(function ($state, $get, callable $set, $livewire) {
                  $precioUnitario = $state;
                  $cantidad = $get('cantidad') ?? 1;
                  $subtotal = $precioUnitario * $cantidad;
                  $set('subtotal', $subtotal);
                  $livewire->calculateTotals();
              }),
            Forms\Components\TextInput::make('subtotal')
              ->label('Subtotal')
              ->required()
              ->numeric()
              ->columnSpan(3),
          ])
          ->columns(12)
          ->columnSpan('full')
          ->addActionLabel('Agregar Material')
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nro_documento')
                    ->label('Nro. Doc')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nro_autorizacion')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('Supplier.razonSocial')
                    ->label('Proveedor')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_compra')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_vencimiento')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('moneda')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tipo_cambio')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('iva')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('IVA')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pagado')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('pago_pendiente')
                    ->label('Pendiente')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_compra')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado_pago')
                    ->label('Pago')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                      'PAGADO' => 'success',
                      'PENDIENTE' => 'danger',
                      'PARCIAL' => 'warning',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_plazo')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('observacion')
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
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
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
