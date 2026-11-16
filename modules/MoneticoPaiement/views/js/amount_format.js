/**
 * Format the refund amount
 */
function replaceCommaToDot(value) {
    var t = 0;
    return value.replace(/[^\d]/g, function (match) {
        if (match === "." || match === ",") {
            t++;
            return (t >= 2) ? "" : ".";
        }
        return "";
    });
}

function fixDecimalValue(newValue, maxDecimalLength) {
    if (maxDecimalLength) {
        var newValueLength = newValue.length;
        var separatorPosition = newValue.indexOf(".");
        if (separatorPosition !== -1) {
            var lastPart = newValue.slice(separatorPosition + 1);
            var lastPartLength = lastPart.length;
            if (lastPartLength > maxDecimalLength) {
                newValue = newValue.substring(0, newValueLength - lastPartLength + maxDecimalLength);
            }
        }
    }
    return newValue;
}

window.onload = function () {
    var input_amount = document.getElementById('amount');
    var max_amount = document.getElementById('hidden_possible_amount').value;
    var maxDecimalLength = 2;
    var in_partial_refund = true;

    document.getElementById('configuration_form_submit_btn').setAttribute('disabled', 'disabled');
    document.getElementById('configuration_form_submit_btn').style.cursor = 'not-allowed';

    document.getElementById('amount').addEventListener('input', (e) => {
        var value = e.target.value;
        // if amount is with "," instead of "."
        var newValue = replaceCommaToDot(value)
        // if decimal amount is more than the maximal
        newValue = fixDecimalValue(newValue, maxDecimalLength);
        //  if amount value is more than maximum amount
        if (Number(newValue) > Number(max_amount)) {
            newValue = max_amount;
        }

        if (Number(newValue) === 0) {
            document.getElementById('configuration_form_submit_btn').setAttribute('disabled', 'disabled');
            document.getElementById('configuration_form_submit_btn').style.cursor = 'not-allowed';
        } else {
            document.getElementById('configuration_form_submit_btn').removeAttribute('disabled');
            document.getElementById('configuration_form_submit_btn').style.cursor = null;
        }

        input_amount.value = newValue;
    });


    document.querySelectorAll('input[name=refund_type], input[name=capture_type]').forEach(type => type.addEventListener('change', function () {
        document.querySelector('#amount').parentElement.parentElement.classList.toggle('hide');
        in_partial_refund = !in_partial_refund;
        if (in_partial_refund) {
            var amount_value = document.getElementById('amount').value;
            if (Number(amount_value) === 0) {
                document.getElementById('configuration_form_submit_btn').setAttribute('disabled', 'disabled');
                document.getElementById('configuration_form_submit_btn').style.cursor = 'not-allowed';
            }
        } else {
            var possible_amount = document.getElementById('hidden_possible_amount').value;
            if (Number(possible_amount) > 0) {
                document.getElementById('configuration_form_submit_btn').removeAttribute('disabled');
                document.getElementById('configuration_form_submit_btn').style.cursor = null;
            }
        }
    }))

    document.getElementById('configuration_form').addEventListener('submit', (e) => {
        document.getElementById('amount').setAttribute('readonly', 'readonly');
        document.getElementById('configuration_form_submit_btn').setAttribute('disabled', 'disabled');
        document.getElementById('configuration_form_submit_btn').style.cursor = 'not-allowed';
    });
}

document.onready = function () {

    var maxDecimalLength = 2;
    // inputs list
    var float_inputs = [
        'code_site_amount_min',
        'code_site_amount_max'
    ]
    // add event
    float_inputs.forEach((input) => {
            if (document.getElementById(input)) {
                document.getElementById(input).addEventListener('input', (e) => {
                    var value = e.target.value;
                    // if amount is with "," instead of "."
                    var newValue = replaceCommaToDot(value)
                    // if decimal amount is more than the maximal
                    newValue = fixDecimalValue(newValue, maxDecimalLength);
                    // update value with new value
                    document.getElementById(input).value = newValue;
                })
            }
        }
    );
}