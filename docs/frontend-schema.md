# Hướng dẫn sử dụng Schema.org trong Frontend

Tài liệu này hướng dẫn cách sử dụng Schema.org trong frontend Next.js, lấy dữ liệu từ API.

## Schema.org là gì?

Schema.org là một chuẩn dữ liệu có cấu trúc được phát triển bởi Google, Microsoft, Yahoo và Yandex. Nó giúp các công cụ tìm kiếm hiểu rõ hơn về nội dung trang web, từ đó hiển thị kết quả tìm kiếm phong phú hơn (rich snippets).

## Cấu trúc API

Frontend sử dụng các API sau để lấy dữ liệu cho Schema.org:

1. **API SEO General**:
   - `GET /api/v1/seo/general` - Lấy cài đặt SEO chung
   - Trả về: `site_title`, `site_description`, `site_keywords`, ...

2. **API Settings General**:
   - `GET /api/v1/settings/general` - Lấy cài đặt chung
   - Trả về: `site_name`, `site_slogan`, `site_logo`, ...

3. **API Settings Company**:
   - `GET /api/v1/settings/company` - Lấy thông tin công ty
   - Trả về: `company_name`, `company_address`, `company_phone`, ...

## Hook useSchema

Hook `useSchema` được định nghĩa trong file `src/hooks/useSeo.ts`:

```typescript
/**
 * Hook để lấy dữ liệu Schema.org
 * @param type Loại Schema.org (WebSite, Organization, Forum, etc.)
 * @param pageData Dữ liệu bổ sung cho Schema
 * @returns Dữ liệu Schema.org
 */
export function useSchema(type: string = 'WebSite', pageData?: any) {
  const { data: generalSeo, isLoading: isLoadingGeneral } = useSeoSettings('general');
  const { data: socialSeo, isLoading: isLoadingSocial } = useSeoSettings('social');
  const { data: generalSettings, isLoading: isLoadingSettings } = useSettings('general');
  const { data: companySettings, isLoading: isLoadingCompany } = useSettings('company');
  
  // Chuyển đổi đường dẫn tương đối thành đường dẫn tuyệt đối
  const logoUrl = useAbsoluteUrl(generalSettings?.site_logo);
  const ogImageUrl = useAbsoluteUrl(socialSeo?.og_image);
  
  const isLoading = isLoadingGeneral || isLoadingSocial || isLoadingSettings || isLoadingCompany;
  
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || 'https://mechamap.test';
  
  // Dữ liệu cơ bản cho tất cả các loại Schema
  const baseSchema = {
    '@context': 'https://schema.org',
    '@type': type,
    name: generalSettings?.site_name || generalSeo?.site_title || 'MechaMap',
    description: generalSeo?.site_description || 'MechaMap là diễn đàn cộng đồng chia sẻ kiến thức',
    url: siteUrl,
  };
  
  // Dữ liệu Schema theo loại
  let schemaData = {};
  
  switch (type) {
    case 'WebSite':
      schemaData = {
        ...baseSchema,
        potentialAction: {
          '@type': 'SearchAction',
          'target': `${siteUrl}/search?q={search_term_string}`,
          'query-input': 'required name=search_term_string'
        }
      };
      break;
      
    case 'Organization':
      schemaData = {
        ...baseSchema,
        logo: logoUrl || `${siteUrl}/images/logo.png`,
        image: ogImageUrl || `${siteUrl}/images/og-image.jpg`,
        email: companySettings?.company_email || 'info@mechamap.com',
        telephone: companySettings?.company_phone || '+84 123 456 789',
        address: {
          '@type': 'PostalAddress',
          addressLocality: companySettings?.company_city || 'Hà Nội',
          addressRegion: companySettings?.company_region || '',
          addressCountry: companySettings?.company_country || 'Việt Nam',
          postalCode: companySettings?.company_postal_code || '',
          streetAddress: companySettings?.company_address || ''
        },
        sameAs: [
          companySettings?.social_facebook,
          companySettings?.social_twitter,
          companySettings?.social_linkedin,
          companySettings?.social_youtube,
          companySettings?.social_instagram
        ].filter(Boolean)
      };
      break;
      
    case 'DiscussionForum':
      schemaData = {
        ...baseSchema,
        headline: generalSettings?.site_slogan || 'Diễn đàn cộng đồng chia sẻ kiến thức',
        publisher: {
          '@type': 'Organization',
          name: companySettings?.company_name || generalSettings?.site_name || 'MechaMap',
          logo: logoUrl || `${siteUrl}/images/logo.png`
        }
      };
      break;
      
    default:
      // Nếu có dữ liệu bổ sung, kết hợp với baseSchema
      schemaData = pageData ? { ...baseSchema, ...pageData } : baseSchema;
  }
  
  return { data: schemaData, isLoading };
}
```

## Component SchemaProvider

Component `SchemaProvider` được định nghĩa trong file `src/components/seo/SchemaProvider.tsx`:

```tsx
'use client';

import { useSchema } from '@/hooks/useSeo';
import Script from 'next/script';

interface SchemaProviderProps {
  type?: string;
  pageData?: any;
}

/**
 * Component để thêm Schema.org vào trang web
 * @param type Loại Schema.org (WebSite, Organization, Forum, etc.)
 * @param pageData Dữ liệu bổ sung cho Schema
 */
export default function SchemaProvider({
  type = 'WebSite',
  pageData,
}: SchemaProviderProps) {
  const { data: schema, isLoading } = useSchema(type, pageData);
  
  if (isLoading) {
    return null;
  }
  
  return (
    <Script
      id={`schema-${type.toLowerCase()}`}
      type="application/ld+json"
      dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }}
    />
  );
}

/**
 * Component để thêm nhiều Schema.org vào trang web
 */
export function MultiSchemaProvider() {
  const { data: websiteSchema, isLoading: isLoadingWebsite } = useSchema('WebSite');
  const { data: organizationSchema, isLoading: isLoadingOrganization } = useSchema('Organization');
  
  if (isLoadingWebsite || isLoadingOrganization) {
    return null;
  }
  
  return (
    <>
      <Script
        id="schema-website"
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(websiteSchema) }}
      />
      <Script
        id="schema-organization"
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(organizationSchema) }}
      />
    </>
  );
}
```

## Cách sử dụng

### 1. Sử dụng MultiSchemaProvider trong layout.tsx

```tsx
import { MultiSchemaProvider } from "@/components/seo/SchemaProvider";

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
          <MultiSchemaProvider />
          {children}
        </QueryProvider>
      </body>
    </html>
  );
}
```

### 2. Sử dụng SchemaProvider trong các trang cụ thể

```tsx
'use client';

import SchemaProvider from "@/components/seo/SchemaProvider";

export default function ThreadPage({ params }: { params: { id: string } }) {
  const { data: thread } = useThread(params.id);
  
  // Dữ liệu Schema.org cho trang chi tiết bài viết
  const threadSchema = {
    headline: thread?.title,
    author: {
      '@type': 'Person',
      name: thread?.author?.name,
      url: `${process.env.NEXT_PUBLIC_SITE_URL}/users/${thread?.author?.username}`
    },
    datePublished: thread?.created_at,
    dateModified: thread?.updated_at,
    commentCount: thread?.comments_count,
    mainEntityOfPage: {
      '@type': 'WebPage',
      '@id': `${process.env.NEXT_PUBLIC_SITE_URL}/threads/${thread?.id}`
    }
  };
  
  return (
    <>
      <SchemaProvider type="DiscussionForumPosting" pageData={threadSchema} />
      <div>
        <h1>{thread?.title}</h1>
        {/* Nội dung trang */}
      </div>
    </>
  );
}
```

## Các loại Schema.org phổ biến

1. **WebSite**: Thông tin về trang web
2. **Organization**: Thông tin về tổ chức, công ty
3. **DiscussionForum**: Thông tin về diễn đàn
4. **DiscussionForumPosting**: Thông tin về bài viết trong diễn đàn
5. **Person**: Thông tin về người dùng
6. **FAQPage**: Thông tin về trang FAQ
7. **BreadcrumbList**: Thông tin về breadcrumb

## Kiểm tra Schema.org

Bạn có thể kiểm tra Schema.org bằng công cụ [Google Rich Results Test](https://search.google.com/test/rich-results) hoặc [Schema.org Validator](https://validator.schema.org/).

## Lưu ý

1. **Đường dẫn tương đối**: Các đường dẫn như `/storage/settings/logo.png` là đường dẫn tương đối. Hook `useSchema` đã tự động chuyển đổi thành đường dẫn tuyệt đối.

2. **Dữ liệu mặc định**: Nếu không có dữ liệu từ API, hook `useSchema` sẽ sử dụng dữ liệu mặc định.

3. **Nhiều Schema**: Bạn có thể sử dụng nhiều Schema.org trong một trang bằng cách sử dụng nhiều component `SchemaProvider`.

4. **Dữ liệu động**: Bạn có thể truyền dữ liệu động vào component `SchemaProvider` thông qua prop `pageData`.

5. **Caching**: Hook `useSchema` sử dụng React Query để cache dữ liệu, giúp giảm số lượng request đến server.
