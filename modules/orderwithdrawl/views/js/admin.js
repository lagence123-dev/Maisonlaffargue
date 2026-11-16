/*
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 * @author    sjmedia GmbH
 * @copyright 2024-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */

/**
 * Back-office enhancements for the OrderWithdrawl module.
 *
 * Covers the extras that were added in Batch 6:
 *   - UX-A-M1: AJAX-backed mail preview and test-send.
 *   - UX-A-L1: confirm-gated reset-to-defaults action.
 *   - UX-A-M4: inline validation feedback next to required fields.
 *   - UX-A-M6: language-tab switch that preserves hidden form values.
 *
 * The core tab switcher and colour synchronisation still live inline in
 * configure.tpl so they keep working for sites that cache the legacy
 * asset. This file is additive only.
 */
(function () {
  'use strict';

  function ready(fn) {
    if (document.readyState !== 'loading') {
      fn();
    } else {
      document.addEventListener('DOMContentLoaded', fn);
    }
  }

  /* ---------- UX: compact help text as label tooltips ----------------- */
  function setupHelpTooltips() {
    var groups = document.querySelectorAll('.ow-form__group');
    var generatedId = 0;

    function getDirectHelp(group) {
      for (var i = 0; i < group.children.length; i += 1) {
        if (group.children[i].classList && group.children[i].classList.contains('ow-help')) {
          return group.children[i];
        }
      }

      return null;
    }

    Array.prototype.forEach.call(groups, function (group) {
      var label = group.querySelector('.ow-label');
      var help = getDirectHelp(group);
      if (!label || !help || label.querySelector('.ow-help-tooltip')) {
        return;
      }

      if (!help.id) {
        generatedId += 1;
        help.id = 'ow-help-source-' + generatedId;
      }

      var tooltip = document.createElement('span');
      tooltip.className = 'ow-help-tooltip';
      tooltip.setAttribute('tabindex', '0');
      tooltip.setAttribute('aria-describedby', help.id);

      var icon = document.createElement('span');
      icon.className = 'ow-help-tooltip__icon';
      icon.setAttribute('aria-hidden', 'true');
      icon.textContent = '?';

      var content = document.createElement('span');
      content.className = 'ow-help-tooltip__content';
      content.setAttribute('role', 'tooltip');
      content.innerHTML = help.innerHTML;

      tooltip.appendChild(icon);
      tooltip.appendChild(content);

      var requiredMarker = label.querySelector('.ow-required');
      if (requiredMarker) {
        label.insertBefore(requiredMarker, label.firstChild);
      }
      label.appendChild(tooltip);
      help.classList.add('ow-help--source');
    });
  }

  /* ---------- UX-A-M4: inline required-field validation --------------- */
  function setupInlineValidation() {
    var form = document.getElementById('module_form');
    if (!form) {
      return;
    }

    var requiredFields = form.querySelectorAll('.js-ow-required, [aria-required="true"]');

    function clearError(field) {
      field.classList.remove('is-invalid');
      var errorNode = form.querySelector('[data-ow-error-for="' + field.id + '"]');
      if (errorNode) {
        errorNode.classList.remove('is-visible');
        errorNode.textContent = '';
      }
    }

    function showError(field, message) {
      field.classList.add('is-invalid');
      var errorNode = form.querySelector('[data-ow-error-for="' + field.id + '"]');
      if (errorNode) {
        errorNode.textContent = message;
        errorNode.classList.add('is-visible');
      }
    }

    Array.prototype.forEach.call(requiredFields, function (field) {
      field.addEventListener('invalid', function (event) {
        event.preventDefault();
        showError(field, field.validationMessage || '');
      });
      field.addEventListener('input', function () {
        if (field.checkValidity()) {
          clearError(field);
        }
      });
      field.addEventListener('change', function () {
        if (field.checkValidity()) {
          clearError(field);
        }
      });
    });

    form.addEventListener('submit', function () {
      // Clear previous errors so a failed re-submit re-evaluates cleanly.
      Array.prototype.forEach.call(requiredFields, clearError);
    });
  }

  /* ---------- UX-A-L1: reset-to-defaults confirmation ----------------- */
  function setupResetButton() {
    var trigger = document.querySelector('[data-ow-reset-trigger]');
    var form = document.getElementById('orderwithdrawl-reset-form');
    if (!trigger || !form) {
      return;
    }

    trigger.addEventListener('click', function () {
      var msg = trigger.getAttribute('data-confirm-message') ||
        'Reset all module settings to defaults?';
      if (window.confirm(msg)) {
        form.submit();
      }
    });
  }

  /* ---------- UX-A-M6: language tabs keep hidden inputs in the DOM ---- */
  // The Smarty template already outputs every language pane in the DOM;
  // the inline tab-switch script toggles the is-active class and CSS
  // hides inactive panes via display:none without disabling the fields.
  // This hardening pass guarantees:
  //   1. tab buttons never act as form submit buttons (explicit type=button),
  //   2. inputs inside inactive panes keep name+value and stay enabled so
  //      the browser serialises them alongside the active pane.
  function setupLanguageTabs() {
    var containers = document.querySelectorAll('[data-ow-lang-tabs]');
    Array.prototype.forEach.call(containers, function (container) {
      var tabs = container.querySelectorAll('[data-ow-lang-tab]');
      Array.prototype.forEach.call(tabs, function (tab) {
        if (!tab.hasAttribute('type')) {
          tab.setAttribute('type', 'button');
        }
      });

      var panes = container.querySelectorAll('[data-ow-lang-pane]');
      Array.prototype.forEach.call(panes, function (pane) {
        var inputs = pane.querySelectorAll('input, textarea, select');
        Array.prototype.forEach.call(inputs, function (field) {
          // Explicitly clear any disabled attribute that a stylesheet or
          // previous admin module may have set; without this the browser
          // would skip the value on submit.
          field.disabled = false;
        });
      });
    });
  }

  /* ---------- UX-A-M1: mail preview & test send ----------------------- */
  function setupMailPreview() {
    var root = document.querySelector('[data-ow-mail-preview]');
    if (!root) {
      return;
    }

    var templateSelect = root.querySelector('[data-ow-mail-preview-template]');
    var languageSelect = root.querySelector('[data-ow-mail-preview-language]');
    var refreshButton = root.querySelector('[data-ow-mail-preview-refresh]');
    var sendButton = root.querySelector('[data-ow-mail-preview-send]');
    var frame = root.querySelector('[data-ow-mail-preview-frame]');
    var status = root.querySelector('[data-ow-mail-preview-status]');
    var endpointInput = root.querySelector('[data-ow-mail-preview-endpoint]');
    if (!templateSelect || !languageSelect || !frame || !endpointInput) {
      return;
    }

    var endpointBase = endpointInput.value || '';

    function setStatus(message, kind) {
      if (!status) {
        return;
      }
      status.textContent = message || '';
      status.classList.remove('ow-mail-preview__status--success', 'ow-mail-preview__status--error');
      if (kind === 'success') {
        status.classList.add('ow-mail-preview__status--success');
      } else if (kind === 'error') {
        status.classList.add('ow-mail-preview__status--error');
      }
    }

    function buildUrl(action) {
      var separator = endpointBase.indexOf('?') === -1 ? '?' : '&';
      var params = [
        'orderwithdrawl_action=' + encodeURIComponent(action),
        'template=' + encodeURIComponent(templateSelect.value),
        'iso=' + encodeURIComponent(languageSelect.value)
      ];
      return endpointBase + separator + params.join('&');
    }

    function loadPreview() {
      if (!templateSelect.value || !languageSelect.value) {
        return;
      }
      setStatus('Loading preview...', '');
      var url = buildUrl('preview_mail');
      // Use fetch() so we can handle HTTP errors cleanly; fall back to
      // setting frame.src if fetch is unavailable.
      if (typeof window.fetch !== 'function') {
        frame.src = url;
        setStatus('', '');
        return;
      }

      fetch(url, { credentials: 'same-origin' })
        .then(function (response) {
          return response.text().then(function (body) {
            if (!response.ok) {
              setStatus(body || ('HTTP ' + response.status), 'error');
              frame.setAttribute('srcdoc', '<p style="font-family:sans-serif">' + (body || 'Error') + '</p>');
              return;
            }
            frame.setAttribute('srcdoc', body);
            setStatus('Preview updated.', 'success');
          });
        })
        .catch(function () {
          setStatus('Network error while loading the preview.', 'error');
        });
    }

    function sendTest() {
      if (!sendButton) {
        return;
      }
      sendButton.disabled = true;
      setStatus('Sending test mail...', '');
      var url = buildUrl('send_test_mail');
      fetch(url, { method: 'POST', credentials: 'same-origin' })
        .then(function (response) {
          return response.json().then(function (payload) {
            if (!response.ok || !payload || !payload.success) {
              setStatus((payload && payload.message) || ('HTTP ' + response.status), 'error');
              return;
            }
            setStatus(payload.message || 'Test mail sent.', 'success');
          });
        })
        .catch(function () {
          setStatus('Network error while sending the test mail.', 'error');
        })
        .then(function () {
          sendButton.disabled = false;
        });
    }

    templateSelect.addEventListener('change', loadPreview);
    languageSelect.addEventListener('change', loadPreview);
    if (refreshButton) {
      refreshButton.addEventListener('click', loadPreview);
    }
    if (sendButton) {
      sendButton.addEventListener('click', sendTest);
    }

    // Initial preview on first activation of the tab so the frame is not
    // blank when the admin reaches it.
    var tabTrigger = document.getElementById('ow-tab-mailpreview-trigger');
    if (tabTrigger) {
      tabTrigger.addEventListener('click', function () {
        if (frame.getAttribute('srcdoc')) {
          return;
        }
        loadPreview();
      });
    }
  }

  /* ---------- Product/category exclusion picker ---------------------- */
  function setupExclusionPickers() {
    var productRoot = document.querySelector('[data-ow-exclusion-picker="products"]');
    var categoryRoot = document.querySelector('[data-ow-exclusion-picker="categories"]');

    function normaliseIds(value) {
      var seen = {};
      return String(value || '')
        .split(/[,\s]+/)
        .map(function (raw) { return parseInt(raw, 10); })
        .filter(function (id) {
          if (!id || seen[id]) {
            return false;
          }
          seen[id] = true;
          return true;
        });
    }

    function dispatchChange(input) {
      var event;
      if (!input) {
        return;
      }
      if (document.createEvent) {
        event = document.createEvent('HTMLEvents');
        event.initEvent('change', true, false);
        input.dispatchEvent(event);
        return;
      }
      if (typeof input.fireEvent === 'function') {
        input.fireEvent('onchange');
      }
    }

    function writeIds(input, ids) {
      if (!input) {
        return;
      }
      input.value = ids.join(',');
      dispatchChange(input);
    }

    function findChip(target) {
      while (target && target !== document) {
        if (target.getAttribute && target.getAttribute('data-ow-chip-id')) {
          return target;
        }
        target = target.parentNode;
      }

      return null;
    }

    function clearNode(node) {
      while (node && node.firstChild) {
        node.removeChild(node.firstChild);
      }
    }

    if (productRoot) {
      var search = productRoot.querySelector('[data-ow-product-search]');
      var resultBox = productRoot.querySelector('[data-ow-product-results]');
      var chips = productRoot.querySelector('[data-ow-product-chips]');
      var hidden = productRoot.querySelector('[data-ow-product-value]');
      var manual = document.querySelector('[data-ow-product-manual]');
      var endpoint = search ? search.getAttribute('data-endpoint') : '';
      var selected = normaliseIds(hidden ? hidden.value : '');
      var lastQuery = '';
      var timer = null;

      function syncProducts() {
        writeIds(hidden, selected);
        if (manual) {
          manual.value = selected.join(',');
        }
      }

      function appendChip(id, label) {
        var chip;
        var labelNode;
        var removeNode;
        if (!chips) {
          return;
        }
        chip = document.createElement('button');
        chip.type = 'button';
        chip.className = 'ow-chip';
        chip.setAttribute('data-ow-chip-id', String(id));
        labelNode = document.createElement('span');
        labelNode.textContent = label || ('#' + id);
        removeNode = document.createElement('span');
        removeNode.setAttribute('aria-hidden', 'true');
        removeNode.textContent = '×';
        chip.appendChild(labelNode);
        chip.appendChild(removeNode);
        chips.appendChild(chip);
      }

      function addChip(id, label) {
        if (!chips || selected.indexOf(id) !== -1) {
          return;
        }
        selected.push(id);
        appendChip(id, label);
        syncProducts();
      }

      function rebuildManualProductChips() {
        if (!chips) {
          return;
        }
        clearNode(chips);
        Array.prototype.forEach.call(selected, function (id) {
          appendChip(id, '#' + id);
        });
      }

      if (chips) {
        chips.addEventListener('click', function (event) {
          var chip = findChip(event.target);
          var id;
          if (!chip) {
            return;
          }
          id = parseInt(chip.getAttribute('data-ow-chip-id'), 10);
          selected = selected.filter(function (current) { return current !== id; });
          chip.parentNode.removeChild(chip);
          syncProducts();
        });
      }

      if (search && resultBox && endpoint && typeof window.fetch === 'function') {
        search.addEventListener('input', function () {
          var query = search.value.replace(/^\s+|\s+$/g, '');
          clearTimeout(timer);
          if (query.length < 2) {
            clearNode(resultBox);
            return;
          }
          timer = setTimeout(function () {
            var separator;
            var url;
            if (query === lastQuery) {
              return;
            }
            lastQuery = query;
            separator = endpoint.indexOf('?') === -1 ? '?' : '&';
            url = endpoint + separator + 'orderwithdrawl_action=search_excluded_products&q=' + encodeURIComponent(query);
            fetch(url, { credentials: 'same-origin' })
              .then(function (response) {
                return response.json();
              })
              .then(function (payload) {
                clearNode(resultBox);
                Array.prototype.forEach.call(payload && payload.items ? payload.items : [], function (item) {
                  var button = document.createElement('button');
                  var id = parseInt(item.id, 10);
                  if (!id || selected.indexOf(id) !== -1) {
                    return;
                  }
                  button.type = 'button';
                  button.className = 'ow-picker__result';
                  button.textContent = item.label + (item.reference ? ' (' + item.reference + ')' : '');
                  button.addEventListener('click', function () {
                    addChip(id, item.label);
                    clearNode(resultBox);
                    search.value = '';
                    lastQuery = '';
                  });
                  resultBox.appendChild(button);
                });
              })
              .catch(function () {
                clearNode(resultBox);
              });
          }, 250);
        });
      }

      if (manual) {
        manual.addEventListener('change', function () {
          selected = normaliseIds(manual.value);
          writeIds(hidden, selected);
          rebuildManualProductChips();
        });
      }
    }

    if (categoryRoot) {
      var categoryHidden = categoryRoot.querySelector('[data-ow-category-value]');
      var categoryManual = document.querySelector('[data-ow-category-manual]');
      var categoryFilter = categoryRoot.querySelector('[data-ow-category-filter]');
      var checkboxes = categoryRoot.querySelectorAll('[data-ow-category-checkbox]');

      function syncCategoriesFromTree() {
        var ids = [];
        Array.prototype.forEach.call(checkboxes, function (box) {
          if (box.checked) {
            ids.push(parseInt(box.value, 10));
          }
        });
        writeIds(categoryHidden, ids);
        if (categoryManual) {
          categoryManual.value = ids.join(',');
        }
      }

      Array.prototype.forEach.call(checkboxes, function (box) {
        box.addEventListener('change', syncCategoriesFromTree);
      });

      if (categoryManual) {
        categoryManual.addEventListener('change', function () {
          var selectedCategories = normaliseIds(categoryManual.value);
          Array.prototype.forEach.call(checkboxes, function (box) {
            box.checked = selectedCategories.indexOf(parseInt(box.value, 10)) !== -1;
          });
          writeIds(categoryHidden, selectedCategories);
        });
      }

      if (categoryFilter) {
        categoryFilter.addEventListener('input', function () {
          var query = categoryFilter.value.replace(/^\s+|\s+$/g, '').toLowerCase();
          Array.prototype.forEach.call(checkboxes, function (box) {
            var row = box.parentNode;
            var text = row ? row.textContent.toLowerCase() : '';
            if (row) {
              row.style.display = !query || text.indexOf(query) !== -1 ? '' : 'none';
            }
          });
        });
      }
    }
  }

  ready(function () {
    setupHelpTooltips();
    setupInlineValidation();
    setupResetButton();
    setupLanguageTabs();
    setupMailPreview();
    setupExclusionPickers();
  });
})();
