import json
from pathlib import Path
from langchain_core.messages import SystemMessage, HumanMessage
from .model_router import get_chat_model

ROOT = Path(__file__).resolve().parents[1]

def read_prompt(role: str) -> str:
    return (ROOT / "agents" / f"{role}.md").read_text(encoding="utf-8")

def invoke_json(role: str, payload: dict) -> dict:
    model = get_chat_model(role)
    messages = [
        SystemMessage(content=read_prompt(role)),
        HumanMessage(content=json.dumps(payload, ensure_ascii=False, indent=2)),
    ]
    response = model.invoke(messages)
    content = response.content
    if isinstance(content, list):
        content = "\n".join(
            item.get("text", "") if isinstance(item, dict) else str(item)
            for item in content
        )
    text = str(content).strip()
    if text.startswith("```"):
        text = text.split("\n", 1)[-1]
        if text.endswith("```"):
            text = text[:-3]
    try:
        return json.loads(text)
    except json.JSONDecodeError as exc:
        raise RuntimeError(f"{role} không trả JSON hợp lệ: {text[:1200]}") from exc
