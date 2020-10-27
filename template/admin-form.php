<div class="wrap">
  <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
  <form method="post">
    <div>
      <table class="form-table" role="presentation" id="createuser">
        <tr class="form-field form-required">
          <th><label>Poll</label></th>
          <td><input type="text" name="ezpoll-poll" value="<?php echo $item['poll'] ?>"/></td>
        </tr>
        <tr class="form-field form-required">
          <th><label>#1</label></th>
          <td><input type="text" name="ezpoll-choice1" value="<?php echo $item['choice1'] ?>"/></td>
        </tr>
        <tr class="form-field form-required">
          <th><label>#2</label></th>
          <td><input type="text" name="ezpoll-choice2" value="<?php echo $item['choice2'] ?>"/></td>
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
