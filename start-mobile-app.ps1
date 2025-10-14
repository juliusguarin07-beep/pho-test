#!/usr/bin/env pwsh
# Script to start the Resident Alert App

Write-Host "====================================" -ForegroundColor Cyan
Write-Host "  Starting Resident Alert App" -ForegroundColor Cyan
Write-Host "====================================" -ForegroundColor Cyan
Write-Host ""

# Navigate to the resident-alert-app directory
Set-Location -Path "$PSScriptRoot\resident-alert-app"

Write-Host "Current Directory: $(Get-Location)" -ForegroundColor Yellow
Write-Host ""

# Check if node_modules exists
if (-not (Test-Path "node_modules")) {
    Write-Host "Installing dependencies..." -ForegroundColor Yellow
    npm install
    Write-Host ""
}

Write-Host "Starting Vite development server..." -ForegroundColor Green
Write-Host ""
Write-Host "Access the app at:" -ForegroundColor Green
Write-Host "  - Computer: http://localhost:5173" -ForegroundColor Cyan
Write-Host "  - Phone:    http://192.168.100.84:5173" -ForegroundColor Cyan
Write-Host ""
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Yellow
Write-Host ""

# Start the dev server
npm run dev
