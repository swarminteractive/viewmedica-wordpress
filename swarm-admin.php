<?php

    global $wpdb;
    $table_name = $wpdb->prefix . 'viewmedica';

    if($_POST['swarm_hidden'] == 'Y') {

        swarm_nag_ignore(true);

        $client = $_POST['vm_id'];
        $width = $_POST['vm_width'];
        $language = $_POST['vm_language'];
        $secure = @$_POST['vm_secure'] == '1' ? 1 : 0;
        $brochures = @$_POST['vm_brochures'] == '1' ? 1 : 0;
        $fullscreen = @$_POST['vm_fullscreen'] == '1' ? 1 : 0;
        $disclaimer = @$_POST['vm_disclaimer'] == '1' ? 1 : 0;
        $visible = @$_POST['vm_visible'] == '1' ? 1 : 0;

        $sql = "UPDATE " . $table_name . "
                SET vm_id = " . $client . ",
                vm_width = " . $width . ",
                vm_secure = " . $secure . ",
                vm_brochures = " . $brochures . ",
                vm_fullscreen = " . $fullscreen . ",
                vm_disclaimer = " . $disclaimer . ",
                vm_visible = " . $visible . ", 
                vm_language = '" . $language . "'
                WHERE id = 1";

        $wpdb->query($sql);
        $updated = true;

    } else {

        $sql = "SELECT * FROM " . $table_name . "
                WHERE id = 1";
        $result = $wpdb->get_results($sql, 'OBJECT');
        $result = $result[0];
        $client = $result->vm_id;
        $width = $result->vm_width;
        $secure = $result->vm_secure;
        $brochures = $result->vm_brochures;
        $fullscreen = $result->vm_fullscreen;
        $disclaimer = $result->vm_disclaimer;
        $visible = $result->vm_visible;
        $language = $result->vm_language;
        $updated = false;

    }
?>

<link rel="stylesheet" type="text/css" href="../wp-content/plugins/viewmedica/viewmedica.css">

<div class="wrap">

<div>

<div class="col-1-3 mobile-col-1-1">

<div class="content">

    <h2><?php _e('Global Options') ?></h2>

    <form name="swarm_admin" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">

    <input type="hidden" name="swarm_hidden" value="Y">

    <table class="form-table">
    <tr>
        <th scope="row"><label for="vm_id"><?php _e('Client ID') ?></label></th>
        <td><input type="text" name="vm_id" value="<?php echo $client; ?>" size="14" aria-describedby="client-id-description"><p class="description" id="client-id-description">required</p></td>
    </tr>
    <tr>
        <th scope="row"><label for="vm_width"><?php _e('Width') ?></label></th>
        <td><input type="text" name="vm_width" value="<?php echo $width; ?>" size="14" aria-describedby="width-description"><p class="description" id="width-description">720px by default</p>
    </tr>

    <tr>
    <th scope="row"><label for="vm_visible"><?php _e('ViewMedica') ?></label></th>
    <td>
        <select name="vm_visible">
        <option value="1" <?php if($visible == 1) echo 'selected'; ?>><?php _e('Show') ?></option>
          <option value="0" <?php if($visible == 0) echo 'selected'; ?>><?php _e('Hide') ?></option>
        </select>
    </td>
    </tr>
    <tr>
    <th scope="row"><label for="vm_secure"><?php _e('Secure') ?></label></th>
    <td><select name="vm_secure">
          <option value="1" <?php if($secure == 1) echo 'selected'; ?>><?php _e('On') ?></option>
          <option value="0" <?php if($secure == 0) echo 'selected'; ?>><?php _e('Off') ?></option>
        </select>
    </tr>
    <tr>
    <th scope="row"><label for="vm_brochures"><?php _e('Brochures') ?></label></th>
    <td><select name="vm_brochures">
          <option value="1" <?php if($brochures == 1) echo 'selected'; ?>><?php _e('Show') ?></option>
          <option value="0" <?php if($brochures == 0) echo 'selected'; ?>><?php _e('Hide') ?></option>
        </select>
    </td>
    </tr>
    <tr>
    <th scope="row"><label for="vm_fullscreen"><?php _e('Fullscreen') ?></label></th>
    <td><select name="vm_fullscreen">
          <option value="1" <?php if($fullscreen == 1) echo 'selected'; ?>><?php _e('Show') ?></option>
          <option value="0" <?php if($fullscreen == 0) echo 'selected'; ?>><?php _e('Hide') ?></option>
        </select>
    </td>
    </tr>
    <tr>
    <th scope="row"><label for="vm_disclaimer"><?php _e('Disclaimer') ?></label></th>
    <td>
       <select name="vm_disclaimer">
          <option value="1" <?php if($disclaimer == 1) echo 'selected'; ?>><?php _e('Show') ?></option>
          <option value="0" <?php if($disclaimer == 0) echo 'selected'; ?>><?php _e('Hide') ?></option>
        </select>
    </td>
    </tr>
    <tr>
    <th scope="row"><label for="vm_language"><?php _e('Language') ?></label></th>
    <td><select name="vm_language">
          <option value="en" <?php if($language == 'en') echo 'selected'; ?>><?php _e('English') ?></option>
          <option value="es" <?php if($language == 'es') echo 'selected'; ?>><?php _e('Spanish') ?></option>
          <option value="de" <?php if($language == 'de') echo 'selected'; ?>><?php _e('German') ?></option>
        </select>
    </td>
    </tr>
    </table>

    <input type="submit" name="Submit" value="<?php _e('Update Options', 'swarm_trdom' ) ?>" class="button button-primary" style="margin-top: 20px;" />

    </form>

  </div>

</div>

<div class="col-1-3 mobile-col-1-1">

  <div class="content" style="background-color: lightgray;">

    <h2><?php _e('Shortcode Generator') ?></h2>

<p>As you change various options the shortcode below will automatically update. When you have selected your desired optoins, copy &amp; paste the code into your post.</p>

<hr />

<p id="shortCode" style="font-family: courier;">[viewmedica]</p>

<hr />

<form id="shortcodeGenerator" name="shortcodeGenerator">
            <h4 style="font-size: 14px; font-weight: 600;">Location</h4>
            <select id="vm_location" name="vm_location" onchange="generateShortCode();updateCode();" style="width: 98%">
            </select>
 <table class="form-table">
<tbody>
					<tr>
						<th><label for="viewmedica-openthis">Code</label></th>
						<td><input id="viewmedica_openthis" name="vm_code" type="text" value="" onchange="generateShortCode()" style="width: 100%;"></td>
          </tr>
					<tr>
						<th><label for="viewmedica-width">Width</label></th>
						<td><input id="viewmedica_width" name="vm_width" type="text" value=""onchange="generateShortCode()" style="width: 100%;"></td>
          </tr>
					<tr>
						<th><label for="viewmedica-menuaccess">Menu Access</label></th>
            <td>
                <select name="vm_menu" onchange="generateShortCode()">
                  <option value="1"><?php _e('On') ?></option>
                  <option value="0"><?php _e('Off') ?></option>
                </select>
            </td>
					</tr>
					<tr>
            <th><label for="viewmedica-audio">Audio</label></th>
            <td>
                <select name="vm_audio" onchange="generateShortCode()">
                  <option value="1"><?php _e('On') ?></option>
                  <option value="0"><?php _e('Off') ?></option>
                </select>
            </td>
					</tr>
					<tr>
            <th><label for="viewmedica-autoplay">Autoplay Video</label></th>
            <td>
                <select name="vm_autoplay" onchange="generateShortCode()">
                  <option value="0"><?php _e('Off') ?></option>
                  <option value="1"><?php _e('On') ?></option>
                </select>
            </td>
					</tr>
					<tr>
						<th><label for="viewmedica-subtitles">Subtitles</label></th>
            <td>
                <select name="vm_subtitles" onchange="generateShortCode()">
                   <option value="1"><?php _e('On') ?></option>
                   <option value="0"><?php _e('Off') ?></option>
                </select>
            </td>
          </tr>
					<tr>
						<th><label for="viewmedica-markup">Markup</label></th>
						<td>
                <select name="vm_markup" onchange="generateShortCode()">
                  <option value="1"><?php _e('Show') ?></option>
                  <option value="0"><?php _e('Hide') ?></option>
                </select>
            </td>
					</tr>
					<tr>
						<th><label for="vm_sections">Sections</label></th>
					  <td>
                <select name="vm_sections" onchange="generateShortCode()">
                  <option value="1"><?php _e('Show') ?></option>
                  <option value="0"><?php _e('Hide') ?></option>
                </select>
            </td>
					</tr>
					<tr>
						<th><label for="vm_sharing">Sharing</label></th>
						<td>
                <select name="vm_sharing" onchange="generateShortCode()">
                  <option value="1"><?php _e('Show') ?></option>
                  <option value="0"><?php _e('Hide') ?></option>
                </select>
            </td>
          </tr>
            <tr>
						<th><label for="vm_captions">Captions</label></th>
            <td>
                <select name="vm_captions" onchange="generateShortCode()">
                  <option value="1">Show</option>
                  <option value="0"><?php _e('Hide') ?></option>
                </select>
            </td>
					</tr>
				</tbody>
      </table>
</form>

<button class="button button-primary" style="margin-top: 20px;" onclick="resetForm()">Reset</button>

</div>

</div>

</div>

</div>

<script type="text/javascript">

  function pageOptions() {
    var vm_format_e = document.getElementsByName('vm_format')[0];
    var vm_format_v = vm_format_e.options[vm_format_e.selectedIndex].value;

    var vm_thumbnail = document.getElementById('vm_thumbnail');
    var vm_size = document.getElementById('vm_size');

    if (vm_format_v == "list") {
        vm_thumbnail.style.display = 'none';
        vm_size.style.display = 'none';
    } else {
        vm_thumbnail.style.display = 'table-row';
        vm_size.style.display = 'table-row';
    }
  }

  function imageOptions() {
    var vm_thumbnail_e = document.getElementsByName('vm_thumbnail')[0];
    var vm_thumbnail_v = vm_thumbnail_e.options[vm_thumbnail_e.selectedIndex].value;
    var vm_size = document.getElementById('vm_size');

    if (vm_thumbnail_v == "hide") {
        vm_size.style.display = 'none';
    } else {
        vm_size.style.display = 'table-row';
    }
  }

  function generateShortCode() {

    var vm_location_e = document.getElementsByName('vm_location')[0];
    var vm_location_v = vm_location_e.options[vm_location_e.selectedIndex].value;

    var vm_code_e = document.getElementsByName('vm_code')[0];
    var vm_code_v = vm_code_e.value;

    var vm_open;

    if (vm_code_v == '' && vm_location_v == '') {
        vm_open = '';
    } else if (vm_code_v == '') {
        vm_open = ' openthis="' + vm_location_v + '"';
    } else {
        vm_open = ' openthis="' + vm_code_v + '"';
    }

    var vm_width_e = document.getElementsByName('vm_width')[1];
    var vm_width_v = vm_width_e.value;

    var vm_width;

    if (vm_width_v == '') {
        vm_width = '';
    } else {
        vm_width = ' width="' + vm_width_v + '"';
    }

    var vm_menu_e = document.getElementsByName('vm_menu')[0];
    var vm_menu_v = vm_menu_e.options[vm_menu_e.selectedIndex].value;
    var vm_menu = '';
    if (vm_menu_v == 0) {
       vm_menu = ' menuaccess="false"';
    }

    var vm_audio_e = document.getElementsByName('vm_audio')[0];
    var vm_audio_v = vm_audio_e.options[vm_audio_e.selectedIndex].value;
    var vm_audio = '';
    if (vm_audio_v == 0) {
       vm_audio = ' audio="false"';
    }

    var vm_autoplay_e = document.getElementsByName('vm_autoplay')[0];
    var vm_autoplay_v = vm_autoplay_e.options[vm_autoplay_e.selectedIndex].value;
    var vm_autoplay = '';
    if (vm_autoplay_v == 1) {
       vm_autoplay = ' autoplay="true"';
    }

    var vm_subtitles_e = document.getElementsByName('vm_subtitles')[0];
    var vm_subtitles_v = vm_subtitles_e.options[vm_subtitles_e.selectedIndex].value;
    var vm_subtitles = '';
    if (vm_subtitles_v == 0) {
       vm_subtitles = ' subtitles="false"';
    }

    var vm_markup_e = document.getElementsByName('vm_markup')[0];
    var vm_markup_v = vm_markup_e.options[vm_markup_e.selectedIndex].value;
    var vm_markup = '';
    if (vm_markup_v == 0) {
       vm_markup = ' markup="false"';
    }

    var vm_sections_e = document.getElementsByName('vm_sections')[0];
    var vm_sections_v = vm_sections_e.options[vm_sections_e.selectedIndex].value;
    var vm_sections = '';
    if (vm_sections_v == 0) {
       vm_sections = ' sections="false"';
    }

    var vm_sharing_e = document.getElementsByName('vm_sharing')[0];
    var vm_sharing_v = vm_sharing_e.options[vm_sharing_e.selectedIndex].value;
    var vm_sharing = '';
    if (vm_sharing_v == 0) {
       vm_sharing = ' sharing="false"';
    }

    var vm_captions_e = document.getElementsByName('vm_captions')[0];
    var vm_captions_v = vm_captions_e.options[vm_captions_e.selectedIndex].value;
    var vm_captions = '';
    if (vm_captions_v == 0) {
       vm_captions = ' captions="false"';
    }

    var shortCode = '[viewmedica' + vm_open + vm_width + vm_menu + vm_audio + vm_autoplay + vm_subtitles + vm_markup + vm_sections + vm_sharing + vm_captions + ']';

    document.getElementById('shortCode').innerHTML = shortCode;

  }

  function updateCode() {
    document.getElementsByName('vm_code')[0].value = document.getElementsByName('vm_location')[0].value;
  }

  function resetForm() {
    document.getElementById('shortcodeGenerator').reset();
    generateShortCode();
  }


  function fetch() {

  var vm_id = document.getElementsByName('vm_id')[0].value;

  jQuery.ajax({
            dataType: "json",
              url: 'https://swarminteractive.com/vm/index/client_json/' + vm_id ,
              success: function(data) {
                build(data);
              }
            });
  }

  function build(profile) {

    var html = '<option value="">Main Embed</option>';

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

    var vm_location = document.getElementsByName('vm_location')[0];

    vm_location.innerHTML = html;

  }

  window.onload = fetch();

  window.onload = pageOptions();

</script>
