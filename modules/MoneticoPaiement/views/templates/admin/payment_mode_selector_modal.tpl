<!-- Modal -->
<div class="modal fade" id="MoneticoPaiementPaymentModeModal" tabindex="-1" role="dialog" aria-labelledby="MoneticoPaiementPaymentModeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex align-content-center">
                <h5 class="modal-title" id="MoneticoPaiementPaymentModeModalLabel">{$modal_title}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {if $can_add_other_demo_ept}
                    <p>{$modal_text}</p>
                    <select id="payment_modes_select" name="payment_modes" class="form-select" size={$dropdownlist_size} multiple aria-label="multiple select">
                        {foreach from=$payment_modes key=$key item=$payment_mode name=pm}
                            {if !$key|@in_array:$created_ept}
                                <option value={$key}>{$payment_mode}</option>
                            {/if}
                        {/foreach}
                    </select>
                {else}
                    <p>{$modal_all_ept_created}</p>
                {/if}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{$modal_close_text}</button>
                {if $can_add_other_demo_ept}
                    <button id="create-data-btn" type="button" class="btn btn-primary" onclick="createDemoData()">{$modal_save_text}</button>
                {/if}
            </div>
        </div>
    </div>
</div>