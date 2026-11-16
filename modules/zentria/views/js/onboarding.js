/**
*  Copyright 2025 Zentria
*
*  @author    Zentria <zentria@profileo.com>
*  @copyright 2025 Zentria
*  @license   Commercial
*  link      https://zentria.profileo.com
*/

document.addEventListener("DOMContentLoaded", function() {
    const skipStepBtn = document.querySelectorAll('.skip_step');
    
    /***********************************************************************************************************************************************
    * EVENTS
    ***********************************************************************************************************************************************/
    
    // Skip step buttons
    skipStepBtn && skipStepBtn.forEach(btn => btn.addEventListener('click', skipSteps));
    
    /***********************************************************************************************************************************************
    * ACTION FUNCTIONS
    ***********************************************************************************************************************************************/
    
    // Skip steps on button click
    function skipSteps(e) {
        // Get current step item
        let stepItem = this.closest('.step-item');
        let stepContent = stepItem.querySelector('.step-content');
        let stepIcon = stepItem.querySelector('.step-item-icon i');
        stepIcon.setAttribute('class', 'icon icon-onboarding-step-succeed');
        stepContent.classList.toggle('inactive_step');
        stepItem.classList.toggle('align-items-center');
        
        // Get next step item
        let nextStepItem = stepItem.nextElementSibling;
        let nextStepContent = nextStepItem.querySelector('.step-content');
        nextStepContent.classList.toggle('inactive_step');
        nextStepItem.classList.toggle('align-items-center');
        
        // If last step item
        if (nextStepItem.classList.contains('last-step')) {
            let nextStepIcon = nextStepItem.querySelector('.step-item-icon i');
            nextStepIcon.setAttribute('class', 'icon icon-onboarding-step-succeed');
        }
        
        // Validate onboarding with ajax query
        if (e.target.id == 'validate_onboarding' || e.target.id == 'validate_onboarding_ignore') {
            const url = this.getAttribute('data-url-validate');
            const commonParamsObj = {
                action: 'validateOnboarding',
                ajax: 1,
            };
            const commonParams = new URLSearchParams(commonParamsObj).toString();
            
            fetch(url,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: commonParams,
                }
            );
        }
    }
});