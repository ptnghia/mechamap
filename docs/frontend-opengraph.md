# Hướng dẫn sử dụng Open Graph trong Frontend

Tài liệu này hướng dẫn cách sử dụng Open Graph trong frontend Next.js, lấy dữ liệu từ API.

## Cấu trúc API

Frontend sử dụng các API sau để lấy dữ liệu SEO và Open Graph:

1. **API SEO General**:
   - `GET /api/v1/seo/general` - Lấy cài đặt SEO chung
   - Trả về: `site_title`, `site_description`, `site_keywords`, `allow_indexing`, `google_analytics_id`, `google_search_console_id`, `facebook_app_id`, `twitter_username`

2. **API SEO Social**:
   - `GET /api/v1/seo/social` - Lấy cài đặt SEO cho mạng xã hội
   - Trả về: `og_title`, `og_description`, `og_image`, `twitter_card`, `twitter_title`, `twitter_description`, `twitter_image`

## Hooks

Hook `useOpenGraph` được định nghĩa trong file `src/hooks/useSeo.ts`:

```typescript
/**
 * Hook để lấy cài đặt Open Graph
 * @returns Dữ liệu Open Graph với đường dẫn tuyệt đối
 */
export function useOpenGraph() {
  const { data: generalSeo, isLoading: isLoadingGeneral } = useSeoSettings('general');
  const { data: socialSeo, isLoading: isLoadingSocial } = useSeoSettings('social');
  
  // Chuyển đổi đường dẫn tương đối thành đường dẫn tuyệt đối
  const ogImageUrl = useAbsoluteUrl(socialSeo?.og_image);
  const twitterImageUrl = useAbsoluteUrl(socialSeo?.twitter_image);
  
  const isLoading = isLoadingGeneral || isLoadingSocial;
  
  const data = {
    title: socialSeo?.og_title || generalSeo?.site_title,
    description: socialSeo?.og_description || generalSeo?.site_description,
    ogTitle: socialSeo?.og_title,
    ogDescription: socialSeo?.og_description,
    ogImage: ogImageUrl,
    twitterCard: socialSeo?.twitter_card,
    twitterTitle: socialSeo?.twitter_title,
    twitterDescription: socialSeo?.twitter_description,
    twitterImage: twitterImageUrl,
    keywords: generalSeo?.site_keywords,
    allowIndexing: generalSeo?.allow_indexing === '1',
    googleAnalyticsId: generalSeo?.google_analytics_id,
    googleSearchConsoleId: generalSeo?.google_search_console_id,
    facebookAppId: generalSeo?.facebook_app_id,
    twitterUsername: generalSeo?.twitter_username,
  };
  
  return { data, isLoading };
}
```

## Component OpenGraphProvider

Component `OpenGraphProvider` được định nghĩa trong file `src/components/seo/OpenGraphProvider.tsx`:

```tsx
'use client';

import { useEffect } from 'react';
import Head from 'next/head';
import { useOpenGraph } from '@/hooks/useSeo';

interface OpenGraphProviderProps {
  title?: string;
  description?: string;
  ogImage?: string;
  url?: string;
  type?: string;
}

export default function OpenGraphProvider({
  title,
  description,
  ogImage,
  url,
  type = 'website',
}: OpenGraphProviderProps) {
  const { data: seo, isLoading } = useOpenGraph();
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || 'https://mechamap.test';
  
  // Ưu tiên props truyền vào, nếu không có thì sử dụng dữ liệu từ API
  const finalTitle = title || seo?.title || 'MechaMap - Diễn đàn cộng đồng';
  const finalDescription = description || seo?.description || 'MechaMap là diễn đàn cộng đồng chia sẻ kiến thức và kinh nghiệm về công nghệ, lập trình, thiết kế và nhiều lĩnh vực khác.';
  const finalOgImage = ogImage || seo?.ogImage || `${siteUrl}/images/og-image.jpg`;
  const finalTwitterImage = seo?.twitterImage || finalOgImage;
  const finalUrl = url ? `${siteUrl}${url}` : siteUrl;
  
  if (isLoading) {
    return null;
  }
  
  return (
    <Head>
      {/* Primary Meta Tags */}
      <title>{finalTitle}</title>
      <meta name="title" content={finalTitle} />
      <meta name="description" content={finalDescription} />
      {seo?.keywords && <meta name="keywords" content={seo.keywords} />}
      
      {/* Open Graph / Facebook */}
      <meta property="og:type" content={type} />
      <meta property="og:url" content={finalUrl} />
      <meta property="og:title" content={seo?.ogTitle || finalTitle} />
      <meta property="og:description" content={seo?.ogDescription || finalDescription} />
      <meta property="og:image" content={finalOgImage} />
      {seo?.facebookAppId && <meta property="fb:app_id" content={seo.facebookAppId} />}
      
      {/* Twitter */}
      <meta name="twitter:card" content={seo?.twitterCard || "summary_large_image"} />
      <meta name="twitter:url" content={finalUrl} />
      <meta name="twitter:title" content={seo?.twitterTitle || finalTitle} />
      <meta name="twitter:description" content={seo?.twitterDescription || finalDescription} />
      <meta name="twitter:image" content={finalTwitterImage} />
      {seo?.twitterUsername && <meta name="twitter:site" content={`@${seo.twitterUsername}`} />}
      
      {/* Robots */}
      {seo?.allowIndexing === false && <meta name="robots" content="noindex, nofollow" />}
      
      {/* Google */}
      {seo?.googleSearchConsoleId && (
        <meta name="google-site-verification" content={seo.googleSearchConsoleId} />
      )}
    </Head>
  );
}
```

## Cách sử dụng

### 1. Sử dụng OpenGraphProvider trong layout.tsx

```tsx
import OpenGraphProvider from "@/components/seo/OpenGraphProvider";

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="vi">
      <body>
        <QueryProvider>
          <ToastProvider />
          <FaviconProvider />
          <OpenGraphProvider />
          {children}
        </QueryProvider>
      </body>
    </html>
  );
}
```

### 2. Sử dụng OpenGraphProvider trong các trang cụ thể

```tsx
'use client';

import OpenGraphProvider from "@/components/seo/OpenGraphProvider";

export default function AboutPage() {
  return (
    <>
      <OpenGraphProvider
        title="Giới thiệu về MechaMap"
        description="Tìm hiểu thêm về MechaMap - Diễn đàn cộng đồng chia sẻ kiến thức và kinh nghiệm về công nghệ, lập trình, thiết kế và nhiều lĩnh vực khác."
        url="/about"
      />
      <div>
        <h1>Giới thiệu về MechaMap</h1>
        {/* Nội dung trang */}
      </div>
    </>
  );
}
```

## Lưu ý

1. **Đường dẫn tương đối**: Các đường dẫn như `/storage/seo/og-image.jpg` là đường dẫn tương đối. Hook `useOpenGraph` đã tự động chuyển đổi thành đường dẫn tuyệt đối.

2. **Ưu tiên props**: Component `OpenGraphProvider` ưu tiên sử dụng props truyền vào, nếu không có thì sử dụng dữ liệu từ API.

3. **Hình ảnh mặc định**: Nếu không có hình ảnh từ API, component sẽ sử dụng hình ảnh mặc định từ `/images/og-image.jpg`.

4. **Caching**: Hook `useOpenGraph` sử dụng React Query để cache dữ liệu, giúp giảm số lượng request đến server.

5. **Metadata mặc định**: Metadata trong `layout.tsx` sẽ được ghi đè bởi `OpenGraphProvider`, nhưng vẫn cần thiết lập để đảm bảo SEO khi JavaScript bị tắt.

6. **Hình ảnh Open Graph**: Hình ảnh Open Graph nên có kích thước 1200x630 pixels để hiển thị tốt nhất trên các nền tảng mạng xã hội.
