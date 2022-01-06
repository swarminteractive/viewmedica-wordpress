<?php

global $wpdb;
$table_name = $wpdb->prefix . 'viewmedica';

if ($_POST['swarm_hidden'] == 'C') {

  swarm_nag_ignore(true);

  $client = $_POST['vm_id'];

  $sql = "UPDATE " . $table_name . "
    SET vm_id = " . $client . "
    WHERE id = 1";

  $wpdb->query($sql);
  $updated = true;

  $sql = "SELECT * FROM " . $table_name . "
    WHERE id = 1";

  $result = $wpdb->get_results($sql, 'OBJECT');
  $result = $result[0];
  $width = $result->vm_width;
  $secure = $result->vm_secure;
  $brochures = $result->vm_brochures;
  $fullscreen = $result->vm_fullscreen;
  $disclaimer = $result->vm_disclaimer;
  $visible = $result->vm_visible;
  $language = $result->vm_language;

} else if($_POST['swarm_hidden'] == 'Y') {

  swarm_nag_ignore(true);

  $width = $_POST['vm_width'];
  $language = $_POST['vm_language'];
  $secure = @$_POST['vm_secure'] == '1' ? 1 : 0;
  $brochures = @$_POST['vm_brochures'] == '1' ? 1 : 0;
  $fullscreen = @$_POST['vm_fullscreen'] == '1' ? 1 : 0;
  $disclaimer = @$_POST['vm_disclaimer'] == '1' ? 1 : 0;
  $visible = @$_POST['vm_visible'] == '1' ? 1 : 0;

  $sql = "UPDATE " . $table_name . "
    SET vm_width = " . $width . ",
    vm_secure = " . $secure . ",
    vm_brochures = " . $brochures . ",
    vm_fullscreen = " . $fullscreen . ",
    vm_disclaimer = " . $disclaimer . ",
    vm_visible = " . $visible . ",
    vm_language = '" . $language . "'
    WHERE id = 1";

  $wpdb->query($sql);
  $updated = true;

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

} else if($_POST['swarm_hidden'] == 'P') {

  $json_string = "https://api.viewmedica.com/wordpress/users/".$_POST['swarm_id']."/profile";

  $rCURL = curl_init();

  curl_setopt($rCURL, CURLOPT_URL, $json_string);
  curl_setopt($rCURL, CURLOPT_HEADER, 0);
  curl_setopt($rCURL, CURLOPT_RETURNTRANSFER, 1);

  $jsondata = curl_exec($rCURL);

  curl_close($rCURL);

  $data = json_decode ($jsondata);

  $content = "[viewmedica]<br /><hr />\r\n\r\n<div>";
  $size = $_POST['vm_size'];
  $float = $_POST['vm_thumbnail'];
  $format = $_POST['vm_format'];

  $itemDiv = "<div class=\"vm-item\" style=\"display: inline-block;\">\r\n\r\n";
  $groupDiv = "<div class=\"vm-group\">\r\n\r\n";
  $collectionDiv = "<div class=\"vm-collection\">\r\n\r\n";
  $closeDiv = "</div>\r\n\r\n";
  $closeItemDiv = "</div>\r\n\r\n";

  if ($format == "list") {
    $itemList = "<ul class=\"vm-list\">\r\n\r\n";
    $closeList = "</ul>\r\n\r\n";
    $itemDiv = "";
    $closeItemDiv = "";
  } else {
    $itemList = "";
    $closeList = "";
  }

  function collectionTitle($label) {
    return "<h2>".$label."</h2>\r\n\r\n";
  }

  function groupTitle($label) {
    return "<h4>".$label."</h4>\r\n\r\n";
  }

  function child($key, $label, $file, $size, $float, $description, $format, $level) {
    $itemTitle = itemTitle($key, $label, $format, $level);
    $itemFile = itemFile($key, $label, $file, $size, $float);
    $itemDescription = itemDescription($description);
    if ($format == "div") {
      return $itemTitle.$itemFile.$itemDescription;
    } else {
      return $itemTitle;
    }
  }

  function itemTitle($key, $label, $format, $level) {
    if ($format == "div") {
      return "<p><a href=\"#\" class=\"vm-link level-" . $level . "\" data-video=\"" . $key . "\" >" . $label. "</a></p>\r\n\r\n";
    } else {
      return "<li><a href=\"#\" class=\"vm-link level-" . $level . "\" data-video=\"" . $key . "\" >" . $label . "</a></li>\r\n\r\n";
    }
  }

  function itemFile($key, $label, $file, $size, $float) {
    $img = "class=\"vm-link vm-image\" data-video=\"".$key."\" src=\"https://api.viewmedica.com/thumbs/".$file."_".$size.".jpg\" alt=\"".$label."\"";
    if ($float == "left") {
      $style = "style=\"padding: 0px 15px 0px 0px; float: left;\"";
    } else if ($float == "right") {
      $style = "style=\"padding: 0px 0px 0px 15px; float: right;\"";
    } else {
      return "";
    }
    return "<a href=\"#\"><img ".$img.$style.$close." /></a>\r\n\r\n";
  }

  function itemDescription($description) {
    return "<p>".$description."</p>\r\n\r\n";
  }

  function handleItem($item, $level, $format, $size, $float) {
    if (!$item->children) {
      return child($item->code, $item->labels->en, $item->file, $size, $float, $item->descriptions->en, $format, $level);
    }
    
    $children = "<div class=\"level-" . $level . "\">";
      
    foreach($item->children as $child) {
      $children .= handleItem($child, $level + 1, $format, $size, $float);
    }
      
    $children .= "</div>";

    $headingOpen = "<p>";

    $headingClose = "</p>";

    if ($level > 0 && $level < 7) {
      $headingOpen = "<h".$level.">";

      $headingClose =  "</h".$level.">";
    }
    
    return $headingOpen . "<a href=\"#\" class=\"vm-link level-" . $level . "\" data-video=\"" . $item->code . "\" >" . $item->labels->en . "</a>" . $headingClose . "\r\n\r\n" . $children;
  }
  
  foreach($data as $item) {
    $content .= handleItem($item, 1, $format, $size, $float);
  }

  $content .= "</div";
  
  $post = array(
    'post_title' => $_POST['vm_page'],
    'post_content' => $content,
    'post_type' => 'page'
  );

  wp_insert_post($post);

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
  <img src="../wp-content/plugins/viewmedica/vm-logo.png" width="200px" height="auto" style="padding: 10px;" />
<div>

<div class="col-1-3 mobile-col-1-1">
  <div id="vm_id_div" class="content">
    <h2 class="vm_title">Get Started</h2>
    <p>Please enter your Client ID to begin using this plugin. You can find your Client ID by logging into your account at <a href="https://my.viewmedica.com" target="_blank">ViewMedica.com</a></p>
    <p id="vm_id_error" style="color: red;"></p>
    <form name="swarm_admin" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
      <input type="hidden" name="swarm_hidden" value="C">
      <table class="form-table">
        <tr>
          <th scope="row"><label for="vm_id"><?php _e('Client ID') ?></label></th>
          <td><input id="vm_id" type="text" name="vm_id" value="<?php echo $client; ?>" size="14" aria-describedby="client-id-description"><p class="description" id="client-id-description">verifying...</p></td>
        </tr>
      </table>
      <input type="submit" name="Submit" value="<?php _e('Set Client ID', 'swarm_trdom' ) ?>" class="button button-primary" style="margin-top: 20px;" />
    </form>
  </div>

  <div id="welcome" class="content">
    <h2 class="vm_title">Welcome</h2>
    <p>Thank you for installing ViewMedica! If this is your first time using this plugin, watch the video below to get started. And, read about some of the plugin’s helpful features below.</p>
    <div class="videoWrapper">
      <iframe width="100%" height="auto" src="//www.youtube.com/embed/jJB6SfZBe60?rel=0&hd=1&showinfo=0" frameborder="0" allowfullscreen></iframe>
    </div>
    <h4><strong>Create a page with links to your ViewMedica content</strong></h4>
    <p>Use the plugin’s Page Generator to instantly make a Web page that features all of your ViewMedica videos. You can choose to show your video links in list form, or with a thumbnail image and description.</p>
    <h4><strong>Change ViewMedica player settings globally</strong></h4>
    <p>The Global Options settings change the behavior of ViewMedica across your entire site. Set things like your player width, default language and other features.</p>
    <h4><strong>Change player settings for a single embed</strong></h4>
    <p>The Inline Options settings give you control of individual ViewMedica embeds on your site. For example, you may want to disable menu access on a page so your users only see one specific video. Checkout the Shortcode Generator to see the options available, or head to a post and use our inline tool.</p>
  </div>
</div>

<div class="col-1-3 mobile-col-1-1">
  <div id="globalOptions" class="content disabled">
    <h2 class="vm_title"><?php _e('Global Options') ?></h2>
    <form name="swarm_admin" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
      <input type="hidden" name="swarm_hidden" value="Y">
      <table class="form-table">
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
          <td>
            <select name="vm_secure">
              <option value="1" <?php if($secure == 1) echo 'selected'; ?>><?php _e('On') ?></option>
              <option value="0" <?php if($secure == 0) echo 'selected'; ?>><?php _e('Off') ?></option>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="vm_brochures"><?php _e('Brochures') ?></label></th>
          <td>
            <select name="vm_brochures">
              <option value="1" <?php if($brochures == 1) echo 'selected'; ?>><?php _e('Show') ?></option>
              <option value="0" <?php if($brochures == 0) echo 'selected'; ?>><?php _e('Hide') ?></option>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="vm_fullscreen"><?php _e('Fullscreen') ?></label></th>
          <td>
            <select name="vm_fullscreen">
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
          <td>
            <select name="vm_language">
              <option value="en" <?php if($language == 'en') echo 'selected'; ?>><?php _e('English') ?></option>
              <option value="es" <?php if($language == 'es') echo 'selected'; ?>><?php _e('Spanish') ?></option>
              <option value="de" <?php if($language == 'de') echo 'selected'; ?>><?php _e('German') ?></option>
            </select>
          </td>
        </tr>
    </table>
    <input id="updateOptionsButton" type="submit" name="Submit" value="<?php _e('Update Options', 'swarm_trdom' ) ?>" class="button button-primary" style="margin-top: 20px;" />
  </form>
</div>

  <div id="pageGenerator" class="content disabled">
    <h2 class="vm_title"><?php _e('Page Generator') ?></h2>
    <form name="swarm_admin" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
      <input type="hidden" name="swarm_hidden" value="P">
      <input type="hidden" name="swarm_id" value="<?php echo $client; ?>">
      <p>This will create a page based on your current ViewMedica selections. If you update your selections in your <a href="https://my.viewmedica.com/" target="_blank">ViewMedica.com</a> account, you will need to regenerate the page.</p>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="vm_page">Page Name</label></td>
          <td><input type="text" name="vm_page" /></td>
        </tr>
        <tr>
          <th scope="row"><label for="vm_format">Format</label></th>
          <td>
            <select name="vm_format" onchange="pageOptions()">
              <option value="div">Div</option>
              <option value="list">List</option>
            </select>
          </td>
        </tr>
        <tr id="vm_thumbnail">
          <th scope="row"><label for="vm_thumbnail">Image</label></th>
          <td>
            <select name="vm_thumbnail" onchange="imageOptions()">
              <option value="left">Float Left</option>
              <option value="right">Float Right</option>
              <option value="hide">Hide</option>
            </select>
          </td>
        </tr>
        <tr id="vm_size">
          <th scope="row"><label for="vm_size">Image Size</label></th>
          <td>
          <select name="vm_size">
            <option value="120">120px</option>
            <option value="300">300px</option>
          </select>
          </td>
        </tr>
      </table>
      <input id="generatePageButton" class="button button-primary" style="margin-top: 20px;" type="submit" name="Submit" value="Generate Page" />
    </form>
  </div>
</div>

<div class="col-1-3 mobile-col-1-1">
  <div id="inlineOptions" class="content disabled" style="background-color: lightgray;">
    <h2 class="vm_title"><?php _e('Shortcode Generator') ?></h2>
    <p>As you change options below, the embed shortcode will automatically update. When your options are set, copy &amp; paste the shortcode into your post.</p>
    <hr />
    <p id="shortCode" style="font-family: courier;">[viewmedica]</p>
    <hr />

    <form id="shortcodeGenerator" name="shortcodeGenerator">
      <h4 style="font-size: 14px; font-weight: 600;">Location</h4>
      <select id="vm_location" name="vm_location" onchange="updateCode();generateShortCode();" style="width: 98%">
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
                 <option value="0"><?php _e('Off') ?></option>
                 <option value="1"><?php _e('On') ?></option>
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
    <button id="shortcodeButton" class="button button-primary" style="margin-top: 20px;" onclick="resetForm()">Reset</button>
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
    if (vm_subtitles_v == 1) {
      vm_subtitles = ' subtitles="true"';
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
        url: 'https://api.viewmedica.com/wordpress/users/' + vm_id + '/profile',
        success: function(data) {
          var client_id_description = document.getElementById('client-id-description');
          var globalOptions = document.getElementById('globalOptions');
          var pageGenerator = document.getElementById('pageGenerator');
          var shortcodeGenerator = document.getElementById('inlineOptions');
          client_id_description.style.color = "#6adc6a";
          client_id_description.innerHTML = "valid Client ID";
          globalOptions.classList.remove('disabled');
          pageGenerator.classList.remove('disabled');
          shortcodeGenerator.classList.remove('disabled');
          build(data);
        },
        error: function() {
          var client_id_description = document.getElementById('client-id-description');
          var generatePageButton = document.getElementById('generatePageButton');
          var updateOptionsButton = document.getElementById('updateOptionsButton');
          var shortcodeButton = document.getElementById('shortcodeButton');
          client_id_description.style.color = "red";
          if (vm_id == "") {
            client_id_description.innerHTML = "required";
          } else {
            client_id_description.innerHTML = "invalid Client ID";
          }
          generatePageButton.disabled = true;
          updateOptionsButton.disabled = true;
          shortcodeButton.disabled = true;
        }
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

  function build(profile) {
    var html = '<option value="">Main Embed</option>';

    profile.forEach((item) => {
      html += handleItem(item, 1); 
    });

    var vm_location = document.getElementsByName('vm_location')[0];
    vm_location.innerHTML = html;
  }

  window.onload = fetch();
  window.onload = pageOptions();
</script>
