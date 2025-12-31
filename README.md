# Webui Key System

Gelişmiş Key Doğrulama ve Yönlendirme Sistemi.  
Bu proje, kullanıcılara belirli anahtarlar (key) aracılığıyla özel linklere erişim sağlayan bir web uygulamasıdır. PHP ve MySQL kullanılarak geliştirilmiştir.

## Özellikler

*   **Key Yönetimi:** Admin panelinden sınırsız sayıda key oluşturma, silme ve düzenleme.
*   **Özel Yönlendirme:** Her key için farklı bir hedef URL (target URL) belirleyebilme.
*   **Key Durumu:** Keyleri aktif veya pasif duruma getirebilme.
*   **Admin Paneli:** Güvenli giriş sistemi ile korunan modern yönetim paneli.
*   **Site Ayarları:** Site başlığı, açıklaması ve footer yazısını admin panelinden değiştirebilme.
*   **Discord Entegrasyonu (Opsiyonel):** (Kod dosyalarında Discord entegrasyonu altyapısı mevcuttur).

## Kurulum

### Gereksinimler
*   PHP 7.4 veya üzeri
*   MySQL Veritabanı
*   Apache/Nginx Web Sunucusu (XAMPP, cPanel vb.)

### Adımlar

1.  **Dosyaları Yükleyin:** Tüm dosyaları sunucunuzun `public_html` veya `htdocs` klasörüne yükleyin.
2.  **Veritabanı Oluşturun:**
    *   Hosting panelinizden (phpMyAdmin vb.) yeni bir veritabanı oluşturun (Örn: `key_system_db`).
3.  **Veritabanını İçe Aktarın (SQL):**
    *   Bu repodaki `schema.sql` dosyasını phpMyAdmin üzerinden oluşturduğunuz veritabanına "İçe Aktar" (Import) diyerek yükleyin.
    *   **VEYA** tarayıcıdan `seninsiten.com/setup.php` sayfasını bir kez ziyaret ederek tabloların otomatik oluşmasını sağlayın. **Kurulum bitince setup.php dosyasını silmeniz önerilir.**
4.  **Veritabanı Ayarını Yapın:**
    *   `db.php` dosyasını bir metin editörü ile açın.
    *   Aşağıdaki kısımları kendi veritabanı bilgilerinizle güncelleyin:
    ```php
    $host = 'localhost';
    $dbname = 'veritabani_adiniz'; // Oluşturduğunuz DB adı
    $username = 'veritabani_kullanici_adiniz';
    $password = 'veritabani_sifreniz';
    ```

## Kullanım Bilgileri

### Admin Paneli
*   **Giriş URL:** `/admin/login.php` (veya direkt `/admin/`)
*   **Varsayılan Kullanıcı Adı:** `admin`
*   **Varsayılan Şifre:** `admin123`

> **Önemli:** Giriş yaptıktan sonra güvenliğiniz için şifrenizi değiştirmeyi unutmayın.

## Geliştirici

Bu proje **Webui** tarafından geliştirilmiştir.
Telif hakkları alınmıştır.

---
*Github'da yayınlanmaya uygundur.*
