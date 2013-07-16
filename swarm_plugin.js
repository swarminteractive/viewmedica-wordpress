(function() {
	tinymce.create('tinymce.plugins.ViewMedica', {
		
		init : function(ed, url) {
			
			ed.addButton('viewmedica', {
				title : 'viewmedica.embed',
				image : url+'/viewmedica.png',
				onclick : function() {
					var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
					W = W - 80;
					H = H - 84;
					tb_show('Viewmedica Options', '#TB_inline?width='+W+'&height='+H+'&inlineId=viewmedica-form');
					/*var clientid = prompt("Client ID", "Enter the id of your swarm account");
					if (clientid != null && clientid != 'undefined')
						ed.execCommand('mceInsertContent', false, '[viewmedica client="'+clientid+'"]');*/
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

jQuery( function() {

	var form = jQuery('<div id="viewmedica-form"><table id="viewmedica-table" class="form-table">\
			<tr>\
				<th><label for="viewmedica-openthis">Openthis Code</label></th>\
				<td><input type="text" id="viewmedica_openthis" name="openthis" value="" /><br />\
				<small>Input desired opening screen, or leave blank for main</small></td>\
			</tr>\
			<tr>\
				<th><label for="viewmedica-menuaccess">Turn Off Menu Access</label></th>\
				<td><input type="checkbox" id="viewmedica_menuaccess" name="menuaccess" value="false" /><br />\
				<small>Do not let users navigate through ViewMedica</small></td>\
			</tr>\
			<tr>\
				<th>Get a List of Your Animation Codes</th>\
				<td><a href="http://swarminteractive.com/vm/login" target="_blank">Login to your Swarm Interactive account</a> and click Installation Support.</td>\
			</tr>\
		</table>\
		<p class="submit">\
			<input type="button" id="viewmedica-submit" class="button-primary" value="Insert Viewmedica Shortcode" name="submit" />\
		</p>\
		</div>');
		
	var table = form.find('table');
	form.appendTo('body').hide();
		
	form.find('#viewmedica-submit').click(function(){
			var options = { 
				'openthis': '',
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