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
                <h1 class="text-3xl md:text-4xl font-bold text-[#61359C] text-center mb-2">
                    LAPOR MAS MANTRI
                </h1>
                <p class="text-sm text-gray-400 text-center mb-8 leading-snug">
                    <span class="font-bold text-gray-400">LA</span>ksanakan
                    <span class="font-bold text-gray-400">P</span>emeriksaan,
                    Kolab<span class="font-bold text-gray-400">OR</span>asi, dan Pe<span class="font-bold text-gray-400">MA</span>ntauan
                    Ke<span class="font-bold text-gray-400">S</span>ehatan
                    <span class="font-bold text-gray-400">MA</span>syarakat
                    Sana<span class="font-bold text-gray-400">N</span>wetan
                    <span class="font-bold text-gray-400">T</span>e<span class="font-bold text-gray-400">RI</span>ntegrasi
                </p>

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
                                onclick="togglePassword('password', 'eye-icon1')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                                <i id="eye-icon1" class="fa-solid fa-eye"></i>
                            </button>
                        </div>

                        <p id="passwordError" class="text-[#E71D1D] text-sm mt-1 hidden"></p>
                    </div>
                    <a href="" class="text-sm text-[#0A90FE] text-left mb-2">Lupa Password?</a>
                    <button type="submit"
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
    document.getElementById("loginForm").addEventListener("submit", async function(e) {
        e.preventDefault();

        const usernameInput = document.querySelector('[name="username"]');
        const passwordInput = document.querySelector('[name="password"]');
        const usernameError = document.getElementById("usernameError");
        const passwordError = document.getElementById("passwordError");

        usernameError.classList.add("hidden");
        passwordError.classList.add("hidden");

        const username = usernameInput.value.trim();
        const password = passwordInput.value.trim();

        if (!username || !password) {
            if (!username) {
                usernameError.textContent = "Username wajib diisi.";
                usernameError.classList.remove("hidden");
            }
            if (!password) {
                passwordError.textContent = "Password wajib diisi.";
                passwordError.classList.remove("hidden");
            }
            return;
        }

        try {
            const res = await fetch(`{{ url('api/auth/login') }}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                },
                body: JSON.stringify({
                    username: username,
                    password: password,
                }),
            });

            const data = await res.json();

            if (!res.ok || data.status !== "success") {
                showErrorToast("Login Gagal", data.message || "Username atau password salah.");
                return;
            }

            const token = data.data.access_token;
            const user = data.data.user;

            showSuccessToast("Login Berhasil");

            localStorage.setItem("token", token);
            localStorage.setItem("role", user.role);
            localStorage.setItem("user", JSON.stringify(user));

            let redirectUrl = "";

            switch (user.role) {
                case "kader":
                    redirectUrl = "{{ route('kader.dashboard_kader') }}";
                    break;
                case "admin":
                    redirectUrl = "{{ route('admin.dashboard_admin') }}";
                    break;
                case "nakes":
                    redirectUrl = "{{ route('nakes.dashboard_nakes') }}";
                    break;
                default:
                    redirectUrl = "/";
            }

            setTimeout(() => {
                window.location.href = redirectUrl;
            }, 1200);

        } catch (error) {
            console.error("Login error:", error);
            showErrorToast("Kesalahan Server", "Tidak dapat terhubung ke server.");
        }
    });

    function togglePassword(inputId, eyeIconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(eyeIconId);

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>