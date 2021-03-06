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
        <?php echo $poll->answer_count < 10? '< 10': number_format($poll->answer_count, 0, '.', "'"); ?> <?php _e('people have participated', 'ezpoll'); ?>
        <?php if($round_diff): ?><div><small><?php echo $round_diff; ?>% <?php _e('rounding difference', 'ezpoll'); ?></small></div><?php endif; ?>
      </div>
    <?php else: ?>
      <form class="ezpoll-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>" method="post">
        <?php wp_nonce_field(); ?>
        <input type="hidden" name="action" value="ezpoll_form_data" />
        <input type="hidden" name="ezpoll_id" value="<?php echo $poll->id; ?>" />
        <input type="hidden" name="ezpoll_url" value="<?php echo $url; ?>" />
        <?php if ($poll->choice1): ?>
          <div><input type="radio" name="ezpoll_answer" value="1" id="ezpoll-answer-1" required> <label for="ezpoll-answer-1"><?php echo $poll->choice1; ?></label></div>
        <?php endif; ?>
        <?php if ($poll->choice2): ?>
          <div><input type="radio" name="ezpoll_answer" value="2" id="ezpoll-answer-2" required> <label for="ezpoll-answer-2"><?php echo $poll->choice2; ?></label></div>
        <?php endif; ?>
        <?php if ($poll->choice3): ?>
          <div><input type="radio" name="ezpoll_answer" value="3" id="ezpoll-answer-3" required> <label for="ezpoll-answer-3"><?php echo $poll->choice3; ?></label></div>
        <?php endif; ?>
        <?php if ($poll->choice4): ?>
          <div><input type="radio" name="ezpoll_answer" value="4" id="ezpoll-answer-4" required> <label for="ezpoll-answer-4"><?php echo $poll->choice4; ?></label></div>
        <?php endif; ?>
        <?php if ($poll->choice5): ?>
          <div><input type="radio" name="ezpoll_answer" value="5" id="ezpoll-answer-5" required> <label for="ezpoll-answer-5"><?php echo $poll->choice5; ?></label></div>
        <?php endif; ?>
        <button type="submit" class="submit"><?php _e('Continue', 'ezpoll'); ?></button>
      </form>
    <?php endif; ?>
  </div>
</div>
