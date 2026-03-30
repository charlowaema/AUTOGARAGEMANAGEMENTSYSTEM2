#!/usr/bin/env bash
# =============================================================================
# AGMS — Download Assets for Offline Use
# Run this ONCE from your project root while you have internet access:
#   chmod +x download-assets.sh
#   bash download-assets.sh
# =============================================================================

set -e

echo ""
echo "╔══════════════════════════════════════════════════════╗"
echo "║   AGMS — Downloading assets for offline support     ║"
echo "╚══════════════════════════════════════════════════════╝"
echo ""

# ── Directories ───────────────────────────────────────────────────────────────
mkdir -p public/js
mkdir -p public/css
mkdir -p public/fonts/barlow
mkdir -p public/fonts/barlow-condensed
mkdir -p public/fonts/jetbrains-mono

# ── 1. Tailwind CDN script ────────────────────────────────────────────────────
echo "▶ Downloading Tailwind CSS CDN script..."
curl -sL "https://cdn.tailwindcss.com" -o public/js/tailwind.js
echo "  ✓ public/js/tailwind.js"

# ── 2. Barlow font (weights 400,500,600,700) ──────────────────────────────────
echo "▶ Downloading Barlow font files..."
declare -A BARLOW=(
  ["400"]="Barlow-Regular"
  ["500"]="Barlow-Medium"
  ["600"]="Barlow-SemiBold"
  ["700"]="Barlow-Bold"
)
for weight in 400 500 600 700; do
  url="https://fonts.gstatic.com/s/barlow/v12/7cHpv4kjgoGqM7E3b8s8yn4.woff2"
  # Fetch the CSS first to get the actual woff2 URL
  css=$(curl -sL "https://fonts.googleapis.com/css2?family=Barlow:wght@${weight}" \
    -H "User-Agent: Mozilla/5.0")
  woff2_url=$(echo "$css" | grep -oP 'https://fonts\.gstatic\.com[^)]+\.woff2' | head -1)
  if [ -n "$woff2_url" ]; then
    curl -sL "$woff2_url" -o "public/fonts/barlow/barlow-${weight}.woff2"
    echo "  ✓ public/fonts/barlow/barlow-${weight}.woff2"
  fi
done

# ── 3. Barlow Condensed (weights 600,700) ─────────────────────────────────────
echo "▶ Downloading Barlow Condensed font files..."
for weight in 600 700; do
  css=$(curl -sL "https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@${weight}" \
    -H "User-Agent: Mozilla/5.0")
  woff2_url=$(echo "$css" | grep -oP 'https://fonts\.gstatic\.com[^)]+\.woff2' | head -1)
  if [ -n "$woff2_url" ]; then
    curl -sL "$woff2_url" -o "public/fonts/barlow-condensed/barlow-condensed-${weight}.woff2"
    echo "  ✓ public/fonts/barlow-condensed/barlow-condensed-${weight}.woff2"
  fi
done

# ── 4. JetBrains Mono (weights 400,500) ──────────────────────────────────────
echo "▶ Downloading JetBrains Mono font files..."
for weight in 400 500; do
  css=$(curl -sL "https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@${weight}" \
    -H "User-Agent: Mozilla/5.0")
  woff2_url=$(echo "$css" | grep -oP 'https://fonts\.gstatic\.com[^)]+\.woff2' | head -1)
  if [ -n "$woff2_url" ]; then
    curl -sL "$woff2_url" -o "public/fonts/jetbrains-mono/jetbrains-mono-${weight}.woff2"
    echo "  ✓ public/fonts/jetbrains-mono/jetbrains-mono-${weight}.woff2"
  fi
done

# ── 5. Generate fonts.css ─────────────────────────────────────────────────────
echo "▶ Generating public/css/fonts.css..."
cat > public/css/fonts.css << 'FONTCSS'
/* ── Barlow ── */
@font-face { font-family:'Barlow'; font-style:normal; font-weight:400; font-display:swap; src:url('/fonts/barlow/barlow-400.woff2') format('woff2'); }
@font-face { font-family:'Barlow'; font-style:normal; font-weight:500; font-display:swap; src:url('/fonts/barlow/barlow-500.woff2') format('woff2'); }
@font-face { font-family:'Barlow'; font-style:normal; font-weight:600; font-display:swap; src:url('/fonts/barlow/barlow-600.woff2') format('woff2'); }
@font-face { font-family:'Barlow'; font-style:normal; font-weight:700; font-display:swap; src:url('/fonts/barlow/barlow-700.woff2') format('woff2'); }

/* ── Barlow Condensed ── */
@font-face { font-family:'Barlow Condensed'; font-style:normal; font-weight:600; font-display:swap; src:url('/fonts/barlow-condensed/barlow-condensed-600.woff2') format('woff2'); }
@font-face { font-family:'Barlow Condensed'; font-style:normal; font-weight:700; font-display:swap; src:url('/fonts/barlow-condensed/barlow-condensed-700.woff2') format('woff2'); }

/* ── JetBrains Mono ── */
@font-face { font-family:'JetBrains Mono'; font-style:normal; font-weight:400; font-display:swap; src:url('/fonts/jetbrains-mono/jetbrains-mono-400.woff2') format('woff2'); }
@font-face { font-family:'JetBrains Mono'; font-style:normal; font-weight:500; font-display:swap; src:url('/fonts/jetbrains-mono/jetbrains-mono-500.woff2') format('woff2'); }
FONTCSS
echo "  ✓ public/css/fonts.css"

# ── Summary ───────────────────────────────────────────────────────────────────
echo ""
echo "╔══════════════════════════════════════════════════════╗"
echo "║   ✅  All assets downloaded successfully!           ║"
echo "║                                                      ║"
echo "║   The app will now work fully offline.              ║"
echo "╚══════════════════════════════════════════════════════╝"
echo ""
echo "Font files in: public/fonts/"
echo "Tailwind JS:   public/js/tailwind.js"
echo "Font CSS:      public/css/fonts.css"
echo ""
