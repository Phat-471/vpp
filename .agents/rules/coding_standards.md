# QUY CHUẨN KỸ THUẬT & QUY TẮC PHÁT TRIỂN DỰ ÁN (AI CODING DIRECTIVES)

Tài liệu này đồng bộ cùng [AGENTS.md](file:///d:/1/phat/vpp/AGENTS.md).

## Tóm tắt 6 nguyên tắc cốt lõi:
1. **Zero Guesswork:** Cái gì không rõ nghiệp vụ BẮT BUỘC phải hỏi người dùng, không được code bừa.
2. **DRY Principle:** Không lặp code; tách các logic dùng >= 2 lần thành Service/Trait/Helper dùng chung.
3. **Dependency Lean:** Chỉ dùng thư viện nhẹ; nếu thư viện lớn chỉ dùng 1 tính năng nhỏ thì BẮT BUỘC tự code nội bộ.
4. **Strict Input Validation:** Xác thực 2 lớp (Client & Server) trước khi submit; làm sạch SĐT, ép kiểu số tiền, chống IDOR, SQL Injection, XSS.
5. **UI-UX-ProMax (100% Tiếng Việt):** Giao diện tiếng Việt chuẩn mực, mobile-first, bảng màu hiện đại, micro-animations, empty states trực quan.
6. **Data Integrity & Transactions:** Luôn dùng `DB::transaction` cho mọi biến động kho và tiền; luôn Eager Load `with()` chống N+1 Query.
