from typing import Any, TypedDict

class TeamState(TypedDict, total=False):
    workspace: str
    user_task: str
    run_id: str
    repo_summary: str
    plan: dict[str, Any]
    developer_result: dict[str, Any]
    changed_files: list[str]
    diff: str
    review: dict[str, Any]
    qa: dict[str, Any]
    qa_execution: dict[str, Any]
    loops: int
    budget_estimate_usd: float
    status: str
    error: str
