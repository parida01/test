/**
 * 2016 WeeTeam
 *
 * @author    WeeTeam <info@weeteam.net>
 * @copyright 2016 WeeTeam
 * @license   http://www.gnu.org/philosophy/categories.html (Shareware)
 */

var WTRECURRINGORDERS_manager = {
    attr: {
        wrapper: null
    },
    temp: {
        popup_auto_close_to: null
    },
    init: {
        main: function(){
            window.WTRECURRINGORDERS_manager.attr.wrapper = jQuery('#' + window.WTRECURRINGORDERS.attr.prefix + 'manager');

            jQuery('*', window.WTRECURRINGORDERS_manager.attr.wrapper).unbind();
            window.WTRECURRINGORDERS_manager.init.manager();
        },
        manager: function(){
            jQuery('.product_recurring_options', window.WTRECURRINGORDERS_manager.attr.wrapper).click(function(){
                window.WTRECURRINGORDERS_manager.actions.products_recurring_manager(1, null, function(data){
                    jQuery('.' + window.WTRECURRINGORDERS.attr.prefix + 'products_recurring_manager .item').click(function(){
                        window.WTRECURRINGORDERS_manager.actions.products_recurring_manager(2, {
                            id: jQuery(this).data('id'),
                            id_product_attribute: jQuery(this).data('id-attribute')
                        }, function(data){
                            var wrapper_manager = jQuery('.' + window.WTRECURRINGORDERS.attr.prefix + 'products_recurring_manager');
                            jQuery('input[name="' + window.WTRECURRINGORDERS.attr.prefix + 'manager[option]"]', wrapper_manager).click(function(){
                                jQuery('.option-params', wrapper_manager).hide();
                                jQuery('.option-params.op-' + jQuery(this).val(), wrapper_manager).show();
                            }).filter(':checked').click();

                            jQuery('.button.prm_cancel', wrapper_manager).unbind('click').click(function(){
                                jQuery.fancybox.close();
                                return false;
                            });

                            jQuery('.button.prm_apply', wrapper_manager).unbind('click').click(function(){
                                var option = jQuery('input[name="' + window.WTRECURRINGORDERS.attr.prefix + 'manager[option]"]:checked', wrapper_manager).val();

                                window.WTRECURRINGORDERS_manager.actions.products_recurring_manager(3, {
                                    id: jQuery('.cart-product', wrapper_manager).data('id'),
                                    id_product_attribute: jQuery('.cart-product', wrapper_manager).data('id-attribute'),
                                    quantity: jQuery('input[name="' + window.WTRECURRINGORDERS.attr.prefix + 'manager[quantity]"]', wrapper_manager).val(),
                                    recurring: {
                                        option: option,
                                        params: {
                                            d: jQuery('input[name="' + window.WTRECURRINGORDERS.attr.prefix + 'manager[params][' + option + '][d]"]', wrapper_manager).val()
                                        }
                                    }
                                }, function(data){
                                    if (data.status)
                                    {
                                        window.WTRECURRINGORDERS_manager.temp.popup_auto_close_to = setTimeout(function(){
                                            jQuery.fancybox.close();
                                        }, 3000);
                                    }
                                }, null, function(){
                                    clearTimeout(window.WTRECURRINGORDERS_manager.temp.popup_auto_close_to);
                                    window.WTRECURRINGORDERS_manager.actions.session_products_recurring_get();
                                });

                                return false;
                            });
                        });
                    });
                });

                return false;
            });
        },
        products_recurring_list: function(){
            jQuery('.cart_products_list .item, .products_recurring_list .item .image, .products_recurring_list .item .info', window.WTRECURRINGORDERS_manager.attr.wrapper).click(function(){
                var item = jQuery(this).closest('.item');
                var id = item.data('product-id');
                if (typeof id == 'undefined') id = item.data('id')
                var id_product_attribute = item.data('id-attribute');
                var key = item.data('key');
                window.WTRECURRINGORDERS_manager.actions.session_product_recurring_edit(id, id_product_attribute,  key);
            });

            jQuery('.products_recurring_list .item .remove a', window.WTRECURRINGORDERS_manager.attr.wrapper).click(function(){
                var key = jQuery(this).closest('.item').data('key');
                window.WTRECURRINGORDERS_manager.actions.session_product_recurring_remove(key);

                return false;
            });
        }
    },
    actions: {
        products_recurring_manager: function(step, data, callback_show, callback_before, callback_close){
            window.WTRECURRINGORDERS.ajax({
                action: 'session_products_recurring_manager',
                data: {
                    step: step,
                    data: data
                },
                callback_before: function(jqXHR, settings){
                    jQuery.fancybox.showLoading();
                },
                callback_success: function(data, textStatus, jqXHR){
                    var css = '';
                    if (typeof(data.css) != 'undefined'){ css = data.css; }
                    if (data.status)
                    {
                        if (typeof data.ok != 'undefined') {
                            if (data.ok === true) {
                                data.html = window.WTRECURRINGORDERS.lng.rec_option_ok;
                            } else {
                                data.html = window.WTRECURRINGORDERS.lng.rec_option_ko;
                            }
                        }
                        jQuery.fancybox.open({
                            wrapCSS: window.WTRECURRINGORDERS.attr.prefix + 'products_recurring_manager ' + css,
                            content: data.html,
                            beforeShow: function(){
                                if (typeof(callback_before) == 'function'){ callback_before(data); }
                            },
                            afterShow: function(){
                                if (typeof(callback_show) == 'function'){ callback_show(data); }
                            },
                            afterClose: function(){
                                if (typeof(callback_close) == 'function'){ callback_close(data); }
                            }
                        });
                    }
                }
            });
        },
        session_products_recurring_get: function(callback){
            window.WTRECURRINGORDERS.ajax({
                action: 'session_products_recurring_get',
                data: {},
                callback_before: function(jqXHR, settings){
                    jQuery.fancybox.showLoading();
                },
                callback_success: function(data, textStatus, jqXHR){
                    if (data.status)
                    {
                        jQuery('.products_recurring_list', window.WTRECURRINGORDERS_manager.attr.wrapper).html(data.html);
                    }

                    if (typeof(callback) == 'function'){ callback(data); }

                    window.WTRECURRINGORDERS_manager.init.products_recurring_list();
                    jQuery.fancybox.hideLoading();
                }
            });
        },
        session_product_recurring_edit: function(id, id_product_attribute, key){
            window.WTRECURRINGORDERS_manager.actions.products_recurring_manager(2, {id: id, id_product_attribute: id_product_attribute,  key: key}, function(data){
                var wrapper_manager = jQuery('.' + window.WTRECURRINGORDERS.attr.prefix + 'products_recurring_manager');

                jQuery('input[name="' + window.WTRECURRINGORDERS.attr.prefix + 'manager[option]"]', wrapper_manager).click(function(){
                    jQuery('.option-params', wrapper_manager).hide();
                    jQuery('.option-params.op-' + jQuery(this).val(), wrapper_manager).show();
                }).filter(':checked').click();

                jQuery('.button.prm_cancel', wrapper_manager).unbind('click').click(function(){
                    jQuery.fancybox.close();
                    return false;
                });

                jQuery('.button.prm_apply', wrapper_manager).unbind('click').click(function(){
                    var option = jQuery('input[name="' + window.WTRECURRINGORDERS.attr.prefix + 'manager[option]"]:checked', wrapper_manager).val();

                    window.WTRECURRINGORDERS_manager.actions.products_recurring_manager(3, {
                        key: data.key,
                        id: jQuery('.cart-product', wrapper_manager).data('id'),
                        quantity: jQuery('input[name="' + window.WTRECURRINGORDERS.attr.prefix + 'manager[quantity]"]', wrapper_manager).val(),
                        recurring: {
                            option: option,
                            params: {
                                d: jQuery('input[name="' + window.WTRECURRINGORDERS.attr.prefix + 'manager[params][' + option + '][d]"]', wrapper_manager).val()
                            }
                        },
                    }, function(data){
                        if (data.status)
                        {
                            window.WTRECURRINGORDERS_manager.temp.popup_auto_close_to = setTimeout(function(){
                                jQuery.fancybox.close();
                            }, 3000);
                        }
                    }, null, function(){
                        clearTimeout(window.WTRECURRINGORDERS_manager.temp.popup_auto_close_to);
                        window.WTRECURRINGORDERS_manager.actions.session_products_recurring_get();
                    });

                    return false;
                });
            });
        },
        session_product_recurring_remove: function(key){
            window.WTRECURRINGORDERS.confirm(window.WTRECURRINGORDERS.lng.confirm_session_product_recurring_remove, function(){
                window.WTRECURRINGORDERS.ajax({
                    action: 'session_product_recurring_remove',
                    data: {
                        key: key
                    },
                    callback_before: function(jqXHR, settings){
                        jQuery.fancybox.showLoading();
                    },
                    callback_success: function(data, textStatus, jqXHR){
                        if (data.status)
                        {
                            window.WTRECURRINGORDERS_manager.actions.session_products_recurring_get();
                        }
                    }
                });
            }, null);
        }
    }
};
