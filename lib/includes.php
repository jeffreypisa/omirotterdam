<?php
function add_theme_scripts() {
    wp_enqueue_style( 'styles', get_template_directory_uri() . '/assets/css/main.css');
    wp_enqueue_style( 'verhalenatlas-fixes', get_template_directory_uri() . '/assets/css/verhalenatlas-fixes.css', array( 'styles' ) );

    $custom_css = '
    .woocommerce-cart .omi-continue-shopping-wrap {
        margin: 0 0 1rem;
    }

    .mec-booking-form-container .omi-booking-datetime {
        margin: 0 0 1rem;
        font-weight: 600;
    }
    ';
    wp_add_inline_style( 'styles', $custom_css );

    wp_enqueue_script( 'script', get_template_directory_uri() . '/assets/js/main-min.js', array ( 'jquery' ), 1.1, true);

    $custom_js = "(function(){\n"
        . "  function formatMecRange(rawValue){\n"
        . "    if(!rawValue){ return ''; }\n"
        . "    var parts = String(rawValue).split(':');\n"
        . "    if(parts.length < 2){ return ''; }\n"
        . "    var start = parseInt(parts[0], 10);\n"
        . "    var end = parseInt(parts[1], 10);\n"
        . "    if(!start || !end){ return ''; }\n"
        . "    var startDate = new Date(start * 1000);\n"
        . "    var endDate = new Date(end * 1000);\n"
        . "    var dateText = startDate.toLocaleDateString('nl-NL', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric', timeZone: 'Europe/Amsterdam' });\n"
        . "    var startTime = startDate.toLocaleTimeString('nl-NL', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'Europe/Amsterdam' });\n"
        . "    var endTime = endDate.toLocaleTimeString('nl-NL', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'Europe/Amsterdam' });\n"
        . "    return dateText + ' ' + startTime + ' - ' + endTime;\n"
        . "  }\n"
        . "  function ensureMecBookingDateTime(){\n"
        . "    var forms = document.querySelectorAll('form.mec-booking-form-container');\n"
        . "    if(!forms.length){ return; }\n"
        . "    forms.forEach(function(form){\n"
        . "      var stepInput = form.querySelector('input[name=\"step\"]');\n"
        . "      if(!stepInput || String(stepInput.value) !== '2'){ return; }\n"
        . "      var dateInput = form.querySelector('input[name=\"book[date]\"]');\n"
        . "      if(!dateInput){ return; }\n"
        . "      var label = form.querySelector('.omi-booking-datetime');\n"
        . "      if(!label){\n"
        . "        label = document.createElement('div');\n"
        . "        label.className = 'omi-booking-datetime';\n"
        . "        var subtotal = form.querySelector('.mec-ticket-subtotal-wrapper');\n"
        . "        if(subtotal && subtotal.parentNode){\n"
        . "          subtotal.parentNode.insertBefore(label, subtotal);\n"
        . "        } else {\n"
        . "          form.insertBefore(label, form.firstChild);\n"
        . "        }\n"
        . "      }\n"
        . "      var text = formatMecRange(dateInput.value);\n"
        . "      if(text){\n"
        . "        label.textContent = 'Gekozen datum en tijd: ' + text;\n"
        . "      }\n"
        . "    });\n"
        . "  }\n"
        . "  function updateMecLabels(){\n"
        . "    var labels = document.querySelectorAll('.mec-select-date-label');\n"
        . "    if(!labels.length){ return; }\n"
        . "    labels.forEach(function(el){ el.textContent = 'Selecteer tijdslot'; });\n"
        . "  }\n"
        . "  function runMecUiFixes(){\n"
        . "    updateMecLabels();\n"
        . "    ensureMecBookingDateTime();\n"
        . "  }\n"
        . "  if(document.readyState === 'loading'){\n"
        . "    document.addEventListener('DOMContentLoaded', runMecUiFixes);\n"
        . "  } else {\n"
        . "    runMecUiFixes();\n"
        . "  }\n"
        . "  document.addEventListener('click', function(){ setTimeout(runMecUiFixes, 150); setTimeout(runMecUiFixes, 800); }, true);\n"
        . "  if(window.jQuery){\n"
        . "    window.jQuery(document).ajaxComplete(function(){ setTimeout(runMecUiFixes, 50); });\n"
        . "  }\n"
        . "  if('MutationObserver' in window){\n"
        . "    var observer = new MutationObserver(function(mutations){\n"
        . "      var shouldRun = mutations.some(function(mutation){\n"
        . "        return Array.prototype.some.call(mutation.addedNodes || [], function(node){\n"
        . "          return node.nodeType === 1 && (node.matches && node.matches('.mec-booking-form-container, .mec-booking, .mec-events-meta-group-booking') || node.querySelector && node.querySelector('.mec-booking-form-container'));\n"
        . "        });\n"
        . "      });\n"
        . "      if(shouldRun){ setTimeout(runMecUiFixes, 50); }\n"
        . "    });\n"
        . "    observer.observe(document.body, { childList: true, subtree: true });\n"
        . "  }\n"
        . "  var attempts = 0;\n"
        . "  var retry = window.setInterval(function(){\n"
        . "    attempts += 1;\n"
        . "    runMecUiFixes();\n"
        . "    if(attempts >= 20){ window.clearInterval(retry); }\n"
        . "  }, 500);\n"
        . "})();";
    wp_add_inline_script( 'script', $custom_js, 'after' );
}

add_action( 'wp_enqueue_scripts', 'add_theme_scripts' );
?>
