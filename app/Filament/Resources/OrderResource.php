<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->forVendor();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(12)->schema([
                    Forms\Components\TextInput::make('total_price')
                        ->label('Total Price')
                        ->disabled()
                        ->columnSpan(4),

                    Forms\Components\Placeholder::make('status')
                        ->label('Current Status')
                        ->content(fn($record) => ucfirst($record->status->value ?? '—'))
                        ->columnSpan(4),
                ]),

                Forms\Components\HasManyRepeater::make('orderItems')
                    ->relationship('orderItems')
                    ->schema([
                        Forms\Components\Placeholder::make('product_name')
                            ->label('Product')
                            ->content(fn($record) => $record->product?->title ?? '—')
                            ->columnSpan(5),

                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->disabled()
                            ->columnSpan(5),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->disabled()
                            ->columnSpan(5),

                        Forms\Components\ViewField::make('variation_type_option_ids')
                            ->view('filament.order.variation-options')
                            ->columnSpan(5),
                    ])
                    ->disableItemCreation()
                    ->disableItemDeletion()
                    ->columns(20),
            ]);
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->searchable(),
                TextColumn::make('total_price')
                    ->numeric()
                    ->formatStateUsing(fn($state) => number_format($state, 2))
                    ->sortable(),
                TextColumn::make('online_payment_commission')
                    ->numeric()
                    ->formatStateUsing(fn($state) => number_format($state, 2)),
                TextColumn::make('website_commission')
                    ->numeric()
                    ->formatStateUsing(fn($state) => number_format($state, 2)),
                TextColumn::make('vendor_subtotal')
                    ->numeric()
                    ->formatStateUsing(fn($state) => number_format($state, 2)),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('cancel')
                ->label('Cancel')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn($record) => $record->status->value !== 'cancelled')
                ->action(function ($record) {
                    try {
                        $record->cancel();
                        $record->save();
                        \Filament\Notifications\Notification::make()
                            ->title('Order cancelled successfully')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Error')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            ]);
   
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
