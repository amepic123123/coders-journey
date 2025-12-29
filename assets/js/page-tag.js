import { apiFetch, escapeHtml, getQueryParam } from "./api.js";
import { renderNavAuth } from "./session.js";

await renderNavAuth();

const tagNameEl = document.getElementById("tag-name");
const tagAboutNameEl = document.getElementById("tag-about-name");
const tagAboutStrongEl = document.getElementById("tag-about-strong");

const listEl = document.getElementById("tag-questions-list");
const emptyEl = document.getElementById("tag-empty");
const errorEl = document.getElementById("tag-error");
const countEl = document.getElementById("tag-results-count");

function renderQuestion(q) {
  const id = q?.id ?? q?.question_id;
  const title = q?.title ?? "";
  const description = q?.description ?? "";
  const tags = q?.tags ?? "";
  const viewCount = q?.view_count ?? 0;
  const username = q?.username ?? "";

  const tagsArray = String(tags)
    .split(",")
    .map((t) => t.trim())
    .filter(Boolean);

  return `
    <div class="question-card">
      <div class="stats-container">
        <div class="stat-box">
          <span class="stat-value">${escapeHtml(viewCount)}</span>
          <span class="stat-label">views</span>
        </div>
      </div>

      <div class="question-content">
        <h3 class="question-title">
          <a href="question.html?id=${encodeURIComponent(id)}">${escapeHtml(title)}</a>
        </h3>
        <p class="question-excerpt">${escapeHtml(String(description).slice(0, 150))}...</p>
        <div class="meta-footer">
          <div class="tags-list">
            ${tagsArray.map((t) => `<span class="tag">${escapeHtml(t)}</span>`).join("")}
          </div>
          <div class="user-info">
            <span class="text-muted">asked by</span>
            <span class="author-name">${escapeHtml(username)}</span>
          </div>
        </div>
      </div>
    </div>
  `;
}

(async () => {
  const tag = getQueryParam("name") || "Unknown";
  tagNameEl.textContent = tag;
  tagAboutNameEl.textContent = tag;
  tagAboutStrongEl.textContent = tag;

  try {
    const data = await apiFetch(`/actions/fetch_questions_by_tag.php?name=${encodeURIComponent(tag)}`);
    const questions = Array.isArray(data) ? data : (data?.questions ?? []);

    countEl.textContent = String(questions.length);

    if (!questions.length) {
      emptyEl.style.display = "block";
      listEl.innerHTML = "";
      return;
    }

    listEl.innerHTML = questions.map(renderQuestion).join("");
  } catch (e) {
    errorEl.style.display = "block";
    errorEl.textContent = `Backend not connected yet: ${e.message}`;
  }
})();
