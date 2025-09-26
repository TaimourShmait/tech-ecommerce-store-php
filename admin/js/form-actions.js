document.addEventListener("DOMContentLoaded", (e) => {
    document.querySelectorAll(".update-admin-btn").forEach(updateAdminBtn => {
        updateAdminBtn.addEventListener("click", (e) => {
            const adminId = updateAdminBtn.dataset.adminId;
            const form = document.querySelector(`update-admin-${adminId}-form`);
            if (form)
                form.submit();
        });
    });
});