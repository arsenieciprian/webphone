# 📘 Voxbee Operator Pro - Documentation

**Voxbee Operator Pro** is a specialized WebRTC webphone module for **FusionPBX**. It replaces the standard phone interface with a **full-screen, 3-column dispatcher console**, designed for high-volume operators who need call history, notes, and quick dialing controls in a single view.

---

## ⚠️ Prerequisites & Critical Security Warning

**WebRTC technology requires a secure environment. This application WILL NOT WORK if these conditions are not met:**

| Requirement | Details |
| :--- | :--- |
| **HTTPS** | The FusionPBX interface must be accessed via `https://`. |
| **Valid SSL** | You **MUST** use a valid, trusted certificate (e.g., **Let's Encrypt**). |
| **No Self-Signed** | ❌ Self-signed or "Generated" certificates will block the WSS connection. |
| **WSS Port** | Ensure port **7443** (or your WSS port) is open and accessible. |

> **Note:** If your browser address bar shows a "Not Secure" warning, the phone will fail to register.

---

## 🌟 Features

### 1. Smart Menu Integration
The application includes a dynamic menu script (`app_menu.php`) that automatically detects the correct parent UUID for "Applications" on your specific server.
* **Benefit:** Works instantly on any FusionPBX installation, regardless of database customization.

### 2. Full-Screen Dispatcher Layout
A professional 3-column design:
* **Left Panel:** Real-time list of active calls with status indicators.
* **Center Console:** Large dial pad, call timer, and active note-taking area.
* **Right Panel:** Persistent call history (doesn't disappear on refresh).

### 3. Productivity Tools
* **Real-Time Notes:** Add notes during a call; they are automatically saved to the history log.
* **One-Click Export:** Download your entire call history and notes to **CSV** format for reporting.
* **Keyboard Shortcuts:** Use your physical keyboard's numpad to dial numbers instantly.

---

## 📥 Installation Guide

### Step 1: Install the Files
Access your FusionPBX server via SSH and clone the repository into the applications directory.

```bash
cd /var/www/fusionpbx/app
git clone [https://github.com/arsenieciprian/webphone.git](https://github.com/arsenieciprian/webphone.git)

tep 2: Set Permissions
Ensure the web server has ownership of the files to allow proper execution.

Bash
chown -R www-data:www-data /var/www/fusionpbx/app/webphone
Step 3: Register the App
You must register the application in the FusionPBX database.

Log in to the FusionPBX Web Interface as Superadmin.

Navigate to Advanced -> Upgrade.

Select the following options:

✅ App Defaults

✅ Menu Defaults

✅ Permission Defaults

Click Execute at the top right.

Step 4: Access
Log out and log back in to refresh your session.

Go to the Applications menu.

Click on Webphone.

🔧 Configuration & Customization
Language
The interface is currently hardcoded in Romanian. To translate it to English or another language, edit the index.php file and replace the text strings (e.g., replace "Apelează" with "Call").

SIP Configuration
The application automatically retrieves SIP credentials (extension and password) for the currently logged-in user using the FusionPBX database. No manual configuration is required on the client side.
