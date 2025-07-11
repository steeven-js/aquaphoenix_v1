<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

/**
 * Ressource Filament pour gérer les clients.
 */
class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $slug = 'shop/customers';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Clients';

    protected static ?string $navigationGroup = 'Livraison';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 1;

    /**
     * Définit le formulaire de création/édition des clients.
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
            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            Forms\Components\TextInput::make('address')
                ->label('Adresse')
                ->maxLength(255),
            Forms\Components\FileUpload::make('photo')
                ->label('Photo'),
            Forms\Components\TextInput::make('phone1')
                ->label('Téléphone 1')
                ->tel()
                ->maxLength(255),
            Forms\Components\TextInput::make('phone2')
                ->label('Téléphone 2')
                ->tel()
                ->maxLength(255),
            Forms\Components\TextInput::make('code')
                ->label('Code')
                ->maxLength(255),
            Forms\Components\TextInput::make('commune')
                ->label('Commune')
                ->maxLength(255),
        ]);
    }

    /**
     * Définit la table de liste des clients.
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
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone1')
                    ->label('Téléphone 1')
                    ->searchable(),
                Tables\Columns\TextColumn::make('commune')
                    ->label('Commune')
                    ->searchable(),
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
     * Définit l'infolist pour la visualisation des clients.
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informations du client')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nom'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email'),
                        Infolists\Components\TextEntry::make('phone1')
                            ->label('Téléphone 1'),
                        Infolists\Components\TextEntry::make('phone2')
                            ->label('Téléphone 2'),
                        Infolists\Components\TextEntry::make('address')
                            ->label('Adresse')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('commune')
                            ->label('Commune'),
                        Infolists\Components\TextEntry::make('code')
                            ->label('Code'),
                        Infolists\Components\ImageEntry::make('photo')
                            ->label('Photo'),
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
     * @return array Les pages configurées
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
