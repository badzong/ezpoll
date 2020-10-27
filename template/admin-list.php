<div class="wrap">
  <h1 class="wp-heading-inline"><?php echo esc_html( get_admin_page_title() ); ?></h1>
  <a href="?page=ezpoll_create" class="page-title-action"><?php _e('Create poll', 'ezpoll') ?></a>
  <hr class="wp-header-end">

  <div class="tablenav-pages alignright">
    <span class="displaying-num"><?php echo $poll_count; ?> <?php _e('Records', 'ezpoll') ?></span>
    <span class="pagination-links">
      <?php if ($page == 1): ?>
        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
      <?php else: ?>
        <a class="next-page button" href="?page=ezpoll_menu&order_by=<?php echo $order_by; ?>--<?php echo $order_dir; ?>&paged=1">
          <span class="screen-reader-text"><?php _e('First page', 'ezpoll'); ?></span><span aria-hidden="true">«</span>
        </a>
        <a class="next-page button" href="?page=ezpoll_menu&order_by=<?php echo $order_by; ?>--<?php echo $order_dir; ?>&paged=<?php echo $page - 1; ?>">
          <span class="screen-reader-text"><?php _e('Previous page', 'ezpoll'); ?></span><span aria-hidden="true">‹</span>
        </a>
      <?php endif ?>
      <span class="paging-input">
        <label for="current-page-selector" class="screen-reader-text"><?php _e('Current page', 'ezpoll') ?></label>
        <input class="current-page" id="current-page-selector" type="text" name="paged" value="<?php echo $page; ?>" size="2" aria-describedby="table-paging">
        <span class="tablenav-paging-text"> <?php _e('of', 'ezpoll'); ?> <span class="total-pages"><?php echo $pages ?></span></span>
      </span>
      <?php if ($page == $pages): ?>
        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
      <?php else: ?>
        <a class="next-page button" href="?page=ezpoll_menu&order_by=<?php echo $order_by; ?>--<?php echo $order_dir; ?>&paged=<?php echo $page + 1; ?>">
          <span class="screen-reader-text"><?php _e('Next page', 'ezpoll'); ?></span><span aria-hidden="true">›</span>
        </a>
        <a class="next-page button" href="?page=ezpoll_menu&order_by=<?php echo $order_by; ?>--<?php echo $order_dir; ?>&paged=<?php echo $pages; ?>">
          <span class="screen-reader-text"><?php _e('Last page', 'ezpoll') ?></span><span aria-hidden="true">»</span>
        </a>
      <?php endif ?>
    </span>
  </div>

  <table class="wp-list-table widefat fixed striped table-view-list users">
    <thead>
      <tr>
        <th scope="col" id="ezpoll_date" class="manage-column sortable <?php echo $order_by == 'date' && $order_dir == 'ASC'? 'asc': 'desc'; ?>">
          <a href="?page=ezpoll_menu&order_by=created--<?php echo $order_by == 'created' && $order_dir == 'ASC'? 'DESC': 'ASC'; ?>">
            <span><?php _e('Date', 'ezpoll'); ?></span><span class="sorting-indicator"></span>
          </a>
        </th>
        <th scope="col" id="ezpoll_poll" class="manage-column sortable <?php echo $order_by == 'poll' && $order_dir == 'ASC'? 'asc': 'desc'; ?>">
          <a href="?page=ezpoll_menu&order_by=poll--<?php echo $order_by == 'poll' && $order_dir == 'ASC'? 'DESC': 'ASC'; ?>">
            <span><?php _e('Poll', 'ezpoll'); ?></span><span class="sorting-indicator"></span>
          </a>
        </th>
        <th scope="col" id="ezpoll_results" class="manage-column sortable <?php echo $order_by == 'answer_count' && $order_dir == 'ASC'? 'asc': 'desc'; ?>">
          <a href="?page=ezpoll_menu&order_by=answer_count--<?php echo $order_by == 'answer_count' && $order_dir == 'ASC'? 'DESC': 'ASC'; ?>">
            <span><?php _e('Results', 'ezpoll'); ?></span><span class="sorting-indicator"></span>
          </a>
        </th>
      </tr>
    </thead>
    <tbody id="the-list">
      <?php foreach ( $polls as $poll ): ?>
      <tr id="poll-<?php echo $poll->id ?>">
        <td class="has-row-actions">
          <strong>
            <a href="?page=ezpoll_edit&ezpoll_id=<?php echo $poll->id; ?>" class="edit">
              <span><?php $poll_created = DateTime::createFromFormat('Y-m-d H:i:s', $poll->created); echo $poll_created->format($date_format); ?></span>
            </a>
          </strong>
          <div class="row-actions">
            <span><strong style="color: #555">[ezpoll id="<?php echo $poll->id; ?>]</strong></span>
          </div>
        </td>
        <td class="has-row-actions column-primary">
          <strong>
            <a href="?page=ezpoll_edit&ezpoll_id=<?php echo $poll->id; ?>" class="edit">
              <span><?php echo $poll->poll; ?></span>
            </a>
          </strong>
          <div class="row-actions">
            <span class="edit"><a href="?page=ezpoll_edit&ezpoll_id=<?php echo $poll->id; ?>"><?php _e('Edit', 'ezpoll'); ?></a></span> | 
            <span class="delete"><a class="submitdelete" href="?page=ezpoll_delete&ezpoll_id=<?php echo $poll->id; ?>"><?php _e('Delete', 'ezpoll'); ?></a>
          </div>
        </td>
        <td class="has-row-actions">
          <strong>
            <span><?php echo $poll->answer_count; ?></span>
          </strong>
          <div class="row-actions">
            <?php if ($poll->answer_count > 0): ?>
              <strong style="color: #555">
                <?php if ($poll->choice1) printf("%s (%.1f%%)", $poll->choice1, round(100.0*$poll->answer1/$poll->answer_count, 1)); ?>
                <?php if ($poll->choice2) printf("| %s (%.1f%%)", $poll->choice2, round(100.0*$poll->answer2/$poll->answer_count, 1)); ?>
                <?php if ($poll->choice3) printf("| %s (%.1f%%)", $poll->choice3, round(100.0*$poll->answer3/$poll->answer_count, 1)); ?>
                <?php if ($poll->choice4) printf("| %s (%.1f%%)", $poll->choice4, round(100.0*$poll->answer4/$poll->answer_count, 1)); ?>
                <?php if ($poll->choice5) printf("| %s (%.1f%%)", $poll->choice5, round(100.0*$poll->answer5/$poll->answer_count, 1)); ?>
              </strong>
            <?php else: ?>
              <strong style="color: #555">
                <?php if ($poll->choice1) echo $poll->choice1; ?>
                <?php if ($poll->choice2) echo '| ' . $poll->choice2; ?>
                <?php if ($poll->choice3) echo '| ' . $poll->choice3; ?>
                <?php if ($poll->choice4) echo '| ' . $poll->choice4; ?>
                <?php if ($poll->choice5) echo '| ' . $poll->choice5; ?>
              </strong>
            <?php endif ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
    </tfoot>
  </table>
</div>
