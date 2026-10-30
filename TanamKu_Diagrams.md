# 📊 Kumpulan Diagram — TanamKu Marketplace

> **TanamKu** adalah marketplace tanaman hias berbasis Laravel 12 dengan tiga peran: **Admin**, **Seller (Penjual)**, dan **Buyer (Pembeli)**.

---

## 1. Use Case Diagram

```mermaid
flowchart TD
    subgraph Aktor
        G["👤 Guest\n(Tamu)"]
        B["🛒 Buyer\n(Pembeli)"]
        S["🏪 Seller\n(Penjual)"]
        A["🛡️ Admin"]
    end

    subgraph UC_PUBLIK["Use Case — Publik"]
        UC1["Lihat Beranda"]
        UC2["Jelajahi Katalog"]
        UC3["Lihat Detail Tanaman"]
        UC4["Lihat Profil Toko"]
        UC5["Cari Tanaman"]
    end

    subgraph UC_AUTH["Use Case — Autentikasi"]
        UC6["Daftar Akun"]
        UC7["Verifikasi Email OTP"]
        UC8["Masuk / Login"]
        UC9["Lupa Password\n(3 Langkah)"]
        UC10["Keluar / Logout"]
    end

    subgraph UC_BUYER["Use Case — Pembeli"]
        UC11["Tambah ke Keranjang"]
        UC12["Kelola Keranjang"]
        UC13["Checkout Pesanan"]
        UC14["Lihat Riwayat Pesanan"]
        UC15["Tandai Pesanan Selesai"]
        UC16["Beri Ulasan & Rating"]
    end

    subgraph UC_SELLER["Use Case — Penjual"]
        UC17["Kelola Profil Toko"]
        UC18["Tambah / Edit Produk"]
        UC19["Hapus Produk"]
        UC20["Lihat & Update Status Pesanan"]
        UC21["Export Pesanan CSV"]
        UC22["Dashboard Statistik"]
    end

    subgraph UC_ADMIN["Use Case — Admin"]
        UC23["Kelola Pengguna\n(Role & Status)"]
        UC24["Setujui / Nonaktifkan Toko"]
        UC25["Kelola Kategori"]
        UC26["Moderasi Produk"]
        UC27["Pantau Semua Pesanan"]
        UC28["Export Pesanan CSV"]
        UC29["Dashboard Platform"]
    end

    G --> UC1
    G --> UC2
    G --> UC3
    G --> UC4
    G --> UC5
    G --> UC6
    G --> UC8

    B --> UC1
    B --> UC2
    B --> UC3
    B --> UC4
    B --> UC5
    B --> UC7
    B --> UC9
    B --> UC10
    B --> UC11
    B --> UC12
    B --> UC13
    B --> UC14
    B --> UC15
    B --> UC16

    S --> UC1
    S --> UC2
    S --> UC9
    S --> UC10
    S --> UC17
    S --> UC18
    S --> UC19
    S --> UC20
    S --> UC21
    S --> UC22

    A --> UC9
    A --> UC10
    A --> UC23
    A --> UC24
    A --> UC25
    A --> UC26
    A --> UC27
    A --> UC28
    A --> UC29
```

---

## 2. Activity Diagram — Alur Pembelian (Buyer)

```mermaid
flowchart TD
    Start([🟢 Mulai]) --> A["Buka Katalog Tanaman"]
    A --> B["Pilih Tanaman"]
    B --> C{"Sudah Login?"}
    C -- Tidak --> D["Login / Daftar"]
    D --> E{"Login Berhasil?"}
    E -- Tidak --> D
    E -- Ya --> B
    C -- Ya --> F["Tambah ke Keranjang"]
    F --> G["Buka Keranjang"]
    G --> H{"Keranjang\nKosong?"}
    H -- Ya --> A
    H -- Tidak --> I["Pilih Toko untuk Checkout"]
    I --> J["Isi Form Checkout\n(nama, HP, alamat, metode)"]
    J --> K{"Validasi\nInput"}
    K -- Gagal --> J
    K -- Lolos --> L{"Cek Stok\n(DB Transaction)"}
    L -- "Stok Habis" --> M["Tampil Error Stok"]
    M --> G
    L -- Aman --> N["Buat Pesanan\n& Kurangi Stok"]
    N --> O["Kosongkan Keranjang\nToko ini"]
    O --> P["Redirect ke Detail Pesanan"]
    P --> Q["Chat WhatsApp Penjual"]
    Q --> R{"Pesanan\nSelesai?"}
    R -- Tidak --> R
    R -- Ya --> S["Tandai Selesai"]
    S --> T{"Sudah Beri\nUlasan?"}
    T -- Belum --> U["Beri Rating & Komentar"]
    U --> V([🔴 Selesai])
    T -- Sudah --> V
```

---

## 3. Activity Diagram — Pendaftaran & Verifikasi Email

```mermaid
flowchart TD
    Start([🟢 Mulai]) --> A["Buka Halaman Daftar"]
    A --> B["Isi Form\n(nama, email, password, role)"]
    B --> C{"Validasi\nServer"}
    C -- Gagal --> B
    C -- Lolos --> D["Buat Akun\n(is_active=false)"]
    D --> E["Kirim OTP ke Email\n(via Brevo SMTP)"]
    E --> F["Redirect ke Halaman\nVerifikasi Email"]
    F --> G["User Input Kode OTP"]
    G --> H{"OTP Valid\n& Belum Expired?"}
    H -- Tidak --> I{"Minta Kirim\nUlang?"}
    I -- Ya --> E
    I -- Tidak --> G
    H -- Ya --> J["Aktifkan Akun\n(email_verified_at diisi)"]
    J --> K{"Role User?"}
    K -- Buyer --> L["Redirect ke Beranda"]
    K -- Seller --> M["Redirect ke Dashboard\nSeller"]
    L --> End([🔴 Selesai])
    M --> End
```

---

## 4. Activity Diagram — Proses Persetujuan Toko (Admin)

```mermaid
flowchart TD
    Start([🟢 Mulai]) --> A["Seller Daftar &\nVerifikasi Email"]
    A --> B["Toko Otomatis Dibuat\n(status: menunggu)"]
    B --> C["Admin Buka\nDashboard Toko"]
    C --> D["Admin Review\nData Toko"]
    D --> E{"Keputusan\nAdmin"}
    E -- "Setujui" --> F["Status Toko\n→ aktif"]
    E -- "Tolak" --> G["Status Toko\n→ nonaktif"]
    F --> H["Produk Toko\nMuncul di Katalog"]
    G --> I["Produk Toko\nTersembunyi"]
    H --> End([🔴 Selesai])
    I --> End
```

---

## 5. Sequence Diagram — Login

```mermaid
sequenceDiagram
    actor U as User (Browser)
    participant C as AuthController
    participant M as User Model
    participant S as Session/Auth
    participant DB as Database (Supabase)

    U->>C: POST /login {email, password}
    C->>C: Validasi input
    C->>M: Auth::attempt({email, password})
    M->>DB: SELECT user WHERE email=?
    DB-->>M: User record
    M->>M: Hash::check(password)
    M-->>C: true / false
    alt Login Berhasil
        C->>S: Regenerate session token
        C-->>U: Redirect ke beranda/dashboard
    else Login Gagal
        C-->>U: Kembali ke form + pesan error
    end
```

---

## 6. Sequence Diagram — Proses Checkout

```mermaid
sequenceDiagram
    actor B as Buyer (Browser)
    participant CC as CheckoutController
    participant Cart as CartItem Model
    participant Plant as Plant Model
    participant Order as Order Model
    participant DB as Database

    B->>CC: GET /checkout/{store}
    CC->>Cart: Ambil item keranjang toko ini
    Cart->>DB: SELECT cart_items WHERE user_id & store_id
    DB-->>Cart: List CartItem
    CC-->>B: Tampil form checkout + total

    B->>CC: POST /checkout/{store} {nama, hp, alamat, metode}
    CC->>CC: Validasi input request

    loop Setiap item di keranjang
        CC->>Plant: lockForUpdate() - cek stok
        Plant->>DB: SELECT ... FOR UPDATE
        DB-->>Plant: Stok terkini
        Plant-->>CC: Stok OK / Kurang
    end

    alt Stok Cukup
        CC->>DB: BEGIN TRANSACTION
        CC->>Order: Order::create(...)
        CC->>Order: items()->create(...) x N
        CC->>Plant: decrement('stok', jumlah) x N
        CC->>Cart: delete() - kosongkan keranjang
        CC->>DB: COMMIT
        CC-->>B: Redirect ke detail pesanan + link WA
    else Stok Kurang
        CC->>DB: ROLLBACK
        CC-->>B: Kembali ke keranjang + error stok
    end
```

---

## 7. Sequence Diagram — Reset Password (3 Langkah)

```mermaid
sequenceDiagram
    actor U as User (Browser)
    participant AC as AuthController
    participant DB as Database
    participant Mail as Brevo SMTP

    Note over U,Mail: Langkah 1 — Kirim Email
    U->>AC: POST /lupa-password {email}
    AC->>DB: Cari user by email
    DB-->>AC: User found
    AC->>DB: Simpan OTP (6 digit) + expired_at
    AC->>Mail: Kirim email OTP
    Mail-->>U: Email berisi kode OTP
    AC-->>U: Redirect ke halaman input kode

    Note over U,Mail: Langkah 2 — Verifikasi OTP
    U->>AC: POST /reset-password/kode {email, kode_otp}
    AC->>DB: Cari OTP record (email + kode)
    DB-->>AC: OTP record
    AC->>AC: Cek expired_at > now()
    alt OTP Valid
        AC->>DB: Tandai OTP dipakai
        AC->>AC: Simpan token ke session
        AC-->>U: Redirect ke form password baru
    else OTP Salah / Expired
        AC-->>U: Error + form ulang
    end

    Note over U,Mail: Langkah 3 — Set Password Baru
    U->>AC: POST /reset-password {password, confirm}
    AC->>AC: Validasi & cek session token
    AC->>DB: Update password user (bcrypt)
    AC->>DB: Hapus OTP record
    AC-->>U: Redirect ke login + pesan sukses
```

---

## 8. Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email UK
        string password
        enum role "admin|seller|buyer"
        string no_hp
        string alamat
        boolean is_active
        timestamp email_verified_at
        timestamp created_at
        timestamp updated_at
    }

    STORES {
        bigint id PK
        bigint user_id FK
        string nama_toko
        string slug UK
        string no_wa
        string alamat
        string kota
        text deskripsi
        string logo
        enum metode_serah "ambil|kirim|keduanya"
        enum status "menunggu|aktif|nonaktif"
        timestamp created_at
        timestamp updated_at
    }

    CATEGORIES {
        bigint id PK
        string nama
        string slug UK
        string ikon
        timestamp created_at
        timestamp updated_at
    }

    PLANTS {
        bigint id PK
        bigint store_id FK
        bigint category_id FK
        string nama
        string slug UK
        text deskripsi
        text perawatan
        decimal harga
        integer stok
        string foto
        enum kondisi "baru|bekas"
        boolean is_featured
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }

    ORDERS {
        bigint id PK
        string kode_pesanan UK
        bigint buyer_id FK
        bigint store_id FK
        string nama_penerima
        string no_hp
        text alamat
        enum metode_serah "ambil|kirim"
        text catatan
        decimal total_harga
        enum status "baru|diproses|selesai|batal"
        timestamp created_at
        timestamp updated_at
    }

    ORDER_ITEMS {
        bigint id PK
        bigint order_id FK
        bigint plant_id FK
        string nama_produk
        decimal harga_satuan
        integer jumlah
        decimal subtotal
        timestamp created_at
        timestamp updated_at
    }

    CART_ITEMS {
        bigint id PK
        bigint user_id FK
        bigint plant_id FK
        integer jumlah
        timestamp created_at
        timestamp updated_at
    }

    REVIEWS {
        bigint id PK
        bigint order_id FK
        bigint plant_id FK
        bigint user_id FK
        tinyint rating "1-5"
        text komentar
        timestamp created_at
        timestamp updated_at
    }

    PASSWORD_RESET_TOKENS {
        string email PK
        string token
        string otp_code
        timestamp expires_at
        timestamp created_at
    }

    USERS ||--o| STORES : "memiliki (seller)"
    USERS ||--o{ ORDERS : "membeli (buyer_id)"
    USERS ||--o{ CART_ITEMS : "punya keranjang"
    USERS ||--o{ REVIEWS : "menulis"

    STORES ||--o{ PLANTS : "menjual"
    STORES ||--o{ ORDERS : "menerima"

    CATEGORIES ||--o{ PLANTS : "mengkategorikan"

    PLANTS ||--o{ ORDER_ITEMS : "termasuk dalam"
    PLANTS ||--o{ CART_ITEMS : "ada di keranjang"
    PLANTS ||--o{ REVIEWS : "mendapat ulasan"

    ORDERS ||--o{ ORDER_ITEMS : "terdiri dari"
    ORDERS ||--o{ REVIEWS : "memiliki ulasan"
```

---

## 9. Class Diagram

```mermaid
classDiagram
    class User {
        +bigint id
        +string name
        +string email
        +string password
        +string role
        +string no_hp
        +string alamat
        +bool is_active
        +timestamp email_verified_at
        +isAdmin() bool
        +isSeller() bool
        +isBuyer() bool
        +cartCount() int
        +getRoleLabelAttribute() string
        +store() HasOne
        +orders() HasMany
        +reviews() HasMany
        +cartItems() HasMany
    }

    class Store {
        +bigint id
        +bigint user_id
        +string nama_toko
        +string slug
        +string no_wa
        +string kota
        +string status
        +string metode_serah
        +isAktif() bool
        +getWaNumberAttribute() string
        +getLogoUrlAttribute() string
        +scopeAktif()
        +user() BelongsTo
        +plants() HasMany
        +orders() HasMany
    }

    class Plant {
        +bigint id
        +bigint store_id
        +bigint category_id
        +string nama
        +string slug
        +decimal harga
        +int stok
        +bool is_active
        +bool is_featured
        +getRatingRataRataAttribute() float
        +getStokStatusAttribute() string
        +getHargaRupiahAttribute() string
        +getFotoUrlAttribute() string
        +scopeTampil()
        +scopeFeatured()
        +store() BelongsTo
        +category() BelongsTo
        +reviews() HasMany
        +cartItems() HasMany
        +orderItems() HasMany
    }

    class Order {
        +bigint id
        +string kode_pesanan
        +bigint buyer_id
        +bigint store_id
        +string status
        +decimal total_harga
        +string metode_serah
        +getStatusLabelAttribute() string
        +getTotalHargaRupiahAttribute() string
        +scopeStatus()
        +buyer() BelongsTo
        +store() BelongsTo
        +items() HasMany
        +reviews() HasMany
    }

    class OrderItem {
        +bigint id
        +bigint order_id
        +bigint plant_id
        +string nama_produk
        +decimal harga_satuan
        +int jumlah
        +decimal subtotal
        +order() BelongsTo
        +plant() BelongsTo
    }

    class CartItem {
        +bigint id
        +bigint user_id
        +bigint plant_id
        +int jumlah
        +getSubtotalAttribute() float
        +user() BelongsTo
        +plant() BelongsTo
    }

    class Category {
        +bigint id
        +string nama
        +string slug
        +string ikon
        +plants() HasMany
    }

    class Review {
        +bigint id
        +bigint order_id
        +bigint plant_id
        +bigint user_id
        +int rating
        +string komentar
        +order() BelongsTo
        +plant() BelongsTo
        +user() BelongsTo
    }

    User "1" --> "0..1" Store : memiliki
    User "1" --> "0..*" Order : membuat
    User "1" --> "0..*" CartItem : punya
    User "1" --> "0..*" Review : menulis
    Store "1" --> "0..*" Plant : memiliki
    Store "1" --> "0..*" Order : menerima
    Category "1" --> "0..*" Plant : mengkategorikan
    Plant "1" --> "0..*" CartItem : ada di
    Plant "1" --> "0..*" OrderItem : termasuk dalam
    Plant "1" --> "0..*" Review : mendapat
    Order "1" --> "1..*" OrderItem : terdiri dari
    Order "1" --> "0..*" Review : memiliki
```

---

## 10. System Architecture Diagram

```mermaid
flowchart TD
    subgraph CLIENT["🖥️ Client Layer"]
        Browser["Browser\n(Chrome / Firefox / Edge)"]
    end

    subgraph VERCEL["☁️ Vercel (Hosting)"]
        CDN["Vercel Edge Network\n(CDN Global)"]
        subgraph Serverless["Serverless Function (sin1 - Singapore)"]
            Entry["api/index.php\n(Entry Point)"]
            Laravel["Laravel 12 App\n(PHP 8.2)"]
            subgraph Laravel_Internal["Laravel Internals"]
                Router["Router\n(routes/web.php)"]
                Middleware["Middleware\n(Auth, Role, Throttle)"]
                Controllers["Controllers\n(Auth, Cart, Order,\nSeller, Admin)"]
                Models["Eloquent Models\n(User, Plant, Order...)"]
                Views["Blade Views\n(Tailwind CSS)"]
            end
        end
        Static["Static Assets\n(public/build/\nCSS + JS via Vite)"]
    end

    subgraph SERVICES["🔌 External Services"]
        Supabase["Supabase\n(PostgreSQL)\nSingapore Region"]
        Brevo["Brevo\n(SMTP Email)\nOTP & Notifikasi"]
        WhatsApp["WhatsApp\n(wa.me link)\nKomunikasi Buyer-Seller"]
    end

    Browser -->|"HTTPS Request"| CDN
    CDN -->|"Route PHP"| Entry
    CDN -->|"Serve static"| Static
    Entry --> Laravel
    Laravel --> Router
    Router --> Middleware
    Middleware --> Controllers
    Controllers --> Models
    Models -->|"PDO SSL"| Supabase
    Controllers --> Views
    Views -->|"HTML Response"| Browser
    Controllers -->|"Mail::send()"| Brevo
    Brevo -->|"Email OTP"| Browser
    Views -->|"wa.me link"| WhatsApp
```

---

## 11. Deployment Diagram

```mermaid
flowchart LR
    subgraph DEV["💻 Development (Lokal)"]
        Git["Git\n(branch: main)"]
        PHP["PHP 8.2\nArtisan Serve :8123"]
        PostgreSQL_Local["PostgreSQL\n(via .env)"]
    end

    subgraph GITHUB["🐙 GitHub"]
        Repo["MahardikaPutraa/Tanamku\nbranch: main"]
    end

    subgraph VERCEL["☁️ Vercel Platform"]
        Build["Build Pipeline\n1. npm install\n2. vite build\n3. copy-storage.cjs"]
        Runtime["PHP Serverless\nvercel-php@0.7.3\nRegion: sin1"]
        EnvVars["Environment Variables\nAPP_KEY, DB_*, MAIL_*"]
    end

    subgraph SUPABASE["🗄️ Supabase (Singapore)"]
        PG["PostgreSQL\naws-0-ap-southeast-1"]
    end

    subgraph BREVO["📧 Brevo"]
        SMTP["SMTP Server\nPort 587 TLS"]
    end

    Git -->|"git push"| Repo
    Repo -->|"Auto Deploy Webhook"| Build
    Build --> Runtime
    EnvVars --> Runtime
    Runtime -->|"SSL Connection"| PG
    Runtime -->|"SMTP TLS"| SMTP
    PHP -->|"Dev only"| PostgreSQL_Local
```

---

## 12. Component Diagram

```mermaid
flowchart TD
    subgraph UI["🎨 UI Components (Blade + Tailwind)"]
        NavBar["layouts/app.blade.php\n(Navbar + Cart Badge)"]
        HomeView["home.blade.php"]
        CatalogView["plants/index.blade.php"]
        DetailView["plants/show.blade.php"]
        CartView["cart/index.blade.php"]
        CheckoutView["checkout/create.blade.php"]
        OrderView["orders/ (mine + show)"]
        SellerUI["seller/* views\n(Dashboard, Produk, Pesanan)"]
        AdminUI["admin/* views\n(Dashboard, Users, Stores...)"]
        AuthViews["auth/* views\n(login, register, forgot, verify)"]
    end

    subgraph CONTROLLER["⚙️ Controllers"]
        AuthCtrl["AuthController\n(login, register, forgot, OTP)"]
        HomeCtrl["HomeController"]
        PlantCtrl["PlantController (publik)"]
        CartCtrl["CartController"]
        CheckoutCtrl["CheckoutController"]
        OrderCtrl["OrderController"]
        ReviewCtrl["ReviewController"]
        SellerCtrl["Seller/*Controller"]
        AdminCtrl["Admin/*Controller"]
    end

    subgraph MODEL["🗃️ Models (Eloquent)"]
        UserM["User"]
        StoreM["Store"]
        PlantM["Plant"]
        OrderM["Order"]
        OrderItemM["OrderItem"]
        CartItemM["CartItem"]
        CategoryM["Category"]
        ReviewM["Review"]
    end

    subgraph MIDDLEWARE["🔐 Middleware"]
        AuthMW["auth\n(Laravel built-in)"]
        RoleMW["EnsureRole\n(role:seller, role:admin)"]
        GuestMW["guest"]
        ThrottleMW["throttle:10,1"]
    end

    subgraph INFRA["🏗️ Infrastructure"]
        DB["Supabase PostgreSQL"]
        Cache["File Cache"]
        Session["File Session"]
        MailSvc["Brevo SMTP"]
        Storage["Storage\n(/tmp + public/storage)"]
    end

    UI --> CONTROLLER
    CONTROLLER --> MIDDLEWARE
    CONTROLLER --> MODEL
    MODEL --> DB
    AuthCtrl --> MailSvc
    PlantM --> Storage
    StoreM --> Storage
    CONTROLLER --> Cache
    AuthCtrl --> Session
```

---

## 13. State Diagram — Status Pesanan (Order)

```mermaid
stateDiagram-v2
    [*] --> baru : Buyer checkout berhasil

    baru --> diproses : Seller update status
    baru --> batal : Seller/Admin batalkan

    diproses --> selesai : Buyer tandai selesai
    diproses --> batal : Seller/Admin batalkan

    selesai --> [*] : Order final
    batal --> [*] : Order dibatalkan

    note right of baru
        Notifikasi ke seller
        via WhatsApp link
    end note

    note right of selesai
        Buyer dapat beri
        Ulasan & Rating
    end note
```

---

## 14. State Diagram — Status Toko (Store)

```mermaid
stateDiagram-v2
    [*] --> menunggu : Seller daftar akun

    menunggu --> aktif : Admin setujui
    menunggu --> nonaktif : Admin tolak

    aktif --> nonaktif : Admin nonaktifkan
    nonaktif --> aktif : Admin aktifkan kembali

    aktif --> [*] : Produk tampil di katalog
    nonaktif --> [*] : Produk tersembunyi dari katalog
```

---

## 15. Data Flow Diagram (DFD) — Level 0 (Context Diagram)

```mermaid
flowchart LR
    Buyer(["🛒 Buyer"])
    Seller(["🏪 Seller"])
    Admin(["🛡️ Admin"])
    System(["⚙️ Sistem\nTanamKu"])
    Brevo(["📧 Brevo\nEmail"])
    WA(["💬 WhatsApp"])

    Buyer -->|"Daftar, Login, Browse,\nKeranjang, Checkout, Ulasan"| System
    System -->|"Katalog, Detail Produk,\nStatus Pesanan, OTP Email"| Buyer
    Seller -->|"Kelola Toko, Produk,\nUpdate Status Pesanan"| System
    System -->|"Data Pesanan Masuk,\nStatistik Dashboard"| Seller
    Admin -->|"Verifikasi Toko,\nKelola User & Kategori"| System
    System -->|"Laporan Platform,\nData Pengguna"| Admin
    System -->|"Kirim OTP\nVerifikasi & Reset PW"| Brevo
    Brevo -->|"Email ke User"| Buyer
    System -->|"Link wa.me"| WA
    WA -->|"Chat Buyer-Seller"| Seller
```

---

## 16. Data Flow Diagram (DFD) — Level 1

```mermaid
flowchart TD
    subgraph EXT["Entitas Eksternal"]
        B(["Buyer"])
        S(["Seller"])
        A(["Admin"])
        Email(["Brevo SMTP"])
    end

    subgraph PROC["Proses"]
        P1["1.0\nAutentikasi\n& Registrasi"]
        P2["2.0\nKatalog &\nPencarian"]
        P3["3.0\nKeranjang\nBelanja"]
        P4["4.0\nCheckout &\nPemesanan"]
        P5["5.0\nKelola Toko\n& Produk"]
        P6["6.0\nManajemen\nPlatform (Admin)"]
        P7["7.0\nUlasan &\nRating"]
    end

    subgraph STORE["Data Store"]
        DS1[("users")]
        DS2[("stores")]
        DS3[("plants")]
        DS4[("orders\n+ order_items")]
        DS5[("cart_items")]
        DS6[("reviews")]
        DS7[("categories")]
        DS8[("password_reset_tokens")]
    end

    B -->|"Kredensial"| P1
    S -->|"Kredensial"| P1
    A -->|"Kredensial"| P1
    P1 <-->|"R/W"| DS1
    P1 <-->|"R/W"| DS8
    P1 -->|"OTP Email"| Email
    Email -->|"Konfirmasi"| B

    B -->|"Query / Filter"| P2
    P2 -->|"Baca"| DS3
    P2 -->|"Baca"| DS2
    P2 -->|"Baca"| DS7
    P2 -->|"Hasil Pencarian"| B

    B -->|"Tambah/Edit/Hapus item"| P3
    P3 <-->|"R/W"| DS5
    P3 -->|"Data Keranjang"| B

    B -->|"Data Checkout"| P4
    P4 -->|"Baca"| DS5
    P4 <-->|"R/W"| DS4
    P4 -->|"Kurangi Stok"| DS3
    P4 -->|"Konfirmasi Pesanan"| B

    S -->|"Data Toko & Produk"| P5
    P5 <-->|"R/W"| DS2
    P5 <-->|"R/W"| DS3
    P5 -->|"Dashboard Seller"| S

    A -->|"Aksi Admin"| P6
    P6 <-->|"R/W"| DS1
    P6 <-->|"R/W"| DS2
    P6 <-->|"R/W"| DS3
    P6 <-->|"R/W"| DS7
    P6 -->|"Laporan"| A

    B -->|"Rating & Komentar"| P7
    P7 -->|"Baca"| DS4
    P7 <-->|"R/W"| DS6
    P7 -->|"Ulasan tersimpan"| B
```

---

## 17. Flowchart — Alur Kelola Produk (Seller)

```mermaid
flowchart TD
    Start([🟢 Login sebagai Seller]) --> A["Buka Dashboard Seller"]
    A --> B["Menu: Produk Saya"]
    B --> C{"Tindakan?"}

    C -->|"Tambah"| D["Form Tambah Produk\n(nama, kategori, harga,\nstok, foto, deskripsi)"]
    D --> E{"Validasi Input"}
    E -- Gagal --> D
    E -- Lolos --> F{"Status Toko\nAktif?"}
    F -- Tidak --> G["Produk Tersimpan\n(tidak tampil di katalog)"]
    F -- Ya --> H["Produk Tersimpan\n& Tampil di Katalog"]

    C -->|"Edit"| I["Form Edit Produk"]
    I --> J{"Validasi Input"}
    J -- Gagal --> I
    J -- Lolos --> K["Produk Diperbarui"]

    C -->|"Hapus"| L["Konfirmasi Hapus"]
    L -->|"Ya"| M["Produk Dihapus\n(soft/hard delete)"]
    L -->|"Tidak"| B

    G --> End([🔴 Selesai])
    H --> End
    K --> End
    M --> End
```

---

## 18. Wireframe / UI Flow

```mermaid
flowchart TD
    subgraph PUBLIC["🌐 Halaman Publik"]
        Home["🏠 Beranda\n- Hero Section\n- Statistik (produk, toko, kategori)\n- Kategori\n- Produk Featured"]
        Catalog["📋 Katalog\n- Filter Kategori\n- Search Bar\n- Grid Produk\n- Pagination"]
        PlantDetail["🌿 Detail Tanaman\n- Foto\n- Nama, Harga, Stok\n- Rating & Ulasan\n- Panduan Perawatan\n- Tombol Keranjang"]
        StoreProfile["🏪 Profil Toko\n- Info Toko\n- Produk Toko\n- Tombol Chat WA"]
    end

    subgraph AUTH_PAGES["🔐 Autentikasi"]
        Login["Masuk\n- Email + Password\n- Ingat saya\n- Lupa Password?"]
        Register["Daftar\n- Nama, Email\n- Password\n- Role (buyer/seller)"]
        VerifyEmail["Verifikasi Email\n- Input 6 digit OTP\n- Kirim Ulang"]
        ForgotPW["Lupa Password\n- Input Email"]
        OTPInput["Input Kode OTP\n- 6 Digit dari Email"]
        NewPW["Password Baru\n- Password + Konfirmasi"]
    end

    subgraph BUYER_PAGES["🛒 Halaman Pembeli"]
        Cart["Keranjang\n- List Item per Toko\n- Qty +/-\n- Hapus Item\n- Total\n- Tombol Checkout"]
        Checkout["Checkout\n- Nama Penerima\n- No HP\n- Alamat\n- Metode (ambil/kirim)\n- Catatan"]
        OrderList["Pesanan Saya\n- List Pesanan\n- Filter Status\n- Kode Pesanan"]
        OrderDetail["Detail Pesanan\n- Info Penerima\n- List Item\n- Total\n- Tombol Selesai\n- Link Chat WA"]
        ReviewForm["Form Ulasan\n- Rating bintang\n- Komentar"]
    end

    subgraph SELLER_PAGES["🏪 Panel Seller"]
        SellerDash["Dashboard\n- Total Produk\n- Pesanan Baru\n- Revenue\n- Stok Menipis"]
        SellerProducts["Produk Saya\n- Tabel Produk\n- Tambah/Edit/Hapus"]
        SellerOrders["Pesanan Masuk\n- Filter Status\n- Update Status\n- Export CSV"]
        SellerStore["Profil Toko\n- Edit Info Toko\n- Logo\n- Metode Serah"]
    end

    subgraph ADMIN_PAGES["🛡️ Panel Admin"]
        AdminDash["Dashboard\n- Total User\n- Total Toko\n- Total Pesanan\n- Revenue"]
        AdminStores["Kelola Toko\n- Setujui/Nonaktifkan"]
        AdminUsers["Kelola Pengguna\n- Ubah Role/Status"]
        AdminCats["Kelola Kategori\n- Tambah/Edit/Hapus"]
        AdminProducts["Moderasi Produk\n- Aktif/Nonaktif/Hapus"]
    end

    Home --> Catalog
    Home --> PlantDetail
    Catalog --> PlantDetail
    PlantDetail --> StoreProfile
    PlantDetail --> Cart
    Cart --> Checkout
    Checkout --> OrderDetail
    OrderDetail --> ReviewForm
    OrderList --> OrderDetail

    Login --> Home
    Register --> VerifyEmail
    VerifyEmail --> Home
    ForgotPW --> OTPInput
    OTPInput --> NewPW
    NewPW --> Login
```

---

## 19. Diagram Pengujian (Test Coverage Map)

```mermaid
flowchart TD
    subgraph TEST_AUTH["🔐 Auth Tests"]
        T1["✅ Guest dapat akses halaman login"]
        T2["✅ Login dengan kredensial valid"]
        T3["✅ Login gagal dengan password salah"]
        T4["✅ Registrasi buyer/seller berhasil"]
        T5["✅ Redirect ke verifikasi email setelah daftar"]
        T6["✅ Email OTP terkirim saat verifikasi"]
        T7["✅ Forgot password — kirim OTP email"]
        T8["✅ Reset password dengan OTP valid"]
        T9["✅ Logout menghapus session"]
    end

    subgraph TEST_MARKET["🛒 Marketplace Tests"]
        T10["✅ Katalog tampil produk aktif toko aktif"]
        T11["✅ Detail tanaman dapat diakses publik"]
        T12["✅ Buyer tambah item ke keranjang"]
        T13["✅ Buyer checkout — pesanan terbuat"]
        T14["✅ Stok berkurang setelah checkout"]
        T15["✅ Checkout gagal jika stok tidak cukup"]
        T16["✅ Buyer tandai pesanan selesai"]
        T17["✅ Buyer beri ulasan setelah selesai"]
    end

    subgraph TEST_SELLER["🏪 Seller Tests"]
        T18["✅ Seller CRUD produk (tambah/edit/hapus)"]
        T19["✅ Seller update status pesanan"]
        T20["✅ Seller export CSV pesanan"]
    end

    subgraph TEST_ADMIN["🛡️ Admin Tests"]
        T21["✅ Admin setujui / nonaktifkan toko"]
        T22["✅ Admin kelola kategori"]
        T23["✅ Admin kelola user (role & status)"]
    end

    subgraph TEST_SECURITY["🔒 Security Tests"]
        T24["✅ Guest tidak bisa akses checkout"]
        T25["✅ Seller tidak bisa akses panel admin"]
        T26["✅ Buyer tidak bisa akses panel seller"]
        T27["✅ CSRF token pada semua form POST"]
        T28["✅ Throttle login (max 10/menit)"]
        T29["✅ Race condition checkout (DB lock)"]
        T30["✅ Seller hanya edit produk milik sendiri"]
    end

    TEST_AUTH --> TEST_MARKET
    TEST_MARKET --> TEST_SELLER
    TEST_SELLER --> TEST_ADMIN
    TEST_ADMIN --> TEST_SECURITY
```

---

## Ringkasan Diagram

| No | Diagram | Tujuan | Prioritas |
|----|---------|--------|-----------|
| 1 | Use Case Diagram | Gambaran semua fitur per aktor | ⭐ UTS |
| 2-4 | Activity Diagram | Alur proses utama sistem | ⭐ UTS |
| 5-7 | Sequence Diagram | Interaksi antar komponen | ⭐ UTS |
| 8 | ERD | Struktur database | ⭐ UTS |
| 9 | Class Diagram | Relasi antar model/class | ⭐ UTS |
| 10 | System Architecture | Gambaran teknis sistem | ⭐ UTS |
| 11 | Deployment Diagram | Infrastruktur hosting | 🔧 Dev |
| 12 | Component Diagram | Komponen & dependensi | 🔧 Dev |
| 13-14 | State Diagram | Siklus hidup Order & Store | ⭐ UTS |
| 15-16 | DFD Level 0 & 1 | Aliran data sistem | ⭐ UTS |
| 17 | Flowchart | Alur logika spesifik | 🔧 Dev |
| 18 | Wireframe / UI Flow | Navigasi antar halaman | 🔧 Dev |
| 19 | Diagram Pengujian | Peta test coverage | 🔧 Dev |
