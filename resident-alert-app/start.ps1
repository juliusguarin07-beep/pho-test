# PHO Resident Alert App - Quick Start Script

Write-Host "`n" -NoNewline
Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║                                                              ║" -ForegroundColor Cyan
Write-Host "║        📱 PHO RESIDENT ALERT APP - QUICK START 🚀          ║" -ForegroundColor Cyan
Write-Host "║                                                              ║" -ForegroundColor Cyan
Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host "`n"

Write-Host "✨ Installing dependencies..." -ForegroundColor Yellow
Write-Host "`n"

# Navigate to app directory
Set-Location -Path "C:\Users\TEMP\Desktop\PHOv2\resident-alert-app"

# Install dependencies
npm install

if ($LASTEXITCODE -eq 0) {
    Write-Host "`n"
    Write-Host "✅ Installation complete!" -ForegroundColor Green
    Write-Host "`n"

    Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
    Write-Host "  🎉 READY TO START!" -ForegroundColor Green
    Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
    Write-Host "`n"

    Write-Host "📋 What's been created:" -ForegroundColor Yellow
    Write-Host "  ✅ Backend API (7 endpoints)" -ForegroundColor Green
    Write-Host "  ✅ Vue 3 + TypeScript PWA" -ForegroundColor Green
    Write-Host "  ✅ 4 Main Views (Alerts, Map, Settings, Detail)" -ForegroundColor Green
    Write-Host "  ✅ Interactive Leaflet Map" -ForegroundColor Green
    Write-Host "  ✅ State Management (Pinia)" -ForegroundColor Green
    Write-Host "  ✅ Responsive Mobile Design" -ForegroundColor Green
    Write-Host "  ✅ PWA (Installable & Offline)" -ForegroundColor Green
    Write-Host "`n"

    Write-Host "🚀 To start the development server:" -ForegroundColor Yellow
    Write-Host "  npm run dev" -ForegroundColor Cyan
    Write-Host "`n"

    Write-Host "🌐 Then open in your browser:" -ForegroundColor Yellow
    Write-Host "  http://localhost:5173" -ForegroundColor Cyan
    Write-Host "`n"

    Write-Host "📱 Test on mobile:" -ForegroundColor Yellow
    Write-Host "  Press F12 → Click device toggle → Select iPhone/Android" -ForegroundColor White
    Write-Host "`n"

    Write-Host "📖 Full documentation:" -ForegroundColor Yellow
    Write-Host "  Read MOBILE_APP_COMPLETE.md for detailed instructions" -ForegroundColor White
    Write-Host "`n"

    Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
    Write-Host "`n"

    # Ask if user wants to start the server
    $start = Read-Host "Start the development server now? (Y/N)"
    if ($start -eq "Y" -or $start -eq "y") {
        Write-Host "`n"
        Write-Host "🚀 Starting development server..." -ForegroundColor Green
        Write-Host "   Press Ctrl+C to stop the server" -ForegroundColor Yellow
        Write-Host "`n"
        npm run dev
    } else {
        Write-Host "`n"
        Write-Host "👍 When you're ready, run:" -ForegroundColor Cyan
        Write-Host "   npm run dev" -ForegroundColor White
        Write-Host "`n"
    }
} else {
    Write-Host "`n"
    Write-Host "❌ Installation failed!" -ForegroundColor Red
    Write-Host "   Try running manually: npm install" -ForegroundColor Yellow
    Write-Host "`n"
}
