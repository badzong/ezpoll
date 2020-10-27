<?php
/**
 * Plugin Name: EZ Poll
 * Plugin URI: https://www.kickstart.ch
 * Description: A simplistic poll plugin
 * Version: 1.0
 * Author: Manuel Bazdong
 * Author URI: https://www.kickstart.ch
 */

global $ezpoll_db_version;
$ezpoll_db_version = '1.0';
register_activation_hook( __FILE__, 'ezpoll_install' );
function ezpoll_install() {
	global $wpdb;
	global $ezpoll_db_version;

	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . 'ezpoll';
	$sql = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		last_update datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		poll text NOT NULL,
		choice1 varchar(255) NOT NULL,
		choice2 varchar(255) NOT NULL,
		choice3 varchar(255),
		choice4 varchar(255),
		choice5 varchar(255),
		answer1 int(11) UNSIGNED DEFAULT 0 NOT NULL,
		answer2 int(11) UNSIGNED DEFAULT 0 NOT NULL,
		answer3 int(11) UNSIGNED DEFAULT 0 NOT NULL,
		answer4 int(11) UNSIGNED DEFAULT 0 NOT NULL,
		answer5 int(11) UNSIGNED DEFAULT 0 NOT NULL,
		answer_count int(11) UNSIGNED DEFAULT 0 NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'ezpoll_db_version', $ezpoll_db_version );
}

add_action( 'admin_menu', 'ezpoll_menu' );
function ezpoll_menu() {
 add_menu_page( 'Polls', 'Polls', 'manage_options', 'ezpoll_menu', 'ezpoll_overview', 'dashicons-chart-pie', 40);
 add_submenu_page( 'ezpoll_menu', 'Create Poll', 'Create Poll', 'manage_options', 'ezpoll_create', 'ezpoll_create', 1);
 add_submenu_page( null, 'Edit Poll', 'Edit Poll', 'manage_options', 'ezpoll_edit', 'ezpoll_create');
 add_submenu_page( null, 'Delete Poll', 'Delete Poll', 'manage_options', 'ezpoll_delete', 'ezpoll_delete');
}

add_action('wp_enqueue_scripts','ezpoll_scripts');
function ezpoll_scripts(){
    wp_enqueue_style( 'ezpoll', plugins_url( 'css/ezpoll.css' , __FILE__ ) );
}

function ezpoll_success() {
    global $wpdb;
    $id = $wpdb->insert_id;
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e( "Poll saved. Shortcode: [ezpoll id=\"$id\"]", "Poll saved. Shortcode: [ezpoll id=\"$id\"]" ); ?></p>
    </div>
    <?php
}

function ezpoll_delete_success() {
    global $wpdb;
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e( "Poll deleted." ); ?></p>
    </div>
    <?php
}

function ezpoll_overview() {
 global $wpdb;
 $paginate_by = 50;

 if (isset($_GET['order_by']) && !empty($_GET['order_by'])) {
   $order_by = $_GET['order_by'];
   if (strpos($order_by, '--')) {
     $order = explode('--', $order_by);
     $order_by = $order[0];
     $order_dir = $order[1];
   } else {
     $order_dir = 'ASC';
   }
   if (!in_array($order_by, array('id', 'created', 'poll', 'answer_count'))) {
     $order_by = 'id';
   }
   if (!in_array($order_dir, array('ASC', 'DESC'))) {
     $order_dir = 'ASC';
   }
 } else {
   $order_by = 'id';
   $order_dir = 'DESC';
 }

 $page = 1;
 if (isset($_GET['paged']) && !empty($_GET['paged'])) {
   $page = intval($_GET['paged']);
   if (!$page) {
     $page = 1;
   }
 } 
 $offset = ($page - 1) * $paginate_by;

 $table_name = $wpdb->prefix . 'ezpoll';
 $polls = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY $order_by $order_dir LIMIT $offset,$paginate_by" );
 $date_format = get_option( 'date_format' );
 $poll_count = $wpdb->get_var( "SELECT count(*) FROM $table_name" );
 $pages = intdiv($poll_count, $paginate_by) + ($poll_count % $paginate_by ? 1: 0);
 
 ?>
 <div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <a href="?page=ezpoll_create" class="page-title-action">Neu hinzufügen</a>
    <hr class="wp-header-end">

    <div class="tablenav-pages alignright">
      <span class="displaying-num"><?php echo $poll_count; ?> Einträge</span>
      <span class="pagination-links">
        <?php if ($page == 1): ?>
          <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
          <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
        <?php else: ?>
          <a class="next-page button" href="?page=ezpoll_menu&order_by=<?php echo $order_by; ?>--<?php echo $order_dir; ?>&paged=1">
            <span class="screen-reader-text">Erste Seite</span><span aria-hidden="true">«</span>
          </a>
          <a class="next-page button" href="?page=ezpoll_menu&order_by=<?php echo $order_by; ?>--<?php echo $order_dir; ?>&paged=<?php echo $page - 1; ?>">
            <span class="screen-reader-text">Vorherige Seite</span><span aria-hidden="true">‹</span>
          </a>
        <?php endif ?>
        <span class="paging-input">
          <label for="current-page-selector" class="screen-reader-text">Aktuelle Seite</label>
          <input class="current-page" id="current-page-selector" type="text" name="paged" value="<?php echo $page; ?>" size="2" aria-describedby="table-paging">
          <span class="tablenav-paging-text"> von <span class="total-pages"><?php echo $pages ?></span></span>
        </span>
        <?php if ($page == $pages): ?>
          <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
          <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
        <?php else: ?>
          <a class="next-page button" href="?page=ezpoll_menu&order_by=<?php echo $order_by; ?>--<?php echo $order_dir; ?>&paged=<?php echo $page + 1; ?>">
            <span class="screen-reader-text">Nächste Seite</span><span aria-hidden="true">›</span>
          </a>
          <a class="next-page button" href="?page=ezpoll_menu&order_by=<?php echo $order_by; ?>--<?php echo $order_dir; ?>&paged=<?php echo $pages; ?>">
            <span class="screen-reader-text">Letzte Seite</span><span aria-hidden="true">»</span>
          </a>
        <?php endif ?>
      </span>
    </div>

    <table class="wp-list-table widefat fixed striped table-view-list users">
      <thead>
        <tr>
          <th scope="col" id="ezpoll_date" class="manage-column sortable <?php echo $order_by == 'date' && $order_dir == 'ASC'? 'asc': 'desc'; ?>">
            <a href="?page=ezpoll_menu&order_by=created--<?php echo $order_by == 'created' && $order_dir == 'ASC'? 'DESC': 'ASC'; ?>">
              <span>Date</span><span class="sorting-indicator"></span>
            </a>
          </th>
          <th scope="col" id="ezpoll_poll" class="manage-column sortable <?php echo $order_by == 'poll' && $order_dir == 'ASC'? 'asc': 'desc'; ?>">
            <a href="?page=ezpoll_menu&order_by=poll--<?php echo $order_by == 'poll' && $order_dir == 'ASC'? 'DESC': 'ASC'; ?>">
              <span>Poll</span><span class="sorting-indicator"></span>
            </a>
          </th>
          <th scope="col" id="ezpoll_results" class="manage-column sortable <?php echo $order_by == 'answer_count' && $order_dir == 'ASC'? 'asc': 'desc'; ?>">
            <a href="?page=ezpoll_menu&order_by=answer_count--<?php echo $order_by == 'answer_count' && $order_dir == 'ASC'? 'DESC': 'ASC'; ?>">
              <span>Results</span><span class="sorting-indicator"></span>
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
              <span class="edit"><a href="?page=ezpoll_edit&ezpoll_id=<?php echo $poll->id; ?>">Bearbeiten</a></span> | 
              <span class="delete"><a class="submitdelete" href="?page=ezpoll_delete&ezpoll_id=<?php echo $poll->id; ?>">Löschen</a>
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
<?php
}

function ezpoll_create() {
 global $wpdb;

 if ( !current_user_can( 'manage_options' ) )  {
  wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
 }

 $table_name = $wpdb->prefix . 'ezpoll';

 if (isset($_GET['ezpoll_id']) && !empty($_GET['ezpoll_id'])) {
   $ezpoll_id = $_GET['ezpoll_id'];
   $item = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $ezpoll_id", ARRAY_A );
 } else {
   $item = array(
     'poll' => '',
     'choice1' => '',
     'choice2' => '',
     'choice3' => '',
     'choice4' => '',
     'choice5' => ''
   );
 }
 if (isset($_GET['ezpoll_id']) && !empty($_GET['ezpoll_id'])) {
   $item['id'] = $_GET['ezpoll_id'];
 }

 if (isset($_POST['ezpoll-poll'])) {
  foreach(array('poll', 'choice1', 'choice2') as $key) {
    $post_key = 'ezpoll-' . $key;
    if (!isset($_POST[$post_key])) {
      wp_die( __( 'Form error.' ) );
    }
    if (empty($_POST[$post_key])) {
      wp_die( __( 'Form error.' ) );
    }
    $item[$key] = $_POST[$post_key];
  }
  foreach(array('choice3', 'choice4', 'choice5') as $key) {
    $post_key = 'ezpoll-' . $key;
    $item[$key] = empty($_POST[$post_key])? null: $_POST[$post_key];
  }

  if ($wpdb->replace( $table_name, $item )) {
    add_action( 'admin_notices', 'ezpoll_success' );
    do_action( 'admin_notices' );
    return ezpoll_overview();
  }
 }  
?>
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
<?php }

function ezpoll_delete() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'ezpoll';

  if (!isset($_GET['ezpoll_id']) || empty($_GET['ezpoll_id']) || !preg_match('/^[0-9]+$/', $_GET['ezpoll_id'])) {
    wp_die( __( 'Invalid poll id supplied' ) );
  }
  $poll_id = $_GET['ezpoll_id'];

  $poll = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $poll_id" );
  if (!$poll) {
    wp_die( __( 'Invalid poll id supplied' ) );
  }

  if (isset($_POST['ezpoll_id']) && !empty($_POST['ezpoll_id']) && $_POST['ezpoll_id'] == $poll_id) {
    $wpdb->delete( $table_name, array( 'ID' => $poll_id ) );
    add_action( 'admin_notices', 'ezpoll_delete_success' );
    do_action( 'admin_notices' );
    return ezpoll_overview();
  }

  ?>
  <h1>Poll: <?php echo $poll->poll; ?></h1> 
  <p><?php echo $poll->answer_count; ?> Antworten</p>
  <form method="post">
    <input type="hidden" name="ezpoll_id" value="<?php echo $poll->id; ?>"/>
    <?php wp_nonce_field(); submit_button('Delete'); ?>
  </form>
  <?php } ?>

<?php

function register_session(){
    if( !session_id() ) {
        session_start();
    }
}
add_action('init','register_session');

function random_id() {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $max = strlen($chars) - 1;
    $id = '';
    for ($i = 0; $i < 16; $i++) {
        $id .= $chars[rand(0, $max)];
    }
    return $id;
}

add_action( 'admin_post_ezpoll_form_data', 'ezpoll_form_data' );
function ezpoll_form_data() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'ezpoll';

  foreach(array('ezpoll_id', 'ezpoll_answer') as $key) {
    if (!isset($_POST[$key]) || empty($_POST[$key]) || !preg_match('/^[0-9]$/', $_POST[$key])) {
      exit;
      wp_redirect( $_SERVER["HTTP_REFERER"], 302, 'WordPress' );
    }
  }

  $poll_id = $_POST['ezpoll_id'];
  $poll = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $poll_id", ARRAY_A );
  if (!$poll) {
    exit;
    wp_redirect( $_SERVER["HTTP_REFERER"], 302, 'WordPress' );
  }

  $answer = $_POST['ezpoll_answer'];
  if ($answer < 1 || $answer > 5) {
    exit;
    wp_redirect( $_SERVER["HTTP_REFERER"], 302, 'WordPress' );
  }

  $choice = 'choice' . $answer;
  if (!$poll[$choice]) {
    wp_redirect( $_SERVER["HTTP_REFERER"], 302, 'WordPress' );
  }

  if (!isset($_SESSION['ezpoll']) || empty($_SESSION['ezpoll'])) {
    wp_redirect( $_SERVER["HTTP_REFERER"], 302, 'WordPress' );
  }

  if (in_array($poll_id, $_SESSION['ezpoll'])) {
    wp_redirect( $_SERVER["HTTP_REFERER"], 302, 'WordPress' );
  }

  array_push($_SESSION['ezpoll'], $poll_id);
  $answer = 'answer' . $answer;
  
  $wpdb->query( "UPDATE $table_name SET $answer = $answer + 1, answer_count = answer_count + 1 WHERE ID = $poll_id ");

  wp_redirect( $_SERVER["HTTP_REFERER"], 302, 'WordPress' );
}


add_shortcode('ezpoll', function ($attrs) {
  global $wpdb;
  $table_name = $wpdb->prefix . 'ezpoll';

  if (!isset($attrs['id']) || empty($attrs['id']) || !preg_match('/^[0-9]+$/', $attrs['id'])) {
    return '<p class="error">Invalid ID</p>';
  }

  $poll_id = $attrs['id'];
  $poll = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $poll_id" );
  if (!$poll) {
    return "<p class=\"error\">Poll id=$poll_id not found</p>";
  }

  if (!isset($_SESSION['ezpoll']) || empty($_SESSION['ezpoll'])) {
    $_SESSION['ezpoll'] = array(0);
  }

  $show_results = in_array($poll_id, $_SESSION['ezpoll']) && $poll->answer_count > 0;
  if ($poll->answer_count > 0) {
    $results = array(
      round($poll->answer1 / $poll->answer_count * 100),
      round($poll->answer2 / $poll->answer_count * 100),
      round($poll->answer3 / $poll->answer_count * 100),
      round($poll->answer4 / $poll->answer_count * 100),
      round($poll->answer5 / $poll->answer_count * 100),
    );
  }
?>
<div class="ezpoll">
  <h4>Umfrage</h4>
  <p><?php echo $poll->poll; ?><ph4>
  <div>
  <?php
    if ($poll->choice1) { echo "<div class=\"ezpoll-answer\"><div class=\"ezpoll-label\">$poll->choice1</div><div class=\"ezpoll-chart\"><div class=\"ezpoll-value\" style=\"width: $results[0]%\">$results[0]%</div><div class=\"ezpoll-bar\" style=\"width: $results[0]%\"></div></div></div>"; }
    if ($poll->choice2) { echo "<div class=\"ezpoll-answer\"><div class=\"ezpoll-label\">$poll->choice2</div><div class=\"ezpoll-chart\"><div class=\"ezpoll-value\" style=\"width: $results[1]%\">$results[1]%</div><div class=\"ezpoll-bar\" style=\"width: $results[1]%\"></div></div></div>"; }
    if ($poll->choice3) { echo "<div class=\"ezpoll-answer\"><div class=\"ezpoll-label\">$poll->choice3</div><div class=\"ezpoll-chart\"><div class=\"ezpoll-value\" style=\"width: $results[2]%\">$results[2]%</div><div class=\"ezpoll-bar\" style=\"width: $results[2]%\"></div></div></div>"; }
    if ($poll->choice4) { echo "<div class=\"ezpoll-answer\"><div class=\"ezpoll-label\">$poll->choice4</div><div class=\"ezpoll-chart\"><div class=\"ezpoll-value\" style=\"width: $results[3]%\">$results[3]%</div><div class=\"ezpoll-bar\" style=\"width: $results[3]%\"></div></div></div>"; }
    if ($poll->choice5) { echo "<div class=\"ezpoll-answer\"><div class=\"ezpoll-label\">$poll->choice5</div><div class=\"ezpoll-chart\"><div class=\"ezpoll-value\" style=\"width: $results[4]%\">$results[4]%</div><div class=\"ezpoll-bar\" style=\"width: $results[4]%\"></div></div></div>"; }
  ?>
  </div>
  <div class="ezpoll-participants"><?php echo $poll->answer_count < 10? '< 10': $poll->answer_count; ?> Personen haben an der Umfrage teilgenommen</div>
</div>
<?php
  if ($show_results): ?>
  <?php else: ?>
  <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>?action=ezpoll_form_data" method="post">
    <input type="hidden" name="ezpoll_id" value="<?php echo $poll->id; ?>" />
  <?php
    if ($poll->choice1) { echo "<div><input type=\"radio\" name=\"ezpoll_answer\" value=\"1\" required> <label>$poll->choice1</label></div>"; }
    if ($poll->choice2) { echo "<div><input type=\"radio\" name=\"ezpoll_answer\" value=\"2\" required> <label>$poll->choice2</label></div>"; }
    if ($poll->choice3) { echo "<div><input type=\"radio\" name=\"ezpoll_answer\" value=\"3\" required> <label>$poll->choice3</label></div>"; }
    if ($poll->choice4) { echo "<div><input type=\"radio\" name=\"ezpoll_answer\" value=\"4\" required> <label>$poll->choice4</label></div>"; }
    if ($poll->choice5) { echo "<div><input type=\"radio\" name=\"ezpoll_answer\" value=\"5\" required> <label>$poll->choice5</label></div>"; }
    wp_nonce_field();
  ?>
  <button type="submit">Weiter</button>
  </form>
  <?php endif; ?>
</div>
<?php }); ?>
