# AI Dev Team — Antigravity / VS Code

Bộ khung khởi đầu cho đội ngũ 4 AI:
1. Architect — phân tích yêu cầu, lập kế hoạch.
2. Developer — đề xuất thay đổi file trong workspace.
3. Reviewer — review độc lập dựa trên Git diff.
4. QA — chạy các lệnh kiểm tra được cho phép và báo cáo kết quả.

> Đây là starter scaffold có cổng an toàn, không phải hệ thống tự trị đã được kiểm chứng cho production. Hãy thử trên dự án demo hoặc bản sao Git trước.

## Yêu cầu
- Windows 10/11
- Python 3.11+
- Git
- API key của ít nhất một nhà cung cấp được cấu hình
- Antigravity có thể mở thư mục này để đọc `AGENTS.md` và hướng dẫn trong `agents/`

## Cài đặt nhanh (PowerShell)
```powershell
cd ai-dev-team-antigravity
py -3.11 -m venv .venv
.\.venv\Scripts\Activate.ps1
python -m pip install --upgrade pip
pip install -r requirements.txt
Copy-Item .env.example .env
```

Mở `.env` và điền API key + model ID hợp lệ của tài khoản bạn. Không commit `.env`.

## Cấu hình
Mở `config/models.yaml`. Mỗi vai trò có thể dùng provider/model riêng. Model ID là ví dụ; hãy thay bằng ID hiện có trong tài khoản của bạn.

Provider được hỗ trợ:
- `openai`
- `anthropic`
- `google`

Nếu muốn dùng chung một provider cho tiết kiệm, đặt cùng provider/model cho nhiều vai trò.

## Chạy
```powershell
python -m orchestrator.main --workspace "C:\duong-dan\du-an-cua-ban" --task "Phân tích dự án và đề xuất module đăng nhập"
```

Lần đầu, nên dùng một thư mục demo và task chỉ yêu cầu phân tích. Khi cho phép sửa code, hãy kiểm tra Git sạch trước:
```powershell
git -C "C:\duong-dan\du-an-cua-ban" status --short
```

## Quy tắc an toàn
- Mặc định không chạy lệnh shell tùy ý.
- QA chỉ chạy command được liệt kê trong `config/commands.yaml`.
- Chỉ cho phép sửa file trong workspace và chặn các đường dẫn nhạy cảm.
- Tạo diff và backup trước khi áp dụng.
- Giới hạn số vòng review và số file thay đổi. Biến ngân sách trong `.env` là cấu hình dự kiến; cần bổ sung bộ tính chi phí theo bảng giá model thực tế trước khi coi đó là hard cap.
- Không tự deploy production, xóa dữ liệu, chạy migration phá hủy hoặc thao tác với credential.
- AI output vẫn có thể sai. Reviewer/QA không thay thế review của con người.

## Trạng thái hiện tại và giới hạn
- Đây là MVP scaffold để Antigravity tiếp tục hoàn thiện và kiểm tra trên môi trường của bạn.
- `AI_MAX_API_BUDGET_USD` hiện chưa được thực thi như hard cap; không chạy tự động với ngân sách quan trọng cho đến khi thêm bộ tính chi phí/giới hạn phía provider.
- Bộ điều phối hiện dùng task đầu tiên trong kế hoạch Architect; chưa có task queue đầy đủ cho nhiều module.
- Hãy kiểm tra command allowlist cho đúng stack dự án; test không có executable sẽ hiện `NOT_RUN`.
- Nên dùng repo demo, vì việc ghi file từ AI là thao tác thật trên workspace.

## Tích hợp Antigravity
1. Mở thư mục này trong Antigravity.
2. Đọc `AGENTS.md` và `agents/*.md`.
3. Nếu Antigravity hỗ trợ project rules, hãy giữ nguyên các quy tắc trong `AGENTS.md`.
4. Mở workspace dự án đích riêng; không đặt API key trong source.
5. Để Antigravity hoàn thiện phần tích hợp editor theo đúng môi trường của bạn; bộ điều phối Python có thể chạy độc lập từ terminal.

## Cấu trúc
- `orchestrator/` — luồng LangGraph và CLI.
- `agents/` — system prompts của 4 vai trò.
- `config/` — model routing, command allowlist.
- `workspace/` — thư mục demo (không phải dự án đích).
- `runs/` — báo cáo JSON của mỗi lần chạy.
- `AGENTS.md` — hướng dẫn cho Antigravity.
