/**
 * Cloneable jQuery Plugin
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */

(function($){

    var CI = $.cloneable = function(options) {

        $(CI.defaults.container).each(function(i, e) {

            CI.init($(this), options); // fall back area for if it's called as $.clone(); could default to all containers
        });
    };

    $.fn.cloneable = function(options) { // called as $('.foo').clone();

        CI.init($(this), options);
    };

    $.extend(CI, {

        defaults: {
            container: '.cloneable',
            item: '.clone-row',
            disabled_item: '.disabled-row',
            protect_value: '.protect-value',

            add_button: '.add-item',
            delete_button: '.delete-item',

            run_add_href: false,
            run_delete_href: false,
            add_item_callback: function(item) { },
            delete_item_callback: function(item) { },
            delete_confirm: true,
            delete_confirm_msg: 'Are you sure?',

            slide_speed: 'normal'

        },

        /**
         * kick off the cloneable plugin!
         */
        init: function(container, options) {

            // merge all the settings / options
            var opts = $.extend({}, CI.defaults, options);
            container.data('opts', opts);

            // set the add button click event
            CI._addButton(container);

            // set the delete button click event
            CI._deleteButton(container);
        },


        /**
         * setup the add item buttons
         *
         * @param jQuery object of the main cloneable container
         */
        _addButton: function(container) {

            var opts = container.data('opts');

            container.find(opts.add_button).bind('click', function(e) {

                // stop the click from running if we don't want to run href
                if (! opts.run_add_href) {

                    e.preventDefault();
                }

                var _click = $(this);

                // get the new option row by duplicating the first one
                var original = container.children(opts.item + ':last');
                var new_option = original.clone().css('display', 'none');
                original.after(new_option);

                new_option.find('input, select, textarea').each(function() {

                    // parse the names
                    var old_name = $(this).attr('name');
                    var new_name = CI.__parseInputName(old_name);
                    $(this).attr('name', new_name);

                    // parse the id value
                    var old_id = $(this).attr('id');

                    if (! CI.__isEmpty(old_id)) {

                        var new_id = CI.__parseInputId(new_name);
                        if (new_id == old_id) {

                            new_id = new_id + '_' + Math.floor((Math.random() * 100) + 1);
                        }

                        $(this).attr('id', new_id);
                    }

                    // clear the value if it's not protected
                    if (! $(this).hasClass(opts.protect_value.replace('.', ''))) {

                        $(this).removeAttr('value');
                    }

                    // check if the original row had disabled elements
                    if ($(this).attr('disabled') == 'disabled') {

                        $(this).removeAttr('disabled');
                    }
                });

                // check if the new option has a disabled-row class
                // from the original row
                if (new_option.hasClass(opts.disabled_item.replace('.', ''))) {

                    original.remove();
                    new_option.removeClass(opts.disabled_item.replace('.', ''));
                }

                // everything should be in place, slide down
                new_option.slideDown(opts.slide_speed, function(e) {

                    // check if we want to run add button href
                    if (opts.run_add_href && ! CI.__isEmpty(_click.attr('href'))) {

                        $.ajax(_click.attr('href'));
                    }

                    // run the new item callback
                    opts.add_item_callback.call(container, new_option);
                });

            });

        },

        /**
         * setup the delete item buttons
         *
         * @param jQuery object of the main cloneable container
         */
        _deleteButton: function(container) {

            var opts = container.data('opts');

            container.on('click', opts.delete_button, function(e) {

                if (opts.delete_confirm) {

                    if (! confirm(opts.delete_confirm_msg)) {

                        e.preventDefault();
                        return;
                    }
                }

                // stop the click from running if we don't want to run href
                if (! opts.run_delete_href) {

                    e.preventDefault();
                }

                var _click = $(this);
                var row_remove = _click.parents(opts.item);

                // slide the row up to hide it
                row_remove.slideUp(opts.slide_speed, function(e) {

                    // check if we want to run delete button href
                    if (opts.run_delete_href && ! CI.__isEmpty(_click.attr('href'))) {

                        $.ajax(_click.attr('href'));
                    }

                    // run the delete item callback
                    opts.delete_item_callback.call(container, row_remove);

                    // check if we want to actually remove from the dom or just disable
                    if (container.find(opts.item).length > 1) {

                        row_remove.remove();

                    } else {

                        // don't remove it, just hide and disable it so we can use it as a template
                        row_remove.addClass(opts.disabled_item.replace('.', ''))
                        .find('input, select, textarea').attr('disabled', 'disabled');
                    }
                });

            });

        },

        /**
         * Helper functions
         */

        /**
         * parse the old input name and increment the indexes
         *
         */
        __parseInputName: function(name) {

            // strip down to the name 'bits'
            name = name.replace('data', '')
                .replace(/\]\[/g, ',')
                .replace(/\[/g, '')
                .replace(/\]/g, '');

            var name_split = name.split(',');
            var length = name.length;

            for (i = 0 ; i < name_split.length ; i++) {

                if (CI.__isInt(name_split[i])) {

                    name_split[i]++;
                    break;
                }
            }

            return 'data[' + name_split.join('][') + ']';
        },

        /**
         * check and compare the input's id. If it's been generated off of
         * the input name, we shall update it
         *
         */
        __parseInputId: function(new_name) {

            new_id = new_name.replace(/\]\[/g, '')
            .replace(/\[/g, '')
            .replace(/\]/g, '');

            return new_id;
        },


        /**
         * check for integer values
         *
         */
        __isInt: function(num) {

            num = parseInt(num);
            return typeof num === 'number' && num >= 0;
        },

        /**
         * check if a string is empty
         *
         */
        __isEmpty: function(str) {

            return (!str || 0 === str.length);
        }

    });

})(jQuery);
