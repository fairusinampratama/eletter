// Modal handling functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove("hidden");
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add("hidden");
    }
}

// Event listeners for modal toggles
document.addEventListener("DOMContentLoaded", function () {
    // Close modal buttons
    document.querySelectorAll("[data-modal-toggle]").forEach((button) => {
        button.addEventListener("click", () => {
            const modalId = button.getAttribute("data-modal-toggle");
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.toggle("hidden");
            }
        });
    });

    // Close modal when clicking outside
    document.querySelectorAll(".modal").forEach((modal) => {
        modal.addEventListener("click", (e) => {
            if (e.target === modal) {
                modal.classList.add("hidden");
            }
        });
    });

    // Close modal when pressing ESC key
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            document
                .querySelectorAll(".modal:not(.hidden)")
                .forEach((modal) => {
                    modal.classList.add("hidden");
                });
        }
    });
});

// Export functions for use in other files
window.openModal = openModal;
window.closeModal = closeModal;
