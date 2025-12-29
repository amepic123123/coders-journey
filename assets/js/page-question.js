import { apiFetch, escapeHtml, getQueryParam } from "./api.js";
import { getSession, renderNavAuth } from "./session.js";

await renderNavAuth();

const questionCardEl = document.getElementById("question-card");
const answersCountEl = document.getElementById("answers-count");
const answersListEl = document.getElementById("answers-list");
const errorEl = document.getElementById("question-error");

const answerForm = document.getElementById("answer-form");
const answerErrorEl = document.getElementById("answer-error");

function setError(msg) {
  errorEl.style.display = "block";
  errorEl.textContent = msg;
}

function renderVoteSidebar({ kind, id, voteCount, userVote, disabled, extraHtml = "" }) {
  const upActive = userVote === 1;
  const downActive = userVote === -1;

  const upStyle = upActive ? "color: var(--primary-dark); font-weight: 700;" : "";
  const downStyle = downActive ? "color: var(--primary-dark); font-weight: 700;" : "";

  return `
    <div class="vote-sidebar">
      <button class="vote-btn vote-up" type="button" ${disabled ? "disabled" : ""}
        data-kind="${escapeHtml(kind)}" data-id="${escapeHtml(id)}" data-vote="${escapeHtml(userVote)}" style="${upStyle}">▲</button>
      <span class="vote-count" data-kind="${escapeHtml(kind)}" data-id="${escapeHtml(id)}">${escapeHtml(voteCount)}</span>
      <button class="vote-btn vote-down" type="button" ${disabled ? "disabled" : ""}
        data-kind="${escapeHtml(kind)}" data-id="${escapeHtml(id)}" data-vote="${escapeHtml(userVote)}" style="${downStyle}">▼</button>
      ${extraHtml}
    </div>
  `;
}

function renderQuestion(q, session) {
  const id = q?.id ?? q?.question_id;
  const title = q?.title ?? "";
  const description = q?.description ?? q?.body ?? "";
  const tags = q?.tags ?? "";
  const voteCount = q?.vote_count ?? 0;
  const userVote = q?.user_vote ?? 0;
  const userId = q?.user_id ?? 0;
  const username = q?.username ?? (userId ? `User #${userId}` : "");
  const createdAt = q?.created_at ?? "";

  const isOwner = !!(session?.logged_in && Number(session?.user_id) === Number(userId));

  const tagsArray = String(tags)
    .split(",")
    .map((t) => t.trim())
    .filter(Boolean);

  return `
    <div class="post-layout">
      ${renderVoteSidebar({
        kind: "question",
        id,
        voteCount,
        userVote,
        disabled: !session?.logged_in,
      })}

      <div style="flex-grow: 1;">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap: 12px;">
          <h1 style="font-size: 1.5rem; margin-bottom: 1rem;">${escapeHtml(title)}</h1>
          ${isOwner ? `<button id="delete-question-btn" class="btn" type="button" style="background: var(--danger); color: white; white-space: nowrap;">Delete</button>` : ""}
        </div>

        <div style="line-height: 1.6; color: #333; margin-bottom: 1.5rem;">${escapeHtml(description).replaceAll("\n", "<br>")}</div>

        <div class="meta-footer">
          <div class="tags-list">
            ${tagsArray
              .map((t) => `<a href="tag.html?name=${encodeURIComponent(t)}" class="tag">${escapeHtml(t)}</a>`)
              .join("")}
          </div>

          <div class="user-info">
            <span class="text-muted">asked by</span>
            <a href="profile.html?id=${encodeURIComponent(userId)}"><strong style="color: #4a90e2;">${escapeHtml(username)}</strong></a>
            <span class="text-muted">${createdAt ? `on ${escapeHtml(createdAt)}` : ""}</span>
          </div>
        </div>
      </div>
    </div>
  `;
}

function renderAnswer(ans) {
  const body = ans?.body ?? ans?.description ?? "";
  const username = ans?.username ?? "";
  const userId = ans?.user_id ?? 0;
  const createdAt = ans?.created_at ?? "";
  const isBest = !!(ans?.is_best ?? ans?.best);
  const voteCount = ans?.vote_count ?? 0;
  const userVote = ans?.user_vote ?? 0;

  return `
    <div class="card ${isBest ? "best-answer" : ""}">
      <div class="post-layout">
        ${renderVoteSidebar({
          kind: "answer",
          id: ans?.answer_id,
          voteCount,
          userVote,
          disabled: true,
          extraHtml: isBest
            ? `<span style="font-size:1.5rem; color: var(--success); margin-top:10px;" title="Best Answer">✓</span>`
            : "",
        })}

        <div style="flex-grow: 1;">
          <div style="margin-bottom: 1rem;">${escapeHtml(body).replaceAll("\n", "<br>")}</div>
          <div class="meta-footer" style="justify-content: flex-end;">
            <div class="user-info">
              <span class="text-muted">answered by</span>
              <a href="profile.html?id=${encodeURIComponent(userId)}"><strong>${escapeHtml(username || (userId ? `User #${userId}` : ""))}</strong></a>
              <span class="text-muted">${createdAt ? escapeHtml(createdAt) : ""}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
}

(async () => {
  const questionId = getQueryParam("id");
  if (!questionId) {
    setError("Missing question id.");
    return;
  }

  try {
    const session = await getSession();
    const data = await apiFetch(`/actions/fetch_single_question.php?id=${encodeURIComponent(questionId)}`);

    const question = data?.question ?? data;
    const answers = data?.answers ?? question?.answers ?? [];

    document.title = `${question?.title ?? "Question"} - Coders' Journey`;

    questionCardEl.innerHTML = renderQuestion(question, session);
    questionCardEl.style.display = "block";

    const answersArr = Array.isArray(answers) ? answers : [];
    answersCountEl.textContent = String(answersArr.length);
    answersListEl.innerHTML = answersArr.map(renderAnswer).join("");

    // Enable answer voting only when logged in, once session is known.
    if (session?.logged_in) {
      for (const btn of answersListEl.querySelectorAll('button.vote-btn')) {
        btn.disabled = false;
      }
    }

    async function applyVote({ kind, id, nextVote }) {
      const endpoint = kind === "answer" ? "/actions/vote_answer.php" : "/actions/vote_question.php";
      const body = kind === "answer" ? { answer_id: id, vote: nextVote } : { question_id: id, vote: nextVote };
      const res = await apiFetch(endpoint, { method: "POST", body });

      const newCount = res?.vote_count ?? 0;
      const newUserVote = res?.user_vote ?? 0;

      const countEl = document.querySelector(`.vote-count[data-kind="${kind}"][data-id="${CSS.escape(String(id))}"]`);
      if (countEl) countEl.textContent = String(newCount);

      const upBtn = document.querySelector(`.vote-up[data-kind="${kind}"][data-id="${CSS.escape(String(id))}"]`);
      const downBtn = document.querySelector(`.vote-down[data-kind="${kind}"][data-id="${CSS.escape(String(id))}"]`);
      if (upBtn) {
        upBtn.dataset.vote = String(newUserVote);
        upBtn.style.color = newUserVote === 1 ? "var(--primary-dark)" : "";
        upBtn.style.fontWeight = newUserVote === 1 ? "700" : "";
      }
      if (downBtn) {
        downBtn.dataset.vote = String(newUserVote);
        downBtn.style.color = newUserVote === -1 ? "var(--primary-dark)" : "";
        downBtn.style.fontWeight = newUserVote === -1 ? "700" : "";
      }
    }

    function wireVoteHandlers(root) {
      root.addEventListener("click", async (e) => {
        const btn = e.target?.closest?.("button.vote-btn");
        if (!btn) return;
        if (btn.disabled) {
          setError("Log in to vote.");
          return;
        }

        const kind = btn.dataset.kind;
        const id = btn.dataset.id;
        if (!kind || !id) return;

        const currentVote = Number(btn.dataset.vote ?? 0);
        const isUp = btn.classList.contains("vote-up");
        const desired = isUp ? 1 : -1;
        const nextVote = currentVote === desired ? 0 : desired;

        try {
          await applyVote({ kind, id, nextVote });
        } catch (err) {
          setError(err.message);
        }
      });
    }

    wireVoteHandlers(questionCardEl);
    wireVoteHandlers(answersListEl);

    const deleteBtn = document.getElementById("delete-question-btn");
    if (deleteBtn) {
      deleteBtn.addEventListener("click", async () => {
        if (!confirm("Delete this question? This cannot be undone.")) return;
        try {
          await apiFetch("/actions/delete_question.php", {
            method: "POST",
            body: { question_id: questionId },
          });
          window.location.href = "index.html";
        } catch (err) {
          setError(err.message);
        }
      });
    }

    answerForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      answerErrorEl.style.display = "none";
      answerErrorEl.textContent = "";

      const fd = new FormData(answerForm);
      const answerBody = String(fd.get("answer_body") ?? "").trim();
      if (!answerBody) return;

      try {
        await apiFetch("/actions/post_answer.php", {
          method: "POST",
          body: {
            question_id: questionId,
            answer_body: answerBody,
          },
        });

        // simplest: reload to show new answer
        window.location.reload();
      } catch (err) {
        answerErrorEl.style.display = "block";
        answerErrorEl.style.background = "#f8d7da";
        answerErrorEl.style.color = "#721c24";
        answerErrorEl.style.padding = "10px";
        answerErrorEl.style.borderRadius = "4px";
        answerErrorEl.textContent = err.message;
      }
    });
  } catch (e) {
    setError(`Backend not connected yet: ${e.message}`);
  }
})();
