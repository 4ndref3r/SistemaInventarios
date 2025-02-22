<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductServiceResource\Pages;
use App\Filament\Resources\ProductServiceResource\RelationManagers;
use App\Models\Category;
use App\Models\ProductService;
use App\Models\SupplyMaterial;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Facades\Filament;

class ProductServiceResource extends Resource
{
    protected static ?string $model = ProductService::class;
    protected static ?string $label = 'Producto/Servicio';
    protected static ?string $pluralLabel = 'Productos/Servicios';
    protected static ?string $navigationLabel = 'Productos/Servicios';
    protected static ?string $navigationGroup = 'Inventarios';
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?int $navigationSort = 8;

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
          ->label('Categoría')
          ->options(Category::where('tipo_categoria', 'SEGUNDO')->pluck('nombre', 'id'))
          ->searchable()
          ->required(),
        Forms\Components\TextInput::make('costo_neto')
          ->live()
          ->required()
          ->numeric()
          ->afterStateUpdated(function ($state, callable $get, callable $set) {
            $cn = $state;
            $mo = $get('mano_obra');
            $mg = 1 + $get('margen_ganancia') / 100;
            $pv = ($mo + $cn) * $mg;
            $set('precio_venta', $pv);
          }),
        Forms\Components\TextInput::make('mano_obra')
          ->required()
          ->numeric()
          ->reactive()
          ->debounce(1000)
          ->afterStateUpdated(function ($state, callable $get, callable $set) {
            $mo = $state;
            $cn = $get('costo_neto');
            $mg = 1 + $get('margen_ganancia') / 100;
            $pv = ($mo + $cn) * $mg;
            $set('precio_venta', $pv);
          }),
        Forms\Components\TextInput::make('margen_ganancia')
          ->required()
          ->numeric()
          ->reactive()
          ->suffix(' %')
          ->required()
          ->numeric()
          ->debounce(800)
          ->afterStateUpdated(function ($state, callable $get, callable $set) {
            $mg = 1 + $state / 100;
            $cn = $get('costo_neto');
            $mo = $get('mano_obra');
            $pv = ($mo + $cn) * $mg;
            $set('precio_venta', $pv);
          }),
        Forms\Components\TextInput::make('precio_venta')
          ->numeric()
          ->reactive()
          ->debounce(1000)
          ->afterStateUpdated(function ($state, callable $get, callable $set) {
            $pv = $state;
            $pr = $get('costo_neto') + $get('mano_obra');
            $mg = round((($pv - $pr) * 100 / $pr), 1);
            $set('margen_ganancia', $mg);
          }),
        Forms\Components\Toggle::make('tiene_iva')
          ->required(),
        Forms\Components\Fieldset::make('Materiales utilizados')
          ->schema([
            Forms\Components\Repeater::make('materials')
              ->label(false)
              ->relationship('materials')
              ->schema([
                Forms\Components\Select::make('supply_material_id')
                  ->label('Suministro | Material')
                  ->options(SupplyMaterial::all()->pluck('nombre', 'id'))
                  ->required()
                  ->placeholder('Seleccione material')
                  ->columnSpan(4)
                  ->reactive()
                  ->afterStateUpdated(function ($state, callable $set, callable $get, $livewire) {
                    $material = SupplyMaterial::find($state);
                    if ($material) {
                      $set('precio_unitario', $material->precio_compra);
                      if ($material->precio_venta) {
                        $set('precio_unitario', $material->precio_venta);
                      }
                    }
                    $costoTotalNeto = collect($livewire->data['materials'])
                      ->sum(fn($item) => ($item['cantidad'] ?? 0) * ($item['precio_unitario'] ?? 0));
                        $livewire->data['costo_neto'] = $costoTotalNeto;
                      }),
                Forms\Components\TextInput::make('cantidad')
                  ->numeric()
                  ->required()
                  ->default(1)
                  ->reactive()
                  ->columnSpan(4)
                  ->afterStateUpdated(function ($state, callable $get, callable $set, $livewire) {
                    $cantidad = $state;
                    $precioUnitario = $get('precio_unitario');
                    $costoNeto = $cantidad * $precioUnitario;
                    $totalCostoNeto = collect($livewire->data['materials'])
                      ->sum(fn($item) => ($item['cantidad'] ?? 0) * ($item['precio_unitario'] ?? 0));
                    $livewire->data['costo_neto'] = $totalCostoNeto;
                  }),
                Forms\Components\TextInput::make('precio_unitario')
                  ->numeric()
                  ->required()
                  ->reactive()
                  ->columnSpan(4),
              ])
              ->columns(12)
              ->addActionLabel('Agregar material')
              ->minItems(1),
          ])
          ->columnSpan('full')
          ->columns(1)
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
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('costo_neto')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mano_obra')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('margen_ganancia')
                    ->label('Ganancia')
                    ->numeric()
                    ->alignment(Alignment::Center)
                    ->suffix(' %')
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_venta')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('tiene_iva')
                    ->label('IVA')
                    ->boolean(),
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
            'index' => Pages\ListProductServices::route('/'),
            'create' => Pages\CreateProductService::route('/create'),
            'edit' => Pages\EditProductService::route('/{record}/edit'),
        ];
    }

    protected static function updateMaterialCost(callable $get, callable $set)
    {
        $materials = $get('SupplyMaterials');
        $totalCost = 2;

        foreach ($materials as $material) {
            // Extraemos la cantidad y el precio unitario de cada material
            $cantidad = isset($material['cantidad']) ? $material['cantidad'] : 0;
            $precioUnitario = isset($material['precio_unitario']) ? $material['precio_unitario'] : 0;
    
            // Calculamos el costo neto para este material y lo sumamos al total
            $totalCost += $cantidad * $precioUnitario;
        }

        $set('costo_neto', $totalCost);
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
