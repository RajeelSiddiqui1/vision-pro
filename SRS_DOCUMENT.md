# Software Requirements Specification (SRS)
## VisionPro LCD Refurbishing E-Commerce Website

---

## 📊 PROJECT SUMMARY AT A GLANCE

| **Project** | VisionPro LCD Refurbishing E-Commerce Website |  
|-------------|--------------------------------------------|
| **Client** | VisionPro LCD Refurbishing Inc. |  
| **Location** | Brampton, Ontario, Canada |  
| **Total Cost** | **$1,000 CAD** |  
| **Timeline** | **1 Month (4 Weeks)** |  
| **Total Hours** | **379 Hours** |  
| **Rate** | $5/hour (effective) |  

---

## 💰 COST & DURATION BREAKDOWN BY MODULE

| Module | Features Included | Hours | Cost (CAD) | Duration | Week |
|--------|-------------------|-------|------------|----------|------|
| **Public Website** | Homepage, Product Catalog, Product Detail, Blog, Static Pages (About, Contact, FAQ, Privacy, Terms, etc.) | 83 | $415 | 2.5 weeks | Week 1-2 |
| **User Management** | Registration, Login, Password Reset, User Dashboard, Profile Management | 33 | $165 | 1 week | Week 2 |
| **E-Commerce** | Shopping Cart, Checkout (Clover), Order Management, Tax Calculation (13% HST) | 47 | $235 | 1.5 weeks | Week 2-3 |
| **Admin Panel** | Dashboard, Product Management, Category Management, Order Management, Customer Management | 60 | $300 | 1.5 weeks | Week 2-3 |
| **Communication** | Email System (Order Confirmations, Notifications), Contact Form | 20 | $100 | 3 days | Week 3 |
| **Setup & Config** | Database Setup, Server Config, Email Config, Base Framework | 30 | $150 | 4 days | Week 1 |
| **Testing & QA** | Cross-browser, Mobile Responsive, Bug Fixes, Performance | 25 | $125 | 4 days | Week 4 |
| **Documentation** | SRS, Admin Guide, Deployment Guide, README | 15 | $75 | 2 days | Week 4 |
| **Deployment** | Production Deployment, Server Setup, SSL Config | 20 | $100 | 2 days | Week 4 |
| **Contingency (15%)** | Buffer for unexpected issues | 46 | $230 | Ongoing | All weeks |
| **TOTAL** | | **379** | **$1,000** | **1 Month** | |

---

## 📅 WEEKLY TIMELINE

| Week | Phase | Main Deliverables | Hours | Cost |
|------|-------|------------------|-------|------|
| **Week 1** | Setup & Foundation | Database schema, Auth system, Base layout, Homepage | 60 | $300 |
| **Week 2** | Core Development | Products, Cart, Checkout, Admin Panel | 80 | $400 |
| **Week 3** | User Features | Dashboard, Orders, Blog, Communications | 70 | $350 |
| **Week 4** | Polish & Launch | Testing, Bug fixes, Deployment, Documentation | 60 | $300 |
| **Subtotal** | | | **270** | **$1,350** |
| **Discount** | | | | **-$350** |
| **TOTAL** | | | | **$1,000** |

---

## 💳 PAYMENT SCHEDULE

| Milestone | Deliverables | Amount | Percentage | Due |
|-----------|--------------|--------|------------|-----|
| **Deposit** | Project Start | $200 | 20% | Before Week 1 |
| **Milestone 1** | Core Features Demo (End of Week 2) | $300 | 30% | End of Week 2 |
| **Final Payment** | Final Delivery (End of Week 4) | $500 | 50% | End of Week 4 |
| **TOTAL** | | **$1,000** | **100%** | |

---

## 📋 FEATURES CHECKLIST

### ✅ Public Website
- [x] Homepage with Hero, Featured Products, Categories, Testimonials
- [x] Product Catalog with Search, Filter, Sort
- [x] Product Detail with Gallery, Add to Cart
- [x] Blog System (Public + Admin Management)
- [x] Static Pages: About, Contact, FAQ, Privacy, Terms, Return Policy, Shipping, Services, Location

### ✅ User Management
- [x] User Registration & Verification
- [x] Secure Login with Session Management
- [x] Password Reset via Email
- [x] User Dashboard (Orders, Profile, Addresses)

### ✅ E-Commerce
- [x] Shopping Cart (Add, Update, Remove)
- [x] Checkout with Shipping Address
- [x] Payment Integration (Clover)
- [x] Tax Calculation (13% HST Ontario)
- [x] Order Confirmation Emails
- [x] Order History & Tracking

### ✅ Admin Panel
- [x] Dashboard with Sales Overview
- [x] Product Management (Add, Edit, Delete, Stock)
- [x] Category Management (Hierarchical)
- [x] Order Management (View, Update Status)
- [x] Customer Management
- [x] Blog Management

### ✅ Communication
- [x] Transactional Emails (Orders, Password Reset)
- [x] Contact Form with Auto-response
- [x] Newsletter Subscription

---

## 🎯 CLIENT PROVIDED RESOURCES

| Resource | Status | Notes |
|----------|--------|-------|
| **Domain** | ✅ Provided by client | visionprolcd.com |
| **Email Hosting** | ✅ Provided by client | info@visionprolcd.com |
| **Branding** | ✅ Provided by client | Logo, Colors, Guidelines |
| **Content** | ✅ Provided by client | Product data, Business info |

---

## 1. Project Overview

### 1.1 Purpose
VisionPro LCD Refurbishing Inc. is a wholesale mobile phone parts and refurbishing supplies business based in Brampton, Ontario. This e-commerce website serves as an online platform for B2B customers to browse, order, and manage wholesale mobile parts, tools, and refurbishing supplies.

### 1.2 Scope
The website is a full-featured e-commerce platform with:
- Public-facing product catalog and company information
- Customer registration and authentication system
- Shopping cart and checkout functionality
- Admin dashboard for inventory and order management
- Blog system for industry insights
- Email notification system

### 1.3 Client Provided Resources
| Resource | Details |
|----------|---------|
| **Domain** | Provided by client |
| **Email Hosting** | Provided by client (info@visionprolcd.com) |
| **Branding** | Client provided logo and brand guidelines |
| **Content** | Client provided product data and business info |

---

## 2. Technical Architecture

### 2.1 Technology Stack
| Component | Technology |
|-----------|------------|
| **Frontend** | PHP, HTML5, CSS3, JavaScript |
| **Styling** | Tailwind CSS + Custom CSS |
| **Backend** | PHP 8.x |
| **Database** | MySQL 8.x |
| **Server** | Apache (XAMPP) |
| **Session Management** | PHP Sessions |
| **Email** | SMTP via PHP mail() |

### 2.2 Database Schema
The system uses 10 database tables:
- `users` - Customer and admin accounts
- `categories` - Product categories (hierarchical support)
- `products` - Product inventory
- `product_images` - Multiple images per product
- `orders` - Order records
- `order_items` - Line items per order
- `addresses` - User shipping/billing addresses
- `cart` - Shopping cart (session-based)
- `blog_posts` - Blog content
- `newsletter_subscribers` - Email list

---

## 3. Feature Specifications

### 3.1 Public Website Features

#### F1: Homepage (index.php)
| Feature | Description | Est. Hours |
|---------|------------|-----------|
| Hero section with brand messaging | Animated hero banner with CTA buttons | 4 |
| Featured products carousel | Display hot-selling items | 3 |
| Category navigation | Grid of product categories | 3 |
| Latest arrivals section | New products grid | 2 |
| Business stats/achievements | Trust indicators (10+ years, 15k+ orders, etc.) | 2 |
| Testimonials section | Customer reviews | 2 |
| Newsletter subscription | Email capture form | 2 |
| **Subtotal** | | **18 hours** |

#### F2: Product Catalog (products.php)
| Feature | Description | Est. Hours |
|---------|------------|-----------|
| Product grid display | Responsive grid with images and pricing | 4 |
| Category filtering | Filter by product categories | 3 |
| Search functionality | Search by name/description | 3 |
| Price sorting | Sort by price (low/high) | 2 |
| Pagination/Infinite scroll | Load more products | 3 |
| **Subtotal** | | **15 hours** |

#### F3: Product Detail (product-detail.php)
| Feature | Description | Est. Hours |
|---------|------------|-----------|
| Product image gallery | Main image + thumbnails | 3 |
| Product info display | Name, price, SKU, stock | 2 |
| Add to cart | Quick add functionality | 2 |
| Related products | Cross-sell recommendations | 2 |
| **Subtotal** | | **9 hours** |

#### F4: Blog System (blog.php, blog-detail.php)
| Feature | Description | Est. Hours |
|---------|------------|-----------|
| Blog listing page | Article cards with excerpts | 4 |
| Single post view | Full article with images | 3 |
| Blog categories | Filter by topic | 2 |
| Admin blog management | Create/edit/delete posts | 6 |
| **Subtotal** | | **15 hours** |

#### F5: Static Pages
| Page | Description | Est. Hours |
|------|------------|-----------|
| About Us | Company story and team | 4 |
| Contact Us | Form with Google Maps integration | 5 |
| Privacy Policy | Legal compliance | 2 |
| Terms & Conditions | Legal compliance | 2 |
| FAQ | Common questions | 3 |
| Return Policy | Return guidelines | 2 |
| Shipping Info | Shipping details | 2 |
| Services | Services offered | 3 |
| Location | Store location | 3 |
| **Subtotal** | | **26 hours** |

---

### 3.2 User Management Features

#### F6: Authentication System (login.php, signup.php)
| Feature | Description | Est. Hours |
|---------|------------|-----------|
| User registration | Sign up with email verification | 6 |
| User login | Secure login with session management | 4 |
| Password reset | Email-based password recovery | 4 |
| Remember me | Persistent login | 2 |
| Session timeout | Security auto-logout | 2 |
| **Subtotal** | | **18 hours** |

#### F7: User Dashboard (dashboard.php)
| Feature | Description | Est. Hours |
|---------|------------|-----------|
| Order history | View past orders | 4 |
| Profile management | Update personal info | 3 |
| Address management | Add/edit shipping addresses | 5 |
| Order tracking | Track order status | 3 |
| **Subtotal** | | **15 hours** |

---

### 3.3 E-Commerce Features

#### F8: Shopping Cart (cart.php, cart_action.php)
| Feature | Description | Est. Hours |
|---------|------------|-----------|
| Cart display | Show items with quantities | 4 |
| Add to cart | Add products with quantity | 3 |
| Update quantity | Change item quantities | 3 |
| Remove items | Delete from cart | 2 |
| Cart persistence | Save between sessions | 3 |
| Price calculations | Subtotal, tax, totals | 3 |
| **Subtotal** | | **18 hours** |

#### F9: Checkout Process (checkout.php)
| Feature | Description | Est. Hours |
|---------|------------|-----------|
| Shipping address | Collect delivery info | 4 |
| Payment integration | Clover payment gateway | 6 |
| Order review | Final order summary | 3 |
| Order confirmation | Success page | 2 |
| Tax calculation | 13% HST Ontario | 2 |
| **Subtotal** | | **17 hours** |

#### F10: Order Management (user-facing)
| Feature | Description | Est. Hours |
|---------|------------|-----------|
| Order listing | List all orders | 3 |
| Order details | View single order | 3 |
| Order status updates | Real-time status | 2 |
| Invoice generation | PDF invoices | 4 |
| **Subtotal** | | **12 hours** |

---

### 3.4 Admin Panel Features

#### F11: Admin Dashboard (admin.php)
| Feature | Description | Est. Hours |
|---------|------------|-----------|
| Sales overview | Total revenue, orders | 4 |
| Recent orders | Latest 5 orders | 2 |
| Quick stats | Products, users count | 2 |
| **Subtotal** | | **8 hours** |

#### F12: Product Management (admin-products.php, admin-product-add.php, admin-product-edit.php)
| Feature | Description | Est. Hours |
|---------|------------|-----------|
| Product listing | List all products with filters | 4 |
| Add product | Create new product | 4 |
| Edit product | Modify existing product | 4 |
| Delete product | Remove product | 2 |
| Bulk import | Import via CSV | 6 |
| Stock management | Inventory tracking | 3 |
| **Subtotal** | | **23 hours** |

#### F13: Category Management (admin-categories.php, admin-category-add.php, admin-category-edit.php)
| Feature | Description | Est. Hours |
|---------|------------|-----------|
| Category listing | List all categories | 2 |
| Add category | Create category | 2 |
| Edit category | Modify category | 2 |
| Hierarchical categories | Subcategories | 4 |
| **Subtotal** | | **10 hours** |

#### F14: Order Management (admin-orders.php, admin-order-details.php)
| Feature | Description | Est. Hours |
|---------|------------|-----------|
| Order listing | All orders with filters | 4 |
| Order details | Full order info | 3 |
| Update status | Change order status | 3 |
| Cancel order | Order cancellation | 2 |
| **Subtotal** | | **12 hours** |

#### F15: Customer Management (admin-users.php)
| Feature | Description | Est. Hours |
|---------|------------|-----------|
| User listing | List all customers | 3 |
| User details | View customer profile | 2 |
| Manage access | Enable/disable users | 2 |
| **Subtotal** | | **7 hours** |

---

### 3.5 Communication Features

#### F16: Email System (includes/email_helper.php, config/email.php)
| Feature | Description | Est. Hours |
|---------|------------|-----------|
| Order confirmation | Email on order placement | 3 |
| Shipping notification | Email when shipped | 2 |
| Contact form email | Forward customer inquiries | 2 |
| Newsletter emails | Bulk email capability | 4 |
| Password reset emails | Security emails | 2 |
| **Subtotal** | | **13 hours** |

#### F17: Contact Form (contact.php)
| Feature | Description | Est. Hours |
|---------|------------|-----------|
| Contact form | Submit inquiries | 3 |
| Form validation | Client/server validation | 2 |
| Auto-response | Confirmation email | 2 |
| **Subtotal** | | **7 hours** |

---

## 4. Cost Breakdown by Feature

### 4.1 Summary Table

| Category | Features | Hours | Cost (CAD) | Percentage |
|----------|----------|-------|-------------|------------|
| **Public Website** | F1-F5 | 83 hours | $415.00 | 20.8% |
| **User Management** | F6-F7 | 33 hours | $165.00 | 8.3% |
| **E-Commerce** | F8-F10 | 47 hours | $235.00 | 11.8% |
| **Admin Panel** | F11-F15 | 60 hours | $300.00 | 15.0% |
| **Communication** | F16-F17 | 20 hours | $100.00 | 5.0% |
| **Setup & Configuration** | - | 30 hours | $150.00 | 7.5% |
| **Testing & QA** | - | 25 hours | $125.00 | 6.3% |
| **Documentation** | - | 15 hours | $75.00 | 3.8% |
| **Deployment** | - | 20 hours | $100.00 | 5.0% |
| **Contingency (15%)** | - | 46 hours | $230.00 | 11.5% |
| **TOTAL** | - | **379 hours** | **$1,000.00** | **100%** |

### 4.2 Detailed Cost Breakdown

#### Public Website - $415.00
| Feature | Hours | Rate ($5/hr) | Cost |
|---------|-------|--------------|------|
| Homepage | 18 | $5 | $90 |
| Product Catalog | 15 | $5 | $75 |
| Product Detail | 9 | $5 | $45 |
| Blog System | 15 | $5 | $75 |
| Static Pages | 26 | $5 | $130 |
| **Subtotal** | **83** | | **$415** |

#### User Management - $165.00
| Feature | Hours | Rate ($5/hr) | Cost |
|---------|-------|--------------|------|
| Authentication | 18 | $5 | $90 |
| User Dashboard | 15 | $5 | $75 |
| **Subtotal** | **33** | | **$165** |

#### E-Commerce - $235.00
| Feature | Hours | Rate ($5/hr) | Cost |
|---------|-------|--------------|------|
| Shopping Cart | 18 | $5 | $90 |
| Checkout | 17 | $5 | $85 |
| Order Management | 12 | $5 | $60 |
| **Subtotal** | **47** | | **$235** |

#### Admin Panel - $300.00
| Feature | Hours | Rate ($5/hr) | Cost |
|---------|-------|--------------|------|
| Admin Dashboard | 8 | $5 | $40 |
| Product Management | 23 | $5 | $115 |
| Category Management | 10 | $5 | $50 |
| Order Management | 12 | $5 | $60 |
| Customer Management | 7 | $5 | $35 |
| **Subtotal** | **60** | | **$300** |

#### Communication - $100.00
| Feature | Hours | Rate ($5/hr) | Cost |
|---------|-------|--------------|------|
| Email System | 13 | $5 | $65 |
| Contact Form | 7 | $5 | $35 |
| **Subtotal** | **20** | | **$100** |

---

## 5. Timeline & Milestones

### 5.1 Project Timeline (4 Weeks / 1 Month)

| Week | Phase | Deliverables | Hours |
|------|-------|--------------|-------|
| **Week 1** | Setup & Foundation | Database schema, auth system, base layout | 60 |
| **Week 2** | Core Features | Products, cart, checkout, admin panel | 80 |
| **Week 3** | User Features | Dashboard, orders, blog, communications | 70 |
| **Week 4** | Polish & Launch | Testing, bug fixes, deployment | 60 |
| **Total** | | | **270 hours** |

### 5.2 Weekly Breakdown

#### Week 1: Setup & Foundation (60 hours)
| Task | Hours | Day |
|------|-------|-----|
| Project setup, folder structure | 4 | Day 1 |
| Database design & creation | 8 | Day 1-2 |
| User authentication system | 16 | Day 2-4 |
| Base layout (header/footer) | 8 | Day 4-5 |
| Homepage development | 16 | Day 5-7 |
| Client review | 8 | Day 7 |

#### Week 2: Core Features (80 hours)
| Task | Hours | Day |
|------|-------|-----|
| Product catalog system | 20 | Day 1-3 |
| Shopping cart functionality | 16 | Day 3-4 |
| Checkout process | 20 | Day 4-6 |
| Admin dashboard | 12 | Day 6-7 |
| Client review | 12 | Day 7 |

#### Week 3: User Features (70 hours)
| Task | Hours | Day |
|------|-------|-----|
| User dashboard | 16 | Day 1-2 |
| Order management (user) | 12 | Day 2-3 |
| Blog system | 16 | Day 3-5 |
| Email notifications | 14 | Day 5-6 |
| Contact form | 8 | Day 6-7 |
| Client review | 4 | Day 7 |

#### Week 4: Polish & Launch (60 hours)
| Task | Hours | Day |
|------|-------|-----|
| Cross-browser testing | 12 | Day 1-2 |
| Mobile responsiveness | 8 | Day 2 |
| Bug fixes & refinements | 16 | Day 3-4 |
| Performance optimization | 8 | Day 4-5 |
| Deployment to production | 8 | Day 5-6 |
| Final client sign-off | 8 | Day 7 |

---

## 6. Payment Terms

### 6.1 Payment Schedule
| Milestone | Amount | Percentage |
|-----------|--------|------------|
| Project Start | $200.00 | 20% |
| Week 2 Review (Core features demo) | $300.00 | 30% |
| Week 4 Final Delivery | $500.00 | 50% |
| **Total** | **$1,000.00** | **100%** |

### 6.2 Included Services
- ✅ Domain configuration (client-provided)
- ✅ Email setup (client-provided)
- ✅ Database setup
- ✅ 30-day bug fix warranty
- ✅ Basic SEO optimization
- ✅ Mobile responsiveness
- ✅ Cross-browser compatibility

### 6.3 Not Included (Available as Add-ons)
| Service | Cost (CAD) |
|---------|------------|
| Additional static pages | $50/page |
| Advanced SEO | $200 |
| Payment gateway setup (additional) | $100 |
| Extra email templates | $50/template |
| Training session | $75/session |
| Extended warranty (6 months) | $150 |
| Speed optimization | $200 |

---

## 7. Deliverables Checklist

### 7.1 Code Deliverables
- [ ] Complete source code (PHP, HTML, CSS, JS)
- [ ] Database schema and seed data
- [ ] Configuration files
- [ ] README with setup instructions

### 7.2 Documentation
- [ ] This SRS document
- [ ] Database schema documentation
- [ ] API documentation (if applicable)
- [ ] Admin user guide
- [ ] Deployment guide

### 7.3 Testing
- [ ] Unit tests for core functions
- [ ] Integration tests
- [ ] User acceptance testing (UAT)
- [ ] Performance testing

### 7.4 Deployment
- [ ] Production-ready code
- [ ] Server configuration
- [ ] SSL certificate setup (client responsibility)
- [ ] Email configuration

---

## 8. Client Responsibilities

| Responsibility | Details |
|---------------|---------|
| **Domain** | Manage DNS settings, renewal |
| **Email** | Configure email accounts, SPF/DKIM |
| **Hosting** | Provide web hosting (XAMPP/LAMP recommended) |
| **Content** | Provide product data, images, text |
| **Approvals** | Review and approve milestones on time |
| **Feedback** | Provide clear feedback during reviews |

---

## 9. Support & Maintenance

### 9.1 Warranty Period
- **30 days** from final delivery
- Bug fixes included
- Minor adjustments included

### 9.2 Ongoing Support (Post-Warranty)
| Service | Cost |
|---------|------|
| Hourly rate | $25/hour |
| Monthly maintenance | $100/month |
| Priority support | $200/month |

---

## 10. Revision History

| Version | Date | Changes | Author |
|---------|------|---------|--------|
| 1.0 | 2026-02-09 | Initial SRS document | Developer |

---

**Total Project Cost: $1,000 CAD**  
**Estimated Timeline: 1 Month (4 Weeks)**  
**Total Hours: 379 hours (including contingency)**

---

*This document serves as the official agreement between the developer and VisionPro LCD Refurbishing Inc. Any changes to scope will require a change order and may affect the timeline and cost.*
