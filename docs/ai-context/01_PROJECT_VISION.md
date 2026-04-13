# 🎯 Project Vision & Core Philosophy

## 📌 Core Principles
1. **Albums-First UX**: Homepage (`/`) = browsable album grid. Zero marketing fluff.
2. **Community-Driven in General**: Fans admire their idols viewing photographs.
3. **Community-Driven for Creators**: Photographers submit work. Bands/theaters request photographers.
4. **Photographer-Centric**: Every photographer gets a dedicated portfolio page.
5. **Performance-First**: Lazy loading, CSS-only animations, minimal JS, CDN-ready images.
6. **No Promo Homepage**: All "about us", policies, FAQs live in footer/static pages.

## 🎯 Target Audience
- Fans of all sorts of live performances from classic theater through experimental stage to rock/metal concerts and festivals
- Concert & theater photographers (contributors)
- Rock/metal bands, festival organizers, theater managers (requestors)
- Photography enthusiasts (consumers)

## 🚫 Anti-Patterns
- ❌ Hero banners with "We are the best"
- ❌ Max-width containers (`container mx-auto`) on main content
- ❌ Hardcoded light/dark mode without OS detection
- ❌ JavaScript-heavy filtering (use Livewire + Alpine only)
- ❌ Ignoring mobile-first or zoom-level behavior
- ❌ Using spatie packages in the project

## ✅ Success Metrics
- Instant visual engagement on load
- Seamless infinite scroll without layout shift
- Photographer booking requests > 30% conversion
- Sub-2s LCP on 3G
