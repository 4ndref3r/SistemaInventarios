<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Customer;
use App\Models\ProductService;
use App\Models\Quotation;
use App\Models\Sale;
use App\Traits\HelperTrait;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Facades\Filament;

class SaleResource extends Resource
{
    use HelperTrait;
    protected static ?string $model = Sale::class;
    protected static ?string $label = 'Venta';
    protected static ?string $pluralLabel = 'Ventas';
    protected static ?string $navigationLabel = 'Ventas';
    protected static ?string $navigationGroup = 'Compras y Ventas';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 13;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('quotation_id')
                    ->label('Cotización')
                    ->options(Quotation::all()->pluck('fecha_emision', 'id'))
                    ->required()
                    ->reactive()
                    ->searchable()
                    ->afterStateUpdated(function($state, callable $set){
                      $quotation=Quotation::find($state);
                      $set('customer_id', $quotation->customer_id);
                      $set('subtotal',$quotation? $quotation->subtotal : 0);
                      $set('iva',$quotation? $quotation->iva : 0);
                      $set('total',$quotation? $quotation->total : 0);
                      $set('pagado',$quotation? $quotation->total : 0);
                      $set('descuento',$quotation? $quotation->descuento : 0);
                      $set('saleProduct', $quotation->quotationProduct->map(function ($item){
                        return [
                          'product_service_id'=>$item->product_service_id,
                          'cantidad' =>$item->cantidad,
                          'precio_unitario' =>$item->precio_unitario,
                        ];
                      })->toArray());
                    }),
                Forms\Components\Select::make('customer_id')
                    ->label('Cliente')
                    ->options(Customer::all()->pluck('nombre', 'id'))
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('nro_documento')
                    ->required()
                    ->default(fn () => str_pad(Sale::latest()->first()?->id + 1 ?? 1, 6, '0', STR_PAD_LEFT)),
                Forms\Components\TextInput::make('nro_autorizacion'),
                Forms\Components\DatePicker::make('fecha_venta')
                    ->default(now())
                    ->required(),
                Forms\Components\Select::make('moneda')
                    ->options([
                        'BOB' => 'Bolivianos',
                        'USD' => 'Dólares',
                    ])
                    ->default('BOB')
                    ->required(),
                Forms\Components\TextInput::make('tipo_cambio')
                    ->required()
                    ->default(6.96)
                    ->numeric(),
                Forms\Components\TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('iva')
                    ->numeric(),
                Forms\Components\TextInput::make('descuento')
                    ->numeric(),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('pagado')
                    ->required()
                    ->numeric()
                    ->reactive()
                    ->debounce(500)
                    ->afterStateUpdated(function($state, $get, callable $set){
                      $total=$get('total');
                      $saldo=round($total-$state,2);
                      $set('saldo_pendiente', $saldo);
                      $set('tipo_compra', $saldo == 0 ? 'CONTADO' : 'CREDITO');
                      $set('estado_pago', $saldo == 0 ? 'PAGADO' : ($saldo == $total ? 'PENDIENTE' : 'PARCIAL'));
                    }),
                Forms\Components\TextInput::make('saldo_pendiente')
                    ->default(0)
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('tipo_compra')
                    ->options([
                        'CONTADO' => 'Contado',
                        'CREDITO' => 'Crédito',
                    ])
                    ->default('CONTADO')
                    ->required(),
                Forms\Components\Select::make('estado_pago')
                    ->options([
                        'PENDIENTE' => 'Pendiente',
                        'PARCIAL' => 'Parcial',
                        'PAGADO' => 'Pagado',
                    ])
                    ->default('PAGADO')
                    ->required(),
                Forms\Components\Select::make('forma_pago')
                    ->options([
                        'EFECTIVO' => 'Efectivo',
                        'CHEQUE' => 'Cheque',
                        'QR' => 'QR',
                        'TRANSF. BANCARIA' => 'Transferencia Bancaria',
                    ])
                    ->default('EFECTIVO')
                    ->required(),
                Forms\Components\DatePicker::make('fecha_plazo'),
                Forms\Components\TextInput::make('observacion'),
                Forms\Components\Repeater::make('saleProduct')
                ->label('Productos/Servicios')
                ->relationship('saleProduct')
                ->schema([
                    Forms\Components\Select::make('product_service_id')
                        ->label('Producto/Servicio')
                        ->options(ProductService::all()->pluck('nombre', 'id'))
                        ->required()
                        ->reactive()
                        ->columnSpan(4)
                        ->afterStateUpdated(function ($state, callable $set, $livewire) {
                          $product = ProductService::find($state);
                          if ($product) {
                            $set('precio_unitario', $product->precio_venta);
                          }
                          $livewire->calculateTotals();
                        }),
                    Forms\Components\TextInput::make('cantidad')
                        ->label('Cantidad')
                        ->required()
                        ->default(1)
                        ->numeric()
                        ->reactive()
                        ->columnSpan(4)
                        ->afterStateUpdated(function ($state, $livewire){
                          $livewire->calculateTotals();
                        }),
                    Forms\Components\TextInput::make('precio_unitario')
                        ->label('Precio Unitario')
                        ->required()
                        ->numeric()
                        ->columnSpan(4),
                ])
                ->addActionLabel('Agregar Producto/Servicio')
                ->columns(12)
                ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nro_documento')
                    ->label('Nro Fact')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nro_autorizacion')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('Customer.razonSocial')
                    ->label('Cliente')
                    ->size(TextColumn\TextColumnSize::Small)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_venta')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('moneda')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_cambio')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('iva')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('descuento')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pagado')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('saldo_pendiente')
                    ->label('Saldo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_compra')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado_pago')
                    ->searchable(),
                Tables\Columns\TextColumn::make('forma_pago')
                    ->toggleable(isToggledHiddenByDefault: true)
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
              Tables\Actions\Action::make('Generar Pdf')
                  ->icon('heroicon-o-printer')
                  ->label('')
                  ->tooltip('Generar Pdf')
                  ->action(function (Sale $sale){
                    return redirect()->route('ventas.show', ['sale' => $sale->id]);
                  })
                  ->requiresConfirmation()
                  ->modalHeading('Generar PDF de la Venta')
                  ->modalSubmitActionLabel('Generar PDF'),
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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
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
