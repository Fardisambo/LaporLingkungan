# Laporin Lingkungan

Platform pelaporan lingkungan modern yang memudahkan masyarakat melaporkan masalah lingkungan dengan sistem autentikasi yang aman dan antarmuka yang user-friendly.


## Screenshot

### 1. Dashboard
![image alt](https://github.com/Fardisambo/LaporLingkungan/blob/9382458110e59130312e3a3922f13f0d53e57fbc/screenshot/Screenshot%202025-09-18%20192640.png)

### 2. Halaman Laporan
![image alt](https://github.com/Fardisambo/LaporLingkungan/blob/fbd7d23580946335d62a6ca9f3f23d93c93123f2/screenshot/Screenshot%202025-09-18%20193219.png)

### 3. Db Diagram
![image alt](https://github.com/Fardisambo/LaporLingkungan/blob/c1a23b4ddd71645fe9eedbd31a1c5f01f93726f2/screenshot/Screenshot%202025-09-18%20200807.png)

## 🌟 Fitur Utama

### 1. Sistem Autentikasi Modern
- **Login/Register Tradisional**: Sistem autentikasi dengan username dan password
- **Google OAuth Integration**: Login menggunakan akun Google untuk kemudahan akses
- **Role-Based Access Control**: Tiga level pengguna (Admin, RT, User)
- **Session Management**: Sistem session yang aman dengan validasi role

### 2. Fitur User Management
- **Profil User**: Manajemen profil dengan informasi NIK dan alamat
- **Password Management**: Fitur ubah password dengan validasi keamanan
- **Google Account Sync**: Sinkronisasi data dari akun Google

### 3. Sistem Pelaporan Lingkungan
- **Buat Laporan**: Form pelaporan dengan kategori dan lokasi
- **Upload Foto Bukti**: Dukungan format JPG, JPEG, PNG, GIF
- **Tracking Status**: Monitoring status laporan (Pending → Proses → Completed/Rejected)
- **Edit Laporan**: Kemampuan mengedit laporan yang sudah dibuat
- **Riwayat Laporan**: Melihat semua laporan yang telah dibuat

### 4. Panel Admin & RT
- **Data Management**: Kelola data warga, KK, dan user
- **Laporan Management**: Review, approve, dan update status laporan
- **User Management**: Tambah, edit, dan hapus user
- **Dashboard Analytics**: Overview sistem secara real-time

### 5. Fitur NIK dan Alamat di Profil User

#### NIK (Nomor Induk Kependudukan)
- **Sumber Data**: Diambil dari tabel `warga` berdasarkan username
- **Akses**: User hanya dapat melihat NIK, tidak dapat mengubah
- **Tampilan**: Field read-only dengan styling khusus
- **Fallback**: Menampilkan "NIK tidak ditemukan" jika data tidak ada

#### Alamat
- **Sumber Data**: Diambil dari tabel `warga` berdasarkan username
- **Akses**: User dapat melihat dan mengubah alamat
- **Validasi**: Alamat wajib diisi (required field)
- **Update**: Perubahan alamat langsung disimpan ke tabel `warga`

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: Bootstrap 5.3.7, HTML5, CSS3, JavaScript
- **Authentication**: Google OAuth 2.0, PHP Sessions
- **File Upload**: PHP File Upload dengan validasi
- **UI Framework**: Bootstrap dengan custom styling
- **Icons**: Font Awesome 6.0
- **Dependencies**: Composer dengan Google API Client

## 🗄️ Struktur Database

### Tabel `users`
```sql
CREATE TABLE users (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(50) NOT NULL,
  email varchar(100) DEFAULT NULL,
  password varchar(255) NOT NULL,
  role enum('admin','rt','user') NOT NULL DEFAULT 'user',
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY username (username),
  UNIQUE KEY email (email)
);
```

### Tabel `warga`
```sql
CREATE TABLE warga (
  id int(11) NOT NULL AUTO_INCREMENT,
  nik varchar(16) NOT NULL,
  nama varchar(100) NOT NULL,
  tempat_lahir varchar(100) NOT NULL,
  tanggal_lahir date NOT NULL,
  jenis_kelamin enum('L','P') NOT NULL,
  alamat text NOT NULL,
  rt varchar(3) NOT NULL,
  rw varchar(3) NOT NULL,
  desa varchar(100) NOT NULL,
  kecamatan varchar(100) NOT NULL,
  agama varchar(20) NOT NULL,
  status_perkawinan varchar(20) NOT NULL,
  pekerjaan varchar(100) NOT NULL,
  kewarganegaraan varchar(3) NOT NULL DEFAULT 'WNI',
  kk_id int(11) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY nik (nik),
  KEY kk_id (kk_id),
  CONSTRAINT warga_ibfk_1 FOREIGN KEY (kk_id) REFERENCES keluarga (id) ON DELETE SET NULL
);
```

### Tabel `laporan`
```sql
CREATE TABLE laporan (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  judul varchar(200) NOT NULL,
  deskripsi text NOT NULL,
  lokasi varchar(200) NOT NULL,
  kategori varchar(50) NOT NULL,
  status enum('pending','proses','completed','rejected') NOT NULL DEFAULT 'pending',
  foto varchar(255) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY user_id (user_id),
  CONSTRAINT laporan_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);
```

## 🚀 Cara Penggunaan

### 1. Setup Project
```bash
# Clone repository
git clone [repository-url]
cd laporr

# Install dependencies
composer install

# Setup database
mysql -u username -p database_name < database_structure.sql

# Configure Google OAuth (opsional)
# Edit google-login.php dan google-callback.php dengan credentials Anda
```

### 2. Konfigurasi
- **Database**: Edit `db.php` dengan kredensial database Anda
- **Google OAuth**: Dapatkan Client ID dan Secret dari Google Cloud Console
- **Upload Directory**: Pastikan folder `uploads/` memiliki permission write

### 3. Akses Sistem
1. **Login** ke sistem dengan username/password atau Google OAuth
2. **Buat Laporan** untuk melaporkan masalah lingkungan
3. **Upload Foto** sebagai bukti laporan
4. **Track Status** laporan Anda
5. **Edit Profil** untuk update informasi pribadi

## 🔐 Keamanan

- **SQL Injection Protection**: Menggunakan prepared statements
- **XSS Protection**: htmlspecialchars() untuk output
- **Password Hashing**: Menggunakan password_hash() dengan bcrypt
- **Session Management**: Validasi session untuk setiap halaman
- **File Upload Security**: Validasi tipe dan ukuran file
- **CSRF Protection**: Token-based form submission
- **Role-Based Access**: Pembatasan akses berdasarkan role user

## 📱 Fitur UI/UX

- **Responsive Design**: Optimized untuk desktop, tablet, dan mobile
- **Modern Interface**: Gradient backgrounds dan card-based layout
- **Interactive Elements**: Hover effects dan smooth transitions
- **User Feedback**: Success/error messages yang informatif
- **Accessibility**: Semantic HTML dan ARIA labels

## 🗂️ Struktur File

```
laporr/
├── adminpannel/          # Panel Admin
├── RtPannel/            # Panel RT
├── user/                # Panel User
├── assets/              # Asset statis
├── bootstrap-5.3.7-dist/ # Bootstrap framework
├── css/                 # Custom stylesheets
├── js/                  # JavaScript files
├── uploads/             # File uploads
├── vendor/              # Composer dependencies
├── google/              # Google API integration
├── index.php            # Login page
├── daftar.php          # Registration page
└── database_structure.sql # Database schema
```

## 🔧 Dependencies

```json
{
    "require": {
        "google/apiclient": "^2.0"
    }
}
```

## 📋 Role dan Permission

### 👤 User (Masyarakat)
- Buat laporan lingkungan
- Upload foto bukti
- Edit laporan sendiri
- Lihat status laporan
- Update profil pribadi

### 🏘️ RT (Ketua RT)
- Kelola laporan warga
- Update status laporan
- Kelola data warga
- Kelola data KK
- Tambah komentar

### 👨‍💼 Admin
- Akses penuh ke semua fitur
- Kelola semua user
- Kelola semua data
- Monitoring sistem
- Backup dan maintenance

## 🚨 Troubleshooting

### Common Issues
1. **Upload Error**: Periksa permission folder `uploads/`
2. **Database Connection**: Pastikan kredensial di `db.php` benar
3. **Google OAuth**: Verifikasi Client ID dan Secret
4. **Session Issues**: Periksa PHP session configuration

### Log Files
- Error logs tersimpan di server web
- Database queries dapat di-debug dengan error reporting

## 📞 Support

Untuk bantuan teknis atau pertanyaan, silakan hubungi:
- **Email**: support@laporin.com
- **Documentation**: Lihat file flowchart dan use case diagrams
- **Issues**: Gunakan issue tracker repository

## 📄 License

Project ini dikembangkan untuk kepentingan pelaporan lingkungan masyarakat. Gunakan dengan bijak dan sesuai peraturan yang berlaku.

## 🔄 Changelog

### v2.0.0 (Current)
- ✅ Google OAuth integration
- ✅ Modern UI dengan Bootstrap 5.3.7
- ✅ Enhanced security features
- ✅ Improved file upload system
- ✅ Better error handling
- ✅ Responsive design optimization

### v1.0.0
- ✅ Basic CRUD operations
- ✅ Role-based access control
- ✅ File upload functionality
- ✅ Database management

---

**Laporin Lingkungan** - Membuat pelaporan lingkungan lebih mudah dan efisien! 🌱
