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

