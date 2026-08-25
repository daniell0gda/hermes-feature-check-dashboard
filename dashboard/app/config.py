"""Runtime configuration, entirely from environment variables.

Everything durable - the SQLite file and every screenshot - lives under
``data_dir``, which is the single path the container needs mounted. Nothing
persistent is ever written outside it, so a container reinstall loses nothing.
"""

from __future__ import annotations

import os
from dataclasses import dataclass
from pathlib import Path
from typing import Mapping

DEFAULT_DATA_DIR = "/data"
DEFAULT_PORT = 8080
DEFAULT_SITE_NAME = "Hermes"

DATABASE_FILENAME = "dashboard.sqlite3"
MEDIA_DIRNAME = "media"


class ConfigError(RuntimeError):
    """Raised when the environment cannot support a working dashboard."""


@dataclass(frozen=True)
class Settings:
    data_dir: Path
    api_key: str | None
    site_name: str
    host: str
    port: int
    root_path: str

    @property
    def database_path(self) -> Path:
        return self.data_dir / DATABASE_FILENAME

    @property
    def media_dir(self) -> Path:
        return self.data_dir / MEDIA_DIRNAME

    @property
    def writes_enabled(self) -> bool:
        """Without a key there is nothing to authenticate against, so the
        ingest routes stay closed rather than becoming anonymous."""
        return bool(self.api_key)


def load_settings(environ: Mapping[str, str] | None = None) -> Settings:
    env = os.environ if environ is None else environ

    api_key = (env.get("HFCD_API_KEY") or "").strip()
    port_text = (env.get("HFCD_PORT") or str(DEFAULT_PORT)).strip()
    if not port_text.isdigit():
        raise ConfigError(f"HFCD_PORT must be a number, got {port_text!r}")

    root_path = (env.get("HFCD_ROOT_PATH") or "").strip().rstrip("/")
    if root_path and not root_path.startswith("/"):
        root_path = "/" + root_path

    return Settings(
        data_dir=Path((env.get("HFCD_DATA_DIR") or DEFAULT_DATA_DIR).strip()),
        api_key=api_key or None,
        site_name=(env.get("HFCD_SITE_NAME") or DEFAULT_SITE_NAME).strip(),
        host=(env.get("HFCD_HOST") or "0.0.0.0").strip(),
        port=int(port_text),
        root_path=root_path,
    )


def prepare_data_dir(settings: Settings) -> None:
    """Create the data directory and prove it is writable.

    Failing here is deliberate: a dashboard that silently stores runs in the
    container's ephemeral layer looks healthy until the next reinstall wipes it.
    """
    try:
        settings.media_dir.mkdir(parents=True, exist_ok=True)
    except OSError as error:
        raise ConfigError(
            f"cannot create {settings.media_dir}: {error}. "
            f"Mount a writable volume at {settings.data_dir} (see compose.yaml)."
        ) from error

    probe = settings.data_dir / ".write-probe"
    try:
        probe.write_bytes(b"ok")
        probe.unlink()
    except OSError as error:
        raise ConfigError(
            f"{settings.data_dir} is not writable: {error}. "
            f"This container runs as {_process_identity()}, but the directory is "
            f"owned by {_ownership(settings.data_dir)}. Fix it on the host with "
            f"`chown -R <uid>:<gid> <dataset>` to match the container, or set a "
            f"`user:` in compose.yaml to match the dataset. "
            f"This is a volume-permission problem, not a registry/login problem."
        ) from error


def _process_identity() -> str:
    """uid:gid of this process, for an actionable permission error."""
    get_uid = getattr(os, "geteuid", None)
    get_gid = getattr(os, "getegid", None)
    if get_uid is None or get_gid is None:
        return "an unknown uid (non-POSIX host)"

    return f"uid {get_uid()}, gid {get_gid()}"


def _ownership(path: Path) -> str:
    """uid:gid that owns the path, so the error names both sides of the mismatch."""
    try:
        info = path.stat()
    except OSError:
        return "an unreadable owner"

    return f"uid {info.st_uid}, gid {info.st_gid}"
