Voxbee Operator Pro - Documentation

Voxbee Operator Pro is a specialized WebRTC webphone module for FusionPBX.
It replaces the standard phone interface with a full-screen, 3-column dispatcher console,
designed for high-volume operators who need call handling, notes, and call history in a single unified view.

PREREQUISITES & SECURITY WARNING

WebRTC requires a secure environment. This application WILL NOT WORK unless all conditions below are met:

- FusionPBX must be accessed via HTTPS
- A valid trusted SSL certificate is required (e.g. Let's Encrypt)
- Self-signed or generated certificates will block WSS connections
- WSS port 7443 (or your custom port) must be open and reachable

If the browser shows "Not Secure", the phone will not register.

FEATURES

1. Smart Menu Integration
Dynamic app_menu.php script automatically detects the correct Applications menu UUID.
Works out-of-the-box on any FusionPBX installation and survives upgrades.

2. Full-Screen Dispatcher Layout
- Left panel: real-time active calls
- Center panel: dial pad, call timer, live notes
- Right panel: persistent call history

3. Productivity Tools
- Real-time call notes
- CSV export of call history
- Keyboard numeric pad dialing

INSTALLATION

1. Install files
cd /var/www/fusionpbx/app
git clone https://github.com/arsenieciprian/webphone.git

2. Set permissions
chown -R www-data:www-data /var/www/fusionpbx/app/webphone

3. Register application
Login as Superadmin
Advanced -> Upgrade
Enable:
- App Defaults
- Menu Defaults
- Permission Defaults
Click Execute

4. Access
Logout and login again
Applications -> Webphone

CONFIGURATION

Language
Interface text is hardcoded in Romanian.
Edit index.php to translate strings.

SIP Configuration
SIP credentials are automatically retrieved for the logged-in user from the FusionPBX database.

CREDITS

- Google Gemini (AI assistance)
- JsSIP JavaScript SIP over WebRTC library https://jssip.net/
- FusionPBX & FreeSWITCH open-source community

LICENSE

Provided as-is. Free to modify for FusionPBX deployments.
