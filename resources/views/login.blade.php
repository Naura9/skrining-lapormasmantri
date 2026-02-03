<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LaporMasMantri - Login')</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <link rel="stylesheet" href="{{ asset('helpers/alert.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="{{ asset('helpers/alert.js') }}"></script>
</head>

<body class="bg-white">
    <div class="min-h-screen flex flex-col md:flex-row items-center justify-center">
        <div
            class="relative w-full md:w-110 h-60 md:h-[90vh] md:rounded-2xl overflow-hidden md:ml-20 mb-6 md:mb-0 flex-shrink-0">
            <img src="{{ asset('assets/foto-puskesmas.jpeg') }}"
                alt="Foto Puskesmas"
                class="object-cover w-full h-full absolute inset-0 opacity-100 transition-opacity duration-700">
        </div>

        <div class="w-full md:w-1/2 flex flex-col justify-center items-center bg-white rounded-r-2xl px-6 md:px-5">
            <div class="w-full max-w-sm">
                <h1 class="text-4xl font-bold text-[#61359C] text-center mb-10">
                    Masuk
                </h1>
                <form id="loginForm" class="flex flex-col gap-4">
                    <div class="w-full">
                        <input type="text" name="username" placeholder="Username"
                            class="w-full px-4 py-2 border border-[#C5CACF] rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-200">
                        <p id="usernameError" class="text-[#E71D1D] text-sm mt-1 hidden"></p>
                    </div>
                    <div class="w-full">
                        <div class="relative">
                            <input id="password" type="password" name="password" placeholder="Password"
                                class="w-full px-4 py-2 border border-[#C5CACF] rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-200">

                            <button type="button"
                                onclick="togglePassword()"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                                <i id="eye-icon1" class="fa-solid fa-eye-slash"></i>
                            </button>
                        </div>

                        <p id="passwordError" class="text-[#E71D1D] text-sm mt-1 hidden"></p>
                    </div>
                    <a href="" class="text-sm text-[#0A90FE] text-left mb-2">Lupa Password?</a>
                    <button type="button"
                        onclick="window.location.href='/dashboard'"
                        class="w-full py-2 font-semibold text-white rounded-lg bg-[#61359C] cursor-pointer">
                        Masuk
                    </button>

                </form>
            </div>
        </div>
    </div>
</body>

</html>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById("password");
        const eyeIcon = document.getElementById("eye-icon1");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
        } else {
            passwordInput.type = "password";
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
        }
    }
</script>