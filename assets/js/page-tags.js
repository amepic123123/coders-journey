import { apiFetch, escapeHtml } from "./api.js";
import { renderNavAuth } from "./session.js";

await renderNavAuth();

const gridEl = document.getElementById("tags-grid");
const emptyEl = document.getElementById("tags-empty");
const errorEl = document.getElementById("tags-error");

function renderTagGroup(group) {
  const name = group?.name ?? group?.tag ?? "";
  const description = group?.description ?? "";
  const count = group?.count ?? group?.questions_count ?? 0;
  const subTags = group?.sub_tags ?? group?.subTags ?? [];

  const subs = Array.isArray(subTags) ? subTags : String(subTags).split(",").map((t) => t.trim()).filter(Boolean);

  return `
    <div class="tag-card">
      <div class="tag-header">
        <a href="tag.html?name=${encodeURIComponent(name)}" class="tag-title">${escapeHtml(name)}</a>
        <span class="tag-count">${escapeHtml(count)} questions</span>
      </div>

      <p class="tag-desc">${escapeHtml(description)}</p>

      <div class="sub-tags-container">
        ${subs
          .map(
            (sub) => `<a href="tag.html?name=${encodeURIComponent(sub)}" class="sub-tag-link">${escapeHtml(sub)}</a>`
          )
          .join("")}
      </div>
    </div>
  `;
}

(async () => {
  try {
    const data = await apiFetch("/actions/fetch_all_tags.php");
    const groups = Array.isArray(data) ? data : (data?.tags ?? data?.tag_groups ?? []);

    if (!groups.length) {
      emptyEl.style.display = "block";
      gridEl.innerHTML = "";
      return;
    }

    gridEl.innerHTML = groups.map(renderTagGroup).join("");
  } catch (e) {
    errorEl.style.display = "block";
    errorEl.textContent = `Backend not connected yet: ${e.message}`;
  }
})();
