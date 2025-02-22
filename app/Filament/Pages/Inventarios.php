<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\Employee;
use App\Models\ProductService;
use App\Models\SupplyMaterial;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Carbon\Carbon;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;

class Inventarios extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use interactsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.inventarios';
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?string $title = 'Reporte de Inventarios';
    protected static ?int $navigationSort = 22;
    public ?array $data = [];
    public $showTable = false;
    public function mount(): void
    {
        $this->form->fill([
            'fecha_fin' => now()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema())
            ->statePath('data')
            ->live();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->getTableQuery())
            ->columns([
              TextColumn::make('index')
                  ->label('#')
                  ->getStateUsing(fn ($rowLoop) => $rowLoop->iteration),
              TextColumn::make('codigo')
                  ->label('Código')
                  ->sortable(),
              TextColumn::make('nombre')
                  ->label('Descripción')
                  ->sortable(),
              TextColumn::make('unitMeasure.abreviatura')
                  ->label('Unidad')
                  ->hidden(fn () => $this->data['tipo_categoria'] === 'SEGUNDO')
                  ->sortable(),
              TextColumn::make('category.nombre')
                  ->label('Categoría')
                  ->sortable(),
              TextColumn::make('stock_actual')
                  ->label('Cantidad')
                  ->sortable()
                  ->hidden(fn () => $this->data['tipo_categoria'] === 'SEGUNDO'),
              TextColumn::make('precio')
                  ->label('Precio')
                  ->getStateUsing(fn ($record) => 
                      $this->data['tipo_categoria'] === 'PRIMERO' 
                          ? $record?->precio_compra 
                          : $record?->precio_venta
                  )
                  ->sortable()
                  ->formatStateUsing(fn ($state) => number_format($state, 3)),
              TextColumn::make('total')
                  ->label('Total')
                  ->getStateUsing(fn ($record) => 
                      $this->data['tipo_categoria'] === 'PRIMERO' 
                          ? (($record->stock_actual ?? 0) * ($record->precio_compra ?? 0)) 
                          : ($record->precio_venta ?? 0)
                  )
                  ->formatStateUsing(fn ($state) => number_format($state, 2)), 
          ])
          ->defaultSort('codigo','desc')
          ->headerActions([
              TableAction::make('export')
                    ->label('Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->button()
                    ->color('success')
                    ->action(fn () => $this->exportToExcel()),
              TableAction::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document')
                    ->button()
                    ->color('danger')
                    ->action(fn () => $this->generatePDF()),
          ]);
    }

    protected function getTableQuery():Builder
    {
    $query = null;
        if ($this->data['tipo_categoria']=='PRIMERO') {
          $query = SupplyMaterial::query()
            ->where('estado','1')
            ->select(['id','codigo','nombre','unit_measure_id','category_id','stock_actual', 'precio_compra'])
            ->with(['UnitMeasure','Category']);
        } else {
          $query = ProductService::query()
            ->where('estado', '1')
            ->select(['id', 'codigo', 'nombre', 'category_id', 'precio_venta'])
            ->with('Category');
        }
        if (!$this->showTable) {
            return $query->whereRaw('1 = 0');
        }
        if (!empty($this->data['category_id'])) {
            $query->where('category_id', $this->data['category_id']);
        }
        return $query;
    }

    public function getFormSchema(): array
    {
      return [
        Grid::make(4)
          ->schema([
            Select::make('tipo_categoria')
              ->label('Tipo')
              ->default('PRIMERO')
              ->options([
                'PRIMERO' => 'Suministro | Material',
                'SEGUNDO' => 'Servicio | Producto',
              ])
              ->required()
              ->live()
              ->reactive(),
            Select::make('category_id')
              ->label('Categoría')
              ->options(fn (callable $get) => 
                    Category::where('estado', '1')
                        ->where('tipo_categoria', $get('tipo_categoria')) // Filtra por tipo seleccionado
                        ->pluck('nombre', 'id')
                )
              ->live(),
          ])
      ];
    }

    public function generate()
    {
      $this->showTable=true;
      $this->dispatch('refresh-table');
    }

    public function resetForm()
    {
        $this->form->fill([
            'tipo_categoria' => 'PRIMERO',
            'categoria' => null,
        ]);
        $this->showTable = false;
        $this->dispatch('refresh-table');
    }

    protected function exportToExcel()
    {
        return redirect()->route('inventory.report', $this->data);
    }

    protected function generatePDF()
    {
        return redirect()->route('inventory.show', $this->data);
    }
}
