<?php
// secure.php - Advanced Anti-Theft & Obfuscation Layer
// Note: This script uses aggressive JS to deter inspection.
?>
<script type="text/javascript">
(function() {
    // 1. Strict Mode & Scoping
    'use strict';

    // 2. Disable Right Click with Alert
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        // Optional: Alert the user (remove if too annoying)
        // alert('Content is protected.');
    });

    // 3. Advanced Key Blocking (F12, Ctrl+U, Ctrl+S, Ctrl+P)
    document.onkeydown = function(e) {
        if (e.keyCode == 123 || // F12
            (e.ctrlKey && e.shiftKey && e.keyCode == 73) || // Ctrl+Shift+I
            (e.ctrlKey && e.shiftKey && e.keyCode == 74) || // Ctrl+Shift+J
            (e.ctrlKey && e.keyCode == 85) || // Ctrl+U (Source)
            (e.ctrlKey && e.keyCode == 83) || // Ctrl+S (Save)
            (e.ctrlKey && e.keyCode == 80)) { // Ctrl+P (Print)
            return false;
        }
    };

    // 4. Console Clearing & Detection (The "Black Hole")
    // This constantly clears the console so they can't see errors or logs
    var devtools = function() {};
    devtools.toString = function() {
        // This triggers if they try to print the function
        return 'Protected';
    };
    setInterval(function() {
        console.clear();
        console.log('%c STOP!', 'color: red; font-size: 50px; font-weight: bold; text-shadow: 2px 2px black;');
        console.log('%c This source code is protected.', 'font-size: 20px;');
        console.log(devtools);
    }, 1000);

    // 5. DOM Mutation Observer (Anti-Tamper)
    // If someone tries to remove this script tag from the DOM, reload the page
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.removedNodes.length > 0) {
                // Check if *this* script was removed
                // (Note: Requires giving this script an ID or checking specific content)
                // Basic reload trigger if body is heavily modified:
                // location.reload(); 
            }
        });
    });
    observer.observe(document.documentElement, { childList: true, subtree: true });

    // 6. Debugger Trap (Aggressive)
    // Stops execution if DevTools is open
    (function antiDebug() {
        try {
            (function a() {
                (function b() {
                    // Function constructor trick
                    (function(x) {
                        return x(x)
                    })(Function('debugger;')) 
                })();
            })();
        } catch (e) {}
        setTimeout(antiDebug, 500);
    })();

})();
</script>

<style>
    /* 7. Disable Selection & Dragging CSS */
    body {
        -webkit-user-select: none; /* Safari */
        -ms-user-select: none; /* IE 10 and IE 11 */
        user-select: none; /* Standard syntax */
        -webkit-touch-callout: none;
    }
    img {
        pointer-events: none; /* Disables right click/drag on images specifically */
    }
</style>
