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

    .mec-events-meta-group-booking .mec-booking-form-container > h4 {
        font-size: 28px;
        line-height: 1.2;
        font-weight: 700;
        letter-spacing: 0;
        text-transform: lowercase;
    }

    .mec-events-meta-group-booking .mec-select-date-label::after {
        content: " *";
        color: #ff6c2f;
    }

    .mec-events-meta-group-booking .mec-ticket-name {
        color: #000;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.3;
        text-transform: none;
    }

    .mec-events-meta-group-booking .mec-ticket-price-label,
    .mec-events-meta-group-booking .mec-ticket-price {
        color: #000;
        font-size: 15px;
        line-height: 1.35;
    }

    .mec-events-meta-group-booking [class*="available"],
    .mec-events-meta-group-booking [class*="Available"],
    .mec-events-meta-group-booking [class*="remaining"],
    .mec-events-meta-group-booking [class*="Remaining"],
    .mec-events-meta-group-booking [class*="ticket-limit"] {
        color: #000;
        font-size: 15px;
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
        . "  function getMecSelectedTicketTimes(form){\n"
        . "    var seen = {};\n"
        . "    var tickets = [];\n"
        . "    form.querySelectorAll('.mec-ticket-detail .mec-ticket-name').forEach(function(ticket){\n"
        . "      var name = String(ticket.textContent || '').replace(/\\s+/g, ' ').trim();\n"
        . "      if(!name || !/\\b\\d{1,2}[:.]\\d{2}\\b/.test(name) || seen[name]){ return; }\n"
        . "      seen[name] = true;\n"
        . "      tickets.push(name);\n"
        . "    });\n"
        . "    return tickets;\n"
        . "  }\n"
        . "  function ensureMecBookingDateTime(){\n"
        . "    var forms = document.querySelectorAll('form.mec-booking-form-container');\n"
        . "    if(!forms.length){ return; }\n"
        . "    forms.forEach(function(form){\n"
        . "      var stepInput = form.querySelector('input[name=\"step\"]');\n"
        . "      if(!stepInput || String(stepInput.value) !== '2'){ return; }\n"
        . "      var dateInput = form.querySelector('input[name=\"book[date]\"]');\n"
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
        . "      var ticketTimes = getMecSelectedTicketTimes(form);\n"
        . "      if(ticketTimes.length){\n"
        . "        label.textContent = (ticketTimes.length > 1 ? 'Gekozen tijdslots: ' : 'Gekozen tijdslot: ') + ticketTimes.join(', ');\n"
        . "        return;\n"
        . "      }\n"
        . "      if(!dateInput){ return; }\n"
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
        . "  function normalizeText(text){\n"
        . "    return String(text || '').replace(/\\s+/g, ' ').trim().toLowerCase();\n"
        . "  }\n"
        . "  function updateMecBookingCopy(){\n"
        . "    document.querySelectorAll('.mec-events-meta-group-booking h3, .mec-events-meta-group-booking h4, .mec-booking-form-container h3, .mec-booking-form-container h4').forEach(function(heading){\n"
        . "      if(normalizeText(heading.textContent) === 'inschrijven'){\n"
        . "        heading.textContent = 'bestel kaarten';\n"
        . "      }\n"
        . "    });\n"
        . "    document.querySelectorAll('.mec-booking-form-container button').forEach(function(button){\n"
        . "      var text = normalizeText(button.textContent);\n"
        . "      if(text === 'add to cart'){\n"
        . "        button.textContent = 'bestel kaarten';\n"
        . "      }\n"
        . "    });\n"
        . "    document.querySelectorAll('.mec-booking-form-container .mec-ticket-subtotal-wrapper .mec-ticket-price-label').forEach(function(label){\n"
        . "      if(normalizeText(label.textContent) === 'subtotal'){\n"
        . "        label.textContent = 'Totaal';\n"
        . "      }\n"
        . "    });\n"
        . "    document.querySelectorAll('.mec-booking-form-container .mec-ticket-detail .mec-ticket-price-label').forEach(function(label){\n"
        . "      if(normalizeText(label.textContent) === 'subtotal'){\n"
        . "        label.textContent = 'Prijs';\n"
        . "      }\n"
        . "    });\n"
        . "  }\n"
        . "  function runMecUiFixes(){\n"
        . "    updateMecLabels();\n"
        . "    updateMecBookingCopy();\n"
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
