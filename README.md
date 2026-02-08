Voxbee Operator Pro - FusionPBX Webphone
Voxbee Operator Pro is a modern, full-screen WebRTC webphone designed specifically for FusionPBX. Unlike the default small webphone, this application provides a "Dispatcher/Operator" style interface, allowing users to manage calls, take notes, and view history efficiently on a single screen.

(Note: You can replace this image link with a screenshot of your actual interface)

🛑 CRITICAL REQUIREMENTS (READ BEFORE INSTALLING)
WebRTC technology has strict security requirements imposed by modern web browsers (Chrome, Firefox, Edge, Safari).

HTTPS is Mandatory: You cannot use this application over HTTP.

Valid SSL Certificate Required: You MUST use a valid, trusted SSL certificate (e.g., Let's Encrypt).

NO Self-Signed Certificates:

❌ Self-Signed certificates will NOT work.

❌ "Generated" certificates will NOT work.

Browsers will block the Secure WebSocket (WSS) connection required for SIP signaling if the certificate is not trusted by a public CA.

WSS Port: Ensure your FusionPBX server allows traffic on the WSS port (usually 7443 for standard installations).

Summary: If you do not have a green lock icon 🔒 with a valid Let's Encrypt certificate on your FusionPBX domain, this application will not connect.

✨ Key Features
Full-Screen Dispatcher Layout: A 3-column professional interface designed for high productivity.

Left: Active Calls & Real-time Status.

Center: Large Dialer, Call Controls, and Active Call Notes.

Right: Persistent Call History list.

Smart Menu Integration: Includes a dynamic app_menu.php script that automatically finds the correct parent UUID for the "Applications" menu. This ensures the menu item works on any FusionPBX server, even those with non-standard database IDs.

Real-Time Note Taking: Add notes to a call while talking. Notes are saved automatically to your local browser history.

Visual History & Export:

View recent calls with status (Answered, Missed, Failed).

See note previews directly in the list.

Export CSV: Download your call history and notes to a CSV file with one click.

Keyboard Support: Use your physical keyboard numpad to dial numbers.

🚀 Installation Guide
Follow these steps to install the application on your FusionPBX server.

1. Download the App
Access your server via SSH and navigate to the FusionPBX applications directory.

Bash
cd /var/www/fusionpbx/app
git clone https://github.com/arsenieciprian/webphone.git
2. Set Permissions
Ensure the web server user (usually www-data) owns the files.

Bash
chown -R www-data:www-data /var/www/fusionpbx/app/webphone
3. Install & Update Database
You need to register the app and the menu in FusionPBX.

Log in to your FusionPBX interface as a Superadmin.

Go to Advanced -> Upgrade.

Check the boxes for:

☑️ App Defaults

☑️ Menu Defaults

☑️ Permission Defaults

Click Execute.

4. Clear Cache & Use
Log out of FusionPBX.

Log back in.

You should now see "Webphone" under the Applications menu.

🌍 Language & Translation
The current interface is hardcoded in Romanian (e.g., "Apelează", "Istoric", "Observații").

Contributions are welcome! If you would like to use this in English or another language:

Fork the repository.

Edit index.php and replace the Romanian strings with English ones.

Submit a Pull Request.

🛠 Troubleshooting
Problem: The status stays on "Offline" or "Registration Failed". Solution:

Open your browser Console (F12).

Look for errors related to WebSocket connection to 'wss://...' failed.

If you see ERR_CERT_AUTHORITY_INVALID, your SSL certificate is invalid or self-signed. You must fix your SSL setup (use Let's Encrypt).

Problem: The menu item doesn't appear. Solution:

Ensure you ran the Upgrade -> Menu Defaults step.

Check app_menu.php permissions.

The app uses a "Smart UUID" detection system (app_menu.php) to find the "Applications" menu. If your server is extremely customized, check your database v_menu_items table.

License: MIT Based on: JsSIP Library
