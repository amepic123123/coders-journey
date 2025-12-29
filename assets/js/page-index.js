import { apiFetch, escapeHtml } from "./api.js";
import { renderNavAuth } from "./session.js";

await renderNavAuth();

const listEl = document.getElementById("questions-list");
const emptyEl = document.getElementById("questions-empty");
const countEl = document.getElementById("questions-count");

function renderQuestion(q) {
  const id = q?.id ?? q?.question_id;
  const title = q?.title ?? "";
  const description = q?.description ?? "";
  const tags = q?.tags ?? "";
  const voteCount = q?.vote_count ?? 0;
  const viewCount = q?.view_count ?? 0;
  const userId = q?.user_id ?? q?.asked_by ?? 0;
  const username = q?.username ?? (userId ? `User #${userId}` : "");
  const createdAt = q?.created_at ?? "";

  const tagsArray = String(tags)
    .split(",")
    .map((t) => t.trim())
    .filter(Boolean);

  return `
    <div class="question-card">
      <div class="stats-container">
        <div class="stat-box">
          <span class="stat-value">${escapeHtml(voteCount)}</span>
          <span class="stat-label">votes</span>
        </div>
        <div class="stat-box">
          <span class="stat-value">0</span>
          <span class="stat-label">answers</span>
        </div>
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
            ${tagsArray
              .map(
                (t) => `<a href="tag.html?name=${encodeURIComponent(t)}" class="tag">${escapeHtml(t)}</a>`
              )
              .join("")}
          </div>

          <div class="user-info">
            <span class="text-muted">asked by</span>
            <a href="profile.html?id=${encodeURIComponent(userId)}" class="author-name">${escapeHtml(username)}</a>
            <span class="text-muted">${createdAt ? ` • ${escapeHtml(createdAt)}` : ""}</span>
          </div>
        </div>
      </div>
    </div>
  `;
}

(async () => {
  try {
    const data = await apiFetch("/actions/fetch_questions.php");
    const questions = Array.isArray(data) ? data : (data?.questions ?? []);

    countEl.textContent = String(questions.length);

    if (!questions.length) {
      emptyEl.style.display = "block";
      listEl.innerHTML = "";
      return;
    }

    listEl.innerHTML = questions.map(renderQuestion).join("");
  } catch (e) {
    emptyEl.style.display = "block";
    listEl.innerHTML = `<div class="card" style="padding:16px;">Backend not connected yet: ${escapeHtml(e.message)}</div>`;
  }
})();
