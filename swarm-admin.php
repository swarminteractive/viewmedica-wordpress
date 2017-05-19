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

        $sql = "UPDATE " . $table_name . "
                SET vm_id = " . $client . ",
                vm_width = " . $width . ",
                vm_secure = " . $secure . ",
                vm_brochures = " . $brochures . ",
                vm_fullscreen = " . $fullscreen . ",
                vm_disclaimer = " . $disclaimer . ",
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
        $language = $result->vm_language;
        $updated = false;

    }
?>

<div class="wrap">

<?php if($updated) {

    echo '<div id="message" class="updated"><p>Your preferences were successfully updated</p></div>';

} else {


} ?>

<?php echo "<h2>" . __( 'ViewMedica Options', 'swarm_trdom' ) . "</h2>"; ?>
    <p>Please insert your ViewMedica Client ID to begin using this plugin.</p>
    <form name="swarm_admin" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <input type="hidden" name="swarm_hidden" value="Y">
        <p><?php _e("Client ID: " ); ?><input type="text" name="vm_id" value="<?php echo $client; ?>" size="20"><?php _e(" required" ); ?></p>

        <p><?php _e("Width: " ); ?><input type="text" name="vm_width" value="<?php echo $width; ?>" size="20"><?php _e(" default: 580" ); ?></p>
        <p><input type="checkbox" name="vm_secure" value="1" <?php if($secure == 1) echo 'checked '; ?>size="20"> <?php _e("Secure Embed" ); ?><?php _e(" (only for https sites)" ); ?></p>
        <p><input type="checkbox" name="vm_brochures" value="1" <?php if($brochures == 1) echo 'checked '; ?>size="20"> <?php _e("Show Brochures" ); ?></p>
        <p><input type="checkbox" name="vm_fullscreen" value="1" <?php if($fullscreen == 1) echo 'checked '; ?>size="20"> <?php _e("Show Fullscreen" ); ?></p>
        <p><input type="checkbox" name="vm_disclaimer" value="1" <?php if($disclaimer == 1) echo 'checked '; ?>size="20"> <?php _e("Show Disclaimer" ); ?></p>
        <p><select name="vm_language">
            <option value="en"<?php if($language=='en') echo ' selected'; ?>>English</option>
            <option value="es"<?php if($language=='es') echo ' selected'; ?>>Spanish</option>
            <option value="de"<?php if($language=='de') echo ' selected'; ?>>German</option>
        </select></p>
        <p class="submit">
        <input type="submit" name="Submit" value="<?php _e('Update Options', 'swarm_trdom' ) ?>" />
        </p>
    </form>
</div>
