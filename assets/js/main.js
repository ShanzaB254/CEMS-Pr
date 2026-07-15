// assets/js/main.js

document.addEventListener('DOMContentLoaded', () => {
    // --- UNIVERSAL ACTION MODAL LOGIC ---
    const actionModal = document.getElementById('universalActionModal');
    
    if (actionModal) {
        const actionTitle = document.getElementById('actionTitle');
        const actionDesc = document.getElementById('actionDesc');
        const confirmBtn = document.getElementById('confirmActionBtn');
        const cancelBtn = document.getElementById('cancelActionBtn');

        // Attach to any link with the class 'trigger-modal'
        document.querySelectorAll('.trigger-modal').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault(); 
                
                const targetUrl = this.getAttribute('href');
                const actionType = this.getAttribute('data-action');
                
                confirmBtn.setAttribute('href', targetUrl);

                // Dynamically change text and colors based on action type
                if (actionType === 'approve') {
                    actionTitle.innerText = "Confirm Approval";
                    actionDesc.innerText = "Are you sure you want to approve this request?";
                    confirmBtn.style.background = "#10b981"; // Emerald green
                    confirmBtn.innerText = "Yes, Approve";
                } else if (actionType === 'reject') {
                    actionTitle.innerText = "Confirm Rejection";
                    actionDesc.innerText = "Are you sure you want to reject this? This cannot be undone.";
                    confirmBtn.style.background = "#ef4444"; // Red
                    confirmBtn.innerText = "Yes, Reject";
                } else if (actionType === 'delete') {
                    actionTitle.innerText = "Confirm Deletion";
                    actionDesc.innerText = "Are you absolutely sure? This will permanently delete the record.";
                    confirmBtn.style.background = "#dc2626"; // Darker red
                    confirmBtn.innerText = "Yes, Delete";
                }

                actionModal.style.display = 'flex';
            });
        });

        cancelBtn.addEventListener('click', () => { 
            actionModal.style.display = 'none'; 
        });
    }
});