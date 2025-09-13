<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('proceed')
                ->label('Proceed Order')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    try {
                        $this->record->proceed();
                        $this->record->refresh();

                        \Filament\Notifications\Notification::make()
                            ->title('Order proceeded successfully')
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

        ];
    }

}
