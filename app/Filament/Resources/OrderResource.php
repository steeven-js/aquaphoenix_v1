<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

/**
 * Ressource Filament pour gérer les commandes/bons de livraison.
 */
class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $slug = 'shop/orders';

    protected static ?string $recordTitleAttribute = 'number';

    protected static ?string $modelLabel = 'Bon de livraison';

    protected static ?string $navigationGroup = 'Livraison';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 2;

    /**
     * Définit le formulaire de création/édition des commandes.
     *
     * @param  Form  $form  Le formulaire à configurer
     * @return Form Le formulaire configuré
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            // Section des informations client
            Forms\Components\Section::make('Informations Client')
                ->schema([
                    Forms\Components\Select::make('customer_id')
                        ->label('Client')
                        ->relationship('customer', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->label('Nom')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->unique()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('phone1')
                                ->label('Téléphone 1')
                                ->tel()
                                ->maxLength(255),
                        ])
                        ->columnSpan('full'),
                ])->columns(2),

            // Section des détails de la commande
            Forms\Components\Section::make('Détails de la commande')
                ->schema([
                    // Numéro de commande auto-généré
                    Forms\Components\TextInput::make('number')
                        ->label('Numéro de commande')
                        ->default(function () {
                            $lastOrder = Order::orderBy('id', 'desc')->first();
                            $lastNumber = $lastOrder ? (int) preg_replace('/[^0-9]/', '', $lastOrder->number) : 0;

                            return 'CMD-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                        })
                        ->disabled()
                        ->dehydrated()
                        ->required(),
                    // Statut de la commande
                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options([
                            'en progression' => 'En progression',
                            'livré' => 'Livré',
                            'annulé' => 'Annulé',
                        ])
                        ->default('en progression')
                        ->required()
                        ->native(false),
                    // Dates de publication et livraison
                    Forms\Components\DatePicker::make('published_at')
                        ->label('Date de publication')
                        ->displayFormat('d/m/Y')
                        ->format('Y-m-d')
                        ->default(now()),
                    Forms\Components\DatePicker::make('delivered_date')
                        ->label('Date de livraison')
                        ->displayFormat('d/m/Y')
                        ->format('Y-m-d'),
                ])->columns(2),

            // Section des produits commandés
            Forms\Components\Section::make('Produits commandés')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->label('Articles')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('product_id')
                                ->label('Produit')
                                ->relationship('product', 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Nom')
                                        ->required(),
                                    Forms\Components\Textarea::make('description')
                                        ->label('Description'),
                                ]),
                            Forms\Components\TextInput::make('qty')
                                ->label('Quantité')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->minValue(1),
                            Forms\Components\TextInput::make('sort')
                                ->label('Ordre')
                                ->numeric()
                                ->default(0)
                                ->hidden(),
                        ])
                        ->orderColumn('sort')
                        ->defaultItems(1)
                        ->reorderable()
                        ->columnSpanFull()
                        ->columns(2),
                ]),

            // Section des notes
            Forms\Components\Section::make('Notes')
                ->schema([
                    Forms\Components\Textarea::make('notes')
                        ->label('Notes')
                        ->maxLength(65535)
                        ->columnSpanFull(),
                ])->columns(2),

            // Champs cachés pour usage backend
            Forms\Components\Hidden::make('url')
                ->default(false),
            Forms\Components\Hidden::make('url'),
        ]);
    }

    /**
     * Définit la table de liste des commandes.
     *
     * @param  Table  $table  La table à configurer
     * @return Table La table configurée
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Colonnes de la table
                Tables\Columns\TextColumn::make('number')
                    ->label('N° Commande')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Client')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->sortable()
                    ->colors([
                        'warning' => 'en progression',
                        'success' => 'livré',
                        'danger' => 'annulé',
                    ]),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Date de publication')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivered_date')
                    ->label('Date de livraison')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            // Filtres disponibles
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'en progression' => 'En progression',
                        'livré' => 'Livré',
                        'annulé' => 'Annulé',
                    ]),
            ])
            // Actions sur les lignes
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Voir'),
                    Tables\Actions\EditAction::make()
                        ->label('Modifier'),
                    Action::make('delivery_note')
                        ->label('Bon de livraison')
                        ->icon('heroicon-o-document-text')
                        ->url(fn (Order $record) => route('order.delivery-note.download', $record))
                        ->openUrlInNewTab()
                        ->color('success'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Supprimer'),
                ]),
            ])
            // Actions groupées
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
     * Définit l'infolist pour la visualisation des commandes.
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informations de la commande')
                    ->schema([
                        Infolists\Components\TextEntry::make('number')
                            ->label('N° Commande'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Statut')
                            ->badge()
                            ->colors([
                                'warning' => 'en progression',
                                'success' => 'livré',
                                'danger' => 'annulé',
                            ]),
                        Infolists\Components\TextEntry::make('customer.name')
                            ->label('Client'),
                        Infolists\Components\TextEntry::make('customer.email')
                            ->label('Email du client'),
                        Infolists\Components\TextEntry::make('published_at')
                            ->label('Date de publication')
                            ->date('d/m/Y'),
                        Infolists\Components\TextEntry::make('delivered_date')
                            ->label('Date de livraison')
                            ->date('d/m/Y'),
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Articles commandés')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->label('Articles')
                            ->schema([
                                Infolists\Components\TextEntry::make('product.name')
                                    ->label('Produit'),
                                Infolists\Components\TextEntry::make('qty')
                                    ->label('Quantité'),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    /**
     * Définit les pages de la ressource.
     *
     * @return array Les routes des pages
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
