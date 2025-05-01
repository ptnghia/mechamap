# Hướng dẫn sử dụng API trong Frontend

Tài liệu này hướng dẫn cách sử dụng API để lấy dữ liệu SEO và Settings trong frontend Next.js.

## Cấu trúc API

Frontend sử dụng các API sau để lấy dữ liệu:

1. **API Settings**:

    - `GET /api/v1/settings` - Lấy tất cả cài đặt
    - `GET /api/v1/settings/{group}` - Lấy cài đặt theo nhóm
    - `GET /api/v1/favicon` - Lấy favicon URL

2. **API SEO**:
    - `GET /api/v1/seo` - Lấy tất cả cài đặt SEO
    - `GET /api/v1/seo/{group}` - Lấy cài đặt SEO theo nhóm
    - `GET /api/v1/page-seo/{routeName}` - Lấy cài đặt SEO theo route name
    - `GET /api/v1/page-seo/url/{urlPattern}` - Lấy cài đặt SEO theo URL pattern

## Services

Các service được định nghĩa trong file `src/services/seo.service.ts`:

```typescript
// Lấy cài đặt SEO theo nhóm
export async function getSeoSettings(
    group: string = "general"
): Promise<SeoData> {
    try {
        const response = await axios.get(`${API_URL}/seo/${group}`);
        return response.data.data;
    } catch (error) {
        console.error("Error fetching SEO settings:", error);
        return {};
    }
}

// Lấy cài đặt SEO cho trang cụ thể theo route name
export async function getPageSeoByRoute(routeName: string): Promise<SeoData> {
    try {
        const response = await axios.get(`${API_URL}/page-seo/${routeName}`);
        return response.data.data;
    } catch (error) {
        console.error("Error fetching page SEO by route:", error);
        return {};
    }
}

// Lấy cài đặt SEO cho trang cụ thể theo URL pattern
export async function getPageSeoByUrl(urlPattern: string): Promise<SeoData> {
    try {
        const response = await axios.get(
            `${API_URL}/page-seo/url/${urlPattern}`
        );
        return response.data.data;
    } catch (error) {
        console.error("Error fetching page SEO by URL:", error);
        return {};
    }
}

// Lấy cài đặt hệ thống theo nhóm
export async function getSettings(
    group: string = "general"
): Promise<SettingsData> {
    try {
        const response = await axios.get(`${API_URL}/settings/${group}`);
        return response.data.data;
    } catch (error) {
        console.error("Error fetching settings:", error);
        return {};
    }
}

// Lấy tất cả cài đặt hệ thống
export async function getAllSettings(): Promise<{
    [group: string]: SettingsData;
}> {
    try {
        const response = await axios.get(`${API_URL}/settings`);
        return response.data.data;
    } catch (error) {
        console.error("Error fetching all settings:", error);
        return {};
    }
}

// Lấy favicon URL
export async function getFavicon(): Promise<string> {
    try {
        const response = await axios.get(`${API_URL}/favicon`);
        return response.data.data.favicon;
    } catch (error) {
        console.error("Error fetching favicon:", error);
        return "/favicon.ico";
    }
}
```

## Hooks

Các hook được định nghĩa trong các file:

1. **src/hooks/useSeo.ts**:

```typescript
// Hook để lấy cài đặt SEO theo nhóm
export function useSeoSettings(group: string = "general") {
    return useQuery<SeoData>({
        queryKey: ["seo", "settings", group],
        queryFn: () => getSeoSettings(group),
    });
}

// Hook để lấy cài đặt SEO cho trang cụ thể theo route name
export function usePageSeoByRoute(routeName: string) {
    return useQuery<SeoData>({
        queryKey: ["seo", "page", "route", routeName],
        queryFn: () => getPageSeoByRoute(routeName),
        enabled: !!routeName,
    });
}

// Hook để lấy cài đặt SEO cho trang cụ thể theo URL pattern
export function usePageSeoByUrl(urlPattern: string) {
    return useQuery<SeoData>({
        queryKey: ["seo", "page", "url", urlPattern],
        queryFn: () => getPageSeoByUrl(urlPattern),
        enabled: !!urlPattern,
    });
}
```

2. **src/hooks/useSettings.ts**:

```typescript
// Hook để lấy cài đặt hệ thống theo nhóm
export function useSettings(group: string = "general") {
    return useQuery<SettingsData>({
        queryKey: ["settings", group],
        queryFn: () => getSettings(group),
        retry: 2,
        staleTime: 1000 * 60 * 5, // 5 phút
    });
}

// Hook để lấy tất cả cài đặt hệ thống
export function useAllSettings() {
    return useQuery<{ [group: string]: SettingsData }>({
        queryKey: ["settings", "all"],
        queryFn: getAllSettings,
        retry: 2,
        staleTime: 1000 * 60 * 5, // 5 phút
    });
}

// Hook để lấy URL tuyệt đối cho đường dẫn tương đối
export function useAbsoluteUrl(relativePath?: string) {
    const baseUrl = process.env.NEXT_PUBLIC_SITE_URL || "https://mechamap.test";

    if (!relativePath) return "";

    // Nếu đã là URL tuyệt đối, trả về nguyên bản
    if (relativePath.startsWith("http")) {
        return relativePath;
    }

    // Xử lý đường dẫn tương đối
    if (relativePath.startsWith("/") && baseUrl.endsWith("/")) {
        return baseUrl + relativePath.substring(1);
    } else if (!relativePath.startsWith("/") && !baseUrl.endsWith("/")) {
        return baseUrl + "/" + relativePath;
    } else {
        return baseUrl + relativePath;
    }
}
```

## Cách sử dụng

### 1. Sử dụng SEO trong các trang

```tsx
"use client";

import { usePageSeoByRoute } from "@/hooks/useSeo";

export default function HomePage() {
    const { data: seo } = usePageSeoByRoute("home");

    return (
        <div>
            <h1>{seo?.title || "Trang chủ"}</h1>
            <p>{seo?.meta_description || "Mô tả trang chủ"}</p>
        </div>
    );
}
```

### 2. Sử dụng Settings trong các component

```tsx
"use client";

import { useSettings, useAbsoluteUrl } from "@/hooks/useSettings";
import Image from "next/image";

export default function Logo() {
    const { data: settings } = useSettings("general");
    const logoUrl = useAbsoluteUrl(settings?.site_logo);

    return (
        <div>
            <Image
                src={logoUrl}
                alt={settings?.site_name || "MechaMap"}
                width={200}
                height={50}
            />
        </div>
    );
}
```

### 3. Sử dụng Favicon

```tsx
"use client";

import { useEffect, useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { getFavicon } from "@/services/seo.service";

export default function FaviconProvider() {
    // State để theo dõi xem favicon đã được cập nhật chưa
    const [faviconUpdated, setFaviconUpdated] = useState(false);

    // Sử dụng API endpoint để lấy favicon
    const {
        data: faviconUrl,
        isLoading,
        isError,
    } = useQuery({
        queryKey: ["favicon"],
        queryFn: getFavicon,
        retry: 3, // Thử lại 3 lần nếu có lỗi
        staleTime: 1000 * 60 * 60, // Cache trong 1 giờ
    });

    useEffect(() => {
        // Nếu favicon đã được cập nhật hoặc đang loading, không làm gì cả
        if (faviconUpdated || isLoading) return;

        // Nếu có lỗi hoặc không có favicon, sử dụng favicon mặc định từ metadata
        if (isError || !faviconUrl) {
            console.log("Using default favicon from metadata");
            setFaviconUpdated(true);
            return;
        }

        try {
            // Xóa tất cả các favicon hiện tại
            const existingFavicons = document.querySelectorAll(
                'link[rel="icon"], link[rel="shortcut icon"]'
            );
            existingFavicons.forEach((favicon) => {
                document.head.removeChild(favicon);
            });

            // Thêm favicon mới
            const link = document.createElement("link");
            link.rel = "icon";
            link.href = faviconUrl;
            link.type = "image/x-icon";
            document.head.appendChild(link);

            // Đánh dấu favicon đã được cập nhật
            setFaviconUpdated(true);
        } catch (error) {
            console.error("Error updating favicon:", error);
        }
    }, [faviconUrl, isLoading, isError, faviconUpdated]);

    return null;
}
```

## Lưu ý

1. **Đường dẫn tương đối**: Các đường dẫn như `/storage/settings/favicon.png` là đường dẫn tương đối. Sử dụng hook `useAbsoluteUrl` để chuyển đổi thành đường dẫn tuyệt đối.

2. **Xử lý lỗi**: Các service và hook đã được cấu hình để xử lý lỗi và trả về giá trị mặc định khi có lỗi.

3. **Caching**: Các hook sử dụng React Query để cache dữ liệu, giúp giảm số lượng request đến server.

4. **Stale Time**: Các hook đã được cấu hình với staleTime là 5 phút, có nghĩa là dữ liệu sẽ được coi là "fresh" trong 5 phút và không cần phải fetch lại.

5. **Retry**: Các hook đã được cấu hình để retry 2 lần khi có lỗi, giúp tăng khả năng thành công khi gọi API.
