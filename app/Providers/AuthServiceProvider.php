<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate; // Đã bỏ comment và kích hoạt

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // ĐỊNH NGHĨA GATE CHO ADMIN
        // Đây là Gate được sử dụng trong middleware 'can:admin' để kiểm tra quyền truy cập.
        Gate::define('admin', function ($user) {
            // Kiểm tra cột vai_tro trong Model NguoiDung phải là giá trị 'admin'
            return $user->vai_tro === 'admin';
        });
    }
}
