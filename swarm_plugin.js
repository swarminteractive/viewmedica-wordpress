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
                author : 'ViewMedica',
                authorurl : 'https://viewmedica.com/',
                infourl : 'https://viewmedica.com/',
                version : "1.2"
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
                    <td><input type="text" id="viewmedica_width" name="width" value="" />px<br />\
                    <small>Set a maximum width for the ViewMedica content in pixels. Leave blank for default set on options page.</small></td>\
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
                    <th><label for="viewmedica-captions">Hide Captions Button</label></th>\
                    <td><input type="checkbox" id="viewmedica_captions" name="captions" value="false" /><br />\
                    <small>Closed captioning button is not visible for the user</small></td>\
                </tr>\
                <tr>\
                    <th><label for="viewmedica-subtitles">Show Subtitles</label></th>\
                    <td><input type="checkbox" id="viewmedica_subtitles" name="subtitles" value="true" /><br />\
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
            url: 'https://api.viewmedica.com/wordpress/users/' + vm_client + '/profile',
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
                    'captions': '',
                    'subtitles': '',
                    'markup': '',
                    'sections': '',
                    'sharing': '',
					'menuaccess': ''
                };
                var shortcode = '[viewmedica';

                for( var index in options) {
                    var value = table.find('#viewmedica_' + index).val();
                    if (table.find('#viewmedica_'+index).is(':checkbox')) {
                      // attaches the attribute to the shortcode only if it's different from the default value
                      if ( table.find('#viewmedica_'+index).is(':checked') && value != '' ) {
                            shortcode += ' ' + index + '="' + value + '"';
                      }
                   } else {
                     // attaches the attribute to the shortcode only if it's different from the default value
                     if ( value !== options[index] && value != '' )
                        if (index === 'width') {
                            shortcode += ' ' + index + '="' + parseInt(value) + '"';
                        } else {
                            shortcode += ' ' + index + '="' + value + '"';
                        }
                       
                     }
                }

                shortcode += ']';

                // inserts the shortcode into the active editor
                tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

                // closes Thickbox
                tb_remove();
        });

    });

}

function handleItem(item, level) {
    if (!item.children) {
        return `<option value="${item.code}">${"--".repeat(level)} ${item.labels.en}</option>`;
    }

    var children = "";

    item.children.forEach((child) => {
        children += handleItem(child, level + 1);
    });

    return `<option value="${item.code}">${"--".repeat(level)} ${item.labels.en}</option>` + children;

}

function build(profile, form) {
    var html = '<select id="vm-open" style="width: 100%"><option value="">Main Embed</option>\
                    ';
    profile.forEach((item) => {
        html += handleItem(item, 1); 
    });

    html+= '</select>';

    document.querySelector("#open-selector").innerHTML = html;

    document.querySelector("#vm-open").addEventListener("change", function(e) {
        document.querySelector("#viewmedica_openthis").value = e.target.value;
    })
}