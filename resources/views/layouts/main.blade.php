<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplikasi')</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <link rel="stylesheet" href="{{ asset('helpers/alert.css') }}">
    <script src="{{ asset('js/fetchAuth.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <!-- <script src="{{ asset('js/fetchAuth.js') }}"></script> -->
    <script src="{{ asset('helpers/alert.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-[#FAFAFA] overflow-x-hidden">
    <header class="fixed top-0 inset-x-0 bg-[#FAFAFA] z-50 h-12 flex items-center px-4">
        @include('layouts.navbar')
    </header>

    @include('layouts.sidebar')

    <div id="overlay"
        class="fixed inset-0 z-40 bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300">
    </div>

    <main class="md:ml-64 pt-16 px-4">
        @yield('content')
    </main>
</body>

</html>

<script>
    window.App = {
        user: JSON.parse(localStorage.getItem("user") || "null"),
        token: localStorage.getItem("token"),
        role: localStorage.getItem("role"),
    };

    async function initApp() {
        if (!window.App.token) return;

        const res = await fetchWithAuth(`/api/auth/profile`);
        if (!res || !res.status) return;

        const user = res.data;

        window.App.user = user;
        window.App.role = user.role;

        localStorage.setItem("user", JSON.stringify(user));
        localStorage.setItem("role", user.role);
    }

    document.addEventListener('DOMContentLoaded', () => {

        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        if (!menuToggle || !sidebar || !overlay) return;

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');

            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');

            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0', 'pointer-events-none');
        }

        menuToggle.addEventListener('click', () => {
            const isOpen = !sidebar.classList.contains('-translate-x-full');

            if (isOpen) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        overlay.addEventListener('click', closeSidebar);
    });

    document.addEventListener("DOMContentLoaded", async () => {
        await initApp();
    });
</script>