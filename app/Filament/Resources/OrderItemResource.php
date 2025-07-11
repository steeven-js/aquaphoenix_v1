<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderItemResource\Pages;
use App\Models\OrderItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ressource Filament pour gérer les articles de commande.
 */
class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    protected static ?string $slug = 'shop/order-items';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $modelLabel = 'Article de commande';

    protected static ?string $pluralModelLabel = 'Articles de commande';

    protected static ?string $navigationGroup = 'Livraison';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations de l\'article')
                    ->schema([
                        Forms\Components\Select::make('order_id')
                            ->label('Commande')
                            ->relationship('order', 'number')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('number')
                                    ->label('Numéro de commande')
                                    ->required(),
                            ]),
                        Forms\Components\Select::make('product_id')
                            ->label('Produit')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom du produit')
                                    ->required(),
                            ]),
                        Forms\Components\TextInput::make('qty')
                            ->label('Quantité')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(1),
                        Forms\Components\TextInput::make('sort')
                            ->label('Ordre de tri')
                            ->numeric()
                            ->default(0)
                            ->helperText('Utilisé pour définir l\'ordre d\'affichage des articles'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.number')
                    ->label('N° Commande')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order.customer.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produit')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty')
                    ->label('Quantité')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('sort')
                    ->label('Ordre')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('order.status')
                    ->label('Statut Commande')
                    ->badge()
                    ->colors([
                        'warning' => 'en progression',
                        'success' => 'livré',
                        'danger' => 'annulé',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date de création')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Date de modification')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('order.status')
                    ->label('Statut de la commande')
                    ->options([
                        'en progression' => 'En progression',
                        'livré' => 'Livré',
                        'annulé' => 'Annulé',
                    ]),
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Produit')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ExportBulkAction::make(),
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
            'index' => Pages\ListOrderItems::route('/'),
            'create' => Pages\CreateOrderItem::route('/create'),
            'edit' => Pages\EditOrderItem::route('/{record}/edit'),
        ];
    }
}
