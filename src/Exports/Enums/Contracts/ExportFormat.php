<?php

namespace Filament\Actions\Exports\Enums\Contracts;

use Filament\Actions\Exports\Downloaders\Contracts\Downloader;
use Filament\Actions\Exports\Models\Export;
use Filament\Notifications\Actions\Action as NotificationAction;

interface ExportFormat
{
    public function getDownloader(): Downloader;

    public function getDownloadNotificationAction(Export $export): NotificationAction;
}
