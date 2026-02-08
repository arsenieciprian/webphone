📘 Voxbee Operator Pro - Documentation
Voxbee Operator Pro is a specialized WebRTC webphone module for FusionPBX. It replaces the standard phone interface with a full-screen, 3-column dispatcher console, designed for high-volume operators who need call history, notes, and quick dialing controls in a single view.
 
⚠️ Prerequisites & Critical Security Warning
WebRTC technology has strict security requirements. This application WILL NOT WORK if these conditions are not met:
•	HTTPS: FusionPBX must be accessed via https://.
•	Valid SSL: You MUST use a valid, trusted certificate (e.g., Let's Encrypt).
•	No Self-Signed: ❌ Self-signed certificates will block the Secure WebSocket (WSS) connection.
•	WSS Port: Ensure port 7443 (or your configured WSS port) is open.
Note: If your browser shows a "Not Secure" warning in the address bar, the phone will fail to register.
 
🌟 Features
1. Smart Menu Integration
The application includes a dynamic menu script (app_menu.php) that automatically detects the correct parent UUID for "Applications" on your specific server.
•	Benefit: Works instantly on any FusionPBX installation, regardless of database customization.
2. Full-Screen Dispatcher Layout
A professional 3-column design:
•	Left Panel: Real-time list of active calls with status indicators.
•	Center Console: Large dial pad, call timer, and active note-taking area.
•	Right Panel: Persistent call history (doesn't disappear on refresh).
3. Productivity Tools
•	Real-Time Notes: Add notes during a call; they are automatically saved to the history log.
•	One-Click Export: Download your entire call history and notes to CSV format for reporting.
•	Keyboard Shortcuts: Use your physical keyboard's numpad to dial numbers instantly.
 
📥 Installation Guide
Step 1: Install the Files
Access your FusionPBX server via SSH and clone the repository into the applications directory.
cd /var/www/fusionpbx/app git clone https://github.com/arsenieciprian/webphone.git
Step 2: Set Permissions
Ensure the web server has ownership of the files.
chown -R www-data:www-data /var/www/fusionpbx/app/webphone
Step 3: Register the App
You must register the application in the FusionPBX database.
1.	Log in to FusionPBX as Superadmin.
2.	Navigate to Advanced -> Upgrade.
3.	Select the following options:
o	☑️ App Defaults
o	☑️ Menu Defaults
o	☑️ Permission Defaults
4.	Click Execute.
Step 4: Access
1.	Log out and log back in to refresh your session.
2.	Go to the Applications menu.
3.	Click on Webphone.
 
🔧 Configuration & Customization
Language
The interface is currently hardcoded in Romanian. To translate it to English or another language, edit the index.php file and replace the text strings (e.g., replace "Apelează" with "Call").
SIP Configuration
The application automatically retrieves SIP credentials (extension and password) for the currently logged-in user using the FusionPBX database. No manual configuration is required on the client side.
 
❓ Troubleshooting
Connection Status: "Offline" / "Registration Failed"
•	Check SSL: Open Developer Tools (F12) -> Console. If you see ERR_CERT_AUTHORITY_INVALID, your certificate is not trusted.
•	Check WSS: Ensure port 7443 is allowed in your firewall.
"Webphone" Menu Missing
•	If the menu does not appear under "Applications", verify that you ran the Menu Defaults upgrade step.
•	The app_menu.php script attempts to find the "Applications" parent menu automatically. If your menu structure is heavily modified, check v_menu_items in your database.

<img width="468" height="656" alt="image" src="https://github.com/user-attachments/assets/ad5f5b3d-9770-411f-974c-03c34cfcaccb" />
