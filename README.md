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
