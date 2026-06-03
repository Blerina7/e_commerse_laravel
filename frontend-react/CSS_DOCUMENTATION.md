# ShoeStore CSS - Documentation

## Overview
The ShoeStore CSS has been completely redesigned from the Inspinia admin theme to a modern, clean e-commerce platform specifically tailored for a shoe retail business. This document outlines the structure, colors, and components.

## Color Scheme
The new color palette is designed to be professional, modern, and perfect for an e-commerce shoe store:

- **Primary Color**: `#2c3e50` (Deep Navy Blue) - Main brand color
- **Accent Color**: `#e74c3c` (Brick Red) - Call-to-action and highlights
- **Success Color**: `#27ae60` (Green) - Positive actions, confirmations
- **Info Color**: `#3498db` (Sky Blue) - Information messages
- **Warning Color**: `#f39c12` (Orange) - Warnings and alerts
- **Danger Color**: `#e74c3c` (Red) - Errors and deletions
- **Light Gray**: `#ecf0f1` - Background and borders
- **Dark Gray**: `#34495e` - Secondary text
- **White**: `#ffffff` - Clean backgrounds

## CSS Structure

### 1. Root Variables
All colors are defined as CSS variables for easy maintenance:
```css
:root {
  --primary-color: #2c3e50;
  --accent-color: #e74c3c;
  /* ... more colors */
}
```

### 2. Global Styles
- Typography defaults
- Base element styling
- Link styling with hover effects

### 3. Components
- **Buttons**: Multiple variants (primary, success, danger, warning, info, default, outline, link)
- **Forms**: Styled inputs, textareas, selects with focus states
- **Navigation**: Responsive navbar with hover effects
- **Alerts**: Success, danger, warning, and info messages
- **Panels/Cards**: Clean white cards with headers and footers
- **Tables**: Striped and bordered table options
- **Badges & Labels**: Small indicators with color variants
- **Modals**: Dialog boxes with proper styling

### 4. Shoe Store Specific
New components designed specifically for shoe retail:
- **Shoe Hero Section**: Large banner for main promotions
- **Product Cards**: Individual shoe product display with hover effects
- **Product Image Container**: Responsive image containers
- **Price Display**: Accent-colored pricing
- **Shoe Filter**: Sidebar filter options for browsing

### 5. Utilities
Helpful utility classes for common styling needs:
- Text alignment: `.text-center`, `.text-left`, `.text-right`
- Text colors: `.text-primary`, `.text-success`, `.text-danger`, etc.
- Background colors: `.bg-primary`, `.bg-success`, etc.
- Spacing: `.m-*`, `.mt-*`, `.mb-*`, `.p-*`, etc.
- Display: `.float-left`, `.float-right`, `.display-none`, etc.
- Borders: `.border`, `.border-top`, `.rounded`, etc.

### 6. Responsive Design
The CSS is fully responsive with breakpoints at:
- **768px** (tablet)
- **576px** (mobile)

## Custom Shoe Store Classes

### `.shoe-hero`
Large hero section for promotions or main messaging
```html
<div class="shoe-hero">
  <h1>Welcome to Our Shoe Store</h1>
  <p>Find your perfect pair today</p>
</div>
```

### `.product-card`
Display individual shoe products with image, name, and price
```html
<div class="product-card">
  <div class="product-image">
    <img src="shoe.jpg" alt="Shoe Name">
  </div>
  <h3 class="product-name">Shoe Name</h3>
  <p class="product-price">$99.99</p>
  <div class="product-rating">⭐⭐⭐⭐⭐</div>
</div>
```

### `.shoe-filter`
Sidebar for filtering products
```html
<div class="shoe-filter">
  <h4>Filter Products</h4>
  <div class="filter-option">
    <input type="checkbox" id="sport">
    <label for="sport">Sport Shoes</label>
  </div>
</div>
```

## Key Improvements

### Before (Inspinia)
- Admin dashboard theme
- Complex and bloated CSS
- Generic naming conventions
- Multiple references to "Inspinia"
- Excessive glyphicon dependencies

### After (ShoeStore)
- Clean, minimal CSS (~500 lines vs 8000+ lines)
- Organized and maintainable
- Modern, professional appearance
- Zero Inspinia references
- E-commerce focused
- CSS variables for easy theming
- Mobile-first responsive design

## Usage Tips

### Changing Colors
To change the primary brand color, simply update the CSS variable:
```css
:root {
  --primary-color: #your-new-color;
}
```

### Adding New Components
1. Add CSS variables for colors if needed
2. Create the component class following BEM naming (Block__Element--Modifier)
3. Include responsive breakpoints
4. Document in this file

### Responsive Classes
Use Bootstrap-compatible media queries:
```css
@media (max-width: 768px) {
  /* Tablet styles */
}

@media (max-width: 576px) {
  /* Mobile styles */
}
```

## File Locations
- Main CSS: `/css/style.css`
- Bootstrap: `/css/bootstrap.min.css`
- Animations: `/css/animate.css`
- DataTables: `/css/dataTables.css`
- Font Awesome: `/font-awesome/css/font-awesome.css`

## Browser Support
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Android)

## Migration Notes
The old Inspinia CSS file has been backed up as `style.css.backup` for reference if needed.

## Future Enhancements
- Dark mode theme variant
- Advanced animations
- Print-friendly styles
- Accessibility improvements (WCAG 2.1 AA)
- Animation performance optimizations

---

**Last Updated**: June 3, 2026
**Version**: 1.0
**Status**: Production Ready
