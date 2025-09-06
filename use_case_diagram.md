# USE CASE DIAGRAM LAPORIN LINGKUNGAN v2.0

## 📋 DAFTAR ISI
1. [User Use Cases](#user-use-cases)
2. [RT Use Cases](#rt-use-cases)
3. [Admin Use Cases](#admin-use-cases)

---

---

## 1. USER USE CASES

```mermaid
flowchart LR
    actor_user[👤 User<br/>Masyarakat]
    
    subgraph UserSystem[User Panel System]
        subgraph Auth[Authentication]
            UC_Login([Login])
            UC_GoogleLogin([Google OAuth])
            UC_Register([Register])
        end
        
        subgraph Main[Main Features]
            UC_Dashboard([Dashboard])
            UC_BuatLaporan([Buat Laporan])
            UC_LihatLaporan([Laporan Saya])
            UC_EditLaporan([Edit Laporan])
            UC_DetailLaporan([Detail Laporan])
        end
        
        subgraph Profile[Profile Management]
            UC_Profil([Profil])
            UC_EditProfil([Edit Profil])
            UC_ChangePassword([Ubah Password])
        end
        
        subgraph Support[Support]
            UC_Bantuan([Bantuan])
            UC_Logout([Logout])
        end
    end

    %% User connections
    actor_user --> UC_Register
    actor_user --> UC_Login
    actor_user --> UC_GoogleLogin
    actor_user --> UC_Dashboard
    actor_user --> UC_BuatLaporan
    actor_user --> UC_LihatLaporan
    actor_user --> UC_EditLaporan
    actor_user --> UC_DetailLaporan
    actor_user --> UC_Profil
    actor_user --> UC_EditProfil
    actor_user --> UC_ChangePassword
    actor_user --> UC_Bantuan
    actor_user --> UC_Logout

    %% Include relationships
    UC_EditLaporan -.->|<<include>>| UC_DetailLaporan
    UC_Profil -.->|<<include>>| UC_EditProfil
    UC_Profil -.->|<<include>>| UC_ChangePassword

    style UserSystem fill:#f8f9fa,stroke:#dee2e6,stroke-width:3px
    style Auth fill:#e3f2fd,stroke:#2196f3,stroke-width:2px
    style Main fill:#f3e5f5,stroke:#9c27b0,stroke-width:2px
    style Profile fill:#e8f5e8,stroke:#4caf50,stroke-width:2px
    style Support fill:#fff3e0,stroke:#ff9800,stroke-width:2px
    style UC_GoogleLogin fill:#4285f4,stroke:#333,stroke-width:2px,color:#fff
```

---

## 2. RT USE CASES

```mermaid
flowchart LR
    actor_rt[🏘️ RT<br/>Ketua RT]
    
    subgraph RTSystem[RT Panel System]
        subgraph Auth[Authentication]
            UC_Login([Login])
        end
        
        subgraph Main[Main Features]
            UC_Dashboard([Dashboard])
            UC_KelolaLaporan([Kelola Laporan])
            UC_KelolaWarga([Kelola Warga])
            UC_KelolaKK([Kelola KK])
        end
        
        subgraph Management[Management]
            UC_UpdateStatus([Update Status])
            UC_TambahKomentar([Tambah Komentar])
            UC_DetailLaporan([Detail Laporan])
            UC_StatistikRT([Statistik RT])
        end
        
        subgraph Profile[Profile]
            UC_Profil([Profil])
            UC_Logout([Logout])
        end
    end

    %% RT connections
    actor_rt --> UC_Login
    actor_rt --> UC_Dashboard
    actor_rt --> UC_KelolaLaporan
    actor_rt --> UC_KelolaWarga
    actor_rt --> UC_KelolaKK
    actor_rt --> UC_UpdateStatus
    actor_rt --> UC_TambahKomentar
    actor_rt --> UC_DetailLaporan
    actor_rt --> UC_StatistikRT
    actor_rt --> UC_Profil
    actor_rt --> UC_Logout

    %% Include relationships
    UC_KelolaLaporan -.->|<<include>>| UC_UpdateStatus
    UC_KelolaLaporan -.->|<<include>>| UC_DetailLaporan
    UC_KelolaLaporan -.->|<<extend>>| UC_TambahKomentar
    UC_Dashboard -.->|<<include>>| UC_StatistikRT

    style RTSystem fill:#f8f9fa,stroke:#dee2e6,stroke-width:3px
    style Auth fill:#e3f2fd,stroke:#2196f3,stroke-width:2px
    style Main fill:#f3e5f5,stroke:#9c27b0,stroke-width:2px
    style Management fill:#e8f5e8,stroke:#4caf50,stroke-width:2px
    style Profile fill:#fff3e0,stroke:#ff9800,stroke-width:2px
    style UC_StatistikRT fill:#ffd93d,stroke:#333,stroke-width:2px,color:#000
```

---

## 3. ADMIN USE CASES

```mermaid
flowchart LR
    actor_admin[👨‍💼 Admin<br/>Administrator]
    
    subgraph AdminSystem[Admin Panel System]
        subgraph Auth[Authentication]
            UC_Login([Login])
        end
        
        subgraph Main[Main Features]
            UC_Dashboard([Dashboard])
            UC_KelolaLaporan([Kelola Laporan])
            UC_KelolaWarga([Kelola Warga])
            UC_KelolaKK([Kelola KK])
            UC_KelolaUser([Kelola User])
        end
        
        subgraph Management[Management]
            UC_UpdateStatus([Update Status])
            UC_TambahKomentar([Tambah Komentar])
            UC_DetailLaporan([Detail Laporan])
            UC_StatistikSistem([Statistik Sistem])
        end
        
        subgraph System[System]
            UC_Backup([Backup Data])
            UC_Maintenance([Maintenance])
            UC_Logout([Logout])
        end
    end

    %% Admin connections
    actor_admin --> UC_Login
    actor_admin --> UC_Dashboard
    actor_admin --> UC_KelolaLaporan
    actor_admin --> UC_KelolaWarga
    actor_admin --> UC_KelolaKK
    actor_admin --> UC_KelolaUser
    actor_admin --> UC_UpdateStatus
    actor_admin --> UC_TambahKomentar
    actor_admin --> UC_DetailLaporan
    actor_admin --> UC_StatistikSistem
    actor_admin --> UC_Backup
    actor_admin --> UC_Maintenance
    actor_admin --> UC_Logout

    %% Include relationships
    UC_KelolaLaporan -.->|<<include>>| UC_UpdateStatus
    UC_KelolaLaporan -.->|<<include>>| UC_DetailLaporan
    UC_KelolaLaporan -.->|<<extend>>| UC_TambahKomentar
    UC_Dashboard -.->|<<include>>| UC_StatistikSistem
    UC_Dashboard -.->|<<extend>>| UC_Backup

    style AdminSystem fill:#f8f9fa,stroke:#dee2e6,stroke-width:3px
    style Auth fill:#e3f2fd,stroke:#2196f3,stroke-width:2px
    style Main fill:#f3e5f5,stroke:#9c27b0,stroke-width:2px
    style Management fill:#e8f5e8,stroke:#4caf50,stroke-width:2px
    style System fill:#fff3e0,stroke:#ff9800,stroke-width:2px
    style UC_StatistikSistem fill:#ffd93d,stroke:#333,stroke-width:2px,color:#000
```

---



Sistem ini dirancang dengan **role-based access control** yang jelas, dimana setiap role memiliki permission yang berbeda sesuai dengan tanggung jawabnya dalam sistem pelaporan lingkungan.
