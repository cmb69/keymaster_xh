/**
 * JavaScript of Keymaster_XH.
 *
 * @copyright   Copyright (c) 2013 Christoph M. Becker <http://3-magi.net/>
 * @license     http://www.gnu.org/licenses/gpl.html GNU GPLv3
 * @version     $Id$
 * @link        <http://3-magi.net/?CMSimple_XH/Keymaster_XH>
 */


/**
 * Retrieve remaining session time from server and act accordingly.
 *
 * @returns {undefined}
 */
keymaster.getTime = function() {
    var url = "./?&keymaster_time";
    var request = new XMLHttpRequest();
    request.open("GET", url);
    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            var remaining = +request.responseText;
            if (remaining != remaining) {
                window.clearInterval(keymaster.timer);
                if (window.confirm(keymaster.text.error)) {
                    window.open(url);
                }
            } else if (remaining < 0) {
                window.location.href = window.location.href;
            } else if (remaining == 0) {
                window.location.href = "./?&logout";
            } else if (remaining < keymaster.warn) {
                keymaster.updateWarning(remaining);
                keymaster.dialog.style.display = "block";
            } else {
                keymaster.dialog.style.display = "none";
            }
        }
    }
    request.send(null);
}


/**
 * Reset the session timeout.
 *
 * @returns {undefined}
 */
keymaster.reset = function() {
    var request = new XMLHttpRequest();
    request.open("POST", "./");
    request.setRequestHeader("Content-Type",
                             "application/x-www-form-urlencoded");
    request.send("keymaster_reset");
    keymaster.dialog.style.display = "none";
}


/**
 * Builds the warning message.
 *
 * @returns {undefined}
 */
keymaster.warning = function() {
    var warning = document.createElement("div");
    warning.id = "keymaster";
    warning.style.display = "none";
    var message = document.createElement("div");
    message.className = "keymaster_message";
    var para = document.createElement("p");
    var text = document.createTextNode("");
    keymaster.warningText = text;
    para.appendChild(text);
    message.appendChild(para);
    var div = document.createElement("div");
    div.className = "keymaster_buttons";
    var button = document.createElement("button");
    button.onclick = keymaster.reset;
    var text = document.createTextNode(keymaster.text.button);
    button.appendChild(text);
    div.appendChild(button);
    message.appendChild(div);
    warning.appendChild(message);
    document.body.appendChild(warning);
    keymaster.dialog = warning;
}


/**
 * Updates the warning message.
 *
 * @param   {Number}  The remaining time in seconds.
 * @returns {undefined}
 */
keymaster.updateWarning = function(seconds) {
    var min = Math.ceil(seconds / 60);
    var text = min == 1
        ? keymaster.text.warning_singular
        : (min >= 2 && min <= 4
            ? keymaster.text.warning_paucal
            : keymaster.text.warning_plural);
    keymaster.warningText.nodeValue = text.replace(/{{{TIME}}}/, min);
}


/**
 * Initializes the keymaster.
 *
 * @returns {undefined}
 */
keymaster.init = function() {
    keymaster.warning();
    keymaster.timer =
        window.setInterval(keymaster.getTime, keymaster.pollInterval);
}


/*
 * Register initialization on window.load.
 */
if (typeof window.addEventListener != "undefined") {
    window.addEventListener("load", keymaster.init, false);
} else if (typeof window.attachEvent != "undefined") {
    window.attachEvent("onload", keymaster.init);
}
