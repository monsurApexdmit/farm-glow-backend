# APIdog Collection Import Guide

## 📥 How to Import into APIdog

### Method 1: Direct Import (Recommended)
1. **Open APIdog**
2. Click on **"Import"** or **"New"** → **"Import"**
3. Select **"Import from file"**
4. Choose the file: `farm_glow_apidog.json`
5. Click **"Import"** → Collection will appear in your workspace

### Method 2: Copy-Paste
1. Open `farm_glow_apidog.json` in text editor
2. In APIdog: Click **"Import"** → **"Paste JSON"**
3. Paste the entire JSON content
4. Click **"Import"**

### Method 3: Download Link
If file is hosted:
1. Click **"Import"** → **"Import from URL"**
2. Paste the URL to `farm_glow_apidog.json`
3. Click **"Import"**

---

## 🔐 Setup Instructions After Import

### Step 1: Add Environment Variables
1. Click **"Environment"** in APIdog
2. Create new environment: `Farm Glow Development`
3. Add variables:
   ```
   token = your_jwt_token_here
   base_url = http://localhost:8006/api/v1
   ```

### Step 2: Get Initial Token
1. Run **Authentication** → **Login**
   ```json
   {
     "email": "admin@smithfarms.com",
     "password": "password123"
   }
   ```
2. Copy the `token` from response
3. Paste it in Environment: `token` variable

### Step 3: Update Base URL (if needed)
If your API is on different host/port:
- Edit Environment variable: `base_url`
- Example: `http://192.168.1.100:8006/api/v1`

---

## 📋 Collection Structure

```
Farm Glow API/
├── Authentication (5 endpoints)
├── Farms (7 endpoints)
├── Fields (3 endpoints)
├── Crops (6 endpoints)
├── Livestock (3 endpoints)
├── Sheds (3 endpoints)
├── Breeding (3 endpoints)
├── Workers (6 endpoints)
├── Schedules (2 endpoints)
├── Attendance (3 endpoints)
├── Inventory (7 endpoints)
├── Financial (14 endpoints)
├── User Profile (5 endpoints)
├── User Management (6 endpoints)
└── Invitations (6 endpoints)
```

**Total: 80+ Request Examples**

---

## 🚀 Quick Start Workflow

### 1. Authentication Flow
```
1. Register Company (or use existing)
   POST /auth/register-company
   
2. Login
   POST /auth/login
   
3. Copy token from response to environment
```

### 2. Create Farm
```
1. Create Farm
   POST /farms
   {
     "name": "My Farm",
     "total_area": 100,
     "unit": "hectares"
   }

2. Get Farm Summary
   GET /farms/1/summary
```

### 3. Add Resources
```
1. Create Field
   POST /fields
   
2. Create Crop
   POST /crops
   
3. Add Workers
   POST /workers
   
4. Record Attendance
   POST /attendance/record
```

### 4. Financial Management
```
1. Create Account
   POST /accounts
   
2. Record Transaction
   POST /transactions
   
3. Create Invoice
   POST /invoices
   
4. Generate Report
   POST /reports/generate
```

---

## 💡 Tips & Tricks

### Use Variables in Requests
Replace hardcoded IDs with variables:
```
Original: POST /farms/1
Better:   POST /farms/{{farm_id}}
```

### Chain Requests
1. Run POST request
2. Extract ID from response
3. Use in next request automatically

### Test Authentication
- Try any endpoint with invalid token to test 401 error
- Use token refresh endpoint to get new token

### Bulk Operations
Create multiple farms/workers:
- Copy request (Cmd/Ctrl + D)
- Modify JSON slightly
- Run multiple times

---

## 📊 Common Response Codes

| Code | Meaning |
|------|---------|
| 200 | Success (GET, PUT, DELETE) |
| 201 | Created (POST) |
| 400 | Bad Request (validation error) |
| 401 | Unauthorized (missing/invalid token) |
| 403 | Forbidden (insufficient permissions) |
| 404 | Not Found |
| 422 | Unprocessable Entity (validation) |
| 500 | Server Error |

---

## 🐛 Troubleshooting

### Import Fails
- ✅ Check JSON syntax is valid
- ✅ File path is correct
- ✅ File size is ~63KB

### Requests Get 401
- ✅ Token variable is set in environment
- ✅ Token is not expired (> 7 days old)
- ✅ Use `/auth/refresh-token` to get new token

### Requests Get 422 (Validation)
- ✅ Check required fields in request body
- ✅ Verify field formats (dates, numbers, enums)
- ✅ Look at error message for specific field

### Requests Get 403
- ✅ User role has required permissions
- ✅ Resource belongs to same company
- ✅ Check Roles & Permissions section in docs

---

## 📁 Files Included

| File | Purpose |
|------|---------|
| `farm_glow_apidog.json` | APIdog Collection (import this) |
| `API_COLLECTION.md` | Detailed endpoint documentation |
| `IMPORT_GUIDE.md` | This file |

---

## 🔗 API Documentation

For detailed endpoint information:
- See `API_COLLECTION.md` for all payloads/responses
- Each request in APIdog has comments with descriptions
- Check response examples in each request

---

## 📞 Support

For issues with:
- **APIdog Features**: Visit [APIdog Help](https://apidog.com)
- **Farm Glow API**: Check API logs at `storage/logs/laravel.log`
- **Collection Structure**: Refer to `API_COLLECTION.md`

---

**Last Updated:** 2026-04-20  
**Version:** 1.0.0  
**Status:** ✅ Production Ready

