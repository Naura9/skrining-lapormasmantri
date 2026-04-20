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
                <i id="dropdownIcon" class="fa-solid fa-chevron-down text-xs text-gray-600 transition-transform"></i>
            </button>

            <div id="userDropdown"
                class="hidden absolute right-4 top-10 w-44 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden z-50">
                <a href="{{ route('kader.fitur.profil') }}"
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

    document.addEventListener("DOMContentLoaded", () => {
        const user = window.App?.user;
        if (!user) return;

        const nameEl = document.getElementById("userName");
        const roleEl = document.getElementById("userRole");

        const name = formatName(user.nama);
        const role = user.role.charAt(0).toUpperCase() + user.role.slice(1);

        if (nameEl) nameEl.textContent = name;
        if (roleEl) roleEl.textContent = role;

        const btn = document.getElementById("userDropdownBtn");
        const dropdown = document.getElementById("userDropdown");
        const icon = document.getElementById("dropdownIcon");

        if (btn && dropdown && icon) {
            btn.addEventListener("click", (e) => {
                e.stopPropagation();

                const isHidden = dropdown.classList.toggle("hidden");

                // toggle icon
                if (isHidden) {
                    icon.classList.remove("fa-chevron-up");
                    icon.classList.add("fa-chevron-down");
                } else {
                    icon.classList.remove("fa-chevron-down");
                    icon.classList.add("fa-chevron-up");
                }
            });

            document.addEventListener("click", () => {
                dropdown.classList.add("hidden");
                icon.classList.remove("fa-chevron-up");
                icon.classList.add("fa-chevron-down");
            });

            dropdown.addEventListener("click", (e) => {
                e.stopPropagation();
            });
        }

        const logoutBtn = document.getElementById("logoutBtn");

        if (logoutBtn) {
            logoutBtn.addEventListener("click", async () => {
                const token = window.App?.token;

                try {
                    await fetch("{{ url('api/auth/logout') }}", {
                        method: "POST",
                        headers: {
                            "Accept": "application/json",
                            "Authorization": `Bearer ${token}`,
                        },
                    });

                    localStorage.clear();
                    window.location.href = "/";
                } catch (err) {
                    console.error(err);
                }
            });
        }
    });
</script>