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

</div>

</div>
