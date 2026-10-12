from pathlib import Path
import subprocess
from .safety import safe_target, git_output

IGNORE_DIRS = {".git", ".venv", "node_modules", ".next", "dist", "build", "__pycache__"}
IGNORE_FILES = {".env", ".env.local", ".env.production", "id_rsa", "id_ed25519"}

def summarize_workspace(workspace: Path, max_files: int = 80) -> str:
    entries = []
    for p in workspace.rglob("*"):
        if any(part in IGNORE_DIRS for part in p.parts):
            continue
        if p.is_file() and p.name not in IGNORE_FILES:
            try:
                rel = p.relative_to(workspace).as_posix()
                entries.append(rel)
            except ValueError:
                continue
        if len(entries) >= max_files:
            break
    return "\n".join(entries) or "(workspace trống hoặc không tìm thấy file)"

def read_relevant_files(workspace: Path, paths: list[str], max_chars: int = 12000) -> dict[str, str]:
    result, remaining = {}, max_chars
    for rel in paths[:20]:
        try:
            p = safe_target(workspace, rel)
            if not p.is_file() or p.stat().st_size > 150_000:
                continue
            data = p.read_text(encoding="utf-8", errors="replace")
            data = data[:remaining]
            result[rel] = data
            remaining -= len(data)
            if remaining <= 0:
                break
        except (ValueError, OSError):
            continue
    return result

def apply_changes(workspace: Path, changes: list[dict], max_files: int = 20) -> list[str]:
    if len(changes) > max_files:
        raise RuntimeError(f"Developer đề xuất {len(changes)} file, vượt giới hạn {max_files}.")
    changed = []
    for item in changes:
        rel = str(item.get("path", ""))
        content = item.get("content")
        if not rel or not isinstance(content, str):
            raise ValueError("Mỗi thay đổi cần path và content dạng chuỗi.")
        target = safe_target(workspace, rel)
        target.parent.mkdir(parents=True, exist_ok=True)
        target.write_text(content, encoding="utf-8", newline="\n")
        changed.append(rel)
    return changed

def get_diff(workspace: Path) -> str:
    p = subprocess.run(
        ["git", "-C", str(workspace), "diff", "--no-ext-diff", "--"],
        capture_output=True, text=True, timeout=30, check=False
    )
    if p.returncode != 0:
        return f"Không lấy được git diff: {p.stderr}"
    untracked = subprocess.run(
        ["git", "-C", str(workspace), "ls-files", "--others", "--exclude-standard"],
        capture_output=True, text=True, timeout=30, check=False
    )
    extra = ""
    if untracked.stdout.strip():
        extra_parts = []
        for rel in untracked.stdout.splitlines():
            try:
                target = safe_target(workspace, rel.strip())
                if target.is_file() and target.stat().st_size <= 150_000:
                    content = target.read_text(encoding="utf-8", errors="replace")
                    extra_parts.append(
                        f"\\n--- UNTRACKED FILE: {rel} ---\\n{content}\\n--- END FILE ---\\n"
                    )
                else:
                    extra_parts.append(f"\\n--- UNTRACKED FILE: {rel} (nội dung không đọc được hoặc quá lớn) ---\\n")
            except (ValueError, OSError):
                extra_parts.append(f"\\n--- UNTRACKED FILE bị bỏ qua: {rel} ---\\n")
        extra = "\\n".join(extra_parts)
    return p.stdout + extra
