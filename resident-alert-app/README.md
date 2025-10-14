# Disease Surveillance Resident Alert App (PWA)

## 📱 Overview
Progressive Web Application for Pangasinan residents to receive real-time outbreak alerts and health advisories from the Provincial Health Office.

## ✨ Features
- 🔔 Real-time outbreak alerts
- 🗺️ Interactive map with color-coded zones
- 📍 Location-based filtering
- 🌐 Multi-language support (English/Tagalog)
- 📱 Installable as native app
- 🔕 Push notifications
- 📶 Offline mode

## 🛠️ Technology Stack
- **Frontend:** Vue 3 + TypeScript + Vite
- **Styling:** Tailwind CSS
- **PWA:** Vite PWA Plugin
- **Maps:** Leaflet
- **HTTP Client:** Axios
- **State Management:** Pinia
- **Backend API:** Laravel (existing PHOv2 backend)

## 📋 Prerequisites
- Node.js 18+ and npm
- Access to PHOv2 Laravel backend API

## 🚀 Installation

### 1. Install Dependencies
```bash
cd resident-alert-app
npm install
```

### 2. Configure Environment
```bash
cp .env.example .env
```

Edit `.env` file:
```
VITE_API_URL=http://127.0.0.1:8000/api/v1/public
VITE_APP_NAME=PHO Alert
```

### 3. Development Server
```bash
npm run dev
```

### 4. Build for Production
```bash
npm run build
```

### 5. Preview Production Build
```bash
npm run preview
```

## 📱 Installation as PWA

### Android/iOS
1. Open the app in Chrome/Safari
2. Tap the menu button (⋮)
3. Select "Install App" or "Add to Home Screen"
4. The app icon will appear on your home screen

## 🏗️ Project Structure
```
resident-alert-app/
├── public/
│   ├── icons/              # PWA icons
│   └── manifest.json       # PWA manifest
├── src/
│   ├── assets/             # Images, styles
│   ├── components/         # Vue components
│   │   ├── AlertCard.vue
│   │   ├── AlertMap.vue
│   │   └── ...
│   ├── views/              # Page components
│   │   ├── AlertsView.vue
│   │   ├── MapView.vue
│   │   └── SettingsView.vue
│   ├── stores/             # Pinia stores
│   ├── services/           # API services
│   ├── types/              # TypeScript types
│   ├── App.vue
│   └── main.ts
├── index.html
├── vite.config.ts
├── tailwind.config.js
└── package.json
```

## 🔌 API Endpoints
- `GET /api/v1/public/alerts` - List all active alerts
- `GET /api/v1/public/alerts/{id}` - Get alert details
- `GET /api/v1/public/alerts-map` - Get map data
- `GET /api/v1/public/alerts-statistics` - Get statistics
- `GET /api/v1/public/municipalities` - Get municipalities list
- `GET /api/v1/public/barangays` - Get barangays list

## 📲 Push Notifications Setup
1. Configure Firebase Cloud Messaging (FCM)
2. Add FCM credentials to backend
3. Enable notifications in app settings

## 🌍 Multi-language Support
- English (default)
- Tagalog/Filipino

## 📄 License
Provincial Health Office - Pangasinan

## 👥 Support
Contact: [Your PHO Contact Information]
