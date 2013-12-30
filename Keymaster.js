/**
 * The Keymaster class.
 *
 * @file
 *
 * @version   SVN: $Id$
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl.html GNU GPLv3
 */

/**
 * The Keymaster class.
 *
 * @constructor
 *
 */
Keymaster = function () {
    /**
     * The warning dialog.
     *
     * @type {HTMLDivElement}
     *
     * @private
     */
    this._warningDialog = null;

    /**
     * The warning text.
     *
     * @type {Text}
     *
     * @private
     */
    this._warningText = '';

    /**
     * The timer handle.
     *
     * @type {Number}
     *
     * @private
     */
    this._timer = null;

    this.createWarningDialog();
    this._timer = setInterval(this.bind(this.requestRemainingTime),
            Keymaster.config.pollInterval);
};

/**
 * The configuration.
 *
 * @type {Object}
 *
 * @static
 */
Keymaster.config = null;

/**
 * The localization
 *
 * @type {Object}
 *
 * @static
 */
Keymaster.l10n = null;

/**
 * Creates the warning dialog.
 *
 * @method
 *
 * @protected
 */
Keymaster.prototype.createWarningDialog = function () {
    var dialog, message, para, text, div, button;

    dialog = document.createElement("div");
    dialog.id = "keymaster";
    dialog.style.display = "none";
    message = document.createElement("div");
    message.className = "keymaster_message";
    para = document.createElement("p");
    text = document.createTextNode("");
    this._warningText = text;
    para.appendChild(text);
    message.appendChild(para);
    div = document.createElement("div");
    div.className = "keymaster_buttons";
    button = document.createElement("button");
    button.onclick = this.bind(this.resetSession);
    text = document.createTextNode(Keymaster.l10n.button);
    button.appendChild(text);
    div.appendChild(button);
    message.appendChild(div);
    dialog.appendChild(message);
    document.body.appendChild(dialog);
    this._warningDialog = dialog;
};

/**
 * Shows the warning dialog.
 *
 * @method
 *
 * @protected
 */
Keymaster.prototype.showWarningDialog = function () {
    this._warningDialog.style.display = "block";
};

/**
 * Hides the warning dialog.
 *
 * @method
 *
 * @protected
 */
Keymaster.prototype.hideWarningDialog = function () {
    this._warningDialog.style.display = "none";
};

/**
 * Requests the remaining session time from the server.
 *
 * @method
 *
 * @protected
 */
Keymaster.prototype.requestRemainingTime = function () {
    var request = new XMLHttpRequest();
    var url = "./?&keymaster_time";

    request.open("GET", url);
    // The following should simply use Function.prototype.bind() or a fallback,
    // but IE 8 doesn't set window.event, so we'd loose the reference to
    // the request object.
    request.onreadystatechange = (function (that) {
        return function () {
            that.receiveRemainingTime(request);
        };
    }(this));
    request.send(null);
};

/**
 * Handles responses for the requested session time.
 *
 * @method
 *
 * @param {XMLHttpRequest} request A request object.
 *
 * @protected
 */
Keymaster.prototype.receiveRemainingTime = function (request) {
    if (request.readyState == 4 && request.status == 200) {
        var remaining = +request.responseText;
        if (remaining != remaining) {
            clearInterval(this._timer);
            if (confirm(Keymaster.l10n.error)) {
                open(url);
            }
        } else if (remaining < 0) {
            location.href = location.href;
        } else if (remaining == 0) {
            location.href = "./?&logout";
        } else if (remaining < Keymaster.config.warn) {
            this.updateWarning(remaining);
            this.showWarningDialog();
        } else {
            this.hideWarningDialog();
        }
    }
};

/**
 * Resets the session timeout.
 *
 * @method
 *
 * @protected
 */
Keymaster.prototype.resetSession = function () {
    var request = new XMLHttpRequest();

    request.open("POST", "./");
    request.setRequestHeader("Content-Type",
            "application/x-www-form-urlencoded");
    request.send("keymaster_reset");
    this.hideWarningDialog();
};

/**
 * Updates the warning message.
 *
 * @method
 *
 * @param {Number} seconds The remaining time in seconds.
 *
 * @protected
 */
Keymaster.prototype.updateWarning = function (seconds) {
    var min = Math.ceil(seconds / 60);

    if (min == 1) {
        text = Keymaster.l10n.warning_singular;
    } else if (min >= 2 && min <= 4) {
        text = Keymaster.l10n.warning_paucal;
    } else {
        text = Keymaster.l10n.warning_plural;
    }
    this._warningText.nodeValue = text.replace(/{{{TIME}}}/, min);
};

/**
 * Creates a new function bound to this. Provides a workaround for possibly
 * missing Function.prototype.bind().
 *
 * @method
 *
 * @param {Function} func A function to bind.
 *
 * @returns {Function}
 *
 * @protected
 */
Keymaster.prototype.bind = function (func) {
    var that = this;

    return function () {
        func.apply(that, arguments);
    };
};

/*
 * Register Keymaster instantiation on load.
 */
if (typeof addEventListener != "undefined") {
    addEventListener("load", function () {
        new Keymaster();
    });
} else if (typeof attachEvent != "undefined") {
    attachEvent("onload", function () {
        new Keymaster();
    });
}
