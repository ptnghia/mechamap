# Blade Localization Audit Report

**Directory:** auth
**Generated:** 2025-07-20 03:21:48
**Files processed:** 10

## 📝 Hardcoded Texts Found (150)

- `></i> Mật khẩu khớp
                    </div>
                </div>
            </div>
        </div>

        {{-- Section: Account Type --}}
        <div class=`
- `>Điều khoản sử dụng</h5>
                <button type=`
- `>
                <p>Điều khoản sử dụng MechaMap sẽ được hiển thị ở đây...</p>
            </div>
            <div class=`
- `>Đóng</button>
            </div>
        </div>
    </div>
</div>

{{-- Privacy Modal --}}
<div class=`
- `>Chính sách bảo mật</h5>
                <button type=`
- `>
                <p>Chính sách bảo mật MechaMap sẽ được hiển thị ở đây...</p>
            </div>
            <div class=`
- `>Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push(`
- `Close`
- `Mô tả chi tiết về hoạt động kinh doanh, sản phẩm/dịch vụ chính của công ty...`
- `Ví dụ: +84 123 456 789`
- `Ví dụ: info@company.com`
- `Nhập địa chỉ đầy đủ của công ty...`
- `>
                    <h6>Tài liệu đã chọn:</h6>
                    <div class=`
- `Chỉ được tải lên tối đa 5 tài liệu`
- `Chỉ được chọn tối đa 5 lĩnh vực kinh doanh`
- `DOMContentLoaded`
- `Bytes`
- `Đăng ký thành công`
- `>
                🎉 Đăng ký thành công!
            </h1>
            
            <p class=`
- `>
                Chào mừng {{ $user->name }} đến với MechaMap
            </p>
        </div>

        {{-- Success Content --}}
        <div class=`
- `>Tài khoản doanh nghiệp đã được tạo</h5>
                                <p class=`
- `>
                                    Tài khoản của bạn đang chờ xác minh từ admin. 
                                    Bạn sẽ nhận được email thông báo khi tài khoản được phê duyệt.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class=`
- `></i>
                            Trạng thái xác minh
                        </h4>
                        
                        <div class=`
- `>
                                    <h6>Thông tin cơ bản</h6>
                                    <p>Đã hoàn thành</p>
                                </div>
                            </div>
                            
                            <div class=`
- `>
                                    <h6>Thông tin doanh nghiệp</h6>
                                    <p>Đã hoàn thành</p>
                                </div>
                            </div>
                            
                            <div class=`
- `>
                                    <h6>Xác minh admin</h6>
                                    <p>Đang chờ xử lý</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=`
- `></i>
                            Bước tiếp theo
                        </h4>
                        
                        <div class=`
- `>
                                    <strong>Xác minh email</strong><br>
                                    Kiểm tra hộp thư và click vào link xác minh
                                </div>
                            </div>
                            
                            <div class=`
- `>
                                    <strong>Chờ xác thực admin</strong><br>
                                    Admin sẽ xem xét và phê duyệt tài khoản trong 1-3 ngày
                                </div>
                            </div>
                            
                            <div class=`
- `>
                                    <strong>Nhận thông báo</strong><br>
                                    Bạn sẽ nhận email khi tài khoản được kích hoạt
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=`
- `></i>
                            <strong>Lưu ý:</strong> 
                            Trong thời gian chờ xác minh, bạn có thể đăng nhập và cập nhật thông tin profile, 
                            nhưng một số tính năng doanh nghiệp sẽ bị hạn chế.
                        </div>
                    </div>
                </div>
            @else
                {{-- Community Account Success --}}
                <div class=`
- `>Tài khoản cộng đồng đã được tạo</h5>
                                <p class=`
- `>
                                    Bạn có thể bắt đầu sử dụng MechaMap ngay lập tức. 
                                    Hãy xác minh email để mở khóa tất cả tính năng.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class=`
- `></i>
                            Tính năng có sẵn
                        </h4>
                        
                        <div class=`
- `></i>
                                <h6>Thảo luận</h6>
                                <p>Tham gia các cuộc thảo luận về cơ khí</p>
                            </div>
                            
                            <div class=`
- `></i>
                                <h6>Chia sẻ</h6>
                                <p>Chia sẻ kiến thức và kinh nghiệm</p>
                            </div>
                            
                            <div class=`
- `></i>
                                <h6>Kết nối</h6>
                                <p>Kết nối với cộng đồng cơ khí</p>
                            </div>
                            
                            <div class=`
- `></i>
                                <h6>Học tập</h6>
                                <p>Truy cập tài liệu và khóa học</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Email Verification Notice --}}
            <div class=`
- `>
                        <h5>Xác minh email</h5>
                        <p>
                            Chúng tôi đã gửi email xác minh đến: 
                            <strong>{{ $user->email }}</strong>
                        </p>
                        <p class=`
- `>
                            Vui lòng kiểm tra hộp thư (bao gồm cả thư mục spam) và click vào link xác minh.
                        </p>
                    </div>
                </div>
                
                <div class=`
- `></i>
                            Gửi lại email xác minh
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class=`
- `></i>
                        Đi đến Dashboard
                    </a>
                @else
                    <a href=`
- `></i>
                        Đi đến Dashboard
                    </a>
                @endif
                
                <a href=`
- `></i>
                    Hoàn thiện Profile
                </a>
            </div>
            
            <div class=`
- `>Cần hỗ trợ?</p>
                <div class=`
- `></i>
                        Hướng dẫn bắt đầu
                    </a>
                    <a href=`
- `></i>
                        Liên hệ hỗ trợ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push(`
- `Confirm Password`
- `Page Title`
- `Password`
- `Confirm`
- `Quên mật khẩu`
- `Khôi phục mật khẩu`
- `Lấy lại quyền truy cập vào tài khoản của bạn`
- `>Quên mật khẩu?</h2>
        <p class=`
- `>Không sao cả! Chúng tôi sẽ gửi link khôi phục mật khẩu đến email của bạn</p>
    </div>

    <!-- Status Messages -->
    @if (session(`
- `></i>Địa chỉ email
            </label>
            <input id=`
- `Nhập địa chỉ email đã đăng ký`
- `></i>
                Chúng tôi sẽ gửi link khôi phục mật khẩu đến email này
            </small>
        </div>

        <!-- Submit Button -->
        <div class=`
- `></i>Gửi link khôi phục
            </button>
        </div>

        <!-- Back to Login -->
        <div class=`
- `></i>Quay lại đăng nhập
            </a>
        </div>
    </form>

    <!-- Help Section -->
    <div class=`
- `></i>Cần hỗ trợ?
        </h6>
        <div class=`
- `></i>
                    <div>
                        <strong>Không nhận được email?</strong>
                        <p class=`
- `>Kiểm tra thư mục spam hoặc thử lại sau 5 phút</p>
                    </div>
                </div>
            </div>
            <div class=`
- `></i>
                    <div>
                        <strong>Email không tồn tại?</strong>
                        <p class=`
- `>
                            Hãy <a href=`
- `>tạo tài khoản mới</a>
                            hoặc liên hệ hỗ trợ
                        </p>
                    </div>
                </div>
            </div>
            <div class=`
- `></i>
                    <div>
                        <strong>Vẫn gặp vấn đề?</strong>
                        <p class=`
- `>
                            Liên hệ: <a href=`
- `></i>Đang gửi...`
- `></i>Loại tài khoản
            </label>
            <select id=`
- `>Chọn loại tài khoản của bạn</option>

                <optgroup label=`
- `}}>
                        Thành viên - Tham gia thảo luận và chia sẻ kiến thức
                    </option>

                </optgroup>

                <optgroup label=`
- `}}>
                        Nhà sản xuất - Sản xuất và cung cấp sản phẩm cơ khí
                    </option>
                    <option value=`
- `}}>
                        Nhà cung cấp - Phân phối thiết bị và vật tư cơ khí
                    </option>
                    <option value=`
- `}}>
                        Nhãn hàng - Quảng bá thương hiệu và sản phẩm
                    </option>
                </optgroup>
            </select>
            @error(`
- `>Chọn loại tài khoản phù hợp với mục đích sử dụng. Bạn có thể thay đổi sau này.</small>
        </div>

        <!-- Terms and Privacy -->
        <div class=`
- `>
                    Tôi đồng ý với <a href=`
- `>Điều khoản sử dụng</a> và
                    <a href=`
- `>Chính sách bảo mật</a>
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class=`
- `></i>Tạo tài khoản
            </button>
        </div>

        <!-- Login Link -->
        <div class=`
- `>Đã có tài khoản? </span>
            <a href=`
- `>
                Đăng nhập ngay
            </a>
        </div>
    </form>
</x-auth-layout>

@push(`
- `Đăng ký tài khoản MechaMap`
- `>
                🎉 Quy trình đăng ký mới!
            </h1>
            <p class=`
- `>
                Chúng tôi đã cải tiến trải nghiệm đăng ký để phục vụ bạn tốt hơn
            </p>
        </div>

        {{-- Content --}}
        <div class=`
- `></i>
                    </div>
                    <h5>Quy trình từng bước</h5>
                    <p>Đăng ký dễ dàng với hướng dẫn rõ ràng từng bước</p>
                </div>

                <div class=`
- `></i>
                    </div>
                    <h5>Xác thực thời gian thực</h5>
                    <p>Kiểm tra thông tin ngay lập tức, tránh lỗi khi submit</p>
                </div>

                <div class=`
- `></i>
                    </div>
                    <h5>Thông tin doanh nghiệp</h5>
                    <p>Dành riêng cho đối tác kinh doanh với form chuyên biệt</p>
                </div>

                <div class=`
- `></i>
                    </div>
                    <h5>Tối ưu mobile</h5>
                    <p>Trải nghiệm mượt mà trên mọi thiết bị</p>
                </div>

                <div class=`
- `></i>
                    </div>
                    <h5>Lưu tự động</h5>
                    <p>Không lo mất dữ liệu khi điền form</p>
                </div>

                <div class=`
- `></i>
                    </div>
                    <h5>Dễ tiếp cận</h5>
                    <p>Hỗ trợ đầy đủ cho người khuyết tật</p>
                </div>
            </div>

            <div class=`
- `></i>
                    <strong>Lưu ý:</strong> Bạn sẽ được chuyển hướng tự động đến quy trình đăng ký mới trong <span id=`
- `>5</span> giây.
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class=`
- `></i>
                    Bắt đầu đăng ký ngay
                </a>

                <a href=`
- `></i>
                    Đã có tài khoản? Đăng nhập
                </a>
            </div>

            <div class=`
- `></i>
                    Cần hỗ trợ? <a href=`
- `>Liên hệ với chúng tôi</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push(`
- `Đặt lại mật khẩu`
- `Tạo mật khẩu mới cho tài khoản của bạn`
- `>Tạo mật khẩu mới</h2>
        <p class=`
- `>Vui lòng nhập mật khẩu mới cho tài khoản của bạn</p>
    </div>

    <!-- Status Messages -->
    @if (session(`
- `></i>
                Đây là địa chỉ email được liên kết với tài khoản của bạn
            </small>
        </div>

        <!-- New Password -->
        <div class=`
- `></i>Mật khẩu mới
            </label>
            <div class=`
- `Nhập mật khẩu mới`
- `>Sử dụng ít nhất 8 ký tự với chữ cái, số và ký hiệu</small>

            <!-- Password Strength Indicator -->
            <div class=`
- `></i>Xác nhận mật khẩu mới
            </label>
            <div class=`
- `Nhập lại mật khẩu mới`
- `></i>Cập nhật mật khẩu
            </button>
        </div>

        <!-- Back to Login -->
        <div class=`
- `></i>Quay lại đăng nhập
            </a>
        </div>
    </form>

    <!-- Security Tips -->
    <div class=`
- `></i>Mẹo bảo mật
        </h6>
        <div class=`
- `></i>
                    <div>
                        <strong>Mật khẩu mạnh</strong>
                        <p class=`
- `>Sử dụng ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký hiệu</p>
                    </div>
                </div>
            </div>
            <div class=`
- `></i>
                    <div>
                        <strong>Tránh thông tin cá nhân</strong>
                        <p class=`
- `>Không sử dụng tên, ngày sinh, số điện thoại trong mật khẩu</p>
                    </div>
                </div>
            </div>
            <div class=`
- `></i>
                    <div>
                        <strong>Mật khẩu duy nhất</strong>
                        <p class=`
- `>Không sử dụng lại mật khẩu từ các tài khoản khác</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-auth-layout>

@push(`
- `Rất yếu`
- `Yếu`
- `Trung bình`
- `Mạnh`
- `Rất mạnh`
- `Độ mạnh:`
- `></i>Mật khẩu khớp</small>`
- `></i>Mật khẩu không khớp</small>`
- `></i>Đang cập nhật...`
- `••••••••`
- `Xác minh email`
- `Hoàn tất quá trình đăng ký tài khoản`
- `>Kiểm tra email của bạn</h2>
        <p class=`
- `>Chúng tôi đã gửi link xác minh đến email của bạn</p>
    </div>

    <!-- Status Messages -->
    @if (session(`
- `></i>
            Link xác minh mới đã được gửi đến địa chỉ email bạn đã đăng ký.
        </div>
    @endif

    <!-- Instructions -->
    <div class=`
- `></i>Hướng dẫn xác minh
        </h6>
        <ol class=`
- `>Mở ứng dụng email trên thiết bị của bạn</li>
            <li class=`
- `>Tìm email từ <strong>MechaMap</strong> với tiêu đề`
- `>Nhấp vào nút <strong>`
- `>Quay lại trang này để tiếp tục</li>
        </ol>
    </div>

    <!-- Action Buttons -->
    <div class=`
- `></i>Gửi lại email xác minh
                </button>
            </form>
        </div>
        <div class=`
- `></i>Đăng xuất
                </button>
            </form>
        </div>
    </div>

    <!-- Help Section -->
    <div class=`
- `></i>
                    <div>
                        <strong>Không tìm thấy email?</strong>
                        <p class=`
- `>Kiểm tra thư mục spam/junk mail hoặc thư mục quảng cáo</p>
                    </div>
                </div>
            </div>
            <div class=`
- `></i>
                    <div>
                        <strong>Email chưa đến?</strong>
                        <p class=`
- `>Đợi 2-3 phút rồi kiểm tra lại, hoặc nhấn`
- `>
                            Đăng xuất và <a href=`
- `>đăng ký lại</a>
                            với email đúng
                        </p>
                    </div>
                </div>
            </div>
            <div class=`
- `>
                            Liên hệ hỗ trợ: <a href=`
- `></i>
            Trang này sẽ tự động làm mới sau khi bạn xác minh email
        </small>
    </div>
</x-auth-layout>

@push(`
- `></i>
                </div>
                <h3>Email đã được xác minh!</h3>
                <p>Đang chuyển hướng...</p>
                <div class=`
- `></i>Gửi lại email xác minh`
- `XMLHttpRequest`

## 🔑 Existing Translation Keys (120)

- `auth.register.step1_title`
- `auth.register.wizard_title`
- `auth.register.step1_subtitle`
- `auth.register.continue_button`
- `auth.register.personal_info_title`
- `auth.register.personal_info_description`
- `auth.full_name_label`
- `auth.full_name_placeholder`
- `auth.register.name_valid`
- `auth.username_label`
- `auth.username_placeholder`
- `auth.register.username_available`
- `auth.username_help`
- `auth.email_label`
- `auth.email_placeholder`
- `auth.register.email_valid`
- `auth.register.email_help`
- `auth.password_label`
- `auth.password_placeholder`
- `auth.password_help`
- `auth.confirm_password_label`
- `auth.confirm_password_placeholder`
- `auth.register.account_type_title`
- `auth.register.account_type_description`
- `auth.register.community_member_title`
- `auth.register.community_member_description`
- `auth.register.member_role`
- `auth.register.recommended`
- `auth.register.member_role_desc`
- `auth.register.guest_role`
- `auth.register.guest_role_desc`
- `ui.common.note`
- `auth.register.note_community`
- `auth.register.business_partner_title`
- `auth.register.business_partner_description`
- `auth.register.manufacturer_role`
- `auth.register.manufacturer_role_desc`
- `auth.register.supplier_role`
- `auth.register.supplier_role_desc`
- `auth.register.brand_role`
- `auth.register.brand_role_desc`
- `auth.register.note_business`
- `auth.register.terms_agreement`
- `auth.register.step2_title`
- `auth.register.step2_subtitle`
- `auth.register.complete_button`
- `auth.register.back_button`
- `auth.register.account_type_label`
- `auth.register.company_info_title`
- `auth.register.company_info_description`
- `auth.register.company_name_label`
- `auth.register.company_name_placeholder`
- `auth.register.company_name_help`
- `auth.register.business_license_label`
- `auth.register.business_license_placeholder`
- `auth.register.tax_code_label`
- `auth.register.tax_code_placeholder`
- `auth.register.tax_code_help`
- `auth.register.company_description_label`
- `auth.register.company_description_help`
- `auth.register.business_field_label`
- `auth.register.business_categories`
- `auth.register.business_field_help`
- `auth.register.contact_info_title`
- `auth.register.contact_info_description`
- `auth.register.company_phone`
- `auth.register.company_email_label`
- `auth.register.company_email_help`
- `auth.register.company_address`
- `auth.register.verification_docs_title`
- `auth.register.verification_docs_description`
- `auth.register.file_upload_title`
- `auth.register.file_upload_support`
- `auth.register.file_upload_size`
- `auth.register.choose_documents`
- `auth.register.document_suggestions`
- `auth.register.important_notes_title`
- `auth.register.note_verification_required`
- `auth.register.note_verification_time`
- `auth.register.note_email_notification`
- `auth.register.note_pending_access`
- `Confirm Password`
- `This is a secure area of the application. Please confirm your password before continuing.`
- `Password`
- `Confirm`
- `auth.register.title`
- `auth.create_new_account`
- `content.join_engineering_community`
- `auth.welcome_to_mechamap`
- `auth.create_account_journey`
- `auth.login.title`
- `auth.knowledge_hub`
- `auth.connect_engineers`
- `auth.join_discussions`
- `auth.share_experience`
- `auth.marketplace_products`
- `auth.trusted_by`
- `auth.members_badge`
- `auth.individual_partners_badge`
- `auth.business_badge`
- `auth.welcome_back`
- `auth.login_journey_description`
- `auth.email_or_username_label`
- `auth.remember_login`
- `auth.forgot_password_link`
- `auth.login_button`
- `auth.or_login_with`
- `auth.login_with_google`
- `auth.login_with_facebook`
- `auth.no_account`
- `auth.register_now`
- `auth.ssl_security`
- `auth.join_community_title`
- `auth.join_community_description`
- `auth.trending_topics`
- `auth.trending_topics_desc`
- `auth.expert_network`
- `auth.expert_network_desc`
- `auth.knowledge_base`
- `auth.knowledge_base_desc`

## 💡 Recommendations (150)

### Text: `></i> Mật khẩu khớp
                    </div>
                </div>
            </div>
        </div>

        {{-- Section: Account Type --}}
        <div class=`
- **Suggested key:** `core.auth.i_mt_khu_khp_div_div_div_div_s`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Điều khoản sử dụng</h5>
                <button type=`
- **Suggested key:** `core.auth.iu_khon_s_dngh5_button_type`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                <p>Điều khoản sử dụng MechaMap sẽ được hiển thị ở đây...</p>
            </div>
            <div class=`
- **Suggested key:** `core.auth._piu_khon_s_dng_mechamap_s_c_h`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Đóng</button>
            </div>
        </div>
    </div>
</div>

{{-- Privacy Modal --}}
<div class=`
- **Suggested key:** `core.auth.ngbutton_div_div_div_div_priva`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Chính sách bảo mật</h5>
                <button type=`
- **Suggested key:** `core.auth.chnh_sch_bo_mth5_button_type`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                <p>Chính sách bảo mật MechaMap sẽ được hiển thị ở đây...</p>
            </div>
            <div class=`
- **Suggested key:** `core.auth._pchnh_sch_bo_mt_mechamap_s_c_`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push(`
- **Suggested key:** `core.auth.ngbutton_div_div_div_div_endse`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Close`
- **Suggested key:** `core.auth.close`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Mô tả chi tiết về hoạt động kinh doanh, sản phẩm/dịch vụ chính của công ty...`
- **Suggested key:** `core.auth.m_t_chi_tit_v_hot_ng_kinh_doan`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Ví dụ: +84 123 456 789`
- **Suggested key:** `core.auth.v_d_84_123_456_789`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Ví dụ: info@company.com`
- **Suggested key:** `core.auth.v_d_infocompanycom`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Nhập địa chỉ đầy đủ của công ty...`
- **Suggested key:** `core.auth.nhp_a_ch_y_ca_cng_ty`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                    <h6>Tài liệu đã chọn:</h6>
                    <div class=`
- **Suggested key:** `core.auth._h6ti_liu_chnh6_div_class`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Chỉ được tải lên tối đa 5 tài liệu`
- **Suggested key:** `core.auth.ch_c_ti_ln_ti_a_5_ti_liu`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Chỉ được chọn tối đa 5 lĩnh vực kinh doanh`
- **Suggested key:** `core.auth.ch_c_chn_ti_a_5_lnh_vc_kinh_do`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `DOMContentLoaded`
- **Suggested key:** `core.auth.domcontentloaded`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Bytes`
- **Suggested key:** `core.auth.bytes`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Đăng ký thành công`
- **Suggested key:** `core.auth.ng_k_thnh_cng`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                🎉 Đăng ký thành công!
            </h1>
            
            <p class=`
- **Suggested key:** `core.auth._ng_k_thnh_cng_h1_p_class`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                Chào mừng {{ $user->name }} đến với MechaMap
            </p>
        </div>

        {{-- Success Content --}}
        <div class=`
- **Suggested key:** `core.auth._cho_mng_username_n_vi_mechama`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Tài khoản doanh nghiệp đã được tạo</h5>
                                <p class=`
- **Suggested key:** `core.auth.ti_khon_doanh_nghip_c_toh5_p_c`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                                    Tài khoản của bạn đang chờ xác minh từ admin. 
                                    Bạn sẽ nhận được email thông báo khi tài khoản được phê duyệt.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class=`
- **Suggested key:** `core.auth._ti_khon_ca_bn_ang_ch_xc_minh_`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                            Trạng thái xác minh
                        </h4>
                        
                        <div class=`
- **Suggested key:** `core.auth.i_trng_thi_xc_minh_h4_div_clas`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                                    <h6>Thông tin cơ bản</h6>
                                    <p>Đã hoàn thành</p>
                                </div>
                            </div>
                            
                            <div class=`
- **Suggested key:** `core.auth._h6thng_tin_c_bnh6_p_hon_thnhp`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                                    <h6>Thông tin doanh nghiệp</h6>
                                    <p>Đã hoàn thành</p>
                                </div>
                            </div>
                            
                            <div class=`
- **Suggested key:** `core.auth._h6thng_tin_doanh_nghiph6_p_ho`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                                    <h6>Xác minh admin</h6>
                                    <p>Đang chờ xử lý</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=`
- **Suggested key:** `core.auth._h6xc_minh_adminh6_pang_ch_x_l`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                            Bước tiếp theo
                        </h4>
                        
                        <div class=`
- **Suggested key:** `core.auth.i_bc_tip_theo_h4_div_class`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                                    <strong>Xác minh email</strong><br>
                                    Kiểm tra hộp thư và click vào link xác minh
                                </div>
                            </div>
                            
                            <div class=`
- **Suggested key:** `core.auth._strongxc_minh_emailstrongbr_k`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                                    <strong>Chờ xác thực admin</strong><br>
                                    Admin sẽ xem xét và phê duyệt tài khoản trong 1-3 ngày
                                </div>
                            </div>
                            
                            <div class=`
- **Suggested key:** `core.auth._strongch_xc_thc_adminstrongbr`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                                    <strong>Nhận thông báo</strong><br>
                                    Bạn sẽ nhận email khi tài khoản được kích hoạt
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=`
- **Suggested key:** `core.auth._strongnhn_thng_bostrongbr_bn_`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                            <strong>Lưu ý:</strong> 
                            Trong thời gian chờ xác minh, bạn có thể đăng nhập và cập nhật thông tin profile, 
                            nhưng một số tính năng doanh nghiệp sẽ bị hạn chế.
                        </div>
                    </div>
                </div>
            @else
                {{-- Community Account Success --}}
                <div class=`
- **Suggested key:** `core.auth.i_stronglu_strong_trong_thi_gi`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Tài khoản cộng đồng đã được tạo</h5>
                                <p class=`
- **Suggested key:** `core.auth.ti_khon_cng_ng_c_toh5_p_class`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                                    Bạn có thể bắt đầu sử dụng MechaMap ngay lập tức. 
                                    Hãy xác minh email để mở khóa tất cả tính năng.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class=`
- **Suggested key:** `core.auth._bn_c_th_bt_u_s_dng_mechamap_n`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                            Tính năng có sẵn
                        </h4>
                        
                        <div class=`
- **Suggested key:** `core.auth.i_tnh_nng_c_sn_h4_div_class`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                                <h6>Thảo luận</h6>
                                <p>Tham gia các cuộc thảo luận về cơ khí</p>
                            </div>
                            
                            <div class=`
- **Suggested key:** `core.auth.i_h6tho_lunh6_ptham_gia_cc_cuc`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                                <h6>Chia sẻ</h6>
                                <p>Chia sẻ kiến thức và kinh nghiệm</p>
                            </div>
                            
                            <div class=`
- **Suggested key:** `core.auth.i_h6chia_sh6_pchia_s_kin_thc_v`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                                <h6>Kết nối</h6>
                                <p>Kết nối với cộng đồng cơ khí</p>
                            </div>
                            
                            <div class=`
- **Suggested key:** `core.auth.i_h6kt_nih6_pkt_ni_vi_cng_ng_c`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                                <h6>Học tập</h6>
                                <p>Truy cập tài liệu và khóa học</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Email Verification Notice --}}
            <div class=`
- **Suggested key:** `core.auth.i_h6hc_tph6_ptruy_cp_ti_liu_v_`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                        <h5>Xác minh email</h5>
                        <p>
                            Chúng tôi đã gửi email xác minh đến: 
                            <strong>{{ $user->email }}</strong>
                        </p>
                        <p class=`
- **Suggested key:** `core.auth._h5xc_minh_emailh5_p_chng_ti_g`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                            Vui lòng kiểm tra hộp thư (bao gồm cả thư mục spam) và click vào link xác minh.
                        </p>
                    </div>
                </div>
                
                <div class=`
- **Suggested key:** `core.auth._vui_lng_kim_tra_hp_th_bao_gm_`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                            Gửi lại email xác minh
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class=`
- **Suggested key:** `core.auth.i_gi_li_email_xc_minh_button_f`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                        Đi đến Dashboard
                    </a>
                @else
                    <a href=`
- **Suggested key:** `core.auth.i_i_n_dashboard_a_else_a_href`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                        Đi đến Dashboard
                    </a>
                @endif
                
                <a href=`
- **Suggested key:** `core.auth.i_i_n_dashboard_a_endif_a_href`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    Hoàn thiện Profile
                </a>
            </div>
            
            <div class=`
- **Suggested key:** `core.auth.i_hon_thin_profile_a_div_div_c`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Cần hỗ trợ?</p>
                <div class=`
- **Suggested key:** `core.auth.cn_h_trp_div_class`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                        Hướng dẫn bắt đầu
                    </a>
                    <a href=`
- **Suggested key:** `core.auth.i_hng_dn_bt_u_a_a_href`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                        Liên hệ hỗ trợ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push(`
- **Suggested key:** `core.auth.i_lin_h_h_tr_a_div_div_div_div`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Confirm Password`
- **Suggested key:** `core.auth.confirm_password`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Page Title`
- **Suggested key:** `core.auth.page_title`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Password`
- **Suggested key:** `core.auth.password`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Confirm`
- **Suggested key:** `core.auth.confirm`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Quên mật khẩu`
- **Suggested key:** `core.auth.qun_mt_khu`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Khôi phục mật khẩu`
- **Suggested key:** `core.auth.khi_phc_mt_khu`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Lấy lại quyền truy cập vào tài khoản của bạn`
- **Suggested key:** `core.auth.ly_li_quyn_truy_cp_vo_ti_khon_`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Quên mật khẩu?</h2>
        <p class=`
- **Suggested key:** `core.auth.qun_mt_khuh2_p_class`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Không sao cả! Chúng tôi sẽ gửi link khôi phục mật khẩu đến email của bạn</p>
    </div>

    <!-- Status Messages -->
    @if (session(`
- **Suggested key:** `core.auth.khng_sao_c_chng_ti_s_gi_link_k`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Địa chỉ email
            </label>
            <input id=`
- **Suggested key:** `core.auth.ia_ch_email_label_input_id`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Nhập địa chỉ email đã đăng ký`
- **Suggested key:** `core.auth.nhp_a_ch_email_ng_k`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                Chúng tôi sẽ gửi link khôi phục mật khẩu đến email này
            </small>
        </div>

        <!-- Submit Button -->
        <div class=`
- **Suggested key:** `core.auth.i_chng_ti_s_gi_link_khi_phc_mt`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Gửi link khôi phục
            </button>
        </div>

        <!-- Back to Login -->
        <div class=`
- **Suggested key:** `core.auth.igi_link_khi_phc_button_div_ba`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Quay lại đăng nhập
            </a>
        </div>
    </form>

    <!-- Help Section -->
    <div class=`
- **Suggested key:** `core.auth.iquay_li_ng_nhp_a_div_form_hel`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Cần hỗ trợ?
        </h6>
        <div class=`
- **Suggested key:** `core.auth.icn_h_tr_h6_div_class`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    <div>
                        <strong>Không nhận được email?</strong>
                        <p class=`
- **Suggested key:** `core.auth.i_div_strongkhng_nhn_c_emailst`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Kiểm tra thư mục spam hoặc thử lại sau 5 phút</p>
                    </div>
                </div>
            </div>
            <div class=`
- **Suggested key:** `core.auth.kim_tra_th_mc_spam_hoc_th_li_s`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    <div>
                        <strong>Email không tồn tại?</strong>
                        <p class=`
- **Suggested key:** `core.auth.i_div_strongemail_khng_tn_tist`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                            Hãy <a href=`
- **Suggested key:** `core.auth._hy_a_href`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>tạo tài khoản mới</a>
                            hoặc liên hệ hỗ trợ
                        </p>
                    </div>
                </div>
            </div>
            <div class=`
- **Suggested key:** `core.auth.to_ti_khon_mia_hoc_lin_h_h_tr_`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    <div>
                        <strong>Vẫn gặp vấn đề?</strong>
                        <p class=`
- **Suggested key:** `core.auth.i_div_strongvn_gp_vn_strong_p_`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                            Liên hệ: <a href=`
- **Suggested key:** `core.auth._lin_h_a_href`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Đang gửi...`
- **Suggested key:** `core.auth.iang_gi`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Loại tài khoản
            </label>
            <select id=`
- **Suggested key:** `core.auth.iloi_ti_khon_label_select_id`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Chọn loại tài khoản của bạn</option>

                <optgroup label=`
- **Suggested key:** `core.auth.chn_loi_ti_khon_ca_bnoption_op`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `}}>
                        Thành viên - Tham gia thảo luận và chia sẻ kiến thức
                    </option>

                </optgroup>

                <optgroup label=`
- **Suggested key:** `core.auth._thnh_vin_tham_gia_tho_lun_v_c`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `}}>
                        Nhà sản xuất - Sản xuất và cung cấp sản phẩm cơ khí
                    </option>
                    <option value=`
- **Suggested key:** `core.auth._nh_sn_xut_sn_xut_v_cung_cp_sn`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `}}>
                        Nhà cung cấp - Phân phối thiết bị và vật tư cơ khí
                    </option>
                    <option value=`
- **Suggested key:** `core.auth._nh_cung_cp_phn_phi_thit_b_v_v`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `}}>
                        Nhãn hàng - Quảng bá thương hiệu và sản phẩm
                    </option>
                </optgroup>
            </select>
            @error(`
- **Suggested key:** `core.auth._nhn_hng_qung_b_thng_hiu_v_sn_`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Chọn loại tài khoản phù hợp với mục đích sử dụng. Bạn có thể thay đổi sau này.</small>
        </div>

        <!-- Terms and Privacy -->
        <div class=`
- **Suggested key:** `core.auth.chn_loi_ti_khon_ph_hp_vi_mc_ch`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                    Tôi đồng ý với <a href=`
- **Suggested key:** `core.auth._ti_ng_vi_a_href`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Điều khoản sử dụng</a> và
                    <a href=`
- **Suggested key:** `core.auth.iu_khon_s_dnga_v_a_href`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Chính sách bảo mật</a>
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class=`
- **Suggested key:** `core.auth.chnh_sch_bo_mta_label_div_div_`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Tạo tài khoản
            </button>
        </div>

        <!-- Login Link -->
        <div class=`
- **Suggested key:** `core.auth.ito_ti_khon_button_div_login_l`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Đã có tài khoản? </span>
            <a href=`
- **Suggested key:** `core.auth._c_ti_khon_span_a_href`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                Đăng nhập ngay
            </a>
        </div>
    </form>
</x-auth-layout>

@push(`
- **Suggested key:** `core.auth._ng_nhp_ngay_a_div_form_xauthl`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Đăng ký tài khoản MechaMap`
- **Suggested key:** `core.auth.ng_k_ti_khon_mechamap`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                🎉 Quy trình đăng ký mới!
            </h1>
            <p class=`
- **Suggested key:** `core.auth._quy_trnh_ng_k_mi_h1_p_class`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                Chúng tôi đã cải tiến trải nghiệm đăng ký để phục vụ bạn tốt hơn
            </p>
        </div>

        {{-- Content --}}
        <div class=`
- **Suggested key:** `core.auth._chng_ti_ci_tin_tri_nghim_ng_k`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    </div>
                    <h5>Quy trình từng bước</h5>
                    <p>Đăng ký dễ dàng với hướng dẫn rõ ràng từng bước</p>
                </div>

                <div class=`
- **Suggested key:** `core.auth.i_div_h5quy_trnh_tng_bch5_png_`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    </div>
                    <h5>Xác thực thời gian thực</h5>
                    <p>Kiểm tra thông tin ngay lập tức, tránh lỗi khi submit</p>
                </div>

                <div class=`
- **Suggested key:** `core.auth.i_div_h5xc_thc_thi_gian_thch5_`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    </div>
                    <h5>Thông tin doanh nghiệp</h5>
                    <p>Dành riêng cho đối tác kinh doanh với form chuyên biệt</p>
                </div>

                <div class=`
- **Suggested key:** `core.auth.i_div_h5thng_tin_doanh_nghiph5`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    </div>
                    <h5>Tối ưu mobile</h5>
                    <p>Trải nghiệm mượt mà trên mọi thiết bị</p>
                </div>

                <div class=`
- **Suggested key:** `core.auth.i_div_h5ti_u_mobileh5_ptri_ngh`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    </div>
                    <h5>Lưu tự động</h5>
                    <p>Không lo mất dữ liệu khi điền form</p>
                </div>

                <div class=`
- **Suggested key:** `core.auth.i_div_h5lu_t_ngh5_pkhng_lo_mt_`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    </div>
                    <h5>Dễ tiếp cận</h5>
                    <p>Hỗ trợ đầy đủ cho người khuyết tật</p>
                </div>
            </div>

            <div class=`
- **Suggested key:** `core.auth.i_div_h5d_tip_cnh5_ph_tr_y_cho`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    <strong>Lưu ý:</strong> Bạn sẽ được chuyển hướng tự động đến quy trình đăng ký mới trong <span id=`
- **Suggested key:** `core.auth.i_stronglu_strong_bn_s_c_chuyn`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>5</span> giây.
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class=`
- **Suggested key:** `core.auth.5span_giy_div_div_div_actions_`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    Bắt đầu đăng ký ngay
                </a>

                <a href=`
- **Suggested key:** `core.auth.i_bt_u_ng_k_ngay_a_a_href`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    Đã có tài khoản? Đăng nhập
                </a>
            </div>

            <div class=`
- **Suggested key:** `core.auth.i_c_ti_khon_ng_nhp_a_div_div_c`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    Cần hỗ trợ? <a href=`
- **Suggested key:** `core.auth.i_cn_h_tr_a_href`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Liên hệ với chúng tôi</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push(`
- **Suggested key:** `core.auth.lin_h_vi_chng_tia_p_div_div_di`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Đặt lại mật khẩu`
- **Suggested key:** `core.auth.t_li_mt_khu`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Tạo mật khẩu mới cho tài khoản của bạn`
- **Suggested key:** `core.auth.to_mt_khu_mi_cho_ti_khon_ca_bn`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Tạo mật khẩu mới</h2>
        <p class=`
- **Suggested key:** `core.auth.to_mt_khu_mih2_p_class`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Vui lòng nhập mật khẩu mới cho tài khoản của bạn</p>
    </div>

    <!-- Status Messages -->
    @if (session(`
- **Suggested key:** `core.auth.vui_lng_nhp_mt_khu_mi_cho_ti_k`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                Đây là địa chỉ email được liên kết với tài khoản của bạn
            </small>
        </div>

        <!-- New Password -->
        <div class=`
- **Suggested key:** `core.auth.i_y_l_a_ch_email_c_lin_kt_vi_t`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Mật khẩu mới
            </label>
            <div class=`
- **Suggested key:** `core.auth.imt_khu_mi_label_div_class`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Nhập mật khẩu mới`
- **Suggested key:** `core.auth.nhp_mt_khu_mi`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Sử dụng ít nhất 8 ký tự với chữ cái, số và ký hiệu</small>

            <!-- Password Strength Indicator -->
            <div class=`
- **Suggested key:** `core.auth.s_dng_t_nht_8_k_t_vi_ch_ci_s_v`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Xác nhận mật khẩu mới
            </label>
            <div class=`
- **Suggested key:** `core.auth.ixc_nhn_mt_khu_mi_label_div_cl`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Nhập lại mật khẩu mới`
- **Suggested key:** `core.auth.nhp_li_mt_khu_mi`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Cập nhật mật khẩu
            </button>
        </div>

        <!-- Back to Login -->
        <div class=`
- **Suggested key:** `core.auth.icp_nht_mt_khu_button_div_back`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Quay lại đăng nhập
            </a>
        </div>
    </form>

    <!-- Security Tips -->
    <div class=`
- **Suggested key:** `core.auth.iquay_li_ng_nhp_a_div_form_sec`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Mẹo bảo mật
        </h6>
        <div class=`
- **Suggested key:** `core.auth.imo_bo_mt_h6_div_class`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    <div>
                        <strong>Mật khẩu mạnh</strong>
                        <p class=`
- **Suggested key:** `core.auth.i_div_strongmt_khu_mnhstrong_p`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Sử dụng ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký hiệu</p>
                    </div>
                </div>
            </div>
            <div class=`
- **Suggested key:** `core.auth.s_dng_t_nht_8_k_t_bao_gm_ch_ho`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    <div>
                        <strong>Tránh thông tin cá nhân</strong>
                        <p class=`
- **Suggested key:** `core.auth.i_div_strongtrnh_thng_tin_c_nh`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Không sử dụng tên, ngày sinh, số điện thoại trong mật khẩu</p>
                    </div>
                </div>
            </div>
            <div class=`
- **Suggested key:** `core.auth.khng_s_dng_tn_ngy_sinh_s_in_th`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    <div>
                        <strong>Mật khẩu duy nhất</strong>
                        <p class=`
- **Suggested key:** `core.auth.i_div_strongmt_khu_duy_nhtstro`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Không sử dụng lại mật khẩu từ các tài khoản khác</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-auth-layout>

@push(`
- **Suggested key:** `core.auth.khng_s_dng_li_mt_khu_t_cc_ti_k`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Rất yếu`
- **Suggested key:** `core.auth.rt_yu`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Yếu`
- **Suggested key:** `core.auth.yu`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Trung bình`
- **Suggested key:** `core.auth.trung_bnh`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Mạnh`
- **Suggested key:** `core.auth.mnh`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Rất mạnh`
- **Suggested key:** `core.auth.rt_mnh`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Độ mạnh:`
- **Suggested key:** `core.auth._mnh`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Mật khẩu khớp</small>`
- **Suggested key:** `core.auth.imt_khu_khpsmall`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Mật khẩu không khớp</small>`
- **Suggested key:** `core.auth.imt_khu_khng_khpsmall`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Đang cập nhật...`
- **Suggested key:** `core.auth.iang_cp_nht`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `••••••••`
- **Suggested key:** `core.auth.`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Xác minh email`
- **Suggested key:** `core.auth.xc_minh_email`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `Hoàn tất quá trình đăng ký tài khoản`
- **Suggested key:** `core.auth.hon_tt_qu_trnh_ng_k_ti_khon`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Kiểm tra email của bạn</h2>
        <p class=`
- **Suggested key:** `core.auth.kim_tra_email_ca_bnh2_p_class`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Chúng tôi đã gửi link xác minh đến email của bạn</p>
    </div>

    <!-- Status Messages -->
    @if (session(`
- **Suggested key:** `core.auth.chng_ti_gi_link_xc_minh_n_emai`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
            Link xác minh mới đã được gửi đến địa chỉ email bạn đã đăng ký.
        </div>
    @endif

    <!-- Instructions -->
    <div class=`
- **Suggested key:** `core.auth.i_link_xc_minh_mi_c_gi_n_a_ch_`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Hướng dẫn xác minh
        </h6>
        <ol class=`
- **Suggested key:** `core.auth.ihng_dn_xc_minh_h6_ol_class`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Mở ứng dụng email trên thiết bị của bạn</li>
            <li class=`
- **Suggested key:** `core.auth.m_ng_dng_email_trn_thit_b_ca_b`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Tìm email từ <strong>MechaMap</strong> với tiêu đề`
- **Suggested key:** `core.auth.tm_email_t_strongmechamapstron`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Nhấp vào nút <strong>`
- **Suggested key:** `core.auth.nhp_vo_nt_strong`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Quay lại trang này để tiếp tục</li>
        </ol>
    </div>

    <!-- Action Buttons -->
    <div class=`
- **Suggested key:** `core.auth.quay_li_trang_ny_tip_tcli_ol_d`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Gửi lại email xác minh
                </button>
            </form>
        </div>
        <div class=`
- **Suggested key:** `core.auth.igi_li_email_xc_minh_button_fo`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Đăng xuất
                </button>
            </form>
        </div>
    </div>

    <!-- Help Section -->
    <div class=`
- **Suggested key:** `core.auth.ing_xut_button_form_div_div_he`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    <div>
                        <strong>Không tìm thấy email?</strong>
                        <p class=`
- **Suggested key:** `core.auth.i_div_strongkhng_tm_thy_emails`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Kiểm tra thư mục spam/junk mail hoặc thư mục quảng cáo</p>
                    </div>
                </div>
            </div>
            <div class=`
- **Suggested key:** `core.auth.kim_tra_th_mc_spamjunk_mail_ho`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                    <div>
                        <strong>Email chưa đến?</strong>
                        <p class=`
- **Suggested key:** `core.auth.i_div_strongemail_cha_nstrong_`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>Đợi 2-3 phút rồi kiểm tra lại, hoặc nhấn`
- **Suggested key:** `core.auth.i_23_pht_ri_kim_tra_li_hoc_nhn`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                            Đăng xuất và <a href=`
- **Suggested key:** `core.auth._ng_xut_v_a_href`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>đăng ký lại</a>
                            với email đúng
                        </p>
                    </div>
                </div>
            </div>
            <div class=`
- **Suggested key:** `core.auth.ng_k_lia_vi_email_ng_p_div_div`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `>
                            Liên hệ hỗ trợ: <a href=`
- **Suggested key:** `core.auth._lin_h_h_tr_a_href`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
            Trang này sẽ tự động làm mới sau khi bạn xác minh email
        </small>
    </div>
</x-auth-layout>

@push(`
- **Suggested key:** `core.auth.i_trang_ny_s_t_ng_lm_mi_sau_kh`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>
                </div>
                <h3>Email đã được xác minh!</h3>
                <p>Đang chuyển hướng...</p>
                <div class=`
- **Suggested key:** `core.auth.i_div_h3email_c_xc_minhh3_pang`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `></i>Gửi lại email xác minh`
- **Suggested key:** `core.auth.igi_li_email_xc_minh`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

### Text: `XMLHttpRequest`
- **Suggested key:** `core.auth.xmlhttprequest`
- **Helper function:** `t_core()`
- **Blade directive:** `@core()`

