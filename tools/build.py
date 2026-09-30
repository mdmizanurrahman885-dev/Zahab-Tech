#!/usr/bin/env python3
"""Minify the plugin stylesheet and package zahab-checkout-style.zip for upload."""
import gzip
import pathlib
import re
import zipfile

ROOT = pathlib.Path(__file__).resolve().parent.parent
PLUGIN = ROOT / "zahab-checkout-style"
CSS = PLUGIN / "assets" / "zahab-checkout.css"
MIN = PLUGIN / "assets" / "zahab-checkout.min.css"
ZIP = ROOT / "zahab-checkout-style.zip"


def minify(css: str) -> str:
    css = re.sub(r"/\*.*?\*/", "", css, flags=re.S)
    css = re.sub(r"\s+", " ", css)
    # Only whitespace next to structural characters is removed; spaces inside selectors are descendant combinators.
    css = re.sub(r"\s*([{};,>])\s*", r"\1", css)
    # Declarations start right after "{" or ";" with a lowercase property name; selectors here never do.
    css = re.sub(r"([{;])(-{0,2}[a-z][a-z0-9-]*): ", r"\1\2:", css)
    css = css.replace(" !important", "!important")
    # Plugin-private custom properties only; nothing outside this file reads them.
    css = css.replace("--zcs-", "--z")
    css = css.replace(";}", "}")
    return css.strip()


def main() -> None:
    out = minify(CSS.read_text(encoding="utf-8"))
    MIN.write_text(out + "\n", encoding="utf-8")
    raw = len(out.encode())
    gz = len(gzip.compress(out.encode()))
    print(f"min.css: {raw / 1024:.1f} KB, gzip {gz / 1024:.1f} KB")

    if ZIP.exists():
        ZIP.unlink()
    with zipfile.ZipFile(ZIP, "w", zipfile.ZIP_DEFLATED) as z:
        for path in sorted(PLUGIN.rglob("*")):
            if path.is_file():
                z.write(path, path.relative_to(ROOT).as_posix())
    print(f"zip: {ZIP.name} ({ZIP.stat().st_size / 1024:.1f} KB)")


if __name__ == "__main__":
    main()
