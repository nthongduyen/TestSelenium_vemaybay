<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Họ tên')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Mật khẩu')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Xác nhận lại mật khẩu')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- ===== BẮT ĐẦU CODE THÊM MỚI ===== --}}

        <div class="mt-4">
            <x-input-label for="so_dien_thoai" :value="__('Số điện thoại')" />
            <x-text-input id="so_dien_thoai" class="block mt-1 w-full" type="tel" name="so_dien_thoai" :value="old('so_dien_thoai')" />
            <x-input-error :messages="$errors->get('so_dien_thoai')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="dia_chi" :value="__('Địa chỉ')" />
            <x-text-input id="dia_chi" class="block mt-1 w-full" type="text" name="dia_chi" :value="old('dia_chi')" />
            <x-input-error :messages="$errors->get('dia_chi')" class="mt-2" />
        </div>

        {{-- ===== KẾT THÚC CODE THÊM MỚI ===== --}}

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Bạn đã có tài khoản? Đăng nhập ngay!') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('Đăng ký') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
