document.addEventListener("DOMContentLoaded", (e) => {
    document.querySelectorAll(".delete-btn").forEach(deleteBtn => deleteBtn.addEventListener("click", (e) => {
        e.preventDefault();
        const deleteForm = deleteBtn.closest("form");
        const id = deleteForm.dataset.id;
        const user = deleteBtn.dataset.user;
        if (confirm(`Are you sure you want to delete ${user}`)) {
            deleteForm.submit();
        } 
    }));
});