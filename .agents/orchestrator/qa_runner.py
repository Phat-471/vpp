from pathlib import Path
import subprocess
import yaml

ROOT = Path(__file__).resolve().parents[1]

def run_allowed_tests(workspace: Path) -> dict:
    cfg = yaml.safe_load((ROOT / "config" / "commands.yaml").read_text(encoding="utf-8"))
    timeout = int(cfg.get("timeout_seconds", 180))
    results = []
    for command in cfg.get("allowed", []):
        # Chỉ chạy lệnh có executable đầu tiên tồn tại; không dùng shell=True.
        try:
            p = subprocess.run(
                command, cwd=str(workspace), capture_output=True, text=True,
                timeout=timeout, shell=False, check=False
            )
            results.append({
                "command": command,
                "status": "PASS" if p.returncode == 0 else "FAIL",
                "returncode": p.returncode,
                "stdout": p.stdout[-5000:],
                "stderr": p.stderr[-5000:],
            })
        except FileNotFoundError:
            results.append({"command": command, "status": "NOT_RUN", "reason": "Executable/dependency không có"})
        except subprocess.TimeoutExpired:
            results.append({"command": command, "status": "FAIL", "reason": f"Timeout sau {timeout}s"})
    executed = [r for r in results if r["status"] in ("PASS", "FAIL")]
    overall = "NOT_RUN" if not executed else ("PASS" if all(r["status"] == "PASS" for r in executed) and all(r["status"] != "NOT_RUN" for r in results) else "FAIL")
    return {"status": overall, "results": results}
