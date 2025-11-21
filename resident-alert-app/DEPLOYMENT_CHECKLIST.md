# ✅ Vercel Deployment Checklist

## Pre-Deployment

- [ ] Run `npm run build` locally and verify it succeeds
- [ ] Test the production build with `npm run preview`
- [ ] All code committed and pushed to GitHub
- [ ] `.env.production` has correct API URL
- [ ] Backend API is live and accessible

## Vercel Setup

- [ ] Create Vercel account at https://vercel.com
- [ ] Connect GitHub repository to Vercel
- [ ] Create new project in Vercel
- [ ] Vercel detects Vite framework automatically
- [ ] Build command: `npm run build` ✓
- [ ] Output directory: `dist` ✓

## Environment Variables

- [ ] Set `VITE_API_URL` in Vercel:
  - Example: `https://your-backend-domain.com/api/v1/public`
- [ ] Apply to: Production, Preview, Development
- [ ] Save and trigger redeploy

## After Deployment

- [ ] Visit deployed URL
- [ ] Check app loads without errors
- [ ] Test API calls work (alerts load)
- [ ] Check browser console for errors (F12)
- [ ] Test on mobile phone
- [ ] Test PWA install functionality
- [ ] Share URL with team

## Troubleshooting

If build fails:
- [ ] Check `package.json` has all dependencies
- [ ] Run `npm install` locally
- [ ] Check Vercel build logs
- [ ] Ensure Node version compatibility

If app doesn't load:
- [ ] Check browser console errors
- [ ] Verify `VITE_API_URL` is correct
- [ ] Test API endpoint directly in browser
- [ ] Check CORS is enabled on backend

## Deploy Command (Alternative)

If using Vercel CLI:

```bash
# Login to Vercel
npm i -g vercel
vercel login

# Deploy
npm run deploy
```

## Rollback

To rollback to previous deployment:
1. Go to Vercel Dashboard
2. Select project
3. Deployments tab
4. Click on previous deployment
5. Click "Redeploy"

---

**Status:** Ready for deployment ✅
