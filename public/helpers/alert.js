function showSuccessToast(title, text = "") {
  hideLoadingToast();

  const overlay = document.createElement("div");
  overlay.className = "toastify-overlay";
  document.body.appendChild(overlay);

  const toast = Toastify({
    text: `
      <div class="flex flex-col items-center justify-center px-6 py-8 h-[200px] sm:h-[300px]">
        <i class="fa-solid fa-circle-check text-[#56FF18] text-6xl sm:text-7xl mb-6"></i>
        <div class="font-bold text-[#56FF18] text-lg sm:text-xl text-center">${title}</div>
        <div class="text-sm sm:text-base text-gray-600 text-center mt-2">${text}</div>
      </div>
    `,
    duration: 1000,
    close: false,
    gravity: "top",
    position: "center",
    escapeMarkup: false,
    className: "toast-custom bg-white text-center rounded-2xl shadow-xl min-w-[300px] max-w-[250px] min-h-[160px] sm:min-h-[260px]",
    callback: function () {
      overlay.remove();
    },
  });

  toast.showToast();
}

function showErrorToast(title, text = "") {
  const overlay = document.createElement("div");
  overlay.className = "toastify-overlay";
  document.body.appendChild(overlay);

  const toast = Toastify({
    text: `
      <div class="flex flex-col items-center justify-center px-6 py-8 h-[200px] sm:h-[300px]">
        <i class="fa-solid fa-circle-xmark text-[#E71D1D] text-6xl sm:text-7xl mb-6"></i>
        <div class="font-bold text-[#E71D1D] text-lg sm:text-xl text-center">${title}</div>
        <div class="text-sm sm:text-base text-gray-600 text-center mt-2">${text}</div>
      </div>
    `,
    duration: 2000,
    close: false,
    gravity: "top",
    position: "center",
    escapeMarkup: false,
    className: "toast-custom bg-white text-center rounded-2xl shadow-xl min-w-[300px] max-w-[250px] min-h-[160px] sm:min-h-[260px]",
    callback: function () {
      overlay.remove();
    },
  });

  toast.showToast();
}

let loadingToast = null;
function showLoadingToast(text = "Loading...") {
  if (loadingToast) return;

  const overlay = document.createElement("div");
  overlay.className = "toastify-overlay";
  document.body.appendChild(overlay);

  loadingToast = Toastify({
    text: `
      <div class="flex flex-col items-center justify-center px-6 py-8 h-[200px] sm:h-[300px]">
        <i class="fa-solid fa-spinner fa-spin text-5xl sm:text-6xl mb-4"></i>
        <div class="font-medium text-gray-700 text-lg sm:text-xl text-center">${text}</div>
      </div>
    `,
    duration: -1,
    close: false,
    gravity: "top",
    position: "center",
    escapeMarkup: false,
    className: "toast-custom bg-white text-center rounded-2xl shadow-xl border border-gray-200 min-w-[150px] max-w-[250px] min-h-[200px] sm:min-h-[300px]",
    callback: function () {
      overlay.remove();
      loadingToast = null;
    },
  });

  loadingToast.showToast();
}

function hideLoadingToast() {
  if (loadingToast) {
    document.querySelector(".toastify-overlay")?.remove();
    loadingToast.hideToast();
    loadingToast = null;
  }
}

function showDeleteConfirmToast(title, onConfirm) {
  const overlay = document.createElement("div");
  overlay.className = "toastify-overlay";
  document.body.appendChild(overlay);

  const toast = Toastify({
    text: `
      <div class="flex flex-col items-center justify-center px-6 py-8 max-w-[280px] mx-auto">
        <i class="fa-solid fa-trash text-6xl sm:text-7xl mb-5"></i>
        <div class="font-semibold text-base sm:text-lg text-center mb-4">${title}</div>

        <div class="flex flex-row gap-1 w-full justify-center">
          <button id="btnConfirmDelete" 
                  class="w-full sm:w-[100px] py-1 bg-[#0B6CF4] text-white rounded-lg font-semibold hover:bg-blue-700 transition mx-auto">
            Ya
          </button>
          <button id="btnCancelDelete" 
                  class="w-full sm:w-[100px] py-1 bg-[#E71D1D] text-white rounded-lg font-semibold hover:bg-red-700 transition mx-auto">
            Tidak
          </button>
        </div>
      </div>
    `,
    duration: -1,
    close: false,
    gravity: "top",
    position: "center",
    escapeMarkup: false,
    className: "toast-custom bg-white text-center rounded-2xl shadow-xl min-w-[300px] max-w-[90%] sm:max-w-[250px] min-h-[160px] sm:min-h-[260px] border border-gray-200",
    callback: function () {
      overlay.remove();
    },
  });

  toast.showToast();

  const closeToast = (confirm = false) => {
    if (confirm && typeof onConfirm === "function") onConfirm();
    toast.hideToast();
  };

  document.getElementById("btnCancelDelete").addEventListener("click", () => closeToast(false));
  document.getElementById("btnConfirmDelete").addEventListener("click", () => closeToast(true));
}

function showSessionExpiredToast() {
  const overlay = document.createElement("div");
  overlay.className = "toastify-overlay";
  document.body.appendChild(overlay);

  const toast = Toastify({
    text: `
      <div class="flex flex-col items-center justify-center px-6 py-8 h-[200px] sm:h-[300px]">
        <i class="fa-solid fa-circle-xmark text-[#E71D1D] text-6xl sm:text-7xl mb-6"></i>
        <div class="font-bold text-[#E71D1D] text-lg sm:text-xl text-center">Sesi Habis</div>
        <div class="text-sm sm:text-base text-gray-600 text-center mt-2">Silahkan login ulang</div>
        <button id="btnSessionExpired" 
                class="mt-4 px-6 py-2 bg-[#0B6CF4] text-white rounded-lg font-semibold hover:bg-blue-700 transition">
          OK
        </button>
      </div>
    `,
    duration: -1,
    close: false,
    gravity: "top",
    position: "center",
    escapeMarkup: false,
    className: "toast-custom bg-white text-center rounded-2xl shadow-xl min-w-[300px] max-w-[250px] min-h-[200px] sm:min-h-[300px] border border-gray-200",
    callback: function () {
      overlay.remove();
    },
  });

  toast.showToast();

  document.getElementById("btnSessionExpired").addEventListener("click", () => {
    localStorage.clear();
    window.location.href = "/login";
    toast.hideToast();
  });
}
