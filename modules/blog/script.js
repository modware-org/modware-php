document.addEventListener('DOMContentLoaded', function() {
    initCommentForm();
    initShareButtons();
    initSidebarPosition();
    initSearchForm();
});

function initCommentForm() {
    const commentForm = document.getElementById('commentForm');
    if (!commentForm) return;

    commentForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        
        try {
            const formData = new FormData(this);
            const response = await fetch('/api/comments', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('success', 'Комментарий успешно добавлен' + 
                               (result.moderated ? ' и ожидает модерации.' : '.'));
                this.reset();

                // If comment was approved immediately, add it to the page
                if (!result.moderated && result.comment) {
                    addCommentToPage(result.comment);
                }
            } else {
                showNotification('error', result.message || 'Ошибка при добавлении комментария.');
            }
        } catch (error) {
            console.error('Error submitting comment:', error);
            showNotification('error', 'Произошла ошибка при отправке комментария.');
        } finally {
            submitButton.disabled = false;
        }
    });

    // Reply functionality
    document.querySelectorAll('.reply-button').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const parentInput = commentForm.querySelector('input[name="parent_id"]');
            parentInput.value = commentId;
            
            // Move form after the comment
            const comment = document.getElementById('comment-' + commentId);
            comment.after(commentForm);
            
            // Add cancel button if not exists
            if (!commentForm.querySelector('.cancel-reply')) {
                const cancelButton = document.createElement('button');
                cancelButton.type = 'button';
                cancelButton.className = 'cancel-reply';
                cancelButton.textContent = 'Отменить ответ';
                cancelButton.onclick = cancelReply;
                commentForm.querySelector('.form-group:last-child').appendChild(cancelButton);
            }
            
            // Scroll to form
            commentForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });
}

function cancelReply() {
    const commentForm = document.getElementById('commentForm');
    const parentInput = commentForm.querySelector('input[name="parent_id"]');
    parentInput.value = '';
    
    // Move form back to original position
    document.querySelector('.comment-form-wrapper').appendChild(commentForm);
    
    // Remove cancel button
    this.remove();
}

function addCommentToPage(comment) {
    const commentsList = document.querySelector('.comments-list');
    if (!commentsList) return;

    const commentElement = document.createElement('div');
    commentElement.className = 'comment';
    commentElement.id = 'comment-' + comment.id;
    
    commentElement.innerHTML = `
        <div class="comment-meta">
            ${comment.author_url ? 
                `<a href="${escapeHtml(comment.author_url)}" class="comment-author" rel="nofollow">
                    ${escapeHtml(comment.author_name)}
                </a>` : 
                `<span class="comment-author">${escapeHtml(comment.author_name)}</span>`
            }
            <time datetime="${formatDate(comment.created_at)}">
                ${formatDateTime(comment.created_at)}
            </time>
        </div>
        <div class="comment-content">
            ${escapeHtml(comment.content).replace(/\n/g, '<br>')}
        </div>
    `;

    if (comment.parent_id) {
        const parentComment = document.getElementById('comment-' + comment.parent_id);
        let replies = parentComment.querySelector('.comment-replies');
        if (!replies) {
            replies = document.createElement('div');
            replies.className = 'comment-replies';
            parentComment.appendChild(replies);
        }
        replies.appendChild(commentElement);
    } else {
        commentsList.appendChild(commentElement);
    }

    commentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function initShareButtons() {
    document.querySelectorAll('.share-button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.href;
            window.open(url, 'share-dialog', 
                       'width=600,height=400,toolbar=no,location=no,status=no,menubar=no');
        });
    });
}

function initSidebarPosition() {
    const blogLayout = document.querySelector('.blog-layout');
    if (!blogLayout) return;

    const sidebarPosition = getComputedStyle(document.documentElement)
        .getPropertyValue('--blog-sidebar-position')
        .trim();
    
    if (sidebarPosition) {
        blogLayout.dataset.sidebar = sidebarPosition;
    }
}

function initSearchForm() {
    const searchForm = document.querySelector('.search-widget .search-form');
    if (!searchForm) return;

    searchForm.addEventListener('submit', function(e) {
        const input = this.querySelector('input[type="search"]');
        if (!input.value.trim()) {
            e.preventDefault();
            input.focus();
        }
    });
}

// Utility Functions
function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Trigger animation
    requestAnimationFrame(() => {
        notification.classList.add('show');
    });
    
    // Remove after delay
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function formatDate(dateString) {
    return new Date(dateString).toISOString().split('T')[0];
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('ru-RU') + ' ' + 
           date.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
}

// Add CSS for notifications
const style = document.createElement('style');
style.textContent = `
    .notification {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 4px;
        color: white;
        font-size: 14px;
        transform: translateY(100px);
        opacity: 0;
        transition: transform 0.3s ease, opacity 0.3s ease;
        z-index: 1000;
    }

    .notification.success {
        background: var(--success-color, #4caf50);
    }

    .notification.error {
        background: var(--danger-color, #f44336);
    }

    .notification.show {
        transform: translateY(0);
        opacity: 1;
    }

    @media (prefers-reduced-motion: reduce) {
        .notification {
            transition: none;
        }
    }
`;
document.head.appendChild(style);
