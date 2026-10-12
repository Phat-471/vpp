from pathlib import Path
import subprocess

BLOCKED_PARTS = {
    ".git", ".venv", "node_modules", "__pycache__", ".next",
}
BLOCKED_NAMES = {
    ".env", ".env.local", ".env.production", "id_rsa", "id_ed25519",
    "credentials.json", "secrets.json",
}

def safe_target(workspace: Path, relative: str) -> Path:
    rel = Path(relative)
    if rel.is_absolute() or ".." in rel.parts:
        raise ValueError(f"Path không an toàn: {relative}")
    if any(part.lower() in BLOCKED_PARTS for part in rel.parts):
        raise ValueError(f"Không được sửa vùng hệ thống/cache: {relative}")
    if rel.name.lower() in BLOCKED_NAMES or rel.name.lower().startswith(".env"):
        raise ValueError(f"Không được sửa file nhạy cảm: {relative}")
    target = (workspace / rel).resolve()
    if workspace.resolve() not in target.parents and target != workspace.resolve():
        raise ValueError(f"Path thoát khỏi workspace: {relative}")
    return target

def git_output(workspace: Path, *args: str) -> str:
    p = subprocess.run(
        ["git", "-C", str(workspace), *args],
        capture_output=True, text=True, timeout=30, check=False
    )
    if p.returncode != 0:
        raise RuntimeError(p.stderr.strip() or "Git command failed")
    return p.stdout

def ensure_git_clean(workspace: Path):
    status = git_output(workspace, "status", "--porcelain")
    if status.strip():
        raise RuntimeError(
            "Workspace có thay đổi chưa commit. Hãy commit/stash hoặc dùng bản sao sạch trước khi chạy AI."
        )
