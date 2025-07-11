<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

/**
 * Ressource Filament pour gérer les produits/désignations.
 */
class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $slug = 'shop/products';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Désignation';

    protected static ?string $navigationGroup = 'Livraison';

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?int $navigationSort = 0;

    /**
     * Définit le formulaire de création/édition des produits.
     *
     * @param  Form  $form  Le formulaire à configurer
     * @return Form Le formulaire configuré
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nom')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('slug')
                ->label('Slug')
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            Forms\Components\Textarea::make('description')
                ->label('Description')
                ->maxLength(65535)
                ->columnSpanFull(),
            Forms\Components\Toggle::make('is_visible')
                ->label('Visible')
                ->default(false),
            Forms\Components\DatePicker::make('published_at')
                ->label('Date de publication')
                ->displayFormat('d/m/Y')
                ->format('Y-m-d'),
        ]);
    }

    /**
     * Définit la table de liste des produits.
     *
     * @param  Table  $table  La table à configurer
     * @return Table La table configurée
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Visible')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Date de publication')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date de création')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Voir'),
                Tables\Actions\EditAction::make()
                    ->label('Modifier'),
                Tables\Actions\DeleteAction::make()
                    ->label('Supprimer'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Supprimer sélectionnés'),
                    Tables\Actions\ExportBulkAction::make()
                        ->label('Exporter sélectionnés'),
                ]),
            ])
            ->recordUrl(null); // Désactive le clic pour rediriger
    }

    /**
     * Définit l'infolist pour la visualisation des produits.
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informations du produit')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nom'),
                        Infolists\Components\TextEntry::make('slug')
                            ->label('Slug'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        Infolists\Components\IconEntry::make('is_visible')
                            ->label('Visible')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('published_at')
                            ->label('Date de publication')
                            ->date('d/m/Y'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Date de création')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Dernière modification')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * Définit les pages disponibles pour cette ressource.
     *
     * @return array Les routes des pages
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
