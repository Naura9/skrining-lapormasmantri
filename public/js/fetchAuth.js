async function fetchWithAuth(url, options = {}) {
    const token = localStorage.getItem('token');
    if (!token) {
        showSessionExpiredToast();
        localStorage.clear();
        window.location.href = "/";
        return;
    }

    options.headers = {
        ...options.headers,
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json',
    };

    try {
        const res = await fetch(url, options);
        let data = {};

        try {
            data = await res.json();
        } catch {
            data = {};
        }

        if (res.status === 422) {
            return { status_code: 422, errors: data.errors || {} };
        }

        if (res.status === 403 && data.errors?.some(err => err.toLowerCase().includes("kadaluarsa"))) {
            showSessionExpiredToast();
            localStorage.clear();
            window.location.href = "/";
            return;
        }

        if (!res.ok) {
            showErrorToast("Terjadi Kesalahan", data.message || "Gagal memproses data");
            return { status_code: res.status, message: data.message || "Terjadi kesalahan" };
        }

        return data;
    } catch (error) {
        showErrorToast("Terjadi Kesalahan", error.message);
        return { status_code: 500, message: error.message };
    }
}
