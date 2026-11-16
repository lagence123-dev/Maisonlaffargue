let ajax_url = null;
let module_url = null;
let demo_activation_form_group = null;
let create_order_form_group = null;
let loader = null;

document.onready = function () {

    // Set values
    ajax_url = document.getElementById('ajax_url').value;
    module_url = document.getElementById('module_url').value;
    demo_activation_form_group = document.querySelector('#configuration_form #fieldset_0 .form-group:nth-child(3) p.help-block');
    create_order_form_group = document.querySelector('#configuration_form #fieldset_0 .form-group:last-child');

    let has_click = false;

    // Check demo mode availability
    demoModeAvailability([
        document.getElementById('demo_mode_on'),
        document.getElementById('demo_mode_off'),
        document.getElementById('create_test_order_on'),
        document.getElementById('create_test_order_off')
    ]);

    // Show/hide/change elements depending on demo mode is activated or not
    if (!document.getElementById('demo_mode_on').checked) {
        create_order_form_group.style.display = "none";

    } else {
        create_order_form_group.style.display = "block";

        // Change EPT add btn href
        let add_ept_btn = document.querySelector('#form-form-ept-list #desc-MoneticoPaiement-new');
        add_ept_btn.href = 'javascript:displayPaymentModeModal()';

        // Hide add company code btn
        if (document.querySelector('#form-form-code-site-list #desc-MoneticoPaiement-new')) {
            document.querySelector('#form-form-code-site-list #desc-MoneticoPaiement-new').style.display = 'none'
        }
    }

    // Demo mode activation/deactivation buttons
    document.getElementsByName('demo_mode').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();

            if (!has_click) {
                has_click = true;

                if (parseInt(e.target.value) === 1) {
                    displayPaymentModeModal().then(() => {
                        has_click = false;
                    });
                } else {
                    displayQuitDemoModal().then(() => {
                        has_click = false;
                    });
                }
            }
        })
    })

    // Orders creation activation/deactivation buttons
    document.getElementsByName('create_test_order').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();

            if (!has_click) {
                has_click = true;

                manageConfigurationField('demo_orders', parseInt(e.target.value)).then((result) => {
                    if (result) {
                        window.location.replace(module_url);
                    }
                });
            }
        })
    })

    $(document).on('hide.bs.modal', '#fieldset_0 .modal', () => {
        $('.custom-ajax-loader-layout').css('opacity', 0.4)
    })

    // Set loader div in html page
    loader = document.createElement('div');
    let loader_div = document.createElement('div');
    let loader_div_layout = document.createElement('div');

    loader.classList.add('custom-loader');
    loader_div.classList.add('custom-ajax-loader');
    loader_div_layout.classList.add('custom-ajax-loader-layout');

    loader.appendChild(loader_div)
    loader.appendChild(loader_div_layout)
    document.getElementById('footer').appendChild(loader);
    loader_div.innerHTML = '<div class="icon-refresh icon-spin icon-fw"></div> '

}

function demoModeAvailability(buttons) {
    $.ajax({
        type: 'POST',
        url: ajax_url,
        data: {
            ajax: true,
            action: 'DemoModeActivation'
        }
    }).success(function (data) {
        let resp = JSON.parse(data);

        if (resp['can_be_activated']) {
            buttons.forEach((btn) => {
                btn.disabled = false;
            })
        } else {
            demo_activation_form_group.innerText += '\n' + resp['help_text'];
        }
    });
}

function addModal(data) {
    let modal_div = document.createElement('div')
    modal_div.innerHTML = data
    document.getElementById('fieldset_0').appendChild(modal_div)
}

function displayPaymentModeModal() {
    return new Promise((resolve, reject) => {
        // ajax request to show the payment mode selector modal
        $.ajax({
            type: 'POST',
            url: ajax_url,
            dataType: "html",
            data: {
                ajax: true,
                action: 'PaymentModeSelector',
            },
            beforeSend: () => {
                loader.style.display = 'block';
            },
        }).success(function (data) {
            loader.style.display = 'none';

            if (!document.getElementById('btn-payment-mode')) {
                addModal(data);
            }
            $('#MoneticoPaiementPaymentModeModal').modal('show');

            resolve(true);
        });
    })
}

function displayQuitDemoModal() {
    return new Promise((resolve, reject) => {

        $.ajax({
            type: 'POST',
            url: ajax_url,
            dataType: "html",
            data: {
                ajax: true,
                action: 'QuitDemoModal',
            },
            beforeSend: () => {
                loader.style.display = 'block';
            },
        }).success(function (data) {
            loader.style.display = 'none';

            if (!document.getElementById('btn-quit-demo-mode')) {
                addModal(data);
            }

            $('#MoneticoPaiementQuitDemoModal').modal('show');

            resolve(true);
        });
    })
}

function manageConfigurationField(config_field, bool_val) {
    return new Promise((resolve, reject) => {
        let method_to_call = config_field === 'demo_mode' ? 'ManageConfigDemoMode' : 'ManageConfigDemoOrders';

        $.ajax({
            type: 'POST',
            url: ajax_url,
            data: {
                ajax: true,
                action: method_to_call,
                bool_val: bool_val,
            },
            beforeSend: () => {
                if (config_field !== 'demo_mode') {
                    loader.style.display = 'block';
                }
            },
        }).success(function (data) {
            resolve(true);
        });
    })

}

function createDemoData() {

    document.getElementById('create-data-btn').disabled = true;

    let payment_mode_choices = document.getElementById('payment_modes_select').options;
    let selected_payment_modes = [];

    Object.values(payment_mode_choices).forEach((choice) => {
        if (choice.selected) {
            selected_payment_modes.push(choice.value);
        }
    })

    if (selected_payment_modes.length) {
        $.ajax({
            type: 'POST',
            url: ajax_url,
            dataType: "text",
            data: {
                ajax: true,
                action: 'CreateDemoData',
                payment_modes: selected_payment_modes,
            },
            beforeSend: () => {
                loader.style.display = 'block';
                $('.custom-ajax-loader-layout').css('opacity', 0)
            },
        }).success(function () {

            manageConfigurationField('demo_mode', 1).then((result) => {
                if (result) {
                    window.location.replace(module_url);
                }
            });
        });
    } else {
        document.getElementById('create-data-btn').disabled = false;

    }
}

function deactivateDemoMode() {
    document.getElementById('confirm-quit-btn').disabled = true;
    loader.style.display = 'block';
    $('.custom-ajax-loader-layout').css('opacity', 0)

    manageConfigurationField('demo_mode', 0).then((result) => {
        if (result) {
            window.location.replace(module_url);

        }
    });
}
