document.querySelectorAll('button.delete-order-item').forEach((button) => {
    button.addEventListener('click', (event) => {
        var row = event.currentTarget.closest('tr');
        row.nextElementSibling.remove();
        row.remove();
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

    var rows = orderItemTable.querySelectorAll('form[name="sylius_order"] tbody tr');
    var lastItemRowDeleteButton = rows[rows.length - 2].querySelector('button.delete-order-item');
    lastItemRowDeleteButton.addEventListener('click', (event) => {
        var row = event.currentTarget.closest('tr');
        row.nextElementSibling.remove();
        row.remove();
    });
});
