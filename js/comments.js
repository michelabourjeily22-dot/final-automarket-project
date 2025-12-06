if (window.__commentsInitialized) {
  console.warn('comments.js duplicate init - aborting second init');

} else {
  window.__commentsInitialized = true;
}

(function () {

  if (window.__comments_js_loaded) return;
  window.__comments_js_loaded = true;

  const API = 'api/comments.php';
  const metaCar = document.querySelector('meta[name="car-id"]');
  const carId = metaCar ? metaCar.getAttribute('content') : (document.querySelector('[data-car-id]')?.getAttribute('data-car-id') || null);
  const container = document.getElementById('comments-container');
  const textarea = document.getElementById('comment-input');
  const postBtn = document.getElementById('comment-post-btn');
  const charCount = document.getElementById("char-count");

if (textarea && charCount) {
  textarea.addEventListener("input", () => {
    const length = textarea.value.length;
    charCount.textContent = `${length}/1000`;
  });
}

  if (!carId || !container) {
    console.warn('Comments: missing carId or comments container');
    return;
  }

  const inFlightPosts = new Set();   

  const inFlightLikes = new Set();   

  function escapeHtml(s) {
    if (s === null || s === undefined) return '';
    return String(s)
      .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
  }

  async function fetchJson(url, opts = {}) {
    opts.credentials = opts.credentials || 'same-origin';
    if (opts.body && !opts.headers) opts.headers = {'Content-Type': 'application/json'};
    const res = await fetch(url, opts);

    if (!res.ok) {
      const text = await res.text();
      throw new Error(`HTTP ${res.status}: ${text}`);
    }
    return res.json();
  }

function buildNestedFromFlat(rows) {
  const map = {};
  const roots = [];

  rows.forEach(r => {
    r.replies = [];
    map[r.id] = r;
  });

  rows.forEach(r => {
    if (r.parent_id && map[r.parent_id]) {
      map[r.parent_id].replies.push(r);
    } else {
      roots.push(r);
    }
  });

  return roots;
}

  function renderCommentTree(comments) {

    return comments.map(c => renderCommentHtml(c)).join('');
  }

  function avatarHtml(pic, name) {
    if (pic) return `<img src="${escapeHtml(pic)}" class="comment-avatar avatar--sm" alt="${escapeHtml(name)}" onerror="this.src='/assets/default_user.png'">`;
    const ch = escapeHtml((name||'U')[0] || 'U').toUpperCase();
    return `<div class="comment-avatar-placeholder avatar--sm">${ch}</div>`;
  }

  function renderCommentHtml(c, isReply = false) {
    const name = c.full_name || c.username || 'User';
    const likes = Number(c.likes_count || 0);
    const likedClass = c.liked_by_user ? 'liked' : '';

    const repliesHtml = (c.replies && c.replies.length) ? `<div class="replies">${c.replies.map(r => renderCommentHtml(r, true)).join('')}</div>` : '';
    const wrapperClass = isReply ? 'comment-row reply-row' : 'comment-row';
    return `
      <div class="${wrapperClass}" data-comment-id="${c.id}">
        <div class="comment-left">${avatarHtml(c.profile_picture, name)}</div>
        <div class="comment-right">
          <div class="comment-meta">
            <strong>${escapeHtml(name)}</strong>
            <span class="comment-time">${escapeHtml(c.created_at || '')}</span>
          </div>
          <div class="comment-text">${escapeHtml(c.comment_text)}</div>
          <div class="comment-actions">
            <button class="btn-action btn-reply" data-id="${c.id}">Reply</button>
            <button class="btn-action btn-like ${likedClass}" data-id="${c.id}">
              <span class="heart">${c.liked_by_user ? '❤' : '♡'}</span>
              <span class="like-count">${likes}</span>
            </button>
            <button class="btn-action btn-edit" data-id="${c.id}">Edit</button>
            <button class="btn-action btn-delete" data-id="${c.id}">Delete</button>
          </div>
          ${repliesHtml}
        </div>
      </div>
    `;
  }

async function loadComments() {
  try {
    const data = await fetchJson(`${API}?car_id=${encodeURIComponent(carId)}`);
    if (!data.success) {
      container.innerHTML = `<div class="small-text">Error: ${escapeHtml(data.error || 'Unable to load comments')}</div>`;
      return;
    }

    let comments = data.comments || [];

if (!comments[0]?.replies) {
  comments = buildNestedFromFlat(comments);
}

container.innerHTML = renderCommentTree(comments);

  } catch (err) {
    console.error('loadComments:', err);
    container.innerHTML = '<div class="small-text">Failed to load comments.</div>';
  }
}

  function commentKey(parentId, text) {
    return `${parentId || 0}::${String(text).slice(0,200)}`;
  }

  async function postComment(text, parentId = null) {
    const key = commentKey(parentId, text);
    if (inFlightPosts.has(key)) return { success:false, error:'duplicate_inflight' };
    inFlightPosts.add(key);
    try {
      const res = await fetchJson(API, { method:'POST', body: JSON.stringify({ car_id: carId, comment_text: text, parent_id: parentId }) });
      return res;
    } catch (err) {
      console.error('postComment err', err);
      return { success:false, error:'network' };
    } finally {
      inFlightPosts.delete(key);
    }
  }

  async function toggleLike(commentId) {
    if (inFlightLikes.has(commentId)) return { success:false, error:'inflight' };
    inFlightLikes.add(commentId);
    try {

      return await fetchJson(`${API}?action=toggle_like`, { method:'POST', body: JSON.stringify({ comment_id: commentId })});
    } catch (err) {
      console.error('toggleLike err', err);
      return { success:false, error:'network' };
    } finally {
      inFlightLikes.delete(commentId);
    }
  }

  container.addEventListener('click', async function (ev) {
    const btn = ev.target.closest('.btn-action');
    if (!btn) return;

    if (btn.classList.contains('btn-reply')) {
      const commentId = btn.getAttribute('data-id');
      const row = btn.closest('[data-comment-id]');
      if (!row) return;

      let inline = row.querySelector('.inline-reply');
      if (inline) { inline.remove(); return; }

      inline = document.createElement('div');
      inline.className = 'inline-reply';
      inline.innerHTML = `
        <textarea class="inline-reply-text" rows="2" placeholder="Write a reply..."></textarea>
        <div style="display:flex; gap:8px; margin-top:8px;">
          <button class="btn-primary btn-inline-post">Reply</button>
          <button class="btn-secondary btn-inline-cancel">Cancel</button>
        </div>
      `;
      row.querySelector('.comment-right').appendChild(inline);

      inline.querySelector('.btn-inline-cancel').addEventListener('click', () => inline.remove());

      inline.querySelector('.btn-inline-post').addEventListener('click', async () => {
        const txt = inline.querySelector('.inline-reply-text').value.trim();
        if (!txt) { alert('Reply cannot be empty'); return; }
        const postBtnLocal = inline.querySelector('.btn-inline-post');
        postBtnLocal.disabled = true;
        const res = await postComment(txt, commentId);
        postBtnLocal.disabled = false;
        if (!res.success) {
          if (res.error && res.error.toLowerCase().includes('not authenticated')) {
            window.location.href = '/index.php?force_auth=1';
            return;
          }
          alert('Error posting reply: ' + (res.error || 'Unknown'));
          return;
        }

        await loadComments();
      });
      return;
    }

    if (btn.classList.contains('btn-like')) {
      const commentId = btn.getAttribute('data-id');

      if (inFlightLikes.has(commentId)) return;
      btn.disabled = true;
      try {
        const res = await toggleLike(commentId);
        if (!res.success) {
          if (res.error && res.error.toLowerCase().includes('not authenticated')) {
            window.location.href = '/index.php?force_auth=1';
            return;
          }
          alert('Like failed: ' + (res.error || 'Unknown'));
        } else {

          const countSpan = btn.querySelector('.like-count');
          const heart = btn.querySelector('.heart');
          if (res.liked) {
            btn.classList.add('liked');
            if (heart) heart.textContent = '❤';
          } else {
            btn.classList.remove('liked');
            if (heart) heart.textContent = '♡';
          }
          if (countSpan) countSpan.textContent = String(res.likes_count || 0);
        }
      } catch (err) {
        console.error('like click error', err);
      } finally {
        btn.disabled = false;
      }
      return;
    }

    if (btn.classList.contains('btn-edit')) {
      const commentId = btn.getAttribute('data-id');
      const row = btn.closest('[data-comment-id]');
      if (!row) return;
      const textEl = row.querySelector('.comment-text');
      if (!textEl) return;

      if (row.querySelector('.inline-edit')) return;

      const original = textEl.textContent;
      const editDiv = document.createElement('div');
      editDiv.className = 'inline-edit';
      editDiv.innerHTML = `
        <textarea class="inline-edit-text" rows="3">${escapeHtml(original)}</textarea>
        <div style="display:flex; gap:8px; margin-top:8px;">
          <button class="btn-primary btn-save-edit">Save</button>
          <button class="btn-secondary btn-cancel-edit">Cancel</button>
        </div>
      `;
      textEl.style.display = 'none';
      textEl.parentNode.insertBefore(editDiv, textEl.nextSibling);

      editDiv.querySelector('.btn-cancel-edit').addEventListener('click', () => {
        editDiv.remove();
        textEl.style.display = '';
      });

      editDiv.querySelector('.btn-save-edit').addEventListener('click', async () => {
        const newText = editDiv.querySelector('.inline-edit-text').value.trim();
        if (!newText) { alert('Comment cannot be empty'); return; }
       const res = await fetchJson(`${API}?action=edit`, {
  const res = await fetchJson(API, { 
  method: 'PUT', 
  body: JSON.stringify({ comment_id: commentId, comment_text: newText }) 
});


        if (!res.success) {
          alert('Error editing comment: ' + (res.error || 'Unknown'));
          return;
        }

        await loadComments();
      });
      return;
    }

   if (btn.classList.contains('btn-delete')) {
  const commentId = btn.getAttribute('data-id');
  if (!confirm('Delete comment? This cannot be undone.')) return;
  try {
    const res = await fetchJson(API, { method:'DELETE', body: JSON.stringify({ comment_id: commentId }) });
    if (!res.success) {
      alert('Delete failed: ' + (res.error || 'Unknown'));
      return;
    }
    await loadComments();
  } catch (err) {
    console.error('delete error', err);
    alert('Delete failed');
  }
  return;
}

  });

  if (postBtn && textarea) {
    postBtn.addEventListener('click', async () => {
      const txt = textarea.value.trim();
      if (!txt) return;
      postBtn.disabled = true;
      const res = await postComment(txt, null);
      postBtn.disabled = false;
      if (!res.success) {
        if (res.error === 'duplicate_inflight') return;
        if (res.error && res.error.toLowerCase().includes('not authenticated')) {
          window.location.href = '/index.php?force_auth=1';
          return;
        }
        alert('Error posting comment: ' + (res.error || 'Unknown'));
        return;
      }
      textarea.value = '';
      await loadComments();
    });
  }

  loadComments();

})();

