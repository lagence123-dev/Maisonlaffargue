// Prevent multiple click on submit btn to save data only once
document.onready = function () {

    let click_count = 0;

    document.getElementById('configuration_form_submit_btn').addEventListener('click', (e) => {
        if(++click_count > 1){
            e.preventDefault()
        }
    })

}