# Order Flag

## Description

**Order Flag** is a WordPress plugin designed to help manage and flag clients with previously failed orders in WooCommerce. The plugin provides a user interface for managing blacklisted keywords and comments, dynamically flags orders based on these keywords, and checks guest users' IP addresses and emails for prior issues. This updated version includes improved features and compatibility with HPOS (High-Performance Order Storage).

### Features
- **Client History Column:** Adds a new column in the WooCommerce orders list to display notes for clients with previously failed orders.
- **Blacklisted Keywords:** Manage and display orders with information matching blacklisted keywords.
- **Dynamic Blacklist Comment:** Customizable comments for blacklisted clients.
- **Guest User Detection:** Flags guest users based on IP address and email.
- **HPOS Compatibility:** Works with High-Performance Order Storage.

## Installation

1. **Download the Plugin:**
   Download the plugin ZIP file or clone the repository.

2. **Upload the Plugin:**
   - Go to the WordPress admin dashboard.
   - Navigate to `Plugins` > `Add New`.
   - Click `Upload Plugin` and choose the downloaded ZIP file.
   - Click `Install Now` and then `Activate Plugin`.

3. **Configure the Plugin:**
   - Go to `Blacklisted Keywords` in the WordPress admin menu to manage blacklisted keywords and comments.

## Usage

1. **View Client History:**
   - Navigate to `WooCommerce` > `Orders`.
   - A new column named "Client History" will display notes for each order based on the client’s history and blacklisted status.

2. **Manage Blacklisted Keywords:**
   - Go to `Blacklisted Keywords` in the WordPress admin menu.
   - Add, view, or remove blacklisted keywords. Keywords are used to flag orders with matching client information.

3. **Customize Blacklisted Comments:**
   - In the `Blacklisted Keywords` menu, you can set or change the comment displayed for blacklisted clients.

4. **Guest User Flagging:**
   - The plugin will automatically check guest user IP addresses and emails for previous failed orders.

## Frequently Asked Questions (FAQ)

### How does the plugin determine if a client is blacklisted?
The plugin checks order data such as billing email, phone number, and customer IP address against a list of blacklisted keywords and previous failed orders. If any match is found, the client is flagged as blacklisted.

### Can I add multiple blacklisted keywords?
Yes, you can add multiple blacklisted keywords separated by commas in the `Blacklisted Keywords` admin page.

### How do I remove a blacklisted keyword?
You can remove blacklisted keywords from the `Blacklisted Keywords` admin page by clicking the "Remove" button next to the keyword you wish to delete.

### What happens if the blacklisted comment field is empty?
If the blacklisted comment field is empty, the default comment "Blacklisted" will be used.

## License

This plugin is licensed under the **GNU General Public License v3.0**. See [LICENSE](LICENSE) for more details.

## Notes

- This plugin is a sample from a project and may require customization to fit specific needs or environments.
- Ensure compatibility with your WooCommerce setup and test the plugin in a staging environment before deploying it on a live site.

## Author

**Yousseif Ahmed**

For more information or support, please contact the author or refer to the plugin documentation.
