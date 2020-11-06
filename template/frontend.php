<div class="ezpoll" id="ezpoll-<?php echo $poll->id; ?>">
  <h4><?php echo $poll->poll; ?></h4>
  <div class="ezpoll-wrap">
    <?php if ($show_results): ?>
      <table>
        <?php if ($poll->choice1): ?>
          <tr>
            <th><?php echo $poll->choice1; ?></th>
            <td>
              <div>
                <div class="ezpoll-value" style="width: <?php echo $results[0]; ?>%"><?php echo $results[0]; ?>%</div>
                <div class="ezpoll-bar" style="width: <?php echo $results[0]; ?>%"></div>
              </div>
            </td>
          </tr>
        <?php endif; ?>
        <?php if ($poll->choice2): ?>
          <tr>
            <th><?php echo $poll->choice2; ?></th>
            <td>
              <div>
                <div class="ezpoll-value" style="width: <?php echo $results[1]; ?>%"><?php echo $results[1]; ?>%</div>
                <div class="ezpoll-bar" style="width: <?php echo $results[1]; ?>%"></div>
              </div>
            </td>
          </tr>
        <?php endif; ?>
        <?php if ($poll->choice3): ?>
          <tr>
            <th><?php echo $poll->choice3; ?></th>
            <td>
              <div>
                <div class="ezpoll-value" style="width: <?php echo $results[2]; ?>%"><?php echo $results[2]; ?>%</div>
                <div class="ezpoll-bar" style="width: <?php echo $results[2]; ?>%"></div>
              </div>
            </td>
          </tr>
        <?php endif; ?>
        <?php if ($poll->choice4): ?>
          <tr>
            <th><?php echo $poll->choice4; ?></th>
            <td>
              <div>
                <div class="ezpoll-value" style="width: <?php echo $results[3]; ?>%"><?php echo $results[3]; ?>%</div>
                <div class="ezpoll-bar" style="width: <?php echo $results[3]; ?>%"></div>
              </div>
            </td>
          </tr>
        <?php endif; ?>
        <?php if ($poll->choice5): ?>
          <tr>
            <th><?php echo $poll->choice5; ?></th>
            <td>
              <div>
                <div class="ezpoll-value" style="width: <?php echo $results[4]; ?>%"><?php echo $results[4]; ?>%</div>
                <div class="ezpoll-bar" style="width: <?php echo $results[4]; ?>%"></div>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </table>
      <div class="ezpoll-participants">
        <?php echo $poll->answer_count < 10? '< 10': $poll->answer_count; ?> <?php _e('people have participated', 'ezpoll'); ?>
      </div>
    <?php else: ?>
      <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>?action=ezpoll_form_data" method="post">
        <input type="hidden" name="ezpoll_id" value="<?php echo $poll->id; ?>" />
        <input type="hidden" name="ezpoll_url" value="<?php echo $url; ?>" />
        <?php if ($poll->choice1): ?>
          <div><input type="radio" name="ezpoll_answer" value="1" required> <label onclick="this.parentNode.querySelector('input').click()"><?php echo $poll->choice1; ?></label></div>
        <?php endif; ?>
        <?php if ($poll->choice2): ?>
          <div><input type="radio" name="ezpoll_answer" value="2" required> <label onclick="this.parentNode.querySelector('input').click()"><?php echo $poll->choice2; ?></label></div>
        <?php endif; ?>
        <?php if ($poll->choice3): ?>
          <div><input type="radio" name="ezpoll_answer" value="3" required> <label onclick="this.parentNode.querySelector('input').click()"><?php echo $poll->choice3; ?></label></div>
        <?php endif; ?>
        <?php if ($poll->choice4): ?>
          <div><input type="radio" name="ezpoll_answer" value="4" required> <label onclick="this.parentNode.querySelector('input').click()"><?php echo $poll->choice4; ?></label></div>
        <?php endif; ?>
        <?php if ($poll->choice5): ?>
          <div><input type="radio" name="ezpoll_answer" value="5" required> <label onclick="this.parentNode.querySelector('input').click()"><?php echo $poll->choice5; ?></label></div>
        <?php endif; ?>
        <?php wp_nonce_field(); ?>
        <button type="submit"><?php _e('Continue', 'ezpoll'); ?></button>
      </form>
    <?php endif; ?>
  </div>
</div>
