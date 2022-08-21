/**
 * The Keymaster class.
 *
 * @file
 *
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013-2019 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl.html GNU GPLv3
 */

(function () {
/**
 * The warning dialog.
 *
 * @type {HTMLDivElement}
 */
var warningDialog = null;

/**
 * The warning text.
 *
 * @type {Text}
 */
var warningText = '';

/**
 * The button text.
 *
 * @type {Text}
 */
var buttonText = '';

/**
 * The timer handle.
 *
 * @type {Number}
 */
var timer = null;

/**
 * The configuration.
 *
 * @type {Object}
 */
var config = JSON.parse(document.getElementsByName("keymaster_config")[0].content);

/**
 * The localization
 *
 * @type {Object}
 */
var l10n = JSON.parse(document.getElementsByName("keymaster_lang")[0].content);

/**
 * Initializes the warning dialog.
 */
function initWarningDialog() {
    var button;

    warningDialog = document.getElementById("keymaster");
    warningText = warningDialog.getElementsByTagName("p")[0].firstChild;
    button = warningDialog.getElementsByTagName("button")[0];
    button.onclick = resetSession;
    buttonText = button.firstChild;
}

/**
 * Shows the warning dialog.
 */
function showWarningDialog() {
    warningDialog.style.display = "block";
}

/**
 * Hides the warning dialog.
 */
function hideWarningDialog() {
    warningDialog.style.display = "none";
}

/**
 * Requests the remaining session time from the server.
 */
function requestRemainingTime() {
    var request = new XMLHttpRequest();
    var url = "./?&keymaster_time";

    request.open("GET", url);
    request.onreadystatechange = function () {
        receiveRemainingTime(request);
    }
    request.send(null);
}

/**
 * Handles responses for the requested session time.
 *
 * @param {XMLHttpRequest} request A request object.
 */
function receiveRemainingTime(request) {
    if (request.readyState == 4 && request.status == 200) {
        var remaining = +request.responseText;
        if (remaining != remaining) {
            clearInterval(timer);
            if (confirm(l10n.error)) {
                open(url);
            }
        } else if (remaining < 0) {
            clearInterval(timer);
            updateWarning(remaining);
            location.href = location.href;
        } else if (remaining == 0) {
            clearInterval(timer);
            updateWarning(remaining);
            location.href = "./?&logout";
        } else if (remaining < config.warn) {
            updateWarning(remaining);
            showWarningDialog();
        } else {
            hideWarningDialog();
        }
    }
}

/**
 * Resets the session timeout.
 */
function resetSession() {
    var request = new XMLHttpRequest();

    request.open("POST", "./");
    request.setRequestHeader("Content-Type",
            "application/x-www-form-urlencoded");
    request.send("keymaster_reset");
    hideWarningDialog();
}

/**
 * Updates the warning message.
 *
 * @param {Number} seconds The remaining time in seconds.
 */
function updateWarning(seconds) {
    var min;

    if (seconds > 0) {
        min = Math.ceil(seconds / 60);
        if (min == 1) {
            text = l10n.warning_singular;
        } else if (min >= 2 && min <= 4) {
            text = l10n.warning_paucal;
        } else {
            text = l10n.warning_plural;
        }
        warningText.nodeValue = text.replace(/{{{TIME}}}/, min);
    } else {
        warningText.nodeValue = l10n.warning_loggedout;
        buttonText.nodeValue = l10n.button_ok;
    }
}

/*
 * Register Keymaster instantiation on load.
 */
addEventListener("load", function () {
    initWarningDialog();
    timer = setInterval(requestRemainingTime, config.pollInterval);
});
}());
