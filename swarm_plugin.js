var vm_client;
var vm_width;
var vm_secure;

(function() {
    tinymce.create('tinymce.plugins.ViewMedica', {

        init : function(ed, url) {
            vm_client = ed.settings.vm_client;
            vm_width = ed.settings.vm_width;
            vm_secure = ed.settings.vm_secure;
            vm_plugin_init();

            ed.addButton('viewmedica', {
                title : 'viewmedica.embed',
                image : url+'/viewmedica.png',
                onclick : function() {
                    var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
                    W = W - 80;
                    H = H - 84;
                    tb_show('Viewmedica Options', '#TB_inline?width='+W+'&height='+H+'&inlineId=viewmedica-form');
                }
            });
        },

        createControl : function(n, cm) {
            return null;
        },

        getInfo : function() {
            return {
                longname : "ViewMedica Embed",
                author : 'Seth Wright & Anthony Lobianco',
                authorurl : 'http://swarminteractive.com/',
                infourl : 'http://swarminteractive.com/',
                version : "1.0"
            };
        }
    });

    tinymce.PluginManager.add('viewmedica', tinymce.plugins.ViewMedica);

})();

function vm_plugin_init() {

    jQuery(function() {

        var form = jQuery('<div id="viewmedica-form"><table id="viewmedica-table" class="form-table">\
                <tr>\
                    <th><label for="viewmedica-menuaccess">Turn Off Menu Access</label></th>\
                    <td><input type="checkbox" id="viewmedica_menuaccess" name="menuaccess" value="false" /><br />\
                    <small>Do not let users navigate through ViewMedica</small></td>\
                </tr>\
                <tr>\
                    <th><label for="viewmedica-width">Player Width</label></th>\
                    <td><input type="text" id="viewmedica_width" name="width" value="" /><br />\
                    <small>Set a maximum width for the ViewMedica content. Leave blank for default set on options page.</small></td>\
                </tr>\
                <tr>\
                    <th><label for="viewmedica-openthis">Open Location</label></th>\
                    <td id="open-selector">Loading...</td>\
                </tr>\
                <tr>\
                    <th><label for="viewmedica-openthis">Openthis Code</label></th>\
                    <td><input type="text" id="viewmedica_openthis" name="openthis" value="" /><br />\
                    <small>Input desired opening screen, or choose from the list above. Leave blank for main.</small></td>\
                </tr>\
            </table>\
            <p class="submit">\
                <input type="button" id="viewmedica-submit" class="button-primary" value="Insert Viewmedica Shortcode" name="submit" />\
            </p>\
        </div>');

        jQuery.ajax({
            dataType: "json",
            url: 'http://swarminteractive.com/vm/index/client_json/' + vm_client,
            success: function(data) {
                build(data, form);
            }
        });

        var table = form.find('table');
        form.appendTo('body').hide();

        form.find('#viewmedica-submit').click(function(){
                var options = {
                    'openthis': '',
                    'width': '',
                };
                var shortcode = '[viewmedica';

                for( var index in options) {
                    var value = table.find('#viewmedica_' + index).val();

                    // attaches the attribute to the shortcode only if it's different from the default value
                    if ( value !== options[index] && value != '' )
                        shortcode += ' ' + index + '="' + value + '"';
                }

                if( table.find('#viewmedica_menuaccess').attr('checked') == 'checked' ) {
                    shortcode += ' menuaccess="false"';
                }

                shortcode += ']';

                // inserts the shortcode into the active editor
                tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

                // closes Thickbox
                tb_remove();
        });

    });

}

function build(profile, form)
{
    var html = '<select id="vm-open" style="width: 100%"><option value="">Main Embed</option>\
                    ';

    for (var lib_key in profile.libraries) {

        var l = profile.libraries[lib_key];
        html += '<option value="' + lib_key + '">' + l.label + '</option>';

        for (var col_key in profile.libraries[lib_key].collections) {

            var c = profile.libraries[lib_key].collections[col_key];
            html += '<option value="' + col_key + '">-- ' + c.label + '</option>';

            for (var grp_key in profile.libraries[lib_key].collections[col_key].groups) {

                var g = profile.libraries[lib_key].collections[col_key].groups[grp_key];
                html += '<option value="' + grp_key + '">---- ' + g.label + '</option>';

                for (var item_key in profile.libraries[lib_key].collections[col_key].groups[grp_key].items) {
                    var r = profile.libraries[lib_key].collections[col_key].groups[grp_key].items[item_key];
                    var label = r.label.replace('|', "");
                    html += '<option value="' + item_key + '">------ ' + label + '</option>';
                }

            }
        }
    }

    html+= '</select>';

    form.find('#open-selector').html(html);

    form.find('#vm-open').change(function() {
        jQuery('#viewmedica_openthis').val( jQuery(this).val() );
    });

}
