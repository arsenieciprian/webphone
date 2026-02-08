# 📘 Voxbee Operator Pro - FusionPBX Webphone

**Voxbee Operator Pro** is a specialized, modern WebRTC webphone module designed for **FusionPBX**.

It replaces the standard phone interface with a **full-screen, 3-column dispatcher console**, custom-built for high-volume operators who need active call management, real-time notes, and instant history access in a single unified view.

---

## ⚠️ Prerequisites & Critical Security Warning

**WebRTC technology requires a secure environment. This application WILL NOT WORK if these conditions are not met:**

| Requirement | Details |
| :--- | :--- |
| **HTTPS** | FusionPBX must be accessed via `https://`. |
| **Valid SSL** | You **MUST** use a valid, trusted certificate (e.g., **Let's Encrypt**). |
| **No Self-Signed** | ❌ Self-signed or "Generated" certificates will block the WSS connection. |
| **WSS Port** | Ensure port **7443** (or your WSS port) is open and accessible. |

> **Note:** If your browser address bar shows a "Not Secure" warning, the phone will fail to register.

---

## 🌟 Key Features

### 1. Professional Dispatcher Layout (3 Columns)
* **Left Panel (Active Calls):** Real-time list of calls. Includes status indicators (Ringing, Connected, Hold) and **live note previews** (yellow text) so you can see details without clicking.
* **Center Console (Workspace):** Large dial pad, huge call timer (seconds included), call controls, and a dedicated note-taking area.
* **Right Panel (History):** Persistent call history list that updates instantly.

### 2. Smart Menu Integration
Includes a dynamic `app_menu.php` script that automatically detects the correct parent UUID for the "Applications" menu.
* **Benefit:** Works instantly on any FusionPBX installation, regardless of database customization or version.

### 3. Productivity & Real-Time Sync
* **Live Notes:** Notes typed in the center console appear **instantly** in the sidebar list.
* **Keyboard Support:** Use your physical keyboard's numpad to dial numbers naturally.
* **One-Click Export:** Download your entire call history and notes to **CSV** format for reporting.

---

## 📥 Installation

### 1. Install the Files
Access your FusionPBX server via SSH and clone the repository into the applications directory.

```bash
cd /var/www/fusionpbx/app
git clone [https://github.com/arsenieciprian/webphone.git](https://github.com/arsenieciprian/webphone.git)

### 2. Set Permissions
Ensure the web server user owns the files to allow proper execution.

```bash
chown -R www-data:www-data /var/www/fusionpbx/app/webphone



3. Register the App
You must register the application in the FusionPBX database.

Log in to the FusionPBX Web Interface as Superadmin.

Navigate to Advanced -> Upgrade.

Check the following options:

✅ App Defaults

✅ Menu Defaults

✅ Permission Defaults

Click Execute at the top right.

4. Access
Log out and log back in to refresh your session.

Go to the Applications menu.

Click on Webphone.

🔧 Configuration
Language
The interface is currently hardcoded in Romanian. To translate it to English or another language:

Edit index.php.

Replace text strings (e.g., replace "Apelează" with "Call", "Observații" with "Notes").

SIP Configuration
The application is Zero-Config for the client. It automatically retrieves SIP credentials (extension and password) for the currently logged-in user directly from the FusionPBX database.

🧠 Credits & Acknowledgements
This project was built with the assistance of Google Gemini (AI) for logic implementation, UI structure, and dynamic menu scripting.

Powered by the excellent open-source library:

JsSIP - The JavaScript SIP library.

License: MIT
