<?php ?>
<div class="cmodal" id="cmodal">
    <div class="cmodal-content" id="cmodalContent">
        <div class="cmodal-icon">
            <i class="bi" id="cmodalIconSymbol"></i>
        </div>
        <div class="cmodal-body">
            <h2 class="cmodal-title" id="cmodalTitle">Confirm</h2>
            <p class="cmodal-message" id="cmodalMessage">Are you sure?</p>
            <p class="cmodal-item-name" id="cmodalItemName"></p>

            <div class="cmodal-buttons" id="cmodalButtons">
                <button class="btn-cmodal-cancel" id="cmodalCancelBtn">Cancel</button>
                <button class="btn-cmodal-confirm" id="cmodalConfirmBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<style>
.cmodal {
    display: none;
    position: fixed;
    z-index: 20000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    align-items: center;
    justify-content: center;
    font-family: 'Outfit', sans-serif;
}

.cmodal-content {
    background: white;
    max-width: 400px;
    width: 90%;
    padding: 0;
    text-align: center;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    animation: cmodalSlideIn 0.3s ease-out;
}

@keyframes cmodalSlideIn {
    from { opacity: 0; transform: translateY(-50px) scale(0.9); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.cmodal-icon {
    padding: 3rem 2rem 1rem 2rem;
}

.cmodal-icon i {
    font-size: 3rem;
}

.cmodal-type-danger .cmodal-icon i { color: #dc3545; }
.cmodal-type-warning .cmodal-icon i { color: #ff9800; }
.cmodal-type-info .cmodal-icon i { color: #2196f3; }
.cmodal-type-success .cmodal-icon i { color: #4caf50; }
.cmodal-type-primary .cmodal-icon i { color: #a03c45; }

.cmodal-type-danger .btn-cmodal-confirm { background-color: #dc3545; border-color: #dc3545; }
.cmodal-type-danger .btn-cmodal-confirm:hover { background-color: #c82333; border-color: #c82333; }

.cmodal-type-warning .btn-cmodal-confirm { background-color: #ff9800; border-color: #ff9800; }
.cmodal-type-warning .btn-cmodal-confirm:hover { background-color: #e68900; border-color: #e68900; }

.cmodal-type-info .btn-cmodal-confirm { background-color: #2196f3; border-color: #2196f3; }
.cmodal-type-info .btn-cmodal-confirm:hover { background-color: #1976d2; border-color: #1976d2; }

.cmodal-type-success .btn-cmodal-confirm { background-color: #4caf50; border-color: #4caf50; }
.cmodal-type-success .btn-cmodal-confirm:hover { background-color: #388e3c; border-color: #388e3c; }

.cmodal-type-primary .btn-cmodal-confirm { background-color: #a03c45; border-color: #a03c45; }
.cmodal-type-primary .btn-cmodal-confirm:hover { background-color: #8b3a3a; border-color: #8b3a3a; }

.cmodal-body {
    padding: 0 2rem 2rem 2rem;
}

.cmodal-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 1rem;
}

.cmodal-message {
    color: #666;
    font-size: 1rem;
    margin-bottom: 1.5rem;
    line-height: 1.5;
}

.cmodal-item-name {
    color: #a03c45;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
    display: none;
    padding: .5rem;
    background-color: #f8f9fa;
    border-radius: 4px;
    border-left: 4px solid #a03c45;
}

.cmodal-item-name:not(:empty) { display: block; }

.cmodal-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.cmodal-buttons.single-button .btn-cmodal-confirm {
    min-width: 150px;
}

.btn-cmodal-cancel,
.btn-cmodal-confirm {
    flex: 1;
    padding: .75rem 1.5rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: .95rem;
    font-weight: 500;
    transition: all .2s ease;
    font-family: inherit;
}

.btn-cmodal-cancel {
    background-color: #f8f9fa;
    color: #333;
    border: 1px solid #dee2e6;
}

.btn-cmodal-cancel:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
}

.btn-cmodal-cancel.hidden {
    display: none;
}

.btn-cmodal-confirm {
    color: white;
    font-weight: 600;
    border: 1px solid transparent;
}

.btn-cmodal-confirm:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

@media (max-width: 480px) {
    .cmodal-content { width: 95%; margin: 1rem; }
    .cmodal-icon { padding: 2rem 1rem 1rem; }
    .cmodal-icon i { font-size: 2.5rem; }
    .cmodal-body { padding: 0 1.5rem 1.5rem; }
    .cmodal-buttons { flex-direction: column; gap: .5rem; }
    .btn-cmodal-cancel, .btn-cmodal-confirm { width: 100%; }
}
</style>


<script>
function showAlert(type, title, message, itemName = "", showCancel = false, confirmCallback = null, confirmText = "Confirm") {
    const modal = document.getElementById("cmodal");
    const modalContent = document.getElementById("cmodalContent");
    const modalIcon = document.getElementById("cmodalIconSymbol");
    const modalTitle = document.getElementById("cmodalTitle");
    const modalMessage = document.getElementById("cmodalMessage");
    const modalItemName = document.getElementById("cmodalItemName");
    const modalCancelBtn = document.getElementById("cmodalCancelBtn");
    const modalConfirmBtn = document.getElementById("cmodalConfirmBtn");
    const modalButtons = document.getElementById("cmodalButtons");

    modal.className = "cmodal cmodal-type-" + type;

    let iconClass = "bi-exclamation-triangle";
    if (type === "success") iconClass = "bi-check-circle";
    else if (type === "warning") iconClass = "bi-exclamation-triangle";
    else if (type === "danger") iconClass = "bi-x-circle";
    else if (type === "info") iconClass = "bi-info-circle";
    else if (type === "primary") iconClass = "bi-question-circle";

    modalIcon.className = "bi " + iconClass;
    modalTitle.textContent = title;
    modalMessage.textContent = message;
    modalItemName.textContent = itemName;
    modalConfirmBtn.textContent = confirmText;

    if (showCancel && confirmCallback) {
        modalCancelBtn.classList.remove("hidden");
        modalButtons.classList.remove("single-button");

        modalConfirmBtn.onclick = function() {
            hideCModal();
            confirmCallback();
        };

        modalCancelBtn.onclick = hideCModal;
    } else {
        modalCancelBtn.classList.add("hidden");
        modalButtons.classList.add("single-button");
        modalConfirmBtn.onclick = hideCModal;
    }

    modal.style.display = "flex";
}

function hideCModal() {
    const modal = document.getElementById("cmodal");
    if (modal) modal.style.display = "none";
}

document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("cmodal");
    const modalContent = document.getElementById("cmodalContent");

    modal.addEventListener("mousedown", function(e) {
        if (e.target === modal) hideCModal();
    });

    modalContent.addEventListener("mousedown", function(e) {
        e.stopPropagation();
    });

    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape") hideCModal();
    });
});
</script>
