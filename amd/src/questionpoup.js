// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Question popup JS modal
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   moodle-block_questionpopup
 * @copyright 2019-07-21 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

/* eslint no-unused-expressions: "off", no-console:off, no-invalid-this:"off",no-script-url:"off", block-scoped-var: "off" */
define(['jquery', 'core/modal_factory', 'core/templates', 'core/str', 'core/notification'],
    function($, ModalFactory, Templates, Str, Notification) {

    /**
     * Opts that are possible to set.
     *
     * @type {{id: number, debugjs: boolean}}
     */
    var opts = {
        debugjs: true,
        id: 0,
    };

    /**
     * Set options base on listed options
     * @param {object} options
     */
    var setOptions = function(options) {
        "use strict";
        var key, vartype;
        for (key in opts) {
            if (opts.hasOwnProperty(key) && options.hasOwnProperty(key)) {

                // Casting to prevent errors.
                vartype = typeof opts[key];
                if (vartype === "boolean") {
                    opts[key] = Boolean(options[key]);
                } else if (vartype === 'number') {
                    opts[key] = Number(options[key]);
                } else if (vartype === 'string') {
                    opts[key] = String(options[key]);
                }
                // Skip all other types.
            }
        }
    };

    /**
     * Console log debug wrapper.
     */
    var debug = {};

    /**
     * Set debug mode
     * Should only be enabled if site is in debug mode.
     * @param {boolean} isenabled
     */
    var setDebug = function(isenabled) {

        if (isenabled) {
            for (var m in console) {
                if (typeof console[m] == 'function') {
                    debug[m] = console[m].bind(window.console);
                }
            }
        } else {

            // Fake wrapper.
            for (var i in console) {
                if (typeof console[i] == 'function') {
                    debug[i] = function() {
                        // Don't do anything.
                    };
                }
            }
        }
    };

    /**
     * questionpopup
     *
     * @type {{init: init}}
     */
    var questionpopup = {

        init : function() {

            // We need the fetch the names of the blocks. It was too much to send in the page.
            var titlerequests = context.blocks.map(function(blockName) {
                return {
                    key: 'pluginname',
                    component: 'block_' + blockName,
                };
            });

            var bodyPromise = Str.get_strings(titlerequests)
                .then(function(blocks) {
                    context.blocks = blocks;
                    return Templates.render('block_questionpopup/question', context);
                })
                .fail(Notification.exception);

            var titlePromise = Str.get_string('addblock').fail(Notification.exception);

            ModalFactory.create({
                title: titlePromise,
                body: bodyPromise,
                type: 'CANCEL',
            });
        }
    };

    return {

        /**
         * Init
         *
         * @param {object} args
         */
        initialise: function(args) {

            // Load the args passed from PHP.
            setOptions(args);

            // Set internal debug console.
            setDebug(opts.debugjs);

            $.noConflict();
            $(document).ready(function() {
                debug.log('Block Quesiton Popup v1.0');
                questionpopup.init();
            });
        }
    };
});