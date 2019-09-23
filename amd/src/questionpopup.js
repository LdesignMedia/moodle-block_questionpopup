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
 * @package   block_questionpopup
 * @copyright 2019-07-21 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

/* eslint no-unused-expressions: "off", no-console:off, no-invalid-this:"off",no-script-url:"off", block-scoped-var: "off" */
define(['jquery', 'core/modal_factory', 'core/templates', 'core/str', 'core/notification', 'core/modal_events', 'core/ajax',],
    function($, ModalFactory, Templates, Str, Notification, ModalEvents, Ajax) {

        /**
         * Opts that are possible to set.
         *
         * @type {{id: number, debugjs: boolean}}
         */
        var opts = {
            debugjs: true,
            contextid: 0,
            questions: [],
            locale: '',
            answers: [],
            display: false
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
                    } else {
                        opts[key] = options[key];
                    }
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
            saveAnswer: function(data) {
                var request = {
                    methodname: 'block_questionpopup_save_answer',
                    args: {
                        'answer': data,
                        'contextid': opts.contextid
                    }
                };

                Ajax.call([request])[0].done(function(data) {
                    if (data.result_code === 200) {
                        window.location.reload();
                    } else {
                        Notification.addNotification({
                            message: data.warnings[0].message,
                            type: 'error'
                        });
                    }
                }).fail(Notification.exception);
            },

            /**
             * Init popup
             *
             * @param opts
             */
            init: function(opts) {
                console.log(opts);
                // Show modal.
                ModalFactory.create({
                    title: M.util.get_string('js:popup_title', 'block_questionpopup'),
                    body: Templates.render('block_questionpopup/question', opts),
                    type: ModalFactory.types.SAVE_CANCEL,
                }, $('.preview-question')).then(function(modal) {

                    // Handle send event.
                    modal.getRoot().on(ModalEvents.save, function(e) {
                        // Send the answer.
                        questionpopup.saveAnswer($('form#question_popup').serialize());
                    });

                    // Handle hidden event.
                    modal.getRoot().on(ModalEvents.hidden, function() {

                        // Destroy when hidden.
                        if (opts.display) {

                            // Don't allow hide, if there is no answer saved.
                            modal.show();
                        }
                    });

                    return modal;

                }).done(function(modal) {

                    // Always show if there is no answer saved.
                    if (opts.display) {
                        modal.show();
                    }

                    $('.modal-footer [data-action=cancel]').hide();
                }).catch(Notification.exception);
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
                    debug.log('Block Quesiton Popup v1.1');
                    questionpopup.init(opts);
                });
            }
        };
    });