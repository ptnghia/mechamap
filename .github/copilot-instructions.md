`````instructions
# 📘 Hướng Dẫn Copilot Tổng Hợp

## 🗣️ Ngôn Ngữ Phản Hồi

- Tất cả **comment, giải thích, thảo luận, và mô tả** đều phải viết bằng **tiếng Việt** rõ ràng, thân thiện, dễ hiểu.
- Luôn thêm comment bằng tiếng Việt giải thích mục đích và cách hoạt động của đoạn mã.
- Tránh dùng tiếng Anh trong phần mô tả, trừ khi là thuật ngữ kỹ thuật phổ biến hoặc tên framework/thư viện.

## 🧾 Cách Đặt Tên Trong Code

- **Tên biến, tên hàm, class** phải dùng tiếng **Anh**, theo đúng chuẩn Laravel và PHP.
  - Biến và hàm: camelCase (`userEmail`, `getLatestPosts`)
  - Class: PascalCase (`PostController`, `UserService`)
- Không dùng tiếng Việt cho tên biến hoặc hàm.

## 🎯 Phong Cách Lập Trình Laravel

- Tuân thủ chuẩn PSR-12 và conventions của Laravel.
- Sử dụng Eloquent ORM cho truy vấn dữ liệu.
- Validation phải dùng Form Request riêng.
- Business logic nên đặt trong Service layer, Controller chỉ nên điều phối luồng xử lý.
- Route phải định nghĩa rõ ràng, tránh closure trong route file.

## 📁 Cấu Trúc Thư Mục

- Controllers: `app/Http/Controllers`
- Models: `app/Models`
- Requests: `app/Http/Requests`
- Services: `app/Services`
- Blade templates: `resources/views`
- Routes: `routes/web.php`, `routes/api.php`

## 💡 UI/UX (nếu sinh mã HTML)

- Ưu tiên bootstrap CSS khi sinh HTML và nên sử dụng các thành phần của Bootstrap để đảm bảo tính nhất quán và dễ sử dụng.
- Toàn bộ nhãn, placeholder, nút, tiêu đề… phải bằng tiếng Việt.
- Tránh sử dụng các thành phần UI phức tạp nếu không cần thiết, ưu tiên sự đơn giản và dễ hiểu.

## ⚠️ Cách Xử Lý Lỗi

- Sử dụng try-catch để xử lý lỗi, và trả về thông báo lỗi rõ ràng cho người dùng.
- Không để lộ thông tin chi tiết về lỗi trong môi trường sản xuất, chỉ hiển thị thông báo chung chung.
- Sử dụng `Log::error()` để ghi lại lỗi trong file log, giúp dễ dàng theo dõi và sửa lỗi sau này.

## 🔄 Cách Xử Lý Dữ Liệu

- Luôn kiểm tra và validate dữ liệu đầu vào trước khi xử lý.
- Sử dụng các phương thức của Eloquent để truy vấn và thao tác với cơ sở dữ liệu.
- Tránh sử dụng query builder trực tiếp trừ khi cần thiết, ưu tiên Eloquent để tận dụng tính năng ORM của Laravel.

## 🤖 Cách Sinh Code

- Khi sinh code, **luôn đảm bảo rằng code có thể chạy được ngay** mà không cần chỉnh sửa thêm.
- Tránh sinh code quá phức tạp hoặc không cần thiết, ưu tiên sự đơn giản và dễ hiểu.
- Nếu có thể, hãy cung cấp các ví dụ cụ thể về cách sử dụng code đã sinh.

## ✅ Kiểm Tra Bắt Buộc Trước Khi Sinh Code

- Luôn đảm bảo rằng các route được sử dụng **đã được định nghĩa trong file routes/web.php hoặc api.php**.
- Trước khi gọi Model hoặc cột trong DB, **chỉ sinh code nếu bảng hoặc cột đã được xác định rõ ràng**.
- Tránh dùng tên bảng hoặc field "giả định" mà không rõ context.
- Nếu gọi đến hàm hoặc service, đảm bảo hàm đó **đã được định nghĩa**, hoặc gợi ý định nghĩa nó.
- Khi sử dụng biến có thể `null`, luôn kiểm tra bằng `isset()`, `optional()`, hoặc toán tử `??`.
- Không dùng các method, service, hoặc middleware chưa được tạo. Phải đảm bảo rằng chúng đã được định nghĩa trong codebase hoặc gợi ý cách tạo chúng.


# 🧠 Hướng Dẫn Cho Copilot

> ⚙️ Framework: Laravel (PHP)

---

## 📦 Quy Tắc Tạo Migration

Khi tạo **các migration cơ sở dữ liệu**, hãy tuân theo các quy tắc sau:

### 1. Sử dụng kiểu dữ liệu phù hợp
- Dùng các kiểu chính xác: `string`, `text`, `boolean`, `integer`, `timestamp`, v.v.
- Thêm `nullable()` nếu cột có thể để trống.
- Dùng `enum()` nếu dữ liệu là một danh sách cố định.

### 2. Ràng buộc khóa ngoại
- Luôn dùng `foreignId()` cho các quan hệ:
  ```php
  $table->foreignId('user_id')->constrained()->onDelete('cascade');
  ```
- Dùng `onDelete('cascade')` nếu muốn xóa dữ liệu con khi dữ liệu cha bị xóa.

### 3. Tạo chỉ mục (index)
- Thêm `index()` hoặc `unique()` cho các cột dùng để tìm kiếm hoặc định danh:
  ```php
  $table->index('slug');
  $table->unique('email');
  ```
- Cân nhắc tạo chỉ mục tổ hợp cho truy vấn nhiều cột.

---

## 🌱 Quy Tắc Tạo Dữ Liệu Seeder

Khi tạo **dữ liệu mẫu (seeders)**, cần đảm bảo dữ liệu chất lượng và thực tế:

### 1. Dữ liệu từ thế giới thực
- Lấy dữ liệu từ các nguồn đáng tin:
  - [Wikipedia](https://wikipedia.org)
  - [IMDb](https://imdb.com)
  - [SimpleMaps](https://simplemaps.com)
  - Open API (như Spotify, News, OpenWeather)

### 2. Nội dung liên quan đến chủ đề
- Dữ liệu phải phù hợp với mục đích dự án:
  - Nếu là forum kỹ thuật, dùng dữ liệu về kỹ thuật, cơ khí, CAD.
  - Tránh dữ liệu không liên quan hoặc quá chung chung.

```php

### 3. Hình ảnh và icon từ internet
- Điền các cột hình ảnh/icon/avatar bằng URL thực:
  - `https://i.pravatar.cc/150?img=23` (ảnh đại diện)
  - `https://source.unsplash.com/800x600/?technology,device` (sản phẩm, chủ đề)
  - `https://api.dicebear.com/` (biểu tượng SVG)

Ví dụ:
```php
'image' => 'https://source.unsplash.com/800x600/?music,concert',
'avatar' => 'https://i.pravatar.cc/150?img=' . rand(1, 70),
```

### 4. Nội dung phong phú và đa dạng
- Dùng `Faker::realText()` hoặc mô tả thực tế từ nguồn uy tín.
- Tránh dùng `Lorem ipsum`, nội dung phải có tính thực tiễn và đa dạng.

---

## ✅ Danh Sách Kiểm Tra Seeder & Migration

| Hạng mục kiểm tra                                 | Đã làm |
|---------------------------------------------------|--------|
| Dùng `foreignId()` với `->constrained()`          | ✔️     |
| Cột tìm kiếm hoặc định danh có index/unique       | ✔️     |
| Dữ liệu đúng chủ đề thực tế                       | ✔️     |
| Cột ảnh/avatar chứa link ảnh thật                 | ✔️     |
| Mô tả đa dạng, không trùng lặp                    | ✔️     |

---

## 📂 Quy Tắc Xử Lý File Lớn

Khi xử lý file lớn (CSV, log, JSON, văn bản), hãy tối ưu hiệu suất:

### 1. Đọc file theo dòng
- Dùng generator để đọc file văn bản lớn:

  ```php
  function readLargeFile($path) {
      $handle = fopen($path, 'r');
      while (!feof($handle)) {
          yield fgets($handle);
      }
      fclose($handle);
  }
  ```

- Đọc CSV:
  ```php
  $handle = fopen('large.csv', 'r');
  while (($row = fgetcsv($handle)) !== false) {
      // Xử lý từng dòng
  }
  fclose($handle);
  ```

### 2. Ghi file ở chế độ append
- Ghi log hoặc dữ liệu lớn bằng `'a'`:
  ```php
  $handle = fopen('output.log', 'a');
  fwrite($handle, "Dòng log
");
  fclose($handle);
  ```
### 3. Xử lý theo lô (batch)
- Không xử lý tất cả dòng cùng lúc:
  ```php
  $batch = [];
  $count = 0;
  foreach (readLargeFile('data.txt') as $line) {
      $batch[] = trim($line);
      if (++$count % 1000 === 0) {
          process($batch);
          $batch = [];
      }
  }
  if ($batch) process($batch);
  ```

### 4. Tránh load toàn bộ file vào RAM
- Không dùng `file_get_contents()` với file lớn.
- Không lưu tất cả dữ liệu vào mảng – hãy xử lý từng dòng hoặc từng lô.

### 5. xử lý khi gặp lỗi Timed out reading request body
- chia nhỏ file thành các phần nhỏ hơn, hoặc sử dụng chunking để xử lý từng phần một.

---

Copilot nên sinh mã có chất lượng sản phẩm, thực tế, chú trọng hiệu năng – nhất là khi làm việc với migration, dữ liệu mẫu, hoặc xử lý file lớn trong các thao tác backend.

---

# 🚀 Hướng Dẫn Next.js + TypeScript

> ⚙️ Framework: Next.js 15 với App Router + TypeScript

---

## 📂 Cấu Trúc Thư Mục Next.js

### Quy ước tổ chức file:
- **Components**: `src/components/` - Chia theo chức năng
  - `ui/` - Components tái sử dụng (Button, Input, Loading...)
  - `layout/` - Layout components (Header, Footer, Sidebar...)
  - `auth/` - Authentication components
  - `forms/` - Form components
- **Pages**: `src/app/` - App Router structure
- **Hooks**: `src/hooks/` - Custom React hooks
- **Utils**: `src/lib/` - Utility functions
- **Types**: `src/types/` - TypeScript type definitions
- **Services**: `src/services/` - API services
- **Contexts**: `src/contexts/` - React contexts

### Naming conventions:
```typescript
// Components: PascalCase
export default function UserProfile() { }

// Files: kebab-case hoặc PascalCase cho components
user-profile.tsx
UserProfile.tsx

// Hooks: camelCase với prefix "use"
useAuth.ts
useLocalStorage.ts

// Types: PascalCase
interface UserData { }
type ApiResponse<T> = { }
```

---

## 🎯 Quy Tắc TypeScript Strict

### 1. Type Safety - Luôn định nghĩa types rõ ràng
```typescript
// ✅ Good - Rõ ràng
interface Props {
  title: string;
  count: number;
  isVisible?: boolean;
}

// ❌ Bad - Mơ hồ
function handleData(data: any) { }

// ✅ Good - Sử dụng unknown thay vì any
function handleData(data: unknown) {
  if (typeof data === 'string') {
    // TypeScript biết data là string ở đây
  }
}
```

### 2. Props Interface cho Components
```typescript
// ✅ Luôn định nghĩa Props interface
interface ButtonProps {
  variant?: 'primary' | 'secondary' | 'danger';
  size?: 'sm' | 'md' | 'lg';
  loading?: boolean;
  disabled?: boolean;
  children: React.ReactNode;
  onClick?: () => void;
}

export function Button({ variant = 'primary', ...props }: ButtonProps) {
  // Implementation
}
```

### 3. API Response Types
```typescript
// ✅ Định nghĩa chính xác API responses
interface ApiResponse<T> {
  success: boolean;
  data: T;
  message: string;
  errors?: Record<string, string[]>;
}

interface LoginResponse {
  access_token: string;
  token_type: string;
  expires_in: number;
  user: User;
}

// ✅ Sử dụng trong service
async function loginUser(credentials: LoginRequest): Promise<LoginResponse> {
  const response = await api.post<LoginResponse>('/auth/login', credentials);
  return response.data;
}
```

---

## 🛠️ Patterns Thường Dùng

### 1. Form Handling với react-hook-form + zod
```typescript
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';

// ✅ Schema validation
const loginSchema = z.object({
  email: z.string().email('Email không hợp lệ'),
  password: z.string().min(8, 'Mật khẩu phải có ít nhất 8 ký tự'),
});

type LoginFormData = z.infer<typeof loginSchema>;

function LoginForm() {
  const { register, handleSubmit, formState: { errors } } = useForm<LoginFormData>({
    resolver: zodResolver(loginSchema),
  });

  const onSubmit = async (data: LoginFormData) => {
    // Handle form submission
  };
}
```

### 2. Custom Hooks Pattern
```typescript
// ✅ Custom hook với proper typing
interface UseApiOptions<T> {
  initialData?: T;
  onSuccess?: (data: T) => void;
  onError?: (error: Error) => void;
}

function useApi<T>(
  url: string, 
  options: UseApiOptions<T> = {}
) {
  const [data, setData] = useState<T | null>(options.initialData || null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);

  // Implementation
  return { data, loading, error, refetch };
}
```

### 3. Context Pattern
```typescript
// ✅ Type-safe context
interface AuthContextType {
  user: User | null;
  isLoading: boolean;
  login: (credentials: LoginRequest) => Promise<void>;
  logout: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

// ✅ Custom hook cho context
export function useAuth(): AuthContextType {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within AuthProvider');
  }
  return context;
}
```

---

## 🔗 API Integration Patterns

### 1. API Service Layer
```typescript
// services/api.ts - Base API client
class ApiClient {
  private baseURL: string;
  private token: string | null = null;

  constructor(baseURL: string) {
    this.baseURL = baseURL;
  }

  setToken(token: string) {
    this.token = token;
  }

  private async request<T>(
    endpoint: string,
    options: RequestInit = {}
  ): Promise<T> {
    const url = `${this.baseURL}${endpoint}`;
    
    const config: RequestInit = {
      headers: {
        'Content-Type': 'application/json',
        ...(this.token && { Authorization: `Bearer ${this.token}` }),
        ...options.headers,
      },
      ...options,
    };

    const response = await fetch(url, config);
    
    if (!response.ok) {
      throw new ApiError(response.status, await response.text());
    }

    return response.json();
  }

  async get<T>(endpoint: string): Promise<T> {
    return this.request<T>(endpoint, { method: 'GET' });
  }

  async post<T>(endpoint: string, data: unknown): Promise<T> {
    return this.request<T>(endpoint, {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }
}

// Singleton instance
export const apiClient = new ApiClient(process.env.NEXT_PUBLIC_API_URL!);
```

### 2. Resource-specific Services
```typescript
// services/auth.service.ts
export class AuthService {
  static async login(credentials: LoginRequest): Promise<AuthResponse> {
    const response = await apiClient.post<AuthResponse>('/auth/login', credentials);
    
    // Lưu token
    if (response.access_token) {
      localStorage.setItem('auth_token', response.access_token);
      apiClient.setToken(response.access_token);
    }
    
    return response;
  }

  static async getCurrentUser(): Promise<User | null> {
    try {
      return await apiClient.get<User>('/auth/me');
    } catch (error) {
      if (error instanceof ApiError && error.status === 401) {
        // Token hết hạn, xóa khỏi storage
        this.clearToken();
        return null;
      }
      throw error;
    }
  }

  static clearToken() {
    localStorage.removeItem('auth_token');
    apiClient.setToken('');
  }
}
```

### 3. Custom Error Class
```typescript
// lib/errors.ts
export class ApiError extends Error {
  constructor(
    public status: number,
    message: string,
    public response?: unknown
  ) {
    super(message);
    this.name = 'ApiError';
  }

  static isApiError(error: unknown): error is ApiError {
    return error instanceof ApiError;
  }
}

// Error handling hook
export function useApiError() {
  const { showError } = useToast();

  const handleError = useCallback((error: unknown) => {
    if (ApiError.isApiError(error)) {
      switch (error.status) {
        case 401:
          showError('Phiên đăng nhập đã hết hạn');
          // Redirect to login
          break;
        case 403:
          showError('Bạn không có quyền thực hiện hành động này');
          break;
        case 422:
          showError('Dữ liệu không hợp lệ');
          break;
        default:
          showError('Có lỗi xảy ra, vui lòng thử lại');
      }
    } else {
      showError('Có lỗi không xác định xảy ra');
    }
  }, [showError]);

  return { handleError };
}
```

---

## 📡 State Management Patterns

### 1. Server State với React Query
```typescript
// hooks/useUsers.ts
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';

export function useUsers() {
  return useQuery({
    queryKey: ['users'],
    queryFn: () => apiClient.get<User[]>('/users'),
    staleTime: 5 * 60 * 1000, // 5 phút
  });
}

export function useCreateUser() {
  const queryClient = useQueryClient();
  
  return useMutation({
    mutationFn: (userData: CreateUserRequest) =>
      apiClient.post<User>('/users', userData),
    onSuccess: () => {
      // Invalidate và refetch users list
      queryClient.invalidateQueries({ queryKey: ['users'] });
    },
  });
}

// Component usage
function UsersList() {
  const { data: users, isLoading, error } = useUsers();
  const createUser = useCreateUser();

  if (isLoading) return <Loading />;
  if (error) return <ErrorMessage error={error} />;

  return (
    <div>
      {users?.map(user => (
        <UserCard key={user.id} user={user} />
      ))}
    </div>
  );
}
```

### 2. Client State với Zustand
```typescript
// stores/useAppStore.ts
import { create } from 'zustand';
import { persist } from 'zustand/middleware';

interface AppState {
  // UI state
  sidebarOpen: boolean;
  theme: 'light' | 'dark';
  
  // User preferences
  preferences: UserPreferences;
  
  // Actions
  toggleSidebar: () => void;
  setTheme: (theme: 'light' | 'dark') => void;
  updatePreferences: (preferences: Partial<UserPreferences>) => void;
}

export const useAppStore = create<AppState>()(
  persist(
    (set) => ({
      sidebarOpen: false,
      theme: 'light',
      preferences: {
        language: 'vi',
        notifications: true,
      },
      
      toggleSidebar: () => set((state) => ({ 
        sidebarOpen: !state.sidebarOpen 
      })),
      
      setTheme: (theme) => set({ theme }),
      
      updatePreferences: (preferences) => set((state) => ({
        preferences: { ...state.preferences, ...preferences }
      })),
    }),
    {
      name: 'app-store', // localStorage key
      partialize: (state) => ({
        theme: state.theme,
        preferences: state.preferences,
      }),
    }
  )
);
```

---

## 🔐 Authentication Patterns

### 1. Auth Context với Persistence
```typescript
// contexts/AuthContext.tsx
interface AuthContextType {
  user: User | null;
  isLoading: boolean;
  isAuthenticated: boolean;
  login: (credentials: LoginRequest) => Promise<void>;
  logout: () => Promise<void>;
  refreshToken: () => Promise<void>;
}

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  // Initialize auth state
  useEffect(() => {
    const initAuth = async () => {
      const token = localStorage.getItem('auth_token');
      if (token) {
        apiClient.setToken(token);
        try {
          const currentUser = await AuthService.getCurrentUser();
          setUser(currentUser);
        } catch (error) {
          // Token invalid, clear it
          AuthService.clearToken();
        }
      }
      setIsLoading(false);
    };

    initAuth();
  }, []);

  // Auto refresh token before expiry
  useEffect(() => {
    if (!user) return;

    const interval = setInterval(async () => {
      try {
        await refreshToken();
      } catch (error) {
        // Refresh failed, logout user
        await logout();
      }
    }, 15 * 60 * 1000); // 15 phút

    return () => clearInterval(interval);
  }, [user]);

  const login = async (credentials: LoginRequest) => {
    const response = await AuthService.login(credentials);
    setUser(response.user);
  };

  const logout = async () => {
    try {
      await AuthService.logout();
    } finally {
      AuthService.clearToken();
      setUser(null);
    }
  };

  const refreshToken = async () => {
    const response = await AuthService.refreshToken();
    apiClient.setToken(response.access_token);
  };

  return (
    <AuthContext.Provider value={{
      user,
      isLoading,
      isAuthenticated: !!user,
      login,
      logout,
      refreshToken,
    }}>
      {children}
    </AuthContext.Provider>
  );
}
```

### 2. Protected Route Component
```typescript
// components/auth/ProtectedRoute.tsx
interface ProtectedRouteProps {
  children: React.ReactNode;
  requiredRole?: UserRole;
  fallback?: React.ComponentType;
}

export function ProtectedRoute({ 
  children, 
  requiredRole,
  fallback: Fallback = () => <div>Bạn không có quyền truy cập</div>
}: ProtectedRouteProps) {
  const { user, isLoading } = useAuth();
  const router = useRouter();

  useEffect(() => {
    if (!isLoading && !user) {
      router.push('/login');
    }
  }, [user, isLoading, router]);

  if (isLoading) {
    return <Loading />;
  }

  if (!user) {
    return null; // Will redirect in useEffect
  }

  if (requiredRole && !hasRole(user, requiredRole)) {
    return <Fallback />;
  }

  return <>{children}</>;
}

// Utility function
function hasRole(user: User, requiredRole: UserRole): boolean {
  const roleHierarchy: Record<UserRole, number> = {
    guest: 0,
    member: 1,
    senior: 2,
    moderator: 3,
    admin: 4,
  };

  return roleHierarchy[user.role] >= roleHierarchy[requiredRole];
}
```

---

## 🎯 Form Patterns Advanced

### 1. Multi-step Form
```typescript
// hooks/useMultiStepForm.ts
interface UseMultiStepFormProps<T> {
  steps: string[];
  initialData: T;
  onSubmit: (data: T) => Promise<void>;
}

export function useMultiStepForm<T>({ 
  steps, 
  initialData, 
  onSubmit 
}: UseMultiStepFormProps<T>) {
  const [currentStep, setCurrentStep] = useState(0);
  const [formData, setFormData] = useState<T>(initialData);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const isFirstStep = currentStep === 0;
  const isLastStep = currentStep === steps.length - 1;

  const goToNext = () => {
    if (!isLastStep) {
      setCurrentStep(prev => prev + 1);
    }
  };

  const goToPrevious = () => {
    if (!isFirstStep) {
      setCurrentStep(prev => prev - 1);
    }
  };

  const updateFormData = (data: Partial<T>) => {
    setFormData(prev => ({ ...prev, ...data }));
  };

  const submitForm = async () => {
    setIsSubmitting(true);
    try {
      await onSubmit(formData);
    } finally {
      setIsSubmitting(false);
    }
  };

  return {
    currentStep,
    currentStepName: steps[currentStep],
    formData,
    isFirstStep,
    isLastStep,
    isSubmitting,
    goToNext,
    goToPrevious,
    updateFormData,
    submitForm,
  };
}
```

### 2. Dynamic Form Fields
```typescript
// components/forms/DynamicForm.tsx
interface FormField {
  name: string;
  type: 'text' | 'email' | 'select' | 'textarea';
  label: string;
  required?: boolean;
  options?: Array<{ value: string; label: string }>;
  validation?: z.ZodSchema;
}

interface DynamicFormProps {
  fields: FormField[];
  onSubmit: (data: Record<string, unknown>) => void;
  defaultValues?: Record<string, unknown>;
}

export function DynamicForm({ 
  fields, 
  onSubmit, 
  defaultValues = {} 
}: DynamicFormProps) {
  // Generate schema dynamically
  const schema = useMemo(() => {
    const schemaObject: Record<string, z.ZodSchema> = {};
    
    fields.forEach(field => {
      if (field.validation) {
        schemaObject[field.name] = field.validation;
      } else {
        // Default validation based on type
        switch (field.type) {
          case 'email':
            schemaObject[field.name] = z.string().email();
            break;
          case 'text':
          case 'textarea':
          default:
            schemaObject[field.name] = field.required 
              ? z.string().min(1) 
              : z.string().optional();
        }
      }
    });

    return z.object(schemaObject);
  }, [fields]);

  const form = useForm({
    resolver: zodResolver(schema),
    defaultValues,
  });

  return (
    <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
      {fields.map(field => (
        <FormField
          key={field.name}
          field={field}
          register={form.register}
          error={form.formState.errors[field.name]}
        />
      ))}
      
      <Button type="submit" loading={form.formState.isSubmitting}>
        Gửi
      </Button>
    </form>
  );
}
```

---

## ✅ Checklist Trước Khi Commit

### Code Quality:
- [ ] Tất cả components có TypeScript interfaces
- [ ] Không có `any` types (dùng `unknown` thay thế)
- [ ] ESLint không báo lỗi
- [ ] Tất cả imports được sử dụng
- [ ] Console.log đã được xóa (trừ error logging)

### Performance:
- [ ] Images sử dụng Next.js Image component
- [ ] Heavy components được lazy load
- [ ] Không có memory leaks (useEffect cleanup)

### UX:
- [ ] Loading states cho async operations
- [ ] Error handling và error boundaries
- [ ] Responsive design hoạt động tốt
- [ ] Accessibility (alt text, keyboard navigation)

### Security:
- [ ] Environment variables được validate
- [ ] Không expose sensitive data client-side
- [ ] API inputs được validate với zod

---

Copilot nên sinh mã Next.js + TypeScript chất lượng cao, tuân thủ best practices, tối ưu performance và đảm bảo type safety hoàn toàn.

---

## 🐛 Debugging & Testing Patterns

### 1. Error Logging & Monitoring
```typescript
// lib/logger.ts
interface LogLevel {
  ERROR: 'error';
  WARN: 'warn';
  INFO: 'info';
  DEBUG: 'debug';
}

class Logger {
  private isDevelopment = process.env.NODE_ENV === 'development';

  error(message: string, error?: Error, context?: Record<string, unknown>) {
    const logData = {
      level: 'error',
      message,
      timestamp: new Date().toISOString(),
      error: error ? {
        name: error.name,
        message: error.message,
        stack: error.stack,
      } : undefined,
      context,
    };

    if (this.isDevelopment) {
      console.error('🔴', message, error, context);
    } else {
      // Send to monitoring service (Sentry, LogRocket, etc.)
      this.sendToMonitoring(logData);
    }
  }

  warn(message: string, context?: Record<string, unknown>) {
    if (this.isDevelopment) {
      console.warn('🟡', message, context);
    }
  }

  info(message: string, context?: Record<string, unknown>) {
    if (this.isDevelopment) {
      console.info('🔵', message, context);
    }
  }

  private sendToMonitoring(logData: unknown) {
    // Integration with monitoring service
    // Sentry.captureException(), LogRocket.captureException(), etc.
  }
}

export const logger = new Logger();

// Usage trong components
function UserProfile() {
  const { user, error } = useUser();

  useEffect(() => {
    if (error) {
      logger.error('Failed to load user profile', error, { 
        userId: user?.id,
        route: '/profile' 
      });
    }
  }, [error, user?.id]);
}
```

### 2. Development Tools
```typescript
// hooks/useDevTools.ts - Chỉ trong development
export function useDevTools() {
  const [isEnabled, setIsEnabled] = useState(false);

  useEffect(() => {
    // Enable dev tools với keyboard shortcut
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.ctrlKey && e.shiftKey && e.key === 'D') {
        setIsEnabled(prev => !prev);
      }
    };

    if (process.env.NODE_ENV === 'development') {
      window.addEventListener('keydown', handleKeyDown);
      return () => window.removeEventListener('keydown', handleKeyDown);
    }
  }, []);

  // Debug utilities
  const logProps = useCallback((componentName: string, props: Record<string, unknown>) => {
    if (isEnabled) {
      console.group(`🔧 ${componentName} Props`);
      console.table(props);
      console.groupEnd();
    }
  }, [isEnabled]);

  const logRender = useCallback((componentName: string) => {
    if (isEnabled) {
      console.log(`🔄 ${componentName} rendered at ${new Date().toISOString()}`);
    }
  }, [isEnabled]);

  return { isEnabled, logProps, logRender };
}

// Component debugging
function UserCard({ user }: { user: User }) {
  const { logProps, logRender } = useDevTools();

  logProps('UserCard', { user });
  logRender('UserCard');

  return <div>...</div>;
}
```

### 3. Testing Utilities
```typescript
// test-utils/render.tsx - Custom render với providers
import { render as rtlRender } from '@testing-library/react';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { AuthProvider } from '@/contexts/AuthContext';
import { ToastProvider } from '@/contexts/ToastContext';

interface RenderOptions {
  user?: User | null;
  queryClient?: QueryClient;
}

export function render(
  ui: React.ReactElement,
  options: RenderOptions = {}
) {
  const { user = null, queryClient = new QueryClient({
    defaultOptions: {
      queries: { retry: false },
      mutations: { retry: false },
    },
  }) } = options;

  function Wrapper({ children }: { children: React.ReactNode }) {
    return (
      <QueryClientProvider client={queryClient}>
        <AuthProvider initialUser={user}>
          <ToastProvider>
            {children}
          </ToastProvider>
        </AuthProvider>
      </QueryClientProvider>
    );
  }

  return rtlRender(ui, { wrapper: Wrapper });
}

// Test helpers
export const mockUser: User = {
  id: 1,
  name: 'Test User',
  email: 'test@example.com',
  role: 'member',
  // ... other properties
};

export function createMockApiResponse<T>(data: T): ApiResponse<T> {
  return {
    success: true,
    data,
    message: 'Success',
  };
}
```

### 4. Component Testing Patterns
```typescript
// __tests__/components/Button.test.tsx
import { render, screen, fireEvent } from '@testing-library/react';
import { Button } from '@/components/ui/Button';

describe('Button Component', () => {
  it('renders với default props', () => {
    render(<Button>Click me</Button>);
    
    const button = screen.getByRole('button');
    expect(button).toBeInTheDocument();
    expect(button).toHaveTextContent('Click me');
    expect(button).toHaveClass('bg-blue-600'); // primary variant
  });

  it('handles click events', () => {
    const handleClick = jest.fn();
    render(<Button onClick={handleClick}>Click me</Button>);
    
    fireEvent.click(screen.getByRole('button'));
    expect(handleClick).toHaveBeenCalledTimes(1);
  });

  it('shows loading state', () => {
    render(<Button loading>Saving...</Button>);
    
    const button = screen.getByRole('button');
    expect(button).toBeDisabled();
    expect(screen.getByTestId('loading-spinner')).toBeInTheDocument();
  });

  it('applies correct variant styles', () => {
    const { rerender } = render(<Button variant="secondary">Test</Button>);
    
    expect(screen.getByRole('button')).toHaveClass('bg-gray-200');
    
    rerender(<Button variant="danger">Test</Button>);
    expect(screen.getByRole('button')).toHaveClass('bg-red-600');
  });
});
```

### 5. Integration Testing
```typescript
// __tests__/pages/login.test.tsx
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import { rest } from 'msw';
import { setupServer } from 'msw/node';
import LoginPage from '@/app/(auth)/login/page';

// Mock API server
const server = setupServer(
  rest.post('/api/auth/login', (req, res, ctx) => {
    return res(
      ctx.json({
        success: true,
        data: {
          access_token: 'mock-token',
          user: mockUser,
        },
      })
    );
  })
);

beforeAll(() => server.listen());
afterEach(() => server.resetHandlers());
afterAll(() => server.close());

describe('Login Page', () => {
  it('successful login redirects to dashboard', async () => {
    const mockPush = jest.fn();
    jest.mock('next/navigation', () => ({
      useRouter: () => ({ push: mockPush }),
    }));

    render(<LoginPage />);

    // Fill form
    fireEvent.change(screen.getByLabelText(/email/i), {
      target: { value: 'test@example.com' },
    });
    fireEvent.change(screen.getByLabelText(/mật khẩu/i), {
      target: { value: 'password123' },
    });

    // Submit
    fireEvent.click(screen.getByRole('button', { name: /đăng nhập/i }));

    // Wait for redirect
    await waitFor(() => {
      expect(mockPush).toHaveBeenCalledWith('/dashboard');
    });
  });

  it('shows error on invalid credentials', async () => {
    server.use(
      rest.post('/api/auth/login', (req, res, ctx) => {
        return res(
          ctx.status(401),
          ctx.json({ message: 'Invalid credentials' })
        );
      })
    );

    render(<LoginPage />);

    // Submit with invalid data
    fireEvent.click(screen.getByRole('button', { name: /đăng nhập/i }));

    await waitFor(() => {
      expect(screen.getByText(/invalid credentials/i)).toBeInTheDocument();
    });
  });
});
```

---

## 🚀 Performance Monitoring

### 1. Performance Metrics
```typescript
// hooks/usePerformanceMonitor.ts
export function usePerformanceMonitor(componentName: string) {
  const renderStart = useRef<number>();
  const [renderTime, setRenderTime] = useState<number>();

  useLayoutEffect(() => {
    renderStart.current = performance.now();
  });

  useEffect(() => {
    if (renderStart.current) {
      const duration = performance.now() - renderStart.current;
      setRenderTime(duration);

      if (duration > 16) { // Longer than 1 frame
        logger.warn(`Slow render detected: ${componentName}`, {
          duration: `${duration.toFixed(2)}ms`,
        });
      }
    }
  });

  // Web Vitals monitoring
  useEffect(() => {
    if (typeof window !== 'undefined' && 'web-vital' in window) {
      // Report Core Web Vitals
      getCLS(console.log);
      getFID(console.log);
      getFCP(console.log);
      getLCP(console.log);
      getTTFB(console.log);
    }
  }, []);

  return { renderTime };
}
```

### 2. Bundle Analysis
```typescript
// next.config.ts
/** @type {import('next').NextConfig} */
const nextConfig = {
  // Bundle analyzer
  ...(process.env.ANALYZE === 'true' && {
    webpack: (config: any) => {
      config.plugins.push(
        new (require('@next/bundle-analyzer'))({
          enabled: true,
        })
      );
      return config;
    },
  }),

  // Performance optimizations
  experimental: {
    optimizePackageImports: ['lucide-react', '@heroicons/react'],
  },

  // Image optimization
  images: {
    domains: ['source.unsplash.com', 'i.pravatar.cc'],
    formats: ['image/webp', 'image/avif'],
  },
};

export default nextConfig;
```

---

Với những quy ước và patterns này, việc phát triển Next.js + TypeScript sẽ:

✅ **Nhất quán**: Tất cả code follow cùng một standard  
✅ **Type-safe**: Tận dụng TypeScript để catch lỗi sớm  
✅ **Performant**: Optimized cho production  
✅ **Maintainable**: Dễ debug, test và maintain  
✅ **Scalable**: Patterns có thể scale theo project  

Copilot sẽ sinh code chất lượng cao, tuân thủ best practices và tránh được những lỗi thường gặp trong Next.js development.
`````

