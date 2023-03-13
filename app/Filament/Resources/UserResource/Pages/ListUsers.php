<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Closure;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function isTableRecordSelectable(): ?Closure
    {
        return fn (Model $record): bool => $record->id !== Auth::id();
    }
    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('disable')
                ->action(fn (Collection $records) => $records->each(function($record, $key) {
                    if($record->id !== Auth::id()) {
                        $record->update(['status' => 0]);
                    }
                }))
                ->deselectRecordsAfterCompletion()
                ->color('danger')
        ];
    }
}
