/**
 * 2016 WeeTeam
 *
 * @author    WeeTeam <info@weeteam.net>
 * @copyright 2016 WeeTeam
 * @license   http://www.gnu.org/philosophy/categories.html (Shareware)
 */


var fc = {
    settings: {
        type:       'post',
        dataType:   'json',
        url:        '',
    },

    ajax_call: function (args, c_name) {
        var data = {};

        if (!(Object.keys(args).length === 0 && args.constructor === Object)) {
            for (var i in args) {
                if (typeof args[i] != undefined) {
                    data[i] = args[i];
                }
            }
        }

        $.ajax({
            type    : fc.settings.type,
            dataType: fc.settings.dataType,
            url     : fc.settings.url,
            data    : data,
            beforeSend: function() {
                jQuery.fancybox.showLoading();
            },
            success : function (data, textStatus, jqXHR)
            {
                if (typeof fc.callbacks[c_name] === "function") {
                    fc.callbacks[c_name](data);
                }
            },
        });
    },

    callbacks : {
        present_prds: function(data) {
            if (data.status) {
                $('.disabled-prds').append(data.html);
                parent_container = jQuery('.disabled-prds');
            }
            jQuery.fancybox.hideLoading();
        },
        silent: function(data) {
            jQuery.fancybox.hideLoading();
        }
    },
}

jQuery(document).ready(function () {
    var parent_container = jQuery('.disabled-prds');

    jQuery('#tree_selected_categories, #tree_selected_products').closest('.form-group').hide();

    jQuery('input[name="setting_disabled_products"]').change(function () {
        jQuery('#tree_selected_categories, #tree_selected_products').closest('.form-group').slideUp();
        jQuery('#tree_' + $(this).attr('id')).closest('.form-group').slideDown();
        parent_container.empty();
    });

    jQuery(document).on('change', '#tree_selected_products [type=checkbox]', function () {
        var clicked_checkbox = jQuery(this);

        if (jQuery(this).is(':checked')) {
            args = {
                wt_action: 'get_list_products',
                id_category: clicked_checkbox.val(),
                already_presented_prds: getPresentedPrds()
            };
            fc.ajax_call(args, 'present_prds');

        } else {
            parent_container.find(jQuery('.single-cat.cat-id-' + clicked_checkbox.val())).hide();
            jQuery('.single-cat.cat-id-' + clicked_checkbox.val()).fadeOut(function() {
               jQuery('.single-cat.cat-id-' + clicked_checkbox.val()).remove();
            });
        }
    });

    jQuery(document).on('click', '#uncheck-all-tree_selected_products', function() {
        parent_container.empty();
    });
    jQuery(document).on('click', '#check-all-tree_selected_products', function() {
        args = {
            wt_action: 'get_list_products',
            id_category: 2,
            already_presented_prds: getPresentedPrds()
        };
        fc.ajax_call(args, 'present_prds');
    });
    jQuery(document).on('change', "input[name='product[]']", function() {
        var clicked_checkbox = jQuery(this);
        args = {
            wt_action: 'change_product_ro_availability',
            id_product: clicked_checkbox.data('id'),
        };
        if (jQuery(this).is(':checked')) {
            args.disabled = 1;
        } else {
            args.disabled = 0;
        }
        fc.ajax_call(args, 'silent');
    });
});


function getPresentedPrds() {
    rez = [];
    $( ".disabled-prds label input" ).each(function() {
        rez.push(jQuery(this).data('id'));
    });
    return rez;
}