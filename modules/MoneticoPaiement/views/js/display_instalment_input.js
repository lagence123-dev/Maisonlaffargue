/**
 * Display the instalment inputs
 */
window.onload = function () {

    var instalment_term_nb = 0;

    // function for check if etp is in instalment mode
    function display_etp_instalment_input() {
        document.addEventListener('input', function (e) {
            if (e.target.id === 'ept_payment_mode'){
                if (e.target.value === 'INSTALMENT') {
                    show_current_input();
                } else {
                    hide_all_input();
                }
            }
        });
    }

    // function for display or not the term nb and term pct
    function display_ept_instalment_event() {
        document.getElementById('ept_instalment_default_on').addEventListener('input', function (e) {
            toggle_all_input();
        });

        document.getElementById('ept_instalment_default_off').addEventListener('input', function (e) {
            toggle_all_input();
        });

    }

    // function for display or not term pct by term nb
    function display_ept_instalment_pct_event() {
        document.getElementById('ept_instalment_term_nb').addEventListener('input', function (e) {

            var value = Number(e.target.value);
            var pct_number = Math.min(value, instalment_term_nb) + 1;
            var max = Math.max(value, instalment_term_nb);

            for (pct_number; pct_number <= max; pct_number++) {
                document.getElementById('ept_instalment_pct_' + pct_number).parentNode.parentNode.classList.toggle('hide');
            }

            instalment_term_nb = value;
        });
    }



    // sub function used
    function hide_all_input(){
        document.getElementById('ept_instalment_default_on').parentNode.parentNode.parentNode.classList.add('hide');
        document.getElementById('ept_instalment_term_nb').parentNode.parentNode.classList.add('hide');
        document.getElementById('ept_instalment_pct_1').parentNode.parentNode.classList.add('hide');
        document.getElementById('ept_instalment_pct_2').parentNode.parentNode.classList.add('hide');
        document.getElementById('ept_instalment_pct_3').parentNode.parentNode.classList.add('hide');
        document.getElementById('ept_instalment_pct_4').parentNode.parentNode.classList.add('hide');
    }
    function show_current_input(){
        document.getElementById('ept_instalment_default_on').parentNode.parentNode.parentNode.classList.remove('hide');
        instalment_term_nb = Number(document.getElementById('ept_instalment_term_nb').value);
        // if etp default mode is = 0
        if (document.getElementById('ept_instalment_default_off').checked) {
            document.getElementById('ept_instalment_term_nb').parentNode.parentNode.classList.remove('hide');
            for (var pct_number = 1; pct_number <= instalment_term_nb; pct_number++) {
                document.getElementById('ept_instalment_pct_' + pct_number).parentNode.parentNode.classList.toggle('hide')
            }
        }
    }
    function toggle_all_input() {
        document.getElementById('ept_instalment_term_nb').parentNode.parentNode.classList.toggle('hide');
        for (var pct_number = 1; pct_number <= instalment_term_nb; pct_number++) {
            document.getElementById('ept_instalment_pct_' + pct_number).parentNode.parentNode.classList.toggle('hide')
        }

    }

    // init function
    function init() {

        hide_all_input();
        // if etp mode is splited
        if (document.getElementById('ept_payment_mode').value === 'INSTALMENT') {
            show_current_input();
        }

        display_etp_instalment_input();
        display_ept_instalment_event();
        display_ept_instalment_pct_event();
    }

    init()
}