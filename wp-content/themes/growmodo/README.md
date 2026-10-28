# Growmodo Theme

A custom WordPress theme for a real-estate website, built from the Estatein Figma design (Home, About Us, Properties, Property Details, Services and Contact Us, with desktop, tablet and mobile layouts).

## How I built it

1. Went through every frame of the Figma PDF and noted the colours, fonts, spacing and copy, and pulled out the photos (saved as WebP to keep the files small).
2. Set up the colours and spacing as CSS variables, then built the shared pieces first: header, footer, call-to-action band, section headings, property card, sliders and forms.
3. Built each page as its own template, so the pages stay easy to change.
4. Added sample content so the site looks complete straight after activation.

## Content management

Everything the client is likely to edit is available in the WordPress dashboard:

- **Properties** – price, bedrooms, bathrooms, area, build year, gallery, features and pricing details, plus Location and Property Type categories.
- **Testimonials, FAQs, Team, Clients, Offices** – each has its own menu item.
- **Services** and **About Items** – grouped, so each group shows as its own section on the page.
- **Growmodo → Site Options** – announcement banner, home hero text, numbers, call-to-action text, contact details, social links and the email address that receives form messages.
- **Pages** – on About, Properties, Services and Contact, the page title is the main heading and the page text is the intro paragraph.
- **Inquiries** – every form submission is saved here and also sent by email.

## Choices I made

- **No ACF.** I wrote simple custom fields into the theme (`inc/meta-boxes.php`), so there is no plugin to install or keep updated.
- **No front-end libraries.** The sliders, gallery and menu use plain JavaScript, and the sliders use CSS scroll-snap.
- **Forms** check input on the page and again on the server, include a hidden spam trap and a limit on repeat submissions, and still work without JavaScript.
- **Property search** uses normal URL parameters, so a filtered result can be shared as a link.
- **Performance:** minified CSS and JS, WebP images with set sizes, lazy loading, and unused WordPress styles removed.
- **Accessibility and SEO:** skip link, keyboard focus styles, labelled buttons and form fields, meta description, social sharing tags and basic structured data.
- **Responsive:** layouts change at 1280px, 1024px and 767px, and the menu becomes a drop-down on smaller screens.

## Plugins

None required.

## Editing the styles or scripts

The readable files are `assets/css/main.css` and `assets/js/main.js`. The site loads the `.min` versions, so after editing, regenerate them:

```
npx esbuild assets/css/main.css --minify --outfile=assets/css/main.min.css
npx esbuild assets/js/main.js --minify --outfile=assets/js/main.min.js
```

