function showToast(message, type = 'info') {
    const toastContainer = $('#toastContainer');
    const toastTitle = $('#toastTitle');
    const toastBody = $('#toastBody');

    toastBody.text(message);

    switch (type) {
        case 'success':
            toastTitle.text('Success');
            toastContainer.removeClass().addClass('toast bg-success text-white');
            break;
        case 'error':
            toastTitle.text('Error');
            toastContainer.removeClass().addClass('toast bg-danger text-white');
            break;
        case 'warning':
            toastTitle.text('Warning');
            toastContainer.removeClass().addClass('toast bg-warning text-dark');
            break;
        case 'info':
        default:
            toastTitle.text('Info');
            toastContainer.removeClass().addClass('toast bg-info text-white');
            break;
    }

    toastContainer.toast({ delay: 5000 });
    toastContainer.toast('show');
}

function showBootstrapConfirmation(message, onConfirm) {
    $('#confirmationModal .modal-body').text(message);
    $('#confirmationModal').modal('show');

    $('#confirmButton').off('click').on('click', function() {
        onConfirm();
        $('#confirmationModal').modal('hide');
    });
}
