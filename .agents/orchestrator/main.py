import argparse, json, os, uuid
from pathlib import Path
from dotenv import load_dotenv
from .graph import build_graph
from .safety import ensure_git_clean

ROOT = Path(__file__).resolve().parents[1]

def main():
    load_dotenv(ROOT / ".env")
    parser = argparse.ArgumentParser(description="AI Dev Team — 4-agent workflow")
    parser.add_argument("--workspace", required=True, help="Đường dẫn repo Git dự án đích")
    parser.add_argument("--task", required=True, help="Yêu cầu cần thực hiện")
    parser.add_argument("--analyze-only", action="store_true", help="Chỉ in hướng dẫn; chưa hỗ trợ chạy agent")
    args = parser.parse_args()
    workspace = Path(args.workspace).resolve()
    if not workspace.exists() or not workspace.is_dir():
        raise SystemExit(f"Workspace không tồn tại: {workspace}")
    if args.analyze_only:
        print("Chế độ analyze-only hiện chỉ là cờ dự phòng. Chưa gọi AI.")
        return
    try:
        ensure_git_clean(workspace)
    except Exception as exc:
        raise SystemExit(f"Dừng vì an toàn: {exc}")
    run_id = str(uuid.uuid4())
    graph = build_graph()
    initial = {
        "workspace": str(workspace),
        "user_task": args.task,
        "run_id": run_id,
        "loops": 0,
        "status": "STARTED",
    }
    try:
        result = graph.invoke(initial, {"recursion_limit": 20})
    except Exception as exc:
        result = {"run_id": run_id, "status": "ERROR", "error": str(exc)}
    report_dir = ROOT / "runs"
    report_dir.mkdir(exist_ok=True)
    report_path = report_dir / f"{run_id}.json"
    report_path.write_text(json.dumps(result, ensure_ascii=False, indent=2, default=str), encoding="utf-8")
    print(json.dumps({
        "run_id": run_id,
        "status": result.get("status"),
        "changed_files": result.get("changed_files", []),
        "review": result.get("review", {}),
        "qa": result.get("qa", {}),
        "qa_execution": result.get("qa_execution", {}),
        "report": str(report_path),
    }, ensure_ascii=False, indent=2))
    print("\nLưu ý: kiểm tra diff bằng git trước khi chấp nhận bất kỳ thay đổi nào.")

if __name__ == "__main__":
    main()
