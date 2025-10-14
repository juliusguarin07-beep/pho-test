# How to Run the Resident Alert App 📱

## Quick Start

The **resident-alert-app** is already running! 🎉

### Access the App:

**On your computer:**
- Open browser: http://localhost:5173
- Or: http://127.0.0.1:5173

**On your phone (same WiFi network):**
- Open browser: http://192.168.100.84:5173
- Replace `192.168.100.84` with your computer's IP address

---

## If Not Running, Start It:

### Step 1: Make sure the Laravel backend is running
```powershell
# In the main PHOv2 folder
php artisan serve
```
This starts the API at: http://127.0.0.1:8000

### Step 2: Start the mobile app
```powershell
# Navigate to the app folder
cd resident-alert-app

# Install dependencies (only needed once)
npm install

# Start the development server
npm run dev
```

### Step 3: Open in browser
- Computer: http://localhost:5173
- Phone: http://YOUR_COMPUTER_IP:5173

---

## Available Commands

### Development (Hot Reload)
```powershell
npm run dev
```
- Starts Vite dev server on port 5173
- Auto-reloads when you edit files
- Shows compilation errors in terminal

### Build for Production
```powershell
npm run build
```
- Compiles and minifies for production
- Outputs to `dist/` folder
- Checks TypeScript types first

### Preview Production Build
```powershell
npm run preview
```
- Serves the production build locally
- Test before deploying

### Lint Code
```powershell
npm run lint
```
- Checks code quality
- Auto-fixes issues when possible

---

## Current Configuration

### API Endpoint (.env file)
```
VITE_API_URL=http://127.0.0.1:8000/api/v1/public
```

**For phone testing**, change to:
```
VITE_API_URL=http://192.168.100.84:8000/api/v1/public
```

**Important:** After changing .env, restart the dev server!

### Network Access
The app is configured to accept network connections, so you can access it from your phone on the same WiFi.

---

## Troubleshooting

### Port Already in Use
**Error:** `Port 5173 is already in use`

**Solution:**
1. The app is already running! Check http://localhost:5173
2. Or kill the process:
   ```powershell
   # Find process using port 5173
   netstat -ano | findstr :5173
   
   # Kill it (replace PID with actual process ID)
   taskkill /PID <PID> /F
   ```

### API Connection Errors
**Error:** "Network Error" or "No Active Alerts"

**Fix:**
1. Make sure Laravel is running: `php artisan serve`
2. Check .env file has correct API URL
3. Restart dev server after changing .env
4. Clear browser cache (Ctrl+Shift+R)

### Module Not Found Errors
**Error:** Cannot find module 'xyz'

**Fix:**
```powershell
# Delete node_modules and reinstall
rm -r node_modules
rm package-lock.json
npm install
```

### TypeScript Errors
**Error:** Type errors during build

**Fix:**
```powershell
# Check types without building
npx vue-tsc --noEmit

# Build without type checking (not recommended)
vite build
```

### CSS Not Loading
**Error:** Styles not applying

**Fix:**
1. Check main.css import order
2. Restart dev server
3. Clear browser cache
4. Check Tailwind config

---

## What You'll See

When the app starts successfully:

```
VITE v5.0.10  ready in 523 ms

➜  Local:   http://localhost:5173/
➜  Network: http://192.168.100.84:5173/
➜  press h to show help
```

### App Features:
✅ **Splash Screen** - First launch welcome
✅ **Alerts Tab** - View all outbreak alerts
✅ **Map Tab** - Interactive map with markers
✅ **Settings Tab** - Municipality filter
✅ **PWA** - Installable on phone

---

## For Android Installation

See: `install-guide.md` in the resident-alert-app folder

Quick steps:
1. Make sure Laravel is running
2. Make sure Vite is running
3. Connect phone to same WiFi
4. Open http://YOUR_COMPUTER_IP:5173 on phone
5. Click "Add to Home Screen" in browser menu
6. App will install as PWA

---

## Stopping the Server

### Stop Vite Dev Server
- Press `Ctrl + C` in the terminal
- Or close the terminal window

### Stop All Servers
```powershell
# Stop Laravel
Ctrl + C (in PHP terminal)

# Stop Vite
Ctrl + C (in Vite terminal)
```

---

## Project Structure

```
resident-alert-app/
├── src/
│   ├── views/          # Main pages (Alerts, Map, Settings)
│   ├── components/     # Reusable components
│   ├── stores/         # Pinia state management
│   ├── services/       # API calls
│   ├── router/         # Vue Router config
│   └── assets/         # CSS, images
├── public/             # Static files
├── .env               # Environment variables
├── vite.config.ts     # Vite configuration
└── package.json       # Dependencies

```

---

## Status Check

### Is Laravel Running?
Visit: http://127.0.0.1:8000/api/v1/public/alerts

Should see: JSON data with outbreak alerts

### Is Vite Running?
Visit: http://localhost:5173

Should see: The mobile app interface

### Are Both Working Together?
1. Open mobile app
2. Go to Alerts tab
3. Should see outbreak alerts loading
4. Check Map tab for markers

---

## Need Help?

**Common Issues:**
- ❌ Blank page → Check browser console (F12)
- ❌ No data → Check Laravel is running
- ❌ Can't connect from phone → Check firewall/WiFi
- ❌ CSS broken → Restart Vite dev server

**Check Logs:**
- Vite terminal: Shows compilation errors
- Browser console: Shows JavaScript errors
- Laravel terminal: Shows API request errors

---

## Summary

**Already Running:** http://localhost:5173 ✅

**To Start Fresh:**
1. `cd resident-alert-app`
2. `npm run dev`
3. Open http://localhost:5173

**For Phone Access:**
1. Get your computer's IP: `ipconfig` (look for IPv4)
2. Update .env: `VITE_API_URL=http://YOUR_IP:8000/api/v1/public`
3. Restart `npm run dev`
4. Open http://YOUR_IP:5173 on phone

Enjoy your mobile app! 🎉
