<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManufacturingMaterialResource\Pages;
use App\Filament\Resources\ManufacturingMaterialResource\RelationManagers;
use App\Models\Employee;
use App\Models\ManufacturingMaterial;
use App\Models\ManufacturingOrder;
use App\Models\SupplyMaterial;
use App\Models\UnitMeasure;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;

class ManufacturingMaterialResource extends Resource
{
    protected static ?string $model = ManufacturingMaterial::class;
    protected static ?string $label = 'Asignar Material';
    protected static ?string $pluralLabel = 'Asignaciones de materiales';
    protected static ?string $navigationLabel = 'Asignacion Material';
    protected static ?string $navigationGroup = 'Inventarios';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('manufacturing_order_id')
                    ->label('Orden de Trabajo')
                    ->options(ManufacturingOrder::query()->selectRaw("codigo || ' - '|| fecha_inicio as name, id")->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('employee_id')
                    ->label('Empleado')
                    ->options(Employee::query()->selectRaw("nombres || ' ' || primer_apellido as name, id")->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('tipo')
                    ->options([
                        'ENTRADA' => 'Entrada',
                        'SALIDA' => 'Salida',
                        'PERDIDA' => 'Pérdida',
                        ])
                    ->default('SALIDA')
                    ->required(),
                Forms\Components\DatePicker::make('fecha')
                    ->default(now())
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->label('Entregado por:')
                    ->options(User::all()->pluck('name', 'id'))
                    ->required()
                    ->default(Auth::user()->id)
                    ->searchable(),
                Forms\Components\TextInput::make('observacion'),
                Forms\Components\Repeater::make('supplyMaterials')
                    ->label('Materiales/Suministros')
                    ->relationship('supplyMaterials')
                    ->schema([
                        Forms\Components\Select::make('supply_material_id')
                            ->label('Material')
                            ->options(SupplyMaterial::query()->selectRaw("nombre as name, id")->pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->columnSpan(5)
                            ->searchable()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $material = SupplyMaterial::find($state);
                                $und= UnitMeasure::find($material->unit_measure_id);
                                if ($material) {
                                    $set('stock_actual', $material->stock_actual.' '.$und->abreviatura);
                                } else {
                                    $set('stock_actual', 0 .' '.$und->abreviatura);
                                }
                            }),
                        Forms\Components\TextInput::make('stock_actual')
                            ->label('Stock Actual')
                            ->columnSpan(2)
                            ->disabled()
                            ->reactive(),
                        Forms\Components\TextInput::make('cantidad')
                            ->label('Cantidad')
                            ->required()
                            ->default(1)
                            ->debounce(100)
                            ->columnSpan(5)
                            ->numeric()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                              $materialId = $get('supply_material_id');
                              if(!$materialId){
                                return;
                              }
                              $stock = SupplyMaterial::find($materialId)->stock_actual;
                              if ($state > $stock) {
                                  $set('cantidad', $stock);
                              }
                            }),
                    ])
                    ->columns(12)
                    ->columnSpan('full')
                    ->addActionLabel('Agregar Material/Suministro'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ManufacturingOrder.codigo')
                    ->label('Orden de Trabajo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Empleado')
                    ->getStateUsing(function ($record) {
                        return $record->employee->nombres . ' ' . $record->employee->primer_apellido;
                    })
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->label('Entregado por')
                    ->formatStateUsing(function ($state) {
                        $relatedRecord = User::find($state);
                        return $relatedRecord ? $relatedRecord->name : 'Desconocido';
                    })
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('observacion')
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
                    ->alignCenter()
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
                  ->action(function (ManufacturingMaterial $material){
                    return redirect()->route('material.show', ['material' => $material->id]);
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
            'index' => Pages\ListManufacturingMaterials::route('/'),
            'create' => Pages\CreateManufacturingMaterial::route('/create'),
            'edit' => Pages\EditManufacturingMaterial::route('/{record}/edit'),
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
