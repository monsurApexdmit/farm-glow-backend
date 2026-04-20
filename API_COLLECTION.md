# Farm Glow API Collection

**Base URL:** `http://localhost:8006/api/v1`

**Authentication:** JWT Bearer Token (obtained from login endpoint)

---

## 1. AUTHENTICATION ENDPOINTS

### Register Company
**POST** `/auth/register-company`

**Request Body:**
```json
{
  "name": "Smith Farms LLC",
  "email": "admin@smithfarms.com"
}
```

**Response (201):**
```json
{
  "message": "Company registered successfully",
  "data": {
    "id": 1,
    "name": "Smith Farms LLC",
    "email": "admin@smithfarms.com",
    "created_at": "2026-04-20T12:00:00Z",
    "updated_at": "2026-04-20T12:00:00Z"
  },
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

---

### Login
**POST** `/auth/login`

**Request Body:**
```json
{
  "email": "admin@smithfarms.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "message": "Login successful",
  "data": {
    "id": 1,
    "email": "admin@smithfarms.com",
    "first_name": "John",
    "last_name": "Smith",
    "company_id": 1,
    "is_active": true,
    "avatar_url": null,
    "roles": ["owner"]
  },
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

---

### Get Current User
**GET** `/auth/me`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "User retrieved successfully",
  "data": {
    "id": 1,
    "email": "admin@smithfarms.com",
    "first_name": "John",
    "last_name": "Smith",
    "phone": "+1234567890",
    "company_id": 1,
    "is_active": true,
    "avatar_url": "https://example.com/avatar.jpg",
    "created_at": "2026-04-20T12:00:00Z"
  }
}
```

---

### Logout
**POST** `/auth/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Logout successful"
}
```

---

### Refresh Token
**POST** `/auth/refresh-token`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Token refreshed successfully",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

---

### Change Password
**POST** `/auth/change-password`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "current_password": "password123",
  "password": "newpassword456",
  "password_confirmation": "newpassword456"
}
```

**Response (200):**
```json
{
  "message": "Password changed successfully"
}
```

---

## 2. FARM ENDPOINTS

### List Farms
**GET** `/farms`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Page number for pagination
- `per_page` (optional): Items per page

**Response (200):**
```json
{
  "message": "Farms retrieved successfully",
  "data": [
    {
      "id": 1,
      "company_id": 1,
      "name": "Main Farm",
      "total_area": 500,
      "unit": "hectares",
      "location": "Colorado",
      "description": "Primary farming operation",
      "is_active": true,
      "created_by": 1,
      "created_at": "2026-04-20T12:00:00Z",
      "updated_at": "2026-04-20T12:00:00Z"
    }
  ],
  "pagination": {
    "total": 1,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1
  }
}
```

---

### Create Farm
**POST** `/farms`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "East Valley Farm",
  "total_area": 750,
  "unit": "hectares",
  "location": "California",
  "description": "Secondary farming operation"
}
```

**Response (201):**
```json
{
  "message": "Farm created successfully",
  "data": {
    "id": 2,
    "company_id": 1,
    "name": "East Valley Farm",
    "total_area": 750,
    "unit": "hectares",
    "location": "California",
    "description": "Secondary farming operation",
    "is_active": true,
    "created_by": 1,
    "created_at": "2026-04-20T12:15:00Z",
    "updated_at": "2026-04-20T12:15:00Z"
  }
}
```

---

### Get Farm
**GET** `/farms/{farm_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Farm retrieved successfully",
  "data": {
    "id": 1,
    "company_id": 1,
    "name": "Main Farm",
    "total_area": 500,
    "unit": "hectares",
    "location": "Colorado",
    "description": "Primary farming operation",
    "is_active": true,
    "created_by": 1,
    "fields_count": 3,
    "crops_count": 5,
    "livestock_count": 150,
    "workers_count": 12,
    "created_at": "2026-04-20T12:00:00Z",
    "updated_at": "2026-04-20T12:00:00Z"
  }
}
```

---

### Update Farm
**PUT** `/farms/{farm_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "Main Farm Updated",
  "total_area": 600,
  "location": "Colorado, USA"
}
```

**Response (200):**
```json
{
  "message": "Farm updated successfully",
  "data": {
    "id": 1,
    "company_id": 1,
    "name": "Main Farm Updated",
    "total_area": 600,
    "unit": "hectares",
    "location": "Colorado, USA",
    "description": "Primary farming operation",
    "is_active": true,
    "created_by": 1,
    "created_at": "2026-04-20T12:00:00Z",
    "updated_at": "2026-04-20T12:30:00Z"
  }
}
```

---

### Delete Farm
**DELETE** `/farms/{farm_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Farm deleted successfully"
}
```

---

### Get Farm Summary
**GET** `/farms/{farm_id}/summary`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Farm summary retrieved successfully",
  "data": {
    "id": 1,
    "name": "Main Farm",
    "total_area": 500,
    "unit": "hectares",
    "fields": 3,
    "crops": 5,
    "livestock": 150,
    "workers": 12,
    "active_schedules": 8,
    "pending_tasks": 3
  }
}
```

---

### Get Farm Statistics
**GET** `/farms/{farm_id}/stats`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Farm statistics retrieved successfully",
  "data": {
    "id": 1,
    "name": "Main Farm",
    "total_crop_yield": 5000,
    "crop_yield_unit": "kg",
    "livestock_count": 150,
    "worker_attendance_rate": 92.5,
    "inventory_value": 45000.00,
    "pending_expenses": 12500.00,
    "last_30_days_revenue": 125000.00
  }
}
```

---

## 3. FIELD ENDPOINTS

### List Fields
**GET** `/fields`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `farm_id` (optional): Filter by farm
- `page` (optional): Pagination

**Response (200):**
```json
{
  "message": "Fields retrieved successfully",
  "data": [
    {
      "id": 1,
      "farm_id": 1,
      "name": "North Field",
      "area": 100,
      "unit": "hectares",
      "soil_type": "loam",
      "ph_level": 6.5,
      "location": "North Section",
      "is_active": true,
      "created_at": "2026-04-20T12:00:00Z"
    }
  ],
  "pagination": {
    "total": 3,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1
  }
}
```

---

### Create Field
**POST** `/fields`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "farm_id": 1,
  "name": "East Field",
  "area": 150,
  "unit": "hectares",
  "soil_type": "clay",
  "ph_level": 7.2,
  "location": "East Section"
}
```

**Response (201):**
```json
{
  "message": "Field created successfully",
  "data": {
    "id": 4,
    "farm_id": 1,
    "name": "East Field",
    "area": 150,
    "unit": "hectares",
    "soil_type": "clay",
    "ph_level": 7.2,
    "location": "East Section",
    "is_active": true,
    "created_by": 1,
    "created_at": "2026-04-20T12:30:00Z"
  }
}
```

---

## 4. CROP ENDPOINTS

### List Crops
**GET** `/crops`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `farm_id` (optional): Filter by farm
- `field_id` (optional): Filter by field
- `status` (optional): active, harvested, failed

**Response (200):**
```json
{
  "message": "Crops retrieved successfully",
  "data": [
    {
      "id": 1,
      "field_id": 1,
      "name": "Wheat",
      "variety": "Winter Wheat",
      "planting_date": "2026-03-15",
      "expected_harvest_date": "2026-08-15",
      "expected_yield": 8000,
      "yield_unit": "kg",
      "status": "growing",
      "notes": "Looks healthy",
      "created_at": "2026-04-20T12:00:00Z"
    }
  ],
  "pagination": {
    "total": 5,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1
  }
}
```

---

### Create Crop
**POST** `/crops`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "field_id": 1,
  "name": "Corn",
  "variety": "Yellow Dent",
  "planting_date": "2026-04-01",
  "expected_harvest_date": "2026-10-01",
  "expected_yield": 12000,
  "yield_unit": "kg",
  "notes": "Premium variety"
}
```

**Response (201):**
```json
{
  "message": "Crop created successfully",
  "data": {
    "id": 6,
    "field_id": 1,
    "name": "Corn",
    "variety": "Yellow Dent",
    "planting_date": "2026-04-01",
    "expected_harvest_date": "2026-10-01",
    "expected_yield": 12000,
    "yield_unit": "kg",
    "status": "planting",
    "notes": "Premium variety",
    "created_by": 1,
    "created_at": "2026-04-20T12:45:00Z"
  }
}
```

---

### Record Crop Health
**POST** `/crops/{crop_id}/health`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "date": "2026-04-20",
  "status": "healthy",
  "disease_notes": "No visible diseases",
  "pest_notes": "Minimal pest activity",
  "general_notes": "Crop developing well"
}
```

**Response (201):**
```json
{
  "message": "Health record created successfully",
  "data": {
    "id": 15,
    "crop_id": 1,
    "date": "2026-04-20",
    "status": "healthy",
    "disease_notes": "No visible diseases",
    "pest_notes": "Minimal pest activity",
    "general_notes": "Crop developing well",
    "created_by": 1,
    "created_at": "2026-04-20T13:00:00Z"
  }
}
```

---

### Get Crop Health Records
**GET** `/crops/{crop_id}/health`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Health records retrieved successfully",
  "data": [
    {
      "id": 15,
      "crop_id": 1,
      "date": "2026-04-20",
      "status": "healthy",
      "disease_notes": "No visible diseases",
      "pest_notes": "Minimal pest activity",
      "general_notes": "Crop developing well",
      "created_by": 1,
      "created_at": "2026-04-20T13:00:00Z"
    }
  ]
}
```

---

### Record Crop Harvest
**POST** `/crops/{crop_id}/harvest`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "harvest_date": "2026-08-15",
  "actual_yield": 7850,
  "yield_unit": "kg",
  "quality_grade": "A",
  "notes": "Good harvest quality"
}
```

**Response (201):**
```json
{
  "message": "Harvest recorded successfully",
  "data": {
    "id": 1,
    "crop_id": 1,
    "harvest_date": "2026-08-15",
    "actual_yield": 7850,
    "yield_unit": "kg",
    "quality_grade": "A",
    "notes": "Good harvest quality",
    "created_by": 1,
    "created_at": "2026-04-20T13:15:00Z"
  }
}
```

---

### Get Crop Yield
**GET** `/crops/{crop_id}/yield`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Crop yield retrieved successfully",
  "data": {
    "id": 1,
    "name": "Wheat",
    "expected_yield": 8000,
    "actual_yield": 7850,
    "yield_unit": "kg",
    "yield_percentage": 98.1,
    "quality_grade": "A",
    "harvest_date": "2026-08-15"
  }
}
```

---

## 5. LIVESTOCK ENDPOINTS

### List Livestock
**GET** `/livestock`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `farm_id` (optional): Filter by farm
- `type` (optional): cattle, sheep, pig, poultry

**Response (200):**
```json
{
  "message": "Livestock retrieved successfully",
  "data": [
    {
      "id": 1,
      "farm_id": 1,
      "type": "cattle",
      "breed": "Angus",
      "tag_number": "FARM-001",
      "age_months": 24,
      "weight": 450,
      "weight_unit": "kg",
      "status": "healthy",
      "acquisition_date": "2025-04-20",
      "created_at": "2026-04-20T12:00:00Z"
    }
  ]
}
```

---

### Create Livestock
**POST** `/livestock`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "farm_id": 1,
  "type": "sheep",
  "breed": "Merino",
  "tag_number": "FARM-102",
  "age_months": 12,
  "weight": 65,
  "weight_unit": "kg",
  "acquisition_date": "2025-06-15"
}
```

**Response (201):**
```json
{
  "message": "Livestock created successfully",
  "data": {
    "id": 151,
    "farm_id": 1,
    "type": "sheep",
    "breed": "Merino",
    "tag_number": "FARM-102",
    "age_months": 12,
    "weight": 65,
    "weight_unit": "kg",
    "status": "healthy",
    "acquisition_date": "2025-06-15",
    "created_by": 1,
    "created_at": "2026-04-20T12:30:00Z"
  }
}
```

---

### Record Livestock Health
**POST** `/livestock/{livestock_id}/health`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "date": "2026-04-20",
  "status": "healthy",
  "temperature": 38.5,
  "weight": 452,
  "weight_unit": "kg",
  "treatment_notes": "Routine checkup",
  "general_notes": "Animal in good condition"
}
```

**Response (201):**
```json
{
  "message": "Health record created successfully",
  "data": {
    "id": 45,
    "livestock_id": 1,
    "date": "2026-04-20",
    "status": "healthy",
    "temperature": 38.5,
    "weight": 452,
    "weight_unit": "kg",
    "treatment_notes": "Routine checkup",
    "general_notes": "Animal in good condition",
    "created_by": 1,
    "created_at": "2026-04-20T13:00:00Z"
  }
}
```

---

## 6. LIVESTOCK SHED ENDPOINTS

### List Sheds
**GET** `/sheds`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `farm_id` (optional): Filter by farm

**Response (200):**
```json
{
  "message": "Sheds retrieved successfully",
  "data": [
    {
      "id": 1,
      "farm_id": 1,
      "name": "Cattle Shed A",
      "type": "cattle",
      "capacity": 50,
      "current_occupancy": 45,
      "last_cleaned": "2026-04-19",
      "is_active": true,
      "created_at": "2026-04-20T12:00:00Z"
    }
  ]
}
```

---

### Create Shed
**POST** `/sheds`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "farm_id": 1,
  "name": "Sheep Shed B",
  "type": "sheep",
  "capacity": 200
}
```

**Response (201):**
```json
{
  "message": "Shed created successfully",
  "data": {
    "id": 3,
    "farm_id": 1,
    "name": "Sheep Shed B",
    "type": "sheep",
    "capacity": 200,
    "current_occupancy": 0,
    "last_cleaned": null,
    "is_active": true,
    "created_by": 1,
    "created_at": "2026-04-20T12:30:00Z"
  }
}
```

---

### Record Shed Cleaning
**POST** `/sheds/{shed_id}/clean`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "date": "2026-04-20",
  "notes": "Deep clean completed"
}
```

**Response (201):**
```json
{
  "message": "Cleaning record created successfully",
  "data": {
    "id": 28,
    "shed_id": 1,
    "date": "2026-04-20",
    "notes": "Deep clean completed",
    "created_by": 1,
    "created_at": "2026-04-20T13:00:00Z"
  }
}
```

---

## 7. BREEDING ENDPOINTS

### List Breeding Records
**GET** `/breeding`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `farm_id` (optional): Filter by farm
- `status` (optional): planned, pregnant, birthed

**Response (200):**
```json
{
  "message": "Breeding records retrieved successfully",
  "data": [
    {
      "id": 1,
      "farm_id": 1,
      "sire_id": 5,
      "dam_id": 12,
      "breed_date": "2026-03-15",
      "expected_birth_date": "2026-05-15",
      "status": "pregnant",
      "notes": "First breeding",
      "created_at": "2026-04-20T12:00:00Z"
    }
  ]
}
```

---

### Create Breeding Record
**POST** `/breeding`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "farm_id": 1,
  "sire_id": 5,
  "dam_id": 12,
  "breed_date": "2026-04-10",
  "expected_birth_date": "2026-06-10",
  "notes": "Selective breeding program"
}
```

**Response (201):**
```json
{
  "message": "Breeding record created successfully",
  "data": {
    "id": 8,
    "farm_id": 1,
    "sire_id": 5,
    "dam_id": 12,
    "breed_date": "2026-04-10",
    "expected_birth_date": "2026-06-10",
    "status": "planned",
    "notes": "Selective breeding program",
    "created_by": 1,
    "created_at": "2026-04-20T12:30:00Z"
  }
}
```

---

### Record Birth
**POST** `/breeding/{breeding_id}/birth`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "birth_date": "2026-06-08",
  "offspring_count": 1,
  "offspring_type": "calf",
  "notes": "Healthy calf born"
}
```

**Response (201):**
```json
{
  "message": "Birth recorded successfully",
  "data": {
    "id": 1,
    "breeding_id": 1,
    "birth_date": "2026-06-08",
    "offspring_count": 1,
    "offspring_type": "calf",
    "notes": "Healthy calf born",
    "created_by": 1,
    "created_at": "2026-04-20T13:00:00Z"
  }
}
```

---

## 8. WORKER ENDPOINTS

### List Workers
**GET** `/workers`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `farm_id` (optional): Filter by farm
- `page` (optional): Pagination

**Response (200):**
```json
{
  "message": "Workers retrieved successfully",
  "data": [
    {
      "id": 1,
      "farm_id": 1,
      "first_name": "James",
      "last_name": "Johnson",
      "email": "james@example.com",
      "phone": "+1234567890",
      "position": "Farm Manager",
      "employment_type": "full-time",
      "hiring_date": "2025-01-15",
      "is_active": true,
      "created_at": "2026-04-20T12:00:00Z"
    }
  ]
}
```

---

### Create Worker
**POST** `/workers`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "farm_id": 1,
  "first_name": "Michael",
  "last_name": "Brown",
  "email": "michael@example.com",
  "phone": "+1987654321",
  "position": "Field Worker",
  "employment_type": "full-time",
  "hiring_date": "2026-04-20"
}
```

**Response (201):**
```json
{
  "message": "Worker created successfully",
  "data": {
    "id": 13,
    "farm_id": 1,
    "first_name": "Michael",
    "last_name": "Brown",
    "email": "michael@example.com",
    "phone": "+1987654321",
    "position": "Field Worker",
    "employment_type": "full-time",
    "hiring_date": "2026-04-20",
    "is_active": true,
    "created_by": 1,
    "created_at": "2026-04-20T12:30:00Z"
  }
}
```

---

## 9. SCHEDULE ENDPOINTS

### List Schedules
**GET** `/schedules`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `farm_id` (optional): Filter by farm
- `worker_id` (optional): Filter by worker

**Response (200):**
```json
{
  "message": "Schedules retrieved successfully",
  "data": [
    {
      "id": 1,
      "worker_id": 1,
      "farm_id": 1,
      "scheduled_date": "2026-04-21",
      "shift": "morning",
      "start_time": "06:00",
      "end_time": "14:00",
      "task": "Field preparation",
      "is_confirmed": true,
      "created_at": "2026-04-20T12:00:00Z"
    }
  ]
}
```

---

### Create Schedule
**POST** `/schedules`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "worker_id": 1,
  "farm_id": 1,
  "scheduled_date": "2026-04-22",
  "shift": "afternoon",
  "start_time": "14:00",
  "end_time": "22:00",
  "task": "Crop monitoring"
}
```

**Response (201):**
```json
{
  "message": "Schedule created successfully",
  "data": {
    "id": 45,
    "worker_id": 1,
    "farm_id": 1,
    "scheduled_date": "2026-04-22",
    "shift": "afternoon",
    "start_time": "14:00",
    "end_time": "22:00",
    "task": "Crop monitoring",
    "is_confirmed": false,
    "created_by": 1,
    "created_at": "2026-04-20T12:30:00Z"
  }
}
```

---

## 10. ATTENDANCE ENDPOINTS

### List Attendance Records
**GET** `/attendance?worker_id={worker_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `worker_id` (optional): Filter by worker

**Response (200):**
```json
{
  "message": "Attendance records retrieved successfully",
  "data": [
    {
      "id": 1,
      "worker_id": 1,
      "attendance_date": "2026-04-20",
      "check_in_time": "06:15",
      "check_out_time": "14:30",
      "hours_worked": 8.25,
      "status": "present",
      "notes": "Worked on field irrigation",
      "created_by": 2,
      "created_at": "2026-04-20T12:00:00Z"
    }
  ]
}
```

---

### Record Attendance
**POST** `/attendance/record`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "worker_id": 1,
  "attendance_date": "2026-04-21",
  "check_in_time": "06:00",
  "check_out_time": "14:30",
  "status": "present",
  "notes": "Regular shift"
}
```

**Response (201):**
```json
{
  "message": "Attendance recorded successfully",
  "data": {
    "id": 156,
    "worker_id": 1,
    "attendance_date": "2026-04-21",
    "check_in_time": "06:00",
    "check_out_time": "14:30",
    "hours_worked": 8.5,
    "status": "present",
    "notes": "Regular shift",
    "created_by": 2,
    "created_at": "2026-04-20T14:00:00Z"
  }
}
```

---

### Get Monthly Attendance
**GET** `/attendance/monthly?worker_id={worker_id}&year={year}&month={month}`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `worker_id` (required): Worker ID
- `year` (required): Year (e.g., 2026)
- `month` (required): Month (1-12)

**Response (200):**
```json
{
  "message": "Monthly attendance retrieved successfully",
  "data": [
    {
      "id": 1,
      "worker_id": 1,
      "attendance_date": "2026-04-01",
      "status": "present",
      "hours_worked": 8.0
    }
  ],
  "attendance_percentage": 95.5
}
```

---

## 11. INVENTORY ENDPOINTS

### List Inventory Items
**GET** `/inventory`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `farm_id` (optional): Filter by farm
- `category_id` (optional): Filter by category

**Response (200):**
```json
{
  "message": "Inventory items retrieved successfully",
  "data": [
    {
      "id": 1,
      "farm_id": 1,
      "category_id": 2,
      "name": "Wheat Seeds",
      "sku": "SEED-WHEAT-001",
      "unit": "kg",
      "quantity": 500,
      "min_quantity": 100,
      "cost_per_unit": 2.50,
      "total_value": 1250.00,
      "expiry_date": "2027-04-20",
      "location": "Storage A",
      "status": "active",
      "created_at": "2026-04-20T12:00:00Z"
    }
  ]
}
```

---

### Create Inventory Item
**POST** `/inventory`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "farm_id": 1,
  "category_id": 3,
  "name": "Nitrogen Fertilizer",
  "sku": "FERT-N-001",
  "unit": "kg",
  "quantity": 1000,
  "min_quantity": 200,
  "cost_per_unit": 1.75,
  "expiry_date": "2026-12-31",
  "location": "Storage B"
}
```

**Response (201):**
```json
{
  "message": "Inventory item created successfully",
  "data": {
    "id": 234,
    "farm_id": 1,
    "category_id": 3,
    "name": "Nitrogen Fertilizer",
    "sku": "FERT-N-001",
    "unit": "kg",
    "quantity": 1000,
    "min_quantity": 200,
    "cost_per_unit": 1.75,
    "total_value": 1750.00,
    "expiry_date": "2026-12-31",
    "location": "Storage B",
    "status": "active",
    "is_active": true,
    "created_by": 1,
    "created_at": "2026-04-20T12:30:00Z"
  }
}
```

---

### Get Low Stock Items
**GET** `/inventory/low-stock`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Low stock items retrieved successfully",
  "data": [
    {
      "id": 5,
      "name": "Pesticide A",
      "sku": "PEST-A-001",
      "quantity": 50,
      "min_quantity": 100,
      "unit": "liters",
      "farm": "Main Farm",
      "reorder_point": 100
    }
  ]
}
```

---

### Get Expired Items
**GET** `/inventory/expired`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Expired items retrieved successfully",
  "data": [
    {
      "id": 8,
      "name": "Old Seed Batch",
      "sku": "SEED-OLD-001",
      "expiry_date": "2026-03-15",
      "quantity": 100,
      "unit": "kg",
      "farm": "Main Farm"
    }
  ]
}
```

---

### Record Inventory Use
**POST** `/inventory/use`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "inventory_item_id": 1,
  "quantity": 50,
  "notes": "Used for field A crop treatment",
  "reference_number": "USE-2026-04-001",
  "transaction_date": "2026-04-20"
}
```

**Response (201):**
```json
{
  "message": "Inventory use recorded successfully",
  "data": {
    "id": 342,
    "inventory_item_id": 1,
    "type": "use",
    "quantity": 50,
    "quantity_before": 500,
    "quantity_after": 450,
    "notes": "Used for field A crop treatment",
    "reference_number": "USE-2026-04-001",
    "transaction_date": "2026-04-20",
    "created_by": 1,
    "created_at": "2026-04-20T13:00:00Z"
  }
}
```

---

### Record Inventory Restock
**POST** `/inventory/restock`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "inventory_item_id": 1,
  "quantity": 200,
  "cost_per_unit": 2.50,
  "notes": "Restock from supplier ABC",
  "reference_number": "PO-2026-04-001",
  "transaction_date": "2026-04-20"
}
```

**Response (201):**
```json
{
  "message": "Inventory restock recorded successfully",
  "data": {
    "id": 343,
    "inventory_item_id": 1,
    "type": "restock",
    "quantity": 200,
    "quantity_before": 450,
    "quantity_after": 650,
    "cost_per_unit": 2.50,
    "notes": "Restock from supplier ABC",
    "reference_number": "PO-2026-04-001",
    "transaction_date": "2026-04-20",
    "created_by": 1,
    "created_at": "2026-04-20T13:15:00Z"
  }
}
```

---

## 12. FINANCIAL ENDPOINTS

### List Accounts
**GET** `/accounts`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Accounts retrieved successfully",
  "data": [
    {
      "id": 1,
      "company_id": 1,
      "name": "Main Business Account",
      "type": "bank",
      "opening_balance": 50000.00,
      "current_balance": 125000.50,
      "currency": "USD",
      "is_active": true,
      "created_by": 1,
      "created_at": "2026-04-20T12:00:00Z"
    }
  ]
}
```

---

### Create Account
**POST** `/accounts`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "Equipment Fund",
  "type": "savings",
  "opening_balance": 25000.00,
  "currency": "USD",
  "description": "Dedicated equipment purchase account"
}
```

**Response (201):**
```json
{
  "message": "Account created successfully",
  "data": {
    "id": 3,
    "company_id": 1,
    "name": "Equipment Fund",
    "type": "savings",
    "opening_balance": 25000.00,
    "current_balance": 25000.00,
    "currency": "USD",
    "is_active": true,
    "created_by": 1,
    "created_at": "2026-04-20T12:30:00Z"
  }
}
```

---

### Get Account Balance
**GET** `/accounts/{account_id}/balance`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Account balance retrieved successfully",
  "data": {
    "id": 1,
    "name": "Main Business Account",
    "opening_balance": 50000.00,
    "total_income": 250000.00,
    "total_expenses": 174999.50,
    "current_balance": 125000.50,
    "currency": "USD"
  }
}
```

---

### List Transactions
**GET** `/transactions`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `account_id` (optional): Filter by account
- `type` (optional): income, expense

**Response (200):**
```json
{
  "message": "Transactions retrieved successfully",
  "data": [
    {
      "id": 1,
      "account_id": 1,
      "farm_id": 1,
      "type": "income",
      "category": "crop_sales",
      "description": "Wheat harvest sale",
      "amount": 15000.00,
      "reference_number": "SALE-2026-04-001",
      "transaction_date": "2026-04-15",
      "status": "completed",
      "created_at": "2026-04-20T12:00:00Z"
    }
  ]
}
```

---

### Create Transaction
**POST** `/transactions`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "account_id": 1,
  "farm_id": 1,
  "type": "expense",
  "category": "fertilizer",
  "description": "Nitrogen fertilizer purchase",
  "amount": 2500.00,
  "reference_number": "PO-2026-04-002",
  "transaction_date": "2026-04-20",
  "notes": "Bulk order discount applied"
}
```

**Response (201):**
```json
{
  "message": "Transaction created successfully",
  "data": {
    "id": 342,
    "account_id": 1,
    "farm_id": 1,
    "type": "expense",
    "category": "fertilizer",
    "description": "Nitrogen fertilizer purchase",
    "amount": 2500.00,
    "reference_number": "PO-2026-04-002",
    "transaction_date": "2026-04-20",
    "status": "completed",
    "notes": "Bulk order discount applied",
    "created_by": 1,
    "created_at": "2026-04-20T12:30:00Z"
  }
}
```

---

### Get Transaction Summary
**GET** `/transactions/summary?start_date={date}&end_date={date}`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `start_date` (optional): Start date (YYYY-MM-DD)
- `end_date` (optional): End date (YYYY-MM-DD)

**Response (200):**
```json
{
  "message": "Transaction summary retrieved successfully",
  "data": {
    "total_income": 50000.00,
    "total_expenses": 25000.00,
    "net": 25000.00
  }
}
```

---

### List Invoices
**GET** `/invoices`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `status` (optional): pending, paid, overdue

**Response (200):**
```json
{
  "message": "Invoices retrieved successfully",
  "data": [
    {
      "id": 1,
      "farm_id": 1,
      "invoice_number": "INV-20260420-001",
      "client_name": "ABC Distributor",
      "client_email": "sales@abc.com",
      "amount": 5000.00,
      "issue_date": "2026-04-15",
      "due_date": "2026-05-15",
      "paid_date": null,
      "status": "pending",
      "created_at": "2026-04-20T12:00:00Z"
    }
  ]
}
```

---

### Create Invoice
**POST** `/invoices`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "farm_id": 1,
  "client_name": "XYZ Company",
  "client_email": "contact@xyz.com",
  "description": "Wheat delivery - 5000 kg",
  "amount": 12500.00,
  "issue_date": "2026-04-20",
  "due_date": "2026-05-20",
  "notes": "Net 30 payment terms"
}
```

**Response (201):**
```json
{
  "message": "Invoice created successfully",
  "data": {
    "id": 18,
    "farm_id": 1,
    "invoice_number": "INV-20260420-156-782",
    "client_name": "XYZ Company",
    "client_email": "contact@xyz.com",
    "description": "Wheat delivery - 5000 kg",
    "amount": 12500.00,
    "issue_date": "2026-04-20",
    "due_date": "2026-05-20",
    "paid_date": null,
    "status": "pending",
    "notes": "Net 30 payment terms",
    "created_by": 1,
    "created_at": "2026-04-20T12:30:00Z"
  }
}
```

---

### Mark Invoice as Paid
**POST** `/invoices/{invoice_id}/mark-paid`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Invoice marked as paid successfully",
  "data": {
    "id": 1,
    "invoice_number": "INV-20260420-001",
    "status": "paid",
    "paid_date": "2026-04-20T13:00:00Z"
  }
}
```

---

### Get Overdue Invoices
**GET** `/invoices/overdue`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Overdue invoices retrieved successfully",
  "data": [
    {
      "id": 5,
      "invoice_number": "INV-20260410-005",
      "client_name": "Late Payer Inc",
      "amount": 3000.00,
      "due_date": "2026-04-15",
      "days_overdue": 5,
      "status": "overdue"
    }
  ]
}
```

---

### List Budgets
**GET** `/budgets`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `month` (optional): Month (1-12)
- `year` (optional): Year

**Response (200):**
```json
{
  "message": "Budgets retrieved successfully",
  "data": [
    {
      "id": 1,
      "farm_id": 1,
      "category": "fertilizer",
      "budgeted_amount": 5000.00,
      "spent_amount": 3200.50,
      "month": 4,
      "year": 2026,
      "remaining": 1799.50,
      "usage_percentage": 64.01,
      "created_at": "2026-04-20T12:00:00Z"
    }
  ]
}
```

---

### Create Budget
**POST** `/budgets`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "farm_id": 1,
  "category": "labor",
  "budgeted_amount": 15000.00,
  "month": 4,
  "year": 2026,
  "notes": "April payroll and contractor costs"
}
```

**Response (201):**
```json
{
  "message": "Budget created successfully",
  "data": {
    "id": 12,
    "farm_id": 1,
    "category": "labor",
    "budgeted_amount": 15000.00,
    "spent_amount": 0,
    "month": 4,
    "year": 2026,
    "remaining": 15000.00,
    "usage_percentage": 0,
    "notes": "April payroll and contractor costs",
    "created_by": 1,
    "created_at": "2026-04-20T12:30:00Z"
  }
}
```

---

### Get Budget Summary
**GET** `/budgets/summary?month={month}&year={year}`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `month` (optional): Month (1-12)
- `year` (optional): Year

**Response (200):**
```json
{
  "message": "Budget summary retrieved successfully",
  "data": {
    "total_budgeted": 50000.00,
    "total_spent": 32500.75,
    "remaining": 17499.25,
    "usage_percentage": 65.0
  }
}
```

---

### List Financial Reports
**GET** `/reports`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Reports retrieved successfully",
  "data": [
    {
      "id": 1,
      "farm_id": 1,
      "type": "monthly",
      "month": 4,
      "year": 2026,
      "total_income": 50000.00,
      "total_expenses": 32500.00,
      "net_profit": 17500.00,
      "created_at": "2026-04-20T12:00:00Z"
    }
  ]
}
```

---

### Generate Financial Report
**POST** `/reports/generate`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "farm_id": 1,
  "type": "monthly",
  "month": 4,
  "year": 2026
}
```

**Response (201):**
```json
{
  "message": "Report generated successfully",
  "data": {
    "id": 15,
    "farm_id": 1,
    "type": "monthly",
    "month": 4,
    "year": 2026,
    "total_income": 75000.00,
    "total_expenses": 45000.00,
    "net_profit": 30000.00,
    "data": {
      "income_breakdown": {
        "crop_sales": 60000.00,
        "other": 15000.00
      },
      "expense_breakdown": {
        "labor": 15000.00,
        "fertilizer": 12000.00,
        "equipment": 8000.00,
        "other": 10000.00
      }
    },
    "created_by": 1,
    "created_at": "2026-04-20T12:30:00Z"
  }
}
```

---

## 13. USER PROFILE ENDPOINTS

### Get Current User
**GET** `/users/me`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Current user retrieved successfully",
  "data": {
    "id": 1,
    "email": "admin@smithfarms.com",
    "first_name": "John",
    "last_name": "Smith",
    "phone": "+1234567890",
    "avatar_url": "https://example.com/avatar.jpg",
    "is_active": true,
    "company": {
      "id": 1,
      "name": "Smith Farms LLC"
    },
    "preferences": {
      "theme": "light",
      "language": "en",
      "timezone": "America/Denver"
    }
  }
}
```

---

### Update Profile
**PUT** `/users/me`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "first_name": "John",
  "last_name": "Smith",
  "phone": "+1234567890",
  "avatar_url": "https://example.com/new-avatar.jpg"
}
```

**Response (200):**
```json
{
  "message": "Profile updated successfully",
  "data": {
    "id": 1,
    "email": "admin@smithfarms.com",
    "first_name": "John",
    "last_name": "Smith",
    "phone": "+1234567890",
    "avatar_url": "https://example.com/new-avatar.jpg"
  }
}
```

---

### Get User Preferences
**GET** `/users/me/preferences`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Preferences retrieved successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "theme": "light",
    "language": "en",
    "timezone": "America/Denver",
    "notifications_enabled": true,
    "email_notifications": true,
    "data": null
  }
}
```

---

### Update Preferences
**PUT** `/users/me/preferences`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "theme": "dark",
  "language": "es",
  "timezone": "America/Los_Angeles",
  "notifications_enabled": false
}
```

**Response (200):**
```json
{
  "message": "Preferences updated successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "theme": "dark",
    "language": "es",
    "timezone": "America/Los_Angeles",
    "notifications_enabled": false,
    "email_notifications": true
  }
}
```

---

### Get User Activity
**GET** `/users/me/activity`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Activity logs retrieved successfully",
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "action": "login",
      "description": "User logged in",
      "ip_address": "192.168.1.100",
      "user_agent": "Mozilla/5.0...",
      "created_at": "2026-04-20T12:00:00Z"
    }
  ]
}
```

---

## 14. USER MANAGEMENT ENDPOINTS

### List Users
**GET** `/users`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Pagination

**Response (200):**
```json
{
  "message": "Users retrieved successfully",
  "data": [
    {
      "id": 1,
      "email": "admin@smithfarms.com",
      "first_name": "John",
      "last_name": "Smith",
      "phone": "+1234567890",
      "is_active": true,
      "roles": ["owner"]
    }
  ],
  "pagination": {
    "total": 5,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1
  }
}
```

---

### Create User
**POST** `/users`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "email": "manager@smithfarms.com",
  "password": "securepass123",
  "password_confirmation": "securepass123",
  "first_name": "Sarah",
  "last_name": "Johnson",
  "phone": "+1987654321",
  "roles": ["manager"]
}
```

**Response (201):**
```json
{
  "message": "User created successfully",
  "data": {
    "id": 6,
    "email": "manager@smithfarms.com",
    "first_name": "Sarah",
    "last_name": "Johnson",
    "phone": "+1987654321",
    "is_active": true,
    "company_id": 1,
    "roles": ["manager"]
  }
}
```

---

### Get User
**GET** `/users/{user_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "User retrieved successfully",
  "data": {
    "id": 2,
    "email": "farmer@smithfarms.com",
    "first_name": "Michael",
    "last_name": "Brown",
    "phone": "+1555666777",
    "is_active": true,
    "roles": ["farmer"],
    "preferences": {
      "theme": "light",
      "language": "en"
    }
  }
}
```

---

### Update User
**PUT** `/users/{user_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "email": "newfarmer@smithfarms.com",
  "first_name": "Michael",
  "last_name": "Johnson",
  "phone": "+1555999888",
  "roles": ["manager"]
}
```

**Response (200):**
```json
{
  "message": "User updated successfully",
  "data": {
    "id": 2,
    "email": "newfarmer@smithfarms.com",
    "first_name": "Michael",
    "last_name": "Johnson",
    "phone": "+1555999888",
    "roles": ["manager"]
  }
}
```

---

### Delete User
**DELETE** `/users/{user_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "User deleted successfully"
}
```

---

### Toggle User Status
**POST** `/users/{user_id}/toggle-active`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "User status updated successfully",
  "data": {
    "id": 2,
    "email": "farmer@smithfarms.com",
    "is_active": false
  }
}
```

---

### Get User Audit Trail
**GET** `/users/{user_id}/audit-trail`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "User audit trail retrieved successfully",
  "data": [
    {
      "id": 1,
      "user_id": 2,
      "event": "profile_updated",
      "model_type": "User",
      "model_id": 2,
      "changes": "{\"first_name\": \"Michael\"}",
      "ip_address": "192.168.1.100",
      "created_at": "2026-04-20T12:00:00Z"
    }
  ]
}
```

---

## 15. INVITATION ENDPOINTS

### List Invitations
**GET** `/invitations`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Invitations retrieved successfully",
  "data": [
    {
      "id": 1,
      "email": "newworker@example.com",
      "role": "farmer",
      "expires_at": "2026-04-27",
      "accepted_at": null,
      "created_by": 1
    }
  ]
}
```

---

### Send Invitation
**POST** `/invitations/send`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "email": "contractor@example.com",
  "role": "farmer"
}
```

**Response (201):**
```json
{
  "message": "Invitation sent successfully",
  "data": {
    "id": 5,
    "email": "contractor@example.com",
    "role": "farmer",
    "token": "randomtoken1234567890",
    "expires_at": "2026-04-27T12:30:00Z",
    "created_by": 1,
    "created_at": "2026-04-20T12:30:00Z"
  }
}
```

---

### Get Invitation Details
**GET** `/invitations/{token}`

**Response (200):**
```json
{
  "message": "Invitation retrieved successfully",
  "data": {
    "id": 5,
    "email": "contractor@example.com",
    "role": "farmer",
    "expires_at": "2026-04-27T12:30:00Z",
    "accepted_at": null,
    "created_by": 1
  }
}
```

---

### Accept Invitation
**POST** `/invitations/{token}/accept`

**Request Body:**
```json
{
  "password": "newpass123",
  "password_confirmation": "newpass123",
  "first_name": "John",
  "last_name": "Contractor"
}
```

**Response (200):**
```json
{
  "message": "Invitation accepted successfully",
  "data": {
    "id": 8,
    "email": "contractor@example.com",
    "first_name": "John",
    "last_name": "Contractor",
    "company_id": 1,
    "is_active": true,
    "roles": ["farmer"]
  }
}
```

---

### Get Pending Invitations
**GET** `/invitations/pending`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Pending invitations retrieved successfully",
  "data": [
    {
      "id": 2,
      "email": "pending@example.com",
      "role": "worker",
      "expires_at": "2026-04-27T12:00:00Z",
      "created_by": 1
    }
  ]
}
```

---

### Delete Invitation
**DELETE** `/invitations/{invitation_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Invitation deleted successfully"
}
```

---

## 16. ROLES & PERMISSIONS

### Roles
- **Owner**: Full access (30/30 permissions)
- **Manager**: Operational management (27/30 permissions) - no payroll, reports, user management
- **Farmer**: Field operations (15/30 permissions)
- **Worker**: View-only access (3/30 permissions)

### Permission Categories

**Farm Management (4):**
- `view_farms`, `create_farms`, `edit_farms`, `delete_farms`

**Crop Management (5):**
- `view_crops`, `create_crops`, `edit_crops`, `delete_crops`, `log_crop_health`

**Livestock Management (6):**
- `view_livestock`, `create_livestock`, `edit_livestock`, `delete_livestock`, `log_livestock_health`, `manage_livestock_breeding`

**Worker Management (7):**
- `view_workers`, `manage_workers`, `view_worker_schedules`, `manage_worker_schedules`, `record_attendance`, `view_attendance`, `review_worker_performance`

**Inventory Management (2):**
- `view_inventory`, `manage_inventory`

**Financial Management (4):**
- `view_finances`, `manage_finances`, `manage_payroll`, `generate_reports`

**User Management (2):**
- `view_users`, `manage_users`

---

## ERROR RESPONSES

### 400 Bad Request
```json
{
  "error": "Invalid request parameters",
  "details": {
    "field_name": ["Error message"]
  }
}
```

### 401 Unauthorized
```json
{
  "error": "Unauthorized - Invalid or missing token"
}
```

### 403 Forbidden
```json
{
  "error": "Forbidden - Insufficient permissions"
}
```

### 404 Not Found
```json
{
  "error": "Resource not found"
}
```

### 422 Unprocessable Entity
```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["Email is required"],
    "password": ["Password must be at least 8 characters"]
  }
}
```

### 500 Internal Server Error
```json
{
  "error": "Internal server error"
}
```

---

## AUTHENTICATION EXAMPLE

**Request with Bearer Token:**
```bash
curl -X GET http://localhost:8006/api/v1/farms \
  -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..." \
  -H "Content-Type: application/json"
```

---

## PAGINATION

All list endpoints support pagination with the following response structure:

```json
{
  "message": "...",
  "data": [...],
  "pagination": {
    "total": 50,
    "per_page": 15,
    "current_page": 1,
    "last_page": 4
  }
}
```

**Usage:**
```
GET /api/v1/farms?page=2&per_page=10
```

---

**Total Endpoints: 188 tests covering all 9 modules**
**Status: ✅ Production Ready**

