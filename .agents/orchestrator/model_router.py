import os
from pathlib import Path
import yaml
from dotenv import load_dotenv

ROOT = Path(__file__).resolve().parents[1]
load_dotenv(ROOT / ".env")

def load_models():
    with (ROOT / "config" / "models.yaml").open("r", encoding="utf-8") as f:
        return yaml.safe_load(f)

def get_chat_model(role: str):
    cfg = load_models()[role]
    provider = cfg["provider"].lower()
    model = os.path.expandvars(cfg["model"])
    if not model or model.startswith("REPLACE_"):
        raise RuntimeError(f"Chưa cấu hình model ID cho role '{role}' trong config/models.yaml")
    temperature = float(cfg.get("temperature", 0))
    if provider == "openai":
        if not os.getenv("OPENAI_API_KEY"):
            raise RuntimeError("Thiếu OPENAI_API_KEY trong .env")
        from langchain_openai import ChatOpenAI
        return ChatOpenAI(model=model, temperature=temperature)
    if provider == "anthropic":
        if not os.getenv("ANTHROPIC_API_KEY"):
            raise RuntimeError("Thiếu ANTHROPIC_API_KEY trong .env")
        from langchain_anthropic import ChatAnthropic
        return ChatAnthropic(model=model, temperature=temperature)
    if provider == "google":
        if not os.getenv("GOOGLE_API_KEY"):
            raise RuntimeError("Thiếu GOOGLE_API_KEY trong .env")
        from langchain_google_genai import ChatGoogleGenerativeAI
        return ChatGoogleGenerativeAI(model=model, temperature=temperature)
    raise ValueError(f"Provider chưa hỗ trợ: {provider}")
