/**
 * Custom Js for code site form
 */
window.onload = function () {

    document.getElementById("tableRender").setAttribute('hidden', 'hidden');

    /**
     Function for generate the header
     **/
    function generate_header() {
        var h_tr = document.createElement("tr");
        cs_3dsv2_header.forEach(col => {
            var th_tr = document.createElement("th");
            th_tr.setAttribute('scope', 'col');
            th_tr.appendChild(document.createTextNode(col));
            h_tr.appendChild(th_tr);
        });
        return h_tr;
    }

    /**
     Function for generate the body
     **/
    function genereate_body() {
        var thead = document.createElement("tbody");
        thead.setAttribute("id", "3dsv2TableBody")
        if (cs_3dsv2_body.length > 0) {
            cs_3dsv2_body.forEach((opt) => {
                row_index++;
                ref_index++;

                var h_tr = document.createElement("tr");
                var th_td = document.createElement("th");
                th_td.setAttribute('scope', 'row');
                th_td.className = "3dsv2Index"
                th_td.appendChild(document.createTextNode(ref_index))
                h_tr.appendChild(th_td)

                var amount_td = document.createElement("td");

                var btn_amount_td = document.createElement("input");
                btn_amount_td.setAttribute('type', 'number');
                btn_amount_td.setAttribute('value', (opt.amount === undefined) ? '0' : opt.amount);
                btn_amount_td.setAttribute('name', "cs_3dsv2_otps[" + row_index + "][amount]");
                btn_amount_td.setAttribute('step', '0.01')
                btn_amount_td.setAttribute('min', '0.01')

                if (opt.amount === undefined || opt.amount === '0') {
                    btn_amount_td.setAttribute('disabled', 'disabled')
                }
                btn_amount_td.className = "form-control";
                btn_amount_td = add_event_input_number(btn_amount_td);
                amount_td.appendChild(btn_amount_td);

                h_tr.appendChild(amount_td)

                var state_td = document.createElement("td");
                var select_state_td = generate_select(opt.state, row_index);
                state_td.appendChild(select_state_td)
                h_tr.appendChild(state_td)

                var delete_td = document.createElement("td");
                if (row_index > 1) {
                    delete_td.appendChild(generate_delete_btn(h_tr));
                }

                h_tr.appendChild(delete_td)

                thead.appendChild(h_tr)
            });
        } else {
            row_index++;
            ref_index++;
            var h_tr = document.createElement("tr");

            var th_td = document.createElement("th");
            th_td.setAttribute('scope', 'row');
            th_td.className = "3dsv2Index"
            th_td.appendChild(document.createTextNode(ref_index))
            h_tr.appendChild(th_td)

            var amount_td = document.createElement("td");
            var btn_amount_td = document.createElement("input");
            btn_amount_td.setAttribute('type', 'number');
            btn_amount_td.setAttribute('value', '0');
            btn_amount_td.setAttribute('name', "cs_3dsv2_otps[" + row_index + "][amount]");
            btn_amount_td.setAttribute('disabled', 'disabled')
            btn_amount_td.className = "form-control";
            amount_td.appendChild(btn_amount_td);

            h_tr.appendChild(amount_td)

            var state_td = document.createElement("td");
            var select_state_td = generate_select("no_preference", row_index);
            state_td.appendChild(select_state_td)
            h_tr.appendChild(state_td)

            var delete_td = document.createElement("td");
            h_tr.appendChild(delete_td)

            thead.appendChild(h_tr)
        }

        return thead;
    }

    /**
     Function for add a delete btn
     **/
    function generate_delete_btn(h_tr) {
        var a_delete_td = document.createElement("a");
        a_delete_td.className = "btn btn-danger removeRow";
        var i_a_delete_td = document.createElement("i");
        i_a_delete_td.className = "icon-trash";
        a_delete_td.appendChild(i_a_delete_td);
        a_delete_td.addEventListener('click', (e) => {
            e.preventDefault();
            h_tr.remove();

            var index = document.getElementsByClassName('3dsv2Index');
            ref_index = index.length;
            for (var i = 1; i <= index.length; i++) {
                index[i - 1].innerHTML = i;
            }
        });

        return a_delete_td;
    }

    /**
     Function for generate the btn for add row
     **/
    function generate_plus() {

        var p_hr = document.createElement("tr");
        var p_td = document.createElement("td");
        p_td.setAttribute("colspan", "4");
        var p_td_btn = document.createElement("button");
        p_td_btn.className = "btn btn-primary";
        p_td_btn.setAttribute("id", "addMoreRow");
        p_td_btn.setAttribute("style", "width:100%")

        p_td_btn.appendChild(document.createTextNode("+"));
        p_td.appendChild(p_td_btn);
        p_hr.appendChild(p_td);

        return p_hr;
    }

    /**
     Function for generate the code_site_3dsv2
     **/
    function generate_select(selected, index) {
        var select_div = document.createElement("select");
        select_div.setAttribute('name', "cs_3dsv2_otps[" + index + "][state]");
        cs_3dsv2_states.forEach((stat) => {
            select_div.appendChild(new Option(stat.name, stat.value, true, stat.value === selected));
        })
        return select_div;
    }

    /**
     * Function for init the render tab
     */
    function init_render_tab() {
        let cs_3dv2 = JSON.parse(document.getElementById("tableRender").value);
        cs_3dsv2_header = cs_3dv2.header;
        cs_3dsv2_states = cs_3dv2.states;
        cs_3dsv2_body = cs_3dv2.body;

        var Table3DSV2_head = document.createElement("thead");
        Table3DSV2_head.appendChild(generate_header());
        var Table3DSV2_plus = generate_plus()
        var Table3DSV2_body = genereate_body();
        var Table3DSV2 = document.createElement("table");
        Table3DSV2.className = "table";
        Table3DSV2.appendChild(Table3DSV2_head);
        Table3DSV2.appendChild(Table3DSV2_body);
        Table3DSV2.appendChild(Table3DSV2_plus);
        // display the render
        var table_render = document.getElementById("tableRender")
        var table_render_parent = table_render.parentNode
        table_render_parent.replaceChild(Table3DSV2, table_render);
    }

    let ref_index = 0;
    let row_index = 0;
    let cs_3dsv2_header = [];
    let cs_3dsv2_states = [];
    let cs_3dsv2_body = [];


    init_render_tab();

    /**
     * Custom event
     */
    function add_event_input_number(input) {
        input.addEventListener('input', function (e) {
            var value = e.target.value;
            var t = 0;

            // if amount is with "," instead of "."
            var newValue = value.replace(/[^\d]/g, function (match) {
                if (match === "." || match === ",") {
                    t++;
                    return (t >= 2) ? "" : ".";
                }
                return "";
            });

            // if decimal amount is more than the maximal
            var newValueLength = newValue.length;
            var separatorPosition = newValue.indexOf(".");
            if (separatorPosition !== -1) {
                var lastPart = newValue.slice(separatorPosition + 1);
                var lastPartLength = lastPart.length;
                if (lastPartLength > 2) {
                    newValue = newValue.substring(0, newValueLength - lastPartLength + 2);
                }
            }

            input.value = newValue;
        })
        return input;
    }

    document.getElementById("addMoreRow").addEventListener('click', (e) => {
        e.preventDefault();
        row_index++;
        ref_index++;

        var h_tr = document.createElement("tr");

        var th_td = document.createElement("th");
        th_td.setAttribute('scope', 'row');
        th_td.className = "3dsv2Index"
        th_td.appendChild(document.createTextNode(ref_index))
        h_tr.appendChild(th_td)

        var amount_td = document.createElement("td");

        var btn_amount_td = document.createElement("input");
        btn_amount_td.setAttribute('type', 'text');
        btn_amount_td.setAttribute('name', "cs_3dsv2_otps[" + row_index + "][amount]");
        btn_amount_td.setAttribute('step', '0.01')
        btn_amount_td.setAttribute('min', '0.01');
        btn_amount_td.className = "form-control";
        btn_amount_td = add_event_input_number(btn_amount_td);
        amount_td.appendChild(btn_amount_td);

        h_tr.appendChild(amount_td)

        var state_td = document.createElement("td");
        var select_state_td = generate_select(null, row_index);
        state_td.appendChild(select_state_td)
        h_tr.appendChild(state_td)

        var delete_td = document.createElement("td");
        delete_td.appendChild(generate_delete_btn(h_tr))

        h_tr.appendChild(delete_td)
        // append to tbody
        document.getElementById("3dsv2TableBody").appendChild(h_tr);
    });

    function enableLangSelect() {
        document.getElementById('code_site_langue').removeAttribute('readonly');
        document.getElementById('code_site_langue').style.pointerEvents = '';
    }

    function disableLangSelect() {
        document.getElementById('code_site_langue').setAttribute('readonly', '');
        document.getElementById('code_site_langue').style.pointerEvents = 'none';
        document.getElementById('code_site_langue').value = 'FR';
    }

    function enableCurrencySelect() {
        document.getElementById('code_site_devises[]').value = '';
        document.getElementById('code_site_devises[]').removeAttribute('readonly');
        document.getElementById('code_site_devises[]').style.pointerEvents = '';
    }

    function disableCurrencySelect() {
        document.getElementById('code_site_devises[]').setAttribute('readonly', '');
        document.getElementById('code_site_devises[]').style.pointerEvents = 'none';
        document.getElementById('code_site_devises[]').value = 'EUR';
    }

    function showDisplayLogoSwitch(){
        document.getElementById('code_site_cards_logo_opt_on').parentNode.parentNode.parentNode.classList.remove('hide');
        document.getElementById('code_site_cards_logo_opt_off').removeAttribute('checked');
        document.getElementById('code_site_cards_logo_opt_on').setAttribute('checked', 'checked');
    }

    /**
     * Custom init code site inputs
     */
    if (document.getElementById('code_site_payment_method').value !== 'cb') {

        document.getElementById('code_site_express_payment_on').parentNode.parentNode.parentNode.classList.add('hide');
        document.getElementById('3dsv2TableBody').parentNode.parentNode.parentNode.classList.add('hide');
        document.getElementById('code_site_capture_event_opt').parentNode.parentNode.classList.add('hide');
        document.getElementById('code_site_payment_display_method').parentNode.parentNode.classList.add('hide');
        document.getElementById('code_site_additional_cards_logos[]').parentNode.parentNode.classList.add('hide');

        if (cofidis_protocols && cofidis_protocols.includes(document.getElementById('code_site_payment_method').value)) {
            document.getElementById('code_site_display_name').setAttribute('readonly', '');
            document.getElementById('code_site_langue').setAttribute('readonly', '');
            document.getElementById('code_site_langue').style.pointerEvents = 'none';

        } else if (document.getElementById('code_site_payment_method').value === 'ipa') {
            document.getElementById('code_site_cards_logo_opt_on').parentNode.parentNode.parentNode.classList.add('hide');
            disableCurrencySelect()
            disableLangSelect();
        }
    }

    document.getElementById('code_site_payment_method').addEventListener('input', function (e) {
        var value = e.target.value;
        if (value !== 'cb') {
            document.getElementById('code_site_express_payment_on').parentNode.parentNode.parentNode.classList.add('hide');
            document.getElementById('3dsv2TableBody').parentNode.parentNode.parentNode.classList.add('hide');
            document.getElementById('code_site_capture_event_opt').parentNode.parentNode.classList.add('hide');
            document.getElementById('code_site_payment_display_method').parentNode.parentNode.classList.add('hide');
            document.getElementById('code_site_additional_cards_logos[]').parentNode.parentNode.classList.add('hide');

            if (document.getElementById('code_site_payment_method').value === 'ipa') {
                document.getElementById('code_site_cards_logo_opt_on').removeAttribute('checked');
                document.getElementById('code_site_cards_logo_opt_off').setAttribute('checked', 'checked');
                document.getElementById('code_site_cards_logo_opt_on').parentNode.parentNode.parentNode.classList.add('hide');
                document.getElementById('code_site_display_name').value = "";
                document.getElementById('code_site_display_name').removeAttribute('readonly');
                disableCurrencySelect();
                disableLangSelect();

            } else if (cofidis_protocols && cofidis_protocols.includes(value)) {
                document.getElementById('code_site_display_name').value = cofidis_protocol_names[value]
                document.getElementById('code_site_display_name').setAttribute('readonly', '');
                showDisplayLogoSwitch();
                disableLangSelect();
                enableCurrencySelect();

            } else {
                document.getElementById('code_site_display_name').value = "";
                document.getElementById('code_site_display_name').removeAttribute('readonly');
                showDisplayLogoSwitch();
                enableCurrencySelect();
                enableLangSelect();
            }
        } else {
            document.getElementById('code_site_express_payment_on').parentNode.parentNode.parentNode.classList.remove('hide');
            document.getElementById('3dsv2TableBody').parentNode.parentNode.parentNode.classList.remove('hide');
            document.getElementById('code_site_capture_event_opt').parentNode.parentNode.classList.remove('hide');
            document.getElementById('code_site_payment_display_method').parentNode.parentNode.classList.remove('hide');
            document.getElementById('code_site_additional_cards_logos[]').parentNode.parentNode.classList.remove('hide');
            document.getElementById('code_site_display_name').value = "";
            document.getElementById('code_site_display_name').removeAttribute('readonly');
            showDisplayLogoSwitch();
            enableCurrencySelect();
            enableLangSelect();
        }
    });

    /**
     * Custom init code site amount option
     */
    document.getElementById('code_site_amount_option_off').addEventListener('click', function (e) {
        document.getElementById('code_site_amount_min').parentNode.parentNode.classList.add('hide');
        document.getElementById('code_site_amount_max').parentNode.parentNode.classList.add('hide');
    });

    document.getElementById('code_site_amount_option_on').addEventListener('click', function (e) {
        document.getElementById('code_site_amount_min').parentNode.parentNode.classList.remove('hide');
        document.getElementById('code_site_amount_max').parentNode.parentNode.classList.remove('hide');
    });

    /**
     *
     * Custom saved code site name when display method changed
     */
    let code_site_payment_display_method_deported_value = '';
    let code_site_payment_display_method_hostedfields_value = '';

    document.getElementById('code_site_payment_display_method').addEventListener('change', function (e) {
        var value = e.target.value;
        if (value === "hostedfields") {
            code_site_payment_display_method_deported_value = document.getElementById('code_site_name').value;
            document.getElementById('code_site_name').value = code_site_payment_display_method_hostedfields_value;
        } else {
            code_site_payment_display_method_hostedfields_value = document.getElementById('code_site_name').value;
            document.getElementById('code_site_name').value = code_site_payment_display_method_deported_value;
        }
    })

    function init_code_site_inputs() {
        if (document.getElementById('code_site_amount_option_off').checked) {
            document.getElementById('code_site_amount_min').parentNode.parentNode.classList.add('hide');
            document.getElementById('code_site_amount_max').parentNode.parentNode.classList.add('hide');
        }

        if (document.getElementById('code_site_payment_method').value !== 'cb') {
            document.getElementById('code_site_express_payment_on').parentNode.parentNode.parentNode.classList.add('hide');
            document.getElementById('3dsv2TableBody').parentNode.parentNode.parentNode.classList.add('hide');
            document.getElementById('code_site_capture_event_opt').parentNode.parentNode.classList.add('hide');
            document.getElementById('code_site_additional_cards_logos[]').parentNode.parentNode.classList.add('hide');
        }
    }

    var display_value = document.getElementById('code_site_payment_display_method').value;
    document.getElementById(display_value + '_desc').style.display = 'block';

    init_code_site_inputs();
};