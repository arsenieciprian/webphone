📘 Voxbee Operator Pro – Documentation

Voxbee Operator Pro is a specialized WebRTC webphone module for FusionPBX.
It replaces the standard phone interface with a full-screen, 3-column dispatcher console, designed for high-volume operators who need call handling, notes, and call history in a single unified view.

⚠️ Prerequisites & Critical Security Warning

WebRTC requires a secure environment. This application WILL NOT WORK unless all conditions below are met:

Requirement	Details
HTTPS	FusionPBX must be accessed via https://
Valid SSL Certificate	A trusted certificate is mandatory (e.g. Let’s Encrypt)
No Self-Signed Certs	❌ Self-signed or “Generated” certificates will block WSS
WSS Port Open	Port 7443 (or your custom WSS port) must be reachable

Important: If your browser shows “Not Secure”, the phone will not register.

🌟 Features
1. Smart Menu Integration

Includes a dynamic app_menu.php script that automatically detects the correct Applications menu UUID, regardless of database structure.

Benefit:
✔ Works out-of-the-box on any FusionPBX installation
✔ No manual menu editing required
✔ Survives upgrades without losing menu entries

2. Full-Screen Dispatcher Layout

Professional 3-column operator console:

Left Panel: Real-time active calls with status indicators

Center Panel: Large dial pad, call timer, and live note-taking

Right Panel: Persistent call history (not lost on refresh)

3. Productivity Tools

✍️ Real-Time Notes – saved automatically to call history

📊 CSV Export – download full call history with notes

⌨️ Keyboard Dialing – use the numeric keypad for fast dialing

📥 Installation Guide
Step 1: Install the Files

Connect to your FusionPBX server via SSH and clone the repository:

cd /var/www/fusionpbx/app
git clone https://github.com/arsenieciprian/webphone.git

Step 2: Set Permissions

Ensure the web server user owns the files:

chown -R www-data:www-data /var/www/fusionpbx/app/webphone

Step 3: Register the Application

Log in to FusionPBX as Superadmin

Go to Advanced → Upgrade

Enable:

✅ App Defaults

✅ Menu Defaults

✅ Permission Defaults

Click Execute

Step 4: Access the Application

Log out and log back in

Navigate to Applications

Click Webphone

🔧 Configuration & Customization
Language

The interface is currently hardcoded in Romanian.

To translate it:

Edit index.php

Replace text strings (example: Apelează → Call)

SIP Configuration

No manual SIP setup required.

The application automatically retrieves:

Extension

SIP password

…for the currently logged-in user directly from the FusionPBX database.

🧠 Credits & Acknowledgements

This project was made possible with the help of:

Google Gemini – AI assistance for logic, structure, and implementation ideas

JsSIP – JavaScript SIP over WebRTC library
🔗 https://jssip.net/

Special thanks to the open-source community around FusionPBX and FreeSWITCH.

📄 License

This project is provided as-is.
You are free to modify and adapt it for your own FusionPBX deployments.
