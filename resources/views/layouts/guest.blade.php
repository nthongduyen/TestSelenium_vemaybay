<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">

        {{-- ===== BẮT ĐẦU KHỐI ĐÃ SỬA ===== --}}

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-cover bg-center"
             style="background-image: url('/images/background-san-ve-may-bay.jpg');">

            {{-- Logo (Bạn có thể đổi logo Laravel thành logo của bạn) --}}
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            {{--
              Sửa thẻ Card:
              - Bỏ 'bg-white' (nền trắng đục)
              - Thêm 'bg-white bg-opacity-90' (nền trắng mờ)
              - Thêm 'shadow-2xl' (đổ bóng rõ hơn)
              - Thêm 'py-6' (tăng padding)
            --}}
            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white bg-opacity-90 shadow-2xl overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>

        {{-- ===== KẾT THÚC KHỐI ĐÃ SỬA ===== --}}

    </body>
</html>
