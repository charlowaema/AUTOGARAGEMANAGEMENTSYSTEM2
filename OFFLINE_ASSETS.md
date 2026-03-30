# AGMS — Offline Asset Setup

By default the app loads Tailwind CSS and fonts from the internet (CDN).
To make it work fully offline, run the download script **once** while connected:

## Quick Setup

```bash
# From your Laravel project root:
chmod +x download-assets.sh
bash download-assets.sh
```

This downloads everything into `public/`:
```
public/
├── js/
│   └── tailwind.js                        ← Tailwind CSS engine
├── css/
│   └── fonts.css                          ← @font-face declarations
└── fonts/
    ├── barlow/
    │   ├── barlow-400.woff2
    │   ├── barlow-500.woff2
    │   ├── barlow-600.woff2
    │   └── barlow-700.woff2
    ├── barlow-condensed/
    │   ├── barlow-condensed-600.woff2
    │   ├── barlow-condensed-700.woff2
    │   └── barlow-condensed-800.woff2
    └── jetbrains-mono/
        ├── jetbrains-mono-400.woff2
        └── jetbrains-mono-500.woff2
```

## How it works

The layout loads `public/js/tailwind.js` locally.
If that file doesn't exist yet, it **automatically falls back to the CDN** —
so the app always works whether or not you've run the script.

After running the script, the app works with **zero internet dependency**.

## Fonts fallback

If a font file is missing, the browser falls back to system sans-serif —
the app remains fully usable, just with a different font.

## After deployment

Run the script once on your production server. The downloaded files are
static and never need to be updated unless you change the Tailwind version.
