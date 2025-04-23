<?php

namespace Filament\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Throwable;

class DeleteBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'delete';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::delete.multiple.label'));

        $this->modalHeading(fn (): string => __('filament-actions::delete.multiple.modal.heading', ['label' => $this->getTitleCasePluralModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-actions::delete.multiple.modal.actions.delete.label'));

        $this->successNotificationTitle(__('filament-actions::delete.multiple.notifications.deleted.title'));

        $this->failureNotificationTitle(function (int $successCount, int $totalCount): string {
            if ($successCount) {
                return trans_choice('filament-actions::delete.multiple.notifications.deleted_partial.title', $successCount, [
                    'count' => Number::format($successCount),
                    'total' => Number::format($totalCount),
                ]);
            }

            return trans_choice('filament-actions::delete.multiple.notifications.deleted_none.title', $totalCount, [
                'count' => Number::format($totalCount),
                'total' => Number::format($totalCount),
            ]);
        });

        $this->missingBulkAuthorizationFailureNotificationMessage(function (int $failureCount, int $totalCount): string {
            return trans_choice(
                ($failureCount === $totalCount)
                    ? 'filament-actions::delete.multiple.notifications.deleted_none.missing_authorization_failure_message'
                    : 'filament-actions::delete.multiple.notifications.deleted_partial.missing_authorization_failure_message',
                $failureCount,
                ['count' => Number::format($failureCount)],
            );
        });

        $this->missingBulkProcessingFailureNotificationMessage(function (int $failureCount, int $totalCount): string {
            return trans_choice(
                ($failureCount === $totalCount)
                    ? 'filament-actions::delete.multiple.notifications.deleted_none.missing_processing_failure_message'
                    : 'filament-actions::delete.multiple.notifications.deleted_partial.missing_processing_failure_message',
                $failureCount,
                ['count' => Number::format($failureCount)],
            );
        });

        $this->color('danger');

        $this->icon(FilamentIcon::resolve('actions::delete-action') ?? Heroicon::Trash);

        $this->requiresConfirmation();

        $this->modalIcon(FilamentIcon::resolve('actions::delete-action.modal') ?? Heroicon::OutlinedTrash);

        $this->action(function (): void {
            $this->process(static function (DeleteBulkAction $action, Collection $records): void {
                $isFirstException = true;

                $records->each(static function (Model $record) use ($action, &$isFirstException): void {
                    try {
                        $record->delete() || $action->reportBulkProcessingFailure();
                    } catch (Throwable $exception) {
                        $action->reportBulkProcessingFailure();

                        if ($isFirstException) {
                            // Only report the first exception so as to not flood error logs. Even
                            // if Filament did not catch exceptions like this, only the first
                            // would be reported as the rest of the process would be halted.
                            report($exception);

                            $isFirstException = false;
                        }
                    }
                });
            });
        });

        $this->deselectRecordsAfterCompletion();

        $this->hidden(function (HasTable $livewire): bool {
            $trashedFilterState = $livewire->getTableFilterState(TrashedFilter::class) ?? [];

            if (! array_key_exists('value', $trashedFilterState)) {
                return false;
            }

            if ($trashedFilterState['value']) {
                return false;
            }

            return filled($trashedFilterState['value']);
        });
    }
}
