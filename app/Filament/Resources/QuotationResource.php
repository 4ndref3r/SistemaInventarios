<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuotationResource\Pages;
use App\Filament\Resources\QuotationResource\RelationManagers;
use App\Models\Customer;
use App\Models\ProductService;
use App\Models\Quotation;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Facades\Filament;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;
    protected static ?string $label = 'Cotizacion';
    protected static ?string $pluralLabel = 'Cotizaciones';
    protected static ?string $navigationLabel = 'Cotizaciones';
    protected static ?string $navigationGroup = 'Compras y Ventas';
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?int $navigationSort = 14;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->label('Cliente')
                    ->required()
                    ->options(Customer::all()->pluck('nombre', 'id'))
                    ->searchable(),
                Forms\Components\DatePicker::make('fecha_emision')
                    ->default(now())
                    ->required(),
                Forms\Components\DatePicker::make('fecha_validez'),
                Forms\Components\Select::make('moneda')
                    ->options(['USD' => 'Dólares', 'BOB' => 'Bolivianos',])
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
                    ->reactive()
                    ->suffix(' Bs')
                    ->numeric()
                    ->debounce(400)
                    ->afterStateUpdated(function($state, $get, callable $set){
                      $descuento= $state ?? 0;
                      $total=$get('subtotal')+$get('iva');
                      $set('total',round($total-$descuento,2));
                    }),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('observacion'),
                Forms\Components\Repeater::make('quotationProduct')
                    ->label('Productos/Servicios')
                    ->relationship('quotationProduct')
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
                Tables\Columns\TextColumn::make('Customer.nombre')
                    ->label('Cliente')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_emision')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_validez')
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
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('iva')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('descuento')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
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
                  ->action(function (Quotation $quotation){
                    return redirect()->route('cotizacion.show', ['quotation' => $quotation->id]);
                  })
                  ->requiresConfirmation()
                  ->modalHeading('Generar PDF de la cotización')
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
            'index' => Pages\ListQuotations::route('/'),
            'create' => Pages\CreateQuotation::route('/create'),
            'edit' => Pages\EditQuotation::route('/{record}/edit'),
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
