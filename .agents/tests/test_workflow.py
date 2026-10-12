from pathlib import Path
import pytest
from orchestrator.safety import safe_target

def test_reject_parent_traversal(tmp_path):
    with pytest.raises(ValueError):
        safe_target(tmp_path, "../outside.txt")

def test_reject_env_file(tmp_path):
    with pytest.raises(ValueError):
        safe_target(tmp_path, ".env")

def test_allow_normal_file(tmp_path):
    result = safe_target(tmp_path, "src/app.py")
    assert result == (tmp_path / "src" / "app.py").resolve()
