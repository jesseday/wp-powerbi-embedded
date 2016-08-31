<?php
/**
 * @file
 * Metabox output for powerbi embedded report metaboxes.
 */
?>
<input type="hidden" name="<?php print $metabox_noncename . '_nonce'; ?>" id="<?php print $metabox_noncename . '_nonce'; ?>" value="<?php print _powerbi_embedded_create_nonce(); ?>" />
<?php foreach($fields as $field): ?>
  <?php print $field; ?>
<?php endforeach; ?>