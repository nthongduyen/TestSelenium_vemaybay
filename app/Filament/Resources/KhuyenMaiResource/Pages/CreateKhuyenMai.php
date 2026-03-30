<?php

namespace App\Filament\Resources\KhuyenMaiResource\Pages;

use App\Filament\Resources\KhuyenMaiResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateKhuyenMai extends CreateRecord
{
    protected static string $resource = KhuyenMaiResource::class;

    /**
     * Tích hợp thông báo thành công (Kết quả mong đợi IT09)
     */
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Thêm khuyến mãi thành công!')
            ->body('Khuyến mãi mới đã được thêm vào hệ thống.')
            ->duration(5000);
    }
}
