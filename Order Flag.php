<?php

/*  

Plugin Name: Order Flag

Description: Plugin allow to show a note for clients that has previously failed orders, UI to add blacklisted keywords, Getting with matching keywords, Dynamic Blacklist comment, Getting guest by ip address&email, Compatible with HPOS.

Author: Yousseif Ahmed 

Version: 1.4.1

*/


// Create a new column in the orders list to display the note
function add_client_history_column($columns)
{
    $columns['client_history'] = __('Client History', 'woocommerce');
    return $columns;
}

// Display the note in the new column for each order
function display_client_history($column, $order_id)
{
    if ($column === 'client_history') {
        $order = wc_get_order($order_id);
        $user_id = $order->get_user_id();
        $customer_ip = $order->get_customer_ip_address();
        $billing_phone = $order->get_billing_phone();
        $billing_email = $order->get_billing_email();
        $blacklisted_keywords = get_blacklisted_keywords();
        $blacklisted_comment = get_option('blacklisted_comment', 'Blacklisted'); // Get the current comment or use default


        $order_data = array(
            'Billing Address' => $order->get_formatted_billing_address(),
            'Shipping Address' => $order->get_formatted_shipping_address(),
            'Name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'Email' => $billing_email,
            'Number' => $billing_phone,
            'City' => $order->get_billing_city(),
            'Zone' => $order->get_billing_state(),
            'Order Note' => $order->get_customer_note(),
            'Customer IP' => $customer_ip

        );

        $blacklisted = false; // Initialize a flag to track if a keyword is blacklisted

        foreach ($blacklisted_keywords as $keyword) {
            foreach ($order_data as $label => $value) {
                if (stripos($value, $keyword) !== false) {
                    $blacklisted = true; // Set the flag if a keyword is found
                    break; // No need to check further if a keyword is found
                }
            }
            if ($blacklisted) {
                break; // No need to check further if a keyword is found
            }
        }

        // Display "Blacklisted" only if a keyword is found
        if ($blacklisted) {
            echo '<span style="color: red; font-size: 12px; text-align:center ;">' . esc_html($blacklisted_comment) . '<br></span>';
        }
        if ($user_id == '' && guest_is_blacklisted($billing_phone, $billing_email)) {
            echo '<span style="color: red; font-size:10px; text-align:center;">FAILED BEFORE</span>';
        }
        if ($user_id != '' && is_blacklisted($user_id, $billing_phone, $billing_email)) {
            echo '<span style="color: red; font-size:12px; text-align:center ;">FAILED BEFORE</span>';
        }

    }
}


// Check if the user or mobile number is blacklisted
function is_blacklisted($user_id, $billing_phone, $billing_email)
{
    if (empty($user_id) && empty($billing_phone) && empty($billing_email)) {
        return false;
    }

    // Check if the user has previously failed orders
    $previous_orders = wc_get_orders(
        array(
            'customer' => $user_id,
            'status' => 'failed',
        )
    );

    // Check if the billing phone or email has previously failed orders
    $previous_orders_by_phone = wc_get_orders(
        array(
            'billing_phone' => $billing_phone,
            'status' => 'failed',
        )
    );

    $previous_orders_by_email = wc_get_orders(
        array(
            'billing_email' => $billing_email,
            'status' => 'failed',
        )
    );

    if (!empty($previous_orders) || !empty($previous_orders_by_phone) || !empty($previous_orders_by_email)) {
        return true;
    }

    return false;
}

function guest_is_blacklisted($billing_phone, $billing_email)
{
    if (empty($billing_phone) && empty($billing_email)) {
        return false;
    }

    // Check if the user has previously failed orders

    $previous_orders_by_phone = wc_get_orders(
        array(
            'billing_phone' => $billing_phone,
            'status' => 'failed',
        )
    );

    $previous_orders_by_email = wc_get_orders(
        array(
            'billing_email' => $billing_email,
            'status' => 'failed',
        )
    );

    if ( !empty($previous_orders_by_phone) || !empty($previous_orders_by_email)) {
        return true;
    }

    return false;
}

// Retrieve the blacklisted keywords
function get_blacklisted_keywords()
{
    $blacklisted_keywords = get_option('blacklisted_keywords');
    return $blacklisted_keywords ? explode(',', $blacklisted_keywords) : array();
}

// Modify the admin menu page for managing blacklisted keywords
function add_blacklisted_keywords_menu_page()
{
    add_menu_page(
        'Blacklisted Keywords',
        'Blacklisted Keywords',
        'manage_options',
        'blacklisted_keywords',
        'display_blacklisted_keywords_page',
        'dashicons-id-alt'
    );
}

// Modify the admin menu page content for managing blacklisted keywords
function display_blacklisted_keywords_page()
{

    ?>
    <div class="wrap">
        <h1>Blacklisted Keywords</h1>

        <!-- Add your input and save blacklisted keywords -->
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="save_blacklisted_keywords">
            <label for="blacklisted_keywords">Enter Blacklisted Keywords (add more than one with comma-separated):</label>
            <input type="text" name="blacklisted_keywords" id="blacklisted_keywords">
            <input type="submit" value="Add To Blacklist">
            <br>
            <br>
        </form>

        <!-- Display the table of blacklisted keywords -->
        <?php display_blacklisted_keywords_table(); ?>
    </div>
    <?php
    $blacklisted_comment = get_option('blacklisted_comment', 'Blacklisted'); // Get the current comment or use default

    echo '<br><form method="post" action="' . admin_url('admin-post.php') . '">
          <input type="hidden" name="action" value="save_blacklisted_comment">
          <label for="blacklisted_comment">Blacklisted Comment:</label>
          <input type="text" name="blacklisted_comment" id="blacklisted_comment" value="' . esc_attr($blacklisted_comment) . '">
          <input type="submit" value="Save">
          </form>';
}


// Save the blacklisted keywords
function save_blacklisted_keywords()
{
    if (isset($_POST['blacklisted_keywords'])) {
        $input_keywords = sanitize_text_field($_POST['blacklisted_keywords']);
        $input_keywords_array = explode(',', $input_keywords);

        foreach ($input_keywords_array as $input_keyword) {
            // Remove any leading or trailing spaces
            $cleaned_keyword = trim($input_keyword);

            if (!empty($cleaned_keyword)) {
                $valid_keywords[] = $cleaned_keyword;
            }
        }

        // Fetch existing blacklisted keywords
        $existing_blacklisted_keywords = get_blacklisted_keywords();

        // Combine the existing and new blacklisted keywords
        $combined_blacklisted_keywords = array_merge($existing_blacklisted_keywords, $valid_keywords);

        // Remove any duplicate keywords
        $unique_blacklisted_keywords = array_unique($combined_blacklisted_keywords);

        // Save the updated blacklisted keywords to the database or any other storage method
        update_option('blacklisted_keywords', implode(',', $unique_blacklisted_keywords));
    }
    wp_redirect(admin_url('admin.php?page=blacklisted_keywords'));
    exit;
}


// Save the blacklisted comment

function save_blacklisted_comment()
{
    if (isset($_POST['blacklisted_comment'])) {
        $blacklisted_comment = sanitize_text_field($_POST['blacklisted_comment']);
        if (empty($blacklisted_comment)) {
            $blacklisted_comment = 'Blacklisted'; // Use 'Blacklisted' if field is empty
        }
        update_option('blacklisted_comment', $blacklisted_comment);
    }
    wp_redirect(admin_url('admin.php?page=blacklisted_keywords'));
    exit;
}

// Retrieve the blacklisted keywords table
function display_blacklisted_keywords_table()
{
    $blacklisted_keywords = get_blacklisted_keywords();
    ?>
    <table class="wp-list-table widefat striped">
        <thead>
            <tr>
                <th>Blacklisted Keywords</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($blacklisted_keywords as $keyword): ?>
                <tr>
                    <td>
                        <?php echo $keyword; ?>
                    </td>
                    <td>
                        <a
                            href="<?php echo admin_url('admin-post.php?action=remove_blacklisted_keyword&keyword=' . urlencode($keyword)); ?>"><button>Remove</button></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

// Remove a blacklisted keyword
function remove_blacklisted_keyword()
{
    if (isset($_GET['keyword'])) {
        $keyword = sanitize_text_field($_GET['keyword']);
        $blacklisted_keywords = get_blacklisted_keywords();
        $updated_keywords = array_diff($blacklisted_keywords, array($keyword));
        update_option('blacklisted_keywords', implode(',', $updated_keywords));
    }
    wp_redirect(admin_url('admin.php?page=blacklisted_keywords'));
    exit;
}
