from pathlib import Path
from langgraph.graph import StateGraph, START, END
from .state import TeamState
from .agents import invoke_json
from .workspace import summarize_workspace, read_relevant_files, apply_changes, get_diff
from .safety import ensure_git_clean
from .qa_runner import run_allowed_tests

def build_graph():
    def architect(state: TeamState):
        ws = Path(state["workspace"])
        repo = summarize_workspace(ws)
        plan = invoke_json("architect", {
            "user_task": state["user_task"],
            "workspace_file_list": repo,
        })
        return {"repo_summary": repo, "plan": plan, "status": "PLANNED"}

    def developer(state: TeamState):
        ws = Path(state["workspace"])
        plan = state.get("plan", {})
        tasks = plan.get("tasks", [])
        task = tasks[0] if tasks else {"title": state["user_task"], "scope": []}
        scope = task.get("scope", [])
        files = read_relevant_files(ws, scope)
        result = invoke_json("developer", {
            "user_task": state["user_task"],
            "task": task,
            "plan": plan,
            "current_files": files,
            "previous_review": state.get("review", {}),
            "previous_qa": state.get("qa", {}),
        })
        changed = apply_changes(ws, result.get("changes", []))
        loops = state.get("loops", 0) + (1 if state.get("review") else 0)
        return {"developer_result": result, "changed_files": changed, "diff": get_diff(ws),
                "loops": loops, "status": "CODE_CHANGED"}

    def reviewer(state: TeamState):
        result = invoke_json("reviewer", {
            "user_task": state["user_task"],
            "plan": state.get("plan", {}),
            "changed_files": state.get("changed_files", []),
            "git_diff": state.get("diff", ""),
        })
        return {"review": result, "status": "REVIEWED"}

    def route_review(state: TeamState):
        review = state.get("review", {})
        if review.get("status") == "APPROVED":
            return "qa"
        if state.get("loops", 0) >= int(__import__("os").getenv("AI_MAX_REVIEW_LOOPS", "2")):
            return "finish_blocked"
        return "developer"

    def qa(state: TeamState):
        ws = Path(state["workspace"])
        execution = run_allowed_tests(ws)
        ai_report = invoke_json("qa", {
            "user_task": state["user_task"],
            "acceptance_criteria": state.get("plan", {}),
            "actual_command_results": execution,
        })
        status = "PASS" if execution["status"] == "PASS" and ai_report.get("status") == "PASS" else "FAIL"
        return {"qa_execution": execution, "qa": ai_report, "status": status}

    def after_developer(state: TeamState):
        return {"loops": state.get("loops", 0) + (1 if state.get("review") else 0)}

    def finish_blocked(state: TeamState):
        return {"status": "NEEDS_HUMAN_REVIEW"}

    graph = StateGraph(TeamState)
    graph.add_node("architect", architect)
    graph.add_node("developer", developer)
    graph.add_node("reviewer", reviewer)
    graph.add_node("qa", qa)
    graph.add_node("finish_blocked", finish_blocked)
    graph.add_edge(START, "architect")
    graph.add_edge("architect", "developer")
    graph.add_edge("developer", "reviewer")
    graph.add_conditional_edges("reviewer", route_review, {"qa": "qa", "developer": "developer", "finish_blocked": "finish_blocked"})
    graph.add_edge("qa", END)
    graph.add_edge("finish_blocked", END)
    return graph.compile()
