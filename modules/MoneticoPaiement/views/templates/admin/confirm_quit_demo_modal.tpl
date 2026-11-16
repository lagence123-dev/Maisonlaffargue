<!-- Modal -->
<div class="modal fade" id="MoneticoPaiementQuitDemoModal" tabindex="-1" role="dialog" aria-labelledby="#MoneticoPaiementQuitDemoModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="MoneticoPaiementQuitDemoModalLabel">{$modal_title}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    {$modal_text}
                </p>
                {foreach from=$modal_explanation item=$line key=$key}
                    {if ($key eq 0 and $count_demo_epts neq 0) or ($key eq 1 and $count_demo_orders neq 0) or $key > 1}
                        <p><em>{$line}</em></p>
                    {/if}
                {/foreach}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancel-quit-btn"
                        data-dismiss="modal">{$modal_close_text}</button>
                <button type="button" class="btn btn-primary" id="confirm-quit-btn" onclick="deactivateDemoMode()">{$modal_confirm_text}</button>
            </div>
        </div>
    </div>
</div>