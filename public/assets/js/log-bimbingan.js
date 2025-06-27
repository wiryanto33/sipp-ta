/**
 * JavaScript untuk halaman Log Bimbingan
 * Menangani modal tambah saran dan modal konfirmasi hapus
 */

document.addEventListener("DOMContentLoaded", function () {
    // Inisialisasi modal
    const saranModal = new bootstrap.Modal(
        document.getElementById("saranModal")
    );
    const deleteModal = new bootstrap.Modal(
        document.getElementById("deleteModal")
    );

    // Form elements
    const saranForm = document.getElementById("saranForm");
    const deleteForm = document.getElementById("deleteForm");
    const saranTextarea = document.getElementById("saran_dosen");
    const submitSaranBtn = document.getElementById("submitSaran");

    // Counter karakter untuk textarea saran
    if (saranTextarea) {
        const maxLength = 2000;
        const formText =
            saranTextarea.parentElement.querySelector(".form-text");

        saranTextarea.addEventListener("input", function () {
            const currentLength = this.value.length;
            const remaining = maxLength - currentLength;

            if (remaining < 0) {
                formText.innerHTML = `<span class="text-danger">Melebihi batas maksimal ${Math.abs(
                    remaining
                )} karakter</span>`;
                formText.classList.add("text-danger");
                submitSaranBtn.disabled = true;
            } else {
                formText.innerHTML = `Sisa ${remaining} karakter dari maksimal ${maxLength}`;
                formText.classList.remove("text-danger");
                submitSaranBtn.disabled = false;
            }
        });
    }

    // Reset form saat modal ditutup
    document
        .getElementById("saranModal")
        .addEventListener("hidden.bs.modal", function () {
            if (saranForm) {
                saranForm.reset();
                const formText =
                    saranTextarea.parentElement.querySelector(".form-text");
                formText.innerHTML = "Maksimal 2000 karakter";
                formText.classList.remove("text-danger");
                submitSaranBtn.disabled = false;
            }
        });

    // Loading state untuk form saran
    if (saranForm) {
        saranForm.addEventListener("submit", function () {
            submitSaranBtn.disabled = true;
            submitSaranBtn.innerHTML =
                '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        });
    }

    // Loading state untuk form hapus
    if (deleteForm) {
        deleteForm.addEventListener("submit", function () {
            const deleteBtn = this.querySelector('button[type="submit"]');
            deleteBtn.disabled = true;
            deleteBtn.innerHTML =
                '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
        });
    }
});

/**
 * Fungsi untuk menampilkan modal tambah saran
 * @param {number} logId - ID log bimbingan
 */
function addSaran(logId) {
    const saranForm = document.getElementById("saranForm");
    const saranModal = new bootstrap.Modal(
        document.getElementById("saranModal")
    );

    // Set action URL untuk form
    const bimbinganId = getBimbinganIdFromUrl();
    saranForm.action = `/bimbingan/${bimbinganId}/log-bimbingan/${logId}/saran`;

    // Reset form
    saranForm.reset();
    const saranTextarea = document.getElementById("saran_dosen");
    const formText = saranTextarea.parentElement.querySelector(".form-text");
    formText.innerHTML = "Maksimal 2000 karakter";
    formText.classList.remove("text-danger");

    // Reset submit button
    const submitBtn = document.getElementById("submitSaran");
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-save"></i> Simpan Saran';

    // Tampilkan modal
    saranModal.show();

    // Focus ke textarea
    setTimeout(() => {
        saranTextarea.focus();
    }, 500);
}

/**
 * Fungsi untuk menampilkan modal konfirmasi hapus
 * @param {number} logId - ID log bimbingan
 */
function deleteLog(logId) {
    const deleteForm = document.getElementById("deleteForm");
    const deleteModal = new bootstrap.Modal(
        document.getElementById("deleteModal")
    );

    // Set action URL untuk form
    const bimbinganId = getBimbinganIdFromUrl();
    deleteForm.action = `/bimbingan/${bimbinganId}/log-bimbingan/${logId}`;

    // Reset submit button
    const deleteBtn = deleteForm.querySelector('button[type="submit"]');
    deleteBtn.disabled = false;
    deleteBtn.innerHTML = '<i class="fas fa-trash"></i> Hapus';

    // Tampilkan modal
    deleteModal.show();
}

/**
 * Fungsi helper untuk mengambil bimbingan ID dari URL
 * @returns {string} bimbingan ID
 */
function getBimbinganIdFromUrl() {
    const pathSegments = window.location.pathname.split("/");
    const bimbinganIndex = pathSegments.indexOf("bimbingan");
    return pathSegments[bimbinganIndex + 1];
}

/**
 * Fungsi untuk menampilkan alert sukses
 * @param {string} message - Pesan sukses
 */
function showSuccessAlert(message) {
    const alertHtml = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    const cardBody = document.querySelector(".card-body");
    cardBody.insertAdjacentHTML("afterbegin", alertHtml);

    // Auto hide after 5 seconds
    setTimeout(() => {
        const alert = cardBody.querySelector(".alert-success");
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}

/**
 * Fungsi untuk menampilkan alert error
 * @param {string} message - Pesan error
 */
function showErrorAlert(message) {
    const alertHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    const cardBody = document.querySelector(".card-body");
    cardBody.insertAdjacentHTML("afterbegin", alertHtml);

    // Auto hide after 8 seconds
    setTimeout(() => {
        const alert = cardBody.querySelector(".alert-danger");
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 8000);
}

/**
 * Fungsi untuk validasi form saran sebelum submit
 */
function validateSaranForm() {
    const saranTextarea = document.getElementById("saran_dosen");
    const saranValue = saranTextarea.value.trim();

    if (saranValue.length === 0) {
        showErrorAlert("Saran tidak boleh kosong.");
        saranTextarea.focus();
        return false;
    }

    if (saranValue.length > 2000) {
        showErrorAlert("Saran tidak boleh lebih dari 2000 karakter.");
        saranTextarea.focus();
        return false;
    }

    return true;
}

// Event listener untuk validasi form saran
document.addEventListener("DOMContentLoaded", function () {
    const saranForm = document.getElementById("saranForm");
    if (saranForm) {
        saranForm.addEventListener("submit", function (e) {
            if (!validateSaranForm()) {
                e.preventDefault();
                return false;
            }
        });
    }
});

/**
 * Fungsi untuk refresh halaman setelah operasi berhasil
 * Digunakan jika ingin menghindari redirect dari server
 */
function refreshPage() {
    window.location.reload();
}

/**
 * Fungsi untuk scroll ke element tertentu
 * @param {string} elementId - ID element tujuan
 */
function scrollToElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({
            behavior: "smooth",
            block: "center",
        });
    }
}

// Keyboard shortcuts
document.addEventListener("keydown", function (e) {
    // ESC untuk menutup modal
    if (e.key === "Escape") {
        const openModals = document.querySelectorAll(".modal.show");
        openModals.forEach((modal) => {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.hide();
            }
        });
    }

    // Ctrl+Enter untuk submit form di modal
    if (e.ctrlKey && e.key === "Enter") {
        const activeModal = document.querySelector(".modal.show");
        if (activeModal) {
            const form = activeModal.querySelector("form");
            if (form) {
                form.dispatchEvent(new Event("submit", { cancelable: true }));
            }
        }
    }
});
