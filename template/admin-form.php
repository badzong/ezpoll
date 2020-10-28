<div class="wrap">
  <h1>
    <?php if ($edit): ?>
      <?php _e('Edit poll', 'ezpoll'); ?>
    <?php else: ?>
      <?php _e('Create poll', 'ezpoll'); ?>
    <?php endif; ?>
  </h1>
  <form method="post">
    <div>
      <table class="form-table" role="presentation" id="createuser">
        <tr class="form-field form-required">
          <th><label><?php _e('Poll', 'ezpoll') ?></label></th>
          <td><textarea name="ezpoll-poll" style="width: auto" rows="5" cols="40" required /><?php echo $item['poll'] ?></textarea></td>
        </tr>
        <tr class="form-field form-required">
          <th><label>#1</label></th>
          <td><input type="text" name="ezpoll-choice1" value="<?php echo $item['choice1'] ?>" required /></td>
        </tr>
        <tr class="form-field form-required">
          <th><label>#2</label></th>
          <td><input type="text" name="ezpoll-choice2" value="<?php echo $item['choice2'] ?>" required /></td>
        </tr>
        <tr class="form-field">
          <th><label>#3</label></th>
          <td><input type="text" name="ezpoll-choice3" value="<?php echo $item['choice3'] ?>"/></td>
        </tr>
        <tr class="form-field">
          <th><label>#4</label></th>
          <td><input type="text" name="ezpoll-choice4" value="<?php echo $item['choice4'] ?>"/></td>
        </tr>
        <tr class="form-field">
          <th><label>#5</label></th>
          <td><input type="text" name="ezpoll-choice5" value="<?php echo $item['choice5'] ?>"/></td>
        </tr>
      </table>
    </div>
    <?php wp_nonce_field(); submit_button(); ?>
  </form>
</div>
