/**
 * The Keymaster class.
 *
 * @file
 *
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013-2019 Christoph M. Becker <http://3-magi.net/>
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
     * The button text.
     *
     * @type {Text}
     *
     * @private
     */
    this._buttonText = '';

    /**
     * The timer handle.
     *
     * @type {Number}
     *
     * @private
     */
    this._timer = null;

    this.initWarningDialog();
    this._timer = setInterval(this.requestRemainingTime.bind(this),
            Keymaster.config.pollInterval);
};

/**
 * The configuration.
 *
 * @type {Object}
 *
 * @static
 */
Keymaster.config = JSON.parse(document.getElementsByName("keymaster_config")[0].content);

/**
 * The localization
 *
 * @type {Object}
 *
 * @static
 */
Keymaster.l10n = JSON.parse(document.getElementsByName("keymaster_lang")[0].content);

/**
 * Initializes the warning dialog.
 *
 * @method
 *
 * @protected
 */
Keymaster.prototype.initWarningDialog = function () {
    var button;

    this._warningDialog = document.getElementById("keymaster");
    this._warningText = this._warningDialog.getElementsByTagName("p")[0].firstChild;
    button = this._warningDialog.getElementsByTagName("button")[0];
    button.onclick = this.resetSession.bind(this);
    this._buttonText = button.firstChild;
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
    request.onreadystatechange = this.receiveRemainingTime.bind(this, request);
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
            clearInterval(this._timer);
            this.updateWarning(remaining);
            location.href = location.href;
        } else if (remaining == 0) {
            clearInterval(this._timer);
            this.updateWarning(remaining);
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
    var min;

    if (seconds > 0) {
        min = Math.ceil(seconds / 60);
        if (min == 1) {
            text = Keymaster.l10n.warning_singular;
        } else if (min >= 2 && min <= 4) {
            text = Keymaster.l10n.warning_paucal;
        } else {
            text = Keymaster.l10n.warning_plural;
        }
        this._warningText.nodeValue = text.replace(/{{{TIME}}}/, min);
    } else {
        this._warningText.nodeValue = Keymaster.l10n.warning_loggedout;
        this._buttonText.nodeValue = Keymaster.l10n.button_ok;
    }
};

/*
 * Register Keymaster instantiation on load.
 */
addEventListener("load", function () {
    new Keymaster();
});
