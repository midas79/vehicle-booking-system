# Vehicle Booking System

Sistem pemesanan kendaraan untuk perusahaan tambang nikel yang memiliki kantor pusat, kantor cabang, dan beberapa lokasi tambang.

##  Fitur

### Autentikasi & Autorisasi

- Multi-role user management (Admin, Approver Level 1, Approver Level 2)
- Secure login system
- Role-based access control

### Dashboard Analytics

- Total kendaraan tersedia
- Booking aktif real-time
- Pending approvals counter
- Grafik penggunaan kendaraan
- Distribusi tipe kendaraan
- Trend booking bulanan

### Manajemen Kendaraan

- **Vehicle Monitoring**
- Tracking kilometer kendaraan
- Monitoring konsumsi BBM
- Status kendaraan (Available/In Use/Maintenance)
  
- **Service Management**
- Penjadwalan service berkala
- Reminder otomatis berdasarkan KM atau tanggal
- Riwayat service lengkap

### Sistem Booking

- **Multi-level Approval**
- 2 tingkat persetujuan
- Tracking status approval
- Notifikasi otomatis
  
- **Fitur Booking**
- Pemilihan kendaraan dan driver
- Penentuan tujuan dan keperluan
- Jadwal pemesanan fleksibel

### Reporting & Export

- Export laporan ke Excel
- Filter berdasarkan periode
- Laporan meliputi:
- Penggunaan kendaraan
- Konsumsi BBM
- Riwayat service
- Status booking

### Activity Logging

- Pencatatan semua aktivitas
- Audit trail lengkap
- Tracking perubahan data

## Live Demo

Aplikasi telah di-deploy dan dapat diakses di:
**https://vehicle-booking-system.laravel.cloud/**

## Persyaratan Sistem

-   **PHP Version**: >= 8.2
-   **Laravel Version**: 12.0
-   **Database**: MySQL 5.7+ / MariaDB 10.3+
-   **Node.js**: >= 16.x
-   **Composer**: >= 2.0

## Daftar User Default

Berikut adalah daftar user yang tersedia untuk testing di aplikasi live:

### Admin Users

| Email                 | Password    | Role  | Region                 | Deskripsi             |
| --------------------- | ----------- | ----- | ---------------------- | --------------------- |
| admin@example.com     | password123 | Admin | Kantor Pusat Jakarta   | Admin utama sistem    |
| admin.sby@example.com | password123 | Admin | Kantor Cabang Surabaya | Admin cabang Surabaya |

### Approver Level 1

| Email                  | Password    | Role     | Level | Region                 | Deskripsi           |
| ---------------------- | ----------- | -------- | ----- | ---------------------- | ------------------- |
| approver1@example.com  | password123 | Approver | 1     | Kantor Pusat Jakarta   | Manager Operasional |
| supervisor@example.com | password123 | Approver | 1     | Tambang Sulawesi Utara | Supervisor Lapangan |

### Approver Level 2

| Email                 | Password    | Role     | Level | Region               | Deskripsi            |
| --------------------- | ----------- | -------- | ----- | -------------------- | -------------------- |
| approver2@example.com | password123 | Approver | 2     | Kantor Pusat Jakarta | General Manager      |
| director@example.com  | password123 | Approver | 2     | Kantor Pusat Jakarta | Direktur Operasional |

## Cara Mengakses Aplikasi Live

1. **Buka Browser**
   Akses URL: https://vehicle-booking-system.laravel.cloud/

2. **Login**
   Gunakan salah satu kredensial di atas sesuai dengan role yang ingin dicoba

3. **Fitur**
    - **Admin**: Memiliki akses penuh ke semua fitur
    - **Approver**: Fokus pada approval pemesanan sesuai level

## Instalasi Lokal (Development)

Jika Anda ingin menjalankan aplikasi secara lokal untuk development:

1. **Clone Repository**

    ```bash
    git clone [repository-url]
    cd vehicle-booking-system

    ```

2. **Install Dependencies**

    ```bash
    composer install
    npm install && npm run build
    ```

3. **Setup Environment**

    ```bash
    cp .env.example .env
    php artisan key:generate

    ```

4. **Konfigurasi Database**
   Edit file .env dan sesuaikan konfigurasi database:

    ```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=vehicle_booking
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5. **Migrasi Database & Seeder**

    ```bash
    php artisan migrate --seed
    ```

6. **Build Assets**

    ```bash
    npm run build
    ```

7. **Jalankan Aplikasi**

    ```bash
    php artisan serve
    npm run dev
    ```

## Panduan Penggunaan

### 1. Login

- Akses aplikasi melalui browser
- Masukkan email dan password
- Klik tombol "Login"

### 2. Dashboard

Setelah login, dashboard menampilkan:

- Statistik kendaraan
- Grafik interaktif
- Status booking terkini

### 3. Alur Pemesanan Kendaraan

#### Pembuatan Booking (Admin)

1. Navigasi ke menu "Bookings"
2. Klik "Create New Booking"
3. Isi form:

- Pilih kendaraan
- Pilih driver
- Input tujuan dan keperluan
- Set tanggal mulai dan selesai

4. Submit booking

#### Approval Process

**Level 1:**

- Approver menerima notifikasi
- Review detail pemesanan
- Approve/Reject dengan catatan

**Level 2:**

- Setelah approval level 1
- Review final
- Approve/Reject untuk finalisasi

#### Penggunaan Kendaraan

- Status berubah menjadi "Approved"
- Driver siap menggunakan kendaraan
- Admin input data aktual setelah selesai

### 4. Manajemen Kendaraan (Admin)

#### Vehicle Usage

- Input KM awal dan akhir
- Catat konsumsi BBM
- Update status kendaraan

#### Service Management

- Schedule service berkala
- Track service history
- Monitor service status

## 🗄️ Struktur Database

### Tabel Utama

| Tabel | Deskripsi |
| --- | --- |
| `users` | Data pengguna sistem |
| `regions` | Data kantor dan lokasi |
| `vehicles` | Data kendaraan |
| `drivers` | Data driver |
| `bookings` | Data pemesanan |
| `approvals` | Data persetujuan |
| `vehicle_usages` | Data penggunaan |
| `activity_logs` | Log aktivitas |

### Activity Diagram

```javascript
[Start] → [User Request Vehicle]
           ↓
    [Admin Create Booking]
           ↓
    [Select Vehicle & Driver]
           ↓
    [Select Approvers (Level 1 & 2)]
           ↓
    [Submit Booking]
           ↓
    [System Send Notification to Level 1 Approver]
           ↓
    <Level 1 Decision>
    ↙ Reject    ↘ Approve
[Booking Rejected]  [Update Status]
    ↓                    ↓
[End]          [Send Notification to Level 2 Approver]
                        ↓
                 <Level 2 Decision>
                ↙ Reject    ↘ Approve
         [Booking Rejected]  [Booking Approved]
                ↓                    ↓
              [End]          [Vehicle Ready to Use]
                                    ↓
                                  [End]
```