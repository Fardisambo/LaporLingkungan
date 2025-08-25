# FLOWCHART LENGKAP APLIKASI LAPORIN LINGKUNGAN v2.0

## 1. FLOWCHART UTAMA SISTEM (Updated)

```mermaid
flowchart TD
    A[Start] --> B[Landing Page]
    B --> C{User Action}
    
    C -->|Traditional Login| D[Login Form]
    C -->|Google OAuth| E[Google Login]
    C -->|Register| F[Register Form]
    C -->|Direct Access| G[Check Session]
    
    D --> H{Validate Credentials}
    H -->|Invalid| I[Show Error] --> D
    H -->|Valid| J[Set Session] --> K{Check Role}
    
    E --> L[Google OAuth Flow]
    L --> M{User Exists?}
    M -->|Yes| N[Login Existing User] --> K
    M -->|No| O[Create New User] --> K
    
    F --> P{Validate Registration}
    P -->|Invalid| Q[Show Error] --> F
    P -->|Valid| R[Create User Account] --> S[Show Success] --> D
    
    G --> T{Session Valid?}
    T -->|No| B
    T -->|Yes| K
    
    K -->|Admin| U[Admin Dashboard]
    K -->|RT| V[RT Dashboard]
    K -->|User| W[User Dashboard]
    
    U --> X[Admin Panel]
    V --> Y[RT Panel]
    W --> Z[User Panel]
    
    X --> AA[Logout] --> B
    Y --> AA
    Z --> AA
```

## 2. FLOWCHART GOOGLE OAUTH INTEGRATION (New)

```mermaid
flowchart TD
    A[User Clicks Google Login] --> B[Redirect to Google OAuth]
    B --> C[User Authorizes App]
    C --> D[Google Returns Auth Code]
    D --> E[Exchange Code for Token]
    E --> F[Get User Info from Google]
    F --> G{User Exists in Database?}
    
    G -->|Yes| H[Login Existing User]
    G -->|No| I[Create New User Account]
    
    H --> J[Set Session & Redirect]
    I --> K[Generate Random Password]
    K --> L[Insert User to Database]
    L --> M[Set Session & Redirect]
    
    J --> N[User Dashboard]
    M --> N
    
    style A fill:#4285f4,stroke:#333,stroke-width:2px,color:#fff
    style B fill:#4285f4,stroke:#333,stroke-width:2px,color:#fff
    style C fill:#34a853,stroke:#333,stroke-width:2px,color:#fff
```

## 3. FLOWCHART LOGIN & AUTHENTICATION (Updated)

```mermaid
flowchart TD
    A[User Input Username & Password] --> B[Validate Input]
    B --> C{Input Valid?}
    C -->|No| D[Show Error: Field Required] --> A
    C -->|Yes| E[Query Database]
    E --> F{User Found?}
    F -->|No| G[Show Error: User Not Found] --> A
    F -->|Yes| H[Verify Password]
    H --> I{Password Correct?}
    I -->|No| J[Show Error: Wrong Password] --> A
    I -->|Yes| K[Create Session]
    K --> L{Check User Role}
    L -->|Admin| M[Redirect to Admin Panel]
    L -->|RT| N[Redirect to RT Panel]
    L -->|User| O[Redirect to User Panel]
    
    style K fill:#34a853,stroke:#333,stroke-width:2px,color:#fff
    style M fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
    style N fill:#4ecdc4,stroke:#333,stroke-width:2px,color:#fff
    style O fill:#45b7d1,stroke:#333,stroke-width:2px,color:#fff
```

## 4. FLOWCHART REGISTRATION (Updated)

```mermaid
flowchart TD
    A[User Fill Registration Form] --> B[Validate Form Data]
    B --> C{All Fields Filled?}
    C -->|No| D[Show Error: Required Fields] --> A
    C -->|Yes| E[Check Password Match]
    E --> F{Password Match?}
    F -->|No| G[Show Error: Password Mismatch] --> A
    F -->|Yes| H[Check Username Availability]
    H --> I{Username Available?}
    I -->|No| J[Show Error: Username Taken] --> A
    I -->|Yes| K[Hash Password with bcrypt]
    K --> L[Insert User to Database]
    L --> M{Insert Success?}
    M -->|No| N[Show Error: Database Error] --> A
    M -->|Yes| O[Show Success Message]
    O --> P[Redirect to Login]
    
    style K fill:#ffd93d,stroke:#333,stroke-width:2px,color:#000
    style O fill:#34a853,stroke:#333,stroke-width:2px,color:#fff
```

## 5. FLOWCHART USER PANEL (Updated)

```mermaid
flowchart TD
    A[User Dashboard] --> B{User Action}
    
    B -->|View Reports| C[Laporan Saya]
    B -->|Create Report| D[Buat Laporan]
    B -->|View Profile| E[Profil]
    B -->|Help| F[Bantuan]
    B -->|Logout| G[Logout]
    
    C --> H[Display User Reports]
    H --> I{Report Actions}
    I -->|View Detail| J[Detail Laporan]
    I -->|Edit Report| K[Edit Laporan]
    I -->|Back| C
    
    D --> L[Report Form]
    L --> M[Fill Report Details]
    M --> N{Form Valid?}
    N -->|No| O[Show Validation Error] --> L
    N -->|Yes| P[Upload Photo]
    P --> Q[Save Report]
    Q --> R{Save Success?}
    R -->|No| S[Show Error] --> L
    R -->|Yes| T[Show Success] --> A
    
    E --> U[Display Profile]
    U --> V{Profile Actions}
    V -->|Edit Profile| W[Edit Form]
    V -->|Change Password| X[Password Form]
    V -->|Back| A
    
    F --> Y[Help Page]
    G --> Z[Clear Session] --> AA[Redirect to Login]
    
    style D fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
    style P fill:#4ecdc4,stroke:#333,stroke-width:2px,color:#fff
    style T fill:#34a853,stroke:#333,stroke-width:2px,color:#fff
```

## 6. FLOWCHART ADMIN PANEL (Updated)

```mermaid
flowchart TD
    A[Admin Dashboard] --> B{Admin Action}
    
    B -->|Manage KK| C[Data KK]
    B -->|Manage Residents| D[Data Warga]
    B -->|Manage Reports| E[Data Laporan]
    B -->|Manage Users| F[Kelola User]
    B -->|System Stats| G[Statistik Sistem]
    B -->|Logout| H[Logout]
    
    C --> I[KK Management]
    I --> J{KK Actions}
    J -->|Add KK| K[Tambah KK]
    J -->|Edit KK| L[Edit KK]
    J -->|Delete KK| M[Hapus KK]
    J -->|View KK| N[View KK Details]
    
    D --> O[Resident Management]
    O --> P{Resident Actions}
    P -->|Add Resident| Q[Tambah Warga]
    P -->|Edit Resident| R[Edit Warga]
    P -->|Delete Resident| S[Hapus Warga]
    P -->|View Resident| T[View Resident Details]
    
    E --> U[Report Management]
    U --> V{Report Actions}
    V -->|View Reports| W[View All Reports]
    V -->|Update Status| X[Update Report Status]
    V -->|View Detail| Y[Report Details]
    V -->|Add Comment| Z[Add Comment]
    
    F --> AA[User Management]
    AA --> BB{User Actions}
    BB -->|Add User| CC[Tambah User]
    BB -->|Edit User| DD[Edit User]
    BB -->|Delete User| EE[Hapus User]
    BB -->|View Users| FF[View All Users]
    
    G --> GG[System Analytics]
    H --> HH[Clear Session] --> II[Redirect to Login]
    
    style G fill:#ffd93d,stroke:#333,stroke-width:2px,color:#000
    style Z fill:#4ecdc4,stroke:#333,stroke-width:2px,color:#fff
```

## 7. FLOWCHART RT PANEL (Updated)

```mermaid
flowchart TD
    A[RT Dashboard] --> B{RT Action}
    
    B -->|View Reports| C[Laporan]
    B -->|Manage Residents| D[Data Warga]
    B -->|Manage KK| E[Kartu Keluarga]
    B -->|Manage Users| F[Data User]
    B -->|View Profile| G[Profil]
    B -->|RT Statistics| H[Statistik RT]
    B -->|Logout| I[Logout]
    
    C --> J[Report Management]
    J --> K{Report Actions}
    K -->|View All Reports| L[View Reports List]
    K -->|Update Status| M[Update Report Status]
    K -->|View Detail| N[Report Details]
    K -->|Add Comment| O[Add Comment]
    
    D --> P[Resident Management]
    P --> Q{Resident Actions}
    Q -->|Add Resident| R[Tambah Warga]
    Q -->|Edit Resident| S[Edit Warga]
    Q -->|Delete Resident| T[Hapus Warga]
    Q -->|View Resident| U[View Resident Details]
    
    E --> V[KK Management]
    V --> W{KK Actions}
    W -->|Add KK| X[Tambah KK]
    W -->|Edit KK| Y[Edit KK]
    W -->|Delete KK| Z[Hapus KK]
    W -->|View KK| AA[View KK Details]
    
    F --> BB[User Management]
    BB --> CC{User Actions}
    CC -->|View Users| DD[View All Users]
    CC -->|Edit User| EE[Edit User]
    
    G --> FF[Profile Management]
    H --> GG[RT Analytics]
    I --> HH[Clear Session] --> II[Redirect to Login]
    
    style H fill:#ffd93d,stroke:#333,stroke-width:2px,color:#000
    style O fill:#4ecdc4,stroke:#333,stroke-width:2px,color:#fff
```

## 8. FLOWCHART LAPORAN (REPORT) PROCESS (Updated)

```mermaid
flowchart TD
    A[User Creates Report] --> B[Fill Report Form]
    B --> C[Upload Photo Optional]
    C --> D[Submit Report]
    D --> E{Form Validation}
    E -->|Invalid| F[Show Error] --> B
    E -->|Valid| G[Save to Database]
    G --> H[Status: Pending]
    H --> I[Notify Admin/RT]
    I --> J[Admin/RT Review]
    J --> K{Decision}
    K -->|Approve| L[Status: Proses]
    K -->|Reject| M[Status: Rejected]
    K -->|Complete| N[Status: Completed]
    
    L --> O[Process Report]
    O --> P{Process Complete?}
    P -->|Yes| N
    P -->|No| O
    
    M --> Q[Send Rejection Notice]
    N --> R[Send Completion Notice]
    Q --> S[User Notification]
    R --> S
    S --> T[End Process]
    
    style H fill:#ffd93d,stroke:#333,stroke-width:2px,color:#000
    style L fill:#4ecdc4,stroke:#333,stroke-width:2px,color:#fff
    style N fill:#34a853,stroke:#333,stroke-width:2px,color:#fff
    style M fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
```

## 9. FLOWCHART DATABASE RELATIONSHIPS (Updated)

```mermaid
erDiagram
    USERS {
        int id PK
        varchar username UK
        varchar email UK
        varchar password
        enum role
        timestamp created_at
        timestamp updated_at
    }
    
    KELUARGA {
        int id PK
        varchar no_kk UK
        varchar kepala_keluarga
        text alamat
        varchar rt
        varchar rw
        varchar desa
        varchar kecamatan
        varchar kabupaten
        varchar provinsi
        varchar kode_pos
        timestamp created_at
        timestamp updated_at
    }
    
    WARGA {
        int id PK
        varchar nik UK
        varchar nama
        varchar tempat_lahir
        date tanggal_lahir
        enum jenis_kelamin
        text alamat
        varchar rt
        varchar rw
        varchar desa
        varchar kecamatan
        varchar agama
        varchar status_perkawinan
        varchar pekerjaan
        varchar kewarganegaraan
        int kk_id FK
        timestamp created_at
        timestamp updated_at
    }
    
    LAPORAN {
        int id PK
        int user_id FK
        varchar judul
        text deskripsi
        varchar lokasi
        varchar kategori
        enum status
        varchar foto
        timestamp created_at
        timestamp updated_at
    }
    
    KOMENTAR_LAPORAN {
        int id PK
        int laporan_id FK
        int user_id FK
        text komentar
        timestamp created_at
    }
    
    USERS ||--o{ LAPORAN : "creates"
    USERS ||--o{ KOMENTAR_LAPORAN : "comments"
    KELUARGA ||--o{ WARGA : "contains"
    LAPORAN ||--o{ KOMENTAR_LAPORAN : "has"
```

## 10. FLOWCHART STATUS LAPORAN (Updated)

```mermaid
stateDiagram-v2
    [*] --> Pending
    Pending --> Proses : Admin/RT Approves
    Pending --> Rejected : Admin/RT Rejects
    Proses --> Completed : Process Finished
    Proses --> Rejected : Process Failed
    Completed --> [*]
    Rejected --> [*]
    
    note right of Pending : User submits report
    note right of Proses : Admin/RT processes
    note right of Completed : Issue resolved
    note right of Rejected : Issue not valid
```

## 11. FLOWCHART FILE UPLOAD PROCESS (Updated)

```mermaid
flowchart TD
    A[User Selects File] --> B{File Selected?}
    B -->|No| C[Continue Without Photo]
    B -->|Yes| D[Validate File Type]
    D --> E{Valid Type?}
    E -->|No| F[Show Error: Invalid Format] --> A
    E -->|Yes| G[Check File Size]
    G --> H{Size OK?}
    H -->|No| I[Show Error: File Too Large] --> A
    H -->|Yes| J[Generate Unique Filename]
    J --> K[Upload to Server]
    K --> L{Upload Success?}
    L -->|No| M[Show Error: Upload Failed] --> A
    L -->|Yes| N[Save Filename to Database]
    N --> O[Continue with Report]
    C --> O
    
    style D fill:#ffd93d,stroke:#333,stroke-width:2px,color:#000
    style G fill:#ffd93d,stroke:#333,stroke-width:2px,color:#000
    style N fill:#34a853,stroke:#333,stroke-width:2px,color:#fff
```

## 12. FLOWCHART GOOGLE OAUTH FLOW (New)

```mermaid
sequenceDiagram
    participant U as User
    participant A as App
    participant G as Google
    participant D as Database
    
    U->>A: Click Google Login
    A->>G: Redirect to OAuth
    G->>U: Show Google Login
    U->>G: Enter Credentials
    G->>A: Return Auth Code
    A->>G: Exchange Code for Token
    G->>A: Return Access Token
    A->>G: Get User Info
    G->>A: Return User Data
    A->>D: Check if User Exists
    alt User Exists
        D->>A: Return User Data
        A->>A: Create Session
    else User Not Exists
        A->>D: Create New User
        D->>A: Confirm Creation
        A->>A: Create Session
    end
    A->>U: Redirect to Dashboard
```

## 13. USE CASE DIAGRAM - OVERVIEW (Updated)

```mermaid
flowchart LR
    actor_user[User]
    actor_rt[RT]
    actor_admin[Admin]

    subgraph System[Aplikasi Laporin Lingkungan v2.0]
        UC_Dashboard([Lihat Dashboard])
        UC_Login([Login])
        UC_GoogleLogin([Google OAuth])
        UC_Register([Register])
        UC_BuatLaporan([Buat Laporan])
        UC_UploadFoto([Upload Foto Bukti])
        UC_LihatLaporan([Lihat Laporan])
        UC_EditLaporan([Edit Laporan Sendiri])
        UC_LihatDetail([Lihat Detail Laporan])
        UC_TambahKomentar([Tambah Komentar])
        UC_UbahStatus([Ubah Status Laporan])
        UC_KelolaLaporan([Kelola Laporan])
        UC_KelolaWarga([Kelola Data Warga])
        UC_KelolaKK([Kelola Data KK])
        UC_KelolaUser([Kelola Data User])
        UC_Profil([Kelola Profil])
        UC_Statistik([Lihat Statistik])
        UC_Logout([Logout])
    end

    actor_user --> UC_Register
    actor_user --> UC_Login
    actor_user --> UC_GoogleLogin
    actor_user --> UC_Dashboard
    actor_user --> UC_BuatLaporan
    actor_user --> UC_LihatLaporan
    actor_user --> UC_EditLaporan
    actor_user --> UC_Profil
    actor_user --> UC_Logout

    actor_rt --> UC_Login
    actor_rt --> UC_Dashboard
    actor_rt --> UC_KelolaLaporan
    actor_rt --> UC_KelolaWarga
    actor_rt --> UC_KelolaKK
    actor_rt --> UC_Statistik
    actor_rt --> UC_Profil
    actor_rt --> UC_Logout

    actor_admin --> UC_Login
    actor_admin --> UC_Dashboard
    actor_admin --> UC_KelolaLaporan
    actor_admin --> UC_KelolaWarga
    actor_admin --> UC_KelolaKK
    actor_admin --> UC_KelolaUser
    actor_admin --> UC_Statistik
    actor_admin --> UC_Logout

    UC_BuatLaporan -.->|<<extend>>| UC_UploadFoto
    UC_KelolaLaporan -.->|<<include>>| UC_LihatDetail
    UC_KelolaLaporan -.->|<<include>>| UC_UbahStatus
    UC_KelolaLaporan -.->|<<extend>>| UC_TambahKomentar

    style System fill:#f9f9f9,stroke:#bbb,stroke-width:2px
    style UC_GoogleLogin fill:#4285f4,stroke:#333,stroke-width:2px,color:#fff
    style UC_Statistik fill:#ffd93d,stroke:#333,stroke-width:2px,color:#000
```

## 14. USE CASE DIAGRAM - USER (Updated)

```mermaid
flowchart LR
    actor_user[User]
    subgraph System[Aplikasi Laporin Lingkungan v2.0]
        UC_Login([Login])
        UC_GoogleLogin([Google OAuth])
        UC_Register([Register])
        UC_Dashboard([Lihat Dashboard])
        UC_BuatLaporan([Buat Laporan])
        UC_UploadFoto([Upload Foto Bukti])
        UC_LihatLaporan([Lihat Laporan Saya])
        UC_EditLaporan([Edit Laporan Sendiri])
        UC_LihatDetail([Lihat Detail Laporan])
        UC_Profil([Kelola Profil])
        UC_Logout([Logout])
    end

    actor_user --> UC_Register
    actor_user --> UC_Login
    actor_user --> UC_GoogleLogin
    actor_user --> UC_Dashboard
    actor_user --> UC_BuatLaporan
    actor_user --> UC_LihatLaporan
    actor_user --> UC_EditLaporan
    actor_user --> UC_LihatDetail
    actor_user --> UC_Profil
    actor_user --> UC_Logout

    UC_BuatLaporan -.->|<<extend>>| UC_UploadFoto
    style System fill:#f9f9f9,stroke:#bbb,stroke-width:2px
    style UC_GoogleLogin fill:#4285f4,stroke:#333,stroke-width:2px,color:#fff
```

## 15. USE CASE DIAGRAM - RT (Updated)

```mermaid
flowchart LR
    actor_rt[RT]
    subgraph System[Aplikasi Laporin Lingkungan v2.0]
        UC_Login([Login])
        UC_Dashboard([Lihat Dashboard])
        UC_KelolaLaporan([Kelola Laporan])
        UC_LihatDetail([Lihat Detail Laporan])
        UC_UbahStatus([Ubah Status Laporan])
        UC_TambahKomentar([Tambah Komentar])
        UC_KelolaWarga([Kelola Data Warga])
        UC_KelolaKK([Kelola Data KK])
        UC_Statistik([Lihat Statistik RT])
        UC_Profil([Kelola Profil])
        UC_Logout([Logout])
    end

    actor_rt --> UC_Login
    actor_rt --> UC_Dashboard
    actor_rt --> UC_KelolaLaporan
    actor_rt --> UC_KelolaWarga
    actor_rt --> UC_KelolaKK
    actor_rt --> UC_Statistik
    actor_rt --> UC_Profil
    actor_rt --> UC_Logout

    UC_KelolaLaporan -.->|<<include>>| UC_LihatDetail
    UC_KelolaLaporan -.->|<<include>>| UC_UbahStatus
    UC_KelolaLaporan -.->|<<extend>>| UC_TambahKomentar
    style System fill:#f9f9f9,stroke:#bbb,stroke-width:2px
    style UC_Statistik fill:#ffd93d,stroke:#333,stroke-width:2px,color:#000
```

## 16. USE CASE DIAGRAM - ADMIN (Updated)

```mermaid
flowchart LR
    actor_admin[Admin]
    subgraph System[Aplikasi Laporin Lingkungan v2.0]
        UC_Login([Login])
        UC_Dashboard([Lihat Dashboard])
        UC_KelolaLaporan([Kelola Laporan])
        UC_LihatDetail([Lihat Detail Laporan])
        UC_UbahStatus([Ubah Status Laporan])
        UC_TambahKomentar([Tambah Komentar])
        UC_KelolaWarga([Kelola Data Warga])
        UC_KelolaKK([Kelola Data KK])
        UC_KelolaUser([Kelola Data User])
        UC_Statistik([Lihat Statistik Sistem])
        UC_Profil([Kelola Profil])
        UC_Logout([Logout])
    end

    actor_admin --> UC_Login
    actor_admin --> UC_Dashboard
    actor_admin --> UC_KelolaLaporan
    actor_admin --> UC_KelolaWarga
    actor_admin --> UC_KelolaKK
    actor_admin --> UC_KelolaUser
    actor_admin --> UC_Statistik
    actor_admin --> UC_Profil
    actor_admin --> UC_Logout

    UC_KelolaLaporan -.->|<<include>>| UC_LihatDetail
    UC_KelolaLaporan -.->|<<include>>| UC_UbahStatus
    UC_KelolaLaporan -.->|<<extend>>| UC_TambahKomentar
    style System fill:#f9f9f9,stroke:#bbb,stroke-width:2px
    style UC_Statistik fill:#ffd93d,stroke:#333,stroke-width:2px,color:#000
```

## 17. FLOWCHART SECURITY FEATURES (New)

```mermaid
flowchart TD
    A[User Request] --> B{Session Valid?}
    B -->|No| C[Redirect to Login]
    B -->|Yes| D{Role Check}
    
    D -->|Insufficient| E[Show 403 Error]
    D -->|Valid| F[Process Request]
    
    F --> G{Input Validation}
    G -->|Invalid| H[Show Validation Error]
    G -->|Valid| I{SQL Injection Check}
    
    I -->|Detected| J[Block Request]
    I -->|Clean| K{File Upload Check}
    
    K -->|Invalid| L[Reject File]
    K -->|Valid| M[Process Request]
    
    M --> N[Database Query]
    N --> O[Response]
    
    style C fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
    style E fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
    style J fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
    style L fill:#ff6b6b,stroke:#333,stroke-width:2px,color:#fff
    style O fill:#34a853,stroke:#333,stroke-width:2px,color:#fff
```

## 18. FLOWCHART MODERN UI COMPONENTS (New)

```mermaid
flowchart TD
    A[User Interface] --> B{Component Type}
    
    B -->|Navigation| C[Navbar Component]
    B -->|Forms| D[Form Components]
    B -->|Tables| E[Data Tables]
    B -->|Cards| F[Card Layouts]
    B -->|Modals| G[Modal Dialogs]
    
    C --> H[Responsive Menu]
    C --> I[User Dropdown]
    C --> J[Breadcrumbs]
    
    D --> K[Input Fields]
    D --> L[Validation Messages]
    D --> M[Submit Buttons]
    
    E --> N[Sortable Columns]
    E --> O[Pagination]
    E --> P[Search Filter]
    
    F --> Q[Report Cards]
    F --> R[Profile Cards]
    F --> S[Dashboard Cards]
    
    G --> T[Confirmation Dialogs]
    G --> U[Form Modals]
    G --> V[Detail Views]
    
    style A fill:#667eea,stroke:#333,stroke-width:2px,color:#fff
    style H fill:#4ecdc4,stroke:#333,stroke-width:2px,color:#fff
    style K fill:#ffd93d,stroke:#333,stroke-width:2px,color:#000
```

## KESIMPULAN v2.0

Aplikasi Laporin Lingkungan v2.0 memiliki fitur-fitur modern dan peningkatan signifikan:

### 🌟 Fitur Baru
1. **Google OAuth Integration**: Login menggunakan akun Google
2. **Modern UI/UX**: Bootstrap 5.3.7 dengan custom styling
3. **Enhanced Security**: Improved validation dan security measures
4. **Better File Handling**: Advanced file upload dengan validasi
5. **Responsive Design**: Optimized untuk semua device

### 👥 Level Pengguna
1. **User** - Masyarakat yang dapat membuat dan melihat laporan
2. **RT** - Ketua RT yang dapat mengelola laporan dan data warga
3. **Admin** - Administrator yang memiliki akses penuh ke semua fitur

### 🔄 Alur Utama Aplikasi
1. **Authentication** - Login/Register dengan traditional atau Google OAuth
2. **Report Management** - Pembuatan, review, dan tracking laporan
3. **Data Management** - Pengelolaan data warga, KK, dan user
4. **Status Tracking** - Monitoring status laporan dari pending hingga completed
5. **File Management** - Upload dan management foto bukti laporan

### 🛠️ Teknologi
- **Backend**: PHP dengan modern security practices
- **Database**: MySQL dengan relasi antar tabel yang jelas
- **Frontend**: Bootstrap 5.3.7, Font Awesome, Custom CSS
- **Authentication**: Google OAuth 2.0 + Traditional login
- **File System**: Secure file upload dengan validasi

Sistem ini dirancang untuk memberikan pengalaman yang modern, aman, dan user-friendly dalam pelaporan masalah lingkungan masyarakat.
