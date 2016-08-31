<?php
/**
 * @file
 * The powerbi embedded settings form.
 *
 * @see powerbi_embedded_settings_page()
 */
?>
<div class="wrap">
  <h2>PowerBi Embedded Settings</h2>

  <form method="post" action="options.php">
    <?php print settings_fields('powerbi-embedded-settings-group'); ?>
    <?php print do_settings_sections('powerbi-embedded-settings-group' ); ?>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">Workspace Collection Name</th>
        <td>
          <input type="text" name="powerbi_embedded_workspace_collection_name" value="<?php echo esc_attr( get_option('powerbi_embedded_workspace_collection_name') ); ?>" />
          <br />
          <div class="description">Provide the workdspace Collection name that includes the workspaces you wish to use.</div>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">Workspace id</th>
        <td>
          <input type="text" name="powerbi_embedded_workspace_id" value="<?php echo esc_attr( get_option('powerbi_embedded_workspace_id') ); ?>" />
          <br />
          <div class="description">Provide the workdspace id to request reports from.</div>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">App Key 1</th>
        <td>
          <input type="password" name="powerbi_embedded_app_key_1" value="<?php echo esc_attr( get_option('powerbi_embedded_app_key_1') ); ?>" />
          <br />
          <div class="description">One of your two powerbi app keys</div>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">App Key 2</th>
        <td>
          <input type="password" name="powerbi_embedded_app_key_2" value="<?php echo esc_attr( get_option('powerbi_embedded_app_key_2') ); ?>" />
          <br />
          <div class="description">One of your two powerbi app keys</div>
        </td>
      </tr>
    </table>

    <?php submit_button(); ?>
  </form>
</div>
