# Portofolio Muhammad Rafi Rizqi — WordPress & Elementor

Website portofolio pribadi **Muhammad Rafi Rizqi** (S1 Teknik Informatika — Universitas Pamulang) yang dibangun menggunakan **WordPress**, **Hello Elementor Child Theme**, **Elementor Flexbox Containers**, dan **Custom Plugin (`rafi-portfolio-core`)** mengikuti standar workflow digital agency.

> **Catatan Repository:** Sesuai praktik terbaik WordPress di digital agency, repositori ini **hanya melacak custom code** (*Child Theme* dan *Custom Plugin*). WordPress Core, `wp-config.php`, `wp-content/uploads`, dan plugin pihak ketiga dikecualikan melalui `.gitignore`.

---

## Arsitektur Teknis (Separation of Concerns)

```text
wp-content/
├── themes/
│   └── hello-elementor-child/        # Presentation Layer (Child Theme)
│       ├── style.css                 # Design system, seamless section gradients, responsive & GPU animations
│       ├── functions.php             # Enqueue parent/child stylesheet, fonts & lightweight JS
│       ├── single-proyek.php         # Template studi kasus proyek otomatis
│       └── assets/js/animations.js   # rAF scroll progress & one-shot IntersectionObserver reveal
└── plugins/
    └── rafi-portfolio-core/          # Data & Functionality Layer (Custom Plugin)
        ├── rafi-portfolio-core.php   # Plugin bootstrap & rewrite flush hooks
        ├── includes/
        │   ├── cpt-proyek.php        # CPT Proyek, Taxonomy Kategori, & Meta Box Studi Kasus
        │   ├── cpt-sertifikat.php    # CPT Sertifikat & Meta Box Kredensial
        │   └── shortcodes.php        # Shortcode [rafi_projects] & [rafi_certificates] untuk Elementor
        └── assets/
            ├── css/portfolio-core.css
            └── js/portfolio-filter.js
```

### Kenapa Child Theme Dipisah dari Custom Plugin?
- **Child Theme (`hello-elementor-child`)** hanya menangani tampilan (*presentation*) agar modifikasi tetap aman saat parent theme `hello-elementor` mendapat update resmi.
- **Custom Plugin (`rafi-portfolio-core`)** mengelola Custom Post Type (*Proyek* & *Sertifikat*), Meta Boxes, dan Shortcode agar data bisnis tetap aman di database walaupun tema diganti.

---

## Fitur & Praktik Standar Industri
- **Elementor First**: Halaman *Beranda*, *CV*, *Sertifikat*, *Proyek*, *Kontak*, serta *Header & Footer* dibangun menggunakan Flexbox Containers & Global Site Settings.
- **Keamanan PHP WordPress**: Setiap meta box menerapkan verifikasi `nonce`, capability check (`current_user_can`), sanitization saat simpan (`sanitize_text_field`, `sanitize_textarea_field`, `esc_url_raw`), dan output escaping (`esc_html`, `esc_url`, `esc_attr`).
- **Performa & Responsivitas**: Plugin minimal (`6` plugin aktif), animasi murni GPU (`translate3d` & `opacity`, tanpa library eksternal berat), dan responsif di Desktop (`1280px`), Tablet (`768px`), dan Mobile (`390px`).
- **SEO & Email**: Terintegrasi dengan **Rank Math SEO** (meta title, description, Open Graph, XML Sitemap) dan **WP Mail SMTP** untuk formulir kontak **Contact Form 7**.

---

## Stack & Environment
- **Core**: WordPress `7.1+`, Elementor, PHP `8.3`, MySQL `8.4`, HTML5, CSS3, Vanilla JS
- **Local Dev**: Laragon (Apache + MySQL), Git & GitHub

---

## Kontak
- **Nama**: Muhammad Rafi Rizqi (Depok, Jawa Barat)
- **Email**: `rafi02062003@gmail.com`
- **Instagram**: [@rafirizqi_](https://instagram.com/rafirizqi_)
- **GitHub**: [github.com/RafiRizqi](https://github.com/RafiRizqi)
