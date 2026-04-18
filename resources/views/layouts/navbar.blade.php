<div class="fixed top-0 inset-x-0 h-12 bg-[#61359C]/30 border-b border-[#00000033]">
    <div class="flex items-center justify-between h-full px-4">
        <button id="menu-toggle" class="md:hidden">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>

        <div class="flex items-center gap-4 ml-auto">
            <p class="text-sm font-bold">
                <span id="userName" class="text-gray-800"></span>
                <span> - </span>
                <span id="userRole" class="text-gray-700 font-medium"></span>
            </p>

            <button id="userDropdownBtn"
                class="flex items-center gap-2 focus:outline-none">
                <i class="fa-solid fa-user-circle text-2xl text-gray-800"></i>
            </button>

            <div id="userDropdown"
                class="hidden absolute right-4 top-10 w-44 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden z-50">
                <a href="/profile"
                    class="block px-4 py-2 text-sm hover:bg-gray-100">
                    <i class="fa-solid fa-user mr-2"></i> Profil
                </a>
                <button id="logoutBtn"
                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100">
                    <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout
                </button>

            </div>
        </div>

    </div>
</div>

<script>
    function formatName(fullName) {
        if (!fullName) return "";

        const parts = fullName.trim().toLowerCase().split(" ");

        if (parts.length === 1) {
            return capitalize(parts[0]);
        }

        const firstName = capitalize(parts[0]);
        const initials = parts.slice(1)
            .map(name => name.charAt(0).toUpperCase() + ".")
            .join(" ");

        return `${firstName} ${initials}`;
    }

    function capitalize(word) {
        return word.charAt(0).toUpperCase() + word.slice(1);
    }

    window.addEventListener("load", function() {
        const userRaw = localStorage.getItem("user");
        if (!userRaw) return;

        const user = JSON.parse(userRaw);

        const name = formatName(user.nama);
        const role = user.role.charAt(0).toUpperCase() + user.role.slice(1).toLowerCase();

        const nameEl = document.getElementById("userName");
        const roleEl = document.getElementById("userRole");

        if (nameEl) nameEl.textContent = name;
        if (roleEl) roleEl.textContent = role;
    });

    document.addEventListener("DOMContentLoaded", () => {
        const btn = document.getElementById("userDropdownBtn");
        const dropdown = document.getElementById("userDropdown");

        if (!btn || !dropdown) return;

        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            dropdown.classList.toggle("hidden");
        });

        document.addEventListener("click", () => {
            dropdown.classList.add("hidden");
        });

        dropdown.addEventListener("click", (e) => {
            e.stopPropagation();
        });

        const logoutBtn = document.getElementById("logoutBtn");
        if (logoutBtn) {
            logoutBtn.addEventListener("click", () => {
                localStorage.removeItem("user");
                window.location.href = "/";
            });
        }
    });

    const logoutBtn = document.getElementById("logoutBtn");

    if (logoutBtn) {
        logoutBtn.addEventListener("click", async () => {
            const token = localStorage.getItem("token");

            try {
                const res = await fetch("{{ url('api/auth/logout') }}", {
                    method: "POST",
                    headers: {
                        "Accept": "application/json",
                        "Authorization": `Bearer ${token}`,
                    },
                });

                const data = await res.json();

                if (!res.ok || !data.status) {
                    showErrorToast("Logout Gagal", data.error || "Gagal logout");
                    return;
                }

                localStorage.removeItem("user");
                localStorage.removeItem("token");
                localStorage.removeItem("role");

                showSuccessToast("Logout Berhasil");

                setTimeout(() => {
                    window.location.href = "/";
                }, 800);

            } catch (err) {
                console.error(err);
                showErrorToast("Error", "Tidak dapat logout ke server");
            }
        });
    }
</script>