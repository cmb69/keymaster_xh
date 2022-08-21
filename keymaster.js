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
 * @type {HTMLDivElement}
 */
var warningDialog = null;

/**
 * @type {Text}
 */
var warningText = '';

/**
 * @type {Text}
 */
var buttonText = '';

/**
 * @type {Number}
 */
var timer = null;

/**
 * @type {Object}
 */
var config = JSON.parse(document.getElementsByName("keymaster_config")[0].content);

/**
 * @type {Object}
 */
var l10n = JSON.parse(document.getElementsByName("keymaster_lang")[0].content);

function initWarningDialog() {
    var button;

    warningDialog = document.getElementById("keymaster");
    warningText = warningDialog.getElementsByTagName("p")[0].firstChild;
    button = warningDialog.getElementsByTagName("button")[0];
    button.onclick = resetSession;
    buttonText = button.firstChild;
}

function showWarningDialog() {
    warningDialog.style.display = "block";
}

function hideWarningDialog() {
    warningDialog.style.display = "none";
}

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
 * @param {XMLHttpRequest} request
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

function resetSession() {
    var request = new XMLHttpRequest();

    request.open("POST", "./");
    request.setRequestHeader("Content-Type",
            "application/x-www-form-urlencoded");
    request.send("keymaster_reset");
    hideWarningDialog();
}

/**
 * @param {Number} seconds
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

addEventListener("load", function () {
    initWarningDialog();
    timer = setInterval(requestRemainingTime, config.pollInterval);
});
}());
