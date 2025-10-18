# Asset Optimization Guide

This document provides guidelines for optimizing marketing assets for web delivery and CodeCanyon submission.

## Overview

All marketing assets must be optimized for:
- Fast loading times
- Professional quality
- CodeCanyon requirements
- WordPress.org standards
- Cross-browser compatibility

## Screenshot Optimization

### Target Specifications
- **Format:** PNG for UI screenshots, JPEG for photos
- **File Size:** Maximum 500 KB per screenshot (target: 200-300 KB)
- **Resolution:** Minimum 1920x1080 (Full HD)
- **Color Space:** sRGB (standard web color space)
- **Bit Depth:** 8-bit per channel (24-bit color)

### Optimization Process

#### Step 1: Capture
- Use native OS screenshot tools or browser extensions
- Capture at 100% zoom (no scaling)
- Ensure clean, uncluttered interface
- Verify no personal information is visible

#### Step 2: Edit (if needed)
- Crop to relevant content
- Remove unnecessary browser chrome
- Add annotations if helpful (arrows, highlights)
- Ensure consistent styling across screenshots

#### Step 3: Optimize File Size
Use one or more of these tools:

**TinyPNG (Recommended)**
- Website: https://tinypng.com/
- Lossy compression for PNG files
- Typically achieves 50-70% size reduction
- No visible quality loss for UI screenshots
- Free for up to 20 files at once

**ImageOptim (Mac)**
- Website: https://imageoptim.com/
- Desktop app for Mac
- Lossless and lossy compression options
- Batch processing support
- Removes metadata

**Squoosh (Web-based)**
- Website: https://squoosh.app/
- Advanced compression options
- Side-by-side comparison
- Fine-tune quality vs. size
- Works in browser (no upload to server)

**OptiPNG (Command-line)**
```bash
optipng -o7 screenshot.png
```
- Lossless optimization
- Maximum compression: `-o7`
- Preserves perfect quality

#### Step 4: Verify
- Check file size (< 500 KB)
- View at 100% to verify quality
- Test in different browsers
- Confirm colors display correctly

### Optimization Commands

For batch processing:

```bash
# Install ImageMagick (if not installed)
# Mac: brew install imagemagick
# Linux: apt-get install imagemagick
# Windows: https://imagemagick.org/script/download.php

# Resize large screenshots
for file in *.png; do
  convert "$file" -resize 1920x1080\> -quality 95 "optimized/$file"
done

# Convert to JPEG for photos (if applicable)
convert avatar-photo.png -quality 85 avatar-photo.jpg

# Strip metadata
exiftool -all= *.png
```

## Video Optimization

### Demo Video Specifications

**CodeCanyon Requirements:**
- **Format:** MP4 (H.264 codec)
- **Resolution:** 1920x1080 (Full HD)
- **Frame Rate:** 30 fps
- **Bitrate:** 5-10 Mbps (variable bitrate)
- **File Size:** Maximum 500 MB
- **Duration:** 2-5 minutes recommended

**Audio:**
- **Codec:** AAC
- **Bitrate:** 128-192 kbps
- **Sample Rate:** 44.1 kHz or 48 kHz
- **Channels:** Stereo

### Video Encoding

**Using FFmpeg (Recommended)**

```bash
# Install FFmpeg
# Mac: brew install ffmpeg
# Linux: apt-get install ffmpeg
# Windows: https://ffmpeg.org/download.html

# Encode video for web
ffmpeg -i raw-demo.mov \
  -c:v libx264 \
  -preset slow \
  -crf 23 \
  -c:a aac \
  -b:a 192k \
  -movflags +faststart \
  demo-video.mp4

# Parameters explained:
# -preset slow: Better compression (slower encoding)
# -crf 23: Quality factor (18-28 range, 23 is good balance)
# -movflags +faststart: Web-optimized (moov atom at start)
```

**Using HandBrake (GUI)**
- Website: https://handbrake.fr/
- Preset: "Web" → "Discord 720p" or "Discord 1080p"
- Video codec: H.264
- Quality: Constant Quality 22-24
- Audio: AAC, 192 kbps

### Video Production Tips

1. **Recording:**
   - Use OBS Studio, ScreenFlow, or Camtasia
   - Record at 1920x1080 or higher
   - Use 30 or 60 fps
   - Keep video segments short

2. **Editing:**
   - Trim unnecessary footage
   - Add smooth transitions
   - Include text overlays for key points
   - Add background music (royalty-free)

3. **Background Music:**
   - Use YouTube Audio Library (free)
   - Use Epidemic Sound (subscription)
   - Use CC0 music from Free Music Archive
   - Keep volume low (background only)

## WordPress.org Assets

### Featured Image
- **Size:** 1280x720 pixels (16:9 aspect ratio)
- **Format:** PNG or JPEG
- **File Size:** < 500 KB
- **Content:** Professional banner with plugin name and key feature

### Icon
- **Sizes Required:**
  - 128x128 pixels (icon-128x128.png)
  - 256x256 pixels (icon-256x256.png)
- **Format:** PNG with transparency
- **File Size:** < 50 KB each
- **Design:** Simple, recognizable, works at small sizes

### Screenshots
- **Minimum:** 4 screenshots
- **Maximum:** 8-10 screenshots
- **Resolution:** 1920x1080 or 1280x720
- **Format:** PNG or JPEG
- **File Size:** < 500 KB each
- **Naming:** screenshot-1.png, screenshot-2.png, etc.

## CodeCanyon Assets

### Main Preview Image
- **Size:** 590x300 pixels (approximately 2:1 aspect ratio)
- **Format:** PNG or JPEG
- **File Size:** < 300 KB
- **Content:** Plugin name, key features, visual appeal

### Gallery Images
- **Minimum:** 3 images
- **Recommended:** 6-8 images
- **Size:** 1920x1080 or larger
- **Format:** PNG for UI, JPEG for photos
- **File Size:** < 500 KB each

### Demo Video (Optional but Recommended)
- See "Video Optimization" section above
- Upload to YouTube or Vimeo
- Provide URL to CodeCanyon

## Asset Checklist

Before submission, verify all assets:

- [ ] All screenshots captured and optimized
- [ ] File sizes under limits (< 500 KB for images)
- [ ] Resolution meets minimum requirements
- [ ] Format is correct (PNG for UI, JPEG for photos)
- [ ] No personal or sensitive information visible
- [ ] Consistent styling and branding
- [ ] Text is readable and clear
- [ ] Colors display correctly across devices
- [ ] Metadata stripped from files
- [ ] Files named according to convention
- [ ] Demo video encoded for web (if applicable)
- [ ] All assets documented in licensing.md
- [ ] Attribution provided for third-party content

## Tools Summary

### Screenshot Tools
- **Capture:** OS native tools, Cleanshot X, ShareX
- **Edit:** GIMP, Photoshop, Canva, Figma
- **Optimize:** TinyPNG, ImageOptim, Squoosh, OptiPNG

### Video Tools
- **Record:** OBS Studio, ScreenFlow, Camtasia
- **Edit:** DaVinci Resolve, Adobe Premiere, iMovie
- **Encode:** FFmpeg, HandBrake
- **Music:** YouTube Audio Library, Epidemic Sound, Free Music Archive

### Verification Tools
- **File Size:** OS file explorer, `ls -lh` (Unix)
- **Dimensions:** ImageMagick `identify`, OS preview
- **Quality:** View at 100% zoom in multiple browsers

## Automation Scripts

### Batch Screenshot Optimization

```bash
#!/bin/bash
# optimize-screenshots.sh

mkdir -p optimized

for file in *.png; do
  echo "Optimizing $file..."
  
  # Resize if larger than 1920x1080
  convert "$file" -resize 1920x1080\> temp.png
  
  # Optimize with optipng
  optipng -o7 temp.png
  
  # Move to optimized directory
  mv temp.png "optimized/$file"
  
  # Show file size reduction
  original_size=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file")
  new_size=$(stat -f%z "optimized/$file" 2>/dev/null || stat -c%s "optimized/$file")
  reduction=$((100 - (new_size * 100 / original_size)))
  
  echo "  Reduced by $reduction% ($original_size → $new_size bytes)"
done

echo "Done! Optimized files in 'optimized/' directory"
```

Usage:
```bash
chmod +x optimize-screenshots.sh
./optimize-screenshots.sh
```

## References

- **WordPress.org Guidelines:** https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/
- **CodeCanyon Requirements:** https://help.author.envato.com/hc/en-us/articles/204677370-WordPress-Plugins-Requirements
- **Image Optimization:** https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/image-optimization
- **Video Encoding:** https://trac.ffmpeg.org/wiki/Encode/H.264

---

**Last Updated:** October 18, 2025  
**Version:** 1.0  
**Maintainer:** Avatar Steward Development Team
