/**
 * 2016 WeeTeam
 *
 * @author    WeeTeam <info@weeteam.net>
 * @copyright 2016 WeeTeam
 * @license   http://www.gnu.org/philosophy/categories.html (Shareware)
 */

var WTRECURRINGORDERS = {
    attr: {
        prefix: ''
    },
    url: {
        ajax: ''
    },
    lng: {},
    init: {
        rich_editor: function(){
            if (typeof tinyMCE != "undefined") {
                tinyMCE.init({
                    editor_selector : window.WTRECURRINGORDERS.attr.prefix + 'email_template',
                    document_base_url: window.WTRECURRINGORDERS.url.base,
                    mode: 'textareas',
                    inlinepopups_skin: 'concreteMCE',
                    theme: 'advanced',
                    theme_advanced_toolbar_location: 'top',
                    width: '100%',
                    height: '200px',
                    relative_urls : false,
                    convert_urls: false,
                    plugins: 'paste,inlinepopups,spellchecker,safari'
                });
            }
        }
    },
    ajax: function(attr){
        //attr: {action, data, callback_before, callback_error, callback_success, callback_complete}

        jQuery.ajax({
            url: window.WTRECURRINGORDERS.url.ajax + '&action=' + attr.action,
            type: 'post',
            dataType: 'json',
            cache: true,
            global: false,
            data: {
                data: attr.data
            },
            beforeSend: function(jqXHR, settings){
                if (typeof(attr.callback_before) == 'function')
                {
                    attr.callback_before(jqXHR, settings);
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                if (typeof(attr.callback_error) == 'function')
                {
                    attr.callback_error(jqXHR, textStatus, errorThrown);
                }
            },
            success: function(data, textStatus, jqXHR){
                if (typeof(attr.callback_success) == 'function')
                {
                    attr.callback_success(data, textStatus, jqXHR);
                }
            },
            complete: function(jqXHR, textStatus){
                if (typeof(attr.callback_complete) == 'function')
                {
                    attr.callback_complete(jqXHR, textStatus);
                }
            }
        });
    },
    confirm: function(message, callback_ok, callback_no){
        var html  = '<div class="message alert alert-info">';
                html += '<p>' + message + '</p>';
            html += '</div>';
            html += '<div class="btns">';
                html += '<a href="#" class="confirm ok btn btn-default">' + window.WTRECURRINGORDERS.lng.confirm_ok + '</a>';
                html += '<a href="#" class="confirm no btn btn-default">' + window.WTRECURRINGORDERS.lng.confirm_no + '</a>';
            html += '</div>';

        jQuery.fancybox.open({
            wrapCSS: window.WTRECURRINGORDERS.attr.prefix + 'confirm bootstrap',
            minWidth: 350,
            content: html,
            afterShow: function(){
                var p = jQuery('.fancybox-inner');

                jQuery('.confirm.ok', p).unbind().bind('click', function(){
                    if (typeof(callback_ok) == 'function'){ callback_ok(); }
                    jQuery.fancybox.close();
                    return false;
                });

                jQuery('.confirm.no', p).unbind().bind('click', function(){
                    if (typeof(callback_no) == 'function'){ callback_no(); }
                    jQuery.fancybox.close();
                    return false;
                });
            }
        });
    }
};
