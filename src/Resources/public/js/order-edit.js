window.addEventListener('DOMContentLoaded', (event) => {
    document.querySelectorAll('button.delete-order-item').forEach((button) => {
        button.addEventListener('click', (event) => {
            const orderLineId = event.currentTarget.closest('tr').remove();
        });
    });

    document.querySelector('button.add-order-item').addEventListener('click', (event) => {
        const orderItemTable = event.currentTarget.closest('table');

        const html = orderItemTable
            .dataset
            .prototype
            .replace(
                /__name__/g,
                orderItemTable.dataset.index
            )
        ;

        orderItemTable.querySelector('tbody').insertAdjacentHTML('beforeend', html)

        orderItemTable.dataset.index++;
    });
});
