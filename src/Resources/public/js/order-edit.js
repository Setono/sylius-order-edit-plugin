window.addEventListener('DOMContentLoaded', (event) => {
    document.querySelectorAll('button.delete-order-item').forEach((button) => {
        button.addEventListener('click', (event) => {
            const orderLineId = event.currentTarget.closest('tr').remove();
        });
    });
});
