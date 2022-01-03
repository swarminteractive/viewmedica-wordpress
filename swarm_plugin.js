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
                    var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 752 < width ) ? 752 : width;
                    W = W;
                    H = H - 130;
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
                authorurl : 'https://swarminteractive.com/',
                infourl : 'https://swarminteractive.com/',
                version : "1.0"
            };
        }
    });

    tinymce.PluginManager.add('viewmedica', tinymce.plugins.ViewMedica);

})();

function vm_plugin_init() {

    jQuery(function() {

	      jQuery("#TB_ajaxContent").css({
            "width": "auto",
            "height": "auto",
            "overflow": "auto"
        });

        var form = jQuery('<div id="viewmedica-form"><table id="viewmedica-table" class="form-table">\
                <tr>\
                    <th><label for="viewmedica-openthis">Open Location</label></th>\
                    <td id="open-selector">Loading...</td>\
                </tr>\
                <tr>\
                    <th><label for="viewmedica-openthis">Openthis Code</label></th>\
                    <td><input type="text" id="viewmedica_openthis" name="openthis" value="" /><br />\
                    <small>Input desired opening screen, or choose from the list above. Leave blank for main.</small></td>\
                </tr>\
                <tr>\
                    <th><label for="viewmedica-width">Player Width</label></th>\
                    <td><input type="text" id="viewmedica_width" name="width" value="" /><br />\
                    <small>Set a maximum width for the ViewMedica content. Leave blank for default set on options page.</small></td>\
                </tr>\
                <tr>\
                    <th><label for="viewmedica-menuaccess">Turn Off Menu Access</label></th>\
                    <td><input type="checkbox" id="viewmedica_menuaccess" name="menuaccess" value="false" /><br />\
                    <small>Do not let users navigate through ViewMedica</small></td>\
                </tr>\
                <tr>\
                    <th><label for="viewmedica-audio">Turn Off Audio</label></th>\
                    <td><input type="checkbox" id="viewmedica_audio" name="audio" value="false" /><br />\
                    <small>Mute the ViewMedica Player by default</small></td>\
                </tr>\
                <tr>\
                    <th><label for="viewmedica-autoplay">Autoplay Video</label></th>\
                    <td><input type="checkbox" id="viewmedica_autoplay" name="autoplay" value="true" /><br />\
                    <small>Attempt to autoplay the video (does not work on mobile)</small></td>\
                </tr>\
                <tr>\
                    <th><label for="viewmedica-captions">Hide Captions Button</label></th>\
                    <td><input type="checkbox" id="viewmedica_captions" name="captions" value="false" /><br />\
                    <small>Closed captioning button is not visible for the user</small></td>\
                </tr>\
                <tr>\
                    <th><label for="viewmedica-subtitles">Show Subtitles</label></th>\
                    <td><input type="checkbox" id="viewmedica_subtitles" name="subtitles" value="false" /><br />\
                    <small>Subtitles are shown by default when a video is playing</small></td>\
                </tr>\
                <tr>\
                    <th><label for="viewmedica-markup">Hide Markup Button</label></th>\
                    <td><input type="checkbox" id="viewmedica_markup" name="markup" value="false" /><br />\
                    <small>Markup mode button is not visible for the user</small></td>\
                </tr>\
                <tr>\
                    <th><label for="viewmedica-sections">Hide Sections Button</label></th>\
                    <td><input type="checkbox" id="viewmedica_sections" name="sections" value="false" /><br />\
                    <small>Sections button is not visible for the user</small></td>\
                </tr>\
                <tr>\
                    <th><label for="viewmedica-sharing">Hide Sharing Button</label></th>\
                    <td><input type="checkbox" id="viewmedica_sharing" name="sharing" value="false" /><br />\
                    <small>Sharing button is not visible for the user</small></td>\
                </tr>\
            </table>\
            <p class="submit">\
                <input type="button" id="viewmedica-submit" class="button-primary" value="Insert Viewmedica Shortcode" name="submit" />\
            </p>\
        </div>');

        jQuery.ajax({
            dataType: "json",
            url: 'https://swarminteractive.com/vm/index/client_json/' + vm_client,
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
                    'audio': '',
                    'autoplay': '',
                    'captions': '',
                    'subtitles': '',
                    'markup': '',
                    'sections': '',
                    'sharing': ''
                };
                var shortcode = '[viewmedica';

                for( var index in options) {
                    var value = table.find('#viewmedica_' + index).val();
                    if (table.find('#viewmedica_'+index).is(':checkbox')) {
                      // attaches the attribute to the shortcode only if it's different from the default value
                      if ( table.find('#viewmedica_'+index).is(':checked') && value != '' )
                          shortcode += ' ' + index + '="' + value + '"';
                   } else {
                     // attaches the attribute to the shortcode only if it's different from the default value
                     if ( value !== options[index] && value != '' )
                       shortcode += ' ' + index + '="' + value + '"';
                     }
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
