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



Sistem ini dirancang untuk memberikan pengalaman yang modern, aman, dan user-friendly dalam pelaporan masalah lingkungan masyarakat.


