<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChuyenBayResource\Pages;
use App\Filament\Resources\ChuyenBayResource\RelationManagers;
use App\Models\ChuyenBay;

// SỬA LẠI CÁC 'USE' STATEMENT CHO V2
use Filament\Forms;
use Filament\Resources\Form as ResourceForm; // Dùng 'ResourceForm'
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Resources\Table as ResourceTable; // Dùng 'ResourceTable'

class ChuyenBayResource extends Resource
{
    protected static ?string $model = ChuyenBay::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $navigationLabel = 'Quản lý Chuyến bay';
    protected static ?string $pluralLabel = 'Chuyến bay';

    // SỬA LẠI ĐỊNH NGHĨA HÀM (dùng ResourceForm)
    public static function form(ResourceForm $form): ResourceForm
    {
        return $form
            ->schema([
                // Cột trái
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Thông tin chuyến bay')
                            ->schema([
                                Forms\Components\TextInput::make('ma_chuyen_bay')
                                    ->label('Mã chuyến bay')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->rules(['regex:/^[A-Z0-9]+$/'])
                                    ->maxLength(50),

                                Forms\Components\Select::make('id_may_bay')
                                    ->label('Máy bay')
                                    ->relationship('mayBay', 'ten_may_bay')
                                    ->searchable()
                                    ->required(),

                                Forms\Components\Select::make('trang_thai')
                                    ->label('Trạng thái')
                                    ->options([
                                        'dang_ban' => 'Đang bán',
                                        'tam_hoan' => 'Tạm hoãn',
                                        'hoan_tat' => 'Hoàn tất',
                                        'huy' => 'Hủy',
                                    ])
                                    ->default('dang_ban')
                                    ->required(),
                            ]),

                        /*Forms\Components\Section::make('Thông tin giá')
                            ->schema([
                                Forms\Components\TextInput::make('gia_ve')
                                    ->label('Giá vé (VND)')
                                    ->required()
                                    ->numeric()
                                    ->rules(['min:1'])
                                    ->default(0.00)
                                    // THÊM DÒNG NÀY: Tắt bong bóng thông báo của trình duyệt
                                    ->extraInputAttributes(['novalidate' => true]),
                            ]),
                    ])->columnSpan(['lg' => 2]),*/
                        Forms\Components\Section::make('Thông tin giá')
                                ->schema([
                                    Forms\Components\TextInput::make('gia_ve')
                                        ->label('Giá vé (VND)')
                                        ->required()
                                        // Sửa: Dùng rules để bắt lỗi numeric từ Server thay vì type="number" của trình duyệt
                                        ->rules(['numeric', 'min:1'])
                                        ->default(0.00)
                                        ->extraInputAttributes([
                                            'type' => 'text', // Ép kiểu text để Dusk nhập được chữ 'adffggg' (TC_TCB_15)
                                            'novalidate' => true, // Tắt validate mặc định của HTML5
                                        ])
                                        ->validationAttribute('giá vé'),
                                ]),
                        ])
                        ->columnSpan(['lg' => 2]),

                // Cột phải
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Hành trình')
                            ->schema([
                                Forms\Components\Select::make('id_san_bay_di')
                                    ->label('Sân bay đi')
                                    ->relationship('sanBayDi', 'ten_san_bay')
                                    ->searchable()
                                    ->required(),

                                Forms\Components\Select::make('id_san_bay_den')
                                    ->label('Sân bay đến')
                                    ->relationship('sanBayDen', 'ten_san_bay')
                                    ->searchable()
                                    ->required()
                                    ->different('id_san_bay_di')
                                    ->validationAttribute('sân bay đến'),
                            ]),

                        Forms\Components\Section::make('Thời gian')
                            ->schema([
                                Forms\Components\DateTimePicker::make('thoi_gian_di')
                                    ->label('Thời gian đi')
                                    ->required()
                                    ->rules(['after_or_equal:now'])
                                    ->validationAttribute('thời gian đi'),

                                Forms\Components\DateTimePicker::make('thoi_gian_den')
                                    ->label('Thời gian đến')
                                    ->required()
                                    ->after('thoi_gian_di')
                                    ->validationAttribute('thời gian đến'),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    // SỬA LẠI ĐỊNH NGHĨA HÀM (dùng ResourceTable)
    public static function table(ResourceTable $table): ResourceTable
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ma_chuyen_bay')
                    ->label('Mã CB')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sanBayDi.ma_san_bay')
                    ->label('Đi từ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sanBayDen.ma_san_bay')
                    ->label('Đến')
                    ->sortable(),

                Tables\Columns\TextColumn::make('thoi_gian_di')
                    ->label('Thời gian đi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('gia_ve')
                    ->label('Giá vé')
                    ->formatStateUsing(fn ($state): string => number_format($state, 0, ',', '.') . ' VND')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('trang_thai') // <-- SỬA THÀNH BadgeColumn
                    ->label('Trạng thái')
                    ->color(fn (string $state): string => match ($state) { // Bỏ dòng .badge()
                        'dang_ban' => 'success',
                        'tam_hoan' => 'warning',
                        'hoan_tat' => 'primary',
                        'huy' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    // Thêm hàm này vào để Filament v2 nhận diện tùy chọn hiển thị ==== 19/5/2026
    // Đảm bảo hàm này nằm độc lập cuối file ChuyenBayResource.php (bên trên hàm getRelations)
    protected static function getRecordsPerPageSelectOptions(): array
    {
        // Số 5 đứng đầu đồng nghĩa với việc mặc định khi vào trang hệ thống sẽ chỉ hiện 5 bản ghi
        // Giá trị -1 đại diện cho option "Tất cả" mà bạn mong muốn
        return [5, 10, 25, 50, -1];
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
            'index' => Pages\ListChuyenBays::route('/'),
            'create' => Pages\CreateChuyenBay::route('/create'),
            'edit' => Pages\EditChuyenBay::route('/{record}/edit'),
        ];
    }
}
