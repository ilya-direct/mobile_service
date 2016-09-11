// Событие при открытии модального окна
$('#order-service').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var device_id = button.data('device-id');
    var service_id = button.data('service-id');

    var description = $(this).find('.device-modal-description')[0];
    var form = $(this).find('form');
    var device = form.find('input[name*=device_id]')[0];
    if (device_id) {
        device.value = device_id;
        $(description).append('<p> Устройтво: <strong>' + button.data('device-name') + '</strong></p>');
    } else {
        device.value = 0;
    }

    var service = form.find('input[name*=service_id]')[0];
    if (service_id) {
        service.value = service_id;
        $(description).append('<p> Услуга: <strong>' + button.data('service-name') + '</strong></p>');
        $(description).append('<p> Стоимость: <strong>' + button.data('price') + ' руб.</strong></p>');
    } else {
        service.value = 0;
    }

});

// Событие при закрытии модального окна
$('#order-service').on('hidden.bs.modal', function (event) {
    var description = $(this).find('.device-modal-description');
    $(description).empty();
});


// Отправка ActiveForm Ajax-запросом
$('#device-order-form').on('beforeSubmit', function (event) {
    event.preventDefault();
    var form = $(this);
    // Предотвращение double-click
    if (form.data('requestRunning')) {
        return false;
    }
    form.data('requestRunning', true);

    $.post(form.attr('action'), form.serialize(), function(errors) {
        form.yiiActiveForm('updateMessages', errors);
        form.data('requestRunning', false);
    });

    return false;
});