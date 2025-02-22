<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManufacturingOrderResource\Pages;
use App\Filament\Resources\ManufacturingOrderResource\RelationManagers;
use App\Models\Employee;
use App\Traits\HelperTrait;
use App\Models\ManufacturingOrder;
use App\Models\ProductService;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Facades\Filament;

class ManufacturingOrderResource extends Resource
{
  use HelperTrait;
    protected static ?string $model = ManufacturingOrder::class;
    protected static ?string $label = 'Orden de Trabajo';
    protected static ?string $pluralLabel = 'Ordenes de Trabajo';
    protected static ?string $navigationLabel = 'Orden de Trabajo';
    protected static ?string $navigationGroup = 'Inventarios';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo')
                    ->required()
                    ->default(function ($record){
                        return self::generarCodigoPorModelo('ManufacturingOrder');
                    }),
                Forms\Components\DatePicker::make('fecha_inicio')
                    ->default(now())
                    ->required(),
                Forms\Components\DatePicker::make('fecha_fin'),
                Forms\Components\Select::make('estado_proceso')
                    ->label('Estado del Proceso')
                    ->options([
                        'EN PROCESO' => 'En Proceso',
                        'TERMINADO' => 'Finalizado',
                        'EN DEMORA' => 'Demorado',
                    ])
                    ->default('EN PROCESO')
                    ->required(),
                Forms\Components\Select::make('product_service_id')
                    ->label('Producto/Servicio')
                    ->options(ProductService::all()->pluck('nombre','id'))
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('cantidad')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('observacion'),
                Forms\Components\Repeater::make('employeeOrders')
                    ->label('Empleados')
                    ->relationship('employeeOrders')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('Empleado')
                            ->options(Employee::query()->selectRaw("id , nombres|| ' ' || primer_apellido as nombre_completo")->pluck('nombre_completo','id'))
                            ->required()
                            ->columnSpan(3)
                            ->searchable(),
                        Forms\Components\DatePicker::make('fecha_asignacion')
                            ->label('Fecha de Asignación')
                            ->default(now())
                            ->columnSpan(3)
                            ->required(),
                    ])
                    ->columns(6),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_fin')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado_proceso')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ProductService.nombre')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cantidad')
                    ->alignCenter()
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
            'index' => Pages\ListManufacturingOrders::route('/'),
            'create' => Pages\CreateManufacturingOrder::route('/create'),
            'edit' => Pages\EditManufacturingOrder::route('/{record}/edit'),
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
