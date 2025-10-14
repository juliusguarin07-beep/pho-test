# 📱 Install PHO Alert on Your Android Phone

## 🎯 Your Network Setup

- **Computer IP:** `192.168.100.84`
- **Mobile App URL:** `http://192.168.100.84:5173`
- **API Backend URL:** `http://192.168.100.84:8000`

---

## 🖥️ Part 1: Setup on Computer

### Step 1: Start Laravel Backend
```bash
# In terminal 1
php artisan serve --host=0.0.0.0
```

This allows your phone to access the API from the network.

### Step 2: Start Mobile App
```bash
# In terminal 2
cd resident-alert-app
npm run dev
```

You should see:
```
➜  Local:   http://localhost:5173/
➜  Network: http://192.168.100.84:5173/
```

✅ Keep both terminals running!

---

## 📱 Part 2: Install on Android Phone

### Step 3: Connect to WiFi
- Ensure your phone is connected to the **SAME WiFi** as your computer
- Check WiFi name matches exactly

### Step 4: Open Chrome Browser
1. Open **Google Chrome** (must use Chrome for PWA features)
2. Type in the address bar: `http://192.168.100.84:5173`
3. Press **Go**

### Step 5: Test the App
- App should load with a green header showing "Outbreak Alerts"
- Check if the Leptospirosis alert is showing
- Try switching between tabs: Alerts, Map, Settings
- If data doesn't load, check troubleshooting below

### Step 6: Install App to Home Screen
1. Tap the **3-dot menu (⋮)** in top-right corner of Chrome
2. Look for one of these options:
   - **"Install app"**
   - **"Add to Home screen"**
   - **"Install PHO Alert"**
3. Tap the install option
4. A popup will appear - tap **"Install"** or **"Add"**
5. The app will be added to your home screen!

### Step 7: Launch Your Installed App
1. Go to your Android home screen
2. Look for the **"PHO Alert"** icon (green background with alert symbol)
3. Tap to launch
4. App opens in **fullscreen mode** (no browser UI - looks like a native app!)

---

## ⚠️ Troubleshooting

### Problem: Can't access the URL
**Symptoms:** "This site can't be reached" or timeout error

**Solutions:**
1. **Check Firewall:** Windows Firewall might be blocking connections
   - Go to Windows Defender Firewall
   - Click "Allow an app through firewall"
   - Allow Node.js and PHP

2. **Verify Same WiFi:** Computer and phone MUST be on the same network
   - Not mobile data!
   - Check WiFi name on both devices

3. **Try Different IP:** Your computer might have multiple IPs
   ```bash
   ipconfig
   ```
   Look for other IPv4 addresses and try those

### Problem: "Install app" option not showing
**Solutions:**
- Must use **Chrome browser** (not Firefox, Samsung Internet, etc.)
- App must load successfully first
- Try looking for "Add to Home screen" instead
- Reload the page (pull down to refresh)

### Problem: App loads but no alerts showing
**Solutions:**
1. Check Laravel backend is running:
   - Visit: `http://192.168.100.84:8000/api/v1/public/alerts` in phone's browser
   - Should show JSON data with alert information

2. Check console for errors:
   - In Chrome on phone, type: `chrome://inspect`
   - Look for error messages

---

## ✨ App Features After Installation

### What You Get:
- ✅ **Offline Support** - View previously loaded alerts without internet
- ✅ **Push Notifications** - Get notified of new outbreak alerts (when backend supports it)
- ✅ **Native Feel** - Looks and behaves like a real Android app
- ✅ **Home Screen Icon** - Quick access from home screen
- ✅ **Fullscreen Mode** - No browser UI clutter
- ✅ **Fast Loading** - Cached assets for instant startup

### App Tabs:
1. **Alerts Tab** (🔔)
   - View all active outbreak alerts
   - Filter by alert level (Red, Orange, Yellow, Green)
   - See disease info, location, and affected areas

2. **Map Tab** (🗺️)
   - Interactive map of Pangasinan
   - Color-coded markers for each alert
   - Click markers to see alert details
   - View affected municipalities

3. **Settings Tab** (⚙️)
   - Filter alerts by municipality
   - Select specific barangay
   - Enable/disable notifications
   - Change language (English/Tagalog)

---

## 🔄 For Production Deployment (Future)

When ready to deploy for public use:

1. **Build for Production:**
   ```bash
   npm run build
   ```

2. **Deploy to Hosting:**
   - **Free Options:** Netlify, Vercel, GitHub Pages
   - **Paid Options:** Your Laravel server, DigitalOcean, AWS

3. **Update API URL:**
   - Edit `.env` to point to your production backend
   - Example: `VITE_API_URL=https://pho-pangasinan.gov.ph/api/v1/public`

4. **Users Install From Public URL:**
   - Share your deployed URL
   - Users visit in Chrome
   - Install directly from the web!

---

## 📞 Need Help?

If you encounter issues:
1. Check both terminals are running (backend + frontend)
2. Verify firewall settings
3. Confirm same WiFi network
4. Try accessing `http://192.168.100.84:5173` from computer's browser first
5. Check Laravel logs: `storage/logs/laravel.log`

---

## 🎉 Success!

Once installed, you can:
- Close Chrome browser
- Launch app from home screen anytime
- App works even without computer (for cached data)
- Share with colleagues - they can install too!

**Enjoy your new PHO Alert mobile app! 📱🏥**
