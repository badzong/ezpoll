<?php
/**
 * Plugin Name: EZ Poll
 * Plugin URI: https://www.kickstart.ch
 * Description: A simplistic poll plugin
 * Version: 1.0
 * Author: Manuel Bazdong
 * Author URI: https://www.kickstart.ch
 */

define('EZPOLL_SEP', 'x');
define('EZPOLL_HISTORY', 15);

function ezpoll_session_checksum($session) {
    $checksum = 47;
    foreach($session as $poll_id) {
        $checksum *= ($poll_id * 33) >> 2;
    }

    return $checksum % 100;
}

function ezpoll_session_load() {
    global $ezpoll_session;

    if (isset($ezpoll_session) && count($ezpoll_session)) {
        return;
    }
    
    $ezpoll_session = array();
    if (!isset($_COOKIE['ezpoll']) || empty($_COOKIE['ezpoll'])) {
        return;
    }

    $poll_ids = explode(EZPOLL_SEP, $_COOKIE['ezpoll']);

    foreach($poll_ids as $poll_id) {
        if(preg_match('/^[0-9]+$/', $poll_id)) {
            array_push($ezpoll_session, intval($poll_id));
        }
    }

    $checksum = array_pop($ezpoll_session);
    if ($checksum == ezpoll_session_checksum($ezpoll_session)) {
        $ezpoll_session = array_unique($ezpoll_session);
    } else {
        $ezpoll_session = array();
    }
}

function ezpoll_session_save($poll_id) {
    global $ezpoll_session;

    $ids = $ezpoll_session;
    array_push($ids, $poll_id);
    $ids = array_slice(array_unique($ids), -EZPOLL_HISTORY);
    array_push($ids, ezpoll_session_checksum($ids));
    setcookie('ezpoll', implode(EZPOLL_SEP, $ids), time()+15552000, '/');
}

add_action('plugins_loaded', 'ezpoll_load_textdomain');
function ezpoll_load_textdomain() {
 load_plugin_textdomain( 'ezpoll', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}

global $ezpoll_db_version;
$ezpoll_db_version = '1.0';
register_activation_hook(__FILE__, 'ezpoll_install');
function ezpoll_install()
{
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

    include_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);

    add_option('ezpoll_db_version', $ezpoll_db_version);
}

add_action('admin_menu', 'ezpoll_menu');
function ezpoll_menu()
{
    add_menu_page(__('Polls', 'ezpoll'), __('Polls', 'ezpoll'), 'manage_options', 'ezpoll_menu', 'ezpoll_overview', 'dashicons-chart-pie', 40);
    add_submenu_page('ezpoll_menu', __('Create poll', 'ezpoll'), __('Create poll', 'ezpoll'), 'manage_options', 'ezpoll_create', 'ezpoll_create', 1);
    add_submenu_page(null, __('Edit poll', 'ezpoll'), __('Edit poll', 'ezpoll'), 'manage_options', 'ezpoll_edit', 'ezpoll_create');
    add_submenu_page(null, __('Delete Poll', 'ezpoll'), __('Delete Poll', 'ezpoll'), 'manage_options', 'ezpoll_delete', 'ezpoll_delete');
}

add_action('wp_enqueue_scripts', 'ezpoll_scripts');
function ezpoll_scripts()
{
    wp_enqueue_style('ezpoll', plugins_url('css/ezpoll.css', __FILE__));
}

function ezpoll_success()
{
    global $wpdb;
    $id = $wpdb->insert_id;
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e("Poll saved. Shortcode: [ezpoll id=\"$id\"]", 'ezpoll'); ?></p>
    </div>
    <?php
}

function ezpoll_delete_success()
{
    global $wpdb;
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e("Poll deleted.", 'ezpoll'); ?></p>
    </div>
    <?php
}

function ezpoll_overview()
{
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
    $polls = $wpdb->get_results("SELECT * FROM $table_name ORDER BY $order_by $order_dir LIMIT $offset,$paginate_by");
    $date_format = get_option('date_format');
    $poll_count = $wpdb->get_var("SELECT count(*) FROM $table_name");
    $pages = intdiv($poll_count, $paginate_by) + ($poll_count % $paginate_by ? 1: 0);
 
    include plugin_dir_path(__FILE__) . 'template/admin-list.php';
}

function ezpoll_create()
{
    global $wpdb;

    if (!current_user_can('manage_options') ) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'ezpoll'));
    }

    $table_name = $wpdb->prefix . 'ezpoll';

    $edit = false;
    if (isset($_GET['ezpoll_id']) && !empty($_GET['ezpoll_id'])) {
        $ezpoll_id = $_GET['ezpoll_id'];
        $item = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $ezpoll_id", ARRAY_A);
    	$edit = true;
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
                wp_die(__('Form error.', 'ezpoll'));
            }
            if (empty($_POST[$post_key])) {
                wp_die(__('Form error.', 'ezpoll'));
            }
            $item[$key] = $_POST[$post_key];
        }
        foreach(array('choice3', 'choice4', 'choice5') as $key) {
            $post_key = 'ezpoll-' . $key;
            $item[$key] = empty($_POST[$post_key])? null: $_POST[$post_key];
        }

        if ($wpdb->replace($table_name, $item)) {
            add_action('admin_notices', 'ezpoll_success');
            do_action('admin_notices');
            return ezpoll_overview();
        }
    }  

    include plugin_dir_path(__FILE__) . 'template/admin-form.php';
}

function ezpoll_delete()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ezpoll';

    if (!isset($_GET['ezpoll_id']) || empty($_GET['ezpoll_id']) || !preg_match('/^[0-9]+$/', $_GET['ezpoll_id'])) {
        wp_die(__('Invalid poll id supplied', 'ezpoll'));
    }
    $poll_id = $_GET['ezpoll_id'];

    $poll = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $poll_id");
    if (!$poll) {
        wp_die(__('Invalid poll id supplied', 'ezpoll'));
    }

    if (isset($_POST['ezpoll_id']) && !empty($_POST['ezpoll_id']) && $_POST['ezpoll_id'] == $poll_id) {
        $wpdb->delete($table_name, array( 'ID' => $poll_id ));
        add_action('admin_notices', 'ezpoll_delete_success');
        do_action('admin_notices');
        return ezpoll_overview();
    }

    ?>
  <h1>Poll: <?php echo $poll->poll; ?></h1> 
  <p><?php echo $poll->answer_count; ?> <?php _e('Participants', 'ezpoll') ?></p>
  <form method="post">
    <input type="hidden" name="ezpoll_id" value="<?php echo $poll->id; ?>"/>
    <?php wp_nonce_field(); submit_button(__('Delete', 'ezpoll')); ?>
  </form>
<?php } ?>

<?php

add_action('admin_post_ezpoll_form_data', 'ezpoll_form_data');
add_action('admin_post_nopriv_ezpoll_form_data', 'ezpoll_form_data');
function ezpoll_form_data()
{
    global $ezpoll_session;
    global $wpdb;
    $table_name = $wpdb->prefix . 'ezpoll';

    foreach(array('ezpoll_id', 'ezpoll_answer') as $key) {
        if (!isset($_POST[$key]) || empty($_POST[$key]) || !preg_match('/^[0-9]$/', $_POST[$key])) {
            wp_redirect($_SERVER["HTTP_REFERER"], 302, 'WordPress');
        }
    }

    $poll_id = intval($_POST['ezpoll_id']);
    $poll = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $poll_id", ARRAY_A);
    if (!$poll) {
        wp_redirect($_SERVER["HTTP_REFERER"], 302, 'WordPress');
    }

    $answer = $_POST['ezpoll_answer'];
    if ($answer < 1 || $answer > 5) {
        wp_redirect($_SERVER["HTTP_REFERER"], 302, 'WordPress');
    }

    $choice = 'choice' . $answer;
    if (!$poll[$choice]) {
        wp_redirect($_SERVER["HTTP_REFERER"], 302, 'WordPress');
    }

    ezpoll_session_load();
    if (in_array($poll_id, $ezpoll_session)) {
        wp_redirect($_SERVER["HTTP_REFERER"], 302, 'WordPress');
    }

    $answer = 'answer' . $answer;
    $wpdb->query("UPDATE $table_name SET $answer = $answer + 1, answer_count = answer_count + 1 WHERE ID = $poll_id ");

    ezpoll_session_save($poll_id);

    wp_redirect($_SERVER["HTTP_REFERER"], 302, 'WordPress');
}

add_shortcode(
    'ezpoll', function ($attrs) {
        global $ezpoll_session;
        global $wpdb;
        $table_name = $wpdb->prefix . 'ezpoll';

        if (!isset($attrs['id']) || empty($attrs['id']) || !preg_match('/^[0-9]+$/', $attrs['id'])) {
            return '<p class="error">' . __('Invalid ID', 'ezpoll') . '</p>';
        }

        $poll_id = $attrs['id'];
        $poll = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $poll_id");
        if (!$poll) {
            return "<p class=\"error\">" . __("Poll id=$poll_id not found", 'ezpoll') . "</p>";
        }

        ezpoll_session_load();
        $show_results = in_array($poll_id, $ezpoll_session) && $poll->answer_count > 0;
        if ($poll->answer_count > 0) {
            $results = array(
            round($poll->answer1 / $poll->answer_count * 100),
            round($poll->answer2 / $poll->answer_count * 100),
            round($poll->answer3 / $poll->answer_count * 100),
            round($poll->answer4 / $poll->answer_count * 100),
            round($poll->answer5 / $poll->answer_count * 100),
            );
        }

        ob_start();
        include plugin_dir_path(__FILE__) . 'template/frontend.php';
        return ob_get_clean();
    }
);
?>
