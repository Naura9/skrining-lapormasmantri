<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplikasi')</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <link rel="stylesheet" href="{{ asset('helpers/alert.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <!-- <script src="{{ asset('js/fetchAuth.js') }}"></script> -->
    <script src="{{ asset('helpers/alert.js') }}"></script>\
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-[#FAFAFA] overflow-x-hidden">
    <header class="fixed top-0 inset-x-0 bg-[#FAFAFA] z-50 h-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-full">
            @include('layouts.navbar')
            @include('layouts.sidebar')
        </div>
    </header>

    <div id="overlay" class="hidden fixed inset-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-50"></div>

    <main class="md:ml-64 pt-12 px-4">
        @yield('content')
    </main>
</body>

</html>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');
        const overlay = document.getElementById('overlay');

        if (!hamburger || !mobileMenu || !overlay) return;

        hamburger.addEventListener('click', () => {
            const isHidden = mobileMenu.classList.contains('hidden');

            if (isHidden) {
                mobileMenu.classList.remove('hidden');
                mobileMenu.style.maxHeight = mobileMenu.scrollHeight + "px";
                overlay.classList.remove('hidden');
                overlay.classList.add('opacity-100');
            } else {
                mobileMenu.style.maxHeight = "0";
                overlay.classList.remove('opacity-100');
                setTimeout(() => {
                    mobileMenu.classList.add('hidden');
                    overlay.classList.add('hidden');
                }, 300);
            }
        });

        overlay.addEventListener('click', () => {
            mobileMenu.style.maxHeight = "0";
            overlay.classList.remove('opacity-100');
            setTimeout(() => {
                mobileMenu.classList.add('hidden');
                overlay.classList.add('hidden');
            }, 300);
        });
    });
</script>