# PHO Alert - Vercel Deployment Guide

## 📋 Prerequisites
- Vercel account (free at https://vercel.com)
- GitHub repository with this code
- Production backend API URL

## 🚀 Deployment Steps

### 1. Push Code to GitHub

```bash
git add .
git commit -m "Prepare for Vercel deployment"
git push origin main
```

### 2. Import Project to Vercel

1. Go to [Vercel Dashboard](https://vercel.com/dashboard)
2. Click **"New Project"**
3. Select **"Import Git Repository"**
4. Choose your GitHub repository
5. Click **"Import"**

### 3. Configure Environment Variables

1. In Vercel Project Settings → **Environment Variables**
2. Add:
   - **Name:** `VITE_API_URL`
   - **Value:** `https://your-pho-backend.com/api/v1/public`
   - **Select environments:** Production, Preview, Development

3. Click **"Save"**

### 4. Deploy

Vercel will automatically detect:
- **Framework:** Vite
- **Build Command:** `npm run build`
- **Output Directory:** `dist`

Click **"Deploy"** to start deployment.

### 5. Configure Custom Domain (Optional)

1. Go to **Settings** → **Domains**
2. Add your custom domain
3. Follow DNS configuration instructions

## 📝 Update Backend API URL

After deployment, update your production API URL:

**In Vercel Dashboard:**
1. Project Settings → Environment Variables
2. Update `VITE_API_URL` to your backend production URL
3. Redeploy

**Redeploy command:**
```bash
vercel --prod
```

## 🔄 Continuous Deployment

Every push to `main` branch automatically:
- Triggers new build
- Runs tests (if configured)
- Deploys to Vercel preview
- Auto-deploys to production on merge to `main`

## 📱 PWA Features

After deployment, the app will be installable:
1. Open the deployed URL in Chrome
2. Tap menu (⋮) → **"Install app"**
3. App appears on home screen

## 🔒 Security Headers

Vercel automatically applies security headers from `vercel.json`:
- X-Content-Type-Options: nosniff
- X-Frame-Options: SAMEORIGIN
- X-XSS-Protection: 1; mode=block
- Cache-Control headers

## 📊 Monitor Deployment

1. **Analytics:** Vercel Dashboard → Analytics
2. **Logs:** Vercel Dashboard → Deployments → View Logs
3. **Performance:** Vercel Dashboard → Analytics → Web Vitals

## 🐛 Troubleshooting

### Build Fails
- Check `npm run build` works locally
- Verify all dependencies in `package.json`
- Check Node.js version compatibility

### App doesn't load
- Check browser console for errors
- Verify `VITE_API_URL` is correct
- Check CORS is enabled on backend

### API calls fail
- Verify backend API is accessible
- Check CORS headers on backend
- Verify environment variables are set

## 📞 Support

For issues:
1. Check Vercel logs: Dashboard → Deployments → View Logs
2. Check browser console errors (F12)
3. Verify backend API is running
4. Test API endpoint directly in browser

---

**Deployed URL:** Will be provided after deployment
**Status:** [![Vercel Status](https://img.shields.io/badge/Vercel-Ready-blue)](https://vercel.com)
