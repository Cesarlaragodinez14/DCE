<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\CatTipoDeAuditoria;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\CheckboxColumn;
use App\Filament\Resources\Panel\CatTipoDeAuditoriaResource\Pages;
use App\Filament\Resources\Panel\CatTipoDeAuditoriaResource\RelationManagers;

class CatTipoDeAuditoriaResource extends Resource
{
    protected static ?string $model = CatTipoDeAuditoria::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.catTipoDeAuditorias.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.catTipoDeAuditorias.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.catTipoDeAuditorias.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    TextInput::make('valor')
                        ->required()
                        ->string()
                        ->unique(
                            'cat_tipo_de_auditoria',
                            'valor',
                            ignoreRecord: true
                        )
                        ->autofocus(),

                    RichEditor::make('descripcion')
                        ->nullable()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    Checkbox::make('activo')
                        ->rules(['boolean'])
                        ->required()
                        ->inline(),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([
                TextColumn::make('valor'),

                TextColumn::make('descripcion')->limit(255),

                CheckboxColumn::make('activo'),
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
            'index' => Pages\ListCatTipoDeAuditorias::route('/'),
            'create' => Pages\CreateCatTipoDeAuditoria::route('/create'),
            'view' => Pages\ViewCatTipoDeAuditoria::route('/{record}'),
            'edit' => Pages\EditCatTipoDeAuditoria::route('/{record}/edit'),
        ];
    }
}
