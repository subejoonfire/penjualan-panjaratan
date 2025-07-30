<!-- Modal Notification Component -->
<div id="modalNotification" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center h-full w-full hidden z-50 p-2 sm:p-4" style="display: none !important;">
    <div class="relative mx-auto p-3 sm:p-5 border w-full max-w-sm sm:max-w-md max-h-[90vh] shadow-lg rounded-md bg-white overflow-y-auto">
        <div class="mt-2 sm:mt-3">
            <!-- Icon -->
            <div class="mx-auto flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full" id="modalIcon">
                <i class="fas fa-info-circle text-lg sm:text-2xl text-blue-500" id="modalIconClass"></i>
            </div>
            
            <!-- Title -->
            <div class="mt-2 sm:mt-3 text-center">
                <h3 class="text-base sm:text-lg font-medium text-gray-900" id="modalTitle">Konfirmasi</h3>
                
                <!-- Message -->
                <div class="mt-2 px-2 py-2 sm:px-7 sm:py-3">
                    <p class="text-sm text-gray-500" id="modalMessage">
                        Apakah Anda yakin ingin melakukan tindakan ini?
                    </p>
                </div>
                
                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row sm:justify-center items-center px-2 py-2 sm:px-4 sm:py-3 space-y-2 sm:space-y-0 sm:space-x-2" id="modalButtons">
                    <button id="modalCancelBtn" class="w-full sm:w-24 px-3 py-2 bg-gray-500 text-white text-sm font-medium rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Batal
                    </button>
                    <button id="modalConfirmBtn" class="w-full sm:w-24 px-3 py-2 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Ya
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Modal Notification Functions
function showModalNotification(options = {}) {
    const modal = document.getElementById('modalNotification');
    const icon = document.getElementById('modalIcon');
    const iconClass = document.getElementById('modalIconClass');
    const title = document.getElementById('modalTitle');
    const message = document.getElementById('modalMessage');
    const buttons = document.getElementById('modalButtons');
    const cancelBtn = document.getElementById('modalCancelBtn');
    const confirmBtn = document.getElementById('modalConfirmBtn');
    
    // Set default values
    const config = {
        type: 'confirm', // confirm, alert, success, error
        title: 'Konfirmasi',
        message: 'Apakah Anda yakin ingin melakukan tindakan ini?',
        confirmText: 'Ya',
        cancelText: 'Batal',
        confirmClass: 'bg-red-500 hover:bg-red-600 focus:ring-red-300',
        onConfirm: null,
        onCancel: null,
        showCancel: true,
        ...options
    };
    
    // Set content
    title.textContent = config.title;
    // Support array or string, always use textContent (never innerHTML)
    if (Array.isArray(config.message)) {
        message.textContent = config.message.join('\n');
    } else {
        // Replace <br> and <br/> with newlines for plain text display
        message.textContent = String(config.message).replace(/<br\s*\/?>/gi, '\n');
    }
    confirmBtn.textContent = config.confirmText;
    cancelBtn.textContent = config.cancelText;
    
    // Set icon and colors based on type
    switch(config.type) {
        case 'success':
            icon.className = 'mx-auto flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-green-100';
            iconClass.className = 'fas fa-check-circle text-lg sm:text-2xl text-green-600';
            confirmBtn.className = 'w-full sm:w-24 px-3 py-2 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300';
            break;
        case 'error':
            icon.className = 'mx-auto flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-red-100';
            iconClass.className = 'fas fa-exclamation-circle text-lg sm:text-2xl text-red-600';
            confirmBtn.className = 'w-full sm:w-24 px-3 py-2 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300';
            break;
        case 'warning':
            icon.className = 'mx-auto flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-yellow-100';
            iconClass.className = 'fas fa-exclamation-triangle text-lg sm:text-2xl text-yellow-600';
            confirmBtn.className = 'w-full sm:w-24 px-3 py-2 bg-yellow-500 text-white text-sm font-medium rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-300';
            break;
        default:
            icon.className = 'mx-auto flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-blue-100';
            iconClass.className = 'fas fa-info-circle text-lg sm:text-2xl text-blue-600';
            confirmBtn.className = 'w-full sm:w-24 px-3 py-2 bg-blue-500 text-white text-sm font-medium rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300';
    }
    
    // Show/hide cancel button
    if (!config.showCancel) {
        cancelBtn.style.display = 'none';
    } else {
        cancelBtn.style.display = 'block';
    }
    
    // Set event listeners
    confirmBtn.onclick = () => {
        hideModalNotification();
        if (config.onConfirm) config.onConfirm();
    };
    
    cancelBtn.onclick = () => {
        hideModalNotification();
        if (config.onCancel) config.onCancel();
    };
    
    // Show modal
    modal.style.display = 'flex';
    modal.classList.remove('hidden');
}

function hideModalNotification() {
    const modal = document.getElementById('modalNotification');
    modal.style.display = 'none';
    modal.classList.add('hidden');
}

// Ensure modal is hidden on page load
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalNotification');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }
});

// Close modal when clicking outside
document.getElementById('modalNotification').addEventListener('click', function(e) {
    if (e.target === this) {
        hideModalNotification();
    }
});

// Helper functions for common use cases
function confirmAction(message, onConfirm, onCancel = null) {
    showModalNotification({
        type: 'confirm',
        title: 'Konfirmasi',
        message: message,
        onConfirm: onConfirm,
        onCancel: onCancel
    });
}

function showAlert(message, type = 'info') {
    // If message is a string with <br>, convert to array for multi-line
    let msg = message;
    if (typeof message === 'string' && message.includes('<br')) {
        msg = message.split(/<br\s*\/?>/gi);
    }
    showModalNotification({
        type: type,
        title: type === 'success' ? 'Berhasil' : type === 'error' ? 'Error' : 'Informasi',
        message: msg,
        confirmText: 'OK',
        showCancel: false,
        onConfirm: () => hideModalNotification()
    });
}
</script>