# Walkthrough: Kustomisasi Sidebar Header dengan Nama dan Logo Gereja

Kami telah berhasil memperbarui sidebar header pada panel admin Filament untuk mengganti kata bawaan **"Laravel"** dengan nama gereja yang dinamis dan menyertakan logo gereja sesuai profil yang terdaftar di database.

---

## Perubahan yang Dilakukan

### 1. File Konfigurasi Panel: [`AdminPanelProvider.php`](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Providers/Filament/AdminPanelProvider.php)
Kami menambahkan pengaturan `brandName()`, `brandLogo()`, dan `favicon()` pada panel Filament untuk mengambil data identitas gereja secara dinamis dari model `ChurchProfile`:

- **`brandName`**: Digunakan untuk judul halaman/tab browser. Menampilkan nama gereja (contoh: *Jemaat Kolhua Kaesarea*) dan default ke nama cadangan jika data kosong.
- **`brandLogo`**: Menghasilkan HTML kustom (`HtmlString`) yang menggabungkan gambar logo gereja dan nama gereja berdampingan dengan layout flexbox yang elegan dan premium.
- **`favicon`**: Mengatur ikon tab browser menggunakan logo gereja yang diunggah.

```php
            ->brandName(fn () => \App\Models\ChurchProfile::first()?->church_name ?? 'Jemaat Kolhua Kaesarea')
            ->brandLogo(function () {
                $profile = \App\Models\ChurchProfile::first();
                $logoUrl = $profile && $profile->logo_path 
                    ? asset('storage/' . $profile->logo_path) 
                    : null;
                $name = $profile?->church_name ?? 'Jemaat Kolhua Kaesarea';
                
                if ($logoUrl) {
                    return new \Illuminate\Support\HtmlString("
                        <div class='flex items-center gap-3 py-1'>
                            <img src='{$logoUrl}' alt='Logo' class='h-9 w-9 object-contain rounded-full shadow-sm' />
                            <span class='font-bold text-lg tracking-tight text-slate-900 dark:text-white'>{$name}</span>
                        </div>
                    ");
                }
                
                return $name;
            })
            ->favicon(function () {
                $profile = \App\Models\ChurchProfile::first();
                return $profile && $profile->logo_path 
                    ? asset('storage/' . $profile->logo_path) 
                    : asset('favicon.ico');
            })
```

---

## Verifikasi Visual

Berikut adalah rekaman alur masuk (login) ke panel admin dan verifikasi tampilan baru pada sidebar header yang kini menampilkan **Logo Gereja** serta nama **Jemaat Kolhua Kaesarea**:

![Verifikasi Tampilan Baru Header Sidebar](/C:/Users/ASUS/.gemini/antigravity-ide/brain/c1949d33-8076-4bfb-ae22-b0bc6174cdf9/login_and_verify_brand_1780978917523.webp)
