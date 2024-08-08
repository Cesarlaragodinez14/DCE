<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Auditorias;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Panel\AuditoriasResource\Pages;
use App\Filament\Resources\Panel\AuditoriasResource\RelationManagers;

class AuditoriasResource extends Resource
{
    protected static ?string $model = Auditorias::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.allAuditorias.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.allAuditorias.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.allAuditorias.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    TextInput::make('clave_de_accion')
                        ->required()
                        ->string()
                        ->unique(
                            'aditorias',
                            'clave_de_accion',
                            ignoreRecord: true
                        )
                        ->autofocus(),

                    Select::make('entrega')
                        ->required()
                        ->relationship('catSiglasTipoAccion', 'valor')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('auditoria_especial')
                        ->required()
                        ->relationship('catAuditoriaEspecial', 'valor')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('tipo_de_auditoria')
                        ->required()
                        ->relationship('catTipoDeAuditoria', 'valor')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('siglas_auditoria_especial')
                        ->required()
                        ->relationship('catSiglasTipoAccion2', 'valor')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('uaa')
                        ->required()
                        ->relationship('catUaa', 'valor')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    TextInput::make('titulo')
                        ->required()
                        ->string(),

                    Select::make('ente_fiscalizado')
                        ->required()
                        ->relationship('catEnteFiscalizado', 'valor')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    TextInput::make('numero_de_auditoria')
                        ->required()
                        ->numeric()
                        ->step(1),

                    Select::make('ente_de_la_accion')
                        ->required()
                        ->relationship('catEnteDeLaAccion', 'valor')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('clave_accion')
                        ->required()
                        ->relationship('auditorias', 'valor')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('siglas_tipo_accion')
                        ->required()
                        ->relationship('catSiglasTipoAccion3', 'valor')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('dgseg_ef')
                        ->required()
                        ->relationship('catDgsegEf', 'valor')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    TextInput::make('nombre_director_general')
                        ->required()
                        ->string(),

                    TextInput::make('direccion_de_area')
                        ->required()
                        ->string(),

                    TextInput::make('nombre_director_de_area')
                        ->required()
                        ->string(),

                    TextInput::make('sub_direccion_de_area')
                        ->required()
                        ->string(),

                    TextInput::make('nombre_sub_director_de_area')
                        ->required()
                        ->string(),

                    TextInput::make('jefe_de_departamento')
                        ->required()
                        ->string(),

                    Select::make('cuenta_publica')
                        ->required()
                        ->relationship('catCuentaPublica', 'valor')
                        ->searchable()
                        ->preload()
                        ->native(false),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([
                TextColumn::make('clave_de_accion'),

                TextColumn::make('catSiglasTipoAccion.valor'),

                TextColumn::make('catAuditoriaEspecial.valor'),

                TextColumn::make('catTipoDeAuditoria.valor'),

                TextColumn::make('catSiglasTipoAccion2.valor'),

                TextColumn::make('catUaa.valor'),

                TextColumn::make('titulo'),

                TextColumn::make('catEnteFiscalizado.valor'),

                TextColumn::make('numero_de_auditoria'),

                TextColumn::make('catEnteDeLaAccion.valor'),

                TextColumn::make('auditorias.valor'),

                TextColumn::make('catSiglasTipoAccion3.valor'),

                TextColumn::make('catDgsegEf.valor'),

                TextColumn::make('nombre_director_general'),

                TextColumn::make('direccion_de_area'),

                TextColumn::make('nombre_director_de_area'),

                TextColumn::make('sub_direccion_de_area'),

                TextColumn::make('nombre_sub_director_de_area'),

                TextColumn::make('jefe_de_departamento'),

                TextColumn::make('catCuentaPublica.valor'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAllAuditorias::route('/'),
            'create' => Pages\CreateAuditorias::route('/create'),
            'view' => Pages\ViewAuditorias::route('/{record}'),
            'edit' => Pages\EditAuditorias::route('/{record}/edit'),
        ];
    }
}
